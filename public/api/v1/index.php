<?php
/**
 * Leadbusiness REST API v1 - Router
 * 
 * Haupteinstiegspunkt für alle API-Anfragen
 * URL: /api/v1/{endpoint}
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/Database.php';
require_once __DIR__ . '/../../../includes/ApiHandler.php';

use Leadbusiness\ApiHandler;

// API Handler initialisieren
$api = new ApiHandler();

// Authentifizierung
if (!$api->authenticate()) {
    exit;
}

// Rate Limit prüfen
if (!$api->checkRateLimit()) {
    exit;
}

// Pfad ermitteln
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api/v1/';
$path = substr($requestUri, strpos($requestUri, $basePath) + strlen($basePath));
$path = strtok($path, '?'); // Query-String entfernen
$pathParts = explode('/', trim($path, '/'));

$endpoint = $pathParts[0] ?? '';
$resourceId = $pathParts[1] ?? null;
$subResource = $pathParts[2] ?? null;

// Routing
switch ($endpoint) {
    
    case 'referrers':
        require __DIR__ . '/referrers.php';
        break;
    
    case 'leads':
        // Alias für referrers
        require __DIR__ . '/referrers.php';
        break;
        
    case 'conversions':
        require __DIR__ . '/conversions.php';
        break;
        
    case 'stats':
    case 'statistics':
        require __DIR__ . '/stats.php';
        break;
        
    case 'rewards':
        require __DIR__ . '/rewards.php';
        break;
        
    case 'webhooks':
        // Nur für Enterprise
        if ($api->getCustomer()['plan'] !== 'enterprise') {
            $api->errorResponse(403, 'Webhooks API requires Enterprise plan', 'ENTERPRISE_REQUIRED');
        }
        require __DIR__ . '/webhooks.php';
        break;
        
    case 'export':
        // Nur für Enterprise
        if ($api->getCustomer()['plan'] !== 'enterprise') {
            $api->errorResponse(403, 'Export API requires Enterprise plan', 'ENTERPRISE_REQUIRED');
        }
        require __DIR__ . '/export.php';
        break;
        
    case '':
        // API Root - Info ausgeben
        $customer = $api->getCustomer();
        $isEnterprise = $customer['plan'] === 'enterprise';
        
        $endpoints = [
            'referrers' => [
                'GET /api/v1/referrers' => 'List all referrers (supports pagination, filtering)',
                'POST /api/v1/referrers' => 'Create a new referrer',
                'GET /api/v1/referrers/{id}' => 'Get a specific referrer by ID or code',
                'PUT /api/v1/referrers/{id}' => 'Update a referrer',
                'DELETE /api/v1/referrers/{id}' => 'Delete a referrer (soft delete)'
            ],
            'conversions' => [
                'GET /api/v1/conversions' => 'List all conversions',
                'POST /api/v1/conversions' => 'Track a new conversion',
                'GET /api/v1/conversions/{id}' => 'Get a specific conversion'
            ],
            'stats' => [
                'GET /api/v1/stats' => 'Get account statistics',
                'GET /api/v1/stats/daily' => 'Get daily statistics'
            ],
            'rewards' => [
                'GET /api/v1/rewards' => 'List all reward levels'
            ]
        ];
        
        if ($isEnterprise) {
            $endpoints['webhooks'] = [
                'GET /api/v1/webhooks' => 'List registered webhooks',
                'POST /api/v1/webhooks' => 'Register a webhook URL',
                'DELETE /api/v1/webhooks/{id}' => 'Remove a webhook'
            ];
            $endpoints['export'] = [
                'GET /api/v1/export/referrers' => 'Export referrers as CSV/JSON',
                'GET /api/v1/export/conversions' => 'Export conversions as CSV/JSON'
            ];
        }
        
        $api->successResponse([
            'name' => 'Leadbusiness REST API',
            'version' => 'v1',
            'plan' => $customer['plan'],
            'documentation' => 'https://empfehlungen.cloud/api/docs',
            'rate_limits' => [
                'requests_per_minute' => $customer['plan'] === 'enterprise' ? 300 : 60,
                'requests_per_day' => $customer['plan'] === 'enterprise' ? 50000 : 5000
            ],
            'endpoints' => $endpoints
        ]);
        break;
        
    default:
        $api->errorResponse(404, "Endpoint '$endpoint' not found", 'ENDPOINT_NOT_FOUND');
}
