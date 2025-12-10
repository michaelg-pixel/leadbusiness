<?php
/**
 * Leadbusiness REST API v1 - Export Endpoint (Enterprise only)
 * 
 * GET /api/v1/export/referrers - Empfehler exportieren
 * GET /api/v1/export/conversions - Conversions exportieren
 */

use Leadbusiness\Database;

$db = Database::getInstance();
$customerId = $api->getCustomerId();
$method = $_SERVER['REQUEST_METHOD'];

if ($method !== 'GET') {
    $api->errorResponse(405, 'Method not allowed', 'METHOD_NOT_ALLOWED');
}

// Format bestimmen (json oder csv)
$format = strtolower($api->getQueryParam('format', 'json'));
if (!in_array($format, ['json', 'csv'])) {
    $format = 'json';
}

// Zeitraum-Filter
$from = $api->getQueryParam('from');
$to = $api->getQueryParam('to');

// Export-Typ bestimmen
$exportType = $resourceId ?? 'referrers';

switch ($exportType) {
    
    case 'referrers':
        // Alle Empfehler exportieren
        $where = "customer_id = ?";
        $params = [$customerId];
        
        // Status-Filter
        $status = $api->getQueryParam('status');
        if ($status) {
            $where .= " AND status = ?";
            $params[] = $status;
        }
        
        // Datum-Filter
        if ($from) {
            $where .= " AND DATE(created_at) >= ?";
            $params[] = $from;
        }
        if ($to) {
            $where .= " AND DATE(created_at) <= ?";
            $params[] = $to;
        }
        
        $data = $db->fetchAll(
            "SELECT id, name, email, phone, referral_code, clicks, conversions, 
                    current_reward_level, status, notes, created_at, updated_at
             FROM leads 
             WHERE $where
             ORDER BY created_at DESC",
            $params
        );
        
        $filename = 'referrers_export_' . date('Y-m-d');
        break;
        
    case 'conversions':
        // Alle Conversions exportieren
        $where = "l.customer_id = ?";
        $params = [$customerId];
        
        // Status-Filter
        $status = $api->getQueryParam('status');
        if ($status) {
            $where .= " AND c.status = ?";
            $params[] = $status;
        }
        
        // Referrer-Filter
        $referrerId = $api->getQueryParam('referrer_id');
        if ($referrerId) {
            $where .= " AND c.referrer_id = ?";
            $params[] = $referrerId;
        }
        
        // Datum-Filter
        if ($from) {
            $where .= " AND DATE(c.created_at) >= ?";
            $params[] = $from;
        }
        if ($to) {
            $where .= " AND DATE(c.created_at) <= ?";
            $params[] = $to;
        }
        
        $data = $db->fetchAll(
            "SELECT c.id, l.name as referrer_name, l.email as referrer_email, l.referral_code,
                    c.converted_name, c.converted_email, c.order_id, c.order_value,
                    c.status, c.created_at
             FROM conversions c
             JOIN leads l ON c.referrer_id = l.id
             WHERE $where
             ORDER BY c.created_at DESC",
            $params
        );
        
        $filename = 'conversions_export_' . date('Y-m-d');
        break;
        
    case 'all':
        // Kompletter Export (Empfehler + Conversions)
        $referrers = $db->fetchAll(
            "SELECT * FROM leads WHERE customer_id = ? ORDER BY created_at DESC",
            [$customerId]
        );
        
        $conversions = $db->fetchAll(
            "SELECT c.*, l.referral_code 
             FROM conversions c
             JOIN leads l ON c.referrer_id = l.id
             WHERE l.customer_id = ?
             ORDER BY c.created_at DESC",
            [$customerId]
        );
        
        if ($format === 'json') {
            $api->successResponse([
                'referrers' => $referrers,
                'conversions' => $conversions,
                'exported_at' => date('c'),
                'total_referrers' => count($referrers),
                'total_conversions' => count($conversions)
            ]);
        } else {
            // Bei CSV nur Referrers
            $data = $referrers;
            $filename = 'full_export_' . date('Y-m-d');
        }
        exit;
        
    default:
        $api->errorResponse(400, "Invalid export type: $exportType. Use 'referrers', 'conversions', or 'all'", 'INVALID_EXPORT_TYPE');
}

// Output
if ($format === 'csv') {
    // CSV Export
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=\"{$filename}.csv\"");
    
    $output = fopen('php://output', 'w');
    
    // BOM für Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    if (!empty($data)) {
        // Header
        fputcsv($output, array_keys($data[0]), ';');
        
        // Daten
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }
    }
    
    fclose($output);
    exit;
    
} else {
    // JSON Export
    $api->successResponse([
        'export_type' => $exportType,
        'format' => $format,
        'filters' => [
            'from' => $from,
            'to' => $to,
            'status' => $status ?? null
        ],
        'total_records' => count($data),
        'exported_at' => date('c'),
        'data' => $data
    ]);
}
