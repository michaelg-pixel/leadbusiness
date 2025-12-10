<?php
/**
 * Dashboard - Webhooks-Verwaltung
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

// Webhook-Limits je nach Plan
$webhookLimits = [
    'professional' => 5,
    'enterprise' => 20
];
$maxWebhooks = $webhookLimits[$customer['plan']];

// Verfügbare Events
$availableEvents = [
    'lead.created' => 'Neuer Lead erstellt',
    'lead.converted' => 'Lead konvertiert',
    'referrer.created' => 'Neuer Empfehler registriert',
    'referrer.reward_earned' => 'Empfehler erreicht Belohnungsstufe',
    'referrer.badge_earned' => 'Empfehler erhält Badge',
];

if ($isEnterprise) {
    $availableEvents['lead.updated'] = 'Lead aktualisiert';
    $availableEvents['referrer.milestone'] = 'Empfehler erreicht Meilenstein';
    $availableEvents['daily.summary'] = 'Tägliche Zusammenfassung';
}

// Nachrichten
$success = '';
$error = '';

// Webhook-Aktionen
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // CSRF-Check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Ungültige Anfrage.';
    } else {
        
        switch ($_POST['action']) {
            case 'create_webhook':
                // Anzahl prüfen
                $webhookCount = $db->fetch(
                    "SELECT COUNT(*) as cnt FROM api_webhooks WHERE customer_id = ?",
                    [$customerId]
                )['cnt'];
                
                if ($webhookCount >= $maxWebhooks) {
                    $error = "Maximale Anzahl an Webhooks erreicht ({$maxWebhooks}).";
                } else {
                    $name = trim($_POST['webhook_name'] ?? 'Webhook');
                    $url = trim($_POST['webhook_url'] ?? '');
                    $events = $_POST['events'] ?? [];
                    
                    // URL validieren
                    if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('/^https:\/\//', $url)) {
                        $error = 'Bitte geben Sie eine gültige HTTPS-URL ein.';
                    } elseif (empty($events)) {
                        $error = 'Bitte wählen Sie mindestens ein Event aus.';
                    } else {
                        $secret = 'whsec_' . bin2hex(random_bytes(24));
                        
                        $db->query(
                            "INSERT INTO api_webhooks (customer_id, name, url, secret, events) VALUES (?, ?, ?, ?, ?)",
                            [$customerId, $name, $url, $secret, json_encode($events)]
                        );
                        
                        $_SESSION['new_webhook_secret'] = $secret;
                        $success = 'Webhook erfolgreich erstellt! Bitte speichern Sie das Secret sicher ab.';
                    }
                }
                break;
                
            case 'delete_webhook':
                $webhookId = (int)($_POST['webhook_id'] ?? 0);
                $db->query(
                    "DELETE FROM api_webhooks WHERE id = ? AND customer_id = ?",
                    [$webhookId, $customerId]
                );
                $success = 'Webhook wurde gelöscht.';
                break;
                
            case 'toggle_webhook':
                $webhookId = (int)($_POST['webhook_id'] ?? 0);
                $db->query(
                    "UPDATE api_webhooks SET is_active = NOT is_active WHERE id = ? AND customer_id = ?",
                    [$webhookId, $customerId]
                );
                $success = 'Webhook Status wurde geändert.';
                break;
                
            case 'test_webhook':
                $webhookId = (int)($_POST['webhook_id'] ?? 0);
                $webhook = $db->fetch(
                    "SELECT * FROM api_webhooks WHERE id = ? AND customer_id = ?",
                    [$webhookId, $customerId]
                );
                
                if ($webhook) {
                    // Test-Payload senden
                    $testPayload = [
                        'event' => 'test',
                        'timestamp' => date('c'),
                        'data' => [
                            'message' => 'Dies ist ein Test-Webhook von Leadbusiness'
                        ]
                    ];
                    
                    $signature = hash_hmac('sha256', json_encode($testPayload), $webhook['secret']);
                    
                    $ch = curl_init($webhook['url']);
                    curl_setopt_array($ch, [
                        CURLOPT_POST => true,
                        CURLOPT_POSTFIELDS => json_encode($testPayload),
                        CURLOPT_HTTPHEADER => [
                            'Content-Type: application/json',
                            'X-Webhook-Signature: ' . $signature,
                            'X-Webhook-Event: test'
                        ],
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 10
                    ]);
                    
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    if ($httpCode >= 200 && $httpCode < 300) {
                        $success = "Test erfolgreich! HTTP Status: {$httpCode}";
                    } else {
                        $error = "Test fehlgeschlagen. HTTP Status: {$httpCode}";
                    }
                }
                break;
                
            case 'regenerate_secret':
                $webhookId = (int)($_POST['webhook_id'] ?? 0);
                $newSecret = 'whsec_' . bin2hex(random_bytes(24));
                $db->query(
                    "UPDATE api_webhooks SET secret = ? WHERE id = ? AND customer_id = ?",
                    [$newSecret, $webhookId, $customerId]
                );
                $_SESSION['new_webhook_secret'] = $newSecret;
                $success = 'Secret wurde neu generiert.';
                break;
        }
    }
}

// Webhooks laden
$webhooks = $db->fetchAll(
    "SELECT * FROM api_webhooks WHERE customer_id = ? ORDER BY created_at DESC",
    [$customerId]
);

$pageTitle = 'Webhooks';
require_once __DIR__ . '/../../includes/dashboard-header.php';
?>

<!-- Neues Webhook Secret -->
<?php if (isset($_SESSION['new_webhook_secret'])): ?>
<div class="mb-6 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl p-6">
    <div class="flex items-start gap-3">
        <i class="fas fa-exclamation-triangle text-amber-500 text-xl mt-1"></i>
        <div class="flex-1">
            <h3 class="font-semibold text-amber-800 dark:text-amber-200 mb-2">Wichtig: Speichern Sie das Secret!</h3>
            <p class="text-sm text-amber-700 dark:text-amber-300 mb-4">Das Webhook-Secret wird nur einmal angezeigt. Speichern Sie es sicher, um Webhook-Signaturen zu verifizieren.</p>
            
            <div class="flex items-center gap-2">
                <code class="flex-1 px-3 py-2 bg-white dark:bg-slate-800 rounded border border-amber-300 dark:border-amber-700 text-sm font-mono"><?= e($_SESSION['new_webhook_secret']) ?></code>
                <button onclick="copyToClipboard('<?= e($_SESSION['new_webhook_secret']) ?>')" class="px-3 py-2 bg-amber-100 dark:bg-amber-800 hover:bg-amber-200 dark:hover:bg-amber-700 rounded text-amber-700 dark:text-amber-200">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php unset($_SESSION['new_webhook_secret']); endif; ?>

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

<div class="grid lg:grid-cols-3 gap-6">
    
    <!-- Webhooks Liste -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <div class="p-6 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Ihre Webhooks</h2>
                        <p class="text-sm text-slate-500"><?= count($webhooks) ?> / <?= $maxWebhooks ?> Webhooks konfiguriert</p>
                    </div>
                    <?php if (count($webhooks) < $maxWebhooks): ?>
                    <button onclick="document.getElementById('createWebhookModal').classList.remove('hidden')" 
                            class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Neuer Webhook
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (empty($webhooks)): ?>
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bolt text-2xl text-slate-400"></i>
                </div>
                <p class="text-slate-600 dark:text-slate-400 mb-2">Noch keine Webhooks konfiguriert</p>
                <p class="text-sm text-slate-500 mb-4">Webhooks ermöglichen es Ihnen, bei bestimmten Events automatisch benachrichtigt zu werden.</p>
                <button onclick="document.getElementById('createWebhookModal').classList.remove('hidden')" 
                        class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium">
                    Ersten Webhook erstellen
                </button>
            </div>
            <?php else: ?>
            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($webhooks as $webhook): 
                    $events = json_decode($webhook['events'], true) ?: [];
                ?>
                <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-medium text-slate-800 dark:text-white"><?= e($webhook['name']) ?></span>
                                <?php if ($webhook['is_active']): ?>
                                <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs rounded-full">Aktiv</span>
                                <?php else: ?>
                                <span class="px-2 py-0.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs rounded-full">Inaktiv</span>
                                <?php endif; ?>
                            </div>
                            
                            <code class="text-xs font-mono text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded block truncate mb-2"><?= e($webhook['url']) ?></code>
                            
                            <div class="flex flex-wrap gap-1 mb-2">
                                <?php foreach ($events as $event): ?>
                                <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs rounded"><?= e($event) ?></span>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="flex items-center gap-4 text-xs text-slate-500">
                                <span><i class="fas fa-calendar mr-1"></i>Erstellt: <?= date('d.m.Y', strtotime($webhook['created_at'])) ?></span>
                                <?php if ($webhook['last_triggered_at']): ?>
                                <span><i class="fas fa-clock mr-1"></i>Zuletzt: <?= date('d.m.Y H:i', strtotime($webhook['last_triggered_at'])) ?></span>
                                <?php endif; ?>
                                <span><i class="fas fa-paper-plane mr-1"></i><?= number_format($webhook['total_triggers']) ?> Ausführungen</span>
                                <?php if ($webhook['failed_triggers'] > 0): ?>
                                <span class="text-red-500"><i class="fas fa-exclamation-triangle mr-1"></i><?= $webhook['failed_triggers'] ?> Fehler</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-1">
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="test_webhook">
                                <input type="hidden" name="webhook_id" value="<?= $webhook['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-primary-600" title="Test senden">
                                    <i class="fas fa-vial"></i>
                                </button>
                            </form>
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="toggle_webhook">
                                <input type="hidden" name="webhook_id" value="<?= $webhook['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="<?= $webhook['is_active'] ? 'Deaktivieren' : 'Aktivieren' ?>">
                                    <i class="fas fa-<?= $webhook['is_active'] ? 'pause' : 'play' ?>"></i>
                                </button>
                            </form>
                            <form method="POST" class="inline" onsubmit="return confirm('Secret wirklich neu generieren?')">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="regenerate_secret">
                                <input type="hidden" name="webhook_id" value="<?= $webhook['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-amber-600" title="Secret neu generieren">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                            <form method="POST" class="inline" onsubmit="return confirm('Webhook wirklich löschen?')">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="action" value="delete_webhook">
                                <input type="hidden" name="webhook_id" value="<?= $webhook['id'] ?>">
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
    </div>
    
    <!-- Info Sidebar -->
    <div class="space-y-6">
        
        <!-- Verfügbare Events -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
                <i class="fas fa-list text-primary-500 mr-2"></i>Verfügbare Events
            </h3>
            
            <div class="space-y-2">
                <?php foreach ($availableEvents as $eventKey => $eventName): ?>
                <div class="flex items-center gap-2 text-sm">
                    <i class="fas fa-check text-green-500"></i>
                    <code class="text-xs font-mono text-slate-600 dark:text-slate-300"><?= e($eventKey) ?></code>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Signatur-Verifizierung -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
                <i class="fas fa-shield-alt text-primary-500 mr-2"></i>Signatur verifizieren
            </h3>
            
            <p class="text-sm text-slate-600 dark:text-slate-300 mb-4">
                Jeder Webhook enthält einen <code class="text-xs bg-slate-100 dark:bg-slate-700 px-1 rounded">X-Webhook-Signature</code> Header.
            </p>
            
            <pre class="px-3 py-2 bg-slate-900 dark:bg-slate-950 rounded text-xs font-mono text-green-400 overflow-x-auto whitespace-pre-wrap">// PHP Beispiel
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'];
$secret = 'whsec_xxx';

$expected = hash_hmac('sha256', $payload, $secret);

if (hash_equals($expected, $signature)) {
    // Signatur gültig
}</pre>
        </div>
        
        <!-- Payload Format -->
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
                <i class="fas fa-code text-primary-500 mr-2"></i>Payload Format
            </h3>
            
            <pre class="px-3 py-2 bg-slate-900 dark:bg-slate-950 rounded text-xs font-mono text-green-400 overflow-x-auto">{
  "event": "lead.created",
  "timestamp": "2024-01-15T10:30:00Z",
  "data": {
    "id": 123,
    "name": "Max Mustermann",
    "email": "max@example.com",
    "referrer_code": "ABC123"
  }
}</pre>
        </div>
        
    </div>
</div>

<!-- Create Webhook Modal -->
<div id="createWebhookModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('createWebhookModal').classList.add('hidden')"></div>
        
        <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">Neuen Webhook erstellen</h3>
            
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                <input type="hidden" name="action" value="create_webhook">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Name</label>
                        <input type="text" name="webhook_name" value="Mein Webhook" class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white" placeholder="z.B. CRM Integration">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Webhook URL (HTTPS)</label>
                        <input type="url" name="webhook_url" required class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white" placeholder="https://ihre-seite.de/webhook">
                        <p class="text-xs text-slate-500 mt-1">Nur HTTPS-URLs werden akzeptiert.</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Events</label>
                        <div class="space-y-2 max-h-48 overflow-y-auto">
                            <?php foreach ($availableEvents as $eventKey => $eventName): ?>
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="events[]" value="<?= e($eventKey) ?>" class="w-4 h-4 text-primary-500 rounded">
                                <span class="text-sm text-slate-600 dark:text-slate-300"><?= e($eventName) ?></span>
                                <code class="text-xs text-slate-400 font-mono"><?= e($eventKey) ?></code>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('createWebhookModal').classList.add('hidden')" 
                            class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                        Abbrechen
                    </button>
                    <button type="submit" class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg font-medium">
                        Webhook erstellen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('In Zwischenablage kopiert!');
    });
}
</script>

<?php require_once __DIR__ . '/../../includes/dashboard-footer.php'; ?>
