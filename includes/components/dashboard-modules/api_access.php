<?php
/**
 * Dashboard-Modul: API & Webhooks
 * API-Zugang und Webhook-Konfiguration
 * Verfügbar: Professional & Enterprise
 */

if (!isset($customer) || !isset($customerId)) {
    return;
}

// API-Key aus Customers-Tabelle (sicher abfragen)
$apiKeyData = $db->fetch(
    "SELECT api_key FROM api_keys WHERE customer_id = ? AND is_active = 1 ORDER BY created_at DESC LIMIT 1",
    [$customerId]
);
$apiKey = $apiKeyData['api_key'] ?? null;

// Webhook Count
$webhookCount = $db->fetch(
    "SELECT COUNT(*) as count FROM webhooks WHERE customer_id = ? AND is_active = 1",
    [$customerId]
)['count'] ?? 0;

// API-Key maskieren
$maskedKey = $apiKey 
    ? substr($apiKey, 0, 8) . '••••••••' . substr($apiKey, -4)
    : null;
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
    
    <!-- API Key Section -->
    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full <?= $apiKey ? 'bg-green-500' : 'bg-slate-400' ?>"></div>
                <span class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    API <?= $apiKey ? 'Aktiv' : 'Nicht eingerichtet' ?>
                </span>
            </div>
        </div>
        
        <?php if ($apiKey): ?>
        <div class="flex items-center gap-2 bg-slate-100 dark:bg-slate-600 rounded-lg px-3 py-2">
            <code id="apiKeyDisplay" class="flex-1 text-xs text-slate-600 dark:text-slate-300 font-mono truncate">
                <?= e($maskedKey) ?>
            </code>
            <button onclick="toggleApiKey()" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" title="Key anzeigen/verbergen">
                <i class="fas fa-eye" id="eyeIcon"></i>
            </button>
            <button onclick="copyApiKey()" class="text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200" title="Key kopieren">
                <i class="fas fa-copy"></i>
            </button>
        </div>
        <?php else: ?>
        <a href="/dashboard/api.php#generate" class="inline-flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 hover:underline">
            <i class="fas fa-key"></i>
            API-Key generieren
        </a>
        <?php endif; ?>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
            <div class="text-xl font-bold text-purple-600 dark:text-purple-400">
                <?= $webhookCount ?>
            </div>
            <div class="text-xs text-purple-600/70 dark:text-purple-400/70">Aktive Webhooks</div>
        </div>
        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
            <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="text-xs text-blue-600/70 dark:text-blue-400/70">API bereit</div>
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
        <div class="text-xs text-slate-500 dark:text-slate-400 mb-2">WEBHOOK-EVENTS</div>
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
(function() {
    const fullApiKey = '<?= e($apiKey ?? '') ?>';
    const maskedKey = '<?= e($maskedKey ?? '') ?>';
    let isVisible = false;
    
    window.toggleApiKey = function() {
        if (!fullApiKey) return;
        
        const display = document.getElementById('apiKeyDisplay');
        const icon = document.getElementById('eyeIcon');
        
        isVisible = !isVisible;
        display.textContent = isVisible ? fullApiKey : maskedKey;
        icon.className = isVisible ? 'fas fa-eye-slash' : 'fas fa-eye';
    };
    
    window.copyApiKey = function() {
        if (!fullApiKey) return;
        
        navigator.clipboard.writeText(fullApiKey).then(() => {
            if (typeof showToast === 'function') {
                showToast('API-Key kopiert!', 'success');
            } else {
                alert('API-Key kopiert!');
            }
        });
    };
})();
</script>
