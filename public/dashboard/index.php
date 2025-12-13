<?php
/**
 * Leadbusiness - Kunden-Dashboard
 * Mit Dark/Light Mode und Setup-Wizard
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/SetupWizard.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Auth prüfen
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

// Setup-Wizard initialisieren
$setupWizard = new \Leadbusiness\SetupWizard($customer);

// Statistiken laden
$stats = $db->fetch(
    "SELECT 
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status IN ('active', 'pending')) as total_leads,
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status = 'active') as active_leads,
        (SELECT SUM(conversions) FROM leads WHERE customer_id = ?) as total_conversions,
        (SELECT SUM(clicks) FROM leads WHERE customer_id = ?) as total_clicks,
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND DATE(created_at) = CURDATE()) as leads_today,
        (SELECT COUNT(*) FROM conversions c 
         JOIN leads l ON c.referrer_id = l.id 
         WHERE l.customer_id = ? AND c.status = 'confirmed' AND DATE(c.created_at) = CURDATE()) as conversions_today
    ",
    [$customerId, $customerId, $customerId, $customerId, $customerId, $customerId]
);

$conversionRate = $stats['total_clicks'] > 0 ? round(($stats['total_conversions'] / $stats['total_clicks']) * 100, 1) : 0;

// Letzte Aktivitäten
$recentActivity = $db->fetchAll(
    "SELECT 'lead' as type, l.name, l.email, l.created_at, 'Neuer Empfehler' as action
     FROM leads l WHERE l.customer_id = ?
     UNION ALL
     SELECT 'conversion' as type, l.name, l.email, c.created_at, 'Erfolgreiche Empfehlung' as action
     FROM conversions c JOIN leads l ON c.referrer_id = l.id
     WHERE l.customer_id = ? AND c.status = 'confirmed'
     ORDER BY created_at DESC LIMIT 10",
    [$customerId, $customerId]
);

// Top Empfehler
$topLeads = $db->fetchAll(
    "SELECT name, email, conversions, clicks FROM leads
     WHERE customer_id = ? AND status = 'active' AND conversions > 0
     ORDER BY conversions DESC LIMIT 5",
    [$customerId]
);

// Chart-Daten
$chartData = $db->fetchAll(
    "SELECT DATE(created_at) as date, COUNT(*) as count FROM leads
     WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
     GROUP BY DATE(created_at) ORDER BY date ASC",
    [$customerId]
);

$isNewCustomer = isset($_GET['welcome']);
$pageTitle = 'Übersicht';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<?php if ($isNewCustomer): ?>
<!-- Welcome Banner (nur beim ersten Mal) -->
<div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 text-white mb-6">
    <div class="flex items-start gap-4">
        <div class="text-4xl">🎉</div>
        <div>
            <h2 class="text-xl font-bold mb-2">Herzlichen Glückwunsch!</h2>
            <p class="text-white/90 mb-4">
                Ihr Account wurde erfolgreich erstellt. Folgen Sie der Checkliste unten, um Ihr Empfehlungsprogramm zu vervollständigen.
            </p>
            <div class="flex items-center gap-2 bg-white/20 rounded-lg px-4 py-2 inline-flex">
                <code class="text-sm"><?= e($customer['subdomain']) ?>.empfehlungen.cloud</code>
                <button onclick="copyToClipboard('https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud', this)" class="text-white/80 hover:text-white">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php 
// Setup-Wizard anzeigen (wenn nicht versteckt oder nicht vollständig)
if (!$setupWizard->isHidden() || !$setupWizard->isSetupComplete()):
    include __DIR__ . '/../../includes/components/setup-wizard-widget.php';
endif;
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-blue-500 dark:text-blue-400 text-xl"></i>
            </div>
            <?php if ($stats['leads_today'] > 0): ?>
            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-full">
                +<?= $stats['leads_today'] ?> heute
            </span>
            <?php endif; ?>
        </div>
        <div class="text-3xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_leads'] ?? 0, 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Empfehler gesamt</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-check text-green-500 dark:text-green-400 text-xl"></i>
            </div>
            <?php if ($stats['conversions_today'] > 0): ?>
            <span class="px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-medium rounded-full">
                +<?= $stats['conversions_today'] ?> heute
            </span>
            <?php endif; ?>
        </div>
        <div class="text-3xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_conversions'] ?? 0, 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Erfolgreiche Empfehlungen</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-mouse-pointer text-amber-500 dark:text-amber-400 text-xl"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_clicks'] ?? 0, 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Link-Klicks</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 hover:-translate-y-1 transition-transform">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                <i class="fas fa-percentage text-purple-500 dark:text-purple-400 text-xl"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-slate-800 dark:text-white"><?= $conversionRate ?>%</div>
        <div class="text-sm text-slate-500 dark:text-slate-400">Conversion-Rate</div>
    </div>
</div>

<!-- Quick Actions (wenn Setup nicht vollständig) -->
<?php if (!$setupWizard->isSetupComplete()): ?>
<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl p-4 mb-8">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center text-amber-600 dark:text-amber-400">
            <i class="fas fa-lightbulb"></i>
        </div>
        <div class="flex-1">
            <p class="text-amber-800 dark:text-amber-200 text-sm">
                <strong>Tipp:</strong> Vervollständigen Sie die Einrichtung, um Ihre Conversion-Rate zu verbessern. 
                Ein professionelles Logo und Design erhöhen das Vertrauen Ihrer Empfehler.
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Charts & Activity -->
<div class="grid lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Chart -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-chart-line text-primary-500 mr-2"></i>Neue Empfehler (14 Tage)
        </h3>
        <div class="h-64">
            <canvas id="leadsChart"></canvas>
        </div>
    </div>
    
    <!-- Top Leads -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-trophy text-amber-500 mr-2"></i>Top Empfehler
        </h3>
        
        <?php if (empty($topLeads)): ?>
        <div class="text-center py-8">
            <div class="w-16 h-16 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-slate-400 dark:text-slate-500 text-2xl"></i>
            </div>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">Noch keine aktiven Empfehler.</p>
            <a href="/dashboard/share.php" class="text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline">
                Jetzt Empfehlungslink teilen →
            </a>
        </div>
        <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($topLeads as $index => $lead): ?>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                    <?= $index === 0 ? 'bg-amber-400 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300' ?>">
                    <?= $index + 1 ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-medium text-slate-800 dark:text-white truncate">
                        <?= e($lead['name'] ?: 'Anonymer Empfehler') ?>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400"><?= $lead['conversions'] ?> Empfehlungen</div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <a href="/dashboard/leads.php" class="mt-4 text-primary-600 dark:text-primary-400 text-sm font-medium hover:underline inline-block">
            Alle Empfehler ansehen →
        </a>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-4">
        <i class="fas fa-clock text-primary-500 mr-2"></i>Letzte Aktivitäten
    </h3>
    
    <?php if (empty($recentActivity)): ?>
    <div class="text-center py-12">
        <div class="w-20 h-20 bg-slate-100 dark:bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-share-alt text-slate-400 dark:text-slate-500 text-3xl"></i>
        </div>
        <p class="text-slate-600 dark:text-slate-300 font-medium mb-2">Noch keine Aktivitäten</p>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">
            Teilen Sie Ihren Empfehlungslink mit Ihren Kunden, um loszulegen.
        </p>
        <a href="/dashboard/share.php" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-share-alt"></i>
            Empfehlungslink teilen
        </a>
    </div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($recentActivity as $activity): ?>
        <div class="flex items-center gap-4 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
            <div class="w-10 h-10 rounded-full flex items-center justify-center
                <?= $activity['type'] === 'conversion' ? 'bg-green-100 dark:bg-green-900/30 text-green-500 dark:text-green-400' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-500 dark:text-blue-400' ?>">
                <i class="fas <?= $activity['type'] === 'conversion' ? 'fa-check' : 'fa-user-plus' ?>"></i>
            </div>
            <div class="flex-1">
                <div class="font-medium text-slate-800 dark:text-white"><?= e($activity['action']) ?></div>
                <div class="text-sm text-slate-500 dark:text-slate-400">
                    <?= e($activity['name'] ?: $activity['email']) ?> · <?= timeAgo($activity['created_at']) ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<script>
    // Chart
    const chartData = <?= json_encode($chartData) ?>;
    const isDark = document.documentElement.classList.contains('dark');
    
    // Labels für letzte 14 Tage generieren
    const labels = [];
    const data = [];
    for (let i = 13; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        labels.push(date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' }));
        
        const found = chartData.find(d => d.date === dateStr);
        data.push(found ? parseInt(found.count) : 0);
    }
    
    new Chart(document.getElementById('leadsChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Neue Empfehler',
                data: data,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 3,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: isDark ? '#334155' : '#e2e8f0' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b', stepSize: 1 }
                }
            }
        }
    });
    
    // Copy to clipboard function
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(() => {
            const icon = btn.querySelector('i');
            icon.className = 'fas fa-check';
            setTimeout(() => {
                icon.className = 'fas fa-copy';
            }, 2000);
        });
    }
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
