<?php
/**
 * Dashboard-Modul: API & Webhooks
 * API-Zugang und Webhook-Konfiguration
 * Verfügbar: Professional & Enterprise
 */

if (!isset($customer) || !isset($customerId)) {
    return;
}

// API-Key und Webhook-Stats laden
$apiStats = $db->fetch(
    "SELECT 
        api_key,
        api_enabled,
        (SELECT COUNT(*) FROM webhooks WHERE customer_id = ? AND is_active = TRUE) as active_webhooks,
        (SELECT COUNT(*) FROM api_requests WHERE customer_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)) as requests_24h
    FROM customers WHERE id = ?",
    [$customerId, $customerId, $customerId]
);

// API-Key maskieren
$maskedKey = $apiStats['api_key'] 
    ? substr($apiStats['api_key'], 0, 8) . '...' . substr($apiStats['api_key'], -4)
    : 'Nicht generiert';
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">
            <i class="fas fa-plug text-primary-500 mr-2"></i>
            API & Webhooks
        </h3>
        <a href="/dashboard/api.php" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
            Dokumentation →
        </a>
    </div>
    
    <!-- API Status -->
    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full <?= $apiStats['api_enabled'] ? 'bg-green-500' : 'bg-slate-400' ?>"></div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    API <?= $apiStats['api_enabled'] ? 'Aktiv' : 'Inaktiv' ?>
                </span>
            </div>
            <a href="/dashboard/api.php#settings" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                Einstellungen
            </a>
        </div>
        
        <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-600 rounded-lg px-3 py-2">
            <code class="flex-1 text-xs text-slate-600 dark:text-slate-300 font-mono truncate">
                <?= e($maskedKey) ?>
            </code>
            <?php if ($apiStats['api_key']): ?>
            <button onclick="toggleApiKey()" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" title="Key anzeigen">
                <i class="fas fa-eye"></i>
            </button>
            <button onclick="copyApiKey()" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" title="Key kopieren">
                <i class="fas fa-copy"></i>
            </button>
            <?php else: ?>
            <a href="/dashboard/api.php#generate" class="text-xs text-primary-600 dark:text-primary-400 hover:underline">
                Generieren
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
            <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                <?= $apiStats['requests_24h'] ?? 0 ?>
            </div>
            <div class="text-xs text-blue-600/70 dark:text-blue-400/70">API-Requests (24h)</div>
        </div>
        <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
            <div class="text-xl font-bold text-purple-600 dark:text-purple-400">
                <?= $apiStats['active_webhooks'] ?? 0 ?>
            </div>
            <div class="text-xs text-purple-600/70 dark:text-purple-400/70">Aktive Webhooks</div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="space-y-2">
        <a href="/dashboard/webhooks.php" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors group">
            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 text-purple-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-bolt"></i>
            </div>
            <div class="flex-1">
                <div class="text-sm font-medium text-slate-800 dark:text-white">Webhooks verwalten</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Events an Ihre Systeme senden</div>
            </div>
            <i class="fas fa-chevron-right text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200"></i>
        </a>
        
        <a href="/dashboard/api.php#endpoints" class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition-colors group">
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 text-blue-500 rounded-lg flex items-center justify-center">
                <i class="fas fa-code"></i>
            </div>
            <div class="flex-1">
                <div class="text-sm font-medium text-slate-800 dark:text-white">API-Endpoints</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">REST API Dokumentation</div>
            </div>
            <i class="fas fa-chevron-right text-slate-400 group-hover:text-slate-600 dark:group-hover:text-slate-200"></i>
        </a>
    </div>
    
    <!-- Verfügbare Events -->
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
        <div class="text-xs text-slate-500 dark:text-slate-400 mb-2">VERFÜGBARE WEBHOOK-EVENTS</div>
        <div class="flex flex-wrap gap-1">
            <?php 
            $events = ['lead.created', 'lead.confirmed', 'conversion.new', 'reward.unlocked'];
            foreach ($events as $event): 
            ?>
            <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 text-xs rounded font-mono">
                <?= $event ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
let apiKeyVisible = false;
const fullApiKey = '<?= e($apiStats['api_key'] ?? '') ?>';

function toggleApiKey() {
    const codeEl = document.querySelector('[data-api-key]');
    if (!codeEl) return;
    
    apiKeyVisible = !apiKeyVisible;
    codeEl.textContent = apiKeyVisible ? fullApiKey : '<?= e($maskedKey) ?>';
}

function copyApiKey() {
    if (!fullApiKey) return;
    navigator.clipboard.writeText(fullApiKey).then(() => {
        if (typeof showToast === 'function') {
            showToast('API-Key kopiert!', 'success');
        }
    });
}
</script>
