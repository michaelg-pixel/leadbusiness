<?php
/**
 * Leadbusiness - Onboarding Wizard
 * 
 * 8-Schritt Wizard für neue Kunden
 * Mit Dark/Light Mode Toggle
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
        
        .reward-type-group {
            border-top: 1px solid #e5e7eb;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
        }
        
        .dark .reward-type-group {
            border-color: #475569;
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
                    <!-- Theme Toggle Button -->
                    <button onclick="toggleTheme()" 
                            class="p-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-600 dark:text-slate-300 hover:bg-gray-200 dark:hover:bg-slate-600 transition-all"
                            title="Design wechseln">
                        <i class="fas fa-moon dark:hidden w-5 h-5"></i>
                        <i class="fas fa-sun hidden dark:inline w-5 h-5"></i>
                    </button>
                    
                    <span class="text-sm text-gray-500 dark:text-slate-400 hidden sm:inline">
                        <i class="fas fa-lock mr-1"></i>
                        Sichere Verbindung
                    </span>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="flex-1 py-4 sm:py-8">
            <div class="max-w-4xl mx-auto px-4">
                
                <!-- Progress Steps -->
                <div class="mb-6 sm:mb-8 overflow-x-auto">
                    <div class="flex items-center justify-between relative min-w-max sm:min-w-0 px-2">
                        <div class="absolute top-4 sm:top-5 left-0 right-0 h-0.5 bg-gray-200 dark:bg-slate-700">
                            <div id="progressBar" class="h-full bg-primary-500 transition-all duration-300" style="width: 0%"></div>
                        </div>
                        
                        <?php for ($i = 1; $i <= 8; $i++): 
                            $labels = ['Branche', 'Firma', 'Kontakt', 'Impressum', 'Belohnungen', 'Design', 'Subdomain', 'Fertig!'];
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
                <form id="onboardingForm" action="/onboarding/process.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="onboarding_token" value="<?= htmlspecialchars($token) ?>">
                    <input type="hidden" name="plan" value="<?= htmlspecialchars($plan) ?>">
                    
                    <!-- Step 1: Branche -->
                    <div class="wizard-panel active" data-panel="1">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700 transition-colors duration-300">
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
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700 transition-colors duration-300">
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
                                    <input type="url" name="website" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="https://www.ihre-website.de">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Kontaktdaten -->
                    <div class="wizard-panel" data-panel="3">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700 transition-colors duration-300">
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
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700 transition-colors duration-300">
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
                    
                    <!-- Step 5: Belohnungen -->
                    <div class="wizard-panel" data-panel="5">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700 transition-colors duration-300">
                            <h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-900 dark:text-white">Belohnungen für Empfehler</h2>
                            <p class="text-gray-500 dark:text-slate-400 mb-6 sm:mb-8 text-sm sm:text-base">Definieren Sie, was Ihre Empfehler für erfolgreiche Empfehlungen bekommen.</p>
                            
                            <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl p-4 mb-6">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-lightbulb text-blue-500 dark:text-blue-400 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-blue-800 dark:text-blue-300"><strong>Tipp:</strong> Je nach Belohnungstyp erscheinen automatisch die passenden Eingabefelder für URL, Gutschein-Code, Betrag oder andere Details. Die URL wird später als klickbarer Link in der Belohnungsmail eingefügt.</p>
                                        <?php if (!$isProfessional): ?>
                                        <p class="text-xs text-blue-600 dark:text-blue-400 mt-2">
                                            <i class="fas fa-crown text-yellow-500 mr-1"></i>
                                            Mit dem <strong>Professional-Tarif</strong> erhalten Sie Zugang zu erweiterten Belohnungstypen wie Video-Kurse, Coaching-Sessions und Affiliate-Provisionen.
                                        </p>
                                        <?php endif; ?>
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
                                    
                                    <!-- Basis-Felder -->
                                    <div class="grid sm:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Belohnungstyp</label>
                                            <select name="reward_<?= $i ?>_type" class="reward-type-select w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" data-level="<?= $i ?>">
                                                <optgroup label="Standard-Belohnungen">
                                                    <option value="discount" <?= $defaults[$i][1] === 'discount' ? 'selected' : '' ?>>💰 Rabatt (%)</option>
                                                    <option value="coupon_code" <?= $defaults[$i][1] === 'coupon_code' ? 'selected' : '' ?>>🎟️ Gutschein-Code</option>
                                                    <option value="free_product" <?= $defaults[$i][1] === 'free_product' ? 'selected' : '' ?>>🎁 Gratis-Produkt</option>
                                                    <option value="free_service">⭐ Gratis-Service</option>
                                                    <option value="digital_download">📥 Digital-Download (URL)</option>
                                                    <option value="voucher">💶 Wertgutschein (€)</option>
                                                </optgroup>
                                                <?php if ($isProfessional): ?>
                                                <optgroup label="Professional-Belohnungen">
                                                    <option value="video_course">🎬 Video-Kurs (URL)</option>
                                                    <option value="coaching_session">🎯 Coaching-Session</option>
                                                    <option value="webinar_access">📹 Webinar-Zugang</option>
                                                    <option value="exclusive_content">🔐 Exklusiver Inhalt (URL)</option>
                                                    <option value="affiliate_commission">💸 Affiliate-Provision (%)</option>
                                                    <option value="cash_bonus">🏆 Bar-Auszahlung (€)</option>
                                                    <option value="membership_upgrade">👑 Membership-Upgrade</option>
                                                    <option value="event_ticket">🎫 Event-Ticket</option>
                                                </optgroup>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Titel / Beschreibung *</label>
                                            <input type="text" name="reward_<?= $i ?>_description" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="z.B. 10% Rabatt auf alle Leistungen">
                                        </div>
                                    </div>
                                    
                                    <!-- Dynamische Zusatzfelder je nach Typ -->
                                    <div class="reward-extra-fields" id="reward_<?= $i ?>_extras">
                                        
                                        <!-- Rabatt (%) - MIT EINLÖSE-URL -->
                                        <div class="extra-field extra-discount <?= $defaults[$i][1] === 'discount' ? 'active' : '' ?> bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-percent text-primary-500 mr-1"></i>Rabatt in Prozent
                                                    </label>
                                                    <div class="flex items-center gap-2">
                                                        <input type="number" name="reward_<?= $i ?>_discount_percent" min="1" max="100" class="w-24 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="10">
                                                        <span class="text-gray-500 dark:text-slate-400">%</span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Wird in der E-Mail als {{rabatt_prozent}} eingefügt</p>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_discount_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/shop">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Seite wo der Rabatt eingelöst wird - wird als {{einloese_link}} eingefügt</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gutschein-Code - MIT EINLÖSE-URL -->
                                        <div class="extra-field extra-coupon_code <?= $defaults[$i][1] === 'coupon_code' ? 'active' : '' ?> bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                                            <div class="space-y-4">
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-ticket text-primary-500 mr-1"></i>Gutschein-Code
                                                        </label>
                                                        <input type="text" name="reward_<?= $i ?>_coupon_code" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono" placeholder="EMPFEHLUNG10">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-calendar text-primary-500 mr-1"></i>Gültigkeit (Tage)
                                                        </label>
                                                        <input type="number" name="reward_<?= $i ?>_coupon_validity" min="1" max="365" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="30">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_coupon_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/shop/checkout">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Seite wo der Gutschein-Code eingelöst wird - wird als {{einloese_link}} eingefügt</p>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-slate-400">Der Code wird als {{gutschein_code}} in die E-Mail eingefügt</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Digital Download -->
                                        <div class="extra-field extra-digital_download bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                <i class="fas fa-download text-primary-500 mr-1"></i>Download-URL *
                                            </label>
                                            <input type="url" name="reward_<?= $i ?>_download_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-seite.de/downloads/bonus.pdf">
                                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zu Ihrer Download-Datei (PDF, E-Book, etc.) - wird als {{download_link}} eingefügt</p>
                                        </div>
                                        
                                        <!-- Wertgutschein - MIT EINLÖSE-URL -->
                                        <div class="extra-field extra-voucher bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-euro-sign text-primary-500 mr-1"></i>Gutscheinwert in Euro
                                                    </label>
                                                    <div class="flex items-center gap-2">
                                                        <input type="number" name="reward_<?= $i ?>_voucher_amount" min="1" step="0.01" class="w-32 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="50.00">
                                                        <span class="text-gray-500 dark:text-slate-400">€</span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Wird in der E-Mail als {{gutschein_wert}} eingefügt</p>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_voucher_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/shop">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Seite wo der Wertgutschein eingelöst wird - wird als {{einloese_link}} eingefügt</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gratis-Produkt - MIT BESTELL-URL -->
                                        <div class="extra-field extra-free_product <?= $defaults[$i][1] === 'free_product' ? 'active' : '' ?> bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                                            <div class="space-y-4">
                                                <div class="flex items-start gap-3">
                                                    <input type="checkbox" name="reward_<?= $i ?>_requires_address" value="1" class="mt-1 w-4 h-4 text-primary-500 rounded border-gray-300 dark:border-slate-600 focus:ring-primary-500 bg-white dark:bg-slate-700">
                                                    <div>
                                                        <label class="text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300">Adresse abfragen</label>
                                                        <p class="text-xs text-gray-500 dark:text-slate-400">Aktivieren, wenn das Produkt versendet werden muss</p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-shopping-cart text-primary-500 mr-1"></i>Bestell-/Produktseite URL (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_product_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/produkt">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Produktseite oder Bestellung - wird als {{bestell_link}} eingefügt</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Gratis-Service - MIT BUCHUNGS-URL -->
                                        <div class="extra-field extra-free_service bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                                            <div class="space-y-4">
                                                <p class="text-xs text-gray-500 dark:text-slate-400">
                                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                                    Der Empfehler erhält eine E-Mail mit der Beschreibung. Sie können ihn dann manuell kontaktieren.
                                                </p>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-calendar-check text-primary-500 mr-1"></i>Buchungs-/Kontakt-URL (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_service_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/termin">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Terminbuchung oder Kontaktseite - wird als {{buchungs_link}} eingefügt</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- ==================== PROFESSIONAL BELOHNUNGEN ==================== -->
                                        
                                        <!-- Video-Kurs (URL) -->
                                        <div class="extra-field extra-video_course bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg p-4 mt-4 border border-purple-200 dark:border-purple-800">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                                            </div>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-video text-purple-500 mr-1"></i>Video-Kurs URL *
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_video_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-seite.de/kurs/zugang">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zum Video-Kurs oder Mitgliederbereich - wird als {{videokurs_link}} eingefügt</p>
                                                </div>
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-key text-purple-500 mr-1"></i>Zugangscode (optional)
                                                        </label>
                                                        <input type="text" name="reward_<?= $i ?>_video_access_code" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono" placeholder="KURS2024">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-clock text-purple-500 mr-1"></i>Zugang gültig (Tage)
                                                        </label>
                                                        <input type="number" name="reward_<?= $i ?>_video_validity" min="1" max="9999" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="365">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Coaching-Session -->
                                        <div class="extra-field extra-coaching_session bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 rounded-lg p-4 mt-4 border border-green-200 dark:border-green-800">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                                            </div>
                                            <div class="space-y-4">
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-clock text-green-500 mr-1"></i>Dauer (Minuten)
                                                        </label>
                                                        <select name="reward_<?= $i ?>_coaching_duration" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                            <option value="15">15 Minuten</option>
                                                            <option value="30" selected>30 Minuten</option>
                                                            <option value="45">45 Minuten</option>
                                                            <option value="60">60 Minuten</option>
                                                            <option value="90">90 Minuten</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-video text-green-500 mr-1"></i>Art der Session
                                                        </label>
                                                        <select name="reward_<?= $i ?>_coaching_type" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                            <option value="video_call">Video-Call (Zoom/Meet)</option>
                                                            <option value="phone">Telefon</option>
                                                            <option value="in_person">Vor Ort</option>
                                                            <option value="chat">Chat/Messenger</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-green-500 mr-1"></i>Buchungslink (optional)
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_coaching_booking_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://calendly.com/ihr-name">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Wird als {{buchungs_link}} in der E-Mail eingefügt</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Webinar-Zugang -->
                                        <div class="extra-field extra-webinar_access bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-4 mt-4 border border-indigo-200 dark:border-indigo-800">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                                            </div>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-broadcast-tower text-indigo-500 mr-1"></i>Webinar-URL / Registrierungslink *
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_webinar_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://webinarjam.com/register/...">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Wird als {{webinar_link}} in der E-Mail eingefügt</p>
                                                </div>
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-calendar-alt text-indigo-500 mr-1"></i>Webinar-Datum
                                                        </label>
                                                        <input type="date" name="reward_<?= $i ?>_webinar_date" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-clock text-indigo-500 mr-1"></i>Uhrzeit
                                                        </label>
                                                        <input type="time" name="reward_<?= $i ?>_webinar_time" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Exklusiver Inhalt (URL) -->
                                        <div class="extra-field extra-exclusive_content bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-lg p-4 mt-4 border border-amber-200 dark:border-amber-800">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                                            </div>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-lock-open text-amber-500 mr-1"></i>Exklusiver Inhalt URL *
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_exclusive_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-seite.de/exklusiv/bonus">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Wird als {{exklusiv_link}} in der E-Mail eingefügt</p>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-file-alt text-amber-500 mr-1"></i>Art des Inhalts
                                                    </label>
                                                    <select name="reward_<?= $i ?>_exclusive_type" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                        <option value="ebook">E-Book / PDF</option>
                                                        <option value="template">Templates / Vorlagen</option>
                                                        <option value="checklist">Checkliste</option>
                                                        <option value="bonus_video">Bonus-Video</option>
                                                        <option value="audio">Audio / Podcast</option>
                                                        <option value="software">Software / Tool</option>
                                                        <option value="other">Sonstiges</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Affiliate-Provision (%) -->
                                        <div class="extra-field extra-affiliate_commission bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-lg p-4 mt-4 border border-emerald-200 dark:border-emerald-800">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                                            </div>
                                            <div class="space-y-4">
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-percentage text-emerald-500 mr-1"></i>Provision in Prozent
                                                        </label>
                                                        <div class="flex items-center gap-2">
                                                            <input type="number" name="reward_<?= $i ?>_affiliate_percent" min="1" max="100" class="w-24 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="20">
                                                            <span class="text-gray-500 dark:text-slate-400">%</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-euro-sign text-emerald-500 mr-1"></i>Max. Auszahlung (€)
                                                        </label>
                                                        <input type="number" name="reward_<?= $i ?>_affiliate_max" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="100.00">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-info-circle text-emerald-500 mr-1"></i>Produkt/Service für Provision
                                                    </label>
                                                    <input type="text" name="reward_<?= $i ?>_affiliate_product" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. Alle Hauptprodukte">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Wird als {{affiliate_prozent}} in der E-Mail eingefügt</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Bar-Auszahlung (€) -->
                                        <div class="extra-field extra-cash_bonus bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-lg p-4 mt-4 border border-yellow-200 dark:border-yellow-800">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                                            </div>
                                            <div class="space-y-4">
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-coins text-yellow-500 mr-1"></i>Auszahlungsbetrag
                                                        </label>
                                                        <div class="flex items-center gap-2">
                                                            <input type="number" name="reward_<?= $i ?>_cash_amount" min="1" step="0.01" class="w-32 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="50.00">
                                                            <span class="text-gray-500 dark:text-slate-400">€</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-credit-card text-yellow-500 mr-1"></i>Auszahlungsmethode
                                                        </label>
                                                        <select name="reward_<?= $i ?>_cash_method" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                            <option value="bank_transfer">Banküberweisung</option>
                                                            <option value="paypal">PayPal</option>
                                                            <option value="amazon_gift">Amazon Gutschein</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-slate-400">
                                                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i>
                                                    Hinweis: Der Empfehler wird kontaktiert um seine Zahlungsdaten anzugeben. Wird als {{bar_betrag}} eingefügt.
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <!-- Membership-Upgrade -->
                                        <div class="extra-field extra-membership_upgrade bg-gradient-to-r from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-lg p-4 mt-4 border border-violet-200 dark:border-violet-800">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                                            </div>
                                            <div class="space-y-4">
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-arrow-up text-violet-500 mr-1"></i>Upgrade auf
                                                        </label>
                                                        <input type="text" name="reward_<?= $i ?>_membership_level" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. Gold-Mitgliedschaft">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-calendar text-violet-500 mr-1"></i>Dauer (Monate)
                                                        </label>
                                                        <select name="reward_<?= $i ?>_membership_duration" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                            <option value="1">1 Monat</option>
                                                            <option value="3">3 Monate</option>
                                                            <option value="6">6 Monate</option>
                                                            <option value="12" selected>12 Monate</option>
                                                            <option value="lifetime">Lebenslang</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-violet-500 mr-1"></i>Aktivierungs-URL *
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_membership_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-seite.de/upgrade">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Wird als {{membership_link}} in der E-Mail eingefügt</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Event-Ticket -->
                                        <div class="extra-field extra-event_ticket bg-gradient-to-r from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 rounded-lg p-4 mt-4 border border-rose-200 dark:border-rose-800">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                                            </div>
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-calendar-check text-rose-500 mr-1"></i>Event-Name
                                                    </label>
                                                    <input type="text" name="reward_<?= $i ?>_event_name" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. Jahreskonferenz 2024">
                                                </div>
                                                <div class="grid sm:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-calendar-alt text-rose-500 mr-1"></i>Event-Datum
                                                        </label>
                                                        <input type="date" name="reward_<?= $i ?>_event_date" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                            <i class="fas fa-map-marker-alt text-rose-500 mr-1"></i>Event-Ort
                                                        </label>
                                                        <input type="text" name="reward_<?= $i ?>_event_location" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="z.B. Berlin oder Online">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                                        <i class="fas fa-link text-rose-500 mr-1"></i>Ticket-/Registrierungslink *
                                                    </label>
                                                    <input type="url" name="reward_<?= $i ?>_event_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://eventbrite.de/...">
                                                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Wird als {{event_link}} in der E-Mail eingefügt</p>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <?php endfor; ?>
                            </div>
                            
                            <?php if ($isProfessional): ?>
                            <button type="button" id="addRewardBtn" class="mt-6 flex items-center gap-2 text-primary-500 hover:text-primary-600 dark:text-primary-400 dark:hover:text-primary-300 font-medium text-sm sm:text-base transition-colors">
                                <i class="fas fa-plus-circle text-lg"></i>
                                <span>Weitere Stufe hinzufügen (bis zu 10 Stufen)</span>
                            </button>
                            <?php else: ?>
                            <div class="mt-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl border border-gray-200 dark:border-slate-600">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-crown text-yellow-500 text-xl"></i>
                                    <div>
                                        <p class="text-sm font-medium text-gray-700 dark:text-slate-300">Mehr Stufen gewünscht?</p>
                                        <p class="text-xs text-gray-500 dark:text-slate-400">Mit dem Professional-Tarif können Sie bis zu 10 Belohnungsstufen erstellen und haben Zugang zu erweiterten Belohnungstypen.</p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Step 6: Design -->
                    <div class="wizard-panel" data-panel="6">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700 transition-colors duration-300">
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
                            
                            <?php if ($isProfessional): ?>
                            <div class="mt-6 p-4 bg-gray-50 dark:bg-slate-700/50 rounded-xl">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-slate-300 mb-2">
                                    <i class="fas fa-crown text-yellow-500 mr-1"></i>Eigenes Hintergrundbild (Pro)
                                </label>
                                <input type="file" name="custom_background" accept="image/*" class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400 mt-1">Empfohlen: 1920x1080px, max. 2MB</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Step 7: Subdomain -->
                    <div class="wizard-panel" data-panel="7">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 border border-gray-200 dark:border-slate-700 transition-colors duration-300">
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
                    
                    <!-- Step 8: Fertig -->
                    <div class="wizard-panel" data-panel="8">
                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 sm:p-8 text-center border border-gray-200 dark:border-slate-700 transition-colors duration-300">
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
        <footer class="bg-white dark:bg-slate-800 border-t border-gray-200 dark:border-slate-700 py-4 sm:py-6 transition-colors duration-300">
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
        const backgroundsByIndustry = <?= json_encode($backgroundsByIndustry) ?>;
        const isProfessional = <?= $isProfessional ? 'true' : 'false' ?>;
        let rewardLevelCount = 3;
        const maxRewardLevels = isProfessional ? 10 : 3;
        
        // Theme Toggle Function
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
        
        // Reward Type Change Handler - Zeigt/versteckt dynamische Felder
        function initRewardTypeHandlers() {
            document.querySelectorAll('.reward-type-select').forEach(select => {
                select.addEventListener('change', function() {
                    const level = this.dataset.level;
                    const type = this.value;
                    updateRewardExtraFields(level, type);
                });
                
                // Initial: richtige Felder anzeigen
                const level = select.dataset.level;
                const type = select.value;
                updateRewardExtraFields(level, type);
            });
        }
        
        function updateRewardExtraFields(level, type) {
            const container = document.getElementById(`reward_${level}_extras`);
            if (!container) return;
            
            // Alle extra-fields verstecken
            container.querySelectorAll('.extra-field').forEach(field => {
                field.classList.remove('active');
            });
            
            // Passendes Feld anzeigen
            const targetField = container.querySelector(`.extra-${type}`);
            if (targetField) {
                targetField.classList.add('active');
            }
        }
        
        // Neue Belohnungsstufe hinzufügen (nur für Professional)
        function addRewardLevel() {
            if (rewardLevelCount >= maxRewardLevels) {
                alert(`Maximal ${maxRewardLevels} Stufen möglich.`);
                return;
            }
            
            rewardLevelCount++;
            const container = document.getElementById('rewardsContainer');
            const template = createRewardLevelHTML(rewardLevelCount);
            container.insertAdjacentHTML('beforeend', template);
            
            // Event-Handler für das neue Select initialisieren
            const newSelect = container.querySelector(`[data-level="${rewardLevelCount}"]`);
            if (newSelect) {
                newSelect.addEventListener('change', function() {
                    updateRewardExtraFields(this.dataset.level, this.value);
                });
                updateRewardExtraFields(rewardLevelCount, newSelect.value);
            }
            
            // Button ausblenden wenn Maximum erreicht
            if (rewardLevelCount >= maxRewardLevels) {
                document.getElementById('addRewardBtn').style.display = 'none';
            }
        }
        
        function createRewardLevelHTML(level) {
            const threshold = level * 5;
            return `
            <div class="reward-level border border-gray-200 dark:border-slate-600 rounded-xl p-4 sm:p-6 bg-white dark:bg-slate-800" data-reward-level="${level}">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold">${level}</div>
                        <div>
                            <h3 class="font-semibold text-sm sm:text-base text-gray-900 dark:text-white">Stufe ${level}</h3>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-slate-400">
                                Nach 
                                <input type="number" name="reward_${level}_threshold" value="${threshold}" min="1" max="100" class="w-12 sm:w-16 px-2 py-1 border border-gray-300 dark:border-slate-600 rounded text-center text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white"> 
                                erfolgreichen Empfehlungen
                            </p>
                        </div>
                    </div>
                    <button type="button" onclick="removeRewardLevel(${level})" class="text-red-500 hover:text-red-600 text-sm">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                
                <div class="grid sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Belohnungstyp</label>
                        <select name="reward_${level}_type" class="reward-type-select w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" data-level="${level}">
                            <optgroup label="Standard-Belohnungen">
                                <option value="discount">💰 Rabatt (%)</option>
                                <option value="coupon_code">🎟️ Gutschein-Code</option>
                                <option value="free_product">🎁 Gratis-Produkt</option>
                                <option value="free_service">⭐ Gratis-Service</option>
                                <option value="digital_download">📥 Digital-Download (URL)</option>
                                <option value="voucher">💶 Wertgutschein (€)</option>
                            </optgroup>
                            ${isProfessional ? `
                            <optgroup label="Professional-Belohnungen">
                                <option value="video_course">🎬 Video-Kurs (URL)</option>
                                <option value="coaching_session">🎯 Coaching-Session</option>
                                <option value="webinar_access">📹 Webinar-Zugang</option>
                                <option value="exclusive_content">🔐 Exklusiver Inhalt (URL)</option>
                                <option value="affiliate_commission">💸 Affiliate-Provision (%)</option>
                                <option value="cash_bonus">🏆 Bar-Auszahlung (€)</option>
                                <option value="membership_upgrade">👑 Membership-Upgrade</option>
                                <option value="event_ticket">🎫 Event-Ticket</option>
                            </optgroup>
                            ` : ''}
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Titel / Beschreibung *</label>
                        <input type="text" name="reward_${level}_description" required class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-slate-500" placeholder="z.B. Exklusiver Bonus">
                    </div>
                </div>
                
                <div class="reward-extra-fields" id="reward_${level}_extras">
                    ${createAllExtraFieldsHTML(level)}
                </div>
            </div>
            `;
        }
        
        function createAllExtraFieldsHTML(level) {
            // Alle Extra-Felder HTML generieren mit URL-Feldern
            return `
                <!-- Rabatt (%) - MIT EINLÖSE-URL -->
                <div class="extra-field extra-discount active bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                <i class="fas fa-percent text-primary-500 mr-1"></i>Rabatt in Prozent
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="reward_${level}_discount_percent" min="1" max="100" class="w-24 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="10">
                                <span class="text-gray-500 dark:text-slate-400">%</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                            </label>
                            <input type="url" name="reward_${level}_discount_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/shop">
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Seite wo der Rabatt eingelöst wird - wird als {{einloese_link}} eingefügt</p>
                        </div>
                    </div>
                </div>
                
                <!-- Gutschein-Code - MIT EINLÖSE-URL -->
                <div class="extra-field extra-coupon_code bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                    <div class="space-y-4">
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                    <i class="fas fa-ticket text-primary-500 mr-1"></i>Gutschein-Code
                                </label>
                                <input type="text" name="reward_${level}_coupon_code" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono" placeholder="EMPFEHLUNG10">
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                    <i class="fas fa-calendar text-primary-500 mr-1"></i>Gültigkeit (Tage)
                                </label>
                                <input type="number" name="reward_${level}_coupon_validity" min="1" max="365" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="30">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                            </label>
                            <input type="url" name="reward_${level}_coupon_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/shop/checkout">
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Seite wo der Gutschein eingelöst wird - wird als {{einloese_link}} eingefügt</p>
                        </div>
                    </div>
                </div>
                
                <!-- Digital Download -->
                <div class="extra-field extra-digital_download bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                    <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                        <i class="fas fa-download text-primary-500 mr-1"></i>Download-URL
                    </label>
                    <input type="url" name="reward_${level}_download_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-seite.de/downloads/bonus.pdf">
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zu Ihrer Download-Datei - wird als {{download_link}} eingefügt</p>
                </div>
                
                <!-- Wertgutschein - MIT EINLÖSE-URL -->
                <div class="extra-field extra-voucher bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                <i class="fas fa-euro-sign text-primary-500 mr-1"></i>Gutscheinwert in Euro
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="reward_${level}_voucher_amount" min="1" step="0.01" class="w-32 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="50.00">
                                <span class="text-gray-500 dark:text-slate-400">€</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                <i class="fas fa-link text-primary-500 mr-1"></i>Einlöse-URL (optional)
                            </label>
                            <input type="url" name="reward_${level}_voucher_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/shop">
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Seite wo der Wertgutschein eingelöst wird - wird als {{einloese_link}} eingefügt</p>
                        </div>
                    </div>
                </div>
                
                <!-- Gratis-Produkt - MIT BESTELL-URL -->
                <div class="extra-field extra-free_product bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="reward_${level}_requires_address" value="1" class="mt-1 w-4 h-4 text-primary-500 rounded">
                            <div>
                                <label class="text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300">Adresse abfragen</label>
                                <p class="text-xs text-gray-500 dark:text-slate-400">Aktivieren, wenn das Produkt versendet werden muss</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                <i class="fas fa-shopping-cart text-primary-500 mr-1"></i>Bestell-/Produktseite URL (optional)
                            </label>
                            <input type="url" name="reward_${level}_product_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/produkt">
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Produktseite oder Bestellung - wird als {{bestell_link}} eingefügt</p>
                        </div>
                    </div>
                </div>
                
                <!-- Gratis-Service - MIT BUCHUNGS-URL -->
                <div class="extra-field extra-free_service bg-gray-50 dark:bg-slate-700/50 rounded-lg p-4 mt-4">
                    <div class="space-y-4">
                        <p class="text-xs text-gray-500 dark:text-slate-400">
                            <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                            Der Empfehler erhält eine E-Mail mit der Beschreibung.
                        </p>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                <i class="fas fa-calendar-check text-primary-500 mr-1"></i>Buchungs-/Kontakt-URL (optional)
                            </label>
                            <input type="url" name="reward_${level}_service_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-website.de/termin">
                            <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Link zur Terminbuchung oder Kontaktseite - wird als {{buchungs_link}} eingefügt</p>
                        </div>
                    </div>
                </div>
                
                <!-- Video-Kurs (URL) -->
                <div class="extra-field extra-video_course bg-gradient-to-r from-purple-50 to-blue-50 dark:from-purple-900/20 dark:to-blue-900/20 rounded-lg p-4 mt-4 border border-purple-200 dark:border-purple-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                                <i class="fas fa-video text-purple-500 mr-1"></i>Video-Kurs URL
                            </label>
                            <input type="url" name="reward_${level}_video_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-seite.de/kurs/zugang">
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Zugangscode</label>
                                <input type="text" name="reward_${level}_video_access_code" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white font-mono" placeholder="KURS2024">
                            </div>
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Zugang gültig (Tage)</label>
                                <input type="number" name="reward_${level}_video_validity" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="365">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Coaching-Session -->
                <div class="extra-field extra-coaching_session bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 rounded-lg p-4 mt-4 border border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Dauer</label>
                            <select name="reward_${level}_coaching_duration" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white">
                                <option value="15">15 Min</option>
                                <option value="30" selected>30 Min</option>
                                <option value="60">60 Min</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Buchungslink</label>
                            <input type="url" name="reward_${level}_coaching_booking_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://calendly.com/...">
                        </div>
                    </div>
                </div>
                
                <!-- Webinar-Zugang -->
                <div class="extra-field extra-webinar_access bg-gradient-to-r from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-lg p-4 mt-4 border border-indigo-200 dark:border-indigo-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Webinar-URL</label>
                        <input type="url" name="reward_${level}_webinar_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://webinarjam.com/...">
                    </div>
                </div>
                
                <!-- Exklusiver Inhalt (URL) -->
                <div class="extra-field extra-exclusive_content bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-lg p-4 mt-4 border border-amber-200 dark:border-amber-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                    </div>
                    <div>
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Exklusiver Inhalt URL</label>
                        <input type="url" name="reward_${level}_exclusive_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://ihre-seite.de/exklusiv">
                    </div>
                </div>
                
                <!-- Affiliate-Provision (%) -->
                <div class="extra-field extra-affiliate_commission bg-gradient-to-r from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-lg p-4 mt-4 border border-emerald-200 dark:border-emerald-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="reward_${level}_affiliate_percent" min="1" max="100" class="w-24 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="20">
                        <span class="text-gray-500 dark:text-slate-400">% Provision</span>
                    </div>
                </div>
                
                <!-- Bar-Auszahlung (€) -->
                <div class="extra-field extra-cash_bonus bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 rounded-lg p-4 mt-4 border border-yellow-200 dark:border-yellow-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="number" name="reward_${level}_cash_amount" min="1" step="0.01" class="w-32 px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="50.00">
                        <span class="text-gray-500 dark:text-slate-400">€ Bar-Auszahlung</span>
                    </div>
                </div>
                
                <!-- Membership-Upgrade -->
                <div class="extra-field extra-membership_upgrade bg-gradient-to-r from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-lg p-4 mt-4 border border-violet-200 dark:border-violet-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Upgrade auf</label>
                            <input type="text" name="reward_${level}_membership_level" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="Gold-Mitgliedschaft">
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Aktivierungs-URL</label>
                            <input type="url" name="reward_${level}_membership_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://...">
                        </div>
                    </div>
                </div>
                
                <!-- Event-Ticket -->
                <div class="extra-field extra-event_ticket bg-gradient-to-r from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/20 rounded-lg p-4 mt-4 border border-rose-200 dark:border-rose-800">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="pro-badge"><i class="fas fa-crown"></i> PRO</span>
                    </div>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Event-Name</label>
                            <input type="text" name="reward_${level}_event_name" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="Jahreskonferenz 2024">
                        </div>
                        <div>
                            <label class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Ticket-Link</label>
                            <input type="url" name="reward_${level}_event_url" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-600 rounded-lg text-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white" placeholder="https://eventbrite.de/...">
                        </div>
                    </div>
                </div>
            `;
        }
        
        function removeRewardLevel(level) {
            if (confirm('Möchten Sie diese Belohnungsstufe wirklich entfernen?')) {
                const levelElement = document.querySelector(`[data-reward-level="${level}"]`);
                if (levelElement) {
                    levelElement.remove();
                    rewardLevelCount--;
                    
                    // Button wieder anzeigen wenn unter Maximum
                    if (rewardLevelCount < maxRewardLevels && isProfessional) {
                        const addBtn = document.getElementById('addRewardBtn');
                        if (addBtn) addBtn.style.display = 'flex';
                    }
                }
            }
        }
        
        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            initRewardTypeHandlers();
            
            // Add reward button handler
            const addRewardBtn = document.getElementById('addRewardBtn');
            if (addRewardBtn) {
                addRewardBtn.addEventListener('click', addRewardLevel);
            }
        });
    </script>
    <script src="/assets/js/onboarding.js"></script>
    
</body>
</html>