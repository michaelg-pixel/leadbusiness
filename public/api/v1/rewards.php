<?php
/**
 * Leadbusiness REST API v1 - Rewards Endpoint
 * 
 * GET /api/v1/rewards - Liste aller Belohnungsstufen
 * GET /api/v1/rewards/{level} - Einzelne Stufe abrufen
 * GET /api/v1/rewards/claims - Belohnungs-Anforderungen (Enterprise)
 */

use Leadbusiness\Database;

$db = Database::getInstance();
$customerId = $api->getCustomerId();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    $api->errorResponse(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
}

// Sub-Endpoint bestimmen
$subEndpoint = $resourceId ?? 'list';

switch ($subEndpoint) {
    
    case 'list':
    default:
        // Alle Belohnungsstufen abrufen
        $rewards = $db->fetchAll(
            "SELECT level, threshold, reward_type, reward_description, 
                    reward_value, reward_code, reward_url, requires_address,
                    created_at
             FROM reward_levels 
             WHERE customer_id = ?
             ORDER BY level ASC",
            [$customerId]
        );
        
        // Statistiken pro Stufe
        $levelStats = $db->fetchAll(
            "SELECT current_reward_level as level, COUNT(*) as count
             FROM leads 
             WHERE customer_id = ? AND status = 'active'
             GROUP BY current_reward_level",
            [$customerId]
        );
        
        $statsByLevel = array_column($levelStats, 'count', 'level');
        
        // Stats zu Rewards hinzufügen
        foreach ($rewards as &$reward) {
            $reward['referrers_at_level'] = (int)($statsByLevel[$reward['level']] ?? 0);
            
            // Referrers die kurz vor dieser Stufe stehen
            $almostThere = $db->fetch(
                "SELECT COUNT(*) as count FROM leads 
                 WHERE customer_id = ? AND status = 'active' 
                 AND conversions >= ? AND conversions < ?",
                [$customerId, max(0, $reward['threshold'] - 2), $reward['threshold']]
            );
            $reward['referrers_close_to_level'] = (int)($almostThere['count'] ?? 0);
        }
        
        $api->successResponse([
            'reward_levels' => $rewards,
            'total_levels' => count($rewards)
        ]);
        break;
        
    case 'claims':
        // Belohnungs-Anforderungen (für Enterprise)
        $customer = $api->getCustomer();
        if ($customer['plan'] !== 'enterprise') {
            $api->errorResponse(403, 'Claims endpoint requires Enterprise plan', 'ENTERPRISE_REQUIRED');
        }
        
        $pagination = $api->getPagination();
        $status = $api->getQueryParam('status');
        
        $where = "rc.customer_id = ?";
        $params = [$customerId];
        
        if ($status) {
            $where .= " AND rc.status = ?";
            $params[] = $status;
        }
        
        $total = $db->fetch(
            "SELECT COUNT(*) as count FROM reward_claims rc WHERE $where",
            $params
        );
        
        $params[] = $pagination['limit'];
        $params[] = $pagination['offset'];
        
        $claims = $db->fetchAll(
            "SELECT rc.id, rc.referrer_id, l.name as referrer_name, l.email as referrer_email,
                    rc.reward_level, rl.reward_type, rl.reward_description,
                    rc.status, rc.claimed_at, rc.processed_at,
                    rc.shipping_address, rc.notes
             FROM reward_claims rc
             JOIN leads l ON rc.referrer_id = l.id
             LEFT JOIN reward_levels rl ON rl.customer_id = rc.customer_id AND rl.level = rc.reward_level
             WHERE $where
             ORDER BY rc.claimed_at DESC
             LIMIT ? OFFSET ?",
            $params
        );
        
        $api->paginatedResponse($claims, $total['count'], $pagination['page'], $pagination['limit']);
        break;
        
    case (is_numeric($subEndpoint) ? $subEndpoint : false):
        // Einzelne Stufe abrufen
        $level = (int)$subEndpoint;
        
        $reward = $db->fetch(
            "SELECT level, threshold, reward_type, reward_description, 
                    reward_value, reward_code, reward_url, requires_address,
                    created_at
             FROM reward_levels 
             WHERE customer_id = ? AND level = ?",
            [$customerId, $level]
        );
        
        if (!$reward) {
            $api->errorResponse(404, 'Reward level not found', 'NOT_FOUND');
        }
        
        // Referrers auf dieser Stufe
        $referrers = $db->fetchAll(
            "SELECT id, name, email, referral_code, conversions
             FROM leads 
             WHERE customer_id = ? AND current_reward_level = ? AND status = 'active'
             ORDER BY conversions DESC
             LIMIT 20",
            [$customerId, $level]
        );
        
        $reward['referrers'] = $referrers;
        $reward['referrer_count'] = count($referrers);
        
        $api->successResponse($reward);
        break;
}
