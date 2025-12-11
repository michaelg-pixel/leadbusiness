<?php
/**
 * Lead Authentication Service
 * 
 * Handles lead login via:
 * - Magic Link (E-Mail)
 * - Password (optional)
 * - Session management
 */

class LeadAuthService {
    
    private $db;
    private const SESSION_DURATION = 30 * 24 * 60 * 60; // 30 Tage
    private const MAGIC_LINK_DURATION = 15 * 60; // 15 Minuten
    private const COOKIE_NAME = 'lead_session';
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Prüft ob Lead eingeloggt ist
     */
    public function check(): ?array {
        // 1. Session Cookie prüfen
        $sessionToken = $_COOKIE[self::COOKIE_NAME] ?? null;
        
        if ($sessionToken) {
            $session = $this->db->fetch(
                "SELECT ls.*, l.email, l.name, l.referral_code, l.status
                 FROM lead_sessions ls
                 JOIN leads l ON ls.lead_id = l.id
                 WHERE ls.session_token = ? 
                 AND ls.expires_at > NOW()
                 AND l.status = 'active'",
                [$sessionToken]
            );
            
            if ($session) {
                // Session aktualisieren
                $this->db->execute(
                    "UPDATE lead_sessions SET last_used_at = NOW() WHERE id = ?",
                    [$session['id']]
                );
                
                return $this->getLeadData($session['lead_id']);
            }
        }
        
        // 2. Referral Code aus URL/Session prüfen (Legacy-Fallback)
        session_start();
        $code = $_GET['code'] ?? $_SESSION['lead_code'] ?? null;
        
        if ($code) {
            $lead = $this->db->fetch(
                "SELECT * FROM leads WHERE referral_code = ? AND status = 'active'",
                [strtoupper($code)]
            );
            
            if ($lead) {
                $_SESSION['lead_code'] = $lead['referral_code'];
                return $lead;
            }
        }
        
        return null;
    }
    
    /**
     * Magic Link senden
     */
    public function sendMagicLink(string $email, int $customerId): array {
        // Lead finden
        $lead = $this->db->fetch(
            "SELECT l.* FROM leads l
             JOIN campaigns c ON l.campaign_id = c.id
             WHERE l.email = ? AND c.customer_id = ? AND l.status = 'active'",
            [$email, $customerId]
        );
        
        if (!$lead) {
            return ['success' => false, 'error' => 'E-Mail nicht gefunden'];
        }
        
        // Token generieren
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::MAGIC_LINK_DURATION);
        
        // Token speichern
        $this->db->execute(
            "UPDATE leads SET magic_link_token = ?, magic_link_expires_at = ? WHERE id = ?",
            [$token, $expiresAt, $lead['id']]
        );
        
        // Kunden-Info für E-Mail
        $customer = $this->db->fetch(
            "SELECT c.* FROM customers c
             JOIN campaigns ca ON ca.customer_id = c.id
             WHERE ca.id = ?",
            [$lead['campaign_id']]
        );
        
        // Magic Link E-Mail senden
        $this->sendMagicLinkEmail($lead, $customer, $token);
        
        // Activity Log
        $this->logActivity($lead['id'], 'login', ['method' => 'magic_link_requested']);
        
        return ['success' => true, 'message' => 'Login-Link wurde gesendet'];
    }
    
    /**
     * Magic Link verifizieren und einloggen
     */
    public function verifyMagicLink(string $token): array {
        $lead = $this->db->fetch(
            "SELECT * FROM leads 
             WHERE magic_link_token = ? 
             AND magic_link_expires_at > NOW()
             AND status = 'active'",
            [$token]
        );
        
        if (!$lead) {
            return ['success' => false, 'error' => 'Link ungültig oder abgelaufen'];
        }
        
        // Token invalidieren
        $this->db->execute(
            "UPDATE leads SET magic_link_token = NULL, magic_link_expires_at = NULL WHERE id = ?",
            [$lead['id']]
        );
        
        // Session erstellen
        $session = $this->createSession($lead['id']);
        
        // Activity Log
        $this->logActivity($lead['id'], 'login', ['method' => 'magic_link']);
        
        return [
            'success' => true,
            'lead' => $this->getLeadData($lead['id']),
            'redirect' => '/lead/dashboard.php'
        ];
    }
    
    /**
     * Mit Passwort einloggen
     */
    public function loginWithPassword(string $email, string $password, int $customerId): array {
        $lead = $this->db->fetch(
            "SELECT l.* FROM leads l
             JOIN campaigns c ON l.campaign_id = c.id
             WHERE l.email = ? AND c.customer_id = ? AND l.status = 'active'",
            [$email, $customerId]
        );
        
        if (!$lead) {
            return ['success' => false, 'error' => 'E-Mail oder Passwort falsch'];
        }
        
        if (empty($lead['password_hash'])) {
            return ['success' => false, 'error' => 'Bitte nutzen Sie den Login-Link per E-Mail'];
        }
        
        if (!password_verify($password, $lead['password_hash'])) {
            return ['success' => false, 'error' => 'E-Mail oder Passwort falsch'];
        }
        
        // Session erstellen
        $session = $this->createSession($lead['id']);
        
        // Activity Log
        $this->logActivity($lead['id'], 'login', ['method' => 'password']);
        
        return [
            'success' => true,
            'lead' => $this->getLeadData($lead['id']),
            'redirect' => '/lead/dashboard.php'
        ];
    }
    
    /**
     * Passwort setzen/ändern
     */
    public function setPassword(int $leadId, string $password): array {
        if (strlen($password) < 8) {
            return ['success' => false, 'error' => 'Passwort muss mindestens 8 Zeichen haben'];
        }
        
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->db->execute(
            "UPDATE leads SET password_hash = ? WHERE id = ?",
            [$hash, $leadId]
        );
        
        // Activity Log
        $this->logActivity($leadId, 'password_set', []);
        
        return ['success' => true, 'message' => 'Passwort wurde gespeichert'];
    }
    
    /**
     * Session erstellen
     */
    private function createSession(int $leadId): array {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::SESSION_DURATION);
        
        // Alte Sessions aufräumen (max 5 pro Lead)
        $this->db->execute(
            "DELETE FROM lead_sessions 
             WHERE lead_id = ? 
             AND id NOT IN (
                SELECT id FROM (
                    SELECT id FROM lead_sessions WHERE lead_id = ? ORDER BY last_used_at DESC LIMIT 4
                ) AS keep
             )",
            [$leadId, $leadId]
        );
        
        // Neue Session erstellen
        $this->db->execute(
            "INSERT INTO lead_sessions (lead_id, session_token, user_agent, ip_hash, expires_at)
             VALUES (?, ?, ?, ?, ?)",
            [
                $leadId,
                $token,
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''),
                $expiresAt
            ]
        );
        
        // Login-Stats aktualisieren
        $this->db->execute(
            "UPDATE leads SET last_login_at = NOW(), login_count = login_count + 1 WHERE id = ?",
            [$leadId]
        );
        
        // Cookie setzen
        $this->setSessionCookie($token);
        
        return ['token' => $token, 'expires_at' => $expiresAt];
    }
    
    /**
     * Cookie setzen
     */
    private function setSessionCookie(string $token): void {
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        
        setcookie(
            self::COOKIE_NAME,
            $token,
            [
                'expires' => time() + self::SESSION_DURATION,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }
    
    /**
     * Ausloggen
     */
    public function logout(): void {
        $token = $_COOKIE[self::COOKIE_NAME] ?? null;
        
        if ($token) {
            // Session aus DB löschen
            $session = $this->db->fetch(
                "SELECT lead_id FROM lead_sessions WHERE session_token = ?",
                [$token]
            );
            
            if ($session) {
                $this->db->execute(
                    "DELETE FROM lead_sessions WHERE session_token = ?",
                    [$token]
                );
                
                $this->logActivity($session['lead_id'], 'logout', []);
            }
        }
        
        // Cookie löschen
        setcookie(self::COOKIE_NAME, '', time() - 3600, '/');
        
        // PHP Session löschen
        session_start();
        unset($_SESSION['lead_code']);
    }
    
    /**
     * Lead-Daten laden
     */
    public function getLeadData(int $leadId): ?array {
        return $this->db->fetch(
            "SELECT l.*, c.company_name, c.subdomain, c.logo_url, c.primary_color,
                    c.leaderboard_enabled, ca.id as campaign_id
             FROM leads l
             JOIN campaigns ca ON l.campaign_id = ca.id
             JOIN customers c ON ca.customer_id = c.id
             WHERE l.id = ? AND l.status = 'active'",
            [$leadId]
        );
    }
    
    /**
     * Aktivität loggen
     */
    private function logActivity(int $leadId, string $type, array $details): void {
        try {
            $this->db->execute(
                "INSERT INTO lead_activity_log (lead_id, activity_type, details, ip_hash, user_agent)
                 VALUES (?, ?, ?, ?, ?)",
                [
                    $leadId,
                    $type,
                    json_encode($details),
                    hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''),
                    $_SERVER['HTTP_USER_AGENT'] ?? ''
                ]
            );
        } catch (Exception $e) {
            // Silent fail - Logging sollte Hauptfunktion nicht blockieren
        }
    }
    
    /**
     * Magic Link E-Mail senden
     */
    private function sendMagicLinkEmail(array $lead, array $customer, string $token): void {
        require_once __DIR__ . '/MailgunService.php';
        
        $mailgun = new MailgunService();
        
        $loginUrl = "https://{$customer['subdomain']}.empfohlen.de/lead/verify.php?token=" . $token;
        
        $subject = "Ihr Login-Link für das Empfehlungsprogramm";
        
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <h2 style='color: {$customer['primary_color']};'>Hallo " . htmlspecialchars($lead['name'] ?: 'Empfehler') . "!</h2>
            
            <p>Sie haben einen Login-Link für Ihr Empfehlungs-Dashboard bei <strong>" . htmlspecialchars($customer['company_name']) . "</strong> angefordert.</p>
            
            <p style='text-align: center; margin: 30px 0;'>
                <a href='{$loginUrl}' 
                   style='background-color: {$customer['primary_color']}; color: white; padding: 15px 30px; 
                          text-decoration: none; border-radius: 8px; font-weight: bold; display: inline-block;'>
                    Jetzt einloggen
                </a>
            </p>
            
            <p style='color: #666; font-size: 14px;'>
                Dieser Link ist 15 Minuten gültig. Falls Sie keinen Login angefordert haben, 
                können Sie diese E-Mail ignorieren.
            </p>
            
            <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
            
            <p style='color: #999; font-size: 12px;'>
                " . htmlspecialchars($customer['company_name']) . " - Empfehlungsprogramm
            </p>
        </div>
        ";
        
        $mailgun->send([
            'to' => $lead['email'],
            'subject' => $subject,
            'html' => $html,
            'customer_id' => $customer['id'] ?? null,
            'lead_id' => $lead['id'],
            'email_type' => 'magic_link'
        ]);
    }
    
    /**
     * Benachrichtigungs-Einstellungen aktualisieren
     */
    public function updateNotificationSettings(int $leadId, array $settings): array {
        $allowed = ['notification_new_conversion', 'notification_reward_unlocked', 
                    'notification_weekly_summary', 'notification_tips'];
        
        $updates = [];
        $params = [];
        
        foreach ($allowed as $key) {
            if (isset($settings[$key])) {
                $updates[] = "$key = ?";
                $params[] = $settings[$key] ? 1 : 0;
            }
        }
        
        if (empty($updates)) {
            return ['success' => false, 'error' => 'Keine Einstellungen angegeben'];
        }
        
        $params[] = $leadId;
        
        $this->db->execute(
            "UPDATE leads SET " . implode(', ', $updates) . " WHERE id = ?",
            $params
        );
        
        $this->logActivity($leadId, 'notifications_updated', $settings);
        
        return ['success' => true, 'message' => 'Einstellungen gespeichert'];
    }
}
