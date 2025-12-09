<?php
/**
 * FraudDetection
 * 
 * Erkennt betrügerische Conversions und Anmeldungen.
 */

namespace Leadbusiness\Security;

use Leadbusiness\Database;

class FraudDetection
{
    private Database $db;
    private array $config;
    private DisposableEmailBlocker $emailBlocker;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $securityConfig = require __DIR__ . '/../../config/security.php';
        $this->config = $securityConfig['fraud_detection'];
        $this->emailBlocker = new DisposableEmailBlocker();
    }
    
    /**
     * Conversion auf Betrug prüfen
     * 
     * @param array $lead Neuer Lead (wird zur Conversion)
     * @param array $referrer Werber (hat den Lead empfohlen)
     * @param array $request Request-Daten (IP, etc.)
     * @return array score, flags, action
     */
    public function checkConversion(array $lead, array $referrer, array $request): array
    {
        if (!$this->config['enabled']) {
            return ['score' => 0, 'flags' => [], 'action' => 'allow'];
        }
        
        $score = 0;
        $flags = [];
        $checks = $this->config['checks'];
        
        // 1. Zu schnelle Conversion (< 5 Sekunden nach Klick)
        if ($checks['fast_conversion']['enabled']) {
            if ($this->isConversionTooFast($lead, $checks['fast_conversion']['min_seconds'])) {
                $score += $checks['fast_conversion']['score'];
                $flags[] = 'fast_conversion';
            }
        }
        
        // 2. Gleiche IP wie Werber
        if ($checks['same_ip']['enabled']) {
            if ($this->isSameIP($lead, $referrer, $request)) {
                $score += $checks['same_ip']['score'];
                $flags[] = 'same_ip';
            }
        }
        
        // 3. Gleiches IP-Subnet
        if ($checks['same_subnet']['enabled']) {
            if ($this->isSameSubnet($lead, $referrer, $request)) {
                $score += $checks['same_subnet']['score'];
                $flags[] = 'same_subnet';
            }
        }
        
        // 4. Zu viele Conversions von dieser IP heute
        if ($checks['ip_abuse']['enabled']) {
            if ($this->hasTooManyFromIP($request['ip'] ?? '', $checks['ip_abuse']['max_per_day'])) {
                $score += $checks['ip_abuse']['score'];
                $flags[] = 'ip_abuse';
            }
        }
        
        // 5. Self-Referral (gleiche E-Mail-Domain)
        if ($checks['self_referral']['enabled']) {
            if ($this->isSelfReferral($lead, $referrer)) {
                $score += $checks['self_referral']['score'];
                $flags[] = 'self_referral';
            }
        }
        
        // 6. Verdächtiges E-Mail-Muster
        if ($checks['suspicious_email']['enabled']) {
            if ($this->hasSuspiciousEmail($lead['email'] ?? '')) {
                $score += $checks['suspicious_email']['score'];
                $flags[] = 'suspicious_email';
            }
        }
        
        // 7. Gleicher Browser-Fingerprint
        if ($checks['same_fingerprint']['enabled']) {
            if ($this->isSameFingerprint($lead, $referrer)) {
                $score += $checks['same_fingerprint']['score'];
                $flags[] = 'same_fingerprint';
            }
        }
        
        // 8. Werber hat zu viele Referrals heute
        if ($checks['referrer_limit']['enabled']) {
            if ($this->isReferrerOverLimit($referrer, $checks['referrer_limit']['max_per_day'])) {
                $score += $checks['referrer_limit']['score'];
                $flags[] = 'referrer_limit';
            }
        }
        
        // 9. VPN/Proxy erkannt (vereinfachte Prüfung)
        if ($checks['vpn_detected']['enabled']) {
            if ($this->isVPNOrProxy($request['ip'] ?? '')) {
                $score += $checks['vpn_detected']['score'];
                $flags[] = 'vpn_detected';
            }
        }
        
        // 10. Wegwerf-E-Mail
        if ($checks['disposable_email']['enabled']) {
            if ($this->emailBlocker->check($lead['email'] ?? '')) {
                $score += $checks['disposable_email']['score'];
                $flags[] = 'disposable_email';
            }
        }
        
        // Aktion basierend auf Score
        $action = $this->determineAction($score);
        
        return [
            'score' => $score,
            'flags' => $flags,
            'action' => $action,
            'thresholds' => $this->config['thresholds']
        ];
    }
    
    /**
     * Aktion bestimmen
     */
    private function determineAction(int $score): string
    {
        $thresholds = $this->config['thresholds'];
        
        if ($score >= $thresholds['block']) {
            return 'block';
        }
        if ($score >= $thresholds['review']) {
            return 'review';
        }
        if ($score >= $thresholds['flag']) {
            return 'flag';
        }
        
        return 'allow';
    }
    
    /**
     * 1. Zu schnelle Conversion prüfen
     */
    private function isConversionTooFast(array $lead, int $minSeconds): bool
    {
        // Wenn click_time im Lead gespeichert ist
        if (isset($lead['click_time'])) {
            $clickTime = strtotime($lead['click_time']);
            $conversionTime = time();
            return ($conversionTime - $clickTime) < $minSeconds;
        }
        
        return false;
    }
    
    /**
     * 2. Gleiche IP prüfen
     */
    private function isSameIP(array $lead, array $referrer, array $request): bool
    {
        $leadIpHash = $request['ip_hash'] ?? hashIp($request['ip'] ?? '');
        $referrerIpHash = $referrer['ip_hash'] ?? '';
        
        return !empty($leadIpHash) && $leadIpHash === $referrerIpHash;
    }
    
    /**
     * 3. Gleiches Subnet prüfen
     */
    private function isSameSubnet(array $lead, array $referrer, array $request): bool
    {
        $leadIp = $request['ip'] ?? '';
        
        // Referrer-IP aus Log abrufen (wenn vorhanden)
        if (isset($referrer['id'])) {
            $referrerClick = $this->db->fetch(
                "SELECT ip_hash FROM clicks WHERE lead_id = ? ORDER BY created_at DESC LIMIT 1",
                [$referrer['id']]
            );
            
            // Vereinfachte Subnet-Prüfung: Gleiche ersten 3 Oktette
            // In Produktion: Bessere IP-Analyse
            if ($referrerClick && !empty($leadIp)) {
                // Hier müssten wir die originale IP haben, was wir nicht tun (gehashed)
                // Daher: Skip oder andere Methode
                return false;
            }
        }
        
        return false;
    }
    
    /**
     * 4. Zu viele von gleicher IP
     */
    private function hasTooManyFromIP(string $ip, int $maxPerDay): bool
    {
        if (empty($ip)) {
            return false;
        }
        
        $ipHash = hashIp($ip);
        
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM leads 
             WHERE ip_hash = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)",
            [$ipHash]
        );
        
        return $count >= $maxPerDay;
    }
    
    /**
     * 5. Self-Referral prüfen
     */
    private function isSelfReferral(array $lead, array $referrer): bool
    {
        $leadEmail = strtolower($lead['email'] ?? '');
        $referrerEmail = strtolower($referrer['email'] ?? '');
        
        if (empty($leadEmail) || empty($referrerEmail)) {
            return false;
        }
        
        // Gleiche E-Mail
        if ($leadEmail === $referrerEmail) {
            return true;
        }
        
        // Gleiche Domain (außer große Provider)
        $leadDomain = DisposableEmailBlocker::extractDomain($leadEmail);
        $referrerDomain = DisposableEmailBlocker::extractDomain($referrerEmail);
        
        // Große Provider ausschließen
        $publicDomains = ['gmail.com', 'googlemail.com', 'yahoo.com', 'hotmail.com', 
            'outlook.com', 'live.com', 'web.de', 'gmx.de', 'gmx.net', 't-online.de', 
            'freenet.de', 'icloud.com', 'me.com', 'aol.com', 'mail.com'];
        
        if (!in_array($leadDomain, $publicDomains) && $leadDomain === $referrerDomain) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 6. Verdächtige E-Mail prüfen
     */
    private function hasSuspiciousEmail(string $email): bool
    {
        $localPart = explode('@', strtolower($email))[0] ?? '';
        
        // Muster wie test1@, user123@, asdf@
        $suspiciousPatterns = [
            '/^test\d*$/',
            '/^user\d+$/',
            '/^admin\d*$/',
            '/^[a-z]{1,2}\d{4,}$/',  // z.B. ab12345
            '/^\d{5,}$/',            // Nur Zahlen
            '/^asdf/',
            '/^qwer/',
            '/^demo\d*$/',
            '/^sample\d*$/',
            '/^fake\d*$/'
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $localPart)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 7. Gleicher Fingerprint
     */
    private function isSameFingerprint(array $lead, array $referrer): bool
    {
        $leadFingerprint = $lead['fingerprint'] ?? '';
        $referrerFingerprint = $referrer['fingerprint'] ?? '';
        
        return !empty($leadFingerprint) && $leadFingerprint === $referrerFingerprint;
    }
    
    /**
     * 8. Referrer über Limit
     */
    private function isReferrerOverLimit(array $referrer, int $maxPerDay): bool
    {
        if (!isset($referrer['id'])) {
            return false;
        }
        
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM conversions 
             WHERE lead_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)",
            [$referrer['id']]
        );
        
        return $count >= $maxPerDay;
    }
    
    /**
     * 9. VPN/Proxy erkennen (vereinfacht)
     */
    private function isVPNOrProxy(string $ip): bool
    {
        if (empty($ip)) {
            return false;
        }
        
        // Bekannte VPN/Proxy-Bereiche (vereinfacht)
        // In Produktion: Externe API oder IP-Datenbank verwenden
        $vpnRanges = [
            '10.',      // Private
            '172.16.',  // Private
            '192.168.', // Private
        ];
        
        foreach ($vpnRanges as $range) {
            if (strpos($ip, $range) === 0) {
                return true;
            }
        }
        
        // Header-basierte Erkennung
        $proxyHeaders = [
            'HTTP_VIA',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_X_FORWARDED',
            'HTTP_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'HTTP_X_PROXY_ID',
            'HTTP_X_REAL_IP'
        ];
        
        $proxyHeadersFound = 0;
        foreach ($proxyHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                $proxyHeadersFound++;
            }
        }
        
        // Mehrere Proxy-Header = wahrscheinlich Proxy
        return $proxyHeadersFound >= 3;
    }
    
    /**
     * Fraud-Event loggen
     */
    public function log(array $check, array $lead, array $referrer, int $customerId): int
    {
        // Haupteintrag
        $fraudLogId = $this->db->insert('fraud_log', [
            'lead_id' => $lead['id'] ?? null,
            'referrer_id' => $referrer['id'] ?? null,
            'customer_id' => $customerId,
            'fraud_type' => $check['flags'][0] ?? 'unknown',
            'score' => $check['score'],
            'details' => json_encode([
                'flags' => $check['flags'],
                'thresholds' => $check['thresholds']
            ]),
            'action_taken' => $check['action'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $fraudLogId;
    }
    
    /**
     * Statistiken abrufen
     */
    public function getStats(int $customerId = null, int $days = 30): array
    {
        $params = [$days];
        $whereCustomer = '';
        
        if ($customerId) {
            $whereCustomer = ' AND customer_id = ?';
            $params[] = $customerId;
        }
        
        $results = $this->db->fetchAll(
            "SELECT fraud_type, action_taken, COUNT(*) as count, AVG(score) as avg_score
             FROM fraud_log 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) {$whereCustomer}
             GROUP BY fraud_type, action_taken
             ORDER BY count DESC",
            $params
        );
        
        $stats = [
            'by_type' => [],
            'by_action' => ['allow' => 0, 'flag' => 0, 'review' => 0, 'block' => 0],
            'total' => 0
        ];
        
        foreach ($results as $row) {
            if (!isset($stats['by_type'][$row['fraud_type']])) {
                $stats['by_type'][$row['fraud_type']] = 0;
            }
            $stats['by_type'][$row['fraud_type']] += $row['count'];
            $stats['by_action'][$row['action_taken']] = ($stats['by_action'][$row['action_taken']] ?? 0) + $row['count'];
            $stats['total'] += $row['count'];
        }
        
        return $stats;
    }
    
    /**
     * Ausstehende Reviews abrufen
     */
    public function getPendingReviews(int $limit = 50): array
    {
        return $this->db->fetchAll(
            "SELECT fl.*, l.email as lead_email, l.name as lead_name, 
                    c.company_name as customer_name
             FROM fraud_log fl
             LEFT JOIN leads l ON fl.lead_id = l.id
             LEFT JOIN customers c ON fl.customer_id = c.id
             WHERE fl.action_taken = 'review' AND fl.reviewed_at IS NULL
             ORDER BY fl.created_at DESC
             LIMIT ?",
            [$limit]
        );
    }
    
    /**
     * Review abschließen
     */
    public function completeReview(int $fraudLogId, int $adminId, string $newAction): bool
    {
        return $this->db->execute(
            "UPDATE fraud_log SET action_taken = ?, reviewed_by = ?, reviewed_at = NOW()
             WHERE id = ?",
            [$newAction, $adminId, $fraudLogId]
        ) > 0;
    }
}
