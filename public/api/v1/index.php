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
        
    case 'conversions':
        require __DIR__ . '/conversions.php';
        break;
        
    case 'stats':
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
        $api->successResponse([
            'name' => 'Leadbusiness API',
            'version' => 'v1',
            'documentation' => 'https://empfehlungen.cloud/api/docs',
            'endpoints' => [
                'GET /api/v1/referrers' => 'List all referrers',
                'POST /api/v1/referrers' => 'Create a referrer',
                'GET /api/v1/referrers/{id}' => 'Get a specific referrer',
                'PUT /api/v1/referrers/{id}' => 'Update a referrer',
                'DELETE /api/v1/referrers/{id}' => 'Delete a referrer',
                'GET /api/v1/conversions' => 'List all conversions',
                'POST /api/v1/conversions' => 'Track a conversion',
                'GET /api/v1/stats' => 'Get statistics',
                'GET /api/v1/rewards' => 'List reward levels'
            ]
        ]);
        break;
        
    default:
        $api->errorResponse(404, "Endpoint '$endpoint' not found", 'ENDPOINT_NOT_FOUND');
}
