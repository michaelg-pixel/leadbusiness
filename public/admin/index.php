<?php
/**
 * Admin Dashboard - Übersicht
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';

session_start();

// Auth Check
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = Database::getInstance();
$pageTitle = 'Dashboard';

// Statistiken laden
$stats = [
    'total_customers' => $db->fetchColumn("SELECT COUNT(*) FROM customers") ?? 0,
    'active_customers' => $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE subscription_status = 'active'") ?? 0,
    'total_leads' => $db->fetchColumn("SELECT COUNT(*) FROM leads") ?? 0,
    'total_conversions' => $db->fetchColumn("SELECT COUNT(*) FROM conversions WHERE status = 'confirmed'") ?? 0,
    'pending_fraud' => $db->fetchColumn("SELECT COUNT(*) FROM fraud_log WHERE action_taken = 'review' AND reviewed_at IS NULL") ?? 0,
    'emails_today' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE DATE(created_at) = CURDATE()") ?? 0,
];

// Umsatz berechnen (aus payments Tabelle)
$stats['revenue_total'] = $db->fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'") ?? 0;
$stats['revenue_month'] = $db->fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())") ?? 0;

// Neue Kunden diese Woche
$stats['new_customers_week'] = $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)") ?? 0;

// Chart-Daten: Neue Kunden pro Tag (letzte 30 Tage)
$chartData = $db->fetchAll("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM customers 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date
");

$chartLabels = [];
$chartValues = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('d.m.', strtotime($date));
    $found = false;
    foreach ($chartData as $row) {
        if ($row['date'] === $date) {
            $chartValues[] = (int)$row['count'];
            $found = true;
            break;
        }
    }
    if (!$found) $chartValues[] = 0;
}

// Letzte Aktivitäten
$recentActivities = $db->fetchAll("
    (SELECT 'customer' as type, c.company_name as title, c.created_at, c.id 
     FROM customers c ORDER BY created_at DESC LIMIT 5)
    UNION ALL
    (SELECT 'lead' as type, l.email as title, l.created_at, l.customer_id as id 
     FROM leads l ORDER BY created_at DESC LIMIT 5)
    ORDER BY created_at DESC
    LIMIT 10
");

// Top Kunden
$topCustomers = $db->fetchAll("
    SELECT c.*, 
           (SELECT COUNT(*) FROM leads WHERE customer_id = c.id) as lead_count,
           (SELECT COUNT(*) FROM conversions WHERE customer_id = c.id AND status = 'confirmed') as conversion_count
    FROM customers c
    ORDER BY lead_count DESC
    LIMIT 5
");

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Kunden -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-primary-600 dark:text-primary-400 text-xl"></i>
            </div>
            <span class="text-xs text-green-500 font-medium">
                +<?= $stats['new_customers_week'] ?> diese Woche
            </span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_customers'], 0, ',', '.') ?></h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Kunden gesamt</p>
        <div class="mt-2 text-xs text-slate-400">
            <?= $stats['active_customers'] ?> aktiv
        </div>
    </div>
    
    <!-- Leads -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_leads'], 0, ',', '.') ?></h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Empfehler gesamt</p>
    </div>
    
    <!-- Conversions -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-handshake text-purple-600 dark:text-purple-400 text-xl"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_conversions'], 0, ',', '.') ?></h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Conversions gesamt</p>
    </div>
    
    <!-- Umsatz -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-euro-sign text-amber-600 dark:text-amber-400 text-xl"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['revenue_total'], 2, ',', '.') ?> €</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Umsatz gesamt</p>
        <div class="mt-2 text-xs text-slate-400">
            <?= number_format($stats['revenue_month'], 2, ',', '.') ?> € diesen Monat
        </div>
    </div>
</div>

<!-- Charts & Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Chart: Neue Kunden -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-chart-line text-primary-500 mr-2"></i>
            Neue Kunden (letzte 30 Tage)
        </h3>
        <div class="h-64">
            <canvas id="customersChart"></canvas>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-bolt text-amber-500 mr-2"></i>
            Schnellübersicht
        </h3>
        
        <div class="space-y-4">
            <!-- Fraud Reviews -->
            <a href="/admin/fraud-review.php" class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50 hover:bg-slate-100 dark:hover:bg-slate-700 transition-all">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shield-exclamation text-red-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">Fraud Reviews</p>
                        <p class="text-xs text-slate-500">Zu prüfen</p>
                    </div>
                </div>
                <span class="text-2xl font-bold <?= $stats['pending_fraud'] > 0 ? 'text-red-500' : 'text-slate-400' ?>">
                    <?= $stats['pending_fraud'] ?>
                </span>
            </a>
            
            <!-- E-Mails heute -->
            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">E-Mails heute</p>
                        <p class="text-xs text-slate-500">Versendet</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-slate-600 dark:text-slate-300">
                    <?= $stats['emails_today'] ?>
                </span>
            </div>
            
            <!-- System Status -->
            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-server text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">System Status</p>
                        <p class="text-xs text-slate-500">Alle Dienste</p>
                    </div>
                </div>
                <span class="flex items-center gap-2 text-green-500 font-medium">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Online
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- Top Kunden -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                    <i class="fas fa-trophy text-amber-500 mr-2"></i>
                    Top Kunden
                </h3>
                <a href="/admin/customers.php" class="text-sm text-primary-600 hover:text-primary-700">
                    Alle anzeigen →
                </a>
            </div>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php foreach ($topCustomers as $customer): ?>
            <a href="/admin/customer-detail.php?id=<?= $customer['id'] ?>" class="flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all">
                <div class="flex items-center gap-3">
                    <?php if (!empty($customer['logo_url'])): ?>
                    <img src="<?= e($customer['logo_url']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover">
                    <?php else: ?>
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <span class="text-primary-600 dark:text-primary-400 font-medium">
                            <?= strtoupper(substr($customer['company_name'], 0, 2)) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white"><?= e($customer['company_name']) ?></p>
                        <p class="text-xs text-slate-500"><?= e($customer['subdomain']) ?>.empfohlen.de</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-800 dark:text-white"><?= number_format($customer['lead_count'], 0, ',', '.') ?></p>
                    <p class="text-xs text-slate-500">Empfehler</p>
                </div>
            </a>
            <?php endforeach; ?>
            
            <?php if (empty($topCustomers)): ?>
            <div class="p-8 text-center text-slate-500">
                <i class="fas fa-inbox text-3xl mb-2"></i>
                <p>Noch keine Kunden</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Letzte Aktivitäten -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-clock text-primary-500 mr-2"></i>
                Letzte Aktivitäten
            </h3>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700 max-h-96 overflow-y-auto">
            <?php foreach ($recentActivities as $activity): ?>
            <div class="flex items-center gap-3 p-4">
                <?php if ($activity['type'] === 'customer'): ?>
                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-building text-green-600 dark:text-green-400 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-800 dark:text-white truncate">
                        Neuer Kunde: <strong><?= e($activity['title']) ?></strong>
                    </p>
                    <p class="text-xs text-slate-500"><?= timeAgo($activity['created_at']) ?></p>
                </div>
                <?php else: ?>
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-blue-600 dark:text-blue-400 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-800 dark:text-white truncate">
                        Neuer Empfehler: <strong><?= e($activity['title']) ?></strong>
                    </p>
                    <p class="text-xs text-slate-500"><?= timeAgo($activity['created_at']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($recentActivities)): ?>
            <div class="p-8 text-center text-slate-500">
                <i class="fas fa-history text-3xl mb-2"></i>
                <p>Noch keine Aktivitäten</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// Kunden Chart
const ctx = document.getElementById('customersChart').getContext('2d');
const isDark = document.documentElement.classList.contains('dark');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Neue Kunden',
            data: <?= json_encode($chartValues) ?>,
            borderColor: '#0ea5e9',
            backgroundColor: 'rgba(14, 165, 233, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 2,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: isDark ? '#94a3b8' : '#64748b',
                    maxTicksLimit: 7
                }
            },
            y: {
                beginAtZero: true,
                grid: {
                    color: isDark ? '#334155' : '#e2e8f0'
                },
                ticks: {
                    color: isDark ? '#94a3b8' : '#64748b',
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
