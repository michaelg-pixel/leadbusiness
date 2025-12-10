<?php
/**
 * API Middleware
 * Authentifizierung, Rate-Limiting, Logging
 */

namespace Leadbusiness\Api;

class ApiMiddleware
{
    private $db;
    private $apiKey;
    private $customer;
    private $permissions;
    private $startTime;
    
    public function __construct()
    {
        $this->startTime = microtime(true);
        $this->db = \Leadbusiness\Database::getInstance();
    }
    
    /**
     * API-Request authentifizieren
     */
    public function authenticate(): bool
    {
        // Headers auslesen
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        $secretKey = $_SERVER['HTTP_X_API_SECRET'] ?? '';
        
        if (empty($apiKey) || empty($secretKey)) {
            $this->error(401, 'Missing API credentials', 'API_KEY_MISSING');
            return false;
        }
        
        // API-Key prüfen
        $this->apiKey = $this->db->fetch(
            "SELECT ak.*, c.plan, c.subdomain, c.company_name 
             FROM api_keys ak 
             JOIN customers c ON ak.customer_id = c.id 
             WHERE ak.api_key = ? AND ak.secret_key = ? AND ak.is_active = 1",
            [$apiKey, $secretKey]
        );
        
        if (!$this->apiKey) {
            $this->error(401, 'Invalid API credentials', 'API_KEY_INVALID');
            return false;
        }
        
        // Ablaufdatum prüfen
        if ($this->apiKey['expires_at'] && strtotime($this->apiKey['expires_at']) < time()) {
            $this->error(401, 'API key has expired', 'API_KEY_EXPIRED');
            return false;
        }
        
        // Plan prüfen (nur Professional/Enterprise)
        if (!in_array($this->apiKey['plan'], ['professional', 'enterprise'])) {
            $this->error(403, 'API access requires Professional or Enterprise plan', 'PLAN_REQUIRED');
            return false;
        }
        
        // Permissions laden
        $this->permissions = json_decode($this->apiKey['permissions'], true) ?: [];
        
        // Customer-Daten laden
        $this->customer = $this->db->fetch(
            "SELECT * FROM customers WHERE id = ?",
            [$this->apiKey['customer_id']]
        );
        
        return true;
    }
    
    /**
     * Rate-Limiting prüfen
     */
    public function checkRateLimit(): bool
    {
        $limit = $this->apiKey['rate_limit_per_hour'] ?? 1000;
        
        // Requests in der letzten Stunde zählen
        $count = $this->db->fetch(
            "SELECT COUNT(*) as cnt FROM api_logs 
             WHERE api_key_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [$this->apiKey['id']]
        )['cnt'];
        
        if ($count >= $limit) {
            $this->error(429, 'Rate limit exceeded', 'RATE_LIMIT_EXCEEDED', [
                'limit' => $limit,
                'remaining' => 0,
                'reset' => date('c', strtotime('+1 hour'))
            ]);
            return false;
        }
        
        // Rate-Limit Headers setzen
        header("X-RateLimit-Limit: {$limit}");
        header("X-RateLimit-Remaining: " . ($limit - $count - 1));
        
        return true;
    }
    
    /**
     * Berechtigung prüfen
     */
    public function hasPermission(string $resource, string $action = 'read'): bool
    {
        $perms = $this->permissions[$resource] ?? [];
        return $perms[$action] ?? false;
    }
    
    /**
     * Berechtigung prüfen und ggf. Fehler zurückgeben
     */
    public function requirePermission(string $resource, string $action = 'read'): bool
    {
        if (!$this->hasPermission($resource, $action)) {
            $this->error(403, "Permission denied for {$action} on {$resource}", 'PERMISSION_DENIED');
            return false;
        }
        return true;
    }
    
    /**
     * Request loggen
     */
    public function logRequest(string $endpoint, int $statusCode = 200, ?string $errorMessage = null): void
    {
        $responseTime = (int)((microtime(true) - $this->startTime) * 1000);
        
        $this->db->query(
            "INSERT INTO api_logs (api_key_id, customer_id, endpoint, method, status_code, response_time_ms, ip_address, user_agent, error_message) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $this->apiKey['id'] ?? 0,
                $this->apiKey['customer_id'] ?? 0,
                $endpoint,
                $_SERVER['REQUEST_METHOD'],
                $statusCode,
                $responseTime,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $errorMessage
            ]
        );
        
        // API-Key Stats aktualisieren
        if ($this->apiKey) {
            $this->db->query(
                "UPDATE api_keys SET last_used_at = NOW(), total_requests = total_requests + 1 WHERE id = ?",
                [$this->apiKey['id']]
            );
        }
    }
    
    /**
     * JSON-Erfolgsantwort
     */
    public function success(array $data, int $code = 200): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        
        $response = [
            'success' => true,
            'data' => $data
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * JSON-Fehlerantwort
     */
    public function error(int $code, string $message, string $errorCode = 'ERROR', array $extra = []): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        
        $response = [
            'success' => false,
            'error' => [
                'code' => $errorCode,
                'message' => $message,
                'status' => $code
            ]
        ];
        
        if (!empty($extra)) {
            $response['error'] = array_merge($response['error'], $extra);
        }
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * Paginierte Liste
     */
    public function paginate(array $items, int $total, int $page, int $perPage): array
    {
        return [
            'items' => $items,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'total_pages' => ceil($total / $perPage),
                'has_more' => ($page * $perPage) < $total
            ]
        ];
    }
    
    /**
     * Request-Body als JSON parsen
     */
    public function getJsonBody(): ?array
    {
        $body = file_get_contents('php://input');
        if (empty($body)) {
            return [];
        }
        
        $data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        
        return $data;
    }
    
    /**
     * Customer-ID abrufen
     */
    public function getCustomerId(): int
    {
        return $this->apiKey['customer_id'] ?? 0;
    }
    
    /**
     * Customer-Daten abrufen
     */
    public function getCustomer(): ?array
    {
        return $this->customer;
    }
    
    /**
     * API-Key-Daten abrufen
     */
    public function getApiKey(): ?array
    {
        return $this->apiKey;
    }
    
    /**
     * Query-Parameter mit Defaults
     */
    public function getQueryParam(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Integer Query-Parameter
     */
    public function getIntParam(string $key, int $default = 0, int $min = 0, int $max = PHP_INT_MAX): int
    {
        $value = (int)($this->getQueryParam($key, $default));
        return max($min, min($max, $value));
    }
}

/**
 * CORS-Headers setzen
 */
function setCorsHeaders(): void
{
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-API-Key, X-API-Secret');
    header('Access-Control-Max-Age: 86400');
    
    // Preflight-Request
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Standard API-Header setzen
 */
function setApiHeaders(): void
{
    header('Content-Type: application/json');
    header('X-API-Version: 1.0');
}
