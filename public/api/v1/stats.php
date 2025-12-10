<?php
/**
 * Leadbusiness REST API v1 - Stats Endpoint
 * 
 * GET /api/v1/stats - Gesamtstatistiken
 * GET /api/v1/stats/daily - Tägliche Statistiken
 * GET /api/v1/stats/top - Top Empfehler
 */

use Leadbusiness\Database;

$db = Database::getInstance();
$customerId = $api->getCustomerId();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    $api->errorResponse(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
}

// Sub-Resource bestimmen
$subEndpoint = $resourceId ?? 'overview';

switch ($subEndpoint) {
    
    case 'overview':
    default:
        // Gesamtstatistiken
        $stats = $db->fetch(
            "SELECT 
                (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status IN ('active', 'pending')) as total_referrers,
                (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status = 'active') as active_referrers,
                (SELECT SUM(conversions) FROM leads WHERE customer_id = ?) as total_conversions,
                (SELECT SUM(clicks) FROM leads WHERE customer_id = ?) as total_clicks,
                (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND DATE(created_at) = CURDATE()) as referrers_today,
                (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as referrers_week,
                (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as referrers_month,
                (SELECT COUNT(*) FROM conversions c JOIN leads l ON c.referrer_id = l.id WHERE l.customer_id = ? AND DATE(c.created_at) = CURDATE()) as conversions_today,
                (SELECT COUNT(*) FROM conversions c JOIN leads l ON c.referrer_id = l.id WHERE l.customer_id = ? AND c.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)) as conversions_week,
                (SELECT COUNT(*) FROM conversions c JOIN leads l ON c.referrer_id = l.id WHERE l.customer_id = ? AND c.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as conversions_month,
                (SELECT AVG(conversions) FROM leads WHERE customer_id = ? AND conversions > 0) as avg_conversions_per_referrer
            ",
            [$customerId, $customerId, $customerId, $customerId, $customerId, $customerId, $customerId, $customerId, $customerId, $customerId, $customerId]
        );
        
        // Conversion Rate berechnen
        $conversionRate = $stats['total_clicks'] > 0 
            ? round(($stats['total_conversions'] / $stats['total_clicks']) * 100, 2) 
            : 0;
        
        $api->successResponse([
            'overview' => [
                'total_referrers' => (int)($stats['total_referrers'] ?? 0),
                'active_referrers' => (int)($stats['active_referrers'] ?? 0),
                'total_conversions' => (int)($stats['total_conversions'] ?? 0),
                'total_clicks' => (int)($stats['total_clicks'] ?? 0),
                'conversion_rate' => $conversionRate,
                'avg_conversions_per_referrer' => round($stats['avg_conversions_per_referrer'] ?? 0, 2)
            ],
            'today' => [
                'new_referrers' => (int)($stats['referrers_today'] ?? 0),
                'conversions' => (int)($stats['conversions_today'] ?? 0)
            ],
            'this_week' => [
                'new_referrers' => (int)($stats['referrers_week'] ?? 0),
                'conversions' => (int)($stats['conversions_week'] ?? 0)
            ],
            'this_month' => [
                'new_referrers' => (int)($stats['referrers_month'] ?? 0),
                'conversions' => (int)($stats['conversions_month'] ?? 0)
            ]
        ]);
        break;
        
    case 'daily':
        // Tägliche Statistiken (letzte 30 Tage)
        $days = min(90, max(7, (int)$api->getQueryParam('days', 30)));
        
        $dailyReferrers = $db->fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM leads 
             WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY DATE(created_at) 
             ORDER BY date ASC",
            [$customerId, $days]
        );
        
        $dailyConversions = $db->fetchAll(
            "SELECT DATE(c.created_at) as date, COUNT(*) as count 
             FROM conversions c
             JOIN leads l ON c.referrer_id = l.id
             WHERE l.customer_id = ? AND c.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY DATE(c.created_at) 
             ORDER BY date ASC",
            [$customerId, $days]
        );
        
        $dailyClicks = $db->fetchAll(
            "SELECT DATE(created_at) as date, COUNT(*) as count 
             FROM link_clicks 
             WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY DATE(created_at) 
             ORDER BY date ASC",
            [$customerId, $days]
        );
        
        // In assoziatives Array umwandeln für einfacheren Zugriff
        $refByDate = array_column($dailyReferrers, 'count', 'date');
        $convByDate = array_column($dailyConversions, 'count', 'date');
        $clicksByDate = array_column($dailyClicks, 'count', 'date');
        
        // Daten für jeden Tag zusammenstellen
        $dailyData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dailyData[] = [
                'date' => $date,
                'new_referrers' => (int)($refByDate[$date] ?? 0),
                'conversions' => (int)($convByDate[$date] ?? 0),
                'clicks' => (int)($clicksByDate[$date] ?? 0)
            ];
        }
        
        $api->successResponse([
            'period' => [
                'days' => $days,
                'from' => date('Y-m-d', strtotime("-$days days")),
                'to' => date('Y-m-d')
            ],
            'daily' => $dailyData
        ]);
        break;
        
    case 'top':
        // Top Empfehler
        $limit = min(50, max(5, (int)$api->getQueryParam('limit', 10)));
        $metric = $api->getQueryParam('by', 'conversions');
        
        $allowedMetrics = ['conversions', 'clicks'];
        if (!in_array($metric, $allowedMetrics)) {
            $metric = 'conversions';
        }
        
        $topReferrers = $db->fetchAll(
            "SELECT id, name, email, referral_code, clicks, conversions,
                    CASE WHEN clicks > 0 THEN ROUND((conversions / clicks) * 100, 2) ELSE 0 END as conversion_rate
             FROM leads 
             WHERE customer_id = ? AND status = 'active' AND $metric > 0
             ORDER BY $metric DESC 
             LIMIT ?",
            [$customerId, $limit]
        );
        
        $api->successResponse([
            'metric' => $metric,
            'top_referrers' => $topReferrers
        ]);
        break;
        
    case 'rewards':
        // Belohnungs-Statistiken
        $rewardStats = $db->fetchAll(
            "SELECT 
                current_reward_level as level,
                COUNT(*) as referrer_count
             FROM leads 
             WHERE customer_id = ? AND status = 'active'
             GROUP BY current_reward_level
             ORDER BY current_reward_level ASC",
            [$customerId]
        );
        
        $pendingRewards = $db->fetch(
            "SELECT COUNT(*) as count FROM reward_claims 
             WHERE customer_id = ? AND status = 'pending'",
            [$customerId]
        );
        
        $api->successResponse([
            'referrers_by_level' => $rewardStats,
            'pending_reward_claims' => (int)($pendingRewards['count'] ?? 0)
        ]);
        break;
}
