<?php
/**
 * Lead API - Update Profile
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

// Daten validieren
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');

// Name: Max 255 Zeichen, keine Sonderzeichen außer Leerzeichen und Bindestriche
if (!empty($name) && !preg_match('/^[\p{L}\s\-\.]{1,255}$/u', $name)) {
    $_SESSION['settings_message'] = 'Ungültiger Name';
    $_SESSION['settings_message_type'] = 'error';
    header('Location: /lead/dashboard.php?tab=settings');
    exit;
}

// Phone: Nur Zahlen, Leerzeichen, +, -, ()
if (!empty($phone) && !preg_match('/^[\d\s\+\-\(\)]{5,30}$/', $phone)) {
    $_SESSION['settings_message'] = 'Ungültige Telefonnummer';
    $_SESSION['settings_message_type'] = 'error';
    header('Location: /lead/dashboard.php?tab=settings');
    exit;
}

// Update
$db->execute(
    "UPDATE leads SET name = ?, phone = ?, updated_at = NOW() WHERE id = ?",
    [$name ?: null, $phone ?: null, $lead['id']]
);

// Activity Log
try {
    $db->execute(
        "INSERT INTO lead_activity_log (lead_id, activity_type, details, ip_hash, user_agent)
         VALUES (?, 'profile_updated', ?, ?, ?)",
        [
            $lead['id'],
            json_encode(['name' => $name, 'phone' => $phone]),
            hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''),
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]
    );
} catch (Exception $e) {}

$_SESSION['settings_message'] = 'Profil erfolgreich aktualisiert';
$_SESSION['settings_message_type'] = 'success';

header('Location: /lead/dashboard.php?tab=settings');
exit;
