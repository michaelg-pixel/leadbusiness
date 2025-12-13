<?php
/**
 * RateLimiter
 * 
 * Begrenzt Anfragen pro IP/Aktion.
 * Verwendet Redis wenn verfügbar, sonst Datenbank.
 * PHP 7.4+ kompatibel - ohne Namespace für Konsistenz
 */

use Leadbusiness\Database;

class RateLimiter
{
    private $db;
    private $config;
    private $useDatabase = true;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $securityConfig = require __DIR__ . '/../../config/security.php';
        $this->config = $securityConfig['rate_limits'];
    }
    
    /**
     * Rate-Limit prüfen
     * 
     * @param string $action Aktion (z.B. 'lead_registration')
     * @param string $identifier IP-Hash oder andere Kennung
     * @return bool true wenn erlaubt, false wenn Limit erreicht
     */
    public function checkLimit(string $action, string $identifier): bool
    {
        if (!isset($this->config[$action])) {
            return true; // Keine Limits definiert
        }
        
        $limit = $this->config[$action];
        $current = $this->getCurrentCount($action, $identifier, $limit['window']);
        
        if ($current >= $limit['max']) {
            $this->logRateLimitHit($action, $identifier);
            return false;
        }
        
        $this->incrementCount($action, $identifier, $limit['window']);
        return true;
    }
    
    /**
     * Verbleibende Anfragen abrufen
     */
    public function getRemainingAttempts(string $action, string $identifier): int
    {
        if (!isset($this->config[$action])) {
            return 999;
        }
        
        $limit = $this->config[$action];
        $current = $this->getCurrentCount($action, $identifier, $limit['window']);
        
        return max(0, $limit['max'] - $current);
    }
    
    /**
     * Zeit bis Reset abrufen (in Sekunden)
     */
    public function getResetTime(string $action, string $identifier): int
    {
        if (!isset($this->config[$action])) {
            return 0;
        }
        
        $windowStart = $this->getWindowStart($action, $identifier);
        if (!$windowStart) {
            return 0;
        }
        
        $limit = $this->config[$action];
        $resetTime = strtotime($windowStart) + $limit['window'];
        
        return max(0, $resetTime - time());
    }
    
    /**
     * Aktuellen Zählerstand abrufen (Datenbank)
     */
    private function getCurrentCount(string $action, string $identifier, int $window): int
    {
        $windowStart = date('Y-m-d H:i:s', time() - $window);
        
        $result = $this->db->fetch(
            "SELECT SUM(count) as total FROM rate_limit_log 
             WHERE ip_hash = ? AND action = ? AND window_start >= ?",
            [$identifier, $action, $windowStart]
        );
        
        return (int) ($result['total'] ?? 0);
    }
    
    /**
     * Zähler erhöhen
     */
    private function incrementCount(string $action, string $identifier, int $window): void
    {
        $now = date('Y-m-d H:i:s');
        $windowStart = date('Y-m-d H:i:00'); // Auf Minute runden
        
        // Versuche zu aktualisieren
        $updated = $this->db->execute(
            "UPDATE rate_limit_log SET count = count + 1, created_at = ? 
             WHERE ip_hash = ? AND action = ? AND window_start = ?",
            [$now, $identifier, $action, $windowStart]
        );
        
        // Falls nicht vorhanden, einfügen
        if ($updated === 0) {
            $this->db->execute(
                "INSERT INTO rate_limit_log (ip_hash, action, count, window_start, created_at) 
                 VALUES (?, ?, 1, ?, ?)",
                [$identifier, $action, $windowStart, $now]
            );
        }
    }
    
    /**
     * Window-Start abrufen
     */
    private function getWindowStart(string $action, string $identifier): ?string
    {
        $result = $this->db->fetch(
            "SELECT window_start FROM rate_limit_log 
             WHERE ip_hash = ? AND action = ? 
             ORDER BY window_start DESC LIMIT 1",
            [$identifier, $action]
        );
        
        return $result['window_start'] ?? null;
    }
    
    /**
     * Rate-Limit Hit loggen
     */
    private function logRateLimitHit(string $action, string $identifier): void
    {
        // Fraud-Log Eintrag
        $this->db->execute(
            "INSERT INTO fraud_log (customer_id, fraud_type, score, details, action_taken, created_at) 
             VALUES (0, 'rate_limit', 50, ?, 'blocked', NOW())",
            [json_encode(['action' => $action, 'ip_hash' => $identifier])]
        );
    }
    
    /**
     * Alte Einträge bereinigen (via Cron)
     */
    public function cleanup(int $maxAge = 86400): int
    {
        $cutoff = date('Y-m-d H:i:s', time() - $maxAge);
        
        return $this->db->execute(
            "DELETE FROM rate_limit_log WHERE created_at < ?",
            [$cutoff]
        );
    }
    
    /**
     * Alle Limits für eine IP zurücksetzen
     */
    public function resetLimits(string $identifier): void
    {
        $this->db->execute(
            "DELETE FROM rate_limit_log WHERE ip_hash = ?",
            [$identifier]
        );
    }
    
    /**
     * Statistiken abrufen
     */
    public function getStats(): array
    {
        $stats = [];
        
        foreach (array_keys($this->config) as $action) {
            $result = $this->db->fetch(
                "SELECT COUNT(*) as total_entries, SUM(count) as total_requests 
                 FROM rate_limit_log WHERE action = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)",
                [$action]
            );
            
            $stats[$action] = [
                'entries' => (int) $result['total_entries'],
                'requests' => (int) $result['total_requests']
            ];
        }
        
        return $stats;
    }
}
