<?php
/**
 * Broadcast Open Tracking
 * 
 * Tracking-Pixel für E-Mail-Öffnungen
 * URL: /api/track/open?bid=BROADCAST_ID&rid=RECIPIENT_ID
 */

require_once __DIR__ . '/../../../includes/init.php';

// 1x1 transparentes GIF
$pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

header('Content-Type: image/gif');
header('Content-Length: ' . strlen($pixel));
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Parameter
$broadcastId = intval($_GET['bid'] ?? 0);
$recipientId = intval($_GET['rid'] ?? 0);

if ($broadcastId && $recipientId) {
    try {
        $db = db();
        
        // Recipient aktualisieren
        $recipient = $db->fetch("
            SELECT id, status, opened_at, opened_count 
            FROM admin_broadcast_recipients 
            WHERE id = ? AND broadcast_id = ?
        ", [$recipientId, $broadcastId]);
        
        if ($recipient) {
            $updates = [
                'opened_count' => ($recipient['opened_count'] ?? 0) + 1,
            ];
            
            // Nur beim ersten Öffnen den Status ändern
            if (!$recipient['opened_at']) {
                $updates['opened_at'] = date('Y-m-d H:i:s');
                
                // Status nur hochstufen, nicht runter (clicked > opened > delivered > sent)
                if (in_array($recipient['status'], ['sent', 'delivered'])) {
                    $updates['status'] = 'opened';
                }
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
                SET opened_count = (
                    SELECT COUNT(*) FROM admin_broadcast_recipients 
                    WHERE broadcast_id = ? AND opened_at IS NOT NULL
                )
                WHERE id = ?
            ", [$broadcastId, $broadcastId]);
        }
        
    } catch (Exception $e) {
        error_log("Broadcast open tracking error: " . $e->getMessage());
    }
}

// Pixel ausgeben
echo $pixel;
exit;
