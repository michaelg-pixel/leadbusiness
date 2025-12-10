<?php
/**
 * Dashboard - API-Verwaltung
 * Für Professional und Enterprise Kunden
 */

require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../includes/Auth.php';

// Auth prüfen
$auth = new Auth();
if (!$auth->check()) {
    header('Location: /dashboard/login.php');
    exit;
}

$db = Database::getInstance();
$customer = $auth->user();
$customerId = $customer['id'];

// Plan-Check
$hasApiAccess = in_array($customer['plan'], ['professional', 'enterprise']);
$isEnterprise = $customer['plan'] === 'enterprise';

if (!$hasApiAccess) {
    header('Location: /dashboard/');
    exit;
}

// Rate Limits je nach Plan
$rateLimits = [
    'professional' => ['keys' => 3, 'requests' => 1000],
    'enterprise' => ['keys' => 10, 'requests' => 10000]
];
$limits = $rateLimits[$customer['plan']];

// Nachrichten
$success = '';
$error = '';

// API-Key erstellen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // CSRF-Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Ungültige Anfrage.';
    } else {
        
        switch ($_POST['action']) {
            case 'create_key':
                // Anzahl bestehender Keys prüfen
                $keyCount = $db->fetch(
                    "SELECT COUNT(*) as cnt FROM api_keys WHERE customer_id = ?",
                    [$customerId]
                )['cnt'];
                
                if ($keyCount >= $limits['keys']) {
                    $error = "Maximale Anzahl an API-Keys erreicht ({$limits['keys']}).";
                } else {
                    $keyName = trim($_POST['key_name'] ?? 'API Key');
                    $apiKey = 'lb_' . bin2hex(random_bytes(24));
                    $secretKey = 'sk_' . bin2hex(random_bytes(24));
                    
                    // Berechtigungen
                    $permissions = [
                        'leads' => ['read' => true, 'write' => isset($_POST['perm_leads_write'])],
                        'referrers' => ['read' => true, 'write' => isset($_POST['perm_referrers_write'])],
                        'rewards' => ['read' => true, 'write' => false],
                        'stats' => ['read' => true]
                    ];
                    
                    $db->query(
                        "INSERT INTO api_keys (customer_id, name, api_key, secret_key, permissions, rate_limit_per_hour) 
                         VALUES (?, ?, ?, ?, ?, ?)",
                        [$customerId, $keyName, $apiKey, $secretKey, json_encode($permissions), $limits['requests']]
                    );
                    
                    $_SESSION['new_api_key'] = $apiKey;
                    $_SESSION['new_secret_key'] = $secretKey;
                    $success = 'API-Key erfolgreich erstellt! Bitte speichern Sie den Secret-Key sicher ab.';
                }
                break;
                
            case 'delete_key':
                $keyId = (int)($_POST['key_id'] ?? 0);
                $db->query(
                    "DELETE FROM api_keys WHERE id = ? AND customer_id = ?",
                    [$keyId, $customerId]
                );
                $success = 'API-Key wurde gelöscht.';
                break;
                
            case 'toggle_key':
                $keyId = (int)($_POST['key_id'] ?? 0);
                $db->query(
                    "UPDATE api_keys SET is_active = NOT is_active WHERE id = ? AND customer_id = ?",
                    [$keyId, $customerId]
                );
                $success = 'API-Key Status wurde geändert.';
                break;
                
            case 'regenerate_secret':
                $keyId = (int)($_POST['key_id'] ?? 0);
                $newSecret = 'sk_' . bin2hex(random_bytes(24));
                $db->query(
                    "UPDATE api_keys SET secret_key = ? WHERE id = ? AND customer_id = ?",
                    [$newSecret, $keyId, $customerId]
                );
                $_SESSION['new_secret_key'] = $newSecret;
                $success = 'Secret-Key wurde neu generiert. Bitte speichern Sie ihn sicher ab.';
                break;
        }
    }
}

// API-Keys laden
$apiKeys = $db->fetchAll(
    "SELECT * FROM api_keys WHERE customer_id = ? ORDER BY created_at DESC",
    [$customerId]
);

// Statistiken laden
$stats = $db->fetch(
    "SELECT 
        COUNT(*) as total_requests,
        SUM(CASE WHEN created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR) THEN 1 ELSE 0 END) as requests_24h,
        SUM(CASE WHEN created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) THEN 1 ELSE 0 END) as requests_1h,
        SUM(CASE WHEN status_code >= 400 THEN 1 ELSE 0 END) as errors
     FROM api_logs WHERE customer_id = ?",
    [$customerId]
);

// Letzte Requests laden
$recentLogs = $db->fetchAll(
    "SELECT al.*, ak.name as key_name 
     FROM api_logs al 
     LEFT JOIN api_keys ak ON al.api_key_id = ak.id 
     WHERE al.customer_id = ? 
     ORDER BY al.created_at DESC LIMIT 20",
    [$customerId]
);

$pageTitle = 'API-Zugang';
require_once __DIR__ . '/../../includes/dashboard-header.php';
?>

<!-- Neuer API-Key wurde erstellt -->
<?php if (isset($_SESSION['new_api_key']) || isset($_SESSION['new_secret_key'])): ?>
<div class="mb-6 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl p-6">
    <div class="flex items-start gap-3">
        <i class="fas fa-exclamation-triangle text-amber-500 text-xl mt-1"></i>
        <div class="flex-1">
            <h3 class="font-semibold text-amber-800 dark:text-amber-200 mb-2">Wichtig: Speichern Sie diese Daten jetzt!</h3>
            <p class="text-sm text-amber-700 dark:text-amber-300 mb-4">Diese Informationen werden nur einmal angezeigt.</p>
            
            <?php if (isset($_SESSION['new_api_key'])): ?>
            <div class="mb-3">
                <label class="block text-xs font-medium text-amber-700 dark:text-amber-300 mb-1">API-Key</label>
                <div class="flex items-center gap-2">
                    <code class="flex-1 px-3 py-2 bg-white dark:bg-slate-800 rounded border border-amber-300 dark:border-amber-700 text-sm font-mono"><?= e($_SESSION['new_api_key']) ?></code>
                    <button onclick="copyToClipboard('<?= e($_SESSION['new_api_key']) ?>')" class="px-3 py-2 bg-amber-100 dark:bg-amber-800 hover:bg-amber-200 dark:hover:bg-amber-700 rounded text-amber-700 dark:text-amber-200">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['new_api_key']); endif; ?>
            
            <?php if (isset($_SESSION['new_secret_key'])): ?>
            <div>
                <label class="block text-xs font-medium text-amber-700 dark:text-amber-300 mb-1">Secret-Key (nur jetzt sichtbar!)</label>
                <div class="flex items-center gap-2">
                    <code class="flex-1 px-3 py-2 bg-white dark:bg-slate-800 rounded border border-amber-300 dark:border-amber-700 text-sm font-mono"><?= e($_SESSION['new_secret_key']) ?></code>
                    <button onclick="copyToClipboard('<?= e($_SESSION['new_secret_key']) ?>')" class="px-3 py-2 bg-amber-100 dark:bg-amber-800 hover:bg-amber-200 dark:hover:bg-amber-700 rounded text-amber-700 dark:text-amber-200">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            <?php unset($_SESSION['new_secret_key']); endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if ($success): ?>
<div class="mb-6 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-4">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500"></i>
        <p class="text-green-800 dark:text-green-200"><?= e($success) ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-4">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500"></i>
        <p class="text-red-800 dark:text-red-200"><?= e($error) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Statistiken -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-exchange-alt text-primary-500"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_requests'] ?? 0) ?></p>
                <p class="text-xs text-slate-500">Gesamt-Requests</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-blue-500"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['requests_24h'] ?? 0) ?></p>
                <p class="text-xs text-slate-500">Letzte 24h</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-tachometer-alt text-green-500"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['requests_1h'] ?? 0) ?> / <?= number_format($limits['requests']) ?></p>
                <p class="text-xs text-slate-500">Rate Limit/h</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['errors'] ?? 0) ?></p>
                <p class="text-xs text-slate-500">Fehler</p>
            </div>
        </div>
    </div>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    
    <!-- API-Keys Liste -->
    <div class="lg:col-span-2 space-y-6">
        
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800 dark:text-white">API-Keys</h2>
                        <p class="text-sm text-slate-500"><?= count($apiKeys) ?> / <?= $limits['keys'] ?> Keys verwendet</p>
                    </div>
                    <?php if (count($apiKeys) < $limits['keys']): ?>
                    <button onclick="document.getElementById('createKeyModal').classList.remove('hidden')" 
                            class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Neuer Key
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (empty($apiKeys)): ?>
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-key text-2xl text-slate-400"></i>
                </div>
                <p class="text-slate-600 dark:text-slate-400 mb-4">Noch keine API-Keys erstellt</p>
                <button onclick="document.getElementById('createKeyModal').classList.remove('hidden')" 
                        class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium">
                    Ersten API-Key erstellen
                </button>
            </div>
            <?php else: ?>
            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($apiKeys as $key): ?>
                <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-slate-800 dark:text-white"><?= e($key['name']) ?></span>
                                <?php if ($key['is_active']): ?>
                                <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs rounded-full">Aktiv</span>
                                <?php else: ?>
                                <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs rounded-full">Inaktiv</span>
                                <?php endif; ?>
                            </div>
                            <div class="flex items-center gap-2 mb-2">
                                <code class="text-xs font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded"><?= e($key['api_key']) ?></code>
                                <button onclick="copyToClipboard('<?= e($key['api_key']) ?>')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                                    <i class="fas fa-copy text-xs"></i>
                                </button>
                            </div>
                            <div class="flex items-center gap-4 text-xs text-slate-500">
                                <span><i class="fas fa-calendar mr-1"></i>Erstellt: <?= date('d.m.Y', strtotime($key['created_at'])) ?></span>
                                <?php if ($key['last_used_at']): ?>
                                <span><i class="fas fa-clock mr-1"></i>Zuletzt: <?= date('d.m.Y H:i', strtotime($key['last_used_at'])) ?></span>
                                <?php endif; ?>
                                <span><i class="fas fa-chart-bar mr-1"></i><?= number_format($key['total_requests']) ?> Requests</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="toggle_key">
                                <input type="hidden" name="key_id" value="<?= $key['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="<?= $key['is_active'] ? 'Deaktivieren' : 'Aktivieren' ?>">
                                    <i class="fas fa-<?= $key['is_active'] ? 'pause' : 'play' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" class="inline" onsubmit="return confirm('Secret-Key wirklich neu generieren? Der alte funktioniert dann nicht mehr.')">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="regenerate_secret">
                                <input type="hidden" name="key_id" value="<?= $key['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-amber-600" title="Secret neu generieren">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                            <form method="POST" class="inline" onsubmit="return confirm('API-Key wirklich löschen?')">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="delete_key">
                                <input type="hidden" name="key_id" value="<?= $key['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Letzte Requests -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Letzte API-Requests</h2>
            </div>
            
            <?php if (empty($recentLogs)): ?>
            <div class="p-8 text-center text-slate-500">
                Noch keine API-Requests protokolliert.
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 dark:bg-slate-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Zeit</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Endpoint</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Methode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase">Zeit (ms)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                        <?php foreach ($recentLogs as $log): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50">
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?= date('d.m. H:i:s', strtotime($log['created_at'])) ?></td>
                            <td class="px-4 py-3">
                                <code class="text-xs font-mono text-slate-600 dark:text-slate-300"><?= e($log['endpoint']) ?></code>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs rounded <?= $log['method'] === 'GET' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300' ?>">
                                    <?= e($log['method']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs rounded <?= $log['status_code'] < 400 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' ?>">
                                    <?= $log['status_code'] ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?= $log['response_time_ms'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
    </div>
    
    <!-- Dokumentation Sidebar -->
    <div class="space-y-6">
        
        <!-- Quick Start -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
                <i class="fas fa-rocket text-primary-500 mr-2"></i>Quick Start
            </h3>
            
            <div class="space-y-4 text-sm">
                <div>
                    <p class="text-slate-600 dark:text-slate-300 mb-2">Base URL:</p>
                    <code class="block px-3 py-2 bg-slate-100 dark:bg-slate-700 rounded text-xs font-mono break-all">
                        https://www.empfehlungen.cloud/api/v1
                    </code>
                </div>
                
                <div>
                    <p class="text-slate-600 dark:text-slate-300 mb-2">Authentifizierung:</p>
                    <code class="block px-3 py-2 bg-slate-100 dark:bg-slate-700 rounded text-xs font-mono">
                        X-API-Key: lb_xxx<br>
                        X-API-Secret: sk_xxx
                    </code>
                </div>
                
                <div>
                    <p class="text-slate-600 dark:text-slate-300 mb-2">Beispiel Request:</p>
                    <pre class="px-3 py-2 bg-slate-900 dark:bg-slate-950 rounded text-xs font-mono text-green-400 overflow-x-auto">curl -X GET \
  https://www.empfehlungen.cloud/api/v1/leads \
  -H "X-API-Key: lb_xxx" \
  -H "X-API-Secret: sk_xxx"</pre>
                </div>
            </div>
        </div>
        
        <!-- Endpoints -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
                <i class="fas fa-book text-primary-500 mr-2"></i>Verfügbare Endpoints
            </h3>
            
            <div class="space-y-3 text-sm">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">GET</span>
                    <code class="text-slate-600 dark:text-slate-300 font-mono text-xs">/leads</code>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded">POST</span>
                    <code class="text-slate-600 dark:text-slate-300 font-mono text-xs">/leads</code>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">GET</span>
                    <code class="text-slate-600 dark:text-slate-300 font-mono text-xs">/leads/{id}</code>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">GET</span>
                    <code class="text-slate-600 dark:text-slate-300 font-mono text-xs">/referrers</code>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">GET</span>
                    <code class="text-slate-600 dark:text-slate-300 font-mono text-xs">/referrers/{code}</code>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">GET</span>
                    <code class="text-slate-600 dark:text-slate-300 font-mono text-xs">/stats</code>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded">GET</span>
                    <code class="text-slate-600 dark:text-slate-300 font-mono text-xs">/rewards</code>
                </div>
            </div>
            
            <a href="/api/docs" target="_blank" class="mt-4 flex items-center justify-center gap-2 w-full px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg text-sm text-slate-700 dark:text-slate-200 transition-all">
                <i class="fas fa-external-link-alt"></i>
                Vollständige Dokumentation
            </a>
        </div>
        
        <!-- Plan Info -->
        <div class="bg-gradient-to-br from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 rounded-xl border border-primary-200 dark:border-primary-800 p-6">
            <h3 class="font-semibold text-primary-800 dark:text-primary-200 mb-3">
                <i class="fas fa-<?= $isEnterprise ? 'crown' : 'gem' ?> text-primary-500 mr-2"></i>
                Ihr Plan: <?= ucfirst($customer['plan']) ?>
            </h3>
            <ul class="space-y-2 text-sm text-primary-700 dark:text-primary-300">
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-primary-500"></i>
                    <?= $limits['keys'] ?> API-Keys
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-primary-500"></i>
                    <?= number_format($limits['requests']) ?> Requests/Stunde
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-primary-500"></i>
                    Webhooks
                </li>
                <?php if ($isEnterprise): ?>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-primary-500"></i>
                    Prioritäts-Support
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-primary-500"></i>
                    Custom Integrations
                </li>
                <?php endif; ?>
            </ul>
        </div>
        
    </div>
</div>

<!-- Create Key Modal -->
<div id="createKeyModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('createKeyModal').classList.add('hidden')"></div>
        
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Neuen API-Key erstellen</h3>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="create_key">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name</label>
                        <input type="text" name="key_name" value="API Key" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white" placeholder="z.B. Production, Development">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Berechtigungen</label>
                        <div class="space-y-2">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="perm_leads_write" class="w-4 h-4 text-primary-500 rounded">
                                <span class="text-sm text-slate-600 dark:text-slate-300">Leads erstellen/bearbeiten</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="perm_referrers_write" class="w-4 h-4 text-primary-500 rounded">
                                <span class="text-sm text-slate-600 dark:text-slate-300">Empfehler erstellen/bearbeiten</span>
                            </label>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Lese-Zugriff ist immer aktiviert.</p>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('createKeyModal').classList.add('hidden')" 
                            class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                        Abbrechen
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg font-medium">
                        Key erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Kurze Feedback-Animation könnte hier hinzugefügt werden
        alert('In Zwischenablage kopiert!');
    });
}
</script>

<?php require_once __DIR__ . '/../../includes/dashboard-footer.php'; ?>
