<?php
/**
 * API v1 - Stats Endpoint
 * 
 * GET /api/v1/stats - Statistiken abrufen
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
    $api->logRequest('/stats', 401, 'Authentication failed');
    exit;
}

// Rate-Limiting
if (!$api->checkRateLimit()) {
    $api->logRequest('/stats', 429, 'Rate limit exceeded');
    exit;
}

$db = \Leadbusiness\Database::getInstance();
$customerId = $api->getCustomerId();

// Nur GET erlaubt
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    $api->error(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
    $api->logRequest('/stats', 405, 'Method not allowed');
    exit;
}

// Permission prüfen
if (!$api->requirePermission('stats', 'read')) {
    $api->logRequest('/stats', 403, 'Permission denied');
    exit;
}

// Zeitraum (Standard: 30 Tage)
$days = $api->getIntParam('days', 30, 1, 365);
$since = date('Y-m-d', strtotime("-{$days} days"));

// Gesamt-Statistiken
$totals = $db->fetch(
    "SELECT 
        (SELECT COUNT(*) FROM leads WHERE customer_id = ?) as total_leads,
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status = 'converted') as total_conversions,
        (SELECT COUNT(*) FROM referrers WHERE customer_id = ?) as total_referrers,
        (SELECT COUNT(*) FROM referrer_rewards WHERE referrer_id IN (SELECT id FROM referrers WHERE customer_id = ?)) as total_rewards_earned",
    [$customerId, $customerId, $customerId, $customerId]
);

// Leads im Zeitraum
$periodStats = $db->fetch(
    "SELECT 
        COUNT(*) as leads,
        SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as conversions
     FROM leads 
     WHERE customer_id = ? AND created_at >= ?",
    [$customerId, $since]
);

// Konversionsrate
$conversionRate = $totals['total_leads'] > 0 
    ? round(($totals['total_conversions'] / $totals['total_leads']) * 100, 2) 
    : 0;

// Leads pro Tag (für Chart)
$dailyLeads = $db->fetchAll(
    "SELECT DATE(created_at) as date, 
            COUNT(*) as leads,
            SUM(CASE WHEN status = 'converted' THEN 1 ELSE 0 END) as conversions
     FROM leads 
     WHERE customer_id = ? AND created_at >= ?
     GROUP BY DATE(created_at)
     ORDER BY date ASC",
    [$customerId, $since]
);

// Top Empfehler
$topReferrers = $db->fetchAll(
    "SELECT r.referral_code, r.name, 
            COUNT(l.id) as total_leads,
            SUM(CASE WHEN l.status = 'converted' THEN 1 ELSE 0 END) as conversions
     FROM referrers r
     LEFT JOIN leads l ON r.id = l.referrer_id
     WHERE r.customer_id = ?
     GROUP BY r.id
     ORDER BY total_leads DESC
     LIMIT 10",
    [$customerId]
);

// Lead-Quellen
$sources = $db->fetchAll(
    "SELECT COALESCE(source, 'unknown') as source, COUNT(*) as count
     FROM leads 
     WHERE customer_id = ? AND created_at >= ?
     GROUP BY source
     ORDER BY count DESC",
    [$customerId, $since]
);

// Response
$api->success([
    'period' => [
        'days' => $days,
        'from' => $since,
        'to' => date('Y-m-d')
    ],
    'totals' => [
        'leads' => (int)$totals['total_leads'],
        'conversions' => (int)$totals['total_conversions'],
        'referrers' => (int)$totals['total_referrers'],
        'rewards_earned' => (int)$totals['total_rewards_earned'],
        'conversion_rate' => $conversionRate
    ],
    'period_stats' => [
        'leads' => (int)$periodStats['leads'],
        'conversions' => (int)$periodStats['conversions']
    ],
    'daily' => array_map(function($d) {
        return [
            'date' => $d['date'],
            'leads' => (int)$d['leads'],
            'conversions' => (int)$d['conversions']
        ];
    }, $dailyLeads),
    'top_referrers' => array_map(function($r) {
        return [
            'code' => $r['referral_code'],
            'name' => $r['name'],
            'leads' => (int)$r['total_leads'],
            'conversions' => (int)$r['conversions']
        ];
    }, $topReferrers),
    'sources' => array_map(function($s) {
        return [
            'source' => $s['source'],
            'count' => (int)$s['count']
        ];
    }, $sources)
]);

$api->logRequest('/stats', 200);
