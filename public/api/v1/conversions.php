<?php
/**
 * Leadbusiness REST API v1 - Conversions Endpoint
 * 
 * GET /api/v1/conversions - Liste aller Conversions
 * POST /api/v1/conversions - Neue Conversion tracken
 * GET /api/v1/conversions/{id} - Einzelne Conversion abrufen
 */

use Leadbusiness\Database;

$db = Database::getInstance();
$customerId = $api->getCustomerId();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    
    case 'GET':
        if ($resourceId) {
            // Einzelne Conversion abrufen
            $conversion = $db->fetch(
                "SELECT c.*, l.name as referrer_name, l.email as referrer_email, l.referral_code
                 FROM conversions c
                 JOIN leads l ON c.referrer_id = l.id
                 WHERE c.id = ? AND l.customer_id = ?",
                [$resourceId, $customerId]
            );
            
            if (!$conversion) {
                $api->errorResponse(404, 'Conversion not found', 'NOT_FOUND');
            }
            
            $api->successResponse($conversion);
            
        } else {
            // Alle Conversions abrufen (mit Pagination)
            $pagination = $api->getPagination();
            
            // Filter
            $status = $api->getQueryParam('status');
            $referrerId = $api->getQueryParam('referrer_id');
            $referralCode = $api->getQueryParam('referral_code');
            $from = $api->getQueryParam('from'); // Datum (YYYY-MM-DD)
            $to = $api->getQueryParam('to'); // Datum (YYYY-MM-DD)
            
            // Query bauen
            $where = "l.customer_id = ?";
            $params = [$customerId];
            
            if ($status) {
                $where .= " AND c.status = ?";
                $params[] = $status;
            }
            
            if ($referrerId) {
                $where .= " AND c.referrer_id = ?";
                $params[] = $referrerId;
            }
            
            if ($referralCode) {
                $where .= " AND l.referral_code = ?";
                $params[] = $referralCode;
            }
            
            if ($from) {
                $where .= " AND DATE(c.created_at) >= ?";
                $params[] = $from;
            }
            
            if ($to) {
                $where .= " AND DATE(c.created_at) <= ?";
                $params[] = $to;
            }
            
            // Total zählen
            $total = $db->fetch(
                "SELECT COUNT(*) as count FROM conversions c JOIN leads l ON c.referrer_id = l.id WHERE $where", 
                $params
            );
            
            // Daten abrufen
            $params[] = $pagination['limit'];
            $params[] = $pagination['offset'];
            
            $conversions = $db->fetchAll(
                "SELECT c.id, c.referrer_id, l.referral_code, l.name as referrer_name, 
                        c.converted_name, c.converted_email, c.order_id, c.order_value,
                        c.status, c.created_at
                 FROM conversions c
                 JOIN leads l ON c.referrer_id = l.id
                 WHERE $where
                 ORDER BY c.created_at DESC
                 LIMIT ? OFFSET ?",
                $params
            );
            
            $api->paginatedResponse($conversions, $total['count'], $pagination['page'], $pagination['limit']);
        }
        break;
        
    case 'POST':
        // Neue Conversion tracken
        $data = $api->getRequestBody();
        
        // Referrer finden (über ID oder Code)
        $referrerId = $data['referrer_id'] ?? null;
        $referralCode = $data['referral_code'] ?? null;
        
        if (!$referrerId && !$referralCode) {
            $api->errorResponse(422, 'Either referrer_id or referral_code is required', 'VALIDATION_ERROR');
        }
        
        if ($referrerId) {
            $referrer = $db->fetch(
                "SELECT * FROM leads WHERE id = ? AND customer_id = ? AND status = 'active'",
                [$referrerId, $customerId]
            );
        } else {
            $referrer = $db->fetch(
                "SELECT * FROM leads WHERE referral_code = ? AND customer_id = ? AND status = 'active'",
                [$referralCode, $customerId]
            );
        }
        
        if (!$referrer) {
            $api->errorResponse(404, 'Referrer not found or inactive', 'REFERRER_NOT_FOUND');
        }
        
        // Conversion erstellen
        $db->query(
            "INSERT INTO conversions (referrer_id, customer_id, converted_name, converted_email, order_id, order_value, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 'confirmed', NOW())",
            [
                $referrer['id'],
                $customerId,
                $data['converted_name'] ?? null,
                $data['converted_email'] ?? null,
                $data['order_id'] ?? null,
                $data['order_value'] ?? 0
            ]
        );
        
        $conversionId = $db->lastInsertId();
        
        // Empfehler-Statistiken aktualisieren
        $db->query(
            "UPDATE leads SET conversions = conversions + 1, updated_at = NOW() WHERE id = ?",
            [$referrer['id']]
        );
        
        // Kunden-Statistiken aktualisieren
        $db->query(
            "UPDATE customers SET total_conversions = total_conversions + 1 WHERE id = ?",
            [$customerId]
        );
        
        // Belohnungslevel prüfen und aktualisieren
        $newConversionCount = $referrer['conversions'] + 1;
        $nextRewardLevel = $db->fetch(
            "SELECT level FROM reward_levels 
             WHERE customer_id = ? AND threshold <= ? 
             ORDER BY threshold DESC LIMIT 1",
            [$customerId, $newConversionCount]
        );
        
        if ($nextRewardLevel && $nextRewardLevel['level'] > ($referrer['current_reward_level'] ?? 0)) {
            $db->query(
                "UPDATE leads SET current_reward_level = ? WHERE id = ?",
                [$nextRewardLevel['level'], $referrer['id']]
            );
        }
        
        // Conversion zurückgeben
        $conversion = $db->fetch(
            "SELECT c.*, l.name as referrer_name, l.email as referrer_email, l.referral_code
             FROM conversions c
             JOIN leads l ON c.referrer_id = l.id
             WHERE c.id = ?",
            [$conversionId]
        );
        
        $api->successResponse($conversion, 201);
        break;
        
    case 'PUT':
        if (!$resourceId) {
            $api->errorResponse(400, 'Conversion ID required', 'MISSING_ID');
        }
        
        // Conversion finden
        $conversion = $db->fetch(
            "SELECT c.* FROM conversions c
             JOIN leads l ON c.referrer_id = l.id
             WHERE c.id = ? AND l.customer_id = ?",
            [$resourceId, $customerId]
        );
        
        if (!$conversion) {
            $api->errorResponse(404, 'Conversion not found', 'NOT_FOUND');
        }
        
        $data = $api->getRequestBody();
        
        // Nur Status kann geändert werden
        if (!isset($data['status'])) {
            $api->errorResponse(400, 'Status field is required', 'VALIDATION_ERROR');
        }
        
        $allowedStatuses = ['pending', 'confirmed', 'rejected', 'cancelled'];
        if (!in_array($data['status'], $allowedStatuses)) {
            $api->errorResponse(422, 'Invalid status. Allowed: ' . implode(', ', $allowedStatuses), 'INVALID_STATUS');
        }
        
        $db->query(
            "UPDATE conversions SET status = ?, updated_at = NOW() WHERE id = ?",
            [$data['status'], $resourceId]
        );
        
        // Bei Stornierung: Counter zurücksetzen
        if ($data['status'] === 'cancelled' && $conversion['status'] === 'confirmed') {
            $db->query("UPDATE leads SET conversions = conversions - 1 WHERE id = ? AND conversions > 0", [$conversion['referrer_id']]);
            $db->query("UPDATE customers SET total_conversions = total_conversions - 1 WHERE id = ? AND total_conversions > 0", [$customerId]);
        }
        
        // Aktualisierte Conversion zurückgeben
        $conversion = $db->fetch(
            "SELECT c.*, l.name as referrer_name, l.email as referrer_email, l.referral_code
             FROM conversions c
             JOIN leads l ON c.referrer_id = l.id
             WHERE c.id = ?",
            [$resourceId]
        );
        
        $api->successResponse($conversion);
        break;
        
    default:
        $api->errorResponse(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
}
