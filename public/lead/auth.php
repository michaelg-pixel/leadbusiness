<?php
/**
 * Leadbusiness - One-Click Login
 * 
 * Ermöglicht Login mit permanentem Token aus E-Mail-Links.
 * URL: /lead/auth.php?token=ABC123...
 */

// Zuerst Domain prüfen BEVOR Dependencies laden
$host = $_SERVER['HTTP_HOST'] ?? '';
$host = strtolower(preg_replace('/^www\./', '', $host));

$mainDomains = ['empfehlungen.cloud', 'empfohlen.de', 'leadbusiness.de'];
if (in_array($host, $mainDomains)) {
    header('Location: /lead/login.php');
    exit;
}

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/services/LeadAuthService.php';
require_once __DIR__ . '/../../includes/DomainResolver.php';

use Leadbusiness\DomainResolver;

$db = Database::getInstance();
$auth = new LeadAuthService();

// Token aus URL
$token = $_GET['token'] ?? null;

if (!$token || strlen($token) < 32) {
    header('Location: /lead/login.php?error=invalid_token');
    exit;
}

// Kunde ermitteln
$customer = DomainResolver::init();

if (!$customer) {
    header('Location: /lead/login.php?error=no_customer');
    exit;
}

// Lead mit diesem Token finden
$lead = $db->fetch(
    "SELECT l.* FROM leads l
     JOIN campaigns c ON l.campaign_id = c.id
     WHERE l.login_token = ? 
     AND c.customer_id = ?
     AND l.status = 'active'",
    [$token, $customer['id']]
);

if (!$lead) {
    header('Location: /lead/login.php?error=invalid_token');
    exit;
}

// Token gueltig -> Session erstellen
$result = $auth->loginWithToken($lead['id']);

if ($result['success']) {
    // Activity Log
    try {
        $db->execute(
            "INSERT INTO lead_activity_log (lead_id, activity_type, details, ip_hash, user_agent)
             VALUES (?, 'login', ?, ?, ?)",
            [
                $lead['id'],
                json_encode(['method' => 'one_click_token']),
                hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''),
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]
        );
    } catch (Exception $e) {
        // Silent fail
    }
    
    header('Location: /lead/dashboard.php');
    exit;
}

header('Location: /lead/login.php?error=login_failed');
exit;
