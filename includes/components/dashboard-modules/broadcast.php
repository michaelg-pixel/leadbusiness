<?php
/**
 * Dashboard-Modul: Broadcast E-Mails
 * Schnellzugang für Broadcast-Mails an alle Empfehler
 * Verfügbar: Professional & Enterprise
 */

if (!isset($customer) || !isset($customerId)) {
    return;
}

// Statistiken für Broadcast laden
$broadcastStats = $db->fetch(
    "SELECT 
        COUNT(*) as total_broadcasts,
        (SELECT COUNT(*) FROM broadcast_emails WHERE customer_id = ? AND status = 'sent' AND created_at > DATE_SUB(NOW(), INTERVAL 30 DAY)) as recent_broadcasts,
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status = 'active' AND email_confirmed = 1) as active_leads
    FROM broadcast_emails WHERE customer_id = ?",
    [$customerId, $customerId, $customerId]
) ?: ['total_broadcasts' => 0, 'recent_broadcasts' => 0, 'active_leads' => 0];

// Letzter Broadcast
$lastBroadcast = $db->fetch(
    "SELECT subject, created_at, 
        (SELECT COUNT(*) FROM broadcast_email_sends WHERE broadcast_id = broadcast_emails.id AND status = 'delivered') as delivered,
        (SELECT COUNT(*) FROM broadcast_email_sends WHERE broadcast_id = broadcast_emails.id AND status = 'opened') as opened
     FROM broadcast_emails 
     WHERE customer_id = ? AND status = 'sent'
     ORDER BY created_at DESC LIMIT 1",
    [$customerId]
);
?>

<div class="p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white">
            <i class="fas fa-paper-plane text-primary-500 mr-2"></i>
            Broadcast E-Mails
        </h3>
        <a href="/dashboard/broadcasts.php" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
            Alle anzeigen →
        </a>
    </div>
    
    <!-- Quick Stats -->
    <div class="grid grid-cols-2 gap-3 mb-4">
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-slate-800 dark:text-white">
                <?= number_format($broadcastStats['active_leads'] ?? 0, 0, ',', '.') ?>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Aktive Empfänger</div>
        </div>
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
            <div class="text-2xl font-bold text-slate-800 dark:text-white">
                <?= $broadcastStats['recent_broadcasts'] ?? 0 ?>
            </div>
            <div class="text-xs text-slate-500 dark:text-slate-400">Mails (30 Tage)</div>
        </div>
    </div>
    
    <?php if ($lastBroadcast): ?>
    <!-- Letzte Broadcast -->
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-4">
        <div class="text-xs text-green-600 dark:text-green-400 font-medium mb-1">LETZTER BROADCAST</div>
        <div class="font-medium text-slate-800 dark:text-white truncate mb-2">
            <?= e($lastBroadcast['subject']) ?>
        </div>
        <div class="flex items-center gap-4 text-xs text-slate-600 dark:text-slate-400">
            <span><i class="fas fa-paper-plane mr-1"></i><?= $lastBroadcast['delivered'] ?? 0 ?> gesendet</span>
            <span><i class="fas fa-envelope-open mr-1"></i><?= $lastBroadcast['opened'] ?? 0 ?> geöffnet</span>
        </div>
        <div class="text-xs text-slate-500 dark:text-slate-500 mt-1">
            <?= timeAgo($lastBroadcast['created_at']) ?>
        </div>
    </div>
    <?php endif; ?>
    
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
    
    <?php if (($broadcastStats['active_leads'] ?? 0) == 0): ?>
    <!-- Keine Empfänger Hinweis -->
    <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
        <div class="flex items-center gap-2 text-sm text-amber-700 dark:text-amber-300">
            <i class="fas fa-info-circle"></i>
            <span>Noch keine bestätigten Empfehler. Teilen Sie Ihren Link!</span>
        </div>
    </div>
    <?php endif; ?>
</div>
