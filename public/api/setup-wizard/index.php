<?php
/**
 * Leadbusiness - Setup Wizard API
 * 
 * Endpoints:
 * POST /hide - Wizard ausblenden
 * POST /show - Wizard wieder einblenden
 * POST /review - Schritt als reviewed markieren
 * GET /status - Status abrufen
 */

require_once __DIR__ . '/../../../includes/init.php';

header('Content-Type: application/json');

// Auth prüfen
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    jsonError('Nicht autorisiert', 401);
}

$customer = $auth->getCurrentCustomer();
$setupWizard = new \Leadbusiness\SetupWizard($customer);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'hide':
            if ($method !== 'POST') jsonError('Method not allowed', 405);
            $setupWizard->hide();
            jsonSuccess(['hidden' => true], 'Wizard ausgeblendet');
            break;
            
        case 'show':
            if ($method !== 'POST') jsonError('Method not allowed', 405);
            $setupWizard->show();
            jsonSuccess(['hidden' => false], 'Wizard eingeblendet');
            break;
            
        case 'review':
            if ($method !== 'POST') jsonError('Method not allowed', 405);
            $step = input('step');
            if (empty($step)) jsonError('Step erforderlich', 400);
            $setupWizard->markAsReviewed($step);
            jsonSuccess(['reviewed' => $step], 'Schritt als erledigt markiert');
            break;
            
        case 'status':
        default:
            jsonSuccess([
                'progress' => $setupWizard->getProgress(),
                'stats' => $setupWizard->getStats(),
                'steps' => $setupWizard->getSteps(),
                'nextStep' => $setupWizard->getNextRequiredStep(),
                'isComplete' => $setupWizard->isSetupComplete(),
                'isHidden' => $setupWizard->isHidden()
            ]);
            break;
    }
} catch (Exception $e) {
    error_log('Setup Wizard API Error: ' . $e->getMessage());
    jsonError('Ein Fehler ist aufgetreten', 500);
}
