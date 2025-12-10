<?php
/**
 * Leadbusiness - API Dashboard
 * 
 * API-Key Management für Professional und Enterprise Kunden
 * Enterprise: E-Mail-Versand Kontrolle
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/ApiHandler.php';
require_once __DIR__ . '/../../includes/helpers.php';

use Leadbusiness\Database;
use Leadbusiness\ApiHandler;

// Auth prüfen
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

// Plan-Check
$hasApiAccess = in_array($customer['plan'], ['professional', 'enterprise']);
$isEnterprise = $customer['plan'] === 'enterprise';

// Erfolgsmeldung
$success = '';
$error = '';

// API-Key und E-Mail Aktionen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $hasApiAccess) {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';
    
    if ($csrf !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Ungültige Anfrage. Bitte versuchen Sie es erneut.';
    } else {
        switch ($action) {
            case 'generate':
                $apiKey = ApiHandler::generateApiKey();
                $apiSecret = ApiHandler::generateApiSecret();
                
                $db->query(
                    "UPDATE customers SET api_key = ?, api_secret = ?, api_enabled = 1 WHERE id = ?",
                    [$apiKey, $apiSecret, $customerId]
                );
                
                $customer['api_key'] = $apiKey;
                $customer['api_secret'] = $apiSecret;
                $customer['api_enabled'] = 1;
                
                $success = 'API-Schlüssel wurden erfolgreich generiert. Speichern Sie den Secret sicher ab - er wird nur einmal angezeigt!';
                $_SESSION['show_secret'] = $apiSecret;
                break;
                
            case 'regenerate':
                $apiKey = ApiHandler::generateApiKey();
                
                $db->query(
                    "UPDATE customers SET api_key = ? WHERE id = ?",
                    [$apiKey, $customerId]
                );
                
                $customer['api_key'] = $apiKey;
                $success = 'API-Key wurde erfolgreich neu generiert.';
                break;
                
            case 'regenerate_secret':
                $apiSecret = ApiHandler::generateApiSecret();
                
                $db->query(
                    "UPDATE customers SET api_secret = ? WHERE id = ?",
                    [$apiSecret, $customerId]
                );
                
                $customer['api_secret'] = $apiSecret;
                $success = 'API-Secret wurde erfolgreich neu generiert. Speichern Sie es sicher ab!';
                $_SESSION['show_secret'] = $apiSecret;
                break;
                
            case 'toggle':
                $enabled = $customer['api_enabled'] ? 0 : 1;
                $db->query(
                    "UPDATE customers SET api_enabled = ? WHERE id = ?",
                    [$enabled, $customerId]
                );
                $customer['api_enabled'] = $enabled;
                $success = $enabled ? 'API wurde aktiviert.' : 'API wurde deaktiviert.';
                break;
                
            case 'revoke':
                $db->query(
                    "UPDATE customers SET api_key = NULL, api_secret = NULL, api_enabled = 0 WHERE id = ?",
                    [$customerId]
                );
                $customer['api_key'] = null;
                $customer['api_secret'] = null;
                $customer['api_enabled'] = 0;
                $success = 'API-Zugang wurde vollständig widerrufen.';
                break;
            
            // Enterprise: E-Mail-Einstellungen
            case 'save_email_settings':
                if ($isEnterprise) {
                    $emailMode = $_POST['email_mode'] ?? 'leadbusiness';
                    
                    if ($emailMode === 'leadbusiness') {
                        // Leadbusiness versendet alle E-Mails
                        $db->query(
                            "UPDATE customers SET email_notifications_enabled = 1, email_self_managed = 0, webhook_email_events = 0 WHERE id = ?",
                            [$customerId]
                        );
                        $success = 'E-Mail-Versand wird jetzt von Leadbusiness übernommen.';
                        
                    } elseif ($emailMode === 'self_managed') {
                        // Kunde versendet selbst - nur Webhook-Events
                        $db->query(
                            "UPDATE customers SET email_notifications_enabled = 0, email_self_managed = 1, webhook_email_events = 1 WHERE id = ?",
                            [$customerId]
                        );
                        $success = 'E-Mail-Versand ist jetzt deaktiviert. Sie erhalten alle Events per Webhook.';
                        
                    } elseif ($emailMode === 'hybrid') {
                        // Hybrid: Leadbusiness sendet + Webhook-Events
                        $db->query(
                            "UPDATE customers SET email_notifications_enabled = 1, email_self_managed = 0, webhook_email_events = 1 WHERE id = ?",
                            [$customerId]
                        );
                        $success = 'Hybrid-Modus aktiviert. Leadbusiness sendet E-Mails UND Sie erhalten Webhook-Events.';
                    }
                }
                break;
        }
        
        // Kunden-Daten neu laden
        $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
    }
}

// API-Statistiken laden
$apiStats = $db->fetch(
    "SELECT 
        COUNT(*) as total_requests,
        SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as requests_today,
        SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as requests_week,
        SUM(CASE WHEN status_code >= 200 AND status_code < 300 THEN 1 ELSE 0 END) as successful_requests,
        AVG(response_time_ms) as avg_response_time
     FROM api_logs WHERE customer_id = ?",
    [$customerId]
);

// Letzte API-Requests
$recentRequests = $db->fetchAll(
    "SELECT endpoint, method, status_code, response_time_ms, ip_address, created_at 
     FROM api_logs WHERE customer_id = ? 
     ORDER BY created_at DESC LIMIT 20",
    [$customerId]
);

// Rate Limits nach Plan
$rateLimits = [
    'professional' => ['per_minute' => 60, 'per_day' => 5000, 'per_month' => 100000],
    'enterprise' => ['per_minute' => 300, 'per_day' => 50000, 'per_month' => 1000000]
];
$currentLimits = $rateLimits[$customer['plan']] ?? $rateLimits['professional'];

// Secret zum Anzeigen (nur nach Generierung)
$showSecret = $_SESSION['show_secret'] ?? null;
unset($_SESSION['show_secret']);

// Aktueller E-Mail-Modus ermitteln
$emailMode = 'leadbusiness'; // Standard
if (!empty($customer['email_self_managed'])) {
    $emailMode = 'self_managed';
} elseif (!empty($customer['webhook_email_events']) && !empty($customer['email_notifications_enabled'])) {
    $emailMode = 'hybrid';
}

$pageTitle = 'API-Zugang';
include __DIR__ . '/../../includes/dashboard-header.php';
?>

<?php if (!$hasApiAccess): ?>
<!-- Upgrade Banner für Starter -->
<div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-8 text-white mb-8">
    <div class="flex flex-col md:flex-row items-center gap-6">
        <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
            <i class="fas fa-code text-4xl"></i>
        </div>
        <div class="flex-1 text-center md:text-left">
            <h2 class="text-2xl font-bold mb-2">API-Zugang freischalten</h2>
            <p class="text-white/90 mb-4">
                Mit der REST API können Sie Ihr Empfehlungsprogramm in eigene Systeme integrieren, 
                Leads automatisch synchronisieren und Workflows automatisieren.
            </p>
            <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                <span class="bg-white/20 px-3 py-1 rounded-full text-sm"><i class="fas fa-check mr-1"></i> Leads abrufen & erstellen</span>
                <span class="bg-white/20 px-3 py-1 rounded-full text-sm"><i class="fas fa-check mr-1"></i> Empfehler verwalten</span>
                <span class="bg-white/20 px-3 py-1 rounded-full text-sm"><i class="fas fa-check mr-1"></i> Webhooks empfangen</span>
                <span class="bg-white/20 px-3 py-1 rounded-full text-sm"><i class="fas fa-check mr-1"></i> Statistiken abrufen</span>
            </div>
        </div>
        <div class="text-center">
            <a href="/preise" class="inline-block bg-white text-indigo-600 px-6 py-3 rounded-xl font-semibold hover:bg-indigo-50 transition">
                <i class="fas fa-crown mr-2"></i>Upgrade auf Professional
            </a>
            <p class="text-white/70 text-sm mt-2">Ab 49€/Monat</p>
        </div>
    </div>
</div>

<!-- Feature-Übersicht -->
<div class="grid md:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
        <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4">
            <i class="fas fa-plug text-blue-500 text-xl"></i>
        </div>
        <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-2">Einfache Integration</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">RESTful API mit JSON-Responses. Einfach zu integrieren in jedes System.</p>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
        <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-4">
            <i class="fas fa-shield-alt text-green-500 text-xl"></i>
        </div>
        <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-2">Sichere Authentifizierung</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">API-Key + Secret für sichere Requests. Rate-Limiting zum Schutz.</p>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4">
            <i class="fas fa-book text-purple-500 text-xl"></i>
        </div>
        <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-2">Vollständige Dokumentation</h3>
        <p class="text-slate-600 dark:text-slate-400 text-sm">Ausführliche API-Docs mit Beispielen für alle Endpunkte.</p>
    </div>
</div>

<?php else: ?>

<?php if ($success): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500 text-xl"></i>
        <p class="text-green-800 dark:text-green-300"><?= e($success) ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
        <p class="text-red-800 dark:text-red-300"><?= e($error) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- API Status & Keys -->
<div class="grid lg:grid-cols-2 gap-6 mb-8">
    
    <!-- API Keys Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">
                <i class="fas fa-key text-primary-500 mr-2"></i>API-Schlüssel
            </h2>
            <?php if (!empty($customer['api_key'])): ?>
            <form method="POST" class="inline">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="action" value="toggle">
                <button type="submit" class="px-3 py-1 rounded-full text-sm font-medium transition
                    <?= $customer['api_enabled'] 
                        ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' 
                        : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' ?>">
                    <i class="fas fa-circle text-xs mr-1"></i>
                    <?= $customer['api_enabled'] ? 'Aktiv' : 'Deaktiviert' ?>
                </button>
            </form>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($customer['api_key'])): ?>
        
        <!-- API Key -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">API-Key</label>
            <div class="flex items-center gap-2">
                <input type="text" 
                       value="<?= e($customer['api_key']) ?>" 
                       readonly 
                       class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg text-slate-800 dark:text-white font-mono text-sm">
                <button onclick="copyToClipboard('<?= e($customer['api_key']) ?>', this)" 
                        class="p-3 bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition"
                        title="Kopieren">
                    <i class="fas fa-copy text-slate-500"></i>
                </button>
            </div>
        </div>
        
        <!-- API Secret -->
        <?php if ($showSecret): ?>
        <div class="mb-4 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
            <div class="flex items-start gap-3 mb-3">
                <i class="fas fa-exclamation-triangle text-amber-500 mt-1"></i>
                <div>
                    <p class="font-medium text-amber-800 dark:text-amber-300">Wichtig: API-Secret speichern!</p>
                    <p class="text-sm text-amber-700 dark:text-amber-400">Dieses Secret wird nur einmal angezeigt.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="text" value="<?= e($showSecret) ?>" readonly 
                       class="flex-1 px-4 py-3 bg-white dark:bg-slate-700 border border-amber-300 dark:border-amber-700 rounded-lg font-mono text-sm text-slate-800 dark:text-white">
                <button onclick="copyToClipboard('<?= e($showSecret) ?>', this)" 
                        class="p-3 bg-amber-100 dark:bg-amber-900/50 rounded-lg hover:bg-amber-200 transition">
                    <i class="fas fa-copy text-amber-600"></i>
                </button>
            </div>
        </div>
        <?php else: ?>
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-2">API-Secret</label>
            <div class="px-4 py-3 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg">
                <span class="text-slate-500 dark:text-slate-400 text-sm">••••••••••••••••••••••••</span>
                <span class="text-xs text-slate-400 dark:text-slate-500 ml-2">(Wird nur bei Generierung angezeigt)</span>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Aktionen -->
        <div class="flex flex-wrap gap-3 mt-6">
            <form method="POST" class="inline">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="action" value="regenerate">
                <button type="submit" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition text-sm">
                    <i class="fas fa-sync-alt mr-2"></i>Key neu generieren
                </button>
            </form>
            
            <form method="POST" class="inline">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="action" value="regenerate_secret">
                <button type="submit" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition text-sm">
                    <i class="fas fa-lock mr-2"></i>Secret neu generieren
                </button>
            </form>
            
            <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher? Alle API-Zugriffe werden sofort ungültig.')">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="action" value="revoke">
                <button type="submit" class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition text-sm">
                    <i class="fas fa-trash mr-2"></i>Widerrufen
                </button>
            </form>
        </div>
        
        <?php else: ?>
        
        <!-- Noch kein API-Key -->
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-key text-slate-400 text-2xl"></i>
            </div>
            <p class="text-slate-600 dark:text-slate-400 mb-6">Sie haben noch keinen API-Schlüssel generiert.</p>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                <input type="hidden" name="action" value="generate">
                <button type="submit" class="px-6 py-3 bg-primary-500 text-white rounded-xl hover:bg-primary-600 transition font-medium">
                    <i class="fas fa-plus mr-2"></i>API-Schlüssel generieren
                </button>
            </form>
        </div>
        
        <?php endif; ?>
    </div>
    
    <!-- Rate Limits & Stats -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">
            <i class="fas fa-tachometer-alt text-primary-500 mr-2"></i>Rate Limits & Nutzung
        </h2>
        
        <div class="space-y-4">
            <!-- Plan Badge -->
            <div class="flex items-center justify-between p-4 bg-gradient-to-r <?= $isEnterprise ? 'from-purple-500 to-indigo-600' : 'from-primary-500 to-purple-600' ?> rounded-xl text-white">
                <div>
                    <span class="text-white/80 text-sm">Ihr Plan</span>
                    <div class="text-xl font-bold"><?= ucfirst($customer['plan']) ?></div>
                </div>
                <i class="fas <?= $isEnterprise ? 'fa-building' : 'fa-crown' ?> text-3xl text-white/30"></i>
            </div>
            
            <!-- Limits -->
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($currentLimits['per_minute']) ?></div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">pro Minute</div>
                </div>
                <div class="text-center p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($currentLimits['per_day']) ?></div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">pro Tag</div>
                </div>
                <div class="text-center p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                    <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($currentLimits['per_month'] / 1000) ?>k</div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">pro Monat</div>
                </div>
            </div>
            
            <!-- Usage Stats -->
            <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-3">Ihre Nutzung</h3>
                
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-slate-600 dark:text-slate-400">Heute</span>
                        <span class="font-medium text-slate-800 dark:text-white">
                            <?= number_format($apiStats['requests_today'] ?? 0) ?> / <?= number_format($currentLimits['per_day']) ?>
                        </span>
                    </div>
                    <div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-primary-500 h-2 rounded-full transition-all" 
                             style="width: <?= min(100, (($apiStats['requests_today'] ?? 0) / $currentLimits['per_day']) * 100) ?>%"></div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <div class="text-sm text-slate-500 dark:text-slate-400">Diese Woche</div>
                            <div class="font-semibold text-slate-800 dark:text-white"><?= number_format($apiStats['requests_week'] ?? 0) ?></div>
                        </div>
                        <div>
                            <div class="text-sm text-slate-500 dark:text-slate-400">⌀ Antwortzeit</div>
                            <div class="font-semibold text-slate-800 dark:text-white"><?= round($apiStats['avg_response_time'] ?? 0) ?> ms</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($isEnterprise): ?>
<!-- ENTERPRISE: E-Mail-Versand Kontrolle -->
<div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-2xl p-6 border border-purple-200 dark:border-purple-800 mb-8">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-10 h-10 bg-purple-500 rounded-xl flex items-center justify-center">
            <i class="fas fa-envelope text-white"></i>
        </div>
        <div>
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">E-Mail-Versand Kontrolle</h2>
            <p class="text-sm text-slate-600 dark:text-slate-400">Enterprise-Funktion: Wählen Sie, wer E-Mails an Ihre Empfehler versendet</p>
        </div>
        <span class="ml-auto px-3 py-1 bg-purple-500 text-white text-xs font-bold rounded-full">ENTERPRISE</span>
    </div>
    
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
        <input type="hidden" name="action" value="save_email_settings">
        
        <div class="grid md:grid-cols-3 gap-4 mb-6">
            
            <!-- Option 1: Leadbusiness versendet -->
            <label class="relative cursor-pointer">
                <input type="radio" name="email_mode" value="leadbusiness" <?= $emailMode === 'leadbusiness' ? 'checked' : '' ?> class="peer sr-only">
                <div class="p-5 bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/30 transition-all hover:border-purple-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-paper-plane text-green-500"></i>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-purple-500 peer-checked:bg-purple-500 flex items-center justify-center">
                            <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                        </div>
                    </div>
                    <h3 class="font-semibold text-slate-800 dark:text-white mb-1">Leadbusiness versendet</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Wir kümmern uns um alle E-Mails an Ihre Empfehler (Willkommen, Belohnungen, etc.)</p>
                    <div class="mt-3 flex items-center gap-2 text-xs text-green-600 dark:text-green-400">
                        <i class="fas fa-check-circle"></i>
                        <span>Empfohlen - Keine Arbeit für Sie</span>
                    </div>
                </div>
            </label>
            
            <!-- Option 2: Selbst verwaltet -->
            <label class="relative cursor-pointer">
                <input type="radio" name="email_mode" value="self_managed" <?= $emailMode === 'self_managed' ? 'checked' : '' ?> class="peer sr-only">
                <div class="p-5 bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/30 transition-all hover:border-purple-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-cogs text-amber-500"></i>
                        </div>
                    </div>
                    <h3 class="font-semibold text-slate-800 dark:text-white mb-1">Ich versende selbst</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Leadbusiness sendet KEINE E-Mails. Sie erhalten alle Events per Webhook.</p>
                    <div class="mt-3 flex items-center gap-2 text-xs text-amber-600 dark:text-amber-400">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Erfordert eigenes E-Mail-System</span>
                    </div>
                </div>
            </label>
            
            <!-- Option 3: Hybrid -->
            <label class="relative cursor-pointer">
                <input type="radio" name="email_mode" value="hybrid" <?= $emailMode === 'hybrid' ? 'checked' : '' ?> class="peer sr-only">
                <div class="p-5 bg-white dark:bg-slate-800 rounded-xl border-2 border-slate-200 dark:border-slate-700 peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/30 transition-all hover:border-purple-300">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                            <i class="fas fa-arrows-split-up-and-left text-blue-500"></i>
                        </div>
                    </div>
                    <h3 class="font-semibold text-slate-800 dark:text-white mb-1">Hybrid-Modus</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Leadbusiness sendet E-Mails UND Sie erhalten zusätzlich Webhook-Events.</p>
                    <div class="mt-3 flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400">
                        <i class="fas fa-layer-group"></i>
                        <span>Beides gleichzeitig</span>
                    </div>
                </div>
            </label>
        </div>
        
        <!-- Info-Box je nach Auswahl -->
        <div id="email-info" class="p-4 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 mb-4">
            <div id="info-leadbusiness" class="<?= $emailMode !== 'leadbusiness' ? 'hidden' : '' ?>">
                <h4 class="font-medium text-slate-800 dark:text-white mb-2"><i class="fas fa-info-circle text-green-500 mr-2"></i>Automatischer E-Mail-Versand</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">Folgende E-Mails werden automatisch von Leadbusiness versendet:</p>
                <ul class="mt-2 space-y-1 text-sm text-slate-500 dark:text-slate-400">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Willkommens-E-Mail bei Registrierung</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Benachrichtigung bei neuer Empfehlung</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Belohnungs-E-Mail bei freigeschalteter Stufe</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Wöchentlicher Status-Bericht (optional)</li>
                </ul>
            </div>
            
            <div id="info-self" class="<?= $emailMode !== 'self_managed' ? 'hidden' : '' ?>">
                <h4 class="font-medium text-slate-800 dark:text-white mb-2"><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>Sie sind verantwortlich für E-Mails</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-3">Leadbusiness sendet KEINE E-Mails an Ihre Empfehler. Stattdessen erhalten Sie Webhook-Events:</p>
                <div class="grid sm:grid-cols-2 gap-2 text-sm">
                    <code class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">referrer.created</code>
                    <code class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">conversion.created</code>
                    <code class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">reward.unlocked</code>
                    <code class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">reward.claimed</code>
                </div>
                <p class="mt-3 text-sm text-amber-600 dark:text-amber-400">
                    <i class="fas fa-arrow-right mr-1"></i>
                    Stellen Sie sicher, dass Sie <a href="/dashboard/webhooks.php" class="underline">Webhooks konfiguriert</a> haben!
                </p>
            </div>
            
            <div id="info-hybrid" class="<?= $emailMode !== 'hybrid' ? 'hidden' : '' ?>">
                <h4 class="font-medium text-slate-800 dark:text-white mb-2"><i class="fas fa-layer-group text-blue-500 mr-2"></i>Beides gleichzeitig</h4>
                <p class="text-sm text-slate-600 dark:text-slate-400">Perfekt für zusätzliche Automationen:</p>
                <ul class="mt-2 space-y-1 text-sm text-slate-500 dark:text-slate-400">
                    <li><i class="fas fa-envelope text-green-500 mr-2"></i>Leadbusiness sendet alle Standard-E-Mails</li>
                    <li><i class="fas fa-bolt text-blue-500 mr-2"></i>Sie erhalten zusätzlich Webhook-Events</li>
                    <li><i class="fas fa-cog text-purple-500 mr-2"></i>Ideal für: CRM-Updates, Slack-Benachrichtigungen, eigene Dashboards</li>
                </ul>
            </div>
        </div>
        
        <button type="submit" class="px-6 py-3 bg-purple-500 text-white rounded-xl hover:bg-purple-600 transition font-medium">
            <i class="fas fa-save mr-2"></i>Einstellungen speichern
        </button>
    </form>
</div>

<script>
// Info-Box bei Auswahl wechseln
document.querySelectorAll('input[name="email_mode"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('info-leadbusiness').classList.add('hidden');
        document.getElementById('info-self').classList.add('hidden');
        document.getElementById('info-hybrid').classList.add('hidden');
        
        if (this.value === 'leadbusiness') {
            document.getElementById('info-leadbusiness').classList.remove('hidden');
        } else if (this.value === 'self_managed') {
            document.getElementById('info-self').classList.remove('hidden');
        } else if (this.value === 'hybrid') {
            document.getElementById('info-hybrid').classList.remove('hidden');
        }
    });
});
</script>
<?php endif; ?>

<!-- Quick Start Guide -->
<div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700 mb-8">
    <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">
        <i class="fas fa-rocket text-primary-500 mr-2"></i>Schnellstart
    </h2>
    
    <div class="grid md:grid-cols-2 gap-6">
        <!-- cURL Beispiel -->
        <div>
            <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-3">
                <i class="fas fa-terminal mr-2"></i>cURL Beispiel
            </h3>
            <pre class="bg-slate-900 text-green-400 rounded-xl p-4 text-sm overflow-x-auto"><code># Alle Empfehler abrufen
curl -X GET "https://empfehlungen.cloud/api/v1/referrers" \
  -H "X-API-Key: <?= e($customer['api_key'] ?? 'IHR_API_KEY') ?>" \
  -H "X-API-Secret: IHR_API_SECRET"</code></pre>
        </div>
        
        <!-- PHP Beispiel -->
        <div>
            <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-3">
                <i class="fab fa-php mr-2"></i>PHP Beispiel
            </h3>
            <pre class="bg-slate-900 text-blue-400 rounded-xl p-4 text-sm overflow-x-auto"><code>$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://empfehlungen.cloud/api/v1/referrers',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'X-API-Key: <?= e($customer['api_key'] ?? 'IHR_API_KEY') ?>',
        'X-API-Secret: IHR_API_SECRET'
    ]
]);
$response = json_decode(curl_exec($ch), true);</code></pre>
        </div>
    </div>
    
    <!-- Verfügbare Endpunkte -->
    <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-700 dark:text-slate-300 mb-4">Verfügbare Endpunkte</h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-slate-500 dark:text-slate-400">
                        <th class="pb-3 font-medium">Methode</th>
                        <th class="pb-3 font-medium">Endpunkt</th>
                        <th class="pb-3 font-medium">Beschreibung</th>
                    </tr>
                </thead>
                <tbody class="text-slate-700 dark:text-slate-300">
                    <tr class="border-t border-slate-100 dark:border-slate-700">
                        <td class="py-3"><span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded text-xs font-mono">GET</span></td>
                        <td class="py-3 font-mono text-xs">/api/v1/referrers</td>
                        <td class="py-3">Alle Empfehler abrufen</td>
                    </tr>
                    <tr class="border-t border-slate-100 dark:border-slate-700">
                        <td class="py-3"><span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs font-mono">POST</span></td>
                        <td class="py-3 font-mono text-xs">/api/v1/referrers</td>
                        <td class="py-3">Neuen Empfehler erstellen</td>
                    </tr>
                    <tr class="border-t border-slate-100 dark:border-slate-700">
                        <td class="py-3"><span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded text-xs font-mono">GET</span></td>
                        <td class="py-3 font-mono text-xs">/api/v1/conversions</td>
                        <td class="py-3">Alle Conversions abrufen</td>
                    </tr>
                    <tr class="border-t border-slate-100 dark:border-slate-700">
                        <td class="py-3"><span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs font-mono">POST</span></td>
                        <td class="py-3 font-mono text-xs">/api/v1/conversions</td>
                        <td class="py-3">Neue Conversion erstellen</td>
                    </tr>
                    <tr class="border-t border-slate-100 dark:border-slate-700">
                        <td class="py-3"><span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded text-xs font-mono">GET</span></td>
                        <td class="py-3 font-mono text-xs">/api/v1/stats</td>
                        <td class="py-3">Statistiken abrufen</td>
                    </tr>
                    <?php if ($isEnterprise): ?>
                    <tr class="border-t border-slate-100 dark:border-slate-700 bg-purple-50 dark:bg-purple-900/10">
                        <td class="py-3"><span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded text-xs font-mono">POST</span></td>
                        <td class="py-3 font-mono text-xs">/api/v1/webhooks</td>
                        <td class="py-3"><i class="fas fa-building text-purple-500 mr-1"></i>Webhook registrieren</td>
                    </tr>
                    <tr class="border-t border-slate-100 dark:border-slate-700 bg-purple-50 dark:bg-purple-900/10">
                        <td class="py-3"><span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded text-xs font-mono">GET</span></td>
                        <td class="py-3 font-mono text-xs">/api/v1/export</td>
                        <td class="py-3"><i class="fas fa-building text-purple-500 mr-1"></i>Daten exportieren</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <a href="/api/v1/docs" class="mt-4 inline-flex items-center text-primary-500 hover:text-primary-600 font-medium">
            <i class="fas fa-book mr-2"></i>Vollständige API-Dokumentation
            <i class="fas fa-arrow-right ml-2 text-sm"></i>
        </a>
    </div>
</div>

<!-- Recent API Requests -->
<?php if (!empty($recentRequests)): ?>
<div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
    <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6">
        <i class="fas fa-history text-primary-500 mr-2"></i>Letzte API-Anfragen
    </h2>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700">
                    <th class="pb-3 font-medium">Zeitpunkt</th>
                    <th class="pb-3 font-medium">Methode</th>
                    <th class="pb-3 font-medium">Endpunkt</th>
                    <th class="pb-3 font-medium">Status</th>
                    <th class="pb-3 font-medium">Zeit</th>
                </tr>
            </thead>
            <tbody class="text-slate-700 dark:text-slate-300">
                <?php foreach ($recentRequests as $req): ?>
                <tr class="border-b border-slate-100 dark:border-slate-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="py-3 text-slate-500 dark:text-slate-400"><?= date('d.m. H:i', strtotime($req['created_at'])) ?></td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded text-xs font-mono
                            <?php
                            switch($req['method']) {
                                case 'GET': echo 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300'; break;
                                case 'POST': echo 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'; break;
                                case 'PUT': echo 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300'; break;
                                case 'DELETE': echo 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'; break;
                                default: echo 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300';
                            }
                            ?>"><?= e($req['method']) ?></span>
                    </td>
                    <td class="py-3 font-mono text-xs"><?= e($req['endpoint']) ?></td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded text-xs
                            <?= $req['status_code'] < 300 
                                ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' 
                                : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300' ?>"><?= $req['status_code'] ?></span>
                    </td>
                    <td class="py-3 text-slate-500 dark:text-slate-400"><?= $req['response_time_ms'] ?>ms</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php endif; ?>

<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(() => {
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-green-500"></i>';
        setTimeout(() => { button.innerHTML = originalIcon; }, 2000);
    });
}
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
