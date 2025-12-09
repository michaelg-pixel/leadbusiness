<?php
/**
 * Digistore24-Konfiguration
 * 
 * Integration für Zahlungen und Abonnements.
 */

return [
    'vendor_id' => getenv('DIGISTORE_VENDOR_ID') ?: '',
    'api_key' => getenv('DIGISTORE_API_KEY') ?: '',
    'ipn_passphrase' => getenv('DIGISTORE_IPN_PASSPHRASE') ?: '',
    
    // API-Endpoint
    'api_endpoint' => 'https://www.digistore24.com/api/call',
    
    // Produkt-IDs
    'products' => [
        'setup' => getenv('DIGISTORE_PRODUCT_SETUP') ?: '',
        'starter_monthly' => getenv('DIGISTORE_PRODUCT_STARTER') ?: '',
        'professional_monthly' => getenv('DIGISTORE_PRODUCT_PRO') ?: ''
    ],
    
    // IPN-Webhook URL
    'ipn_url' => '/api/webhooks/digistore.php',
    
    // Erlaubte IPN-IPs von Digistore24
    'allowed_ips' => [
        '185.156.179.226',
        '185.156.179.227',
        '185.156.179.228',
        '185.156.179.229',
        '185.156.179.230'
    ],
    
    // Testmodus (für Sandbox-Testing)
    'sandbox_mode' => (bool)(getenv('DIGISTORE_SANDBOX') ?: false)
];
