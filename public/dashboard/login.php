<?php
/**
 * Leadbusiness - Kunden Login
 * Mit Dark/Light Mode und Auto-Login via Token (für Onboarding)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/security/RateLimiter.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$auth = new Auth();
$db = \Leadbusiness\Database::getInstance();

// Bereits eingeloggt?
if ($auth->isLoggedIn() && $auth->getUserType() === 'customer') {
    $welcomeParam = isset($_GET['welcome']) ? '?welcome=1' : '';
    redirect('/dashboard' . $welcomeParam);
}

$error = '';
$success = '';

// ========================================
// AUTO-LOGIN via Token (nach Onboarding)
// ========================================
if (!empty($_GET['auto'])) {
    $autoToken = trim($_GET['auto']);
    
    // Token in DB suchen
    $customer = $db->fetch(
        "SELECT id, email, company_name, auto_login_expires 
         FROM customers 
         WHERE auto_login_token = ? AND auto_login_token IS NOT NULL",
        [$autoToken]
    );
    
    if ($customer) {
        // Token Ablauf prüfen
        $expiresAt = strtotime($customer['auto_login_expires'] ?? '');
        
        if ($expiresAt && $expiresAt > time()) {
            // Token gültig - Kunde einloggen
            $_SESSION['customer_id'] = $customer['id'];
            $_SESSION['customer_email'] = $customer['email'];
            $_SESSION['user_type'] = 'customer';
            
            // Token invalidieren (one-time use)
            $db->update('customers', [
                'auto_login_token' => null,
                'auto_login_expires' => null
            ], 'id = ?', [$customer['id']]);
            
            // Erfolgs-Logging
            error_log("Auto-Login successful for customer ID: {$customer['id']}");
            
            // Weiterleitung zum Dashboard
            $welcomeParam = isset($_GET['welcome']) ? '?welcome=1' : '';
            redirect('/dashboard' . $welcomeParam);
            exit;
        } else {
            // Token abgelaufen
            error_log("Auto-Login token expired for customer ID: {$customer['id']}");
            $error = 'Der Login-Link ist abgelaufen. Bitte melden Sie sich mit Ihren Zugangsdaten an.';
            
            // Abgelaufenen Token löschen
            $db->update('customers', [
                'auto_login_token' => null,
                'auto_login_expires' => null
            ], 'id = ?', [$customer['id']]);
        }
    } else {
        // Ungültiger Token
        error_log("Invalid auto-login token attempted: " . substr($autoToken, 0, 10) . "...");
        $error = 'Ungültiger Login-Link. Bitte melden Sie sich mit Ihren Zugangsdaten an.';
    }
}

// Login verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Rate Limiting
    $rateLimiter = new RateLimiter();
    $clientIp = getClientIp();
    
    if (!$rateLimiter->checkLimit('login_attempts', hashIp($clientIp))) {
        $error = 'Zu viele Login-Versuche. Bitte warten Sie 15 Minuten.';
    } else {
        $result = $auth->loginCustomer($email, $password);
        
        if ($result['success']) {
            redirect('/dashboard');
        } else {
            $error = $result['error'];
        }
    }
}

// CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Theme aus Cookie
$theme = $_COOKIE['dashboard_theme'] ?? 'light';

// Welcome-Hinweis wenn von Onboarding kommend aber Token ungültig
$showWelcomeHint = isset($_GET['welcome']) && $error;
?>
<!DOCTYPE html>
<html lang="de" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kunden-Login | Leadbusiness</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc',
                            400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1',
                            800: '#075985', 900: '#0c4a6e', 950: '#082f49'
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #0c4a6e 0%, #1e3a5f 50%, #0f172a 100%);
        }
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center px-4">
    
    <!-- Theme Toggle -->
    <button onclick="toggleTheme()" class="fixed top-4 right-4 p-3 rounded-full bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 transition-all">
        <i class="fas fa-moon dark:hidden"></i>
        <i class="fas fa-sun hidden dark:inline"></i>
    </button>
    
    <div class="w-full max-w-md">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="w-12 h-12 bg-white/10 backdrop-blur-sm rounded-xl flex items-center justify-center">
                    <i class="fas fa-share-nodes text-white text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-white">Leadbusiness</span>
            </a>
            <p class="text-white/60 mt-2">Kunden-Dashboard</p>
        </div>
        
        <!-- Login Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-8">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2 text-center">Anmelden</h1>
            <p class="text-slate-500 dark:text-slate-400 text-center mb-6">
                <?= $showWelcomeHint ? 'Bitte melden Sie sich an, um Ihr Dashboard zu sehen.' : 'Willkommen zurück!' ?>
            </p>
            
            <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= e($error) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($showWelcomeHint): ?>
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl">
                <i class="fas fa-check-circle mr-2"></i>
                Ihr Konto wurde erfolgreich erstellt! Melden Sie sich jetzt mit Ihrer E-Mail und dem gewählten Passwort an.
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">E-Mail-Adresse</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" required autofocus
                            class="w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-slate-600 rounded-xl 
                                   bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                   focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="ihre@email.de"
                            value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Passwort</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required id="password"
                            class="w-full pl-10 pr-12 py-3 border border-slate-200 dark:border-slate-600 rounded-xl 
                                   bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                   focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" 
                               class="w-4 h-4 text-primary-500 rounded border-slate-300 dark:border-slate-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Angemeldet bleiben</span>
                    </label>
                    <a href="/dashboard/forgot-password.php" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">
                        Passwort vergessen?
                    </a>
                </div>
                
                <button type="submit"
                    class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl 
                           transition-all shadow-lg shadow-primary-600/30 flex items-center justify-center gap-2">
                    <i class="fas fa-sign-in-alt"></i>
                    Anmelden
                </button>
            </form>
        </div>
        
        <!-- Footer Links -->
        <div class="text-center mt-6 text-sm text-white/60">
            <p>
                Noch kein Kunde? 
                <a href="/onboarding" class="text-white hover:underline">Jetzt starten</a>
            </p>
            <p class="mt-4">
                <a href="/" class="text-white/40 hover:text-white/70">← Zurück zur Startseite</a>
            </p>
        </div>
        
    </div>
    
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            html.classList.toggle('dark');
            document.cookie = `dashboard_theme=${isDark ? 'light' : 'dark'};path=/;max-age=31536000`;
        }
        
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>
