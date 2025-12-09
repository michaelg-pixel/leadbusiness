<?php
/**
 * Allgemeine Einstellungen
 * 
 * Plattform-Konfiguration und Plan-Limits.
 */

// Globale Variable setzen für einfachen Zugriff
$settings = [
    // Plattform
    'site_name' => 'Leadbusiness',
    'site_url' => getenv('SITE_URL') ?: 'https://empfehlungen.cloud',
    'support_email' => 'support@empfehlungen.cloud',
    'timezone' => 'Europe/Berlin',
    'locale' => 'de_DE',
    
    // Subdomain-System
    'subdomain' => [
        'base_domain' => getenv('BASE_DOMAIN') ?: 'empfehlungen.cloud',
        'min_length' => 3,
        'max_length' => 50,
        'reserved' => ['www', 'api', 'admin', 'dashboard', 'app', 'mail', 'smtp', 'ftp', 'cdn', 'static', 'assets', 'img', 'images', 'js', 'css', 'test', 'dev', 'staging', 'demo']
    ],
    
    // Plan-Limits
    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'price_monthly' => 49,
            'price_setup' => 499,
            'max_leads' => 200,
            'max_rewards' => 3,
            'max_campaigns' => 1,
            'features' => [
                'weekly_digest' => false,
                'broadcast_emails' => false,
                'custom_background' => false,
                'lead_export' => false,
                'webhooks' => false,
                'embed_widget' => false,
                'remove_branding' => false,
                'custom_domain' => false,
                'priority_support' => false,
                'badges_limit' => 5
            ]
        ],
        'professional' => [
            'name' => 'Professional',
            'price_monthly' => 99,
            'price_setup' => 499,
            'max_leads' => 5000,
            'max_rewards' => 5,
            'max_campaigns' => 999,
            'features' => [
                'weekly_digest' => true,
                'broadcast_emails' => true,
                'custom_background' => true,
                'lead_export' => true,
                'webhooks' => true,
                'embed_widget' => true,
                'remove_branding' => true,
                'custom_domain' => true,
                'priority_support' => true,
                'badges_limit' => 9
            ]
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price_monthly' => null,    // Auf Anfrage
            'price_setup' => null,
            'max_leads' => 999999,
            'max_rewards' => 10,
            'max_campaigns' => 999,
            'features' => [
                'weekly_digest' => true,
                'broadcast_emails' => true,
                'custom_background' => true,
                'lead_export' => true,
                'webhooks' => true,
                'embed_widget' => true,
                'remove_branding' => true,
                'custom_domain' => true,
                'priority_support' => true,
                'badges_limit' => 9,
                'dedicated_support' => true
            ]
        ]
    ],
    
    // Referral-Code
    'referral_code' => [
        'length' => 8,
        'charset' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',  // Ohne 0, O, I, 1
        'prefix' => ''
    ],
    
    // Token-Gültigkeiten
    'tokens' => [
        'confirmation' => 86400,        // 24 Stunden
        'password_reset' => 3600,       // 1 Stunde
        'download_link' => 86400        // 24 Stunden
    ],
    
    // Uploads
    'uploads' => [
        'logo' => [
            'max_size' => 2 * 1024 * 1024,  // 2 MB
            'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            'max_width' => 1000,
            'max_height' => 1000
        ],
        'background' => [
            'max_size' => 5 * 1024 * 1024,  // 5 MB
            'allowed_types' => ['image/jpeg', 'image/png', 'image/webp'],
            'target_width' => 1920,
            'target_height' => 1080
        ],
        'download' => [
            'max_size' => 50 * 1024 * 1024, // 50 MB
            'allowed_types' => ['application/pdf', 'application/zip', 'image/jpeg', 'image/png', 'video/mp4']
        ]
    ],
    
    // Gamification
    'gamification' => [
        'points' => [
            'per_conversion' => 10,
            'per_week_streak' => 5,
            'per_badge' => 0    // Badge-spezifisch
        ],
        'streak' => [
            'days_for_week' => 7,
            'min_shares_per_week' => 1
        ],
        'leaderboard' => [
            'top_count' => 10,
            'update_interval' => 900    // 15 Minuten
        ]
    ],
    
    // Branchen mit Icons
    'industries' => [
        'zahnarzt' => [
            'name' => 'Zahnarzt / Zahnklinik',
            'icon' => 'fas fa-tooth'
        ],
        'friseur' => [
            'name' => 'Friseur / Beauty-Salon',
            'icon' => 'fas fa-scissors'
        ],
        'handwerker' => [
            'name' => 'Handwerker',
            'icon' => 'fas fa-hammer'
        ],
        'coach' => [
            'name' => 'Coach / Berater',
            'icon' => 'fas fa-user-tie'
        ],
        'restaurant' => [
            'name' => 'Restaurant / Gastronomie',
            'icon' => 'fas fa-utensils'
        ],
        'fitness' => [
            'name' => 'Fitnessstudio / Sportstudio',
            'icon' => 'fas fa-dumbbell'
        ],
        'onlineshop' => [
            'name' => 'Online-Shop',
            'icon' => 'fas fa-shopping-cart'
        ],
        'onlinemarketing' => [
            'name' => 'Online-Marketing / Kursanbieter',
            'icon' => 'fas fa-laptop'
        ],
        'newsletter' => [
            'name' => 'Newsletter / Content Creator',
            'icon' => 'fas fa-envelope-open-text'
        ],
        'software' => [
            'name' => 'Software / SaaS',
            'icon' => 'fas fa-code'
        ],
        'allgemein' => [
            'name' => 'Sonstige Branche',
            'icon' => 'fas fa-briefcase'
        ]
    ],
    
    // Share-Plattformen
    'share_platforms' => [
        'whatsapp' => ['name' => 'WhatsApp', 'icon' => 'fab fa-whatsapp', 'color' => '#25D366'],
        'facebook' => ['name' => 'Facebook', 'icon' => 'fab fa-facebook', 'color' => '#1877F2'],
        'telegram' => ['name' => 'Telegram', 'icon' => 'fab fa-telegram', 'color' => '#0088CC'],
        'email' => ['name' => 'E-Mail', 'icon' => 'fas fa-envelope', 'color' => '#EA4335'],
        'sms' => ['name' => 'SMS', 'icon' => 'fas fa-comment-sms', 'color' => '#34B7F1'],
        'linkedin' => ['name' => 'LinkedIn', 'icon' => 'fab fa-linkedin', 'color' => '#0A66C2'],
        'xing' => ['name' => 'Xing', 'icon' => 'fab fa-xing', 'color' => '#006567'],
        'twitter' => ['name' => 'X (Twitter)', 'icon' => 'fab fa-x-twitter', 'color' => '#000000'],
        'pinterest' => ['name' => 'Pinterest', 'icon' => 'fab fa-pinterest', 'color' => '#BD081C'],
        'copy_link' => ['name' => 'Link kopieren', 'icon' => 'fas fa-copy', 'color' => '#6B7280'],
        'qr_code' => ['name' => 'QR-Code', 'icon' => 'fas fa-qrcode', 'color' => '#374151']
    ],
    
    // Debug-Modus
    'debug' => (bool)(getenv('APP_DEBUG') ?: false),
    
    // Wartungsmodus
    'maintenance_mode' => (bool)(getenv('MAINTENANCE_MODE') ?: false)
];

// Auch als Return für alternative Verwendung
return $settings;
