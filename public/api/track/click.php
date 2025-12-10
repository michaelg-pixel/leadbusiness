<?php
/**
 * Broadcast Click Tracking
 * 
 * Redirect-basiertes Link-Tracking
 * URL: /api/track/click?bid=BROADCAST_ID&rid=RECIPIENT_ID&url=ENCODED_URL
 */

require_once __DIR__ . '/../../../includes/init.php';

// Parameter
$broadcastId = intval($_GET['bid'] ?? 0);
$recipientId = intval($_GET['rid'] ?? 0);
$url = $_GET['url'] ?? '';

// URL validieren
if (empty($url)) {
    header('HTTP/1.1 400 Bad Request');
    exit('Missing URL');
}

// URL dekodieren
$targetUrl = urldecode($url);

// Basis-Validierung der URL
if (!filter_var($targetUrl, FILTER_VALIDATE_URL)) {
    // Versuche relative URL zu absoluter zu machen
    if (strpos($targetUrl, '/') === 0) {
        $targetUrl = 'https://empfehlungen.cloud' . $targetUrl;
    } else {
        // Ungültige URL - trotzdem weiterleiten
        header('Location: ' . $targetUrl, true, 302);
        exit;
    }
}

// Tracking
if ($broadcastId && $recipientId) {
    try {
        $db = db();
        
        // Recipient aktualisieren
        $recipient = $db->fetch("
            SELECT id, status, clicked_at, clicked_count, clicked_links 
            FROM admin_broadcast_recipients 
            WHERE id = ? AND broadcast_id = ?
        ", [$recipientId, $broadcastId]);
        
        if ($recipient) {
            // Geklickte Links aktualisieren
            $clickedLinks = json_decode($recipient['clicked_links'] ?? '{}', true) ?: [];
            if (!isset($clickedLinks[$targetUrl])) {
                $clickedLinks[$targetUrl] = 0;
            }
            $clickedLinks[$targetUrl]++;
            
            $updates = [
                'clicked_count' => ($recipient['clicked_count'] ?? 0) + 1,
                'clicked_links' => json_encode($clickedLinks),
            ];
            
            // Nur beim ersten Klick den Status ändern
            if (!$recipient['clicked_at']) {
                $updates['clicked_at'] = date('Y-m-d H:i:s');
                
                // Status auf "clicked" setzen (höchster Status)
                if (in_array($recipient['status'], ['sent', 'delivered', 'opened'])) {
                    $updates['status'] = 'clicked';
                }
                
                // Falls noch nicht geöffnet, auch opened_at setzen
                $db->execute("
                    UPDATE admin_broadcast_recipients 
                    SET opened_at = COALESCE(opened_at, NOW())
                    WHERE id = ? AND opened_at IS NULL
                ", [$recipientId]);
            }
            
            $setClauses = [];
            $params = [];
            foreach ($updates as $key => $value) {
                $setClauses[] = "$key = ?";
                $params[] = $value;
            }
            $params[] = $recipientId;
            
            $db->execute(
                "UPDATE admin_broadcast_recipients SET " . implode(', ', $setClauses) . " WHERE id = ?",
                $params
            );
            
            // Broadcast-Statistik aktualisieren
            $db->execute("
                UPDATE admin_broadcasts 
                SET 
                    opened_count = (SELECT COUNT(*) FROM admin_broadcast_recipients WHERE broadcast_id = ? AND opened_at IS NOT NULL),
                    clicked_count = (SELECT COUNT(*) FROM admin_broadcast_recipients WHERE broadcast_id = ? AND clicked_at IS NOT NULL)
                WHERE id = ?
            ", [$broadcastId, $broadcastId, $broadcastId]);
        }
        
    } catch (Exception $e) {
        error_log("Broadcast click tracking error: " . $e->getMessage());
    }
}

// Weiterleitung zur Ziel-URL
header('Location: ' . $targetUrl, true, 302);
exit;
