<?php
/**
 * Leadbusiness - Digistore24 IPN Webhook
 * 
 * Empfängt und verarbeitet Zahlungsbenachrichtigungen von Digistore24
 * 
 * Webhook-URL: https://empfohlen.de/api/webhooks/digistore24.php
 * 
 * Digistore24 IPN Events:
 * - connection_test: Test der Verbindung
 * - on_payment: Erfolgreiche Zahlung
 * - on_payment_missed: Fehlgeschlagene Zahlung
 * - on_refund: Erstattung
 * - on_chargeback: Rückbuchung
 * - on_rebill_resumed: Abo fortgesetzt
 * - on_rebill_cancelled: Abo gekündigt
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/digistore.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Logging für Debugging
function logIPN($message, $data = null) {
    $logFile = __DIR__ . '/../../logs/digistore24_ipn.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] $message";
    
    if ($data !== null) {
        $entry .= " | Data: " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    file_put_contents($logFile, $entry . PHP_EOL, FILE_APPEND);
}

// Antwort senden
function respond($status, $message = '') {
    http_response_code($status === 'OK' ? 200 : 400);
    header('Content-Type: text/plain');
    echo $status;
    if ($message) {
        logIPN("Response: $status - $message");
    }
    exit;
}

// IPN-Daten validieren
function validateIPN($data) {
    global $digistore_config;
    
    // Pflichtfelder prüfen
    $requiredFields = ['api_mode', 'event'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            return false;
        }
    }
    
    // API-Mode prüfen (live oder sandbox)
    if (!in_array($data['api_mode'], ['live', 'sandbox'])) {
        return false;
    }
    
    // Bei Sandbox nur wenn erlaubt
    if ($data['api_mode'] === 'sandbox' && !$digistore_config['sandbox_mode']) {
        logIPN("Sandbox IPN rejected - sandbox mode disabled");
        return false;
    }
    
    // SHA-Signatur prüfen (wenn vorhanden)
    if (!empty($data['sha_sign'])) {
        $secret = $digistore_config['ipn_passphrase'];
        
        // Signatur-String erstellen (alle Parameter außer sha_sign, alphabetisch sortiert)
        $signData = $data;
        unset($signData['sha_sign']);
        ksort($signData);
        
        $signString = '';
        foreach ($signData as $key => $value) {
            if ($value !== '') {
                $signString .= $key . '=' . $value . $secret;
            }
        }
        
        $calculatedSign = strtoupper(sha1($signString));
        
        if ($calculatedSign !== strtoupper($data['sha_sign'])) {
            logIPN("SHA signature mismatch", [
                'received' => $data['sha_sign'],
                'calculated' => $calculatedSign
            ]);
            return false;
        }
    }
    
    return true;
}

// Haupt-IPN-Verarbeitung
try {
    // POST-Daten empfangen
    $rawData = file_get_contents('php://input');
    parse_str($rawData, $ipnData);
    
    // Alternativ: $_POST verwenden wenn parse_str nicht funktioniert
    if (empty($ipnData)) {
        $ipnData = $_POST;
    }
    
    logIPN("IPN received", $ipnData);
    
    // Validierung
    if (!validateIPN($ipnData)) {
        logIPN("IPN validation failed");
        respond('ERROR', 'Validation failed');
    }
    
    $db = Database::getInstance();
    $event = $ipnData['event'] ?? '';
    
    // Event verarbeiten
    switch ($event) {
        
        case 'connection_test':
            // Verbindungstest - einfach bestätigen
            logIPN("Connection test successful");
            respond('OK');
            break;
            
        case 'on_payment':
            // Erfolgreiche Zahlung
            handlePayment($db, $ipnData);
            break;
            
        case 'on_payment_missed':
            // Fehlgeschlagene Zahlung
            handlePaymentMissed($db, $ipnData);
            break;
            
        case 'on_refund':
            // Erstattung
            handleRefund($db, $ipnData);
            break;
            
        case 'on_chargeback':
            // Rückbuchung
            handleChargeback($db, $ipnData);
            break;
            
        case 'on_rebill_resumed':
            // Abo fortgesetzt
            handleRebillResumed($db, $ipnData);
            break;
            
        case 'on_rebill_cancelled':
            // Abo gekündigt
            handleRebillCancelled($db, $ipnData);
            break;
            
        default:
            logIPN("Unknown event: $event");
            respond('OK'); // Unbekannte Events akzeptieren
    }
    
} catch (Exception $e) {
    logIPN("Exception: " . $e->getMessage());
    respond('ERROR', $e->getMessage());
}

/**
 * Erfolgreiche Zahlung verarbeiten
 */
function handlePayment($db, $data) {
    global $digistore_config;
    
    $orderId = $data['order_id'] ?? '';
    $productId = $data['product_id'] ?? '';
    $email = $data['email'] ?? '';
    $firstName = $data['address_first_name'] ?? '';
    $lastName = $data['address_last_name'] ?? '';
    $amount = floatval($data['billing_amount'] ?? 0);
    $isRecurring = ($data['is_rebilling'] ?? '0') === '1';
    $transactionId = $data['transaction_id'] ?? '';
    
    logIPN("Processing payment", [
        'order_id' => $orderId,
        'product_id' => $productId,
        'email' => $email,
        'amount' => $amount,
        'is_recurring' => $isRecurring
    ]);
    
    // Produkt-ID prüfen und Plan ermitteln
    $plan = null;
    
    // Einrichtungsgebühr + Starter
    if (in_array($productId, $digistore_config['product_ids']['starter_setup'])) {
        $plan = 'starter';
        $isSetup = true;
    }
    // Einrichtungsgebühr + Professional
    elseif (in_array($productId, $digistore_config['product_ids']['professional_setup'])) {
        $plan = 'professional';
        $isSetup = true;
    }
    // Monatliche Zahlung Starter
    elseif (in_array($productId, $digistore_config['product_ids']['starter_monthly'])) {
        $plan = 'starter';
        $isSetup = false;
    }
    // Monatliche Zahlung Professional
    elseif (in_array($productId, $digistore_config['product_ids']['professional_monthly'])) {
        $plan = 'professional';
        $isSetup = false;
    }
    else {
        logIPN("Unknown product ID: $productId");
        respond('OK'); // Unbekannte Produkte ignorieren
        return;
    }
    
    // Zahlung in DB speichern
    $db->insert('payments', [
        'order_id' => $orderId,
        'transaction_id' => $transactionId,
        'product_id' => $productId,
        'email' => $email,
        'amount' => $amount,
        'currency' => 'EUR',
        'payment_type' => $isSetup ? 'setup' : ($isRecurring ? 'recurring' : 'initial'),
        'plan' => $plan,
        'status' => 'completed',
        'raw_data' => json_encode($data),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Prüfen ob Kunde bereits existiert
    $customer = $db->fetch(
        "SELECT * FROM customers WHERE email = ? OR digistore_order_id = ?",
        [$email, $orderId]
    );
    
    if ($customer) {
        // Bestehender Kunde - Subscription aktualisieren
        $db->update('customers', [
            'subscription_status' => 'active',
            'plan' => $plan,
            'digistore_order_id' => $orderId,
            'subscription_ends_at' => date('Y-m-d H:i:s', strtotime('+35 days')), // 35 Tage Puffer
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customer['id']]);
        
        logIPN("Updated existing customer", ['customer_id' => $customer['id']]);
        
    } else {
        // Neuer Kunde - Onboarding-Token erstellen
        $onboardingToken = generateToken(64);
        
        // Temporären Eintrag erstellen (wird durch Onboarding vervollständigt)
        $customerId = $db->insert('customers', [
            'email' => $email,
            'password_hash' => '', // Wird im Onboarding gesetzt
            'company_name' => '', // Wird im Onboarding gesetzt
            'industry' => 'allgemein',
            'subdomain' => '', // Wird im Onboarding gesetzt
            'contact_name' => trim($firstName . ' ' . $lastName),
            'address_street' => '',
            'address_zip' => '',
            'address_city' => '',
            'plan' => $plan,
            'subscription_status' => 'trial', // Wird nach Onboarding auf 'active' gesetzt
            'digistore_order_id' => $orderId,
            'onboarding_token' => $onboardingToken,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        logIPN("Created new customer", ['customer_id' => $customerId]);
        
        // Willkommens-E-Mail mit Onboarding-Link senden
        sendOnboardingEmail($email, $firstName, $onboardingToken, $plan);
    }
    
    respond('OK');
}

/**
 * Fehlgeschlagene Zahlung verarbeiten
 */
function handlePaymentMissed($db, $data) {
    $orderId = $data['order_id'] ?? '';
    $email = $data['email'] ?? '';
    
    logIPN("Payment missed", ['order_id' => $orderId, 'email' => $email]);
    
    // Zahlung loggen
    $db->insert('payments', [
        'order_id' => $orderId,
        'email' => $email,
        'status' => 'failed',
        'payment_type' => 'failed',
        'raw_data' => json_encode($data),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Kunde finden und Status aktualisieren
    $customer = $db->fetch(
        "SELECT * FROM customers WHERE digistore_order_id = ?",
        [$orderId]
    );
    
    if ($customer) {
        $db->update('customers', [
            'subscription_status' => 'paused',
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customer['id']]);
        
        // TODO: E-Mail an Kunden senden
    }
    
    respond('OK');
}

/**
 * Erstattung verarbeiten
 */
function handleRefund($db, $data) {
    $orderId = $data['order_id'] ?? '';
    $amount = floatval($data['billing_amount'] ?? 0);
    
    logIPN("Refund processed", ['order_id' => $orderId, 'amount' => $amount]);
    
    // Zahlung loggen
    $db->insert('payments', [
        'order_id' => $orderId,
        'amount' => -$amount, // Negativ für Erstattung
        'status' => 'refunded',
        'payment_type' => 'refund',
        'raw_data' => json_encode($data),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Kunde finden und ggf. deaktivieren
    $customer = $db->fetch(
        "SELECT * FROM customers WHERE digistore_order_id = ?",
        [$orderId]
    );
    
    if ($customer) {
        // Bei vollständiger Erstattung: Konto pausieren
        $db->update('customers', [
            'subscription_status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customer['id']]);
    }
    
    respond('OK');
}

/**
 * Rückbuchung verarbeiten
 */
function handleChargeback($db, $data) {
    $orderId = $data['order_id'] ?? '';
    
    logIPN("Chargeback received", ['order_id' => $orderId]);
    
    // Zahlung loggen
    $db->insert('payments', [
        'order_id' => $orderId,
        'status' => 'chargeback',
        'payment_type' => 'chargeback',
        'raw_data' => json_encode($data),
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    // Kunde sofort deaktivieren
    $customer = $db->fetch(
        "SELECT * FROM customers WHERE digistore_order_id = ?",
        [$orderId]
    );
    
    if ($customer) {
        $db->update('customers', [
            'subscription_status' => 'cancelled',
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customer['id']]);
        
        // TODO: Admin benachrichtigen
    }
    
    respond('OK');
}

/**
 * Abo fortgesetzt
 */
function handleRebillResumed($db, $data) {
    $orderId = $data['order_id'] ?? '';
    
    logIPN("Rebill resumed", ['order_id' => $orderId]);
    
    $customer = $db->fetch(
        "SELECT * FROM customers WHERE digistore_order_id = ?",
        [$orderId]
    );
    
    if ($customer) {
        $db->update('customers', [
            'subscription_status' => 'active',
            'subscription_ends_at' => date('Y-m-d H:i:s', strtotime('+35 days')),
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customer['id']]);
    }
    
    respond('OK');
}

/**
 * Abo gekündigt
 */
function handleRebillCancelled($db, $data) {
    $orderId = $data['order_id'] ?? '';
    $cancellationDate = $data['cancellation_date'] ?? '';
    
    logIPN("Rebill cancelled", ['order_id' => $orderId, 'cancellation_date' => $cancellationDate]);
    
    $customer = $db->fetch(
        "SELECT * FROM customers WHERE digistore_order_id = ?",
        [$orderId]
    );
    
    if ($customer) {
        // Abo endet zum nächsten Abrechnungszeitraum
        $endsAt = $cancellationDate ?: date('Y-m-d H:i:s', strtotime('+30 days'));
        
        $db->update('customers', [
            'subscription_status' => 'cancelled',
            'subscription_ends_at' => $endsAt,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customer['id']]);
        
        // TODO: Kündigungs-E-Mail senden
    }
    
    respond('OK');
}

/**
 * Onboarding-E-Mail senden
 */
function sendOnboardingEmail($email, $firstName, $token, $plan) {
    // TODO: Mailgun-Integration
    $onboardingUrl = 'https://empfohlen.de/onboarding?token=' . $token;
    
    logIPN("Onboarding email queued", [
        'email' => $email,
        'url' => $onboardingUrl
    ]);
    
    // Temporär: E-Mail in Queue speichern
    $db = Database::getInstance();
    $db->insert('email_queue', [
        'recipient_email' => $email,
        'recipient_name' => $firstName,
        'subject' => 'Willkommen bei Leadbusiness – Jetzt einrichten!',
        'template' => 'onboarding_welcome',
        'variables' => json_encode([
            'first_name' => $firstName,
            'onboarding_url' => $onboardingUrl,
            'plan' => $plan
        ]),
        'priority' => 10,
        'status' => 'pending',
        'created_at' => date('Y-m-d H:i:s')
    ]);
}
