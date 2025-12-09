<?php
/**
 * Leadbusiness - Subdomain Availability Check API
 * 
 * Prüft ob eine Subdomain verfügbar ist
 */

// CORS Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

// Konfiguration laden
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';

// Nur GET-Requests
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Subdomain aus URL
$subdomain = $_GET['subdomain'] ?? '';

// Validierung
if (empty($subdomain)) {
    echo json_encode([
        'available' => false,
        'error' => 'Subdomain ist erforderlich'
    ]);
    exit;
}

// Bereinigen
$subdomain = strtolower(trim($subdomain));
$subdomain = preg_replace('/[^a-z0-9-]/', '', $subdomain);

// Länge prüfen
if (strlen($subdomain) < 3) {
    echo json_encode([
        'available' => false,
        'error' => 'Subdomain muss mindestens 3 Zeichen lang sein'
    ]);
    exit;
}

if (strlen($subdomain) > 50) {
    echo json_encode([
        'available' => false,
        'error' => 'Subdomain darf maximal 50 Zeichen lang sein'
    ]);
    exit;
}

// Reservierte Subdomains prüfen
global $settings;
$reserved = $settings['subdomain']['reserved'] ?? [];

if (in_array($subdomain, $reserved)) {
    echo json_encode([
        'available' => false,
        'error' => 'Diese Subdomain ist reserviert'
    ]);
    exit;
}

// In Datenbank prüfen
try {
    $db = Database::getInstance();
    
    $existing = $db->fetch(
        "SELECT id FROM customers WHERE subdomain = ?",
        [$subdomain]
    );
    
    if ($existing) {
        echo json_encode([
            'available' => false,
            'error' => 'Diese Subdomain ist bereits vergeben'
        ]);
        exit;
    }
    
    // Verfügbar!
    echo json_encode([
        'available' => true,
        'subdomain' => $subdomain,
        'full_url' => $subdomain . '.' . ($settings['subdomain']['base_domain'] ?? 'empfehlungen.cloud')
    ]);
    
} catch (Exception $e) {
    error_log('Subdomain Check Error: ' . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'available' => false,
        'error' => 'Datenbankfehler'
    ]);
}
