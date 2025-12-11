<?php
/**
 * Lead Logout
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/services/LeadAuthService.php';

$auth = new LeadAuthService();
$auth->logout();

// Zurück zur Login-Seite
header('Location: /lead/login.php?logged_out=1');
exit;
