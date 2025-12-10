<?php
/**
 * API: Test-E-Mail für Broadcasts senden
 * 
 * POST /admin/api/send-test-email.php
 */

require_once __DIR__ . '/../../includes/init.php';

header('Content-Type: application/json');

// Nur POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

// Auth prüfen
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Daten
$testEmail = sanitize($_POST['test_email'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$bodyHtml = $_POST['body_html'] ?? '';
$fromName = sanitize($_POST['from_name'] ?? 'Leadbusiness');
$fromEmail = sanitize($_POST['from_email'] ?? 'info@empfehlungen.cloud');

// Validierung
if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'error' => 'Ungültige E-Mail-Adresse']);
    exit;
}

if (empty($subject)) {
    echo json_encode(['success' => false, 'error' => 'Betreff ist erforderlich']);
    exit;
}

if (empty($bodyHtml)) {
    echo json_encode(['success' => false, 'error' => 'Inhalt ist erforderlich']);
    exit;
}

try {
    require_once __DIR__ . '/../../includes/services/MailgunService.php';
    $mailgun = new MailgunService();
    
    // Test-Variablen ersetzen
    $testVariables = [
        '{company_name}' => 'Test Firma GmbH',
        '{contact_name}' => 'Max Mustermann',
        '{email}' => $testEmail,
        '{subdomain}' => 'test-firma',
        '{unsubscribe_link}' => 'https://empfehlungen.cloud/admin/unsubscribe?email=' . urlencode($testEmail) . '&token=test',
    ];
    
    $subject = str_replace(array_keys($testVariables), array_values($testVariables), $subject);
    $bodyHtml = str_replace(array_keys($testVariables), array_values($testVariables), $bodyHtml);
    
    // Test-Banner hinzufügen
    $testBanner = '<div style="background: #fef3c7; border: 2px solid #f59e0b; padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center;">
        <strong style="color: #92400e;">🧪 TEST-E-MAIL</strong><br>
        <span style="font-size: 12px; color: #78350f;">Diese E-Mail ist nur ein Test. Variablen wurden mit Beispieldaten ersetzt.</span>
    </div>';
    
    // Banner am Anfang des Body einfügen
    if (stripos($bodyHtml, '<body') !== false) {
        $bodyHtml = preg_replace('/(<body[^>]*>)/i', '$1' . $testBanner, $bodyHtml);
    } else {
        $bodyHtml = $testBanner . $bodyHtml;
    }
    
    // Senden
    $result = $mailgun->send(
        $testEmail,
        '[TEST] ' . $subject,
        $bodyHtml,
        [
            'from_name' => $fromName,
            'from_email' => $fromEmail,
            'tags' => ['test_email', 'admin_broadcast_test'],
        ]
    );
    
    // Log
    $db = db();
    $db->execute("
        INSERT INTO admin_activity_log (admin_id, action, details, ip_address, created_at)
        VALUES (?, 'broadcast_test_sent', ?, ?, NOW())
    ", [
        $_SESSION['admin_id'],
        json_encode(['to' => $testEmail, 'subject' => $subject]),
        $_SERVER['REMOTE_ADDR'] ?? ''
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => "Test-E-Mail wurde an $testEmail gesendet.",
        'mailgun_id' => $result['id'] ?? null
    ]);
    
} catch (Exception $e) {
    error_log("Test email error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
