<?php
/**
 * Admin Broadcast Unsubscribe
 * 
 * Abmelde-Seite für Admin-Broadcasts
 */

require_once __DIR__ . '/../../includes/init.php';

$db = db();
$email = sanitize($_GET['email'] ?? $_POST['email'] ?? '');
$token = sanitize($_GET['token'] ?? $_POST['token'] ?? '');
$success = false;
$error = '';

// Token validieren
function validateToken($email, $token) {
    $expected = md5($email . 'leadbusiness_unsubscribe_salt');
    return hash_equals($expected, $token);
}

// Abmeldung verarbeiten
if (isPost() && isset($_POST['confirm_unsubscribe'])) {
    if (!$email || !$token) {
        $error = 'Ungültige Anfrage.';
    } elseif (!validateToken($email, $token)) {
        $error = 'Ungültiger Token.';
    } else {
        // Prüfen ob bereits abgemeldet
        $existing = $db->fetch("SELECT id FROM admin_broadcast_unsubscribes WHERE email = ?", [$email]);
        
        if (!$existing) {
            $db->execute("
                INSERT INTO admin_broadcast_unsubscribes (email, unsubscribed_at, ip_address, user_agent)
                VALUES (?, NOW(), ?, ?)
            ", [
                $email,
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
        }
        
        $success = true;
    }
}

// Token-Validierung für GET-Request
$validRequest = $email && $token && validateToken($email, $token);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter abmelden - Leadbusiness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-8">
        <?php if ($success): ?>
            <!-- Erfolgreich abgemeldet -->
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-check text-green-500 text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 mb-4">Erfolgreich abgemeldet</h1>
                <p class="text-slate-600 mb-6">
                    Sie wurden erfolgreich von unserem Newsletter abgemeldet und erhalten keine weiteren E-Mails von uns.
                </p>
                <p class="text-sm text-slate-500">
                    <strong><?= e($email) ?></strong>
                </p>
            </div>
            
        <?php elseif ($error): ?>
            <!-- Fehler -->
            <div class="text-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-times text-red-500 text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 mb-4">Fehler</h1>
                <p class="text-slate-600 mb-6"><?= e($error) ?></p>
                <a href="/" class="text-primary-600 hover:underline">Zur Startseite</a>
            </div>
            
        <?php elseif ($validRequest): ?>
            <!-- Bestätigungsformular -->
            <div class="text-center">
                <div class="w-20 h-20 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-envelope-open text-amber-500 text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 mb-4">Newsletter abmelden</h1>
                <p class="text-slate-600 mb-6">
                    Möchten Sie sich wirklich vom Leadbusiness Newsletter abmelden?
                </p>
                <p class="text-sm text-slate-500 mb-6">
                    <strong><?= e($email) ?></strong>
                </p>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="email" value="<?= e($email) ?>">
                    <input type="hidden" name="token" value="<?= e($token) ?>">
                    
                    <button type="submit" name="confirm_unsubscribe" value="1" 
                            class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-all">
                        <i class="fas fa-user-minus mr-2"></i>Ja, abmelden
                    </button>
                    
                    <a href="/" class="block text-slate-500 hover:text-slate-700 text-sm">
                        Abbrechen
                    </a>
                </form>
            </div>
            
        <?php else: ?>
            <!-- Ungültige Anfrage -->
            <div class="text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-question text-slate-400 text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 mb-4">Ungültiger Link</h1>
                <p class="text-slate-600 mb-6">
                    Dieser Abmelde-Link ist ungültig oder abgelaufen. Bitte nutzen Sie den Link aus der E-Mail.
                </p>
                <a href="/" class="text-primary-600 hover:underline">Zur Startseite</a>
            </div>
        <?php endif; ?>
        
        <div class="mt-8 pt-6 border-t border-slate-200 text-center">
            <p class="text-xs text-slate-400">
                <a href="https://empfehlungen.cloud" class="hover:underline">Leadbusiness</a> • 
                <a href="/datenschutz" class="hover:underline">Datenschutz</a> • 
                <a href="/impressum" class="hover:underline">Impressum</a>
            </p>
        </div>
    </div>
</body>
</html>
