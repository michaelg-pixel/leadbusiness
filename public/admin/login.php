<?php
/**
 * Admin Login
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

// Bereits eingeloggt?
if (isset($_SESSION['admin_id'])) {
    redirect('/admin/');
}

$error = '';
$email = '';

// Login verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Bitte alle Felder ausfüllen.';
    } else {
        $db = db();
        $admin = $db->fetch(
            "SELECT * FROM admin_users WHERE email = ? AND is_active = 1",
            [$email]
        );
        
        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Login erfolgreich
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            $_SESSION['admin_email'] = $admin['email'];
            
            // Last Login aktualisieren
            $db->execute(
                "UPDATE admin_users SET last_login_at = NOW() WHERE id = ?",
                [$admin['id']]
            );
            
            // Remember Me Cookie
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                setcookie('admin_remember', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                $db->execute(
                    "UPDATE admin_users SET remember_token = ? WHERE id = ?",
                    [hash('sha256', $token), $admin['id']]
                );
            }
            
            redirect('/admin/');
        } else {
            $error = 'Ungültige Anmeldedaten.';
            error_log("Failed admin login attempt for: " . $email);
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
    <title>Admin Login - Leadbusiness</title>
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
        .gradient-bg { background: linear-gradient(135deg, #0c4a6e 0%, #1e3a5f 50%, #0f172a 100%); }
        .dark .gradient-bg { background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    
    <button onclick="toggleTheme()" class="fixed top-4 right-4 p-3 rounded-full bg-white/10 backdrop-blur-sm text-white hover:bg-white/20 transition-all">
        <i class="fas fa-moon dark:hidden"></i>
        <i class="fas fa-sun hidden dark:inline"></i>
    </button>
    
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/10 backdrop-blur-sm rounded-2xl mb-4">
                <i class="fas fa-shield-halved text-3xl text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Leadbusiness</h1>
            <p class="text-white/60 mt-1">Admin-Bereich</p>
        </div>
        
        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl p-8">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-white mb-6">Anmelden</h2>
            
            <?php if ($error): ?>
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
                <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">E-Mail-Adresse</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" value="<?= e($email) ?>" required
                               class="w-full pl-10 pr-4 py-3 border border-slate-200 dark:border-slate-600 rounded-lg 
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="admin@example.com">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Passwort</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" required id="password"
                               class="w-full pl-10 pr-12 py-3 border border-slate-200 dark:border-slate-600 rounded-lg 
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-primary-600 border-slate-300 rounded focus:ring-primary-500">
                        <span class="ml-2 text-sm text-slate-600 dark:text-slate-400">Angemeldet bleiben</span>
                    </label>
                    <a href="/admin/forgot-password.php" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400">Passwort vergessen?</a>
                </div>
                
                <button type="submit" class="w-full py-3 px-4 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-primary-600/30">
                    <i class="fas fa-sign-in-alt"></i>Anmelden
                </button>
            </form>
        </div>
        
        <p class="text-center text-white/40 text-sm mt-6">&copy; <?= date('Y') ?> Leadbusiness. Alle Rechte vorbehalten.</p>
    </div>
    
    <script>
        function toggleTheme() {
            const html = document.documentElement;
            html.classList.toggle('dark');
            document.cookie = `admin_theme=${html.classList.contains('dark') ? 'dark' : 'light'};path=/;max-age=31536000`;
        }
        
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('toggleIcon');
            input.type = input.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    </script>
</body>
</html>
