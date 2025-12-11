<?php
/**
 * Lead API - Update Notification Settings
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

// Einstellungen aktualisieren
$settings = [
    'notification_new_conversion' => isset($_POST['notification_new_conversion']),
    'notification_reward_unlocked' => isset($_POST['notification_reward_unlocked']),
    'notification_weekly_summary' => isset($_POST['notification_weekly_summary']),
    'notification_tips' => isset($_POST['notification_tips'])
];

$result = $auth->updateNotificationSettings($lead['id'], $settings);

if ($result['success']) {
    $_SESSION['settings_message'] = 'Benachrichtigungs-Einstellungen gespeichert';
    $_SESSION['settings_message_type'] = 'success';
} else {
    $_SESSION['settings_message'] = $result['error'];
    $_SESSION['settings_message_type'] = 'error';
}

header('Location: /lead/dashboard.php?tab=settings');
exit;
