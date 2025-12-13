<?php
/**
 * Auth Klasse
 * 
 * Authentifizierung und Session-Management für Kunden, Leads und Admins.
 * PHP 7.4+ kompatibel
 */

namespace Leadbusiness;

class Auth
{
    private Database $db;
    private array $config;
    private ?array $user = null;
    private ?string $userType = null;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->config = require __DIR__ . '/../config/security.php';
        $this->initSession();
    }
    
    /**
     * Session initialisieren
     */
    private function initSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $sessionConfig = $this->config['session'];
            
            session_set_cookie_params([
                'lifetime' => $sessionConfig['lifetime'],
                'path' => '/',
                'domain' => '',
                'secure' => $sessionConfig['cookie_secure'],
                'httponly' => $sessionConfig['cookie_httponly'],
                'samesite' => $sessionConfig['cookie_samesite']
            ]);
            
            session_start();
        }
        
        // Bestehende Session laden
        if (isset($_SESSION['user_type'], $_SESSION['user_id'])) {
            $this->loadUser($_SESSION['user_type'], $_SESSION['user_id']);
        }
        
        // Fallback: Alte Session-Variablen (customer_id)
        if (!$this->user && isset($_SESSION['customer_id'])) {
            $this->loadUser('customer', $_SESSION['customer_id']);
            if ($this->user) {
                $_SESSION['user_type'] = 'customer';
                $_SESSION['user_id'] = $_SESSION['customer_id'];
            }
        }
    }
    
    /**
     * Benutzer aus DB laden
     * PHP 7.4 kompatibel: switch statt match
     */
    private function loadUser(string $type, int $id): bool
    {
        switch ($type) {
            case 'customer':
                $table = 'customers';
                break;
            case 'lead':
                $table = 'leads';
                break;
            case 'admin':
                $table = 'admin_users';
                break;
            default:
                $table = null;
        }
        
        if (!$table) {
            return false;
        }
        
        $user = $this->db->find($table, $id);
        
        if ($user) {
            $this->user = $user;
            $this->userType = $type;
            return true;
        }
        
        return false;
    }
    
    /**
     * Kunde einloggen
     */
    public function loginCustomer(string $email, string $password): bool
    {
        $customer = $this->db->findBy('customers', ['email' => $email]);
        
        if (!$customer || !password_verify($password, $customer['password_hash'])) {
            return false;
        }
        
        $this->setSession('customer', $customer['id']);
        $this->user = $customer;
        $this->userType = 'customer';
        
        // Last login aktualisieren
        $this->db->update('customers', 
            ['last_login_at' => date('Y-m-d H:i:s')],
            'id = ?', [$customer['id']]
        );
        
        return true;
    }
    
    /**
     * Lead einloggen (über E-Mail-Link oder Referral-Code)
     */
    public function loginLead(string $email, int $customerId): bool
    {
        $lead = $this->db->fetch(
            "SELECT * FROM leads WHERE email = ? AND customer_id = ? AND status != 'blocked'",
            [$email, $customerId]
        );
        
        if (!$lead) {
            return false;
        }
        
        $this->setSession('lead', $lead['id']);
        $this->user = $lead;
        $this->userType = 'lead';
        
        // Last activity aktualisieren
        $this->db->update('leads',
            ['last_activity_at' => date('Y-m-d H:i:s')],
            'id = ?', [$lead['id']]
        );
        
        return true;
    }
    
    /**
     * Lead über Referral-Code einloggen
     */
    public function loginLeadByCode(string $referralCode): bool
    {
        $lead = $this->db->findBy('leads', ['referral_code' => $referralCode]);
        
        if (!$lead || $lead['status'] === 'blocked') {
            return false;
        }
        
        $this->setSession('lead', $lead['id']);
        $this->user = $lead;
        $this->userType = 'lead';
        
        // Last activity aktualisieren
        $this->db->update('leads',
            ['last_activity_at' => date('Y-m-d H:i:s')],
            'id = ?', [$lead['id']]
        );
        
        return true;
    }
    
    /**
     * Admin einloggen
     */
    public function loginAdmin(string $email, string $password): bool
    {
        $admin = $this->db->findBy('admin_users', ['email' => $email, 'is_active' => 1]);
        
        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return false;
        }
        
        $this->setSession('admin', $admin['id']);
        $this->user = $admin;
        $this->userType = 'admin';
        
        // Last login aktualisieren
        $this->db->update('admin_users',
            ['last_login_at' => date('Y-m-d H:i:s')],
            'id = ?', [$admin['id']]
        );
        
        return true;
    }
    
    /**
     * Session setzen
     */
    private function setSession(string $type, int $id): void
    {
        // Session-ID regenerieren für Sicherheit
        session_regenerate_id(true);
        
        $_SESSION['user_type'] = $type;
        $_SESSION['user_id'] = $id;
        $_SESSION['created_at'] = time();
        $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'] ?? '';
        
        // Auch alte Session-Variablen für Kompatibilität
        if ($type === 'customer') {
            $_SESSION['customer_id'] = $id;
            $_SESSION['customer_email'] = $this->user['email'] ?? '';
        }
        
        // In DB speichern
        $this->db->execute(
            "INSERT INTO sessions (session_id, user_type, user_id, ip_address, user_agent, expires_at)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE user_type = VALUES(user_type), user_id = VALUES(user_id), 
             ip_address = VALUES(ip_address), last_activity = NOW()",
            [
                session_id(),
                $type,
                $id,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null,
                date('Y-m-d H:i:s', time() + $this->config['session']['lifetime'])
            ]
        );
    }
    
    /**
     * Ausloggen
     */
    public function logout(): void
    {
        // Session aus DB löschen
        if (session_id()) {
            $this->db->delete('sessions', 'session_id = ?', [session_id()]);
        }
        
        // Session-Variablen löschen
        $_SESSION = [];
        
        // Session-Cookie löschen
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Session zerstören
        session_destroy();
        
        $this->user = null;
        $this->userType = null;
    }
    
    /**
     * Ist eingeloggt?
     */
    public function isLoggedIn(): bool
    {
        return $this->user !== null;
    }
    
    /**
     * Ist Kunde eingeloggt?
     */
    public function isCustomer(): bool
    {
        return $this->userType === 'customer';
    }
    
    /**
     * Ist Lead eingeloggt?
     */
    public function isLead(): bool
    {
        return $this->userType === 'lead';
    }
    
    /**
     * Ist Admin eingeloggt?
     */
    public function isAdmin(): bool
    {
        return $this->userType === 'admin';
    }
    
    /**
     * Benutzer-Typ abrufen
     */
    public function getUserType(): ?string
    {
        return $this->userType;
    }
    
    /**
     * Benutzer-Daten abrufen
     */
    public function getUser(): ?array
    {
        return $this->user;
    }
    
    /**
     * Aktuellen Kunden abrufen (Alias für getUser bei Kunden)
     */
    public function getCurrentCustomer(): ?array
    {
        if ($this->userType === 'customer') {
            return $this->user;
        }
        return null;
    }
    
    /**
     * Aktuellen Lead abrufen
     */
    public function getCurrentLead(): ?array
    {
        if ($this->userType === 'lead') {
            return $this->user;
        }
        return null;
    }
    
    /**
     * Aktuellen Admin abrufen
     */
    public function getCurrentAdmin(): ?array
    {
        if ($this->userType === 'admin') {
            return $this->user;
        }
        return null;
    }
    
    /**
     * Benutzer-ID abrufen
     */
    public function getUserId(): ?int
    {
        return $this->user['id'] ?? null;
    }
    
    /**
     * Kunde als Admin? (hat Admin-Rechte)
     */
    public function isSuperAdmin(): bool
    {
        return $this->isAdmin() && ($this->user['role'] ?? '') === 'super_admin';
    }
    
    /**
     * Passwort hashen
     */
    public function hashPassword(string $password): string
    {
        return password_hash(
            $password,
            $this->config['password']['hash_algo'],
            ['cost' => $this->config['password']['hash_cost']]
        );
    }
    
    /**
     * Passwort verifizieren
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Passwort-Anforderungen prüfen
     */
    public function validatePassword(string $password): array
    {
        $errors = [];
        $rules = $this->config['password'];
        
        if (strlen($password) < $rules['min_length']) {
            $errors[] = "Passwort muss mindestens {$rules['min_length']} Zeichen lang sein";
        }
        
        if ($rules['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "Passwort muss mindestens einen Großbuchstaben enthalten";
        }
        
        if ($rules['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = "Passwort muss mindestens einen Kleinbuchstaben enthalten";
        }
        
        if ($rules['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = "Passwort muss mindestens eine Zahl enthalten";
        }
        
        if ($rules['require_special'] && !preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Passwort muss mindestens ein Sonderzeichen enthalten";
        }
        
        return $errors;
    }
    
    /**
     * CSRF-Token generieren
     */
    public function generateCsrfToken(): string
    {
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time']) 
            || (time() - $_SESSION['csrf_token_time']) > $this->config['csrf']['token_lifetime']) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * CSRF-Token validieren
     */
    public function validateCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * CSRF-Token aus Request abrufen
     */
    public function getCsrfTokenFromRequest(): ?string
    {
        $fieldName = $this->config['csrf']['field_name'];
        $headerName = $this->config['csrf']['header_name'];
        
        // Aus POST
        if (isset($_POST[$fieldName])) {
            return $_POST[$fieldName];
        }
        
        // Aus Header
        $headers = getallheaders();
        if (isset($headers[$headerName])) {
            return $headers[$headerName];
        }
        
        return null;
    }
    
    /**
     * Require: Muss eingeloggt sein
     */
    public function requireLogin(string $type = null): void
    {
        if (!$this->isLoggedIn()) {
            $this->redirectToLogin($type);
        }
        
        if ($type && $this->userType !== $type) {
            $this->redirectToLogin($type);
        }
    }
    
    /**
     * Zur Login-Seite weiterleiten
     * PHP 7.4 kompatibel: switch statt match
     */
    private function redirectToLogin(?string $type): void
    {
        switch ($type) {
            case 'admin':
                $url = '/admin/login.php';
                break;
            case 'customer':
                $url = '/dashboard/login.php';
                break;
            case 'lead':
                $url = '/lead/';
                break;
            default:
                $url = '/dashboard/login.php';
        }
        
        header("Location: {$url}");
        exit;
    }
    
    /**
     * Kunde muss bestimmten Plan haben
     */
    public function requirePlan(array $allowedPlans): void
    {
        $this->requireLogin('customer');
        
        if (!in_array($this->user['plan'], $allowedPlans)) {
            header('HTTP/1.1 403 Forbidden');
            exit('Upgrade erforderlich');
        }
    }
    
    /**
     * Feature verfügbar für aktuellen Plan?
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->isCustomer()) {
            return false;
        }
        
        $settings = require __DIR__ . '/../config/settings.php';
        $plan = $this->user['plan'] ?? 'starter';
        
        return $settings['plans'][$plan]['features'][$feature] ?? false;
    }
    
    /**
     * Plan-Limit abrufen
     * PHP 7.4 kompatibel: mixed Type Hint entfernt
     * @return mixed
     */
    public function getPlanLimit(string $limit)
    {
        if (!$this->isCustomer()) {
            return null;
        }
        
        $settings = require __DIR__ . '/../config/settings.php';
        $plan = $this->user['plan'] ?? 'starter';
        
        return $settings['plans'][$plan][$limit] ?? null;
    }
}
