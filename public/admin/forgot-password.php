<?php
/**
 * Admin Passwort vergessen
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/mailgun.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/services/MailgunService.php';

session_start();

$success = false;
$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    } else {
        $db = Database::getInstance();
        $admin = $db->fetch(
            "SELECT id, name, email FROM admin_users WHERE email = ? AND is_active = 1",
            [$email]
        );
        
        // Immer Erfolg zeigen (verhindert E-Mail-Enumeration)
        $success = true;
        
        if ($admin) {
            // Alte Tokens löschen
            $db->execute(
                "DELETE FROM password_reset_tokens WHERE user_type = 'admin' AND user_id = ?",
                [$admin['id']]
            );
            
            // Neuen Token erstellen
            $token = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $db->execute(
                "INSERT INTO password_reset_tokens (user_type, user_id, email, token, expires_at) VALUES (?, ?, ?, ?, ?)",
                ['admin', $admin['id'], $admin['email'], hash('sha256', $token), $expiresAt]
            );
            
            // E-Mail senden
            $resetLink = SITE_URL . '/admin/reset-password.php?token=' . $token;
            
            $mailgun = new MailgunService();
            $mailgun->send(
                $admin['email'],
                'Passwort zurücksetzen - Leadbusiness Admin',
                "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: linear-gradient(135deg, #0c4a6e 0%, #1e3a5f 100%); padding: 30px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>🔐 Passwort zurücksetzen</h1>
                    </div>
                    <div style='background: #f8fafc; padding: 30px;'>
                        <p>Hallo {$admin['name']},</p>
                        <p>Sie haben angefordert, Ihr Passwort zurückzusetzen. Klicken Sie auf den Button unten, um ein neues Passwort festzulegen:</p>
                        <div style='text-align: center; margin: 30px 0;'>
                            <a href='{$resetLink}' style='background: #0284c7; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block;'>
                                Neues Passwort festlegen
                            </a>
                        </div>
                        <p style='color: #64748b; font-size: 14px;'>Dieser Link ist 1 Stunde gültig.</p>
                        <p style='color: #64748b; font-size: 14px;'>Falls Sie diese Anfrage nicht gestellt haben, ignorieren Sie diese E-Mail einfach.</p>
                        <hr style='border: none; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                        <p style='color: #94a3b8; font-size: 12px;'>
                            Oder kopieren Sie diesen Link:<br>
                            <a href='{$resetLink}' style='color: #0284c7;'>{$resetLink}</a>
                        </p>
                    </div>
                </div>
                ",
                $admin['name']
            );
        }
    }
}

$theme = $_COOKIE['admin_theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="de" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passwort vergessen - Leadbusiness Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #0c4a6e 0%, #1e3a5f 50%, #0f172a 100%);
        }
        .dark .gradient-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    
    <!-- Theme Toggle -->
    <button onclick="toggleTheme()" class="fixed top-4 right-4 p-3 rounded-full bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 transition-all">
        <i class="fas fa-moon dark:hidden"></i>
        <i class="fas fa-sun hidden dark:inline"></i>
    </button>
    
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl mb-4">
                <i class="fas fa-key text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Passwort vergessen</h1>
            <p class="text-white/60 mt-1">Wir senden Ihnen einen Reset-Link</p>
        </div>
        
        <!-- Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-8">
            
            <?php if ($success): ?>
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-4">
                    <i class="fas fa-check text-3xl text-green-600 dark:text-green-400"></i>
                </div>
                <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">E-Mail gesendet!</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-6">
                    Falls ein Konto mit dieser E-Mail existiert, haben wir Ihnen einen Link zum Zurücksetzen des Passworts gesendet.
                </p>
                <a href="/admin/login.php" 
                   class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 dark:text-primary-400">
                    <i class="fas fa-arrow-left"></i>
                    Zurück zum Login
                </a>
            </div>
            
            <?php else: ?>
            
            <?php if ($error): ?>
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= e($error) ?>
            </div>
            <?php endif; ?>
            
            <p class="text-slate-600 dark:text-slate-400 mb-6">
                Geben Sie Ihre E-Mail-Adresse ein und wir senden Ihnen einen Link zum Zurücksetzen Ihres Passworts.
            </p>
            
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        E-Mail-Adresse
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" value="<?= e($email) ?>" required autofocus
                               class="w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-slate-600 rounded-lg 
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="admin@example.com">
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg 
                               transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-primary-600/30">
                    <i class="fas fa-paper-plane"></i>
                    Reset-Link senden
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <a href="/admin/login.php" class="text-sm text-slate-600 dark:text-slate-400 hover:text-primary-600">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Zurück zum Login
                </a>
            </div>
            
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            html.classList.toggle('dark');
            document.cookie = `admin_theme=${isDark ? 'light' : 'dark'};path=/;max-age=31536000`;
        }
    </script>
</body>
</html>
