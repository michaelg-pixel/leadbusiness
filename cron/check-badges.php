<?php
/**
 * Leadbusiness - Cron: Badges prüfen
 * 
 * Läuft stündlich: 0 * * * *
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/BadgeService.php';
require_once __DIR__ . '/../includes/services/MailgunService.php';

// Nur CLI
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

echo "[" . date('Y-m-d H:i:s') . "] Checking badges...\n";

try {
    $badgeService = new BadgeService();
    $awarded = $badgeService->checkAllPendingBadges(100);
    
    echo "Awarded {$awarded} badges.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Cron check-badges error: " . $e->getMessage());
}

echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
