<?php
/**
 * Mailgun-Konfiguration
 * 
 * E-Mail-Versand über Mailgun EU (DSGVO-konform).
 */

return [
    'api_key' => getenv('MAILGUN_API_KEY') ?: '',
    'domain' => getenv('MAILGUN_DOMAIN') ?: '',
    
    // EU-Endpoint für DSGVO-Konformität
    'endpoint' => 'https://api.eu.mailgun.net',
    
    // Standard-Absender
    'from_email' => getenv('MAILGUN_FROM_EMAIL') ?: 'noreply@empfohlen.de',
    'from_name' => getenv('MAILGUN_FROM_NAME') ?: 'Empfehlungsprogramm',
    
    // Reply-To (falls nicht überschrieben)
    'reply_to' => getenv('MAILGUN_REPLY_TO') ?: 'support@empfohlen.de',
    
    // Tracking-Optionen
    'tracking' => [
        'clicks' => true,
        'opens' => true
    ],
    
    // Tags für Mailgun-Analytics
    'default_tags' => ['leadbusiness'],
    
    // Testmodus (E-Mails werden nur geloggt, nicht versendet)
    'test_mode' => (bool)(getenv('MAILGUN_TEST_MODE') ?: false)
];
