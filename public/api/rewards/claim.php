<?php
/**
 * API - Claim Reward
 * Markiert eine Belohnung als eingelöst
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/services/LeadAuthService.php';

header('Content-Type: application/json');

$auth = new LeadAuthService();
$lead = $auth->check();

if (!$lead) {
    echo json_encode(['success' => false, 'error' => 'Nicht angemeldet']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$deliveryId = intval($input['delivery_id'] ?? 0);

if (!$deliveryId) {
    echo json_encode(['success' => false, 'error' => 'Keine Delivery-ID']);
    exit;
}

$db = Database::getInstance();

// Prüfen ob die Delivery dem Lead gehört
$delivery = $db->fetch(
    "SELECT * FROM reward_deliveries WHERE id = ? AND lead_id = ?",
    [$deliveryId, $lead['id']]
);

if (!$delivery) {
    echo json_encode(['success' => false, 'error' => 'Belohnung nicht gefunden']);
    exit;
}

if ($delivery['status'] === 'claimed') {
    echo json_encode(['success' => false, 'error' => 'Bereits eingelöst']);
    exit;
}

// Als eingelöst markieren
$db->execute(
    "UPDATE reward_deliveries SET status = 'claimed', claimed_at = NOW() WHERE id = ?",
    [$deliveryId]
);

// Activity Log
try {
    $db->execute(
        "INSERT INTO lead_activity_log (lead_id, activity_type, details, ip_hash, user_agent)
         VALUES (?, 'reward_claimed', ?, ?, ?)",
        [
            $lead['id'],
            json_encode(['delivery_id' => $deliveryId]),
            hash('sha256', $_SERVER['REMOTE_ADDR'] ?? ''),
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]
    );
} catch (Exception $e) {}

echo json_encode(['success' => true, 'message' => 'Belohnung als eingelöst markiert']);
