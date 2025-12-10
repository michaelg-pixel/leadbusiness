<?php
/**
 * Leadbusiness - Cron: Admin E-Mail-Sequenzen verarbeiten
 * 
 * Läuft alle 15 Minuten: */15 * * * *
 * 
 * Aufgaben:
 * 1. Trigger-Erkennung: Empfänger zu Sequenzen hinzufügen basierend auf Broadcast-Events
 * 2. Fällige Sequenz-E-Mails versenden
 * 3. Nächsten Schritt nach erfolgreichem Versand queuen
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/MailgunService.php';

use Leadbusiness\Database;

// Nur CLI erlauben
if (php_sapi_name() !== 'cli') {
    die('CLI only');
}

$startTime = microtime(true);
echo "[" . date('Y-m-d H:i:s') . "] 🚀 Processing admin email sequences...\n";

$db = Database::getInstance();
$mailgun = new MailgunService();

$stats = [
    'triggered' => 0,
    'sent' => 0,
    'skipped' => 0,
    'failed' => 0,
    'queued_next' => 0,
];

try {
    // =========================================================
    // SCHRITT 1: TRIGGER-ERKENNUNG
    // Füge Empfänger zu Sequenzen hinzu basierend auf Broadcast-Events
    // =========================================================
    echo "\n📌 SCHRITT 1: Trigger-Erkennung\n";
    
    // Alle aktiven Sequenzen mit ihren Triggern laden
    $sequences = $db->fetchAll("
        SELECT s.*, 
               COALESCE(s.delay_hours, 24) as delay_hours,
               b.id as broadcast_id,
               b.status as broadcast_status,
               b.sent_at as broadcast_sent_at
        FROM admin_broadcast_sequences s
        LEFT JOIN admin_broadcasts b ON s.trigger_broadcast_id = b.id
        WHERE s.is_active = 1
        AND s.trigger_broadcast_id IS NOT NULL
    ");
    
    foreach ($sequences as $seq) {
        echo "  📧 Sequenz: {$seq['name']} (Trigger: {$seq['trigger_type']})\n";
        
        // Prüfen ob Broadcast bereits gesendet wurde
        if (!in_array($seq['broadcast_status'], ['sent', 'completed'])) {
            echo "    ⏸ Broadcast noch nicht gesendet\n";
            continue;
        }
        
        // Zeitfenster für Trigger berechnen
        $triggerAfter = date('Y-m-d H:i:s', strtotime($seq['broadcast_sent_at'] . " + {$seq['delay_hours']} hours"));
        
        // Ist das Zeitfenster erreicht?
        if (strtotime($triggerAfter) > time()) {
            $waitHours = round((strtotime($triggerAfter) - time()) / 3600, 1);
            echo "    ⏰ Wartet noch {$waitHours}h (bis {$triggerAfter})\n";
            continue;
        }
        
        // Empfänger basierend auf Trigger-Type finden
        $recipients = getTriggeredRecipients($db, $seq);
        
        if (empty($recipients)) {
            echo "    ✓ Keine neuen Empfänger\n";
            continue;
        }
        
        echo "    → " . count($recipients) . " neue Empfänger gefunden\n";
        
        // Ersten aktiven Schritt der Sequenz holen
        $firstStep = $db->fetch("
            SELECT id, delay_hours 
            FROM admin_broadcast_sequence_steps 
            WHERE sequence_id = ? AND is_active = 1 
            ORDER BY step_order ASC 
            LIMIT 1
        ", [$seq['id']]);
        
        if (!$firstStep) {
            echo "    ⚠ Keine aktiven Schritte in der Sequenz\n";
            continue;
        }
        
        // Empfänger zur Sequenz hinzufügen
        $added = addRecipientsToSequence($db, $seq['id'], $firstStep['id'], $recipients, $firstStep['delay_hours']);
        $stats['triggered'] += $added;
        echo "    ✅ {$added} Empfänger hinzugefügt\n";
    }
    
    // =========================================================
    // SCHRITT 2: FÄLLIGE E-MAILS VERSENDEN
    // =========================================================
    echo "\n📬 SCHRITT 2: Fällige E-Mails versenden\n";
    
    // Alle fälligen E-Mails laden
    $pendingSends = $db->fetchAll("
        SELECT 
            ss.*,
            seq.name as sequence_name,
            st.name as step_name,
            st.subject,
            st.body_html,
            st.from_name,
            st.from_email,
            st.step_order,
            st.condition_type,
            c.company_name,
            c.contact_name
        FROM admin_broadcast_sequence_sends ss
        JOIN admin_broadcast_sequences seq ON ss.sequence_id = seq.id
        JOIN admin_broadcast_sequence_steps st ON ss.step_id = st.id
        JOIN customers c ON ss.customer_id = c.id
        WHERE ss.status = 'pending'
        AND ss.scheduled_for <= NOW()
        AND seq.is_active = 1
        AND st.is_active = 1
        ORDER BY ss.scheduled_for ASC
        LIMIT 100
    ");
    
    echo "  → " . count($pendingSends) . " E-Mails fällig\n";
    
    foreach ($pendingSends as $send) {
        echo "  📤 [{$send['sequence_name']}] Schritt {$send['step_order']}: {$send['email']}\n";
        
        // Bedingung prüfen
        if (!checkStepCondition($db, $send)) {
            // Bedingung nicht erfüllt → überspringen aber nächsten Schritt queuen
            $db->execute("
                UPDATE admin_broadcast_sequence_sends 
                SET status = 'skipped', sent_at = NOW()
                WHERE id = ?
            ", [$send['id']]);
            
            $stats['skipped']++;
            echo "    ⏭ Übersprungen (Bedingung: {$send['condition_type']})\n";
            
            // Trotzdem nächsten Schritt queuen
            queueNextStep($db, $send);
            continue;
        }
        
        // E-Mail personalisieren
        $html = personalizeContent($send['body_html'], [
            'company_name' => $send['company_name'],
            'contact_name' => $send['contact_name'],
            'email' => $send['email'],
            'unsubscribe_link' => "https://empfehlungen.cloud/unsubscribe?email=" . urlencode($send['email']),
        ]);
        
        $subject = personalizeContent($send['subject'], [
            'company_name' => $send['company_name'],
            'contact_name' => $send['contact_name'],
        ]);
        
        // E-Mail senden mit korrekter Signatur: send($to, $subject, $html, $options)
        try {
            $result = $mailgun->send(
                $send['email'],
                $subject,
                $html,
                [
                    'from_name' => $send['from_name'],
                    'from_email' => $send['from_email'],
                    'variables' => [
                        'sequence_id' => $send['sequence_id'],
                        'step_id' => $send['step_id'],
                        'send_id' => $send['id'],
                    ],
                    'tags' => ['sequence', 'sequence_' . $send['sequence_id']],
                ]
            );
            
            // Status aktualisieren
            $db->execute("
                UPDATE admin_broadcast_sequence_sends 
                SET status = 'sent', 
                    sent_at = NOW(), 
                    mailgun_message_id = ?
                WHERE id = ?
            ", [$result['id'] ?? null, $send['id']]);
            
            $stats['sent']++;
            echo "    ✅ Gesendet\n";
            
            // Nächsten Schritt queuen
            $nextQueued = queueNextStep($db, $send);
            if ($nextQueued) {
                $stats['queued_next']++;
            }
            
            // Rate Limiting
            usleep(100000); // 100ms Pause
            
        } catch (Exception $e) {
            $db->execute("
                UPDATE admin_broadcast_sequence_sends 
                SET status = 'failed', 
                    error_message = ?,
                    sent_at = NOW()
                WHERE id = ?
            ", [$e->getMessage(), $send['id']]);
            
            $stats['failed']++;
            echo "    ❌ Fehler: {$e->getMessage()}\n";
        }
    }
    
    // =========================================================
    // SCHRITT 3: ZUSAMMENFASSUNG
    // =========================================================
    $duration = round(microtime(true) - $startTime, 2);
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "📊 ZUSAMMENFASSUNG\n";
    echo str_repeat("=", 50) . "\n";
    echo "  Neue Trigger:     {$stats['triggered']}\n";
    echo "  E-Mails gesendet: {$stats['sent']}\n";
    echo "  Übersprungen:     {$stats['skipped']}\n";
    echo "  Fehlgeschlagen:   {$stats['failed']}\n";
    echo "  Nächste Steps:    {$stats['queued_next']}\n";
    echo str_repeat("=", 50) . "\n";
    echo "[" . date('Y-m-d H:i:s') . "] ✅ Fertig in {$duration}s\n";
    
} catch (Exception $e) {
    echo "\n❌ FEHLER: " . $e->getMessage() . "\n";
    echo "Stack Trace:\n" . $e->getTraceAsString() . "\n";
    error_log("Cron process-admin-sequences error: " . $e->getMessage());
    exit(1);
}

// =========================================================
// HILFSFUNKTIONEN
// =========================================================

/**
 * Findet Empfänger basierend auf dem Trigger-Type
 */
function getTriggeredRecipients(Database $db, array $seq): array {
    $broadcastId = $seq['broadcast_id'];
    $triggerType = $seq['trigger_type'];
    $sequenceId = $seq['id'];
    
    $baseQuery = "
        SELECT DISTINCT br.customer_id, br.email
        FROM admin_broadcast_recipients br
        WHERE br.broadcast_id = ?
        AND br.status NOT IN ('bounced', 'failed', 'unsubscribed')
        AND NOT EXISTS (
            SELECT 1 FROM admin_broadcast_sequence_sends ss 
            WHERE ss.sequence_id = ? 
            AND ss.customer_id = br.customer_id
        )
    ";
    
    switch ($triggerType) {
        case 'after_broadcast':
            // Alle Empfänger des Broadcasts
            return $db->fetchAll($baseQuery . " AND br.status IN ('sent', 'delivered', 'opened', 'clicked')", 
                [$broadcastId, $sequenceId]);
            
        case 'not_opened':
            // Empfänger die NICHT geöffnet haben
            return $db->fetchAll($baseQuery . " AND br.opened_at IS NULL AND br.status IN ('sent', 'delivered')", 
                [$broadcastId, $sequenceId]);
            
        case 'not_clicked':
            // Empfänger die geöffnet aber NICHT geklickt haben
            return $db->fetchAll($baseQuery . " AND br.opened_at IS NOT NULL AND br.clicked_at IS NULL", 
                [$broadcastId, $sequenceId]);
            
        case 'opened':
            // Empfänger die geöffnet haben
            return $db->fetchAll($baseQuery . " AND br.opened_at IS NOT NULL", 
                [$broadcastId, $sequenceId]);
            
        case 'clicked':
            // Empfänger die geklickt haben
            return $db->fetchAll($baseQuery . " AND br.clicked_at IS NOT NULL", 
                [$broadcastId, $sequenceId]);
            
        default:
            return [];
    }
}

/**
 * Fügt Empfänger zur Sequenz hinzu
 */
function addRecipientsToSequence(Database $db, int $sequenceId, int $stepId, array $recipients, int $delayHours): int {
    $added = 0;
    $scheduledFor = date('Y-m-d H:i:s', strtotime("+{$delayHours} hours"));
    
    foreach ($recipients as $r) {
        try {
            $db->execute("
                INSERT IGNORE INTO admin_broadcast_sequence_sends 
                (sequence_id, step_id, customer_id, email, status, scheduled_for, created_at)
                VALUES (?, ?, ?, ?, 'pending', ?, NOW())
            ", [$sequenceId, $stepId, $r['customer_id'], $r['email'], $scheduledFor]);
            
            if ($db->rowCount() > 0) {
                $added++;
            }
        } catch (Exception $e) {
            // Duplikat ignorieren
        }
    }
    
    return $added;
}

/**
 * Prüft ob die Bedingung für einen Schritt erfüllt ist
 */
function checkStepCondition(Database $db, array $send): bool {
    $conditionType = $send['condition_type'];
    
    // 'all' = immer senden
    if ($conditionType === 'all') {
        return true;
    }
    
    // Vorherigen Schritt finden
    $prevStep = $db->fetch("
        SELECT ss.*, st.step_order
        FROM admin_broadcast_sequence_sends ss
        JOIN admin_broadcast_sequence_steps st ON ss.step_id = st.id
        WHERE ss.sequence_id = ?
        AND ss.customer_id = ?
        AND st.step_order < ?
        ORDER BY st.step_order DESC
        LIMIT 1
    ", [$send['sequence_id'], $send['customer_id'], $send['step_order']]);
    
    // Wenn kein vorheriger Schritt existiert, Bedingung erfüllt
    if (!$prevStep) {
        return true;
    }
    
    switch ($conditionType) {
        case 'not_opened':
            return $prevStep['opened_at'] === null;
            
        case 'not_clicked':
            return $prevStep['clicked_at'] === null;
            
        case 'opened':
            return $prevStep['opened_at'] !== null;
            
        case 'clicked':
            return $prevStep['clicked_at'] !== null;
            
        default:
            return true;
    }
}

/**
 * Fügt den nächsten Schritt zur Queue hinzu
 */
function queueNextStep(Database $db, array $currentSend): bool {
    // Nächsten aktiven Schritt finden
    $nextStep = $db->fetch("
        SELECT id, delay_hours 
        FROM admin_broadcast_sequence_steps 
        WHERE sequence_id = ? 
        AND step_order > (SELECT step_order FROM admin_broadcast_sequence_steps WHERE id = ?)
        AND is_active = 1
        ORDER BY step_order ASC 
        LIMIT 1
    ", [$currentSend['sequence_id'], $currentSend['step_id']]);
    
    if (!$nextStep) {
        return false; // Keine weiteren Schritte
    }
    
    $scheduledFor = date('Y-m-d H:i:s', strtotime("+{$nextStep['delay_hours']} hours"));
    
    try {
        $db->execute("
            INSERT IGNORE INTO admin_broadcast_sequence_sends 
            (sequence_id, step_id, customer_id, email, status, scheduled_for, created_at)
            VALUES (?, ?, ?, ?, 'pending', ?, NOW())
        ", [
            $currentSend['sequence_id'],
            $nextStep['id'],
            $currentSend['customer_id'],
            $currentSend['email'],
            $scheduledFor
        ]);
        
        return $db->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Personalisiert den E-Mail-Inhalt mit Platzhaltern
 */
function personalizeContent(string $content, array $variables): string {
    foreach ($variables as $key => $value) {
        $content = str_replace('{' . $key . '}', $value ?? '', $content);
    }
    return $content;
}
