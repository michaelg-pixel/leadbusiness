<?php
/**
 * Magic Link Verification
 * Verifiziert den Magic Link Token und loggt den Lead ein
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/services/LeadAuthService.php';

$auth = new LeadAuthService();

$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: /lead/login.php?error=missing_token');
    exit;
}

$result = $auth->verifyMagicLink($token);

if ($result['success']) {
    // Erfolgreich eingeloggt
    header('Location: /lead/dashboard.php?welcome=1');
    exit;
} else {
    // Token ungültig oder abgelaufen
    header('Location: /lead/login.php?error=' . urlencode($result['error']));
    exit;
}
