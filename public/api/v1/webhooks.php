<?php
/**
 * Leadbusiness REST API v1 - Webhooks Endpoint (Enterprise only)
 * 
 * GET /api/v1/webhooks - Liste aller Webhooks
 * POST /api/v1/webhooks - Neuen Webhook registrieren
 * DELETE /api/v1/webhooks/{id} - Webhook löschen
 * POST /api/v1/webhooks/{id}/test - Test-Event senden
 */

use Leadbusiness\Database;

$db = Database::getInstance();
$customerId = $api->getCustomerId();
$method = $_SERVER['REQUEST_METHOD'];

// Webhook-Tabelle erstellen falls nicht vorhanden
$db->query("CREATE TABLE IF NOT EXISTS webhooks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    url VARCHAR(500) NOT NULL,
    events JSON NOT NULL,
    secret VARCHAR(64) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    last_triggered_at DATETIME NULL,
    last_status_code INT NULL,
    failure_count INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_customer (customer_id),
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE
)");

switch ($method) {
    
    case 'GET':
        if ($resourceId) {
            // Einzelnen Webhook abrufen
            $webhook = $db->fetch(
                "SELECT id, url, events, is_active, last_triggered_at, last_status_code, failure_count, created_at
                 FROM webhooks 
                 WHERE id = ? AND customer_id = ?",
                [$resourceId, $customerId]
            );
            
            if (!$webhook) {
                $api->errorResponse(404, 'Webhook not found', 'NOT_FOUND');
            }
            
            $webhook['events'] = json_decode($webhook['events'], true);
            
            // Letzte Webhook-Logs
            $logs = $db->fetchAll(
                "SELECT event_type, status_code, response_time_ms, created_at
                 FROM webhook_logs 
                 WHERE webhook_id = ? 
                 ORDER BY created_at DESC LIMIT 10",
                [$resourceId]
            );
            
            $webhook['recent_deliveries'] = $logs;
            
            $api->successResponse($webhook);
            
        } else {
            // Alle Webhooks auflisten
            $webhooks = $db->fetchAll(
                "SELECT id, url, events, is_active, last_triggered_at, last_status_code, failure_count, created_at
                 FROM webhooks 
                 WHERE customer_id = ?
                 ORDER BY created_at DESC",
                [$customerId]
            );
            
            foreach ($webhooks as &$wh) {
                $wh['events'] = json_decode($wh['events'], true);
            }
            
            $api->successResponse([
                'webhooks' => $webhooks,
                'available_events' => [
                    'referrer.created' => 'Neuer Empfehler registriert',
                    'referrer.updated' => 'Empfehler aktualisiert',
                    'referrer.deleted' => 'Empfehler gelöscht',
                    'conversion.created' => 'Neue Conversion erfasst',
                    'conversion.confirmed' => 'Conversion bestätigt',
                    'conversion.cancelled' => 'Conversion storniert',
                    'reward.unlocked' => 'Belohnung freigeschaltet',
                    'reward.claimed' => 'Belohnung eingelöst'
                ]
            ]);
        }
        break;
        
    case 'POST':
        // Prüfen ob Sub-Resource (test)
        if ($resourceId && $subResource === 'test') {
            // Test-Event senden
            $webhook = $db->fetch(
                "SELECT * FROM webhooks WHERE id = ? AND customer_id = ?",
                [$resourceId, $customerId]
            );
            
            if (!$webhook) {
                $api->errorResponse(404, 'Webhook not found', 'NOT_FOUND');
            }
            
            // Test-Payload erstellen
            $testPayload = [
                'event' => 'test',
                'timestamp' => date('c'),
                'data' => [
                    'message' => 'This is a test webhook delivery',
                    'webhook_id' => (int)$webhook['id']
                ]
            ];
            
            // Signatur erstellen
            $signature = hash_hmac('sha256', json_encode($testPayload), $webhook['secret']);
            
            // Webhook aufrufen
            $ch = curl_init($webhook['url']);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($testPayload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'X-Webhook-Signature: ' . $signature,
                    'X-Webhook-Event: test',
                    'User-Agent: Leadbusiness-Webhook/1.0'
                ]
            ]);
            
            $startTime = microtime(true);
            $response = curl_exec($ch);
            $responseTime = round((microtime(true) - $startTime) * 1000);
            $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            $api->successResponse([
                'success' => $statusCode >= 200 && $statusCode < 300,
                'status_code' => $statusCode,
                'response_time_ms' => $responseTime,
                'error' => $error ?: null,
                'payload_sent' => $testPayload
            ]);
        }
        
        // Neuen Webhook erstellen
        $data = $api->getRequestBody();
        
        // URL validieren
        $url = filter_var($data['url'] ?? '', FILTER_VALIDATE_URL);
        if (!$url) {
            $api->errorResponse(422, 'Valid URL is required', 'VALIDATION_ERROR');
        }
        
        // HTTPS erzwingen
        if (strpos($url, 'https://') !== 0) {
            $api->errorResponse(422, 'Webhook URL must use HTTPS', 'HTTPS_REQUIRED');
        }
        
        // Events validieren
        $allowedEvents = [
            'referrer.created', 'referrer.updated', 'referrer.deleted',
            'conversion.created', 'conversion.confirmed', 'conversion.cancelled',
            'reward.unlocked', 'reward.claimed'
        ];
        
        $events = $data['events'] ?? ['conversion.created'];
        if (!is_array($events)) {
            $events = [$events];
        }
        
        $events = array_intersect($events, $allowedEvents);
        if (empty($events)) {
            $api->errorResponse(422, 'At least one valid event is required', 'NO_VALID_EVENTS');
        }
        
        // Webhook-Limit prüfen (max 5 pro Kunde)
        $count = $db->fetch(
            "SELECT COUNT(*) as count FROM webhooks WHERE customer_id = ?",
            [$customerId]
        );
        
        if ($count['count'] >= 5) {
            $api->errorResponse(400, 'Maximum of 5 webhooks allowed', 'WEBHOOK_LIMIT_REACHED');
        }
        
        // Secret generieren
        $secret = bin2hex(random_bytes(32));
        
        // Webhook erstellen
        $db->query(
            "INSERT INTO webhooks (customer_id, url, events, secret, created_at)
             VALUES (?, ?, ?, ?, NOW())",
            [$customerId, $url, json_encode($events), $secret]
        );
        
        $webhookId = $db->lastInsertId();
        
        $api->successResponse([
            'id' => (int)$webhookId,
            'url' => $url,
            'events' => $events,
            'secret' => $secret,
            'message' => 'Webhook created. Save the secret - it will not be shown again!'
        ], 201);
        break;
        
    case 'PUT':
        if (!$resourceId) {
            $api->errorResponse(400, 'Webhook ID required', 'MISSING_ID');
        }
        
        // Webhook finden
        $webhook = $db->fetch(
            "SELECT * FROM webhooks WHERE id = ? AND customer_id = ?",
            [$resourceId, $customerId]
        );
        
        if (!$webhook) {
            $api->errorResponse(404, 'Webhook not found', 'NOT_FOUND');
        }
        
        $data = $api->getRequestBody();
        $updates = [];
        $params = [];
        
        // URL aktualisieren
        if (isset($data['url'])) {
            $url = filter_var($data['url'], FILTER_VALIDATE_URL);
            if (!$url || strpos($url, 'https://') !== 0) {
                $api->errorResponse(422, 'Valid HTTPS URL is required', 'INVALID_URL');
            }
            $updates[] = "url = ?";
            $params[] = $url;
        }
        
        // Events aktualisieren
        if (isset($data['events'])) {
            $allowedEvents = [
                'referrer.created', 'referrer.updated', 'referrer.deleted',
                'conversion.created', 'conversion.confirmed', 'conversion.cancelled',
                'reward.unlocked', 'reward.claimed'
            ];
            $events = array_intersect((array)$data['events'], $allowedEvents);
            if (!empty($events)) {
                $updates[] = "events = ?";
                $params[] = json_encode($events);
            }
        }
        
        // Aktiv-Status aktualisieren
        if (isset($data['is_active'])) {
            $updates[] = "is_active = ?";
            $params[] = $data['is_active'] ? 1 : 0;
        }
        
        if (empty($updates)) {
            $api->errorResponse(400, 'No valid fields to update', 'NO_UPDATES');
        }
        
        $params[] = $resourceId;
        $params[] = $customerId;
        
        $db->query(
            "UPDATE webhooks SET " . implode(', ', $updates) . " WHERE id = ? AND customer_id = ?",
            $params
        );
        
        // Aktualisierte Daten zurückgeben
        $webhook = $db->fetch(
            "SELECT id, url, events, is_active, created_at, updated_at FROM webhooks WHERE id = ?",
            [$resourceId]
        );
        $webhook['events'] = json_decode($webhook['events'], true);
        
        $api->successResponse($webhook);
        break;
        
    case 'DELETE':
        if (!$resourceId) {
            $api->errorResponse(400, 'Webhook ID required', 'MISSING_ID');
        }
        
        // Webhook finden
        $webhook = $db->fetch(
            "SELECT * FROM webhooks WHERE id = ? AND customer_id = ?",
            [$resourceId, $customerId]
        );
        
        if (!$webhook) {
            $api->errorResponse(404, 'Webhook not found', 'NOT_FOUND');
        }
        
        // Löschen
        $db->query("DELETE FROM webhooks WHERE id = ?", [$resourceId]);
        
        $api->successResponse(['deleted' => true, 'id' => (int)$resourceId]);
        break;
        
    default:
        $api->errorResponse(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
}
