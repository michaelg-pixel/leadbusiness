<?php
/**
 * Leadbusiness - Subdomain Check API
 * 
 * GET /api/subdomain/check.php?subdomain=xxx
 * Prüft ob Subdomain verfügbar ist
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';

header('Content-Type: application/json');

$subdomain = strtolower(trim($_GET['subdomain'] ?? ''));

if (empty($subdomain)) {
    jsonError('Subdomain erforderlich', 400);
}

// Format prüfen
if (!preg_match('/^[a-z0-9-]{3,50}$/', $subdomain)) {
    jsonError('Ungültiges Format. Nur Kleinbuchstaben, Zahlen und Bindestriche (3-50 Zeichen)', 400);
}

// Reservierte Subdomains
$reserved = [
    'www', 'admin', 'api', 'app', 'dashboard', 'mail', 'smtp', 'ftp', 
    'test', 'dev', 'staging', 'beta', 'alpha', 'demo', 'support', 
    'help', 'docs', 'blog', 'shop', 'store', 'cdn', 'static', 'assets',
    'login', 'register', 'signup', 'signin', 'auth', 'oauth', 'sso',
    'account', 'accounts', 'user', 'users', 'profile', 'profiles',
    'lead', 'leads', 'customer', 'customers', 'client', 'clients'
];

if (in_array($subdomain, $reserved)) {
    echo json_encode([
        'available' => false,
        'reason' => 'reserved',
        'message' => 'Diese Subdomain ist reserviert'
    ]);
    exit;
}

// Datenbank prüfen
$db = Database::getInstance();
$existing = $db->fetch(
    "SELECT id FROM customers WHERE subdomain = ?",
    [$subdomain]
);

if ($existing) {
    echo json_encode([
        'available' => false,
        'reason' => 'taken',
        'message' => 'Diese Subdomain ist bereits vergeben'
    ]);
    exit;
}

// Verfügbar!
echo json_encode([
    'available' => true,
    'subdomain' => $subdomain,
    'preview' => "{$subdomain}.empfohlen.de"
]);
