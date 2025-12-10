<?php
/**
 * Leadbusiness REST API v1 - Referrers Endpoint
 * 
 * GET /api/v1/referrers - Liste aller Empfehler
 * POST /api/v1/referrers - Neuen Empfehler erstellen
 * GET /api/v1/referrers/{id} - Einzelnen Empfehler abrufen
 * PUT /api/v1/referrers/{id} - Empfehler aktualisieren
 * DELETE /api/v1/referrers/{id} - Empfehler löschen
 */

use Leadbusiness\Database;

$db = Database::getInstance();
$customerId = $api->getCustomerId();
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    
    case 'GET':
        if ($resourceId) {
            // Einzelnen Empfehler abrufen
            $referrer = $db->fetch(
                "SELECT id, name, email, phone, referral_code, clicks, conversions, 
                        current_reward_level, status, notes, created_at, updated_at
                 FROM leads 
                 WHERE customer_id = ? AND (id = ? OR referral_code = ?)",
                [$customerId, $resourceId, $resourceId]
            );
            
            if (!$referrer) {
                $api->errorResponse(404, 'Referrer not found', 'NOT_FOUND');
            }
            
            // Conversions für diesen Empfehler
            $conversions = $db->fetchAll(
                "SELECT id, converted_name, converted_email, order_value, status, created_at
                 FROM conversions WHERE referrer_id = ? ORDER BY created_at DESC LIMIT 10",
                [$referrer['id']]
            );
            
            $referrer['recent_conversions'] = $conversions;
            
            // Empfehlungslink hinzufügen
            $customer = $api->getCustomer();
            $referrer['referral_link'] = "https://{$customer['subdomain']}.empfehlungen.cloud/r/{$referrer['referral_code']}";
            
            $api->successResponse($referrer);
            
        } else {
            // Alle Empfehler abrufen (mit Pagination)
            $pagination = $api->getPagination();
            
            // Filter
            $status = $api->getQueryParam('status');
            $search = $api->getQueryParam('search');
            $orderBy = $api->getQueryParam('order_by', 'created_at');
            $orderDir = strtoupper($api->getQueryParam('order_dir', 'DESC')) === 'ASC' ? 'ASC' : 'DESC';
            $minConversions = (int)$api->getQueryParam('min_conversions', 0);
            
            // Erlaubte Sortierfelder
            $allowedOrderBy = ['id', 'name', 'email', 'clicks', 'conversions', 'created_at', 'updated_at'];
            if (!in_array($orderBy, $allowedOrderBy)) {
                $orderBy = 'created_at';
            }
            
            // Query bauen
            $where = "customer_id = ?";
            $params = [$customerId];
            
            if ($status) {
                $where .= " AND status = ?";
                $params[] = $status;
            }
            
            if ($minConversions > 0) {
                $where .= " AND conversions >= ?";
                $params[] = $minConversions;
            }
            
            if ($search) {
                $where .= " AND (name LIKE ? OR email LIKE ? OR referral_code LIKE ?)";
                $searchTerm = "%$search%";
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
            }
            
            // Total zählen
            $total = $db->fetch("SELECT COUNT(*) as count FROM leads WHERE $where", $params);
            
            // Daten abrufen
            $params[] = $pagination['limit'];
            $params[] = $pagination['offset'];
            
            $referrers = $db->fetchAll(
                "SELECT id, name, email, phone, referral_code, clicks, conversions, 
                        current_reward_level, status, created_at, updated_at
                 FROM leads 
                 WHERE $where
                 ORDER BY $orderBy $orderDir
                 LIMIT ? OFFSET ?",
                $params
            );
            
            // Empfehlungslinks hinzufügen
            $customer = $api->getCustomer();
            foreach ($referrers as &$ref) {
                $ref['referral_link'] = "https://{$customer['subdomain']}.empfehlungen.cloud/r/{$ref['referral_code']}";
            }
            
            $api->paginatedResponse($referrers, $total['count'], $pagination['page'], $pagination['limit']);
        }
        break;
        
    case 'POST':
        // Neuen Empfehler erstellen
        $data = $api->getRequestBody();
        
        // Validierung
        $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $api->errorResponse(422, 'Valid email is required', 'VALIDATION_ERROR');
        }
        
        // Prüfen ob E-Mail bereits existiert
        $existing = $db->fetch(
            "SELECT id FROM leads WHERE customer_id = ? AND email = ?",
            [$customerId, $email]
        );
        
        if ($existing) {
            $api->errorResponse(409, 'Referrer with this email already exists', 'DUPLICATE_EMAIL');
        }
        
        // Referral Code generieren oder verwenden
        $referralCode = $data['referral_code'] ?? strtoupper(substr(md5($email . time()), 0, 8));
        
        // Code-Konflikt prüfen
        $codeExists = $db->fetch(
            "SELECT id FROM leads WHERE customer_id = ? AND referral_code = ?",
            [$customerId, $referralCode]
        );
        
        if ($codeExists) {
            $referralCode = strtoupper(substr(md5($email . microtime()), 0, 8));
        }
        
        // Erstellen
        $db->query(
            "INSERT INTO leads (customer_id, name, email, phone, referral_code, notes, status, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())",
            [
                $customerId,
                $data['name'] ?? '',
                $email,
                $data['phone'] ?? null,
                $referralCode,
                $data['notes'] ?? null
            ]
        );
        
        $newId = $db->lastInsertId();
        
        // Neuen Empfehler zurückgeben
        $referrer = $db->fetch(
            "SELECT id, name, email, phone, referral_code, clicks, conversions, status, created_at
             FROM leads WHERE id = ?",
            [$newId]
        );
        
        // Counter aktualisieren
        $db->query("UPDATE customers SET total_leads = total_leads + 1 WHERE id = ?", [$customerId]);
        
        // Empfehlungslink hinzufügen
        $customer = $api->getCustomer();
        $referrer['referral_link'] = "https://{$customer['subdomain']}.empfehlungen.cloud/r/{$referrer['referral_code']}";
        
        $api->successResponse($referrer, 201);
        break;
        
    case 'PUT':
        if (!$resourceId) {
            $api->errorResponse(400, 'Referrer ID required', 'MISSING_ID');
        }
        
        // Empfehler finden
        $referrer = $db->fetch(
            "SELECT * FROM leads WHERE customer_id = ? AND id = ?",
            [$customerId, $resourceId]
        );
        
        if (!$referrer) {
            $api->errorResponse(404, 'Referrer not found', 'NOT_FOUND');
        }
        
        $data = $api->getRequestBody();
        
        // Nur erlaubte Felder updaten
        $updates = [];
        $params = [];
        
        $allowedFields = ['name', 'phone', 'notes', 'status'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            $api->errorResponse(400, 'No valid fields to update', 'NO_UPDATES');
        }
        
        $params[] = $resourceId;
        $params[] = $customerId;
        
        $db->query(
            "UPDATE leads SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = ? AND customer_id = ?",
            $params
        );
        
        // Aktualisierte Daten zurückgeben
        $referrer = $db->fetch(
            "SELECT id, name, email, phone, referral_code, clicks, conversions, status, created_at, updated_at
             FROM leads WHERE id = ?",
            [$resourceId]
        );
        
        // Empfehlungslink hinzufügen
        $customer = $api->getCustomer();
        $referrer['referral_link'] = "https://{$customer['subdomain']}.empfehlungen.cloud/r/{$referrer['referral_code']}";
        
        $api->successResponse($referrer);
        break;
        
    case 'DELETE':
        if (!$resourceId) {
            $api->errorResponse(400, 'Referrer ID required', 'MISSING_ID');
        }
        
        // Empfehler finden
        $referrer = $db->fetch(
            "SELECT * FROM leads WHERE customer_id = ? AND id = ?",
            [$customerId, $resourceId]
        );
        
        if (!$referrer) {
            $api->errorResponse(404, 'Referrer not found', 'NOT_FOUND');
        }
        
        // Soft Delete (Status auf deleted)
        $db->query(
            "UPDATE leads SET status = 'deleted', updated_at = NOW() WHERE id = ? AND customer_id = ?",
            [$resourceId, $customerId]
        );
        
        // Counter aktualisieren
        $db->query("UPDATE customers SET total_leads = total_leads - 1 WHERE id = ? AND total_leads > 0", [$customerId]);
        
        $api->successResponse(['deleted' => true, 'id' => (int)$resourceId], 200);
        break;
        
    default:
        $api->errorResponse(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
}
