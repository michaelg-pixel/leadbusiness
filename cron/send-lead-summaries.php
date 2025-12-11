<?php
/**
 * Cron: Send Weekly Lead Summaries
 * 
 * Sendet wöchentliche Zusammenfassungen an Leads die das aktiviert haben
 * Sollte jeden Montag morgen laufen: 0 9 * * 1
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/services/LeadNotificationService.php';

$db = Database::getInstance();
$notificationService = new LeadNotificationService();

echo "[" . date('Y-m-d H:i:s') . "] Starting weekly lead summaries...\n";

// Alle Leads mit aktivierter wöchentlicher Zusammenfassung
$leads = $db->fetchAll(
    "SELECT l.id, l.email, l.name
     FROM leads l
     WHERE l.status = 'active'
       AND l.email_unsubscribed = 0
       AND l.notification_weekly_summary = 1
       AND l.conversions > 0
     ORDER BY l.id ASC"
);

$count = 0;
$errors = 0;

foreach ($leads as $lead) {
    try {
        $notificationService->sendWeeklySummary($lead['id']);
        $count++;
        
        // Rate limiting
        usleep(100000); // 100ms zwischen E-Mails
        
    } catch (Exception $e) {
        $errors++;
        echo "  Error for lead {$lead['id']}: " . $e->getMessage() . "\n";
    }
}

echo "[" . date('Y-m-d H:i:s') . "] Done. Sent: {$count}, Errors: {$errors}\n";
