<?php
/**
 * Security-Konfiguration
 * 
 * Anti-Spam, Rate Limiting, Bot-Schutz, Fraud Detection.
 */

return [
    // Rate Limiting
    'rate_limits' => [
        'lead_registration' => ['max' => 5, 'window' => 3600],      // 5 pro Stunde
        'click_tracking' => ['max' => 50, 'window' => 3600],        // 50 pro Stunde
        'api_requests' => ['max' => 100, 'window' => 60],           // 100 pro Minute
        'login_attempts' => ['max' => 5, 'window' => 900],          // 5 pro 15 Min
        'share_actions' => ['max' => 20, 'window' => 3600],         // 20 pro Stunde
        'password_reset' => ['max' => 3, 'window' => 3600],         // 3 pro Stunde
    ],
    
    // Bot-Erkennung
    'bot_detection' => [
        'enabled' => true,
        'honeypot_field' => 'website',
        'min_form_time' => 3,           // Sekunden
        'suspicious_user_agents' => ['bot', 'spider', 'crawl', 'curl', 'wget', 'headless', 'phantom'],
        'score_threshold' => 50,         // Ab diesem Score ist es ein Bot
    ],
    
    // Fraud Detection
    'fraud_detection' => [
        'enabled' => true,
        'thresholds' => [
            'block' => 80,
            'review' => 50,
            'flag' => 30
        ],
        'checks' => [
            'fast_conversion' => ['enabled' => true, 'score' => 40, 'min_seconds' => 5],
            'same_ip' => ['enabled' => true, 'score' => 50],
            'same_subnet' => ['enabled' => true, 'score' => 30],
            'ip_abuse' => ['enabled' => true, 'score' => 40, 'max_per_day' => 10],
            'self_referral' => ['enabled' => true, 'score' => 80],
            'suspicious_email' => ['enabled' => true, 'score' => 35],
            'same_fingerprint' => ['enabled' => true, 'score' => 60],
            'referrer_limit' => ['enabled' => true, 'score' => 30, 'max_per_day' => 20],
            'vpn_detected' => ['enabled' => true, 'score' => 25],
            'disposable_email' => ['enabled' => true, 'score' => 70],
        ]
    ],
    
    // hCaptcha
    'hcaptcha' => [
        'enabled' => (bool)(getenv('HCAPTCHA_ENABLED') ?: false),
        'site_key' => getenv('HCAPTCHA_SITE_KEY') ?: '',
        'secret_key' => getenv('HCAPTCHA_SECRET_KEY') ?: '',
        'show_after_attempts' => 3,
        'show_on_high_risk' => true,
        'bot_score_threshold' => 30
    ],
    
    // IP-Blocking
    'ip_blocking' => [
        'enabled' => true,
        'auto_block_after_rate_limit_hits' => 5,
        'block_durations' => [
            1 => 3600,      // 1. Block: 1 Stunde
            2 => 86400,     // 2. Block: 24 Stunden
            3 => 604800,    // 3. Block: 7 Tage
            4 => null       // Ab 4. Block: permanent
        ]
    ],
    
    // Session Security
    'session' => [
        'lifetime' => 86400,            // 24 Stunden
        'regenerate_after' => 3600,     // Session-ID nach 1 Stunde erneuern
        'cookie_secure' => true,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax'
    ],
    
    // Password Policy
    'password' => [
        'min_length' => 8,
        'require_uppercase' => false,
        'require_lowercase' => false,
        'require_numbers' => false,
        'require_special' => false,
        'hash_algo' => PASSWORD_BCRYPT,
        'hash_cost' => 12
    ],
    
    // CSRF Protection
    'csrf' => [
        'enabled' => true,
        'token_lifetime' => 7200,       // 2 Stunden
        'field_name' => '_csrf_token',
        'header_name' => 'X-CSRF-Token'
    ]
];
