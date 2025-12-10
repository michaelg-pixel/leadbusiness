<?php
/**
 * Leadbusiness - API Handler
 * 
 * Authentifizierung, Rate-Limiting und Response-Handling für die REST API
 */

namespace Leadbusiness;

class ApiHandler {
    
    private $db;
    private $customer = null;
    private $startTime;
    
    // Rate Limits nach Plan
    private $rateLimits = [
        'professional' => [
            'per_minute' => 60,
            'per_day' => 5000,
            'per_month' => 100000
        ],
        'enterprise' => [
            'per_minute' => 300,
            'per_day' => 50000,
            'per_month' => 1000000
        ]
    ];
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->startTime = microtime(true);
        
        // CORS Headers
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-API-Key, X-API-Secret');
        header('X-RateLimit-Limit: 0');
        header('X-RateLimit-Remaining: 0');
        
        // Preflight Request
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
    }
    
    /**
     * Authentifiziert die API-Anfrage
     */
    public function authenticate(): bool {
        $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
        $apiSecret = $_SERVER['HTTP_X_API_SECRET'] ?? '';
        
        if (empty($apiKey)) {
            $this->errorResponse(401, 'API key required', 'MISSING_API_KEY');
            return false;
        }
        
        // Kunde anhand API-Key finden
        $this->customer = $this->db->fetch(
            "SELECT * FROM customers WHERE api_key = ? AND api_enabled = 1",
            [$apiKey]
        );
        
        if (!$this->customer) {
            $this->logRequest('auth', 401);
            $this->errorResponse(401, 'Invalid API key or API access disabled', 'INVALID_API_KEY');
            return false;
        }
        
        // Secret prüfen (optional aber empfohlen)
        if (!empty($this->customer['api_secret']) && $apiSecret !== $this->customer['api_secret']) {
            $this->logRequest('auth', 401);
            $this->errorResponse(401, 'Invalid API secret', 'INVALID_API_SECRET');
            return false;
        }
        
        // Plan prüfen (nur Professional und Enterprise)
        if (!in_array($this->customer['plan'], ['professional', 'enterprise'])) {
            $this->logRequest('auth', 403);
            $this->errorResponse(403, 'API access requires Professional or Enterprise plan', 'PLAN_UPGRADE_REQUIRED');
            return false;
        }
        
        // Subscription Status prüfen
        if ($this->customer['subscription_status'] !== 'active' && $this->customer['subscription_status'] !== 'trial') {
            $this->logRequest('auth', 403);
            $this->errorResponse(403, 'Subscription is not active', 'SUBSCRIPTION_INACTIVE');
            return false;
        }
        
        return true;
    }
    
    /**
     * Prüft Rate Limits
     */
    public function checkRateLimit(): bool {
        if (!$this->customer) {
            return false;
        }
        
        $plan = $this->customer['plan'];
        $limits = $this->rateLimits[$plan] ?? $this->rateLimits['professional'];
        
        // Requests heute prüfen
        $today = date('Y-m-d');
        $requestsToday = $this->db->fetch(
            "SELECT COUNT(*) as count FROM api_logs 
             WHERE customer_id = ? AND DATE(created_at) = ?",
            [$this->customer['id'], $today]
        );
        
        // Requests pro Minute prüfen
        $oneMinuteAgo = date('Y-m-d H:i:s', strtotime('-1 minute'));
        $requestsMinute = $this->db->fetch(
            "SELECT COUNT(*) as count FROM api_logs 
             WHERE customer_id = ? AND created_at >= ?",
            [$this->customer['id'], $oneMinuteAgo]
        );
        
        // Rate Limit Headers setzen
        header('X-RateLimit-Limit-Day: ' . $limits['per_day']);
        header('X-RateLimit-Remaining-Day: ' . max(0, $limits['per_day'] - ($requestsToday['count'] ?? 0)));
        header('X-RateLimit-Limit-Minute: ' . $limits['per_minute']);
        header('X-RateLimit-Remaining-Minute: ' . max(0, $limits['per_minute'] - ($requestsMinute['count'] ?? 0)));
        
        // Limit erreicht?
        if (($requestsToday['count'] ?? 0) >= $limits['per_day']) {
            $this->logRequest('rate_limit', 429);
            $this->errorResponse(429, 'Daily rate limit exceeded', 'RATE_LIMIT_DAY');
            return false;
        }
        
        if (($requestsMinute['count'] ?? 0) >= $limits['per_minute']) {
            header('Retry-After: 60');
            $this->logRequest('rate_limit', 429);
            $this->errorResponse(429, 'Rate limit exceeded. Try again in 60 seconds.', 'RATE_LIMIT_MINUTE');
            return false;
        }
        
        return true;
    }
    
    /**
     * Gibt den authentifizierten Kunden zurück
     */
    public function getCustomer(): ?array {
        return $this->customer;
    }
    
    /**
     * Gibt die Kunden-ID zurück
     */
    public function getCustomerId(): ?int {
        return $this->customer['id'] ?? null;
    }
    
    /**
     * Erfolgreiche Antwort senden
     */
    public function successResponse($data, int $statusCode = 200): void {
        http_response_code($statusCode);
        
        $response = [
            'success' => true,
            'data' => $data,
            'meta' => [
                'timestamp' => date('c'),
                'response_time_ms' => round((microtime(true) - $this->startTime) * 1000, 2)
            ]
        ];
        
        $this->logRequest($_SERVER['REQUEST_URI'], $statusCode);
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Paginierte Antwort senden
     */
    public function paginatedResponse(array $data, int $total, int $page, int $limit): void {
        $totalPages = ceil($total / $limit);
        
        $response = [
            'success' => true,
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'count' => count($data),
                'per_page' => $limit,
                'current_page' => $page,
                'total_pages' => $totalPages,
                'has_more' => $page < $totalPages
            ],
            'meta' => [
                'timestamp' => date('c'),
                'response_time_ms' => round((microtime(true) - $this->startTime) * 1000, 2)
            ]
        ];
        
        $this->logRequest($_SERVER['REQUEST_URI'], 200);
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Fehler-Antwort senden
     */
    public function errorResponse(int $statusCode, string $message, string $code = 'ERROR'): void {
        http_response_code($statusCode);
        
        $response = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
                'status' => $statusCode
            ],
            'meta' => [
                'timestamp' => date('c'),
                'response_time_ms' => round((microtime(true) - $this->startTime) * 1000, 2)
            ]
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    /**
     * Validierung der Request-Daten
     */
    public function validateRequest(array $rules): array {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $input[$field] ?? null;
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = "Field '$field' is required";
                continue;
            }
            
            if (!empty($value)) {
                if (strpos($rule, 'email') !== false && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "Field '$field' must be a valid email";
                }
                
                if (strpos($rule, 'url') !== false && !filter_var($value, FILTER_VALIDATE_URL)) {
                    $errors[$field] = "Field '$field' must be a valid URL";
                }
                
                if (preg_match('/max:(\d+)/', $rule, $matches) && strlen($value) > $matches[1]) {
                    $errors[$field] = "Field '$field' must not exceed {$matches[1]} characters";
                }
                
                if (preg_match('/min:(\d+)/', $rule, $matches) && strlen($value) < $matches[1]) {
                    $errors[$field] = "Field '$field' must be at least {$matches[1]} characters";
                }
            }
        }
        
        if (!empty($errors)) {
            $this->errorResponse(422, 'Validation failed', 'VALIDATION_ERROR');
        }
        
        return $input;
    }
    
    /**
     * Request-Body als Array holen
     */
    public function getRequestBody(): array {
        return json_decode(file_get_contents('php://input'), true) ?? [];
    }
    
    /**
     * Query-Parameter holen
     */
    public function getQueryParam(string $key, $default = null) {
        return $_GET[$key] ?? $default;
    }
    
    /**
     * Paginierung Parameter
     */
    public function getPagination(): array {
        $page = max(1, (int)($this->getQueryParam('page', 1)));
        $limit = min(100, max(1, (int)($this->getQueryParam('limit', 20))));
        $offset = ($page - 1) * $limit;
        
        return compact('page', 'limit', 'offset');
    }
    
    /**
     * API-Request loggen
     */
    private function logRequest(string $endpoint, int $statusCode): void {
        if (!$this->customer) {
            return;
        }
        
        try {
            $this->db->query(
                "INSERT INTO api_logs (customer_id, endpoint, method, status_code, ip_address, user_agent, response_time_ms)
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $this->customer['id'],
                    substr($endpoint, 0, 100),
                    $_SERVER['REQUEST_METHOD'],
                    $statusCode,
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500),
                    round((microtime(true) - $this->startTime) * 1000)
                ]
            );
            
            // Kunden-Stats updaten
            $this->db->query(
                "UPDATE customers SET 
                    api_requests_today = api_requests_today + 1,
                    api_requests_month = api_requests_month + 1,
                    api_last_request_at = NOW()
                 WHERE id = ?",
                [$this->customer['id']]
            );
        } catch (\Exception $e) {
            // Logging-Fehler ignorieren
        }
    }
    
    /**
     * API-Key generieren
     */
    public static function generateApiKey(): string {
        return 'lb_' . bin2hex(random_bytes(24));
    }
    
    /**
     * API-Secret generieren
     */
    public static function generateApiSecret(): string {
        return 'lbs_' . bin2hex(random_bytes(32));
    }
}
