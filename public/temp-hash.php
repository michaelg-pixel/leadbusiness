<?php
// Admin Setup Script - NACH VERWENDUNG LÖSCHEN!
// Aufruf: https://www.empfehlungen.cloud/temp-hash.php?action=create

require_once __DIR__ . '/../includes/init.php';

$action = $_GET['action'] ?? 'show';

if ($action === 'create') {
    $email = 'admin-leadsoftware@magnodesign.de';
    $password = 'Mgn#Lead2025$Secure';
    $name = 'Magnodesign Admin';
    
    $db = db();
    
    // Prüfen ob schon existiert
    $exists = $db->fetchColumn("SELECT id FROM admin_users WHERE email = ?", [$email]);
    
    if ($exists) {
        echo "Admin existiert bereits mit ID: " . $exists;
        exit;
    }
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $db->execute("
        INSERT INTO admin_users (email, password_hash, name, role, is_active, created_at)
        VALUES (?, ?, ?, 'admin', 1, NOW())
    ", [$email, $hash, $name]);
    
    $newId = $db->lastInsertId();
    
    echo "✅ Admin erfolgreich erstellt!\n\n";
    echo "ID: " . $newId . "\n";
    echo "E-Mail: " . $email . "\n";
    echo "Passwort: " . $password . "\n";
    echo "Name: " . $name . "\n";
    echo "Rolle: admin\n\n";
    echo "Login: https://www.empfehlungen.cloud/admin/login.php";
} else {
    echo "Aufruf mit ?action=create um Admin zu erstellen";
}
