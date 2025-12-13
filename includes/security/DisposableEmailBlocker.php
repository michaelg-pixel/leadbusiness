<?php
/**
 * DisposableEmailBlocker
 * 
 * Blockiert Wegwerf-E-Mail-Adressen.
 * PHP 7.4+ kompatibel - ohne Namespace für Konsistenz
 */

use Leadbusiness\Database;

class DisposableEmailBlocker
{
    private $db;
    private $inMemoryBlacklist = [];
    
    // Bekannte große Wegwerf-E-Mail-Anbieter
    private $hardcodedDomains = [
        'tempmail.com', 'temp-mail.org', 'guerrillamail.com',
        '10minutemail.com', 'mailinator.com', 'throwaway.email',
        'getnada.com', 'yopmail.com', 'trashmail.com', 'maildrop.cc',
        'wegwerfmail.de', 'trash-mail.de', 'sofort-mail.de',
        'einwegmail.de', 'spoofmail.de', 'byom.de'
    ];
    
    // Verdächtige Muster in Domain-Namen
    private $suspiciousPatterns = [
        '/^temp/i',
        '/^trash/i', 
        '/^fake/i',
        '/^spam/i',
        '/^wegwerf/i',
        '/^disposable/i',
        '/^throwaway/i',
        '/^mail\d+/i',
        '/^test\d*mail/i',
        '/minute.*mail/i',
        '/^junk/i',
        '/^burner/i'
    ];
    
    // Verdächtige Muster in lokalen E-Mail-Teilen
    private $suspiciousLocalPatterns = [
        '/^test\d*$/i',
        '/^user\d+$/i',
        '/^admin\d*$/i',
        '/^info\d+$/i',
        '/^[a-z]{1,2}\d{4,}$/i',  // z.B. ab12345
        '/^\d{5,}$/i',            // Nur Zahlen
        '/^asdf/i',
        '/^qwer/i'
    ];
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->loadBlacklistFromDb();
    }
    
    /**
     * Blacklist aus Datenbank laden
     */
    private function loadBlacklistFromDb(): void
    {
        try {
            $domains = $this->db->fetchAll("SELECT domain FROM email_domain_blacklist");
            $this->inMemoryBlacklist = array_column($domains, 'domain');
        } catch (Exception $e) {
            $this->inMemoryBlacklist = [];
        }
    }
    
    /**
     * Prüfen ob E-Mail eine Wegwerf-Adresse ist
     * Gibt true/false zurück für einfache Nutzung
     */
    public function isDisposable(string $email): bool
    {
        $result = $this->checkDisposable($email);
        return $result['is_disposable'];
    }
    
    /**
     * Detaillierte Prüfung ob E-Mail eine Wegwerf-Adresse ist
     */
    public function checkDisposable(string $email): array
    {
        $email = strtolower(trim($email));
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['is_disposable' => true, 'reason' => 'invalid_email'];
        }
        
        $parts = explode('@', $email);
        $localPart = $parts[0];
        $domain = $parts[1] ?? '';
        
        // 1. Hardcoded Domains prüfen
        if (in_array($domain, $this->hardcodedDomains)) {
            return ['is_disposable' => true, 'reason' => 'hardcoded_blacklist', 'domain' => $domain];
        }
        
        // 2. Datenbank-Blacklist prüfen
        if (in_array($domain, $this->inMemoryBlacklist)) {
            return ['is_disposable' => true, 'reason' => 'database_blacklist', 'domain' => $domain];
        }
        
        // 3. Domain-Muster prüfen
        foreach ($this->suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $domain)) {
                return ['is_disposable' => true, 'reason' => 'suspicious_domain_pattern', 'domain' => $domain, 'pattern' => $pattern];
            }
        }
        
        // 4. Lokalen Teil prüfen
        foreach ($this->suspiciousLocalPatterns as $pattern) {
            if (preg_match($pattern, $localPart)) {
                return ['is_disposable' => false, 'reason' => 'suspicious_local_part', 'warning' => true, 'pattern' => $pattern];
            }
        }
        
        // 5. Sehr kurze oder sehr lange Domain
        if (strlen($domain) < 4 || strlen($domain) > 50) {
            return ['is_disposable' => false, 'reason' => 'unusual_domain_length', 'warning' => true];
        }
        
        return ['is_disposable' => false, 'reason' => 'passed'];
    }
    
    /**
     * Nur boolean zurückgeben
     */
    public function check(string $email): bool
    {
        return $this->isDisposable($email);
    }
    
    /**
     * Domain zur Blacklist hinzufügen
     */
    public function addToBlacklist(string $domain, string $source = 'manual'): bool
    {
        $domain = strtolower(trim($domain));
        
        // Bereits vorhanden?
        if ($this->isInBlacklist($domain)) {
            return false;
        }
        
        $this->db->execute(
            "INSERT INTO email_domain_blacklist (domain, source, created_at) VALUES (?, ?, NOW())",
            [$domain, $source]
        );
        
        $this->inMemoryBlacklist[] = $domain;
        
        return true;
    }
    
    /**
     * Domain aus Blacklist entfernen
     */
    public function removeFromBlacklist(string $domain): bool
    {
        $domain = strtolower(trim($domain));
        
        $deleted = $this->db->execute(
            "DELETE FROM email_domain_blacklist WHERE domain = ?",
            [$domain]
        );
        
        if ($deleted > 0) {
            $this->inMemoryBlacklist = array_diff($this->inMemoryBlacklist, [$domain]);
            return true;
        }
        
        return false;
    }
    
    /**
     * Prüfen ob Domain in Blacklist
     */
    public function isInBlacklist(string $domain): bool
    {
        $domain = strtolower(trim($domain));
        return in_array($domain, $this->inMemoryBlacklist) || in_array($domain, $this->hardcodedDomains);
    }
    
    /**
     * Blacklist abrufen
     */
    public function getBlacklist(): array
    {
        return array_merge($this->hardcodedDomains, $this->inMemoryBlacklist);
    }
    
    /**
     * Domain aus E-Mail extrahieren
     */
    public static function extractDomain(string $email): string
    {
        $parts = explode('@', strtolower(trim($email)));
        return $parts[1] ?? '';
    }
    
    /**
     * Batch-Check für mehrere E-Mails
     */
    public function checkBatch(array $emails): array
    {
        $results = [];
        
        foreach ($emails as $email) {
            $results[$email] = $this->checkDisposable($email);
        }
        
        return $results;
    }
    
    /**
     * Statistiken abrufen
     */
    public function getStats(): array
    {
        $dbCount = $this->db->fetchColumn("SELECT COUNT(*) FROM email_domain_blacklist");
        
        return [
            'hardcoded_count' => count($this->hardcodedDomains),
            'database_count' => (int) $dbCount,
            'total_count' => count($this->hardcodedDomains) + (int) $dbCount,
            'patterns_count' => count($this->suspiciousPatterns)
        ];
    }
}
