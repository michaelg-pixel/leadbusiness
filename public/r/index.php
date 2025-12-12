<?php
/**
 * Leadbusiness - Empfehlungsseite (Referral Landing Page)
 * 
 * Diese Seite wird unter den Kunden-Subdomains angezeigt:
 * z.B. zahnarzt-mueller.empfohlen.de
 * 
 * URL-Struktur:
 * - / oder /r/ = Hauptseite (Anmeldung als Empfehler)
 * - /r/{code} = Empfehlungslink (trackt Klick, zeigt Anmeldeformular)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/security/RateLimiter.php';
require_once __DIR__ . '/../../includes/security/BotDetector.php';
require_once __DIR__ . '/../../includes/security/DisposableEmailBlocker.php';
require_once __DIR__ . '/../../includes/services/BackgroundService.php';
require_once __DIR__ . '/../../includes/services/LeaderboardService.php';
require_once __DIR__ . '/../../includes/services/LeadEventHandler.php';

$db = Database::getInstance();

// Subdomain aus Host ermitteln
$host = $_SERVER['HTTP_HOST'] ?? '';
$subdomain = '';

// Subdomain extrahieren (zahnarzt-mueller aus zahnarzt-mueller.empfohlen.de)
if (preg_match('/^([a-z0-9-]+)\.empfohlen\.de$/i', $host, $matches)) {
    $subdomain = strtolower($matches[1]);
}

// Fallback für lokale Entwicklung
if (empty($subdomain) && isset($_GET['subdomain'])) {
    $subdomain = strtolower($_GET['subdomain']);
}

if (empty($subdomain)) {
    // Keine Subdomain - zur Hauptseite weiterleiten
    header('Location: https://empfohlen.de');
    exit;
}

// Kunde anhand Subdomain laden
$customer = $db->fetch(
    "SELECT c.*, bi.filename as bg_filename, bi.industry as bg_industry
     FROM customers c
     LEFT JOIN background_images bi ON c.background_image_id = bi.id
     WHERE c.subdomain = ? 
     AND c.subscription_status = 'active'",
    [$subdomain]
);

if (!$customer) {
    // Kunde nicht gefunden oder inaktiv
    http_response_code(404);
    include __DIR__ . '/../404.php';
    exit;
}

// Standard-Kampagne laden
$campaign = $db->fetch(
    "SELECT * FROM campaigns 
     WHERE customer_id = ? AND is_default = 1 AND is_active = 1",
    [$customer['id']]
);

if (!$campaign) {
    http_response_code(404);
    include __DIR__ . '/../404.php';
    exit;
}

// Belohnungen laden
$rewards = $db->fetchAll(
    "SELECT * FROM rewards 
     WHERE campaign_id = ? AND is_active = 1
     ORDER BY level ASC",
    [$campaign['id']]
);

// Referral-Code aus URL extrahieren
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$referralCode = null;
$referrer = null;

if (preg_match('/\/r\/([A-Z0-9]+)/i', $path, $matches)) {
    $referralCode = strtoupper($matches[1]);
    
    // Referrer laden
    $referrer = $db->fetch(
        "SELECT * FROM leads 
         WHERE referral_code = ? AND campaign_id = ? AND status = 'active'",
        [$referralCode, $campaign['id']]
    );
    
    // Klick tracken
    if ($referrer) {
        $rateLimiter = new RateLimiter();
        $clientIp = getClientIp();
        
        if ($rateLimiter->checkLimit('click_tracking', hashIp($clientIp))) {
            // Klick speichern
            $db->insert('clicks', [
                'lead_id' => $referrer['id'],
                'campaign_id' => $campaign['id'],
                'ip_hash' => hashIp($clientIp),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'referrer_url' => $_SERVER['HTTP_REFERER'] ?? '',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Klick-Zaehler erhoehen
            $db->query("UPDATE leads SET clicks = clicks + 1 WHERE id = ?", [$referrer['id']]);
        }
    }
}

// Hintergrundbild URL
$backgroundService = new BackgroundService();
$backgroundUrl = $backgroundService->getCustomerBackgroundUrl($customer);

// Live-Counter Daten
$leaderboardService = new LeaderboardService();
$liveCounter = $leaderboardService->getLiveCounterData($campaign['id']);

// Leaderboard
$leaderboard = [];
if ($customer['leaderboard_enabled']) {
    $leaderboard = $leaderboardService->getAnonymizedLeaderboard($campaign['id'], 5);
}

// Session fuer Form
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Bot-Schutz Felder
$botDetector = new BotDetector();
$protectionFields = $botDetector->getAllProtectionFields();

// Formular verarbeiten (AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAjax()) {
    header('Content-Type: application/json');
    
    try {
        // CSRF pruefen
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            throw new Exception('Ungueltige Anfrage. Bitte laden Sie die Seite neu.');
        }
        
        // Bot-Check
        $botResult = $botDetector->analyze($_POST);
        if ($botResult['is_bot']) {
            throw new Exception('Ihre Anfrage wurde als verdaechtig eingestuft.');
        }
        
        // Rate Limiting
        $rateLimiter = new RateLimiter();
        $clientIp = getClientIp();
        
        if (!$rateLimiter->checkLimit('lead_registration', hashIp($clientIp))) {
            throw new Exception('Zu viele Anfragen. Bitte warten Sie einen Moment.');
        }
        
        // Eingaben validieren
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $name = trim($_POST['name'] ?? '');
        
        if (!$email) {
            throw new Exception('Bitte geben Sie eine gueltige E-Mail-Adresse ein.');
        }
        
        // Wegwerf-E-Mail pruefen
        $disposableBlocker = new DisposableEmailBlocker();
        if ($disposableBlocker->isDisposable($email)) {
            throw new Exception('Bitte verwenden Sie eine permanente E-Mail-Adresse.');
        }
        
        // Pruefen ob E-Mail bereits registriert
        $existingLead = $db->fetch(
            "SELECT id, status FROM leads WHERE email = ? AND campaign_id = ?",
            [$email, $campaign['id']]
        );
        
        if ($existingLead) {
            if ($existingLead['status'] === 'blocked') {
                throw new Exception('Diese E-Mail-Adresse ist gesperrt.');
            }
            throw new Exception('Diese E-Mail-Adresse ist bereits registriert.');
        }
        
        // Einzigartigen Referral-Code generieren
        $newReferralCode = generateUniqueReferralCode($db);
        
        // Confirmation Token
        $confirmationToken = generateToken(64);
        
        // Lead erstellen
        $leadId = $db->insert('leads', [
            'customer_id' => $customer['id'],
            'campaign_id' => $campaign['id'],
            'email' => $email,
            'name' => $name,
            'referral_code' => $newReferralCode,
            'referred_by_id' => $referrer ? $referrer['id'] : null,
            'status' => 'pending', // Wird nach E-Mail-Bestaetigung aktiv
            'confirmation_token' => $confirmationToken,
            'ip_hash' => hashIp($clientIp),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'source' => $referrer ? 'referral' : 'direct',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // E-Mail-Tool Sync (passiv - Lead landet beim Kunden-Tool)
        triggerLeadCreated($leadId, $customer['id'], [
            'email' => $email,
            'name' => $name,
            'referral_code' => $newReferralCode,
            'campaign_name' => $campaign['name'] ?? ''
        ]);
        
        // Bestaetigungs-E-Mail in Queue
        $confirmUrl = "https://{$subdomain}.empfohlen.de/confirm/{$confirmationToken}";
        
        $db->insert('email_queue', [
            'customer_id' => $customer['id'],
            'lead_id' => $leadId,
            'recipient_email' => $email,
            'recipient_name' => $name,
            'template' => 'confirmation',
            'variables' => json_encode([
                'lead_name' => $name ?: 'Empfehler',
                'company_name' => $customer['company_name'],
                'confirm_url' => $confirmUrl,
                'referral_code' => $newReferralCode
            ]),
            'priority' => 10,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Wenn Referrer vorhanden, als Conversion tracken (nach Bestaetigung)
        if ($referrer) {
            $db->insert('conversions', [
                'lead_id' => $leadId,
                'referrer_id' => $referrer['id'],
                'campaign_id' => $campaign['id'],
                'status' => 'pending', // Wird nach Bestaetigung 'confirmed'
                'ip_hash' => hashIp($clientIp),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
        
        // Kunden-Stats aktualisieren
        $db->query(
            "UPDATE customers SET total_leads = total_leads + 1 WHERE id = ?",
            [$customer['id']]
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Fast geschafft! Bitte bestaetigen Sie Ihre E-Mail-Adresse.',
            'redirect' => null
        ]);
        exit;
        
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

// Impressum-Adresse zusammenbauen
$impressumAddress = '';
if (!empty($customer['address_street'])) {
    $impressumAddress = $customer['address_street'];
}
if (!empty($customer['address_zip']) && !empty($customer['address_city'])) {
    $impressumAddress .= ', ' . $customer['address_zip'] . ' ' . $customer['address_city'];
}

// Page Title
$pageTitle = "Empfehlen Sie {$customer['company_name']} und erhalten Sie tolle Belohnungen!";
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="Werden Sie Empfehler bei <?= htmlspecialchars($customer['company_name']) ?> und erhalten Sie Belohnungen fuer erfolgreiche Empfehlungen.">
    <meta name="robots" content="noindex, nofollow">
    
    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="Empfehlen Sie <?= htmlspecialchars($customer['company_name']) ?> und erhalten Sie tolle Belohnungen!">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://<?= htmlspecialchars($subdomain) ?>.empfohlen.de">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?= htmlspecialchars($customer['primary_color']) ?>',
                        'primary-dark': '<?= adjustColor($customer['primary_color'], -20) ?>'
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
    
    <!-- Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        .hero-bg {
            background-image: url('<?= htmlspecialchars($backgroundUrl) ?>');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .hero-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.4) 100%);
        }
        
        .reward-card {
            transition: transform 0.2s ease;
        }
        
        .reward-card:hover {
            transform: translateY(-4px);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <!-- Hero Section -->
    <section class="hero-bg min-h-screen flex items-center justify-center px-4 py-12">
        <div class="relative z-10 max-w-4xl mx-auto text-center">
            
            <!-- Logo -->
            <?php if ($customer['logo_url']): ?>
            <div class="mb-8">
                <img src="<?= htmlspecialchars($customer['logo_url']) ?>" 
                     alt="<?= htmlspecialchars($customer['company_name']) ?>" 
                     class="h-16 md:h-20 mx-auto object-contain bg-white/90 rounded-xl px-6 py-3">
            </div>
            <?php else: ?>
            <div class="mb-8">
                <div class="inline-block bg-white/90 rounded-xl px-6 py-3">
                    <span class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($customer['company_name']) ?></span>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Headline -->
            <h1 class="text-3xl md:text-5xl font-extrabold text-white mb-6 leading-tight">
                <?php if ($referrer): ?>
                    <?= htmlspecialchars($referrer['name'] ?: 'Ein Freund') ?> hat Sie eingeladen!
                <?php else: ?>
                    Empfehlen Sie uns und erhalten Sie
                    <span class="text-yellow-400">tolle Belohnungen</span>
                <?php endif; ?>
            </h1>
            
            <p class="text-lg md:text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Werden Sie Teil unseres Empfehlungsprogramms und erhalten Sie fuer jede erfolgreiche Empfehlung attraktive Praemien.
            </p>
            
            <!-- Live Counter -->
            <?php if ($customer['live_counter_enabled'] && $liveCounter['total'] > 10): ?>
            <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm px-4 py-2 rounded-full mb-8">
                <span class="w-2 h-2 bg-green-400 rounded-full pulse-animation"></span>
                <span class="text-white text-sm font-medium">
                    <?= number_format($liveCounter['total']) ?> Personen nehmen bereits teil
                </span>
            </div>
            <?php endif; ?>
            
            <!-- Signup Form Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-6 md:p-8 max-w-md mx-auto">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Jetzt kostenlos anmelden</h2>
                
                <form id="signupForm" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <?= $protectionFields ?>
                    
                    <div class="space-y-4">
                        <div>
                            <input type="text" name="name" placeholder="Ihr Name (optional)"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <div>
                            <input type="email" name="email" required placeholder="Ihre E-Mail-Adresse *"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary">
                        </div>
                        
                        <button type="submit" id="submitBtn"
                            class="w-full py-4 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                            <span>Jetzt Empfehler werden</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                    
                    <div id="formMessage" class="mt-4 hidden"></div>
                </form>
                
                <p class="text-xs text-gray-500 mt-4">
                    Mit Ihrer Anmeldung akzeptieren Sie unsere 
                    <a href="#datenschutz" class="text-primary hover:underline">Datenschutzbestimmungen</a>.
                </p>
            </div>
            
        </div>
    </section>
    
    <!-- Rewards Section -->
    <section class="py-16 bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">So werden Sie belohnt</h2>
                <p class="text-gray-600">Fuer jede erfolgreiche Empfehlung erhalten Sie tolle Praemien.</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <?php foreach ($rewards as $index => $reward): ?>
                <div class="reward-card bg-gray-50 rounded-2xl p-6 text-center border-2 border-transparent hover:border-primary">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center text-2xl"
                         style="background-color: <?= htmlspecialchars($customer['primary_color']) ?>20;">
                        <?php
                        $icons = ['🎁', '🏆', '👑', '💎', '🚀'];
                        echo $icons[$index] ?? '🎁';
                        ?>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Stufe <?= $reward['level'] ?></h3>
                    <p class="text-sm text-gray-500 mb-3"><?= $reward['required_conversions'] ?> erfolgreiche Empfehlungen</p>
                    <p class="font-semibold" style="color: <?= htmlspecialchars($customer['primary_color']) ?>;">
                        <?= htmlspecialchars($reward['description']) ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    
    <!-- How It Works -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">So einfach funktioniert's</h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-12 h-12 rounded-full text-white font-bold text-xl flex items-center justify-center mx-auto mb-4"
                         style="background-color: <?= htmlspecialchars($customer['primary_color']) ?>;">
                        1
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Anmelden</h3>
                    <p class="text-gray-600 text-sm">Kostenlos registrieren und Ihren persoenlichen Empfehlungslink erhalten.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 rounded-full text-white font-bold text-xl flex items-center justify-center mx-auto mb-4"
                         style="background-color: <?= htmlspecialchars($customer['primary_color']) ?>;">
                        2
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Teilen</h3>
                    <p class="text-gray-600 text-sm">Teilen Sie Ihren Link mit Freunden und Bekannten.</p>
                </div>
                
                <div class="text-center">
                    <div class="w-12 h-12 rounded-full text-white font-bold text-xl flex items-center justify-center mx-auto mb-4"
                         style="background-color: <?= htmlspecialchars($customer['primary_color']) ?>;">
                        3
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Belohnung erhalten</h3>
                    <p class="text-gray-600 text-sm">Fuer jede erfolgreiche Empfehlung erhalten Sie automatisch Ihre Belohnung.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Leaderboard -->
    <?php if ($customer['leaderboard_enabled'] && !empty($leaderboard)): ?>
    <section class="py-16 bg-white">
        <div class="max-w-2xl mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Top Empfehler</h2>
                <p class="text-gray-600 text-sm">Unsere erfolgreichsten Empfehler</p>
            </div>
            
            <div class="bg-gray-50 rounded-2xl overflow-hidden">
                <?php foreach ($leaderboard as $index => $leader): ?>
                <div class="flex items-center gap-4 p-4 <?= $index > 0 ? 'border-t border-gray-200' : '' ?>">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg
                        <?= $index === 0 ? 'bg-yellow-400 text-white' : ($index === 1 ? 'bg-gray-300 text-white' : ($index === 2 ? 'bg-amber-600 text-white' : 'bg-gray-200 text-gray-600')) ?>">
                        <?= $index + 1 ?>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900"><?= htmlspecialchars($leader['display_name']) ?></div>
                        <div class="text-sm text-gray-500"><?= $leader['conversions'] ?> Empfehlungen</div>
                    </div>
                    <?php if ($leader['badge_count'] > 0): ?>
                    <div class="text-sm text-gray-400">
                        🏅 x<?= $leader['badge_count'] ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
    
    <!-- Footer mit vollständigem Impressum -->
    <footer class="py-8 bg-gray-900 text-gray-400">
        <div class="max-w-4xl mx-auto px-4 text-center text-sm">
            <!-- Firmenname -->
            <p class="text-white font-semibold mb-2"><?= htmlspecialchars($customer['company_name']) ?></p>
            
            <!-- Adresse -->
            <?php if (!empty($impressumAddress)): ?>
            <p class="mb-1"><?= htmlspecialchars($impressumAddress) ?></p>
            <?php endif; ?>
            
            <!-- USt-IdNr. -->
            <?php if (!empty($customer['tax_id'])): ?>
            <p class="mb-3">USt-IdNr.: <?= htmlspecialchars($customer['tax_id']) ?></p>
            <?php endif; ?>
            
            <!-- Kontakt (falls vorhanden) -->
            <?php if (!empty($customer['phone']) || !empty($customer['email'])): ?>
            <p class="mb-3">
                <?php if (!empty($customer['phone'])): ?>
                Tel: <?= htmlspecialchars($customer['phone']) ?>
                <?php endif; ?>
                <?php if (!empty($customer['phone']) && !empty($customer['email'])): ?> | <?php endif; ?>
                <?php if (!empty($customer['email'])): ?>
                E-Mail: <?= htmlspecialchars($customer['email']) ?>
                <?php endif; ?>
            </p>
            <?php endif; ?>
            
            <!-- Copyright und Links -->
            <p class="text-gray-500 mb-3">&copy; <?= date('Y') ?> <?= htmlspecialchars($customer['company_name']) ?>. Alle Rechte vorbehalten.</p>
            
            <div class="space-x-4">
                <a href="#impressum" class="hover:text-white">Impressum</a>
                <a href="#datenschutz" class="hover:text-white">Datenschutz</a>
            </div>
            
            <p class="mt-6 text-xs text-gray-600">
                Powered by <a href="https://empfehlungen.cloud" class="hover:text-gray-400">Leadbusiness</a>
            </p>
        </div>
    </footer>
    
    <!-- Impressum Modal -->
    <div id="impressumModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Impressum</h3>
                <button onclick="closeModal('impressumModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="text-gray-600 space-y-2">
                <p><strong><?= htmlspecialchars($customer['company_name']) ?></strong></p>
                
                <?php if (!empty($customer['contact_name'])): ?>
                <p>Vertreten durch: <?= htmlspecialchars($customer['contact_name']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($customer['address_street'])): ?>
                <p><?= htmlspecialchars($customer['address_street']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($customer['address_zip']) || !empty($customer['address_city'])): ?>
                <p><?= htmlspecialchars($customer['address_zip']) ?> <?= htmlspecialchars($customer['address_city']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($customer['phone'])): ?>
                <p class="mt-4">Telefon: <?= htmlspecialchars($customer['phone']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($customer['email'])): ?>
                <p>E-Mail: <?= htmlspecialchars($customer['email']) ?></p>
                <?php endif; ?>
                
                <?php if (!empty($customer['tax_id'])): ?>
                <p class="mt-4">USt-IdNr.: <?= htmlspecialchars($customer['tax_id']) ?></p>
                <?php endif; ?>
            </div>
            
            <button onclick="closeModal('impressumModal')" class="mt-6 w-full px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                Schließen
            </button>
        </div>
    </div>
    
    <!-- Datenschutz Modal -->
    <div id="datenschutzModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 max-h-[80vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold">Datenschutzerklärung</h3>
                <button onclick="closeModal('datenschutzModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="text-gray-600 space-y-4 text-sm">
                <p><strong>Verantwortlich für die Datenverarbeitung:</strong></p>
                <p><?= htmlspecialchars($customer['company_name']) ?><br>
                   <?= htmlspecialchars($impressumAddress) ?></p>
                
                <p><strong>Erhobene Daten:</strong></p>
                <p>Im Rahmen des Empfehlungsprogramms werden folgende Daten erhoben: Name (optional), E-Mail-Adresse, IP-Adresse (anonymisiert), Zeitstempel der Anmeldung.</p>
                
                <p><strong>Zweck der Verarbeitung:</strong></p>
                <p>Die Daten werden ausschließlich zur Durchführung des Empfehlungsprogramms verwendet, um Ihnen Ihren persönlichen Empfehlungslink bereitzustellen und Belohnungen zuzuweisen.</p>
                
                <p><strong>Ihre Rechte:</strong></p>
                <p>Sie haben das Recht auf Auskunft, Berichtigung, Löschung und Einschränkung der Verarbeitung Ihrer personenbezogenen Daten. Kontaktieren Sie uns unter: <?= htmlspecialchars($customer['email']) ?></p>
                
                <p><strong>E-Mail-Kommunikation:</strong></p>
                <p>Sie können sich jederzeit von unseren E-Mails abmelden, indem Sie den Abmeldelink in jeder E-Mail nutzen.</p>
            </div>
            
            <button onclick="closeModal('datenschutzModal')" class="mt-6 w-full px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                Schließen
            </button>
        </div>
    </div>
    
    <script>
        // Form Submit
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('submitBtn');
            const msg = document.getElementById('formMessage');
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet...';
            
            try {
                const formData = new FormData(this);
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    msg.className = 'mt-4 p-4 bg-green-100 text-green-700 rounded-xl';
                    msg.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
                    msg.classList.remove('hidden');
                    
                    // Confetti!
                    confetti({
                        particleCount: 100,
                        spread: 70,
                        origin: { y: 0.6 }
                    });
                    
                    this.reset();
                } else {
                    throw new Error(data.error || 'Ein Fehler ist aufgetreten.');
                }
            } catch (error) {
                msg.className = 'mt-4 p-4 bg-red-100 text-red-700 rounded-xl';
                msg.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + error.message;
                msg.classList.remove('hidden');
            }
            
            btn.disabled = false;
            btn.innerHTML = '<span>Jetzt Empfehler werden</span><i class="fas fa-arrow-right"></i>';
        });
        
        // Modal Links
        document.querySelectorAll('a[href="#impressum"]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                openModal('impressumModal');
            });
        });
        
        document.querySelectorAll('a[href="#datenschutz"]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                openModal('datenschutzModal');
            });
        });
        
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
        }
        
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }
        
        // Modal schließen bei Klick außerhalb
        document.querySelectorAll('#impressumModal, #datenschutzModal').forEach(modal => {
            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    closeModal(modal.id);
                }
            });
        });
    </script>
    
</body>
</html>
<?php

// Helper: Farbe anpassen
function adjustColor($hex, $percent) {
    $hex = ltrim($hex, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}
