<?php
/**
 * Dashboard-Modul: Website-Widget
 * Embed-Code Generator für die eigene Kundenwebsite
 * Verfügbar: Professional & Enterprise
 */

// Sicherstellen dass Variablen verfügbar sind
if (!isset($customer) || !isset($campaign)) {
    return;
}

$referralUrl = "https://{$customer['subdomain']}.empfehlungen.cloud";
$widgetId = 'lb-widget-' . substr(md5($customer['subdomain']), 0, 8);
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">
            <i class="fas fa-code text-primary-500 mr-2"></i>
            Website-Widget
        </h3>
        <a href="/dashboard/api.php#widget" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
            Mehr Optionen →
        </a>
    </div>
    
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
        Binden Sie Ihr Empfehlungsprogramm direkt auf Ihrer Website ein.
    </p>
    
    <!-- Widget-Vorschau -->
    <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 mb-4">
        <div class="text-xs text-slate-500 dark:text-slate-400 mb-2 font-medium">VORSCHAU</div>
        <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-600 text-center">
            <div class="text-lg font-bold text-slate-800 dark:text-white mb-2">
                Empfehlen Sie uns weiter!
            </div>
            <div class="text-sm text-slate-600 dark:text-slate-400 mb-3">
                Erhalten Sie tolle Belohnungen für Ihre Empfehlungen
            </div>
            <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500 text-white rounded-lg text-sm font-medium">
                <i class="fas fa-gift"></i>
                Jetzt mitmachen
            </div>
        </div>
    </div>
    
    <!-- Embed Codes -->
    <div class="space-y-4">
        
        <!-- Popup Button -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                <i class="fas fa-window-maximize mr-1"></i> Popup-Button
            </label>
            <div class="relative">
                <pre class="bg-slate-900 text-green-400 text-xs p-3 rounded-lg overflow-x-auto"><code>&lt;script src="<?= $referralUrl ?>/widget/popup.js"&gt;&lt;/script&gt;
&lt;button onclick="LeadbusinessWidget.open()"&gt;
    Empfehlen &amp; Belohnung erhalten
&lt;/button&gt;</code></pre>
                <button onclick="copyWidgetCode('popup')" class="absolute top-2 right-2 px-2 py-1 bg-slate-700 hover:bg-slate-600 text-white text-xs rounded transition-colors" data-type="popup">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
        
        <!-- Inline Embed -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                <i class="fas fa-square mr-1"></i> Inline-Widget
            </label>
            <div class="relative">
                <pre class="bg-slate-900 text-green-400 text-xs p-3 rounded-lg overflow-x-auto"><code>&lt;div id="<?= e($widgetId) ?>"&gt;&lt;/div&gt;
&lt;script src="<?= $referralUrl ?>/widget/embed.js"&gt;&lt;/script&gt;</code></pre>
                <button onclick="copyWidgetCode('inline')" class="absolute top-2 right-2 px-2 py-1 bg-slate-700 hover:bg-slate-600 text-white text-xs rounded transition-colors" data-type="inline">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
        
        <!-- Floating Button -->
        <div>
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                <i class="fas fa-circle mr-1"></i> Floating-Button (Ecke)
            </label>
            <div class="relative">
                <pre class="bg-slate-900 text-green-400 text-xs p-3 rounded-lg overflow-x-auto"><code>&lt;script src="<?= $referralUrl ?>/widget/floating.js"&gt;&lt;/script&gt;</code></pre>
                <button onclick="copyWidgetCode('floating')" class="absolute top-2 right-2 px-2 py-1 bg-slate-700 hover:bg-slate-600 text-white text-xs rounded transition-colors" data-type="floating">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Help Link -->
    <div class="mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
        <a href="/dashboard/api.php#widget-docs" class="text-sm text-primary-600 dark:text-primary-400 hover:underline inline-flex items-center gap-1">
            <i class="fas fa-book"></i>
            Widget-Dokumentation
        </a>
    </div>
</div>

<script>
function copyWidgetCode(type) {
    const codes = {
        popup: `<script src="<?= $referralUrl ?>/widget/popup.js"><\/script>
<button onclick="LeadbusinessWidget.open()">
    Empfehlen & Belohnung erhalten
</button>`,
        inline: `<div id="<?= e($widgetId) ?>"></div>
<script src="<?= $referralUrl ?>/widget/embed.js"><\/script>`,
        floating: `<script src="<?= $referralUrl ?>/widget/floating.js"><\/script>`
    };
    
    navigator.clipboard.writeText(codes[type]).then(() => {
        const btn = document.querySelector(`button[data-type="${type}"]`);
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(() => { btn.innerHTML = originalHtml; }, 2000);
    });
}
</script>
