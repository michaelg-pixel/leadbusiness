<?php
/**
 * Dashboard: Zurück zum Admin (Impersonation beenden)
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

// Prüfen ob Admin-Impersonation aktiv ist
if (!isset($_SESSION['admin_impersonating']) || !$_SESSION['admin_impersonating']) {
    header('Location: /dashboard/');
    exit;
}

// Original Admin-Session wiederherstellen
$adminId = $_SESSION['original_admin_id'] ?? null;
$adminEmail = $_SESSION['original_admin_email'] ?? null;

// Kunden-Session löschen
unset($_SESSION['customer_id']);
unset($_SESSION['customer_email']);
unset($_SESSION['customer_company']);
unset($_SESSION['customer_plan']);
unset($_SESSION['customer_subdomain']);

// Admin-Session wiederherstellen
$_SESSION['admin_id'] = $adminId;
$_SESSION['admin_email'] = $adminEmail;

// Impersonation-Flag löschen
unset($_SESSION['admin_impersonating']);
unset($_SESSION['original_admin_id']);
unset($_SESSION['original_admin_email']);

// Zurück zum Admin-Dashboard
$_SESSION['flash_success'] = 'Zurück als Admin.';
header('Location: /admin/customers.php');
exit;
