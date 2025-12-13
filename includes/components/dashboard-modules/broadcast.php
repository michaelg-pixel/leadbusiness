<?php
/**
 * Dashboard-Modul: Broadcast E-Mails
 * Schnellzugang für Broadcast-Mails an alle Empfehler
 * Verfügbar: Professional & Enterprise
 */

if (!isset($customer) || !isset($customerId)) {
    return;
}

// Statistiken laden (sichere Abfragen)
$activeLeadsCount = $db->fetch(
    "SELECT COUNT(*) as count FROM leads WHERE customer_id = ? AND status = 'active' AND email_confirmed = 1",
    [$customerId]
)['count'] ?? 0;

// Prüfen ob email_queue Einträge vorhanden sind
$recentEmailCount = $db->fetch(
    "SELECT COUNT(*) as count FROM email_queue WHERE customer_id = ? AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)",
    [$customerId]
)['count'] ?? 0;
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">
            <i class="fas fa-paper-plane text-primary-500 mr-2"></i>
            Broadcast E-Mails
        </h3>
    </div>
    
    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
        Senden Sie Nachrichten an alle Ihre bestätigten Empfehler.
    </p>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-slate-800 dark:text-white">
                <?= number_format($activeLeadsCount, 0, ',', '.') ?>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Aktive Empfänger</div>
        </div>
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-slate-800 dark:text-white">
                <?= number_format($recentEmailCount, 0, ',', '.') ?>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Mails (7 Tage)</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="space-y-2">
        <a href="/dashboard/broadcasts.php?action=new" class="flex items-center gap-3 p-3 bg-primary-50 dark:bg-primary-900/20 hover:bg-primary-100 dark:hover:bg-primary-900/30 rounded-xl transition-colors group">
            <div class="w-10 h-10 bg-primary-500 text-white rounded-lg flex items-center justify-center group-hover:scale-105 transition-transform">
                <i class="fas fa-plus"></i>
            </div>
            <div>
                <div class="font-medium text-primary-700 dark:text-primary-300">Neue Broadcast-Mail</div>
                <div class="text-xs text-primary-600/70 dark:text-primary-400/70">An alle aktiven Empfehler senden</div>
            </div>
        </a>
        
        <!-- Schnellvorlagen -->
        <div class="text-xs text-slate-500 dark:text-slate-400 mt-3 mb-2">SCHNELLVORLAGEN</div>
        <div class="flex gap-2">
            <a href="/dashboard/broadcasts.php?template=reminder" class="flex-1 p-2 text-center text-sm bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors text-slate-700 dark:text-slate-300">
                <i class="fas fa-bell mr-1"></i>
                Erinnerung
            </a>
            <a href="/dashboard/broadcasts.php?template=news" class="flex-1 p-2 text-center text-sm bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors text-slate-700 dark:text-slate-300">
                <i class="fas fa-star mr-1"></i>
                Neuigkeit
            </a>
            <a href="/dashboard/broadcasts.php?template=promo" class="flex-1 p-2 text-center text-sm bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-colors text-slate-700 dark:text-slate-300">
                <i class="fas fa-gift mr-1"></i>
                Aktion
            </a>
        </div>
    </div>
    
    <?php if ($activeLeadsCount == 0): ?>
    <!-- Keine Empfänger Hinweis -->
    <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
        <div class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-300">
            <i class="fas fa-info-circle"></i>
            <span>Noch keine bestätigten Empfehler. Teilen Sie Ihren Link!</span>
        </div>
    </div>
    <?php endif; ?>
</div>
