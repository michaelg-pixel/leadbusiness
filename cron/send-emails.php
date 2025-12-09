<?php
/**
 * Leadbusiness - Cron: E-Mail Queue verarbeiten
 * 
 * Läuft alle 5 Minuten: */5 * * * *
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/MailgunService.php';

// Nur CLI
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

echo "[" . date('Y-m-d H:i:s') . "] Starting email queue processing...\n";

try {
    $mailgun = new MailgunService();
    $result = $mailgun->processQueue(50);
    
    echo "Sent: {$result['sent']}, Failed: {$result['failed']}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Cron send-emails error: " . $e->getMessage());
}

echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
