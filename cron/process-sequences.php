<?php
/**
 * Leadbusiness - Cron: E-Mail-Sequenzen verarbeiten
 * 
 * Läuft alle 30 Minuten: */30 * * * *
 * 
 * Verarbeitet automatische E-Mail-Sequenzen wie:
 * - Reminder nach 3 Tagen ohne Share
 * - Inaktivitäts-E-Mails nach 30 Tagen
 * - "Fast geschafft" E-Mails
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/MailgunService.php';

// Nur CLI
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

echo "[" . date('Y-m-d H:i:s') . "] Processing email sequences...\n";

$db = Database::getInstance();
$mailgun = new MailgunService();
$processed = 0;

try {
    // ============================================
    // 1. Reminder: 3 Tage ohne Share
    // ============================================
    $noShareLeads = $db->fetchAll(
        "SELECT l.*, c.company_name, c.subdomain
         FROM leads l
         JOIN campaigns ca ON l.campaign_id = ca.id
         JOIN customers c ON ca.customer_id = c.id
         WHERE l.status = 'active'
         AND l.email_confirmed = 1
         AND l.last_share_at IS NULL
         AND l.created_at < DATE_SUB(NOW(), INTERVAL 3 DAY)
         AND l.created_at > DATE_SUB(NOW(), INTERVAL 4 DAY)
         AND NOT EXISTS (
             SELECT 1 FROM email_queue eq 
             WHERE eq.recipient_email = l.email 
             AND eq.template = 'reminder_no_share'
             AND eq.created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
         )"
    );
    
    foreach ($noShareLeads as $lead) {
        echo "  Sending no-share reminder to {$lead['email']}\n";
        
        $referralLink = "https://{$lead['subdomain']}.empfohlen.de/r/{$lead['referral_code']}";
        
        $mailgun->queue(
            $lead['customer_id'],
            $lead['email'],
            $lead['name'],
            'reminder_no_share',
            [
                'lead_name' => $lead['name'] ?: 'Empfehler',
                'company_name' => $lead['company_name'],
                'referral_link' => $referralLink,
                'dashboard_link' => "https://{$lead['subdomain']}.empfohlen.de/lead?code={$lead['referral_code']}"
            ],
            5
        );
        $processed++;
    }
    
    // ============================================
    // 2. "Fast geschafft" - 1 Conversion vor nächster Stufe
    // ============================================
    $nearRewardLeads = $db->fetchAll(
        "SELECT l.*, c.company_name, c.subdomain, r.description as next_reward, r.required_conversions
         FROM leads l
         JOIN campaigns ca ON l.campaign_id = ca.id
         JOIN customers c ON ca.customer_id = c.id
         JOIN rewards r ON r.campaign_id = ca.id 
            AND r.required_conversions = l.conversions + 1
            AND r.level > l.current_reward_level
         WHERE l.status = 'active'
         AND l.email_confirmed = 1
         AND l.conversions > 0
         AND NOT EXISTS (
             SELECT 1 FROM email_queue eq 
             WHERE eq.recipient_email = l.email 
             AND eq.template = 'near_reward'
             AND eq.variables LIKE CONCAT('%\"required_conversions\":', r.required_conversions, '%')
         )
         ORDER BY r.level ASC"
    );
    
    foreach ($nearRewardLeads as $lead) {
        echo "  Sending near-reward to {$lead['email']} (1 until {$lead['required_conversions']})\n";
        
        $referralLink = "https://{$lead['subdomain']}.empfohlen.de/r/{$lead['referral_code']}";
        
        $mailgun->queue(
            $lead['customer_id'],
            $lead['email'],
            $lead['name'],
            'near_reward',
            [
                'lead_name' => $lead['name'] ?: 'Empfehler',
                'company_name' => $lead['company_name'],
                'current_conversions' => $lead['conversions'],
                'required_conversions' => $lead['required_conversions'],
                'next_reward' => $lead['next_reward'],
                'referral_link' => $referralLink
            ],
            8
        );
        $processed++;
    }
    
    // ============================================
    // 3. Inaktivität: 30 Tage keine Aktivität
    // ============================================
    $inactiveLeads = $db->fetchAll(
        "SELECT l.*, c.company_name, c.subdomain
         FROM leads l
         JOIN campaigns ca ON l.campaign_id = ca.id
         JOIN customers c ON ca.customer_id = c.id
         WHERE l.status = 'active'
         AND l.email_confirmed = 1
         AND (l.last_activity_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
              OR (l.last_activity_at IS NULL AND l.created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)))
         AND NOT EXISTS (
             SELECT 1 FROM email_queue eq 
             WHERE eq.recipient_email = l.email 
             AND eq.template = 'inactive_reminder'
             AND eq.created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)
         )
         LIMIT 100"
    );
    
    foreach ($inactiveLeads as $lead) {
        echo "  Sending inactive reminder to {$lead['email']}\n";
        
        $referralLink = "https://{$lead['subdomain']}.empfohlen.de/r/{$lead['referral_code']}";
        
        $mailgun->queue(
            $lead['customer_id'],
            $lead['email'],
            $lead['name'],
            'inactive_reminder',
            [
                'lead_name' => $lead['name'] ?: 'Empfehler',
                'company_name' => $lead['company_name'],
                'referral_link' => $referralLink,
                'conversions' => $lead['conversions'],
                'days_inactive' => 30
            ],
            3
        );
        $processed++;
    }
    
    echo "Sequence emails queued: {$processed}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Cron process-sequences error: " . $e->getMessage());
}

echo "[" . date('Y-m-d H:i:s') . "] Done.\n";
