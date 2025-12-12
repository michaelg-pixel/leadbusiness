<?php
/**
 * Leadbusiness - Cron: Belohnungen prüfen und versenden
 * 
 * Läuft alle 15 Minuten: */15 * * * *
 * 
 * Prüft ob Empfehler neue Belohnungsstufen erreicht haben
 * und versendet automatisch die Belohnungs-E-Mails
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/MailgunService.php';

// Nur CLI
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

echo "[" . date('Y-m-d H:i:s') . "] Starting reward check...\n";

$db = Database::getInstance();
$mailgun = new MailgunService();

try {
    // Alle aktiven Leads mit bestätigten Conversions abrufen
    // WICHTIG: customer_id muss explizit selektiert werden für queue()
    $leads = $db->fetchAll(
        "SELECT l.*, c.id as customer_id, c.company_name, c.subdomain, ca.id as campaign_id
         FROM leads l
         JOIN campaigns ca ON l.campaign_id = ca.id
         JOIN customers c ON ca.customer_id = c.id
         WHERE l.status = 'active'
         AND l.conversions > 0
         AND l.email_confirmed = 1"
    );
    
    $rewardsProcessed = 0;
    
    foreach ($leads as $lead) {
        // Rewards für diese Kampagne laden
        $rewards = $db->fetchAll(
            "SELECT * FROM rewards 
             WHERE campaign_id = ? AND is_active = 1
             ORDER BY level ASC",
            [$lead['campaign_id']]
        );
        
        // Prüfen welche Rewards erreicht wurden
        foreach ($rewards as $reward) {
            // Wurde dieses Level bereits erreicht?
            if ($lead['current_reward_level'] >= $reward['level']) {
                continue; // Bereits freigeschaltet
            }
            
            // Genug Conversions?
            if ($lead['conversions'] >= $reward['required_conversions']) {
                // Prüfen ob bereits ausgeliefert
                $existingDelivery = $db->fetch(
                    "SELECT id FROM reward_deliveries 
                     WHERE lead_id = ? AND reward_id = ?",
                    [$lead['id'], $reward['id']]
                );
                
                if ($existingDelivery) {
                    // Schon verarbeitet, nur Level aktualisieren
                    $db->update('leads', [
                        'current_reward_level' => $reward['level']
                    ], 'id = ?', [$lead['id']]);
                    continue;
                }
                
                // Neue Belohnung!
                echo "  Lead #{$lead['id']} erreicht Stufe {$reward['level']}\n";
                
                // Delivery erstellen
                $deliveryId = $db->insert('reward_deliveries', [
                    'lead_id' => $lead['id'],
                    'reward_id' => $reward['id'],
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                // Lead-Level aktualisieren
                $db->update('leads', [
                    'current_reward_level' => $reward['level']
                ], 'id = ?', [$lead['id']]);
                
                // E-Mail Queue
                // WICHTIG: lead_id als letzten Parameter übergeben,
                // damit sendLeadEmail() verwendet wird (mit Impressum im Footer)
                $referralLink = "https://{$lead['subdomain']}.empfohlen.de/r/{$lead['referral_code']}";
                $dashboardLink = "https://{$lead['subdomain']}.empfohlen.de/lead?code={$lead['referral_code']}";
                
                $mailgun->queue(
                    $lead['customer_id'],
                    $lead['email'],
                    $lead['name'],
                    'reward_earned',
                    [
                        'lead_name' => $lead['name'] ?: 'Empfehler',
                        'company_name' => $lead['company_name'],
                        'reward_level' => $reward['level'],
                        'reward_description' => $reward['description'],
                        'reward_type' => $reward['reward_type'],
                        'reward_value' => $reward['reward_value'],
                        'referral_link' => $referralLink,
                        'dashboard_link' => $dashboardLink,
                        'conversions' => $lead['conversions']
                    ],
                    10, // Hohe Priorität
                    null, // scheduledAt
                    $lead['id'] // lead_id - wichtig für Impressum im Footer!
                );
                
                // Delivery als gesendet markieren
                $db->update('reward_deliveries', [
                    'status' => 'sent',
                    'sent_at' => date('Y-m-d H:i:s')
                ], 'id = ?', [$deliveryId]);
                
                // Gamification Log
                $db->insert('gamification_log', [
                    'lead_id' => $lead['id'],
                    'action' => 'reward_earned',
                    'points' => 50 * $reward['level'],
                    'details' => json_encode([
                        'reward_id' => $reward['id'],
                        'level' => $reward['level']
                    ]),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                
                // Punkte gutschreiben
                $db->query(
                    "UPDATE leads SET total_points = total_points + ? WHERE id = ?",
                    [50 * $reward['level'], $lead['id']]
                );
                
                $rewardsProcessed++;
            }
        }
    }
    
    echo "Rewards processed: {$rewardsProcessed}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Cron check-rewards error: " . $e->getMessage());
}

echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
