<?php
/**
 * Lead Login Page
 * 
 * Login via:
 * - Magic Link (E-Mail)
 * - Passwort (optional)
 */

// WICHTIG: Zuerst prüfen ob wir auf der Hauptdomain sind
// BEVOR wir Database-Verbindungen etc. laden
$host = $_SERVER['HTTP_HOST'] ?? '';
$host = strtolower(preg_replace('/^www\./', '', $host));

// Hauptdomains ohne Subdomain -> Info-Seite
$mainDomains = ['empfehlungen.cloud', 'empfohlen.de', 'leadbusiness.de'];
if (in_array($host, $mainDomains)) {
    showInfoPage();
    exit;
}

// Jetzt erst die Dependencies laden
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/services/LeadAuthService.php';
require_once __DIR__ . '/../../includes/DomainResolver.php';

use Leadbusiness\DomainResolver;

$db = Database::getInstance();
$auth = new LeadAuthService();

// Schon eingeloggt?
$lead = $auth->check();
if ($lead) {
    header('Location: /lead/dashboard.php');
    exit;
}

// Kunde ermitteln (über Subdomain)
$customer = DomainResolver::init();

// Wenn kein Kunde gefunden -> Info-Seite anzeigen
if (!$customer) {
    showInfoPage();
    exit;
}

$error = '';
$success = '';
$showPasswordForm = false;

// Form Handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $action = $_POST['action'] ?? 'magic_link';
    
    if ($action === 'magic_link') {
        // Magic Link senden
        $result = $auth->sendMagicLink($email, $customer['id']);
        
        if ($result['success']) {
            $success = 'Ein Login-Link wurde an Ihre E-Mail-Adresse gesendet. Bitte prüfen Sie Ihr Postfach.';
        } else {
            $error = $result['error'];
        }
    } elseif ($action === 'password') {
        // Passwort-Login
        $result = $auth->loginWithPassword($email, $password, $customer['id']);
        
        if ($result['success']) {
            header('Location: ' . ($result['redirect'] ?? '/lead/dashboard.php'));
            exit;
        } else {
            $error = $result['error'];
            $showPasswordForm = true;
        }
    } elseif ($action === 'check_password') {
        // Prüfen ob Lead Passwort hat
        $leadCheck = $db->fetch(
            "SELECT l.password_hash FROM leads l
             JOIN campaigns c ON l.campaign_id = c.id
             WHERE l.email = ? AND c.customer_id = ? AND l.status = 'active'",
            [$email, $customer['id']]
        );
        
        if ($leadCheck && !empty($leadCheck['password_hash'])) {
            $showPasswordForm = true;
        } else {
            // Kein Passwort -> Magic Link senden
            $result = $auth->sendMagicLink($email, $customer['id']);
            if ($result['success']) {
                $success = 'Ein Login-Link wurde an Ihre E-Mail-Adresse gesendet.';
            } else {
                $error = $result['error'];
            }
        }
    }
}

$primaryColor = $customer['primary_color'] ?? '#667eea';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmelden | <?= htmlspecialchars($customer['company_name']) ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?= htmlspecialchars($primaryColor) ?>'
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        
        <!-- Logo/Header -->
        <div class="text-center mb-8">
            <?php if (!empty($customer['logo_url'])): ?>
            <img src="<?= htmlspecialchars($customer['logo_url']) ?>" 
                 alt="<?= htmlspecialchars($customer['company_name']) ?>" 
                 class="h-12 mx-auto mb-4">
            <?php else: ?>
            <h1 class="text-2xl font-bold text-gray-900 mb-2"><?= htmlspecialchars($customer['company_name']) ?></h1>
            <?php endif; ?>
            <p class="text-gray-500">Empfehlungsprogramm - Anmelden</p>
        </div>
        
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            
            <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div>
                        <p class="font-medium">E-Mail gesendet!</p>
                        <p class="text-sm"><?= htmlspecialchars($success) ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <?php if (!$showPasswordForm): ?>
            <!-- E-Mail Eingabe -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="check_password">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        E-Mail-Adresse
                    </label>
                    <input type="email" name="email" required autofocus
                           placeholder="ihre@email.de"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
                
                <button type="submit" 
                        class="w-full py-3 bg-primary text-white font-semibold rounded-xl hover:opacity-90 transition">
                    Weiter
                </button>
            </form>
            
            <?php else: ?>
            <!-- Passwort Eingabe -->
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="password">
                <input type="hidden" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                
                <div class="text-center mb-4">
                    <p class="text-gray-600">Anmelden als</p>
                    <p class="font-semibold text-gray-900"><?= htmlspecialchars($_POST['email'] ?? '') ?></p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Passwort
                    </label>
                    <input type="password" name="password" required autofocus
                           placeholder="••••••••"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                </div>
                
                <button type="submit" 
                        class="w-full py-3 bg-primary text-white font-semibold rounded-xl hover:opacity-90 transition">
                    Anmelden
                </button>
                
                <div class="text-center">
                    <button type="button" onclick="sendMagicLink()" class="text-sm text-primary hover:underline">
                        <i class="fas fa-envelope mr-1"></i>
                        Stattdessen Login-Link per E-Mail senden
                    </button>
                </div>
            </form>
            
            <form id="magicLinkForm" method="POST" style="display: none;">
                <input type="hidden" name="action" value="magic_link">
                <input type="hidden" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </form>
            
            <script>
                function sendMagicLink() {
                    document.getElementById('magicLinkForm').submit();
                }
            </script>
            <?php endif; ?>
            
            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-4 bg-white text-gray-500">oder</span>
                </div>
            </div>
            
            <!-- Magic Link Button -->
            <?php if (!$showPasswordForm): ?>
            <form method="POST">
                <input type="hidden" name="action" value="magic_link">
                <input type="hidden" name="email" id="magicLinkEmail" value="">
                
                <button type="button" onclick="requestMagicLink()" 
                        class="w-full py-3 border-2 border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition flex items-center justify-center gap-2">
                    <i class="fas fa-magic text-primary"></i>
                    Login-Link per E-Mail anfordern
                </button>
            </form>
            
            <script>
                function requestMagicLink() {
                    const email = document.querySelector('input[name="email"]').value;
                    if (!email) {
                        alert('Bitte geben Sie Ihre E-Mail-Adresse ein.');
                        return;
                    }
                    document.getElementById('magicLinkEmail').value = email;
                    document.getElementById('magicLinkEmail').closest('form').submit();
                }
            </script>
            <?php endif; ?>
            
        </div>
        
        <!-- Info -->
        <div class="mt-6 text-center text-sm text-gray-500">
            <p>Noch nicht registriert?</p>
            <a href="https://<?= htmlspecialchars($customer['subdomain']) ?>.empfehlungen.cloud/r/" 
               class="text-primary hover:underline">
                Jetzt am Empfehlungsprogramm teilnehmen
            </a>
        </div>
        
        <!-- Footer -->
        <div class="mt-8 text-center text-xs text-gray-400">
            <a href="/r/datenschutz.php" class="hover:text-gray-600">Datenschutz</a>
            <span class="mx-2">•</span>
            <span>Powered by <a href="https://empfehlungen.cloud" class="hover:text-primary">empfehlungen.cloud</a></span>
        </div>
        
    </div>
    
</body>
</html>
<?php

/**
 * Zeigt die Info-Seite für Besucher auf der Hauptdomain
 * (ohne Database-Abhängigkeiten)
 */
function showInfoPage() {
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Portal | empfehlungen.cloud</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-users text-3xl text-indigo-600"></i>
        </div>
        
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Empfehler-Portal</h1>
        <p class="text-gray-500 mb-6">Zugang zu Ihrem Empfehlungs-Dashboard</p>
        
        <div class="bg-gray-50 rounded-xl p-6 mb-6 text-left">
            <h2 class="font-semibold text-gray-900 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle text-indigo-500"></i>
                So funktioniert's
            </h2>
            <p class="text-sm text-gray-600 mb-4">
                Das Empfehler-Portal ist für jeden Kunden individuell. Um sich einzuloggen, benötigen Sie den Link des jeweiligen Unternehmens.
            </p>
            
            <div class="bg-white border border-gray-200 rounded-lg p-3">
                <p class="text-xs text-gray-500 mb-1">Beispiel-URL:</p>
                <code class="text-sm text-indigo-600 break-all">
                    https://firmenname.empfehlungen.cloud/lead/
                </code>
            </div>
        </div>
        
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mb-6 text-left">
            <div class="flex items-start gap-3">
                <i class="fas fa-lightbulb text-amber-500 mt-0.5"></i>
                <div class="text-sm text-amber-800">
                    <p class="font-medium mb-1">Haben Sie einen Login-Link per E-Mail erhalten?</p>
                    <p>Klicken Sie einfach auf den Link in der E-Mail, um direkt zu Ihrem Dashboard zu gelangen.</p>
                </div>
            </div>
        </div>
        
        <div class="space-y-3">
            <a href="/" class="block w-full py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                <i class="fas fa-home mr-2"></i>Zur Startseite
            </a>
            <a href="/kontakt.php" class="block w-full py-3 border-2 border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition">
                <i class="fas fa-envelope mr-2"></i>Kontakt aufnehmen
            </a>
        </div>
        
        <div class="mt-8 pt-6 border-t border-gray-100">
            <p class="text-sm text-gray-500 mb-3">
                Sie sind Unternehmer und möchten Ihr eigenes Empfehlungsprogramm starten?
            </p>
            <a href="/preise.php" class="inline-flex items-center gap-2 text-indigo-600 font-medium hover:underline">
                Jetzt starten <i class="fas fa-arrow-right text-sm"></i>
            </a>
        </div>
    </div>
</body>
</html>
    <?php
}
