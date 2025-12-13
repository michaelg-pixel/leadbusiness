<?php
/**
 * Leadbusiness - Onboarding Wizard
 * 
 * 9-Schritt Wizard für neue Kunden (Schritt 6 optional für bestimmte Branchen)
 * Mit Dark/Light Mode Toggle und E-Mail-Tool-Integration
 */

// KORRIGIERTE PFADE - von /public/onboarding/ zwei Ebenen hoch
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Session starten
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Token generieren falls nicht vorhanden
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Theme aus Cookie
$theme = $_COOKIE['onboarding_theme'] ?? 'light';

// Database mit korrektem Namespace
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

// Bereits eingeloggter Kunde?
$customerId = $_SESSION['onboarding_customer_id'] ?? null;

// Branchen aus Config laden
global $settings;
$industries = $settings['industries'] ?? [];

// Hintergrundbilder laden
$backgrounds = $db->fetchAll(
    "SELECT * FROM background_images WHERE is_active = 1 ORDER BY industry, sort_order"
);

// Gruppen nach Branche
$backgroundsByIndustry = [];
foreach ($backgrounds as $bg) {
    $backgroundsByIndustry[$bg['industry']][] = $bg;
}

$pageTitle = 'Empfehlungsprogramm einrichten';

// Plan-Check für Belohnungstypen
$isProfessional = ($plan === 'professional');

// Branchen die den E-Mail-Tool-Schritt sehen
$emailToolBranches = ['onlinemarketing', 'coach', 'onlineshop', 'newsletter', 'software'];
?>
<!DOCTYPE html>
<html lang="de" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Leadbusiness</title>
    
    <!-- Tailwind CSS -->
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
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
        
        .step-item.completed .step-circle i {
            display: block;
        }
        
        .step-item.completed .step-number {
            display: none;
        }
        
        .step-item.hidden {
            display: none !important;
        }
        
        .wizard-panel {
            display: none;
        }
        
        .wizard-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
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
        
        .industry-card.selected .check-icon {
            display: flex !important;
        }
        
        .background-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .background-card:hover {
            transform: scale(1.02);
        }
        
        .reward-level {
            transition: all 0.2s ease;
        }
        
        .reward-level:hover {
            background: #f8fafc;
        }
        
        .dark .reward-level:hover {
            background: rgba(51, 65, 85, 0.5);
        }
        
        .extra-field {
            display: none;
        }
        
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
        
        .tool-card {
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .tool-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .tool-card.selected {
            border-color: #667eea !important;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
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
                
                <!-- Progress Steps (9 Schritte, Schritt 6 optional) -->
                <div class="mb-6 sm:mb-8 overflow-x-auto">
                    <div class="flex items-center justify-between relative min-w-max sm:min-w-0 px-2">
                        <div class="absolute top-4 sm:top-5 left-0 right-0 h-0.5 bg-gray-200 dark:bg-slate-700">
                            <div id="progressBar" class="h-full bg-primary-500 transition-all duration-300" style="width: 0%"></div>
                        </div>
                        
                        <?php 
                        $labels = ['Branche', 'Firma', 'Kontakt', 'Impressum', 'Belohnungen', 'E-Mail', 'Design', 'Subdomain', 'Fertig!'];
                        for ($i = 1; $i <= 9; $i++): 
                        ?>
                        <div class="step-item <?= $i === 1 ? 'active' : '' ?> <?= $i === 6 ? 'hidden' : '' ?> flex flex-col items-center relative z-10" data-step="<?= $i ?>">
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
                <form id="onboardingForm" action="/onboarding/process.php" method="POST" enctype="multipart/form-data">
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
                    
                    <!-- Step 2: Firmendaten -->
                    <div class="wizard-panel" data-panel="2">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Erzählen Sie uns von Ihrem Unternehmen</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Diese Informationen erscheinen auf Ihrer Empfehlungsseite.</p>
                            
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Firmenname *</label>
                                    <input type="text" name="company_name" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="z.B. Zahnarztpraxis Dr. Müller">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Logo hochladen (optional)</label>
                                    <div class="border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-xl p-6 sm:p-8 text-center hover:border-primary-500 transition-colors cursor-pointer bg-gray-50 dark:bg-slate-700/50" id="logoDropzone">
                                        <input type="file" name="logo" id="logoInput" accept="image/*" class="hidden">
                                        <div id="logoPreview" class="hidden mb-4"><img src="" alt="Logo Preview" class="max-h-24 mx-auto"></div>
                                        <div id="logoPlaceholder">
                                            <i class="fas fa-cloud-upload-alt text-3xl sm:text-4xl text-gray-400 dark:text-slate-500 mb-3"></i>
                                            <p class="text-gray-500 dark:text-slate-400 text-sm sm:text-base">Klicken oder Bild hierher ziehen</p>
                                            <p class="text-gray-400 dark:text-slate-500 text-xs sm:text-sm mt-1">PNG, JPG bis 2MB</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Website (optional)</label>
                                    <input type="url" name="website" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://www.ihre-website.de">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Kontaktdaten -->
                    <div class="wizard-panel" data-panel="3">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Wie können wir Sie erreichen?</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Diese Daten werden nicht öffentlich angezeigt.</p>
                            
                            <div class="space-y-6">
                                <div class="grid sm:grid-cols-2 gap-4 sm:gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Ansprechpartner *</label>
                                        <input type="text" name="contact_name" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="Max Mustermann" value="<?= htmlspecialchars($customer['contact_name'] ?? '') ?>">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">E-Mail-Adresse *</label>
                                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="max@firma.de" value="<?= htmlspecialchars($customer['email'] ?? '') ?>" <?= $customer ? 'readonly' : '' ?>>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Telefon (optional)</label>
                                    <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="+49 123 456789">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Passwort erstellen *</label>
                                    <input type="password" name="password" required minlength="8" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="Mindestens 8 Zeichen">
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400 mt-1">Min. 8 Zeichen, mit Groß-/Kleinbuchstaben und Zahlen</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Passwort bestätigen *</label>
                                    <input type="password" name="password_confirm" required class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="Passwort wiederholen">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 4: Impressum -->
                    <div class="wizard-panel" data-panel="4">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Impressumsangaben</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Gesetzlich vorgeschriebene Angaben für Ihre Empfehlungsseite.</p>
                            
                            <div class="space-y-6">
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
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">USt-IdNr. (optional)</label>
                                    <input type="text" name="tax_id" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="DE123456789">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 5: Belohnungen - ERWEITERT mit URL-Feldern -->
                    <div class="wizard-panel" data-panel="5">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Belohnungen für Empfehler</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Definieren Sie, was Ihre Empfehler für erfolgreiche Empfehlungen bekommen.</p>
                            
                            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-lightbulb text-blue-500 dark:text-blue-400 mt-1"></i>
                                    <div class="text-sm text-blue-800 dark:text-blue-300">
                                        <p><strong>Tipp:</strong> Je nach Belohnungstyp erscheinen automatisch die passenden Eingabefelder.</p>
                                        <p class="mt-1 text-xs">Die URL wird in der Belohnungs-E-Mail an Ihre Empfehler als "Jetzt einlösen"-Button angezeigt.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="rewardsContainer" class="space-y-6">
                                <?php for ($i = 1; $i <= 3; $i++): 
                                    $defaults = [1 => [3, 'discount'], 2 => [5, 'coupon_code'], 3 => [10, 'free_product']];
                                ?>
                                <div class="reward-level border border-gray-200 dark:border-slate-600 rounded-xl p-4 sm:p-6 bg-white dark:bg-slate-800" data-reward-level="<?= $i ?>">
                                    <div class="flex items-center gap-3 sm:gap-4 mb-4">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold"><?= $i ?></div>
                                        <div>
                                            <h3 class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">Stufe <?= $i ?></h3>
                                            <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400">
                                                Nach 
                                                <input type="number" name="reward_<?= $i ?>_threshold" value="<?= $defaults[$i][0] ?>" min="1" max="100" class="w-12 sm:w-16 px-2 py-1 border border-gray-300 dark:border-slate-600 rounded text-center text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white"> 
                                                erfolgreichen Empfehlungen
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Hauptfelder: Typ und Beschreibung -->
                                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Belohnungstyp</label>
                                            <select name="reward_<?= $i ?>_type" class="reward-type-select w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" data-level="<?= $i ?>">
                                                <option value="discount" <?= $defaults[$i][1] === 'discount' ? 'selected' : '' ?>>💰 Rabatt (%)</option>
                                                <option value="coupon_code" <?= $defaults[$i][1] === 'coupon_code' ? 'selected' : '' ?>>🎟️ Gutschein-Code</option>
                                                <option value="free_product" <?= $defaults[$i][1] === 'free_product' ? 'selected' : '' ?>>🎁 Gratis-Produkt</option>
                                                <option value="free_service">⭐ Gratis-Service</option>
                                                <option value="voucher">💶 Wertgutschein (€)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Beschreibung *</label>
                                            <input type="text" name="reward_<?= $i ?>_description" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="z.B. 10% Rabatt auf Ihre nächste Bestellung">
                                        </div>
                                    </div>
                                    
                                    <!-- Dynamische Extra-Felder Container -->
                                    <div class="reward-extra-fields space-y-4 mt-4" id="reward_<?= $i ?>_extra_fields">
                                        
                                        <!-- Rabatt (%) - Extra Feld -->
                                        <div class="extra-field <?= $defaults[$i][1] === 'discount' ? 'active' : '' ?>" data-type="discount">
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-percent text-primary-500 mr-1"></i>Rabatt in %
                                                    </label>
                                                    <input type="number" name="reward_<?= $i ?>_discount_percent" min="1" max="100" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. 10">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_discount_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://shop.de/rabatt">
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">
                                                <i class="fas fa-info-circle mr-1"></i>Diese URL wird in der Belohnungs-E-Mail als "Jetzt einlösen"-Button verlinkt.
                                            </p>
                                        </div>
                                        
                                        <!-- Gutschein-Code - Extra Felder -->
                                        <div class="extra-field <?= $defaults[$i][1] === 'coupon_code' ? 'active' : '' ?>" data-type="coupon_code">
                                            <div class="grid sm:grid-cols-3 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-ticket text-purple-500 mr-1"></i>Gutschein-Code
                                                    </label>
                                                    <input type="text" name="reward_<?= $i ?>_coupon_code" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono" placeholder="RABATT10">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-calendar text-purple-500 mr-1"></i>Gültig für (Tage)
                                                    </label>
                                                    <input type="number" name="reward_<?= $i ?>_coupon_validity" value="30" min="1" max="365" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_coupon_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://shop.de/checkout">
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">
                                                <i class="fas fa-info-circle mr-1"></i>Der Code und die URL werden in der Belohnungs-E-Mail angezeigt.
                                            </p>
                                        </div>
                                        
                                        <!-- Gratis-Produkt - Extra Felder -->
                                        <div class="extra-field <?= $defaults[$i][1] === 'free_product' ? 'active' : '' ?>" data-type="free_product">
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Produkt-/Bestell-URL
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_product_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://shop.de/gratis-produkt">
                                                </div>
                                                <div class="flex items-center gap-2 pt-6">
                                                    <input type="checkbox" name="reward_<?= $i ?>_requires_address" id="reward_<?= $i ?>_requires_address" class="w-4 h-4 text-primary-500 rounded border-gray-300 dark:border-slate-600 focus:ring-primary-500 bg-white dark:bg-slate-700">
                                                    <label for="reward_<?= $i ?>_requires_address" class="text-xs sm:text-sm text-gray-700 dark:text-slate-300">
                                                        Lieferadresse erforderlich
                                                    </label>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">
                                                <i class="fas fa-info-circle mr-1"></i>Die URL wird als "Produkt ansehen"-Button in der E-Mail angezeigt.
                                            </p>
                                        </div>
                                        
                                        <!-- Gratis-Service - Extra Felder -->
                                        <div class="extra-field" data-type="free_service">
                                            <div>
                                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                    <i class="fas fa-link text-primary-500 mr-1"></i>Buchungs-/Termin-URL
                                                </label>
                                                <input type="url" name="reward_<?= $i ?>_service_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://calendly.com/ihre-firma">
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">
                                                <i class="fas fa-info-circle mr-1"></i>Die URL wird als "Termin buchen"-Button in der E-Mail angezeigt.
                                            </p>
                                        </div>
                                        
                                        <!-- Wertgutschein (€) - Extra Felder -->
                                        <div class="extra-field" data-type="voucher">
                                            <div class="grid sm:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-euro-sign text-green-500 mr-1"></i>Gutscheinwert in €
                                                    </label>
                                                    <input type="number" name="reward_<?= $i ?>_voucher_amount" min="1" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. 25">
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_voucher_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://shop.de/gutschein">
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-2">
                                                <i class="fas fa-info-circle mr-1"></i>Der Betrag und die URL werden in der Belohnungs-E-Mail angezeigt.
                                            </p>
                                        </div>
                                        
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 6: E-Mail-Tool Integration (NEUER SCHRITT - Optional für bestimmte Branchen) -->
                    <div class="wizard-panel" data-panel="6">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">
                                <i class="fas fa-envelope text-primary-500 mr-2"></i>
                                E-Mail-Tool verbinden
                            </h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 text-sm sm:text-base">
                                Verbinden Sie Ihr E-Mail-Marketing-Tool, damit Ihre Empfehler automatisch in Ihrer Liste landen.
                            </p>
                            
                            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-info-circle text-blue-500 dark:text-blue-400 mt-1"></i>
                                    <div class="text-sm text-blue-800 dark:text-blue-300">
                                        <p><strong>Optional:</strong> Sie können diesen Schritt überspringen und später im Dashboard einrichten.</p>
                                        <p class="mt-1 text-xs">Die Empfehler werden passiv in Ihr Tool übertragen – Marketing-E-Mails (Belohnungen, Reminder) werden weiterhin von uns versendet.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tool-Auswahl Container (wird von JS befüllt) -->
                            <div id="emailToolContainer">
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6" id="emailToolGrid">
                                    <!-- KlickTipp -->
                                    <div class="tool-card border-2 border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center" data-tool="klicktipp">
                                        <div class="w-12 h-12 mx-auto bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center mb-3">
                                            <i class="fas fa-paper-plane text-orange-500 text-xl"></i>
                                        </div>
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm">KlickTipp</div>
                                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">Beliebt in DE</div>
                                    </div>
                                    
                                    <!-- Quentn -->
                                    <div class="tool-card border-2 border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center" data-tool="quentn">
                                        <div class="w-12 h-12 mx-auto bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-3">
                                            <i class="fas fa-bolt text-blue-500 text-xl"></i>
                                        </div>
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm">Quentn</div>
                                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">DSGVO-konform</div>
                                    </div>
                                    
                                    <!-- CleverReach -->
                                    <div class="tool-card border-2 border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center" data-tool="cleverreach">
                                        <div class="w-12 h-12 mx-auto bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-3">
                                            <i class="fas fa-envelope-open-text text-green-500 text-xl"></i>
                                        </div>
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm">CleverReach</div>
                                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">Made in DE</div>
                                    </div>
                                    
                                    <!-- Überspringen -->
                                    <div class="tool-card border-2 border-gray-200 dark:border-slate-600 rounded-xl p-4 text-center" data-tool="skip">
                                        <div class="w-12 h-12 mx-auto bg-gray-100 dark:bg-slate-700 rounded-xl flex items-center justify-center mb-3">
                                            <i class="fas fa-forward text-gray-400 dark:text-slate-500 text-xl"></i>
                                        </div>
                                        <div class="font-semibold text-gray-900 dark:text-white text-sm">Überspringen</div>
                                        <div class="text-xs text-gray-500 dark:text-slate-400 mt-1">Später einrichten</div>
                                    </div>
                                </div>
                                
                                <!-- Credentials Form (wird dynamisch angezeigt) -->
                                <div id="emailToolCredentials" class="hidden">
                                    <div class="border-t border-gray-200 dark:border-slate-700 pt-6 mt-6">
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-4" id="toolCredentialsTitle">API-Zugangsdaten</h3>
                                        
                                        <div class="space-y-4" id="credentialsFields">
                                            <!-- Felder werden dynamisch eingefügt -->
                                        </div>
                                        
                                        <button type="button" id="testConnectionBtn" class="mt-4 px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors text-sm">
                                            <i class="fas fa-plug mr-2"></i>Verbindung testen
                                        </button>
                                        
                                        <div id="connectionStatus" class="mt-4 hidden"></div>
                                        
                                        <!-- Tag-Auswahl (nach erfolgreicher Verbindung) -->
                                        <div id="tagSelection" class="hidden mt-6 pt-6 border-t border-gray-200 dark:border-slate-700">
                                            <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                                                Tag für neue Empfehler (optional)
                                            </label>
                                            <select name="email_tool_tag_id" id="tagSelect" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                <option value="">-- Kein Tag --</option>
                                            </select>
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Dieser Tag wird allen neuen Empfehlern zugewiesen</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Hidden fields für E-Mail-Tool-Daten -->
                            <input type="hidden" name="email_tool" id="selectedEmailTool" value="">
                            <input type="hidden" name="email_tool_api_key" id="emailToolApiKey" value="">
                            <input type="hidden" name="email_tool_api_secret" id="emailToolApiSecret" value="">
                            <input type="hidden" name="email_tool_api_url" id="emailToolApiUrl" value="">
                            <input type="hidden" name="email_tool_tag_name" id="emailToolTagName" value="">
                        </div>
                    </div>
                    
                    <!-- Step 7: Design (war Step 6) -->
                    <div class="wizard-panel" data-panel="7">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Design wählen</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Wählen Sie ein Hintergrundbild für Ihre Empfehlungsseite.</p>
                            
                            <div id="backgroundsContainer" class="grid grid-cols-2 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                                <p class="col-span-3 text-center text-gray-500 dark:text-slate-400 py-4">Hintergrundbilder werden geladen...</p>
                            </div>
                            
                            <input type="hidden" name="background_image_id" id="selectedBackground" value="">
                            
                            <div class="mt-6">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">Hauptfarbe</label>
                                <div class="flex items-center gap-4">
                                    <input type="color" name="primary_color" value="#667eea" class="w-12 h-10 sm:w-16 rounded cursor-pointer">
                                    <span class="text-gray-500 dark:text-slate-400 text-xs sm:text-sm">Diese Farbe wird für Buttons und Akzente verwendet</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 8: Subdomain (war Step 7) -->
                    <div class="wizard-panel" data-panel="8">
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
                    
                    <!-- Step 9: Fertig (war Step 8) -->
                    <div class="wizard-panel" data-panel="9">
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
        // PHP-Variablen an JS übergeben
        const backgroundsByIndustry = <?= json_encode($backgroundsByIndustry) ?>;
        const isProfessional = <?= $isProfessional ? 'true' : 'false' ?>;
        const emailToolBranches = <?= json_encode($emailToolBranches) ?>;
        
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
        
        // ================================
        // REWARD TYPE EXTRA FIELDS HANDLER
        // ================================
        document.addEventListener('DOMContentLoaded', function() {
            // Event Listener für alle Belohnungstyp-Selects
            document.querySelectorAll('.reward-type-select').forEach(function(select) {
                select.addEventListener('change', function() {
                    const level = this.dataset.level;
                    const type = this.value;
                    updateExtraFields(level, type);
                });
            });
            
            // Initial für alle vorhandenen Reward-Levels die Extra-Felder setzen
            document.querySelectorAll('.reward-type-select').forEach(function(select) {
                const level = select.dataset.level;
                const type = select.value;
                updateExtraFields(level, type);
            });
        });
        
        function updateExtraFields(level, type) {
            const container = document.getElementById('reward_' + level + '_extra_fields');
            if (!container) return;
            
            // Alle Extra-Felder verstecken
            container.querySelectorAll('.extra-field').forEach(function(field) {
                field.classList.remove('active');
            });
            
            // Das passende Extra-Feld anzeigen
            const activeField = container.querySelector('.extra-field[data-type="' + type + '"]');
            if (activeField) {
                activeField.classList.add('active');
            }
        }
    </script>
    
    <!-- E-Mail-Tool Integration Script -->
    <script src="/assets/js/email-integration.js"></script>
    
    <!-- Onboarding Wizard Script -->
    <script src="/assets/js/onboarding.js"></script>
    
</body>
</html>