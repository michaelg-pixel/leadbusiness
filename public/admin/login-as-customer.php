<?php
/**
 * Admin: Als Kunde einloggen (Impersonation)
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$customerId = intval($_GET['id'] ?? 0);

if (!$customerId) {
    $_SESSION['flash_error'] = 'Keine Kunden-ID angegeben.';
    header('Location: /admin/customers.php');
    exit;
}

// Kunde laden
$customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);

if (!$customer) {
    $_SESSION['flash_error'] = 'Kunde nicht gefunden.';
    header('Location: /admin/customers.php');
    exit;
}

// Admin-Session speichern für späteren Rücksprung
$_SESSION['admin_impersonating'] = true;
$_SESSION['original_admin_id'] = $_SESSION['admin_id'];
$_SESSION['original_admin_email'] = $_SESSION['admin_email'] ?? null;

// Kunden-Session setzen
$_SESSION['customer_id'] = $customer['id'];
$_SESSION['customer_email'] = $customer['email'];
$_SESSION['customer_company'] = $customer['company_name'];
$_SESSION['customer_plan'] = $customer['plan'];
$_SESSION['customer_subdomain'] = $customer['subdomain'];

// Impersonation loggen
$db->execute(
    "INSERT INTO admin_activity_log (admin_id, action, target_type, target_id, details, ip_address, created_at) 
     VALUES (?, 'impersonate', 'customer', ?, ?, ?, NOW())",
    [
        $_SESSION['original_admin_id'],
        $customerId,
        json_encode(['company_name' => $customer['company_name']]),
        $_SERVER['REMOTE_ADDR'] ?? null
    ]
);

// Zur Kunden-Dashboard weiterleiten
header('Location: /dashboard/');
exit;
