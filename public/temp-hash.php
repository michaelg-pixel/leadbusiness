<?php
// Temporäres Script - nach Verwendung löschen!
$password = 'Mgn#Lead2025$Secure';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: " . $password . "\n";
echo "Hash: " . $hash . "\n";
