<?php
/**
 * Leadbusiness - Custom Domain Resolver
 * 
 * Erkennt ob die Anfrage über eine Custom Domain kommt
 * und lädt den entsprechenden Kunden.
 */

namespace Leadbusiness;

class DomainResolver {
    
    private static $resolvedCustomer = null;
    private static $isCustomDomain = false;
    
    /**
     * Initialisiert den Domain Resolver
     * Sollte am Anfang jeder Public-Seite aufgerufen werden
     */
    public static function init(): ?array {
        // Bereits aufgelöst?
        if (self::$resolvedCustomer !== null) {
            return self::$resolvedCustomer;
        }
        
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $host = strtolower($host);
        $host = preg_replace('/^www\./', '', $host);
        
        // Standard-Domain prüfen
        if (self::isStandardDomain($host)) {
            return self::resolveStandardDomain($host);
        }
        
        // Custom Domain prüfen
        return self::resolveCustomDomain($host);
    }
    
    /**
     * Prüft ob es eine Standard-Domain ist
     */
    private static function isStandardDomain(string $host): bool {
        $standardDomains = [
            'empfehlungen.cloud',
            'empfohlen.de',
            'leadbusiness.de'
        ];
        
        foreach ($standardDomains as $domain) {
            if (strpos($host, '.' . $domain) !== false || $host === $domain) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Löst Standard-Domain (subdomain.empfehlungen.cloud) auf
     */
    private static function resolveStandardDomain(string $host): ?array {
        // Subdomain extrahieren
        $parts = explode('.', $host);
        
        if (count($parts) < 3) {
            return null; // Keine Subdomain
        }
        
        $subdomain = $parts[0];
        
        // Reservierte Subdomains
        $reserved = ['www', 'api', 'admin', 'dashboard', 'app', 'mail', 'lb-proxy'];
        if (in_array($subdomain, $reserved)) {
            return null;
        }
        
        // Kunde suchen
        $db = Database::getInstance();
        $customer = $db->fetch(
            "SELECT * FROM customers WHERE subdomain = ? AND status = 'active'",
            [$subdomain]
        );
        
        if ($customer) {
            self::$resolvedCustomer = $customer;
            self::$isCustomDomain = false;
        }
        
        return self::$resolvedCustomer;
    }
    
    /**
     * Löst Custom Domain auf
     */
    private static function resolveCustomDomain(string $host): ?array {
        $db = Database::getInstance();
        
        // Kunde mit verifizierter Custom Domain suchen
        $customer = $db->fetch(
            "SELECT * FROM customers 
             WHERE custom_domain = ? 
             AND custom_domain_verified = 1 
             AND custom_domain_ssl_status = 'active'
             AND status = 'active'",
            [$host]
        );
        
        if ($customer) {
            self::$resolvedCustomer = $customer;
            self::$isCustomDomain = true;
        }
        
        return self::$resolvedCustomer;
    }
    
    /**
     * Gibt den aufgelösten Kunden zurück
     */
    public static function getCustomer(): ?array {
        if (self::$resolvedCustomer === null) {
            self::init();
        }
        return self::$resolvedCustomer;
    }
    
    /**
     * Prüft ob die Anfrage über eine Custom Domain kommt
     */
    public static function isCustomDomain(): bool {
        if (self::$resolvedCustomer === null) {
            self::init();
        }
        return self::$isCustomDomain;
    }
    
    /**
     * Gibt die primäre URL des Kunden zurück
     */
    public static function getPrimaryUrl(?array $customer = null): string {
        $customer = $customer ?? self::getCustomer();
        
        if (!$customer) {
            return 'https://empfehlungen.cloud';
        }
        
        // Custom Domain bevorzugen wenn aktiv
        if (!empty($customer['custom_domain']) && 
            !empty($customer['custom_domain_verified']) && 
            $customer['custom_domain_ssl_status'] === 'active') {
            return 'https://' . $customer['custom_domain'];
        }
        
        // Standard-Domain
        return 'https://' . $customer['subdomain'] . '.empfehlungen.cloud';
    }
    
    /**
     * Gibt die Referral-Link URL zurück
     */
    public static function getReferralUrl(string $referralCode, ?array $customer = null): string {
        $baseUrl = self::getPrimaryUrl($customer);
        return $baseUrl . '/r/' . $referralCode;
    }
    
    /**
     * Prüft ob eine Weiterleitung nötig ist
     * (z.B. von Standard-Domain zu Custom-Domain)
     */
    public static function shouldRedirect(): ?string {
        $customer = self::getCustomer();
        
        if (!$customer) {
            return null;
        }
        
        // Hat Custom Domain und kommt über Standard-Domain?
        if (!self::$isCustomDomain && 
            !empty($customer['custom_domain']) && 
            !empty($customer['custom_domain_verified']) && 
            $customer['custom_domain_ssl_status'] === 'active') {
            
            // Prüfen ob Weiterleitung aktiviert ist
            if (!empty($customer['redirect_to_custom_domain'])) {
                $uri = $_SERVER['REQUEST_URI'] ?? '/';
                return 'https://' . $customer['custom_domain'] . $uri;
            }
        }
        
        return null;
    }
    
    /**
     * Führt Weiterleitung aus falls nötig
     */
    public static function handleRedirect(): void {
        $redirectUrl = self::shouldRedirect();
        
        if ($redirectUrl) {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirectUrl);
            exit;
        }
    }
    
    /**
     * Debug-Informationen
     */
    public static function debug(): array {
        return [
            'host' => $_SERVER['HTTP_HOST'] ?? 'unknown',
            'is_custom_domain' => self::$isCustomDomain,
            'customer_id' => self::$resolvedCustomer['id'] ?? null,
            'customer_subdomain' => self::$resolvedCustomer['subdomain'] ?? null,
            'customer_custom_domain' => self::$resolvedCustomer['custom_domain'] ?? null,
            'primary_url' => self::getPrimaryUrl()
        ];
    }
}
