<?php
/**
 * BotDetector
 * 
 * Erkennt Bots anhand verschiedener Signale.
 * PHP 7.4+ kompatibel - ohne Namespace für Konsistenz
 */

use Leadbusiness\Database;

class BotDetector
{
    private $db;
    private $config;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $securityConfig = require __DIR__ . '/../../config/security.php';
        $this->config = $securityConfig['bot_detection'];
    }
    
    /**
     * Request auf Bot-Verhalten analysieren
     * 
     * @param array $request Request-Daten (inkl. Honeypot, Timing, etc.)
     * @return array Score, is_bot, reasons
     */
    public function analyze(array $request): array
    {
        if (!$this->config['enabled']) {
            return ['score' => 0, 'is_bot' => false, 'reasons' => []];
        }
        
        $score = 0;
        $reasons = [];
        
        // 1. Honeypot-Feld ausgefüllt (verstecktes Feld)
        $honeypotField = $this->config['honeypot_field'];
        if (!empty($request[$honeypotField])) {
            $score += 100;
            $reasons[] = 'honeypot_filled';
        }
        
        // 2. Zu schnell ausgefüllt
        if (isset($request['form_started_at'])) {
            $formTime = time() - (int) $request['form_started_at'];
            if ($formTime < $this->config['min_form_time']) {
                $score += 80;
                $reasons[] = 'too_fast';
            }
        }
        
        // 3. JavaScript-Check nicht bestanden
        if (empty($request['js_check'])) {
            $score += 60;
            $reasons[] = 'no_javascript';
        }
        
        // 4. Verdächtiger User-Agent
        $userAgent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
        if ($this->hasSuspiciousUserAgent($userAgent)) {
            $score += 70;
            $reasons[] = 'bot_user_agent';
        }
        
        // 5. Fehlender User-Agent
        if (empty($userAgent)) {
            $score += 50;
            $reasons[] = 'missing_user_agent';
        }
        
        // 6. Fehlende Browser-Header
        if (empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $score += 30;
            $reasons[] = 'missing_accept_language';
        }
        
        if (empty($_SERVER['HTTP_ACCEPT'])) {
            $score += 20;
            $reasons[] = 'missing_accept';
        }
        
        // 7. Verdächtige Header-Kombination
        if ($this->hasInconsistentHeaders()) {
            $score += 40;
            $reasons[] = 'inconsistent_headers';
        }
        
        // 8. Referer-Check (optional)
        if (isset($request['check_referer']) && empty($_SERVER['HTTP_REFERER'])) {
            $score += 15;
            $reasons[] = 'missing_referer';
        }
        
        // 9. Zu viele Requests von dieser IP in kurzer Zeit
        if (isset($request['ip_hash'])) {
            $recentRequests = $this->getRecentRequestCount($request['ip_hash']);
            if ($recentRequests > 10) {
                $score += 30;
                $reasons[] = 'high_request_rate';
            }
        }
        
        $isBot = $score >= $this->config['score_threshold'];
        
        return [
            'score' => $score,
            'is_bot' => $isBot,
            'reasons' => $reasons,
            'threshold' => $this->config['score_threshold']
        ];
    }
    
    /**
     * Verdächtigen User-Agent prüfen
     */
    private function hasSuspiciousUserAgent(string $userAgent): bool
    {
        foreach ($this->config['suspicious_user_agents'] as $pattern) {
            if (strpos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        // Zusätzliche Muster
        $additionalPatterns = [
            'python-requests',
            'python-urllib',
            'java/',
            'httpclient',
            'libwww',
            'lwp-',
            'scrapy',
            'selenium',
            'puppeteer',
            'playwright'
        ];
        
        foreach ($additionalPatterns as $pattern) {
            if (strpos($userAgent, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Inkonsistente Header prüfen
     */
    private function hasInconsistentHeaders(): bool
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $acceptLanguage = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
        
        // Mobile User-Agent aber keine mobile Accept-Language
        if (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            // Mobile sollte normalerweise Accept-Language haben
            if (empty($acceptLanguage)) {
                return true;
            }
        }
        
        // Chrome/Firefox ohne DNT Header ist ok, aber IE mit modernsten Features ist verdächtig
        if (preg_match('/MSIE [6-9]\./i', $userAgent) && !empty($_SERVER['HTTP_SEC_FETCH_MODE'])) {
            return true; // Alte IE-Version mit modernen Security Headers
        }
        
        return false;
    }
    
    /**
     * Anzahl kürzlicher Requests von IP abrufen
     */
    private function getRecentRequestCount(string $ipHash): int
    {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM bot_detection_log 
             WHERE ip_hash = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)",
            [$ipHash]
        );
        
        return (int) ($result['count'] ?? 0);
    }
    
    /**
     * Bot-Detection loggen
     */
    public function log(array $analysis, string $action = 'allowed'): int
    {
        return $this->db->insert('bot_detection_log', [
            'ip_hash' => $_SERVER['REMOTE_ADDR'] ? hashIp($_SERVER['REMOTE_ADDR']) : null,
            'fingerprint' => $_POST['fingerprint'] ?? null,
            'score' => $analysis['score'],
            'reasons' => json_encode($analysis['reasons']),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'action_taken' => $action,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Aktion basierend auf Score bestimmen
     */
    public function determineAction(array $analysis): string
    {
        if ($analysis['score'] >= 80) {
            return 'block';
        }
        
        if ($analysis['score'] >= 50) {
            return 'captcha';
        }
        
        if ($analysis['score'] >= 30) {
            return 'flag';
        }
        
        return 'allow';
    }
    
    /**
     * HTML für Honeypot-Feld generieren
     */
    public function getHoneypotField(): string
    {
        $fieldName = $this->config['honeypot_field'];
        
        return sprintf(
            '<div style="position:absolute;left:-9999px;top:-9999px;"><label for="%s">Bitte leer lassen</label><input type="text" name="%s" id="%s" value="" tabindex="-1" autocomplete="off"></div>',
            $fieldName, $fieldName, $fieldName
        );
    }
    
    /**
     * HTML für Timing-Feld generieren
     */
    public function getTimingField(): string
    {
        return sprintf(
            '<input type="hidden" name="form_started_at" value="%d">',
            time()
        );
    }
    
    /**
     * HTML für JavaScript-Check generieren
     */
    public function getJsCheckField(): string
    {
        $token = bin2hex(random_bytes(8));
        
        return sprintf(
            '<input type="hidden" name="js_check" id="js_check" value="">
            <script>document.getElementById("js_check").value="%s";</script>',
            $token
        );
    }
    
    /**
     * Alle Bot-Schutz-Felder kombiniert
     */
    public function getAllProtectionFields(): string
    {
        return $this->getHoneypotField() . $this->getTimingField() . $this->getJsCheckField();
    }
    
    /**
     * Statistiken abrufen
     */
    public function getStats(int $hours = 24): array
    {
        $results = $this->db->fetchAll(
            "SELECT action_taken, COUNT(*) as count, AVG(score) as avg_score 
             FROM bot_detection_log 
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? HOUR)
             GROUP BY action_taken",
            [$hours]
        );
        
        $stats = [
            'allowed' => 0,
            'blocked' => 0,
            'captcha_shown' => 0,
            'total' => 0,
            'avg_score' => 0
        ];
        
        $totalScore = 0;
        foreach ($results as $row) {
            $stats[$row['action_taken']] = (int) $row['count'];
            $stats['total'] += (int) $row['count'];
            $totalScore += $row['avg_score'] * $row['count'];
        }
        
        if ($stats['total'] > 0) {
            $stats['avg_score'] = round($totalScore / $stats['total'], 2);
        }
        
        return $stats;
    }
    
    /**
     * Alte Logs bereinigen
     */
    public function cleanup(int $daysToKeep = 7): int
    {
        return $this->db->execute(
            "DELETE FROM bot_detection_log WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)",
            [$daysToKeep]
        );
    }
}
