<?php
/**
 * Lead API - Update Password
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/Database.php';
require_once __DIR__ . '/../../../includes/services/LeadAuthService.php';

session_start();

$auth = new LeadAuthService();
$lead = $auth->check();

if (!$lead) {
    header('Location: /lead/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /lead/dashboard.php?tab=settings');
    exit;
}

$db = Database::getInstance();

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

// Validierung
if (strlen($newPassword) < 8) {
    $_SESSION['settings_message'] = 'Das Passwort muss mindestens 8 Zeichen haben';
    $_SESSION['settings_message_type'] = 'error';
    header('Location: /lead/dashboard.php?tab=settings');
    exit;
}

if ($newPassword !== $confirmPassword) {
    $_SESSION['settings_message'] = 'Die Passwörter stimmen nicht überein';
    $_SESSION['settings_message_type'] = 'error';
    header('Location: /lead/dashboard.php?tab=settings');
    exit;
}

// Aktuelles Passwort prüfen (falls vorhanden)
if (!empty($lead['password_hash'])) {
    if (empty($currentPassword)) {
        $_SESSION['settings_message'] = 'Bitte geben Sie Ihr aktuelles Passwort ein';
        $_SESSION['settings_message_type'] = 'error';
        header('Location: /lead/dashboard.php?tab=settings');
        exit;
    }
    
    if (!password_verify($currentPassword, $lead['password_hash'])) {
        $_SESSION['settings_message'] = 'Das aktuelle Passwort ist falsch';
        $_SESSION['settings_message_type'] = 'error';
        header('Location: /lead/dashboard.php?tab=settings');
        exit;
    }
}

// Neues Passwort setzen
$result = $auth->setPassword($lead['id'], $newPassword);

if ($result['success']) {
    $_SESSION['settings_message'] = empty($lead['password_hash']) 
        ? 'Passwort erfolgreich festgelegt. Sie können sich jetzt auch mit Passwort anmelden.' 
        : 'Passwort erfolgreich geändert';
    $_SESSION['settings_message_type'] = 'success';
} else {
    $_SESSION['settings_message'] = $result['error'];
    $_SESSION['settings_message_type'] = 'error';
}

header('Location: /lead/dashboard.php?tab=settings');
exit;
