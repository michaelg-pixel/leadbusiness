<?php
/**
 * API v1 - Rewards Endpoint
 * 
 * GET /api/v1/rewards - Belohnungsstufen abrufen
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/Database.php';
require_once __DIR__ . '/../../../includes/api/ApiMiddleware.php';

use Leadbusiness\Api\ApiMiddleware;
use function Leadbusiness\Api\setCorsHeaders;
use function Leadbusiness\Api\setApiHeaders;

// Headers
setCorsHeaders();
setApiHeaders();

// Middleware
$api = new ApiMiddleware();

// Authentifizierung
if (!$api->authenticate()) {
    $api->logRequest('/rewards', 401, 'Authentication failed');
    exit;
}

// Rate-Limiting
if (!$api->checkRateLimit()) {
    $api->logRequest('/rewards', 429, 'Rate limit exceeded');
    exit;
}

$db = \Leadbusiness\Database::getInstance();
$customerId = $api->getCustomerId();

// Nur GET erlaubt
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $api->error(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
    $api->logRequest('/rewards', 405, 'Method not allowed');
    exit;
}

// Permission prüfen
if (!$api->requirePermission('rewards', 'read')) {
    $api->logRequest('/rewards', 403, 'Permission denied');
    exit;
}

// Belohnungsstufen laden
$rewards = $db->fetchAll(
    "SELECT * FROM reward_settings WHERE customer_id = ? ORDER BY level ASC",
    [$customerId]
);

// Statistiken pro Belohnung
$rewardStats = $db->fetchAll(
    "SELECT rs.id, COUNT(rr.id) as times_earned
     FROM reward_settings rs
     LEFT JOIN referrer_rewards rr ON rs.id = rr.reward_setting_id
     WHERE rs.customer_id = ?
     GROUP BY rs.id",
    [$customerId]
);

$statsMap = [];
foreach ($rewardStats as $stat) {
    $statsMap[$stat['id']] = (int)$stat['times_earned'];
}

// Response formatieren
$formattedRewards = array_map(function($r) use ($statsMap) {
    $metadata = json_decode($r['metadata'] ?? '{}', true) ?: [];
    
    return [
        'id' => (int)$r['id'],
        'level' => (int)$r['level'],
        'threshold' => (int)$r['threshold'],
        'type' => $r['reward_type'],
        'description' => $r['description'],
        'is_active' => (bool)$r['is_active'],
        'metadata' => $metadata,
        'stats' => [
            'times_earned' => $statsMap[$r['id']] ?? 0
        ]
    ];
}, $rewards);

$api->success([
    'rewards' => $formattedRewards,
    'total_levels' => count($rewards)
]);

$api->logRequest('/rewards', 200);
