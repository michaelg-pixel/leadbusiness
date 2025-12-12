<?php
// Admin Setup Script - NACH VERWENDUNG LÖSCHEN!
// Aufruf: https://www.empfehlungen.cloud/temp-hash.php?action=update

require_once __DIR__ . '/../includes/init.php';

$action = $_GET['action'] ?? 'show';

if ($action === 'update') {
    $email = 'admin-leadsoftware@magnodesign.de';
    $password = 'Mgn#Lead2025$Secure';
    
    $db = db();
    
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $db->execute("UPDATE admin_users SET password_hash = ? WHERE email = ?", [$hash, $email]);
    
    echo "<!DOCTYPE html><html><head><title>Admin Update</title></head><body style='font-family:monospace;padding:40px;'>";
    echo "<h2>✅ Admin Passwort aktualisiert!</h2>";
    echo "<p><strong>E-Mail:</strong> " . htmlspecialchars($email) . "</p>";
    echo "<p><strong>Passwort:</strong> " . htmlspecialchars($password) . "</p>";
    echo "<p><strong>Login:</strong> <a href='https://www.empfehlungen.cloud/admin/login.php'>https://www.empfehlungen.cloud/admin/login.php</a></p>";
    echo "<hr><p style='color:red;'>⚠️ DIESES SCRIPT JETZT LÖSCHEN!</p>";
    echo "</body></html>";
} else {
    echo "Aufruf mit ?action=update";
}
