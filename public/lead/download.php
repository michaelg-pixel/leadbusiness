<?php
/**
 * Lead Reward Download Handler
 * 
 * Stellt digitale Downloads für Belohnungen bereit
 * Prüft Token-Gültigkeit und loggt Downloads
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/services/LeadAuthService.php';

$db = Database::getInstance();
$auth = new LeadAuthService();

$token = $_GET['token'] ?? '';

if (empty($token)) {
    http_response_code(400);
    die('Fehlender Token');
}

// Delivery per Token finden
$delivery = $db->fetch(
    "SELECT rd.*, r.download_file_path, r.download_file_name, r.description as reward_name
     FROM reward_deliveries rd
     JOIN rewards r ON rd.reward_id = r.id
     WHERE rd.download_token = ?",
    [$token]
);

if (!$delivery) {
    http_response_code(404);
    die('Download nicht gefunden');
}

// Ablaufdatum prüfen
if ($delivery['download_expires_at'] && strtotime($delivery['download_expires_at']) < time()) {
    http_response_code(410);
    die('Dieser Download-Link ist abgelaufen. Bitte kontaktieren Sie uns für einen neuen Link.');
}

// Datei prüfen
$filePath = __DIR__ . '/../../' . ltrim($delivery['download_file_path'], '/');

if (!file_exists($filePath)) {
    http_response_code(404);
    die('Datei nicht gefunden');
}

// Download-Counter erhöhen
$db->execute(
    "UPDATE reward_deliveries SET download_count = download_count + 1 WHERE id = ?",
    [$delivery['id']]
);

// Activity Log
try {
    $db->execute(
        "INSERT INTO lead_activity_log (lead_id, activity_type, details, ip_hash, user_agent)
         VALUES (?, 'reward_downloaded', ?, ?, ?)",
        [
            $delivery['lead_id'],
            json_encode([
                'delivery_id' => $delivery['id'],
                'download_count' => $delivery['download_count'] + 1
            ]),
            hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''),
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]
    );
} catch (Exception $e) {}

// Dateiname für Download
$fileName = $delivery['download_file_name'] ?: basename($filePath);

// MIME-Type ermitteln
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filePath);
finfo_close($finfo);

// Headers setzen
header('Content-Type: ' . $mimeType);
header('Content-Disposition: attachment; filename="' . addslashes($fileName) . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Datei ausgeben
readfile($filePath);
exit;
