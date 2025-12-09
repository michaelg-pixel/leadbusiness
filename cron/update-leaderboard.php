<?php
/**
 * Leadbusiness - Cron: Leaderboard aktualisieren
 * 
 * Läuft alle 15 Minuten: */15 * * * *
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/LeaderboardService.php';

// Nur CLI
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

echo "[" . date('Y-m-d H:i:s') . "] Updating leaderboards...\n";

try {
    $leaderboardService = new LeaderboardService();
    $updated = $leaderboardService->updateAllCaches();
    
    echo "Updated {$updated} leaderboards.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Cron update-leaderboard error: " . $e->getMessage());
}

echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
