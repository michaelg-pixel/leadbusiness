<?php
/**
 * Leadbusiness - E-Mail-Bestätigung (Double Opt-In)
 * 
 * URL: /confirm/{token}
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/services/GamificationService.php';
require_once __DIR__ . '/../includes/services/MailgunService.php';

$db = Database::getInstance();

// Token aus URL
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$token = '';

if (preg_match('/\/confirm\/([a-zA-Z0-9]+)/', $path, $matches)) {
    $token = $matches[1];
}

$error = '';
$success = false;
$lead = null;
$customer = null;

if (empty($token)) {
    $error = 'Ungültiger Bestätigungslink.';
} else {
    // Lead mit Token finden
    $lead = $db->fetch(
        "SELECT l.*, c.company_name, c.subdomain, c.logo_url, c.primary_color,
                ca.id as campaign_id
         FROM leads l
         JOIN campaigns ca ON l.campaign_id = ca.id
         JOIN customers c ON ca.customer_id = c.id
         WHERE l.confirmation_token = ?",
        [$token]
    );
    
    if (!$lead) {
        $error = 'Ungültiger oder abgelaufener Bestätigungslink.';
    } elseif ($lead['email_confirmed']) {
        $error = 'Ihre E-Mail-Adresse wurde bereits bestätigt.';
        $success = true; // Technisch erfolgreich, nur schon erledigt
    } else {
        // E-Mail bestätigen
        $db->update('leads', [
            'status' => 'active',
            'email_confirmed' => 1,
            'email_confirmed_at' => date('Y-m-d H:i:s'),
            'confirmation_token' => null
        ], 'id = ?', [$lead['id']]);
        
        // Wenn Referrer vorhanden, Conversion bestätigen
        if ($lead['referred_by_id']) {
            $db->query(
                "UPDATE conversions SET status = 'confirmed', confirmed_at = NOW() 
                 WHERE lead_id = ? AND status = 'pending'",
                [$lead['id']]
            );
            
            // Gamification für Referrer
            $gamification = new GamificationService();
            
            // Prüfen ob erste Conversion
            $referrerConversions = $db->fetch(
                "SELECT conversions FROM leads WHERE id = ?",
                [$lead['referred_by_id']]
            );
            $isFirst = ($referrerConversions['conversions'] ?? 0) == 0;
            
            $gamification->processConversion($lead['referred_by_id'], $isFirst);
            
            // Kunden-Stats aktualisieren
            $db->query(
                "UPDATE customers SET total_conversions = total_conversions + 1 
                 WHERE id = (SELECT customer_id FROM campaigns WHERE id = ?)",
                [$lead['campaign_id']]
            );
        }
        
        // Willkommens-E-Mail senden
        $mailgun = new MailgunService();
        $mailgun->queue(
            $lead['customer_id'],
            $lead['email'],
            $lead['name'],
            'welcome',
            [
                'lead_name' => $lead['name'] ?: 'Empfehler',
                'company_name' => $lead['company_name'],
                'referral_code' => $lead['referral_code'],
                'referral_link' => "https://{$lead['subdomain']}.empfohlen.de/r/{$lead['referral_code']}",
                'dashboard_link' => "https://{$lead['subdomain']}.empfohlen.de/lead"
            ],
            10
        );
        
        $success = true;
        $customer = [
            'company_name' => $lead['company_name'],
            'subdomain' => $lead['subdomain'],
            'logo_url' => $lead['logo_url'],
            'primary_color' => $lead['primary_color']
        ];
    }
}

$pageTitle = $success ? 'E-Mail bestätigt!' : 'Fehler bei der Bestätigung';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
    
    <div class="max-w-md w-full text-center">
        
        <?php if ($success && $customer): ?>
        
        <!-- Erfolg -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <?php if ($customer['logo_url']): ?>
            <img src="<?= htmlspecialchars($customer['logo_url']) ?>" 
                 alt="<?= htmlspecialchars($customer['company_name']) ?>" 
                 class="h-12 mx-auto mb-6">
            <?php endif; ?>
            
            <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-check text-4xl text-green-500"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-4">E-Mail bestätigt! 🎉</h1>
            
            <p class="text-gray-600 mb-6">
                Vielen Dank! Sie sind jetzt offiziell Empfehler bei 
                <strong><?= htmlspecialchars($customer['company_name']) ?></strong>.
            </p>
            
            <?php if ($lead && $lead['referral_code']): ?>
            <div class="bg-gray-50 rounded-xl p-4 mb-6">
                <p class="text-sm text-gray-500 mb-2">Ihr persönlicher Empfehlungslink:</p>
                <div class="flex items-center gap-2 bg-white rounded-lg px-4 py-2 border">
                    <code class="text-sm text-primary-600 flex-1 truncate">
                        <?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de/r/<?= htmlspecialchars($lead['referral_code']) ?>
                    </code>
                    <button onclick="copyLink()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
            
            <a href="https://<?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de/lead" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-xl transition-colors">
                <i class="fas fa-share-alt"></i>
                Jetzt teilen & Belohnungen sammeln
            </a>
        </div>
        
        <script>
            // Confetti!
            setTimeout(() => {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }, 300);
            
            function copyLink() {
                const link = '<?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de/r/<?= htmlspecialchars($lead['referral_code'] ?? '') ?>';
                navigator.clipboard.writeText('https://' + link);
                alert('Link kopiert!');
            }
        </script>
        
        <?php else: ?>
        
        <!-- Fehler -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="w-20 h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-times text-4xl text-red-500"></i>
            </div>
            
            <h1 class="text-2xl font-bold text-gray-900 mb-4">Fehler</h1>
            
            <p class="text-gray-600 mb-6">
                <?= htmlspecialchars($error) ?>
            </p>
            
            <a href="/" class="text-primary-500 hover:underline">
                ← Zurück zur Startseite
            </a>
        </div>
        
        <?php endif; ?>
        
    </div>
    
</body>
</html>
