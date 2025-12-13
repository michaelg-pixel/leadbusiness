<?php
/**
 * Dashboard Module: Leads List
 * Verfügbar für: Professional, Enterprise
 * Zeigt eine kompakte Liste der Empfehler mit Suchfunktion
 */

if (!isset($customer) || !isset($db)) {
    return;
}

// Plan-Check
$isAvailable = in_array($customer['plan'], ['professional', 'enterprise']);

// Branchenspezifische Texte
$customerTerm = $dashboardLayout['customer_term'] ?? 'Kunden';

// Leads laden (nur wenn verfügbar)
$leads = [];
$totalLeads = 0;
$activeLeads = 0;

if ($isAvailable) {
    $leads = $db->fetchAll(
        "SELECT 
            l.id, l.name, l.email, l.referral_code, l.clicks, l.conversions,
            l.status, l.created_at, l.last_activity_at,
            (SELECT COUNT(*) FROM leads ref WHERE ref.referred_by_id = l.id) as referrals_count
         FROM leads l 
         WHERE l.customer_id = ? 
         ORDER BY l.conversions DESC, l.created_at DESC 
         LIMIT 10",
        [$customer['id']]
    );
    
    $stats = $db->fetch(
        "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as today
         FROM leads WHERE customer_id = ?",
        [$customer['id']]
    );
    
    $totalLeads = $stats['total'] ?? 0;
    $activeLeads = $stats['active'] ?? 0;
}

// Status-Labels
$statusLabels = [
    'active' => ['label' => 'Aktiv', 'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
    'pending' => ['label' => 'Ausstehend', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
    'inactive' => ['label' => 'Inaktiv', 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
    'blocked' => ['label' => 'Gesperrt', 'class' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300']
];
?>

<div class="dashboard-module module-leads-list p-6" data-module="leads_list">
    <div class="module-header flex justify-between items-center mb-4">
        <div>
            <h3 class="module-title text-lg font-bold text-gray-800 dark:text-white">
                <i class="fas fa-users text-primary-500 mr-2"></i>
                Empfehler
            </h3>
            <?php if ($isAvailable && $totalLeads > 0): ?>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                <?= number_format($activeLeads, 0, ',', '.') ?> aktiv von <?= number_format($totalLeads, 0, ',', '.') ?> gesamt
            </p>
            <?php endif; ?>
        </div>
        
        <?php if ($isAvailable): ?>
        <a href="/dashboard/leads.php" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
            Alle anzeigen <i class="fas fa-arrow-right ml-1"></i>
        </a>
        <?php else: ?>
        <span class="badge bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-medium">
            <i class="fas fa-star mr-1"></i> Professional
        </span>
        <?php endif; ?>
    </div>
    
    <?php if (!$isAvailable): ?>
    <!-- Upgrade-Hinweis für Starter -->
    <div class="upgrade-notice bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 text-center">
        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-users text-blue-500 text-2xl"></i>
        </div>
        <h4 class="font-bold text-gray-800 dark:text-white mb-2">
            Empfehler-Übersicht freischalten
        </h4>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
            Sehen Sie alle Ihre Empfehler, deren Performance und exportieren Sie die Daten.
        </p>
        <a href="/dashboard/upgrade.php" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-up mr-2"></i>
            Jetzt upgraden
        </a>
    </div>
    
    <?php elseif (empty($leads)): ?>
    <!-- Keine Empfehler -->
    <div class="empty-state text-center py-8">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-user-plus text-gray-400 text-2xl"></i>
        </div>
        <p class="text-gray-600 dark:text-gray-300 font-medium mb-2">
            Noch keine Empfehler
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            Teilen Sie Ihren Empfehlungslink, um die ersten Empfehler zu gewinnen.
        </p>
        <a href="/dashboard/share.php" class="btn btn-primary btn-sm">
            <i class="fas fa-share-alt mr-2"></i>
            Jetzt teilen
        </a>
    </div>
    
    <?php else: ?>
    
    <div class="module-content">
        <!-- Quick-Suche -->
        <div class="search-bar mb-4">
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                       id="leadSearchInput"
                       placeholder="Empfehler suchen..."
                       onkeyup="filterLeads(this.value)"
                       class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-gray-700 border-0 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            </div>
        </div>
        
        <!-- Leads-Liste -->
        <div class="leads-list space-y-2" id="leadsList">
            <?php foreach ($leads as $lead): ?>
            <div class="lead-item flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer"
                 onclick="showLeadDetails(<?= $lead['id'] ?>)"
                 data-search="<?= strtolower(e($lead['name'] . ' ' . $lead['email'])) ?>">
                
                <!-- Avatar -->
                <div class="lead-avatar w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm flex-shrink-0"
                     style="background-color: <?= stringToColor($lead['email']) ?>">
                    <?= strtoupper(substr($lead['name'] ?: $lead['email'], 0, 1)) ?>
                </div>
                
                <!-- Info -->
                <div class="lead-info flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-800 dark:text-white truncate">
                            <?= e($lead['name'] ?: 'Unbekannt') ?>
                        </span>
                        <?php 
                        $status = $statusLabels[$lead['status']] ?? $statusLabels['pending'];
                        ?>
                        <span class="px-1.5 py-0.5 rounded text-xs <?= $status['class'] ?>">
                            <?= $status['label'] ?>
                        </span>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        <?= e($lead['email']) ?>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="lead-stats flex items-center gap-4 flex-shrink-0">
                    <div class="text-center">
                        <div class="text-sm font-bold text-gray-800 dark:text-white">
                            <?= (int)$lead['conversions'] ?>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <?= $lead['conversions'] == 1 ? $customerTerm : $customerTerm ?>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-bold text-gray-800 dark:text-white">
                            <?= (int)$lead['clicks'] ?>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Klicks
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Mehr anzeigen -->
        <?php if ($totalLeads > 10): ?>
        <div class="mt-4 text-center">
            <a href="/dashboard/leads.php" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                Alle <?= number_format($totalLeads, 0, ',', '.') ?> Empfehler anzeigen →
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Quick Stats -->
        <div class="leads-quick-stats mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-3 gap-4 text-center">
            <div>
                <div class="text-lg font-bold text-gray-800 dark:text-white">
                    <?= number_format($totalLeads, 0, ',', '.') ?>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Gesamt</div>
            </div>
            <div>
                <div class="text-lg font-bold text-green-600">
                    <?= number_format($activeLeads, 0, ',', '.') ?>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Aktiv</div>
            </div>
            <div>
                <div class="text-lg font-bold text-blue-600">
                    <?= $stats['today'] ?? 0 ?>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Heute neu</div>
            </div>
        </div>
        
        <!-- Export-Button -->
        <?php if ($customer['plan'] === 'enterprise' || $customer['plan'] === 'professional'): ?>
        <div class="export-section mt-4">
            <button onclick="exportLeads()" class="btn btn-outline btn-sm w-full">
                <i class="fas fa-download mr-2"></i>
                Als CSV exportieren
            </button>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php
// Helper-Funktion für Avatar-Farben
function stringToColor($str) {
    $hash = md5($str);
    $colors = ['#6366f1', '#8b5cf6', '#ec4899', '#ef4444', '#f97316', '#eab308', '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6'];
    return $colors[hexdec(substr($hash, 0, 2)) % count($colors)];
}
?>

<script>
function filterLeads(query) {
    const items = document.querySelectorAll('#leadsList .lead-item');
    const lowerQuery = query.toLowerCase();
    
    items.forEach(item => {
        const searchText = item.dataset.search || '';
        item.style.display = searchText.includes(lowerQuery) ? '' : 'none';
    });
}

function showLeadDetails(leadId) {
    window.location.href = `/dashboard/leads.php?id=${leadId}`;
}

function exportLeads() {
    const customerId = <?= (int)$customer['id'] ?>;
    window.location.href = `/api/export-leads.php?customer_id=${customerId}&format=csv`;
    
    // Track
    fetch('/api/track-event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            customer_id: customerId,
            action: 'leads_export',
            format: 'csv'
        })
    }).catch(() => {});
}
</script>

<style>
.lead-item:hover .lead-stats {
    opacity: 1;
}

.lead-avatar {
    font-size: 0.75rem;
}
</style>
