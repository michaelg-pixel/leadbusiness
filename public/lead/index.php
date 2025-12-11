<?php
/**
 * Leadbusiness - Lead Entry Point
 * 
 * Leitet zum Dashboard oder Login weiter
 */

// WICHTIG: Zuerst prüfen ob wir auf der Hauptdomain sind
// BEVOR wir Database-Verbindungen etc. laden
$host = $_SERVER['HTTP_HOST'] ?? '';
$host = strtolower(preg_replace('/^www\./', '', $host));

// Hauptdomains ohne Subdomain -> Zur Login-Seite (die zeigt Info)
$mainDomains = ['empfehlungen.cloud', 'empfohlen.de', 'leadbusiness.de'];
if (in_array($host, $mainDomains)) {
    header('Location: /lead/login.php');
    exit;
}

// Jetzt erst Dependencies laden
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/services/LeadAuthService.php';

$auth = new LeadAuthService();

// Referral-Code aus URL?
$code = $_GET['code'] ?? null;

if ($code) {
    // Legacy-Support: Direkt per Code einloggen
    $db = Database::getInstance();
    $lead = $db->fetch(
        "SELECT * FROM leads WHERE referral_code = ? AND status = 'active'",
        [strtoupper($code)]
    );
    
    if ($lead) {
        // Session setzen für Legacy-Kompatibilität
        session_start();
        $_SESSION['lead_code'] = $lead['referral_code'];
        
        // Zum neuen Dashboard weiterleiten
        header('Location: /lead/dashboard.php');
        exit;
    }
}

// Schon eingeloggt?
$lead = $auth->check();

if ($lead) {
    header('Location: /lead/dashboard.php');
    exit;
}

// Nicht eingeloggt -> Login-Seite
header('Location: /lead/login.php');
exit;
