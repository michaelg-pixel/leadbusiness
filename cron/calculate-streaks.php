<?php
/**
 * Leadbusiness - Cron: Wochen-Streaks berechnen
 * 
 * Läuft jeden Sonntag um 23:00: 0 23 * * 0
 * 
 * Prüft ob Empfehler in der vergangenen Woche aktiv waren
 * und aktualisiert ihre Streak-Werte
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/BadgeService.php';

// Nur CLI
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

echo "[" . date('Y-m-d H:i:s') . "] Calculating weekly streaks...\n";

$db = Database::getInstance();
$badgeService = new BadgeService();

try {
    // Alle aktiven Leads
    $leads = $db->fetchAll(
        "SELECT id, current_streak, longest_streak, last_share_at, email
         FROM leads 
         WHERE status = 'active' AND email_confirmed = 1"
    );
    
    $streaksContinued = 0;
    $streaksReset = 0;
    $badgesAwarded = 0;
    
    foreach ($leads as $lead) {
        $lastShare = $lead['last_share_at'] ? strtotime($lead['last_share_at']) : 0;
        $oneWeekAgo = strtotime('-7 days');
        
        if ($lastShare >= $oneWeekAgo) {
            // Aktiv in dieser Woche - Streak erhöhen
            $newStreak = $lead['current_streak'] + 1;
            $longestStreak = max($lead['longest_streak'], $newStreak);
            
            $db->update('leads', [
                'current_streak' => $newStreak,
                'longest_streak' => $longestStreak
            ], 'id = ?', [$lead['id']]);
            
            $streaksContinued++;
            
            // Streak-Badges prüfen
            // 3 Wochen Streak = "Durchstarter"
            if ($newStreak === 3) {
                $badge = $db->fetch("SELECT id FROM badges WHERE badge_key = 'streak_3_weeks'");
                if ($badge) {
                    $existing = $db->fetch(
                        "SELECT id FROM lead_badges WHERE lead_id = ? AND badge_id = ?",
                        [$lead['id'], $badge['id']]
                    );
                    if (!$existing) {
                        $db->insert('lead_badges', [
                            'lead_id' => $lead['id'],
                            'badge_id' => $badge['id'],
                            'earned_at' => date('Y-m-d H:i:s')
                        ]);
                        $badgeService->queueBadgeEmail($lead['id'], $badge['id']);
                        $badgesAwarded++;
                        echo "  Badge 'Durchstarter' für Lead #{$lead['id']}\n";
                    }
                }
            }
            
            // 8 Wochen Streak = "Unaufhaltsam"
            if ($newStreak === 8) {
                $badge = $db->fetch("SELECT id FROM badges WHERE badge_key = 'streak_8_weeks'");
                if ($badge) {
                    $existing = $db->fetch(
                        "SELECT id FROM lead_badges WHERE lead_id = ? AND badge_id = ?",
                        [$lead['id'], $badge['id']]
                    );
                    if (!$existing) {
                        $db->insert('lead_badges', [
                            'lead_id' => $lead['id'],
                            'badge_id' => $badge['id'],
                            'earned_at' => date('Y-m-d H:i:s')
                        ]);
                        $badgeService->queueBadgeEmail($lead['id'], $badge['id']);
                        $badgesAwarded++;
                        echo "  Badge 'Unaufhaltsam' für Lead #{$lead['id']}\n";
                    }
                }
            }
            
        } else {
            // Nicht aktiv - Streak zurücksetzen
            if ($lead['current_streak'] > 0) {
                $db->update('leads', [
                    'current_streak' => 0
                ], 'id = ?', [$lead['id']]);
                $streaksReset++;
            }
        }
    }
    
    echo "Streaks continued: {$streaksContinued}\n";
    echo "Streaks reset: {$streaksReset}\n";
    echo "Badges awarded: {$badgesAwarded}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Cron calculate-streaks error: " . $e->getMessage());
}

echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
