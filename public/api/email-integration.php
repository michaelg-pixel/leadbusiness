<?php
/**
 * Leadbusiness - E-Mail Integration API
 * 
 * Endpoints für das Onboarding und Dashboard:
 * - Verbindung testen
 * - Listen abrufen
 * - Tags abrufen
 * - Integration speichern
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';

// CORS für Onboarding-Requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Action kann via GET oder POST kommen
$input = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $_GET['action'] ?? $input['action'] ?? '';

// Tool-Name kann als 'tool' oder 'tool_name' gesendet werden
$toolName = $input['tool'] ?? $input['tool_name'] ?? $_GET['tool'] ?? '';

$response = ['success' => false, 'error' => 'Ungültige Aktion'];

try {
    // Prüfen ob Services existieren
    $servicesExist = file_exists(__DIR__ . '/../../includes/services/EmailIntegrationService.php') 
                  && file_exists(__DIR__ . '/../../includes/services/emailtools/EmailToolFactory.php');
    
    if ($servicesExist) {
        require_once __DIR__ . '/../../includes/services/EmailIntegrationService.php';
        require_once __DIR__ . '/../../includes/services/emailtools/EmailToolFactory.php';
        
        $factoryClass = 'Leadbusiness\\EmailTools\\EmailToolFactory';
        $serviceClass = 'Leadbusiness\\Services\\EmailIntegrationService';
    }
    
    switch ($action) {
        
        /**
         * Verfügbare Tools abrufen
         */
        case 'get_tools':
            if ($servicesExist && class_exists($factoryClass)) {
                $response = [
                    'success' => true,
                    'tools' => $factoryClass::getAvailableTools()
                ];
            } else {
                // Fallback: Statische Liste
                $response = [
                    'success' => true,
                    'tools' => [
                        ['name' => 'klicktipp', 'display_name' => 'KlickTipp', 'country' => 'DE'],
                        ['name' => 'quentn', 'display_name' => 'Quentn', 'country' => 'DE'],
                        ['name' => 'cleverreach', 'display_name' => 'CleverReach', 'country' => 'DE']
                    ]
                ];
            }
            break;
        
        /**
         * Verbindung testen
         */
        case 'test_connection':
            $credentials = [
                'api_key' => $input['api_key'] ?? '',
                'api_secret' => $input['api_secret'] ?? null,
                'api_url' => $input['api_url'] ?? null
            ];
            
            if (empty($toolName) || empty($credentials['api_key'])) {
                $response = ['success' => false, 'error' => 'Tool und API-Key sind erforderlich'];
                break;
            }
            
            if (!$servicesExist) {
                // Simuliere erfolgreiche Verbindung für Demo
                $response = ['success' => true, 'message' => 'Verbindung erfolgreich (Demo-Modus)'];
                break;
            }
            
            if (!$factoryClass::isImplemented($toolName)) {
                $response = ['success' => false, 'error' => 'Dieses Tool wird noch nicht unterstützt'];
                break;
            }
            
            $tool = $factoryClass::create($toolName, $credentials);
            
            if (!$tool) {
                $response = ['success' => false, 'error' => 'Konnte Tool nicht initialisieren'];
                break;
            }
            
            $result = $tool->testConnection();
            
            if ($result['success']) {
                $response = ['success' => true, 'message' => 'Verbindung erfolgreich!'];
            } else {
                $response = ['success' => false, 'error' => $result['error'] ?? 'Verbindung fehlgeschlagen'];
            }
            break;
        
        /**
         * Tags abrufen
         */
        case 'get_tags':
            $credentials = [
                'api_key' => $input['api_key'] ?? '',
                'api_secret' => $input['api_secret'] ?? null,
                'api_url' => $input['api_url'] ?? null
            ];
            
            if (empty($toolName)) {
                $response = ['success' => false, 'error' => 'Tool nicht angegeben'];
                break;
            }
            
            if (!$servicesExist) {
                // Demo-Tags
                $response = [
                    'success' => true,
                    'tags' => [
                        ['id' => '1', 'name' => 'Empfehler'],
                        ['id' => '2', 'name' => 'Newsletter'],
                        ['id' => '3', 'name' => 'Kunde']
                    ]
                ];
                break;
            }
            
            if (!$factoryClass::isImplemented($toolName)) {
                $response = ['success' => false, 'error' => 'Dieses Tool wird noch nicht unterstützt'];
                break;
            }
            
            $tool = $factoryClass::create($toolName, $credentials);
            
            if (!$tool) {
                $response = ['success' => false, 'error' => 'Konnte Tool nicht initialisieren'];
                break;
            }
            
            $result = $tool->getTags();
            
            if ($result['success']) {
                $response = ['success' => true, 'tags' => $result['tags'] ?? []];
            } else {
                $response = ['success' => false, 'error' => $result['error'] ?? 'Tags konnten nicht geladen werden'];
            }
            break;
        
        /**
         * Listen/Gruppen abrufen
         */
        case 'get_lists':
            $credentials = [
                'api_key' => $input['api_key'] ?? '',
                'api_secret' => $input['api_secret'] ?? null,
                'api_url' => $input['api_url'] ?? null
            ];
            
            if (empty($toolName)) {
                $response = ['success' => false, 'error' => 'Tool nicht angegeben'];
                break;
            }
            
            if (!$servicesExist || !$factoryClass::isImplemented($toolName)) {
                $response = ['success' => true, 'lists' => []];
                break;
            }
            
            $tool = $factoryClass::create($toolName, $credentials);
            
            if (!$tool) {
                $response = ['success' => false, 'error' => 'Konnte Tool nicht initialisieren'];
                break;
            }
            
            $result = $tool->getLists();
            
            if ($result['success']) {
                $response = ['success' => true, 'lists' => $result['lists'] ?? []];
            } else {
                $response = ['success' => false, 'error' => $result['error'] ?? 'Listen konnten nicht geladen werden'];
            }
            break;
        
        /**
         * Integration speichern
         */
        case 'save_integration':
            // Session für Customer-ID
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $customerId = $input['customer_id'] ?? $_SESSION['customer_id'] ?? $_SESSION['onboarding_customer_id'] ?? null;
            
            if (!$customerId) {
                $response = ['success' => false, 'error' => 'Nicht authentifiziert'];
                break;
            }
            
            if (!$servicesExist) {
                $response = ['success' => false, 'error' => 'Service nicht verfügbar'];
                break;
            }
            
            $service = new $serviceClass();
            
            $data = [
                'tool_name' => $toolName,
                'api_key' => $input['api_key'] ?? '',
                'api_secret' => $input['api_secret'] ?? null,
                'api_url' => $input['api_url'] ?? null,
                'list_id' => $input['list_id'] ?? null,
                'list_name' => $input['list_name'] ?? null,
                'tag_id' => $input['tag_id'] ?? null,
                'tag_name' => $input['tag_name'] ?? null
            ];
            
            $result = $service->createIntegration($customerId, $data);
            
            if ($result['success']) {
                $response = ['success' => true, 'message' => 'Integration gespeichert'];
            } else {
                $response = ['success' => false, 'error' => $result['error'] ?? 'Fehler beim Speichern'];
            }
            break;
        
        /**
         * Integration deaktivieren
         */
        case 'deactivate':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $customerId = $_SESSION['customer_id'] ?? null;
            
            if (!$customerId || !$toolName) {
                $response = ['success' => false, 'error' => 'Ungültige Anfrage'];
                break;
            }
            
            if (!$servicesExist) {
                $response = ['success' => false, 'error' => 'Service nicht verfügbar'];
                break;
            }
            
            $service = new $serviceClass();
            $success = $service->deactivateIntegration($customerId, $toolName);
            
            $response = [
                'success' => $success,
                'message' => $success ? 'Integration deaktiviert' : 'Fehler beim Deaktivieren'
            ];
            break;
        
        /**
         * Integrationen eines Kunden abrufen
         */
        case 'get_integrations':
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            $customerId = $_SESSION['customer_id'] ?? null;
            
            if (!$customerId) {
                $response = ['success' => false, 'error' => 'Nicht authentifiziert'];
                break;
            }
            
            if (!$servicesExist) {
                $response = ['success' => true, 'integrations' => []];
                break;
            }
            
            $service = new $serviceClass();
            $integrations = $service->getIntegrations($customerId);
            
            $response = [
                'success' => true,
                'integrations' => $integrations
            ];
            break;
        
        default:
            $response = ['success' => false, 'error' => 'Unbekannte Aktion: ' . $action];
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => 'Fehler: ' . $e->getMessage()
    ];
}

echo json_encode($response);
