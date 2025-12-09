<?php
/**
 * Leadbusiness - Kunden Login
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

// Bereits eingeloggt?
if ($auth->isLoggedIn() && $auth->getUserType() === 'customer') {
    redirect('/dashboard');
}

$error = '';
$success = '';

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

$pageTitle = 'Kunden-Login';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Leadbusiness</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 500: '#667eea', 600: '#5a67d8', 700: '#4c51bf' }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    
    <div class="w-full max-w-md">
        
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center gap-2">
                <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-purple-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-share-nodes text-white text-xl"></i>
                </div>
                <span class="text-2xl font-bold text-gray-900">Leadbusiness</span>
            </a>
        </div>
        
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h1 class="text-2xl font-bold text-gray-900 mb-2 text-center">Kunden-Login</h1>
            <p class="text-gray-500 text-center mb-6">Melden Sie sich in Ihrem Dashboard an.</p>
            
            <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl">
                <i class="fas fa-check-circle mr-2"></i>
                <?= htmlspecialchars($success) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">E-Mail-Adresse</label>
                    <input type="email" name="email" required autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="ihre@email.de"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Passwort</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                        placeholder="••••••••">
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary-500 rounded border-gray-300">
                        <span class="text-sm text-gray-600">Angemeldet bleiben</span>
                    </label>
                    <a href="/dashboard/forgot-password.php" class="text-sm text-primary-500 hover:underline">
                        Passwort vergessen?
                    </a>
                </div>
                
                <button type="submit"
                    class="w-full py-3 bg-primary-500 hover:bg-primary-600 text-white font-semibold rounded-xl transition-colors">
                    Anmelden
                </button>
            </form>
        </div>
        
        <!-- Footer Links -->
        <div class="text-center mt-6 text-sm text-gray-500">
            <p>
                Noch kein Kunde? 
                <a href="/onboarding" class="text-primary-500 hover:underline">Jetzt starten</a>
            </p>
            <p class="mt-4">
                <a href="/" class="text-gray-400 hover:text-gray-600">← Zurück zur Startseite</a>
            </p>
        </div>
        
    </div>
    
</body>
</html>
