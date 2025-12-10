<?php
/**
 * Leadbusiness - Cron: Admin Broadcasts verarbeiten
 * 
 * Läuft alle 2 Minuten: */2 * * * *
 * 
 * Verarbeitet:
 * 1. Geplante Broadcasts starten
 * 2. Pending Recipients versenden
 * 3. Broadcast-Status aktualisieren
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/MailgunService.php';

// Nur CLI
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

$startTime = microtime(true);
echo "[" . date('Y-m-d H:i:s') . "] Starting broadcast processing...\n";

try {
    $db = Database::getInstance();
    $mailgun = new MailgunService();
    
    // =====================================================
    // 1. GEPLANTE BROADCASTS STARTEN
    // =====================================================
    $scheduledBroadcasts = $db->fetchAll("
        SELECT id, name FROM admin_broadcasts 
        WHERE status = 'scheduled' 
        AND scheduled_for <= NOW()
    ");
    
    foreach ($scheduledBroadcasts as $broadcast) {
        echo "  Starting scheduled broadcast: {$broadcast['name']} (ID: {$broadcast['id']})\n";
        
        $db->execute("
            UPDATE admin_broadcasts 
            SET status = 'sending', started_at = NOW() 
            WHERE id = ?
        ", [$broadcast['id']]);
    }
    
    // =====================================================
    // 2. PENDING RECIPIENTS VERSENDEN
    // =====================================================
    
    // Broadcasts die gerade senden
    $sendingBroadcasts = $db->fetchAll("
        SELECT * FROM admin_broadcasts 
        WHERE status = 'sending'
    ");
    
    $totalSent = 0;
    $totalFailed = 0;
    
    foreach ($sendingBroadcasts as $broadcast) {
        echo "  Processing broadcast: {$broadcast['name']} (ID: {$broadcast['id']})\n";
        
        // Batch von pending Recipients holen (max 50 pro Durchlauf)
        $recipients = $db->fetchAll("
            SELECT r.*, c.company_name, c.contact_name, c.subdomain, c.email as customer_email
            FROM admin_broadcast_recipients r
            LEFT JOIN customers c ON r.customer_id = c.id
            WHERE r.broadcast_id = ? AND r.status = 'pending'
            ORDER BY r.id
            LIMIT 50
        ", [$broadcast['id']]);
        
        if (empty($recipients)) {
            // Alle gesendet - Broadcast abschließen
            $db->execute("
                UPDATE admin_broadcasts 
                SET status = 'sent', completed_at = NOW() 
                WHERE id = ?
            ", [$broadcast['id']]);
            echo "    Broadcast completed!\n";
            continue;
        }
        
        foreach ($recipients as $recipient) {
            try {
                // Unsubscribe prüfen
                $isUnsubscribed = $db->fetchColumn("
                    SELECT COUNT(*) FROM admin_broadcast_unsubscribes 
                    WHERE email = ?
                ", [$recipient['email']]);
                
                if ($isUnsubscribed) {
                    $db->execute("
                        UPDATE admin_broadcast_recipients 
                        SET status = 'skipped', error_message = 'Unsubscribed' 
                        WHERE id = ?
                    ", [$recipient['id']]);
                    continue;
                }
                
                // Variablen ersetzen
                $variables = [
                    '{company_name}' => $recipient['company_name'] ?? '',
                    '{contact_name}' => $recipient['contact_name'] ?? '',
                    '{email}' => $recipient['email'],
                    '{subdomain}' => $recipient['subdomain'] ?? '',
                    '{unsubscribe_link}' => 'https://empfehlungen.cloud/admin/unsubscribe?email=' . urlencode($recipient['email']) . '&token=' . md5($recipient['email'] . 'leadbusiness_unsubscribe_salt'),
                ];
                
                $subject = str_replace(array_keys($variables), array_values($variables), $broadcast['subject']);
                $bodyHtml = str_replace(array_keys($variables), array_values($variables), $broadcast['body_html']);
                
                // Tracking-Pixel hinzufügen
                $trackingPixel = '<img src="https://empfehlungen.cloud/api/track/open?bid=' . $broadcast['id'] . '&rid=' . $recipient['id'] . '" width="1" height="1" style="display:none" />';
                $bodyHtml .= $trackingPixel;
                
                // Links für Click-Tracking wrappen
                $bodyHtml = preg_replace_callback(
                    '/<a\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/i',
                    function($matches) use ($broadcast, $recipient) {
                        $url = $matches[2];
                        if (strpos($url, 'unsubscribe') !== false || strpos($url, '#') === 0) {
                            return $matches[0]; // Unsubscribe-Links und Anker nicht tracken
                        }
                        $trackUrl = 'https://empfehlungen.cloud/api/track/click?bid=' . $broadcast['id'] . '&rid=' . $recipient['id'] . '&url=' . urlencode($url);
                        return '<a ' . $matches[1] . 'href="' . $trackUrl . '"' . $matches[3] . '>';
                    },
                    $bodyHtml
                );
                
                // E-Mail senden
                $result = $mailgun->send(
                    $recipient['email'],
                    $subject,
                    $bodyHtml,
                    [
                        'from_name' => $broadcast['from_name'],
                        'from_email' => $broadcast['from_email'],
                        'reply_to' => $broadcast['reply_to'] ?: null,
                        'tags' => ['admin_broadcast', 'broadcast_' . $broadcast['id']],
                        'variables' => [
                            'broadcast_id' => $broadcast['id'],
                            'recipient_id' => $recipient['id']
                        ]
                    ]
                );
                
                // Status aktualisieren
                $db->execute("
                    UPDATE admin_broadcast_recipients 
                    SET status = 'sent', sent_at = NOW(), mailgun_message_id = ?
                    WHERE id = ?
                ", [$result['id'] ?? null, $recipient['id']]);
                
                $totalSent++;
                
                // Rate limiting - 100ms Pause zwischen E-Mails
                usleep(100000);
                
            } catch (Exception $e) {
                $db->execute("
                    UPDATE admin_broadcast_recipients 
                    SET status = 'failed', failed_at = NOW(), error_message = ?
                    WHERE id = ?
                ", [$e->getMessage(), $recipient['id']]);
                
                $totalFailed++;
                echo "    Failed to send to {$recipient['email']}: {$e->getMessage()}\n";
            }
        }
        
        // Broadcast-Zähler aktualisieren
        $db->execute("
            UPDATE admin_broadcasts b
            SET 
                sent_count = (SELECT COUNT(*) FROM admin_broadcast_recipients WHERE broadcast_id = b.id AND status IN ('sent', 'delivered', 'opened', 'clicked')),
                delivered_count = (SELECT COUNT(*) FROM admin_broadcast_recipients WHERE broadcast_id = b.id AND status IN ('delivered', 'opened', 'clicked')),
                opened_count = (SELECT COUNT(*) FROM admin_broadcast_recipients WHERE broadcast_id = b.id AND status IN ('opened', 'clicked')),
                clicked_count = (SELECT COUNT(*) FROM admin_broadcast_recipients WHERE broadcast_id = b.id AND status = 'clicked'),
                bounced_count = (SELECT COUNT(*) FROM admin_broadcast_recipients WHERE broadcast_id = b.id AND status = 'bounced'),
                unsubscribed_count = (SELECT COUNT(*) FROM admin_broadcast_recipients WHERE broadcast_id = b.id AND status = 'unsubscribed')
            WHERE id = ?
        ", [$broadcast['id']]);
    }
    
    // =====================================================
    // 3. SEQUENZ-SCHRITTE VERARBEITEN
    // =====================================================
    
    $pendingSequenceSends = $db->fetchAll("
        SELECT s.*, st.subject, st.body_html, st.from_name, st.from_email,
               seq.name as sequence_name
        FROM admin_broadcast_sequence_sends s
        JOIN admin_broadcast_sequence_steps st ON s.step_id = st.id
        JOIN admin_broadcast_sequences seq ON s.sequence_id = seq.id
        WHERE s.status = 'pending' 
        AND s.scheduled_for <= NOW()
        AND seq.is_active = 1
        AND st.is_active = 1
        LIMIT 50
    ");
    
    $sequenceSent = 0;
    
    foreach ($pendingSequenceSends as $send) {
        try {
            // Customer-Daten laden
            $customer = $db->fetch("
                SELECT company_name, contact_name, subdomain, email 
                FROM customers WHERE id = ?
            ", [$send['customer_id']]);
            
            if (!$customer) {
                $db->execute("UPDATE admin_broadcast_sequence_sends SET status = 'skipped', error_message = 'Customer not found' WHERE id = ?", [$send['id']]);
                continue;
            }
            
            // Variablen ersetzen
            $variables = [
                '{company_name}' => $customer['company_name'] ?? '',
                '{contact_name}' => $customer['contact_name'] ?? '',
                '{email}' => $send['email'],
                '{subdomain}' => $customer['subdomain'] ?? '',
                '{unsubscribe_link}' => 'https://empfehlungen.cloud/admin/unsubscribe?email=' . urlencode($send['email']) . '&token=' . md5($send['email'] . 'leadbusiness_unsubscribe_salt'),
            ];
            
            $subject = str_replace(array_keys($variables), array_values($variables), $send['subject']);
            $bodyHtml = str_replace(array_keys($variables), array_values($variables), $send['body_html']);
            
            $result = $mailgun->send(
                $send['email'],
                $subject,
                $bodyHtml,
                [
                    'from_name' => $send['from_name'],
                    'from_email' => $send['from_email'],
                    'tags' => ['admin_sequence', 'sequence_' . $send['sequence_id']],
                ]
            );
            
            $db->execute("
                UPDATE admin_broadcast_sequence_sends 
                SET status = 'sent', sent_at = NOW(), mailgun_message_id = ?
                WHERE id = ?
            ", [$result['id'] ?? null, $send['id']]);
            
            $sequenceSent++;
            usleep(100000);
            
        } catch (Exception $e) {
            $db->execute("
                UPDATE admin_broadcast_sequence_sends 
                SET status = 'failed', error_message = ?
                WHERE id = ?
            ", [$e->getMessage(), $send['id']]);
        }
    }
    
    $duration = round(microtime(true) - $startTime, 2);
    echo "\nBroadcasts sent: {$totalSent}, Failed: {$totalFailed}, Sequences: {$sequenceSent}\n";
    echo "[" . date('Y-m-d H:i:s') . "] Done in {$duration}s.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    error_log("Cron process-broadcasts error: " . $e->getMessage());
}
