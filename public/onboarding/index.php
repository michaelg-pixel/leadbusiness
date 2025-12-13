<?php
/**
 * Leadbusiness - Onboarding Wizard (Verschlankte Version)
 * 
 * 5-Schritt Wizard für neue Kunden
 * 1. Branche
 * 2. Ihre Daten (Firma + Kontakt + Adresse)
 * 3. Belohnungen
 * 4. Subdomain
 * 5. Fertig
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$theme = $_COOKIE['onboarding_theme'] ?? 'light';
$db = \Leadbusiness\Database::getInstance();

// Token aus URL prüfen (von Digistore24 IPN)
$token = $_GET['token'] ?? '';
$customer = null;
$plan = $_GET['plan'] ?? 'starter';

if ($token) {
    $customer = $db->fetch(
        "SELECT * FROM customers WHERE onboarding_token = ?",
        [$token]
    );
    
    if ($customer) {
        $plan = $customer['plan'];
        $_SESSION['onboarding_customer_id'] = $customer['id'];
        $_SESSION['onboarding_token'] = $token;
    }
}

$customerId = $_SESSION['onboarding_customer_id'] ?? null;

// Branchen aus Config laden
global $settings;
$industries = $settings['industries'] ?? [];

$pageTitle = 'Empfehlungsprogramm einrichten';
$isProfessional = ($plan === 'professional' || $plan === 'enterprise');
$isEnterprise = ($plan === 'enterprise');

$planLimits = [
    'starter' => 3,
    'professional' => 5,
    'enterprise' => 10
];
$maxRewardLevels = $planLimits[$plan] ?? 3;

$defaultRewards = [
    1 => ['threshold' => 3, 'type' => 'discount'],
    2 => ['threshold' => 5, 'type' => 'coupon_code'],
    3 => ['threshold' => 10, 'type' => 'free_product'],
    4 => ['threshold' => 15, 'type' => 'voucher'],
    5 => ['threshold' => 20, 'type' => 'free_service'],
];

$initialLevels = 3;
?>
<!DOCTYPE html>
<html lang="de" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Leadbusiness</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f4ff',
                            100: '#e0e9ff',
                            200: '#c7d6fe',
                            300: '#a4bbfc',
                            400: '#8098f9',
                            500: '#667eea',
                            600: '#5a67d8',
                            700: '#4c51bf',
                            800: '#434190',
                            900: '#3c366b',
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .step-item.active .step-circle {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .step-item.completed .step-circle {
            background: #10b981;
            color: white;
        }
        
        .step-item.completed .step-circle i { display: block; }
        .step-item.completed .step-number { display: none; }
        
        .wizard-panel { display: none; }
        .wizard-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .industry-card {
            transition: all 0.2s ease;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        
        .industry-card:hover,
        .industry-card:active {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .industry-card.selected {
            border-color: #667eea !important;
            background: linear-gradient(to bottom, #f0f4ff, white);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
        }
        
        .dark .industry-card.selected {
            background: linear-gradient(to bottom, rgba(102, 126, 234, 0.2), rgba(30, 41, 59, 1));
        }
        
        .industry-card.selected .check-icon { display: flex !important; }
        
        .reward-level {
            transition: all 0.2s ease;
            animation: slideIn 0.3s ease;
        }
        
        .reward-level:hover { background: #f8fafc; }
        .dark .reward-level:hover { background: rgba(51, 65, 85, 0.5); }
        
        .extra-field { display: none; }
        .extra-field.active {
            display: block;
            animation: fadeIn 0.2s ease;
        }
        
        .pro-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.125rem 0.5rem;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            font-size: 0.625rem;
            font-weight: 600;
            border-radius: 9999px;
            margin-left: 0.5rem;
        }
        
        @media (max-width: 640px) {
            .industry-card { padding: 0.75rem; }
            .industry-card .w-12 { width: 2.5rem; height: 2.5rem; }
            .step-circle { width: 2rem !important; height: 2rem !important; font-size: 0.75rem; }
        }
    </style>
</head>
<body class="bg-gray-50 dark:bg-slate-900 transition-colors duration-300">
    
    <div class="min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 transition-colors duration-300">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-share-nodes text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Leadbusiness</span>
                </a>
                <div class="flex items-center gap-4">
                    <button onclick="toggleTheme()" 
                            class="p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 transition-all"
                            title="Design wechseln">
                        <i class="fas fa-moon dark:hidden w-5 h-5"></i>
                        <i class="fas fa-sun hidden dark:inline w-5 h-5"></i>
                    </button>
                    <span class="text-sm text-gray-500 dark:text-slate-400 hidden sm:inline">
                        <i class="fas fa-lock mr-1"></i>Sichere Verbindung
                    </span>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="flex-1 py-4 sm:py-8">
            <div class="max-w-4xl mx-auto px-4">
                
                <!-- Progress Steps (5 Schritte) -->
                <div class="mb-6 sm:mb-8 overflow-x-auto">
                    <div class="flex items-center justify-between relative min-w-max sm:min-w-0 px-2">
                        <div class="absolute top-4 sm:top-5 left-0 right-0 h-0.5 bg-gray-200 dark:bg-slate-700">
                            <div id="progressBar" class="h-full bg-primary-500 transition-all duration-300" style="width: 0%"></div>
                        </div>
                        
                        <?php 
                        $labels = ['Branche', 'Ihre Daten', 'Belohnungen', 'Subdomain', 'Fertig!'];
                        for ($i = 1; $i <= 5; $i++): 
                        ?>
                        <div class="step-item <?= $i === 1 ? 'active' : '' ?> flex flex-col items-center relative z-10" data-step="<?= $i ?>">
                            <div class="step-circle w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-200 dark:bg-slate-700 flex items-center justify-center text-xs sm:text-sm font-semibold text-gray-600 dark:text-slate-300">
                                <span class="step-number"><?= $i ?></span>
                                <i class="fas fa-check hidden"></i>
                            </div>
                            <span class="text-xs mt-2 text-gray-500 dark:text-slate-400 hidden sm:block"><?= $labels[$i-1] ?></span>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <!-- Wizard Form -->
                <form id="onboardingForm" action="/onboarding/process.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="onboarding_token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="plan" value="<?= htmlspecialchars($plan) ?>">
                    
                    <!-- Step 1: Branche -->
                    <div class="wizard-panel active" data-panel="1">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">In welcher Branche sind Sie tätig?</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Wir passen Ihr Empfehlungsprogramm an Ihre Branche an.</p>
                            
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4" id="industryGrid">
                                <?php foreach ($industries as $key => $industry): ?>
                                <div class="industry-card bg-white dark:bg-slate-800 border-2 border-gray-200 dark:border-slate-600 rounded-xl p-3 sm:p-4 relative" data-industry="<?= htmlspecialchars($key) ?>">
                                    <input type="radio" name="industry" value="<?= htmlspecialchars($key) ?>" class="sr-only industry-radio" required>
                                    <div class="check-icon hidden absolute top-2 right-2 w-5 h-5 sm:w-6 sm:h-6 bg-primary-500 rounded-full items-center justify-center text-white">
                                        <i class="fas fa-check text-xs"></i>
                                    </div>
                                    <div class="text-center">
                                        <div class="w-10 h-10 sm:w-12 sm:h-12 mx-auto bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center text-primary-500 dark:text-primary-400 mb-2 sm:mb-3">
                                            <i class="<?= htmlspecialchars($industry['icon']) ?> text-lg sm:text-xl"></i>
                                        </div>
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base"><?= htmlspecialchars($industry['name']) ?></div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 2: Ihre Daten (Firma + Kontakt + Adresse kombiniert) -->
                    <div class="wizard-panel" data-panel="2">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Ihre Daten</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Diese Informationen werden für Ihr Impressum und Ihren Account benötigt.</p>
                            
                            <div class="space-y-6">
                                <!-- Firma & Kontakt -->
                                <div class="grid sm:grid-cols-2 gap-4 sm:gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Firmenname *</label>
                                        <input type="text" name="company_name" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="z.B. Zahnarztpraxis Dr. Müller">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Ansprechpartner *</label>
                                        <input type="text" name="contact_name" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="Max Mustermann" value="<?= htmlspecialchars($customer['contact_name'] ?? '') ?>">
                                    </div>
                                </div>
                                
                                <!-- E-Mail & Passwort -->
                                <div class="grid sm:grid-cols-2 gap-4 sm:gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">E-Mail-Adresse *</label>
                                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="max@firma.de" value="<?= htmlspecialchars($customer['email'] ?? '') ?>" <?= $customer ? 'readonly' : '' ?>>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Passwort *</label>
                                        <input type="password" name="password" required minlength="8" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="Min. 8 Zeichen">
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Passwort bestätigen *</label>
                                    <input type="password" name="password_confirm" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="Passwort wiederholen">
                                </div>
                                
                                <!-- Trennlinie -->
                                <div class="border-t border-gray-200 dark:border-slate-700 pt-6">
                                    <h3 class="text-sm font-semibold text-gray-700 dark:text-slate-300 mb-4 flex items-center">
                                        <i class="fas fa-building text-primary-500 mr-2"></i>
                                        Impressumsangaben <span class="text-xs font-normal text-gray-500 dark:text-slate-400 ml-2">(gesetzlich erforderlich)</span>
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Straße und Hausnummer *</label>
                                            <input type="text" name="address_street" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="Musterstraße 123">
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4 sm:gap-6">
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">PLZ *</label>
                                                <input type="text" name="address_zip" required pattern="[0-9]{5}" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="12345">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Stadt *</label>
                                                <input type="text" name="address_city" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="Berlin">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Hinweis -->
                                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                                    <div class="flex items-start gap-3">
                                        <i class="fas fa-info-circle text-blue-500 dark:text-blue-400 mt-0.5"></i>
                                        <p class="text-sm text-blue-800 dark:text-blue-300">
                                            Logo, Website und weitere Angaben können Sie später im Dashboard hinzufügen.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Belohnungen -->
                    <div class="wizard-panel" data-panel="3">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Belohnungen für Empfehler</h2>
                                <?php if ($isProfessional): ?>
                                <span class="pro-badge">
                                    <i class="fas fa-crown text-xs"></i>
                                    <?= $isEnterprise ? 'Enterprise' : 'Professional' ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-500 dark:text-slate-400 mb-4 text-sm sm:text-base">Was bekommen Ihre Empfehler für erfolgreiche Empfehlungen?</p>
                            
                            <!-- Plan-Info Box -->
                            <div class="bg-gradient-to-r <?= $isProfessional ? 'from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 border-amber-200 dark:border-amber-800' : 'from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 border-blue-200 dark:border-blue-800' ?> border rounded-xl p-4 mb-6">
                                <div class="flex items-start gap-3">
                                    <i class="fas <?= $isProfessional ? 'fa-crown text-amber-500' : 'fa-lightbulb text-blue-500' ?> mt-1"></i>
                                    <div class="text-sm <?= $isProfessional ? 'text-amber-800 dark:text-amber-300' : 'text-blue-800 dark:text-blue-300' ?>">
                                        <p><strong>Ihr <?= $isProfessional ? ($isEnterprise ? 'Enterprise' : 'Professional') : 'Starter' ?>-Tarif:</strong> Bis zu <strong><?= $maxRewardLevels ?> Belohnungsstufen</strong> möglich.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Rewards Container -->
                            <div id="rewardsContainer" class="space-y-6">
                                <?php for ($i = 1; $i <= $initialLevels; $i++): 
                                    $defaults = $defaultRewards[$i];
                                ?>
                                <div class="reward-level border border-gray-200 dark:border-slate-600 rounded-xl p-4 sm:p-6 bg-white dark:bg-slate-800" data-reward-level="<?= $i ?>">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center gap-3 sm:gap-4">
                                            <div class="reward-level-number w-8 h-8 sm:w-10 sm:h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold"><?= $i ?></div>
                                            <div>
                                                <h3 class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">Stufe <span class="reward-level-label"><?= $i ?></span></h3>
                                                <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400">
                                                    Nach 
                                                    <input type="number" name="reward_<?= $i ?>_threshold" value="<?= $defaults['threshold'] ?>" min="1" max="100" class="w-12 sm:w-16 px-2 py-1 border border-gray-300 dark:border-slate-600 rounded text-center text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white"> 
                                                    Empfehlungen
                                                </p>
                                            </div>
                                        </div>
                                        <?php if ($i > 1): ?>
                                        <button type="button" class="remove-reward-btn text-gray-400 hover:text-red-500 dark:text-slate-500 dark:hover:text-red-400 p-2 transition-colors" title="Stufe entfernen">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Belohnungstyp</label>
                                            <select name="reward_<?= $i ?>_type" class="reward-type-select w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" data-level="<?= $i ?>">
                                                <option value="discount" <?= $defaults['type'] === 'discount' ? 'selected' : '' ?>>💰 Rabatt (%)</option>
                                                <option value="coupon_code" <?= $defaults['type'] === 'coupon_code' ? 'selected' : '' ?>>🎟️ Gutschein-Code</option>
                                                <option value="free_product" <?= $defaults['type'] === 'free_product' ? 'selected' : '' ?>>🎁 Gratis-Produkt</option>
                                                <option value="free_service" <?= $defaults['type'] === 'free_service' ? 'selected' : '' ?>>⭐ Gratis-Service</option>
                                                <option value="voucher" <?= $defaults['type'] === 'voucher' ? 'selected' : '' ?>>💶 Wertgutschein (€)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Beschreibung *</label>
                                            <input type="text" name="reward_<?= $i ?>_description" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="z.B. 10% Rabatt auf nächste Bestellung">
                                        </div>
                                    </div>
                                    
                                    <!-- Extra-Felder Container -->
                                    <div class="reward-extra-fields space-y-4 mt-4" id="reward_<?= $i ?>_extra_fields">
                                        
                                        <!-- Rabatt -->
                                        <div class="extra-field <?= $defaults['type'] === 'discount' ? 'active' : '' ?>" data-type="discount">
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-percent text-primary-500 mr-1"></i>Rabatt in %
                                                    </label>
                                                    <input type="number" name="reward_<?= $i ?>_discount_percent" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. 10">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_discount_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://shop.de/rabatt">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gutschein-Code -->
                                        <div class="extra-field <?= $defaults['type'] === 'coupon_code' ? 'active' : '' ?>" data-type="coupon_code">
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-ticket text-purple-500 mr-1"></i>Gutschein-Code
                                                    </label>
                                                    <input type="text" name="reward_<?= $i ?>_coupon_code" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono" placeholder="RABATT10">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_coupon_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://shop.de/checkout">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gratis-Produkt -->
                                        <div class="extra-field <?= $defaults['type'] === 'free_product' ? 'active' : '' ?>" data-type="free_product">
                                            <div>
                                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                    <i class="fas fa-link text-primary-500 mr-1"></i>Produkt-URL (optional)
                                                </label>
                                                <input type="url" name="reward_<?= $i ?>_product_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://shop.de/produkt">
                                            </div>
                                        </div>
                                        
                                        <!-- Gratis-Service -->
                                        <div class="extra-field <?= $defaults['type'] === 'free_service' ? 'active' : '' ?>" data-type="free_service">
                                            <div>
                                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                    <i class="fas fa-link text-primary-500 mr-1"></i>Buchungs-URL (optional)
                                                </label>
                                                <input type="url" name="reward_<?= $i ?>_service_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://calendly.com/firma">
                                            </div>
                                        </div>
                                        
                                        <!-- Wertgutschein -->
                                        <div class="extra-field <?= $defaults['type'] === 'voucher' ? 'active' : '' ?>" data-type="voucher">
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-euro-sign text-green-500 mr-1"></i>Gutscheinwert in €
                                                    </label>
                                                    <input type="number" name="reward_<?= $i ?>_voucher_amount" min="1" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. 25">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_voucher_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://shop.de/gutschein">
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                            
                            <?php if ($isProfessional): ?>
                            <div id="addRewardContainer" class="mt-6">
                                <button type="button" id="addRewardBtn" class="w-full py-4 border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-xl text-gray-500 dark:text-slate-400 hover:border-primary-500 hover:text-primary-500 dark:hover:border-primary-400 dark:hover:text-primary-400 transition-all flex items-center justify-center gap-2">
                                    <i class="fas fa-plus"></i>
                                    <span>Weitere Stufe hinzufügen</span>
                                    <span class="text-xs opacity-70">(<span id="rewardCountDisplay"><?= $initialLevels ?></span>/<?= $maxRewardLevels ?>)</span>
                                </button>
                            </div>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                    
                    <!-- Step 4: Subdomain -->
                    <div class="wizard-panel" data-panel="4">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Ihre Subdomain wählen</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Unter dieser Adresse ist Ihr Empfehlungsprogramm erreichbar.</p>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Subdomain *</label>
                                    <div class="flex flex-col sm:flex-row">
                                        <input type="text" name="subdomain" id="subdomainInput" required pattern="[a-z0-9-]+" class="flex-1 px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl sm:rounded-r-none focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="ihre-firma">
                                        <span class="bg-gray-100 dark:bg-slate-600 px-4 py-3 border border-gray-300 dark:border-slate-600 sm:border-l-0 rounded-xl sm:rounded-l-none text-gray-500 dark:text-slate-300 text-center sm:text-left mt-2 sm:mt-0">.empfehlungen.cloud</span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400 mt-2">Nur Kleinbuchstaben, Zahlen und Bindestriche erlaubt</p>
                                    <div id="subdomainStatus" class="mt-2 hidden"><span class="text-sm"></span></div>
                                </div>
                                
                                <div class="bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4">
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-slate-300">
                                        <i class="fas fa-info-circle text-primary-500 mr-2"></i>
                                        Ihre Empfehlungsseite wird unter <strong id="previewUrl">ihre-firma.empfehlungen.cloud</strong> erreichbar sein.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 5: Fertig -->
                    <div class="wizard-panel" data-panel="5">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 text-center border border-gray-200 dark:border-slate-700">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6">
                                <i class="fas fa-check text-3xl sm:text-4xl text-green-500"></i>
                            </div>
                            
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Fast geschafft!</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Prüfen Sie Ihre Angaben und klicken Sie auf "Einrichtung starten".</p>
                            
                            <div id="summaryContainer" class="text-left bg-gray-50 dark:bg-slate-700/50 rounded-xl p-4 sm:p-6 mb-6 sm:mb-8"></div>
                            
                            <div class="flex items-start gap-3 text-left mb-6">
                                <input type="checkbox" name="accept_terms" id="acceptTerms" required class="mt-1 w-5 h-5 text-primary-500 rounded border-gray-300 dark:border-slate-600 focus:ring-primary-500 bg-white dark:bg-slate-700">
                                <label for="acceptTerms" class="text-xs sm:text-sm text-gray-600 dark:text-slate-300">
                                    Ich akzeptiere die <a href="/agb" target="_blank" class="text-primary-500 hover:underline">AGB</a> 
                                    und habe die <a href="/datenschutz" target="_blank" class="text-primary-500 hover:underline">Datenschutzerklärung</a> gelesen.
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Navigation Buttons -->
                    <div class="flex justify-between mt-6 sm:mt-8">
                        <button type="button" id="prevBtn" class="px-4 sm:px-6 py-3 border border-gray-300 dark:border-slate-600 rounded-xl text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-700 hidden text-sm sm:text-base transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i><span class="hidden sm:inline">Zurück</span>
                        </button>
                        
                        <button type="button" id="nextBtn" class="ml-auto px-6 sm:px-8 py-3 bg-gradient-to-r from-primary-500 to-purple-600 text-white rounded-xl hover:shadow-lg transition-shadow text-sm sm:text-base">
                            Weiter <i class="fas fa-arrow-right ml-2"></i>
                        </button>
                        
                        <button type="submit" id="submitBtn" class="ml-auto px-6 sm:px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-shadow hidden text-sm sm:text-base">
                            <i class="fas fa-rocket mr-2"></i><span class="hidden sm:inline">Einrichtung </span>starten
                        </button>
                    </div>
                </form>
                
            </div>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700 py-4 sm:py-6">
            <div class="max-w-6xl mx-auto px-4 text-center text-xs sm:text-sm text-gray-500 dark:text-slate-400">
                <p>&copy; <?= date('Y') ?> Leadbusiness. Alle Rechte vorbehalten.</p>
                <div class="mt-2 space-x-4">
                    <a href="/impressum" class="hover:text-gray-700 dark:hover:text-slate-300">Impressum</a>
                    <a href="/datenschutz" class="hover:text-gray-700 dark:hover:text-slate-300">Datenschutz</a>
                    <a href="/agb" class="hover:text-gray-700 dark:hover:text-slate-300">AGB</a>
                </div>
            </div>
        </footer>
        
    </div>
    
    <script>
        const maxRewardLevels = <?= $maxRewardLevels ?>;
        const defaultRewards = <?= json_encode($defaultRewards) ?>;
        const totalSteps = 5;
        let currentStep = 1;
        let currentRewardCount = <?= $initialLevels ?>;
        
        // Theme Toggle
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            if (isDark) {
                html.classList.remove('dark');
                document.cookie = 'onboarding_theme=light;path=/;max-age=31536000';
            } else {
                html.classList.add('dark');
                document.cookie = 'onboarding_theme=dark;path=/;max-age=31536000';
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('onboardingForm');
            const nextBtn = document.getElementById('nextBtn');
            const prevBtn = document.getElementById('prevBtn');
            const submitBtn = document.getElementById('submitBtn');
            const progressBar = document.getElementById('progressBar');
            
            // Industry selection
            document.querySelectorAll('.industry-card').forEach(card => {
                card.addEventListener('click', function() {
                    document.querySelectorAll('.industry-card').forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');
                    this.querySelector('input[type="radio"]').checked = true;
                });
            });
            
            // Reward type change handler
            initRewardTypeSelects();
            
            // Add reward button
            const addRewardBtn = document.getElementById('addRewardBtn');
            if (addRewardBtn) {
                addRewardBtn.addEventListener('click', addNewRewardLevel);
            }
            
            // Remove reward button delegation
            document.getElementById('rewardsContainer')?.addEventListener('click', function(e) {
                if (e.target.closest('.remove-reward-btn')) {
                    const rewardLevel = e.target.closest('.reward-level');
                    if (rewardLevel) removeRewardLevel(rewardLevel);
                }
            });
            
            // Subdomain input
            const subdomainInput = document.getElementById('subdomainInput');
            if (subdomainInput) {
                subdomainInput.addEventListener('input', function() {
                    this.value = this.value.toLowerCase().replace(/[^a-z0-9-]/g, '');
                    document.getElementById('previewUrl').textContent = (this.value || 'ihre-firma') + '.empfehlungen.cloud';
                });
            }
            
            // Navigation
            nextBtn.addEventListener('click', function() {
                if (validateStep(currentStep)) {
                    goToStep(currentStep + 1);
                }
            });
            
            prevBtn.addEventListener('click', function() {
                goToStep(currentStep - 1);
            });
            
            // Form submit
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Wird eingerichtet...';
                
                const formData = new FormData(form);
                
                fetch('/onboarding/process.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.data.redirect;
                    } else {
                        alert(data.message || 'Ein Fehler ist aufgetreten.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-rocket mr-2"></i><span class="hidden sm:inline">Einrichtung </span>starten';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-rocket mr-2"></i><span class="hidden sm:inline">Einrichtung </span>starten';
                });
            });
            
            function goToStep(step) {
                if (step < 1 || step > totalSteps) return;
                
                // Update step indicators
                document.querySelectorAll('.step-item').forEach((item, index) => {
                    const stepNum = index + 1;
                    item.classList.remove('active', 'completed');
                    if (stepNum < step) item.classList.add('completed');
                    if (stepNum === step) item.classList.add('active');
                });
                
                // Update panels
                document.querySelectorAll('.wizard-panel').forEach(panel => {
                    panel.classList.remove('active');
                });
                document.querySelector(`[data-panel="${step}"]`).classList.add('active');
                
                // Update progress bar
                progressBar.style.width = ((step - 1) / (totalSteps - 1) * 100) + '%';
                
                // Update buttons
                prevBtn.classList.toggle('hidden', step === 1);
                nextBtn.classList.toggle('hidden', step === totalSteps);
                submitBtn.classList.toggle('hidden', step !== totalSteps);
                
                // Update summary on last step
                if (step === totalSteps) {
                    updateSummary();
                }
                
                currentStep = step;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
            
            function validateStep(step) {
                const panel = document.querySelector(`[data-panel="${step}"]`);
                const requiredFields = panel.querySelectorAll('[required]');
                let valid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        field.classList.add('border-red-500');
                        valid = false;
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });
                
                // Step 1: Industry check
                if (step === 1) {
                    const industrySelected = document.querySelector('input[name="industry"]:checked');
                    if (!industrySelected) {
                        alert('Bitte wählen Sie Ihre Branche aus.');
                        valid = false;
                    }
                }
                
                // Step 2: Password check
                if (step === 2) {
                    const password = panel.querySelector('input[name="password"]').value;
                    const passwordConfirm = panel.querySelector('input[name="password_confirm"]').value;
                    
                    if (password.length < 8) {
                        alert('Das Passwort muss mindestens 8 Zeichen haben.');
                        valid = false;
                    } else if (password !== passwordConfirm) {
                        alert('Die Passwörter stimmen nicht überein.');
                        valid = false;
                    }
                }
                
                return valid;
            }
            
            function updateSummary() {
                const industry = document.querySelector('input[name="industry"]:checked');
                const industryCard = industry?.closest('.industry-card');
                const industryName = industryCard?.querySelector('.font-semibold')?.textContent || '-';
                
                const companyName = document.querySelector('input[name="company_name"]')?.value || '-';
                const contactName = document.querySelector('input[name="contact_name"]')?.value || '-';
                const email = document.querySelector('input[name="email"]')?.value || '-';
                const subdomain = document.querySelector('input[name="subdomain"]')?.value || '-';
                const street = document.querySelector('input[name="address_street"]')?.value || '';
                const zip = document.querySelector('input[name="address_zip"]')?.value || '';
                const city = document.querySelector('input[name="address_city"]')?.value || '';
                
                // Count rewards
                const rewardLevels = document.querySelectorAll('.reward-level');
                const rewardCount = rewardLevels.length;
                
                document.getElementById('summaryContainer').innerHTML = `
                    <div class="grid sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">Branche:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">${industryName}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">Firma:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">${companyName}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">Ansprechpartner:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">${contactName}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">E-Mail:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">${email}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">Adresse:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">${street}, ${zip} ${city}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">Subdomain:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">${subdomain}.empfehlungen.cloud</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-slate-400">Belohnungsstufen:</span>
                            <span class="font-medium text-gray-900 dark:text-white ml-2">${rewardCount}</span>
                        </div>
                    </div>
                `;
            }
        });
        
        // Reward Functions
        function initRewardTypeSelects() {
            document.querySelectorAll('.reward-type-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    updateExtraFields(this.dataset.level, this.value);
                });
                updateExtraFields(select.dataset.level, select.value);
            });
        }
        
        function updateExtraFields(level, type) {
            const container = document.getElementById('reward_' + level + '_extra_fields');
            if (!container) return;
            
            container.querySelectorAll('.extra-field').forEach(field => {
                field.classList.remove('active');
            });
            
            const activeField = container.querySelector('.extra-field[data-type="' + type + '"]');
            if (activeField) activeField.classList.add('active');
        }
        
        function addNewRewardLevel() {
            if (currentRewardCount >= maxRewardLevels) {
                alert('Maximale Anzahl erreicht.');
                return;
            }
            
            currentRewardCount++;
            const newLevel = currentRewardCount;
            const defaults = defaultRewards[newLevel] || { threshold: newLevel * 5, type: 'discount' };
            
            const container = document.getElementById('rewardsContainer');
            container.insertAdjacentHTML('beforeend', createRewardLevelHTML(newLevel, defaults));
            
            const newSelect = container.querySelector('.reward-level[data-reward-level="' + newLevel + '"] .reward-type-select');
            if (newSelect) {
                newSelect.addEventListener('change', function() {
                    updateExtraFields(newLevel, this.value);
                });
                updateExtraFields(newLevel, newSelect.value);
            }
            
            updateRewardCountDisplay();
            updateAddButtonVisibility();
        }
        
        function removeRewardLevel(element) {
            const level = parseInt(element.dataset.rewardLevel);
            if (level === 1) return;
            
            element.remove();
            currentRewardCount--;
            renumberRewardLevels();
            updateRewardCountDisplay();
            updateAddButtonVisibility();
        }
        
        function renumberRewardLevels() {
            const levels = document.querySelectorAll('#rewardsContainer .reward-level');
            levels.forEach((level, index) => {
                const newNumber = index + 1;
                level.dataset.rewardLevel = newNumber;
                
                const numberBadge = level.querySelector('.reward-level-number');
                if (numberBadge) numberBadge.textContent = newNumber;
                
                const label = level.querySelector('.reward-level-label');
                if (label) label.textContent = newNumber;
                
                level.querySelectorAll('input, select').forEach(input => {
                    if (input.name) input.name = input.name.replace(/reward_\d+_/, 'reward_' + newNumber + '_');
                    if (input.id) input.id = input.id.replace(/reward_\d+_/, 'reward_' + newNumber + '_');
                });
                
                const extraFields = level.querySelector('.reward-extra-fields');
                if (extraFields) extraFields.id = 'reward_' + newNumber + '_extra_fields';
                
                const select = level.querySelector('.reward-type-select');
                if (select) select.dataset.level = newNumber;
            });
        }
        
        function updateRewardCountDisplay() {
            const display = document.getElementById('rewardCountDisplay');
            if (display) display.textContent = currentRewardCount;
        }
        
        function updateAddButtonVisibility() {
            const container = document.getElementById('addRewardContainer');
            if (container) {
                container.style.display = currentRewardCount >= maxRewardLevels ? 'none' : 'block';
            }
        }
        
        function createRewardLevelHTML(level, defaults) {
            return `
            <div class="reward-level border border-gray-200 dark:border-slate-600 rounded-xl p-4 sm:p-6 bg-white dark:bg-slate-800" data-reward-level="${level}">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="reward-level-number w-8 h-8 sm:w-10 sm:h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold">${level}</div>
                        <div>
                            <h3 class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">Stufe <span class="reward-level-label">${level}</span></h3>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400">
                                Nach <input type="number" name="reward_${level}_threshold" value="${defaults.threshold}" min="1" max="100" class="w-12 sm:w-16 px-2 py-1 border border-gray-300 dark:border-slate-600 rounded text-center text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white"> Empfehlungen
                            </p>
                        </div>
                    </div>
                    <button type="button" class="remove-reward-btn text-gray-400 hover:text-red-500 dark:text-slate-500 dark:hover:text-red-400 p-2 transition-colors" title="Stufe entfernen">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="grid sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Belohnungstyp</label>
                        <select name="reward_${level}_type" class="reward-type-select w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" data-level="${level}">
                            <option value="discount">💰 Rabatt (%)</option>
                            <option value="coupon_code">🎟️ Gutschein-Code</option>
                            <option value="free_product">🎁 Gratis-Produkt</option>
                            <option value="free_service">⭐ Gratis-Service</option>
                            <option value="voucher">💶 Wertgutschein (€)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Beschreibung *</label>
                        <input type="text" name="reward_${level}_description" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="z.B. 10% Rabatt auf nächste Bestellung">
                    </div>
                </div>
                
                <div class="reward-extra-fields space-y-4 mt-4" id="reward_${level}_extra_fields">
                    <div class="extra-field active" data-type="discount">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><i class="fas fa-percent text-primary-500 mr-1"></i>Rabatt in %</label>
                                <input type="number" name="reward_${level}_discount_percent" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. 10">
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)</label>
                                <input type="url" name="reward_${level}_discount_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://shop.de/rabatt">
                            </div>
                        </div>
                    </div>
                    <div class="extra-field" data-type="coupon_code">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><i class="fas fa-ticket text-purple-500 mr-1"></i>Gutschein-Code</label>
                                <input type="text" name="reward_${level}_coupon_code" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono" placeholder="RABATT10">
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)</label>
                                <input type="url" name="reward_${level}_coupon_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://shop.de/checkout">
                            </div>
                        </div>
                    </div>
                    <div class="extra-field" data-type="free_product">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><i class="fas fa-link text-primary-500 mr-1"></i>Produkt-URL (optional)</label>
                            <input type="url" name="reward_${level}_product_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://shop.de/produkt">
                        </div>
                    </div>
                    <div class="extra-field" data-type="free_service">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><i class="fas fa-link text-primary-500 mr-1"></i>Buchungs-URL (optional)</label>
                            <input type="url" name="reward_${level}_service_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://calendly.com/firma">
                        </div>
                    </div>
                    <div class="extra-field" data-type="voucher">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><i class="fas fa-euro-sign text-green-500 mr-1"></i>Gutscheinwert in €</label>
                                <input type="number" name="reward_${level}_voucher_amount" min="1" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. 25">
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1"><i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)</label>
                                <input type="url" name="reward_${level}_voucher_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://shop.de/gutschein">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;
        }
    </script>
    
</body>
</html>
