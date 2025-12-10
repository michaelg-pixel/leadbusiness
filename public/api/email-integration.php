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
require_once __DIR__ . '/../../includes/services/EmailIntegrationService.php';
require_once __DIR__ . '/../../includes/services/emailtools/EmailToolFactory.php';

use Leadbusiness\Services\EmailIntegrationService;
use Leadbusiness\EmailTools\EmailToolFactory;

// CORS für Onboarding-Requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Ungültige Aktion'];

try {
    $service = new EmailIntegrationService();
    
    switch ($action) {
        
        /**
         * Verfügbare Tools abrufen
         */
        case 'get_tools':
            $response = [
                'success' => true,
                'tools' => EmailToolFactory::getAvailableTools()
            ];
            break;
        
        /**
         * Setup-Hilfe für ein Tool
         */
        case 'get_setup_help':
            $toolName = $_GET['tool'] ?? '';
            if (empty($toolName)) {
                $response = ['success' => false, 'message' => 'Tool nicht angegeben'];
                break;
            }
            
            $response = [
                'success' => true,
                'help' => EmailToolFactory::getSetupHelp($toolName)
            ];
            break;
        
        /**
         * Verbindung testen
         */
        case 'test_connection':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $toolName = $input['tool_name'] ?? '';
            $credentials = [
                'api_key' => $input['api_key'] ?? '',
                'api_secret' => $input['api_secret'] ?? null,
                'api_url' => $input['api_url'] ?? null
            ];
            
            if (empty($toolName) || empty($credentials['api_key'])) {
                $response = ['success' => false, 'message' => 'Tool und API-Key sind erforderlich'];
                break;
            }
            
            if (!EmailToolFactory::isImplemented($toolName)) {
                $response = ['success' => false, 'message' => 'Dieses Tool wird noch nicht unterstützt'];
                break;
            }
            
            $tool = EmailToolFactory::create($toolName, $credentials);
            
            if (!$tool) {
                $response = ['success' => false, 'message' => 'Ungültige Zugangsdaten'];
                break;
            }
            
            $response = $tool->testConnection();
            break;
        
        /**
         * Listen/Gruppen abrufen
         */
        case 'get_lists':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $toolName = $input['tool_name'] ?? '';
            $credentials = [
                'api_key' => $input['api_key'] ?? '',
                'api_secret' => $input['api_secret'] ?? null,
                'api_url' => $input['api_url'] ?? null
            ];
            
            $lists = $service->getListsForTool($toolName, $credentials);
            
            $response = [
                'success' => true,
                'lists' => $lists
            ];
            break;
        
        /**
         * Tags abrufen
         */
        case 'get_tags':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $toolName = $input['tool_name'] ?? '';
            $credentials = [
                'api_key' => $input['api_key'] ?? '',
                'api_secret' => $input['api_secret'] ?? null,
                'api_url' => $input['api_url'] ?? null
            ];
            
            $tags = $service->getTagsForTool($toolName, $credentials);
            
            $response = [
                'success' => true,
                'tags' => $tags
            ];
            break;
        
        /**
         * Integration speichern (aus Onboarding oder Dashboard)
         */
        case 'save_integration':
            $input = json_decode(file_get_contents('php://input'), true);
            
            $customerId = $input['customer_id'] ?? null;
            
            // Für Onboarding: Customer-ID aus Session
            if (!$customerId && session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $customerId = $customerId ?? ($_SESSION['customer_id'] ?? null);
            
            if (!$customerId) {
                $response = ['success' => false, 'message' => 'Nicht authentifiziert'];
                break;
            }
            
            $data = [
                'tool_name' => $input['tool_name'] ?? '',
                'api_key' => $input['api_key'] ?? '',
                'api_secret' => $input['api_secret'] ?? null,
                'api_url' => $input['api_url'] ?? null,
                'list_id' => $input['list_id'] ?? null,
                'list_name' => $input['list_name'] ?? null,
                'tag_id' => $input['tag_id'] ?? null,
                'tag_name' => $input['tag_name'] ?? null
            ];
            
            $response = $service->createIntegration($customerId, $data);
            break;
        
        /**
         * Integration deaktivieren
         */
        case 'deactivate':
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $customerId = $_SESSION['customer_id'] ?? null;
            $toolName = $input['tool_name'] ?? '';
            
            if (!$customerId || !$toolName) {
                $response = ['success' => false, 'message' => 'Ungültige Anfrage'];
                break;
            }
            
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
                $response = ['success' => false, 'message' => 'Nicht authentifiziert'];
                break;
            }
            
            $integrations = $service->getIntegrations($customerId);
            $response = [
                'success' => true,
                'integrations' => $integrations
            ];
            break;
        
        default:
            $response = ['success' => false, 'message' => 'Unbekannte Aktion: ' . $action];
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Fehler: ' . $e->getMessage()
    ];
}

echo json_encode($response);
