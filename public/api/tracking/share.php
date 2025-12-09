<?php
/**
 * Leadbusiness - Share Tracking API
 * 
 * POST /api/tracking/share.php
 * Trackt Share-Aktionen von Empfehlern
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/security/RateLimiter.php';

header('Content-Type: application/json');

// Nur POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonError('Method not allowed', 405);
}

try {
    $db = Database::getInstance();
    
    // JSON Input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $leadId = intval($input['lead_id'] ?? 0);
    $platform = trim($input['platform'] ?? '');
    
    // Validierung
    if (!$leadId) {
        jsonError('Lead ID erforderlich', 400);
    }
    
    // Erlaubte Plattformen
    $allowedPlatforms = [
        'whatsapp', 'facebook', 'telegram', 'email', 'sms',
        'linkedin', 'xing', 'twitter', 'pinterest', 'copy', 'qrcode'
    ];
    
    if (!in_array($platform, $allowedPlatforms)) {
        jsonError('Ungültige Plattform', 400);
    }
    
    // Rate Limiting
    $rateLimiter = new RateLimiter();
    $clientIp = getClientIp();
    
    if (!$rateLimiter->checkLimit('share_actions', hashIp($clientIp))) {
        jsonError('Zu viele Anfragen', 429);
    }
    
    // Lead prüfen
    $lead = $db->fetch(
        "SELECT l.*, c.id as customer_id 
         FROM leads l 
         JOIN campaigns ca ON l.campaign_id = ca.id
         JOIN customers c ON ca.customer_id = c.id
         WHERE l.id = ? AND l.status = 'active'",
        [$leadId]
    );
    
    if (!$lead) {
        jsonError('Lead nicht gefunden', 404);
    }
    
    // Share tracken
    $db->insert('share_tracking', [
        'lead_id' => $leadId,
        'campaign_id' => $lead['campaign_id'],
        'platform' => $platform,
        'ip_hash' => hashIp($clientIp),
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Last Share aktualisieren (für Streak-Berechnung)
    $db->update('leads', [
        'last_share_at' => date('Y-m-d H:i:s'),
        'last_activity_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$leadId]);
    
    // Gamification: Punkte für Share
    $db->insert('gamification_log', [
        'lead_id' => $leadId,
        'action' => 'share',
        'points' => 5,
        'details' => json_encode(['platform' => $platform]),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    $db->query(
        "UPDATE leads SET total_points = total_points + 5 WHERE id = ?",
        [$leadId]
    );
    
    jsonSuccess(['tracked' => true], 'Share getrackt');
    
} catch (Exception $e) {
    error_log('Share Tracking Error: ' . $e->getMessage());
    jsonError('Interner Fehler', 500);
}
