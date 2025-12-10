<?php
/**
 * API v1 - Referrers Endpoint
 * 
 * GET    /api/v1/referrers           - Liste aller Empfehler
 * GET    /api/v1/referrers/{code}    - Einzelner Empfehler
 * POST   /api/v1/referrers           - Neuen Empfehler erstellen
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
    $api->logRequest('/referrers', 401, 'Authentication failed');
    exit;
}

// Rate-Limiting
if (!$api->checkRateLimit()) {
    $api->logRequest('/referrers', 429, 'Rate limit exceeded');
    exit;
}

$db = \Leadbusiness\Database::getInstance();
$customerId = $api->getCustomerId();

// Routing
$method = $_SERVER['REQUEST_METHOD'];
$pathParts = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$referrerCode = $pathParts[0] ?? null;

switch ($method) {
    case 'GET':
        if ($referrerCode) {
            // Einzelnen Empfehler abrufen
            if (!$api->requirePermission('referrers', 'read')) {
                $api->logRequest("/referrers/{$referrerCode}", 403, 'Permission denied');
                exit;
            }
            
            $referrer = $db->fetch(
                "SELECT r.*, 
                        (SELECT COUNT(*) FROM leads WHERE referrer_id = r.id) as total_leads,
                        (SELECT COUNT(*) FROM leads WHERE referrer_id = r.id AND status = 'converted') as conversions
                 FROM referrers r 
                 WHERE r.referral_code = ? AND r.customer_id = ?",
                [$referrerCode, $customerId]
            );
            
            if (!$referrer) {
                $api->error(404, 'Referrer not found', 'REFERRER_NOT_FOUND');
                $api->logRequest("/referrers/{$referrerCode}", 404, 'Referrer not found');
                exit;
            }
            
            // Badges laden
            $badges = $db->fetchAll(
                "SELECT b.name, b.icon, rb.earned_at 
                 FROM referrer_badges rb 
                 JOIN badges b ON rb.badge_id = b.id 
                 WHERE rb.referrer_id = ?",
                [$referrer['id']]
            );
            
            // Rewards laden
            $rewards = $db->fetchAll(
                "SELECT rr.*, rs.level, rs.reward_type, rs.description 
                 FROM referrer_rewards rr 
                 JOIN reward_settings rs ON rr.reward_setting_id = rs.id 
                 WHERE rr.referrer_id = ?",
                [$referrer['id']]
            );
            
            $api->success(formatReferrer($referrer, $badges, $rewards));
            $api->logRequest("/referrers/{$referrerCode}", 200);
            
        } else {
            // Liste aller Empfehler
            if (!$api->requirePermission('referrers', 'read')) {
                $api->logRequest('/referrers', 403, 'Permission denied');
                exit;
            }
            
            $page = $api->getIntParam('page', 1, 1);
            $perPage = $api->getIntParam('per_page', 50, 1, 100);
            $offset = ($page - 1) * $perPage;
            
            // Filter
            $where = ['r.customer_id = ?'];
            $params = [$customerId];
            
            if ($minLeads = $api->getQueryParam('min_leads')) {
                $where[] = '(SELECT COUNT(*) FROM leads WHERE referrer_id = r.id) >= ?';
                $params[] = (int)$minLeads;
            }
            
            if ($since = $api->getQueryParam('since')) {
                $where[] = 'r.created_at >= ?';
                $params[] = $since;
            }
            
            $whereClause = implode(' AND ', $where);
            
            // Total Count
            $total = $db->fetch(
                "SELECT COUNT(*) as cnt FROM referrers r WHERE {$whereClause}",
                $params
            )['cnt'];
            
            // Referrers laden
            $referrers = $db->fetchAll(
                "SELECT r.*, 
                        (SELECT COUNT(*) FROM leads WHERE referrer_id = r.id) as total_leads,
                        (SELECT COUNT(*) FROM leads WHERE referrer_id = r.id AND status = 'converted') as conversions
                 FROM referrers r 
                 WHERE {$whereClause} 
                 ORDER BY total_leads DESC 
                 LIMIT {$perPage} OFFSET {$offset}",
                $params
            );
            
            $formattedReferrers = array_map(function($r) {
                return formatReferrer($r);
            }, $referrers);
            
            $api->success($api->paginate($formattedReferrers, $total, $page, $perPage));
            $api->logRequest('/referrers', 200);
        }
        break;
        
    case 'POST':
        // Neuen Empfehler erstellen
        if (!$api->requirePermission('referrers', 'write')) {
            $api->logRequest('/referrers', 403, 'Permission denied');
            exit;
        }
        
        $data = $api->getJsonBody();
        if ($data === null) {
            $api->error(400, 'Invalid JSON body', 'INVALID_JSON');
            $api->logRequest('/referrers', 400, 'Invalid JSON');
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
            $api->logRequest('/referrers', 400, 'Validation failed');
            exit;
        }
        
        // E-Mail-Duplikat prüfen
        $existing = $db->fetch(
            "SELECT id FROM referrers WHERE email = ? AND customer_id = ?",
            [$data['email'], $customerId]
        );
        
        if ($existing) {
            $api->error(409, 'Referrer with this email already exists', 'DUPLICATE_EMAIL');
            $api->logRequest('/referrers', 409, 'Duplicate email');
            exit;
        }
        
        // Referral-Code generieren
        $referralCode = generateReferralCode($db, $customerId);
        
        // Empfehler erstellen
        $db->query(
            "INSERT INTO referrers (customer_id, name, email, phone, referral_code, source, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [
                $customerId,
                $data['name'],
                $data['email'],
                $data['phone'] ?? null,
                $referralCode,
                $data['source'] ?? 'api'
            ]
        );
        
        $newId = $db->lastInsertId();
        
        // Erstellten Empfehler laden
        $referrer = $db->fetch(
            "SELECT r.*, 
                    (SELECT COUNT(*) FROM leads WHERE referrer_id = r.id) as total_leads,
                    (SELECT COUNT(*) FROM leads WHERE referrer_id = r.id AND status = 'converted') as conversions
             FROM referrers r 
             WHERE r.id = ?",
            [$newId]
        );
        
        $api->success(formatReferrer($referrer), 201);
        $api->logRequest('/referrers', 201);
        break;
        
    default:
        $api->error(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
        $api->logRequest('/referrers', 405, 'Method not allowed');
}

/**
 * Empfehler-Daten formatieren
 */
function formatReferrer(array $referrer, array $badges = [], array $rewards = []): array
{
    $formatted = [
        'id' => (int)$referrer['id'],
        'name' => $referrer['name'],
        'email' => $referrer['email'],
        'phone' => $referrer['phone'] ?? null,
        'referral_code' => $referrer['referral_code'],
        'stats' => [
            'total_leads' => (int)($referrer['total_leads'] ?? 0),
            'conversions' => (int)($referrer['conversions'] ?? 0),
            'points' => (int)($referrer['points'] ?? 0),
            'streak_days' => (int)($referrer['streak_days'] ?? 0)
        ],
        'created_at' => $referrer['created_at']
    ];
    
    if (!empty($badges)) {
        $formatted['badges'] = array_map(function($b) {
            return [
                'name' => $b['name'],
                'icon' => $b['icon'],
                'earned_at' => $b['earned_at']
            ];
        }, $badges);
    }
    
    if (!empty($rewards)) {
        $formatted['rewards'] = array_map(function($r) {
            return [
                'level' => (int)$r['level'],
                'type' => $r['reward_type'],
                'description' => $r['description'],
                'status' => $r['status'],
                'earned_at' => $r['created_at']
            ];
        }, $rewards);
    }
    
    return $formatted;
}

/**
 * Einzigartigen Referral-Code generieren
 */
function generateReferralCode($db, int $customerId): string
{
    do {
        $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        $exists = $db->fetch(
            "SELECT id FROM referrers WHERE referral_code = ? AND customer_id = ?",
            [$code, $customerId]
        );
    } while ($exists);
    
    return $code;
}
