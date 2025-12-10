<?php
/**
 * API v1 - Leads Endpoint
 * 
 * GET    /api/v1/leads       - Liste aller Leads
 * GET    /api/v1/leads/{id}  - Einzelner Lead
 * POST   /api/v1/leads       - Neuen Lead erstellen
 * PUT    /api/v1/leads/{id}  - Lead aktualisieren
 * DELETE /api/v1/leads/{id}  - Lead löschen
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
    $api->logRequest('/leads', 401, 'Authentication failed');
    exit;
}

// Rate-Limiting
if (!$api->checkRateLimit()) {
    $api->logRequest('/leads', 429, 'Rate limit exceeded');
    exit;
}

$db = \Leadbusiness\Database::getInstance();
$customerId = $api->getCustomerId();

// Routing basierend auf Methode und Pfad
$method = $_SERVER['REQUEST_METHOD'];
$pathParts = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$leadId = isset($pathParts[0]) && is_numeric($pathParts[0]) ? (int)$pathParts[0] : null;

switch ($method) {
    case 'GET':
        if ($leadId) {
            // Einzelnen Lead abrufen
            if (!$api->requirePermission('leads', 'read')) {
                $api->logRequest("/leads/{$leadId}", 403, 'Permission denied');
                exit;
            }
            
            $lead = $db->fetch(
                "SELECT l.*, r.referral_code as referrer_code, r.name as referrer_name 
                 FROM leads l 
                 LEFT JOIN referrers r ON l.referrer_id = r.id 
                 WHERE l.id = ? AND l.customer_id = ?",
                [$leadId, $customerId]
            );
            
            if (!$lead) {
                $api->error(404, 'Lead not found', 'LEAD_NOT_FOUND');
                $api->logRequest("/leads/{$leadId}", 404, 'Lead not found');
                exit;
            }
            
            $api->success(formatLead($lead));
            $api->logRequest("/leads/{$leadId}", 200);
            
        } else {
            // Liste aller Leads
            if (!$api->requirePermission('leads', 'read')) {
                $api->logRequest('/leads', 403, 'Permission denied');
                exit;
            }
            
            $page = $api->getIntParam('page', 1, 1);
            $perPage = $api->getIntParam('per_page', 50, 1, 100);
            $offset = ($page - 1) * $perPage;
            
            // Filter
            $where = ['l.customer_id = ?'];
            $params = [$customerId];
            
            if ($status = $api->getQueryParam('status')) {
                $where[] = 'l.status = ?';
                $params[] = $status;
            }
            
            if ($referrerCode = $api->getQueryParam('referrer_code')) {
                $where[] = 'r.referral_code = ?';
                $params[] = $referrerCode;
            }
            
            if ($since = $api->getQueryParam('since')) {
                $where[] = 'l.created_at >= ?';
                $params[] = $since;
            }
            
            $whereClause = implode(' AND ', $where);
            
            // Total Count
            $total = $db->fetch(
                "SELECT COUNT(*) as cnt FROM leads l 
                 LEFT JOIN referrers r ON l.referrer_id = r.id 
                 WHERE {$whereClause}",
                $params
            )['cnt'];
            
            // Leads laden
            $leads = $db->fetchAll(
                "SELECT l.*, r.referral_code as referrer_code, r.name as referrer_name 
                 FROM leads l 
                 LEFT JOIN referrers r ON l.referrer_id = r.id 
                 WHERE {$whereClause} 
                 ORDER BY l.created_at DESC 
                 LIMIT {$perPage} OFFSET {$offset}",
                $params
            );
            
            $formattedLeads = array_map('formatLead', $leads);
            
            $api->success($api->paginate($formattedLeads, $total, $page, $perPage));
            $api->logRequest('/leads', 200);
        }
        break;
        
    case 'POST':
        // Neuen Lead erstellen
        if (!$api->requirePermission('leads', 'write')) {
            $api->logRequest('/leads', 403, 'Permission denied');
            exit;
        }
        
        $data = $api->getJsonBody();
        if ($data === null) {
            $api->error(400, 'Invalid JSON body', 'INVALID_JSON');
            $api->logRequest('/leads', 400, 'Invalid JSON');
            exit;
        }
        
        // Validierung
        $errors = [];
        if (empty($data['name'])) {
            $errors[] = 'name is required';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'valid email is required';
        }
        
        if (!empty($errors)) {
            $api->error(400, 'Validation failed', 'VALIDATION_ERROR', ['errors' => $errors]);
            $api->logRequest('/leads', 400, 'Validation failed');
            exit;
        }
        
        // Referrer prüfen (falls angegeben)
        $referrerId = null;
        if (!empty($data['referrer_code'])) {
            $referrer = $db->fetch(
                "SELECT id FROM referrers WHERE referral_code = ? AND customer_id = ?",
                [$data['referrer_code'], $customerId]
            );
            if ($referrer) {
                $referrerId = $referrer['id'];
            }
        }
        
        // Lead erstellen
        $db->query(
            "INSERT INTO leads (customer_id, referrer_id, name, email, phone, status, source, ip_address, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
            [
                $customerId,
                $referrerId,
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['status'] ?? 'new',
                $data['source'] ?? 'api',
                $_SERVER['REMOTE_ADDR'] ?? null
            ]
        );
        
        $newLeadId = $db->lastInsertId();
        
        // Erstellten Lead laden
        $lead = $db->fetch(
            "SELECT l.*, r.referral_code as referrer_code, r.name as referrer_name 
             FROM leads l 
             LEFT JOIN referrers r ON l.referrer_id = r.id 
             WHERE l.id = ?",
            [$newLeadId]
        );
        
        $api->success(formatLead($lead), 201);
        $api->logRequest('/leads', 201);
        break;
        
    case 'PUT':
        // Lead aktualisieren
        if (!$leadId) {
            $api->error(400, 'Lead ID required', 'LEAD_ID_REQUIRED');
            $api->logRequest('/leads', 400, 'Lead ID required');
            exit;
        }
        
        if (!$api->requirePermission('leads', 'write')) {
            $api->logRequest("/leads/{$leadId}", 403, 'Permission denied');
            exit;
        }
        
        // Lead prüfen
        $lead = $db->fetch(
            "SELECT * FROM leads WHERE id = ? AND customer_id = ?",
            [$leadId, $customerId]
        );
        
        if (!$lead) {
            $api->error(404, 'Lead not found', 'LEAD_NOT_FOUND');
            $api->logRequest("/leads/{$leadId}", 404, 'Lead not found');
            exit;
        }
        
        $data = $api->getJsonBody();
        if ($data === null) {
            $api->error(400, 'Invalid JSON body', 'INVALID_JSON');
            $api->logRequest("/leads/{$leadId}", 400, 'Invalid JSON');
            exit;
        }
        
        // Updatebare Felder
        $updates = [];
        $params = [];
        
        $allowedFields = ['name', 'email', 'phone', 'status'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updates[] = "{$field} = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            $api->error(400, 'No fields to update', 'NO_UPDATES');
            $api->logRequest("/leads/{$leadId}", 400, 'No updates');
            exit;
        }
        
        $params[] = $leadId;
        $params[] = $customerId;
        
        $db->query(
            "UPDATE leads SET " . implode(', ', $updates) . ", updated_at = NOW() WHERE id = ? AND customer_id = ?",
            $params
        );
        
        // Aktualisierten Lead laden
        $lead = $db->fetch(
            "SELECT l.*, r.referral_code as referrer_code, r.name as referrer_name 
             FROM leads l 
             LEFT JOIN referrers r ON l.referrer_id = r.id 
             WHERE l.id = ?",
            [$leadId]
        );
        
        $api->success(formatLead($lead));
        $api->logRequest("/leads/{$leadId}", 200);
        break;
        
    case 'DELETE':
        // Lead löschen
        if (!$leadId) {
            $api->error(400, 'Lead ID required', 'LEAD_ID_REQUIRED');
            exit;
        }
        
        if (!$api->requirePermission('leads', 'write')) {
            $api->logRequest("/leads/{$leadId}", 403, 'Permission denied');
            exit;
        }
        
        $result = $db->query(
            "DELETE FROM leads WHERE id = ? AND customer_id = ?",
            [$leadId, $customerId]
        );
        
        if ($result->rowCount() === 0) {
            $api->error(404, 'Lead not found', 'LEAD_NOT_FOUND');
            $api->logRequest("/leads/{$leadId}", 404, 'Lead not found');
            exit;
        }
        
        $api->success(['deleted' => true]);
        $api->logRequest("/leads/{$leadId}", 200);
        break;
        
    default:
        $api->error(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
        $api->logRequest('/leads', 405, 'Method not allowed');
}

/**
 * Lead-Daten formatieren
 */
function formatLead(array $lead): array
{
    return [
        'id' => (int)$lead['id'],
        'name' => $lead['name'],
        'email' => $lead['email'],
        'phone' => $lead['phone'],
        'status' => $lead['status'],
        'source' => $lead['source'] ?? 'unknown',
        'referrer' => $lead['referrer_code'] ? [
            'code' => $lead['referrer_code'],
            'name' => $lead['referrer_name']
        ] : null,
        'created_at' => $lead['created_at'],
        'updated_at' => $lead['updated_at'] ?? null
    ];
}
