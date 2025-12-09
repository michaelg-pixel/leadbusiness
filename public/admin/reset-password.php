<?php
/**
 * Admin Passwort zurücksetzen
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';

session_start();

$token = $_GET['token'] ?? '';
$error = '';
$success = false;
$validToken = false;
$tokenData = null;

$db = Database::getInstance();

// Token prüfen
if (!empty($token)) {
    $tokenHash = hash('sha256', $token);
    $tokenData = $db->fetch(
        "SELECT * FROM password_reset_tokens 
         WHERE token = ? AND user_type = 'admin' AND expires_at > NOW() AND used_at IS NULL",
        [$tokenHash]
    );
    $validToken = !empty($tokenData);
}

// Passwort ändern
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    if (strlen($password) < 8) {
        $error = 'Das Passwort muss mindestens 8 Zeichen lang sein.';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Die Passwörter stimmen nicht überein.';
    } else {
        // Passwort aktualisieren
        $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $db->execute(
            "UPDATE admin_users SET password_hash = ?, updated_at = NOW() WHERE id = ?",
            [$passwordHash, $tokenData['user_id']]
        );
        
        // Token als verwendet markieren
        $db->execute(
            "UPDATE password_reset_tokens SET used_at = NOW() WHERE id = ?",
            [$tokenData['id']]
        );
        
        $success = true;
    }
}

$theme = $_COOKIE['admin_theme'] ?? 'light';
?>
<!DOCTYPE html>
<html lang="de" class="<?= $theme === 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neues Passwort - Leadbusiness Admin</title>
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
                <i class="fas fa-lock-open text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Neues Passwort</h1>
            <p class="text-white/60 mt-1">Legen Sie ein sicheres Passwort fest</p>
        </div>
        
        <!-- Card -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-8">
            
            <?php if ($success): ?>
            <!-- Erfolg -->
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full mb-4">
                    <i class="fas fa-check text-3xl text-green-600 dark:text-green-400"></i>
                </div>
                <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Passwort geändert!</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-6">
                    Ihr Passwort wurde erfolgreich aktualisiert. Sie können sich jetzt mit dem neuen Passwort anmelden.
                </p>
                <a href="/admin/login.php" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-all">
                    <i class="fas fa-sign-in-alt"></i>
                    Zum Login
                </a>
            </div>
            
            <?php elseif (!$validToken): ?>
            <!-- Ungültiger Token -->
            <div class="text-center py-6">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full mb-4">
                    <i class="fas fa-times text-3xl text-red-600 dark:text-red-400"></i>
                </div>
                <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-2">Link ungültig</h2>
                <p class="text-slate-600 dark:text-slate-400 mb-6">
                    Dieser Reset-Link ist ungültig oder abgelaufen. Bitte fordern Sie einen neuen Link an.
                </p>
                <a href="/admin/forgot-password.php" 
                   class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-all">
                    <i class="fas fa-redo"></i>
                    Neuen Link anfordern
                </a>
            </div>
            
            <?php else: ?>
            <!-- Formular -->
            
            <?php if ($error): ?>
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= e($error) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Neues Passwort
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" required minlength="8" id="password"
                               class="w-full pl-10 pr-12 py-3 border border-slate-200 dark:border-slate-600 rounded-lg 
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="Mindestens 8 Zeichen">
                        <button type="button" onclick="togglePassword('password', 'toggleIcon1')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fas fa-eye" id="toggleIcon1"></i>
                        </button>
                    </div>
                    <div class="mt-2" id="strength-meter">
                        <div class="h-1 bg-slate-200 dark:bg-slate-600 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300" id="strength-bar" style="width: 0%"></div>
                        </div>
                        <p class="text-xs mt-1 text-slate-500" id="strength-text"></p>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Passwort bestätigen
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password_confirm" required minlength="8" id="password_confirm"
                               class="w-full pl-10 pr-12 py-3 border border-slate-200 dark:border-slate-600 rounded-lg 
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="Passwort wiederholen">
                        <button type="button" onclick="togglePassword('password_confirm', 'toggleIcon2')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fas fa-eye" id="toggleIcon2"></i>
                        </button>
                    </div>
                    <p class="text-xs mt-1 text-red-500 hidden" id="match-error">Passwörter stimmen nicht überein</p>
                </div>
                
                <button type="submit" id="submit-btn"
                        class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg 
                               transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-primary-600/30
                               disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-save"></i>
                    Passwort speichern
                </button>
            </form>
            
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            document.cookie = `admin_theme=${html.classList.contains('dark') ? 'dark' : 'light'};path=/;max-age=31536000`;
        }
        
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
        
        // Passwort-Stärke prüfen
        document.getElementById('password')?.addEventListener('input', function(e) {
            const password = e.target.value;
            let strength = 0;
            
            if (password.length >= 8) strength += 25;
            if (password.length >= 12) strength += 15;
            if (/[a-z]/.test(password)) strength += 15;
            if (/[A-Z]/.test(password)) strength += 15;
            if (/[0-9]/.test(password)) strength += 15;
            if (/[^a-zA-Z0-9]/.test(password)) strength += 15;
            
            const bar = document.getElementById('strength-bar');
            const text = document.getElementById('strength-text');
            
            bar.style.width = strength + '%';
            
            if (strength < 30) {
                bar.className = 'h-full transition-all duration-300 bg-red-500';
                text.textContent = 'Sehr schwach';
                text.className = 'text-xs mt-1 text-red-500';
            } else if (strength < 50) {
                bar.className = 'h-full transition-all duration-300 bg-orange-500';
                text.textContent = 'Schwach';
                text.className = 'text-xs mt-1 text-orange-500';
            } else if (strength < 70) {
                bar.className = 'h-full transition-all duration-300 bg-yellow-500';
                text.textContent = 'Mittel';
                text.className = 'text-xs mt-1 text-yellow-500';
            } else if (strength < 90) {
                bar.className = 'h-full transition-all duration-300 bg-green-500';
                text.textContent = 'Stark';
                text.className = 'text-xs mt-1 text-green-500';
            } else {
                bar.className = 'h-full transition-all duration-300 bg-green-600';
                text.textContent = 'Sehr stark';
                text.className = 'text-xs mt-1 text-green-600';
            }
            
            checkMatch();
        });
        
        // Passwort-Übereinstimmung prüfen
        document.getElementById('password_confirm')?.addEventListener('input', checkMatch);
        
        function checkMatch() {
            const pw1 = document.getElementById('password')?.value;
            const pw2 = document.getElementById('password_confirm')?.value;
            const error = document.getElementById('match-error');
            const btn = document.getElementById('submit-btn');
            
            if (pw2 && pw1 !== pw2) {
                error?.classList.remove('hidden');
                btn?.setAttribute('disabled', 'disabled');
            } else {
                error?.classList.add('hidden');
                btn?.removeAttribute('disabled');
            }
        }
    </script>
</body>
</html>
