<?php
/**
 * Admin Dashboard - Übersicht (ERWEITERT)
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

// Auth Check
if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'Dashboard';

// === STATISTIKEN ===

// Basis-Statistiken
$stats = [
    'total_customers' => $db->fetchColumn("SELECT COUNT(*) FROM customers") ?? 0,
    'active_customers' => $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE subscription_status = 'active'") ?? 0,
    'trial_customers' => $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE subscription_status = 'trial'") ?? 0,
    'total_leads' => $db->fetchColumn("SELECT COUNT(*) FROM leads") ?? 0,
    'total_conversions' => $db->fetchColumn("SELECT COUNT(*) FROM conversions WHERE status = 'confirmed'") ?? 0,
    'pending_fraud' => $db->fetchColumn("SELECT COUNT(*) FROM fraud_log WHERE action_taken = 'review' AND reviewed_at IS NULL") ?? 0,
    'emails_today' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE DATE(created_at) = CURDATE()") ?? 0,
];

// MRR (Monthly Recurring Revenue)
$stats['mrr'] = $db->fetchColumn("
    SELECT COALESCE(SUM(CASE 
        WHEN plan = 'starter' THEN 49
        WHEN plan = 'professional' THEN 99
        WHEN plan = 'enterprise' THEN 199
        ELSE 0
    END), 0) FROM customers WHERE subscription_status = 'active'
") ?? 0;

// ARR (Annual Recurring Revenue)
$stats['arr'] = $stats['mrr'] * 12;

// Umsatz berechnen
$stats['revenue_total'] = $db->fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'") ?? 0;
$stats['revenue_month'] = $db->fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())") ?? 0;

// Neue Kunden diese Woche / diesen Monat
$stats['new_customers_week'] = $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)") ?? 0;
$stats['new_customers_month'] = $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)") ?? 0;

// Heute aktive Kunden (Login)
$stats['active_today'] = $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE DATE(last_login_at) = CURDATE()") ?? 0;

// Churn (gekündigt diesen Monat)
$stats['churned_month'] = $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE subscription_status = 'cancelled' AND MONTH(updated_at) = MONTH(NOW()) AND YEAR(updated_at) = YEAR(NOW())") ?? 0;

// Trial Conversion Rate (letzten 30 Tage)
$trialToActive = $db->fetch("
    SELECT 
        (SELECT COUNT(*) FROM customers WHERE subscription_status = 'active' AND created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)) as converted,
        (SELECT COUNT(*) FROM customers WHERE created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY)) as total
");
$stats['trial_conversion_rate'] = $trialToActive['total'] > 0 ? round(($trialToActive['converted'] / $trialToActive['total']) * 100, 1) : 0;

// Durchschnittlicher Umsatz pro Kunde
$stats['arpu'] = $stats['active_customers'] > 0 ? round($stats['mrr'] / $stats['active_customers'], 2) : 0;

// Top Plan Distribution
$planDistribution = $db->fetchAll("
    SELECT plan, COUNT(*) as count 
    FROM customers 
    WHERE subscription_status = 'active'
    GROUP BY plan
");

// === CHARTS ===

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

// MRR Chart (letzte 6 Monate)
$mrrHistory = $db->fetchAll("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as customer_count
    FROM customers 
    WHERE subscription_status = 'active'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month
");

// Letzte Aktivitäten
$recentActivities = $db->fetchAll("
    (SELECT 'customer' as type, c.company_name as title, c.created_at, c.id, c.plan
     FROM customers c ORDER BY created_at DESC LIMIT 5)
    UNION ALL
    (SELECT 'lead' as type, l.email as title, l.created_at, l.customer_id as id, NULL as plan
     FROM leads l ORDER BY created_at DESC LIMIT 5)
    ORDER BY created_at DESC
    LIMIT 10
");

// Top Kunden
$topCustomers = $db->fetchAll("
    SELECT c.*, 
           (SELECT COUNT(*) FROM leads WHERE customer_id = c.id) as lead_count,
           (SELECT COUNT(*) FROM conversions cv 
            JOIN campaigns camp ON cv.campaign_id = camp.id 
            WHERE camp.customer_id = c.id AND cv.status = 'confirmed') as conversion_count
    FROM customers c
    ORDER BY lead_count DESC
    LIMIT 5
");

// Auslaufende Trials (nächste 7 Tage)
$expiringTrials = $db->fetchAll("
    SELECT * FROM customers 
    WHERE subscription_status = 'trial' 
    AND subscription_ends_at BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
    ORDER BY subscription_ends_at ASC
    LIMIT 5
");

// Cron-Job Status (basierend auf letzten Logs)
$lastCronRun = $db->fetchColumn("SELECT MAX(created_at) FROM email_queue WHERE status = 'sent'");
$cronHealthy = $lastCronRun && strtotime($lastCronRun) > strtotime('-1 hour');

// === VOLLSTÄNDIGE KUNDENLISTE ===
$allCustomers = $db->fetchAll("
    SELECT c.*,
           (SELECT COUNT(*) FROM leads WHERE customer_id = c.id) as lead_count,
           (SELECT COUNT(*) FROM campaigns WHERE customer_id = c.id) as campaign_count,
           (SELECT COUNT(*) FROM conversions cv 
            JOIN campaigns camp ON cv.campaign_id = camp.id 
            WHERE camp.customer_id = c.id AND cv.status = 'confirmed') as conversion_count,
           CASE 
               WHEN c.subscription_status = 'trial' AND c.subscription_ends_at IS NOT NULL 
               THEN DATEDIFF(c.subscription_ends_at, NOW())
               ELSE NULL 
           END as trial_days_left
    FROM customers c
    ORDER BY c.created_at DESC
    LIMIT 50
");

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- KPI Cards Row 1 -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <!-- MRR -->
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-chart-line text-white/80"></i>
            <span class="text-xs bg-white/20 px-2 py-0.5 rounded">MRR</span>
        </div>
        <h3 class="text-2xl font-bold"><?= number_format($stats['mrr'], 0, ',', '.') ?> €</h3>
        <p class="text-sm text-white/80">ARR: <?= number_format($stats['arr'], 0, ',', '.') ?> €</p>
    </div>
    
    <!-- Aktive Kunden -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-primary-600 dark:text-primary-400"></i>
            </div>
            <span class="text-xs text-green-500 font-medium">+<?= $stats['new_customers_week'] ?> /Woche</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['active_customers'] ?></h3>
        <p class="text-sm text-slate-500">Aktive Kunden</p>
    </div>
    
    <!-- Trial Kunden -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-hourglass-half text-blue-600 dark:text-blue-400"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['trial_customers'] ?></h3>
        <p class="text-sm text-slate-500">Im Trial</p>
    </div>
    
    <!-- Leads -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_leads'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Empfehler</p>
    </div>
    
    <!-- Conversions -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-handshake text-amber-600 dark:text-amber-400"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_conversions'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Conversions</p>
    </div>
    
    <!-- Heute aktiv -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-signal text-green-600 dark:text-green-400"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['active_today'] ?></h3>
        <p class="text-sm text-slate-500">Heute aktiv</p>
    </div>
</div>

<!-- KPI Cards Row 2 -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <!-- Trial Conversion -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-percentage text-primary-500"></i>
            <span class="text-sm text-slate-500">Trial → Paid</span>
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?= $stats['trial_conversion_rate'] ?>%</h3>
    </div>
    
    <!-- ARPU -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-user-dollar text-green-500"></i>
            <span class="text-sm text-slate-500">ARPU</span>
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['arpu'], 0, ',', '.') ?> €</h3>
    </div>
    
    <!-- Churn -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-user-minus text-red-500"></i>
            <span class="text-sm text-slate-500">Churn (Monat)</span>
        </div>
        <h3 class="text-xl font-bold <?= $stats['churned_month'] > 0 ? 'text-red-600' : 'text-slate-800 dark:text-white' ?>"><?= $stats['churned_month'] ?></h3>
    </div>
    
    <!-- Umsatz Monat -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-2 mb-1">
            <i class="fas fa-receipt text-amber-500"></i>
            <span class="text-sm text-slate-500">Umsatz (Monat)</span>
        </div>
        <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['revenue_month'], 0, ',', '.') ?> €</h3>
    </div>
</div>

<!-- Charts & Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Chart -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-chart-line text-primary-500 mr-2"></i>Neue Kunden (letzte 30 Tage)
        </h3>
        <div class="h-64"><canvas id="customersChart"></canvas></div>
    </div>
    
    <!-- Quick Stats & Health -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-bolt text-amber-500 mr-2"></i>Schnellübersicht
        </h3>
        <div class="space-y-3">
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
                <span class="text-2xl font-bold <?= $stats['pending_fraud'] > 0 ? 'text-red-500' : 'text-slate-400' ?>"><?= $stats['pending_fraud'] ?></span>
            </a>
            
            <!-- E-Mails heute -->
            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-envelope text-blue-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">E-Mails heute</p>
                        <p class="text-xs text-slate-500">In Queue</p>
                    </div>
                </div>
                <span class="text-2xl font-bold text-slate-600 dark:text-slate-300"><?= $stats['emails_today'] ?></span>
            </div>
            
            <!-- System Status -->
            <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50 dark:bg-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                        <i class="fas fa-server text-green-500"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white">System Status</p>
                        <p class="text-xs text-slate-500">Cron-Jobs</p>
                    </div>
                </div>
                <?php if ($cronHealthy): ?>
                <span class="flex items-center gap-2 text-green-500 font-medium">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>OK
                </span>
                <?php else: ?>
                <span class="flex items-center gap-2 text-amber-500 font-medium">
                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>Prüfen
                </span>
                <?php endif; ?>
            </div>
            
            <!-- Plan Distribution -->
            <div class="pt-3 border-t border-slate-200 dark:border-slate-700">
                <p class="text-xs text-slate-500 mb-2">Plan-Verteilung (aktiv)</p>
                <div class="flex gap-2">
                    <?php foreach ($planDistribution as $pd): ?>
                    <?php
                    $planColors = [
                        'starter' => 'bg-slate-200 text-slate-700',
                        'professional' => 'bg-primary-100 text-primary-700',
                        'enterprise' => 'bg-purple-100 text-purple-700'
                    ];
                    ?>
                    <span class="px-2 py-1 text-xs font-medium rounded <?= $planColors[$pd['plan']] ?? 'bg-slate-200' ?>">
                        <?= ucfirst($pd['plan']) ?>: <?= $pd['count'] ?>
                    </span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Top Kunden -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-slate-800 dark:text-white">
                    <i class="fas fa-trophy text-amber-500 mr-2"></i>Top Kunden
                </h3>
                <a href="/admin/customers.php" class="text-sm text-primary-600 hover:text-primary-700">Alle →</a>
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
                        <span class="text-primary-600 dark:text-primary-400 font-medium"><?= strtoupper(substr($customer['company_name'], 0, 2)) ?></span>
                    </div>
                    <?php endif; ?>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white"><?= e($customer['company_name']) ?></p>
                        <p class="text-xs text-slate-500"><?= e($customer['subdomain']) ?>.empfehlungen.cloud</p>
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
    
    <!-- Auslaufende Trials -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-clock text-amber-500 mr-2"></i>Auslaufende Trials
            </h3>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700">
            <?php foreach ($expiringTrials as $trial): ?>
            <?php 
            $daysLeft = ceil((strtotime($trial['subscription_ends_at']) - time()) / 86400);
            ?>
            <a href="/admin/customer-detail.php?id=<?= $trial['id'] ?>" class="flex items-center justify-between p-4 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all">
                <div>
                    <p class="text-sm font-medium text-slate-800 dark:text-white"><?= e($trial['company_name']) ?></p>
                    <p class="text-xs text-slate-500"><?= e($trial['email']) ?></p>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full <?= $daysLeft <= 2 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' ?>">
                    <?= $daysLeft ?> Tag<?= $daysLeft !== 1 ? 'e' : '' ?>
                </span>
            </a>
            <?php endforeach; ?>
            <?php if (empty($expiringTrials)): ?>
            <div class="p-8 text-center text-slate-500">
                <i class="fas fa-check-circle text-3xl text-green-500 mb-2"></i>
                <p>Keine auslaufenden Trials</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Letzte Aktivitäten -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-history text-primary-500 mr-2"></i>Letzte Aktivitäten
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
                        <?php if ($activity['plan']): ?>
                        <span class="text-xs text-slate-400">(<?= ucfirst($activity['plan']) ?>)</span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-slate-500"><?= timeAgo($activity['created_at']) ?></p>
                </div>
                <?php else: ?>
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-plus text-blue-600 dark:text-blue-400 text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-slate-800 dark:text-white truncate">Neuer Empfehler: <strong><?= e($activity['title']) ?></strong></p>
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

<!-- VOLLSTÄNDIGE KUNDENLISTE -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-address-book text-primary-500 mr-2"></i>Alle Kunden (Vollständige Daten)
            </h3>
            <a href="/admin/customers.php" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded-lg transition-all">
                <i class="fas fa-external-link mr-2"></i>Zur Kundenverwaltung
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Firma</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Kontakt</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">E-Mail / Telefon</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Adresse</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Subdomain</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">Plan</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">Status</th>
                    <th class="px-4 py-3 text-center font-semibold text-slate-600 dark:text-slate-300">Leads</th>
                    <th class="px-4 py-3 text-left font-semibold text-slate-600 dark:text-slate-300">Registriert</th>
                    <th class="px-4 py-3 text-right font-semibold text-slate-600 dark:text-slate-300">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($allCustomers as $customer): ?>
                <?php
                $isOnline = $customer['last_login_at'] && strtotime($customer['last_login_at']) > strtotime('-15 minutes');
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <?php if (!empty($customer['logo_url'])): ?>
                                <img src="<?= e($customer['logo_url']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover">
                                <?php else: ?>
                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                    <span class="text-primary-600 dark:text-primary-400 font-medium text-xs">
                                        <?= strtoupper(substr($customer['company_name'], 0, 2)) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                                <?php if ($isOnline): ?>
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-white dark:border-slate-800" title="Online"></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 dark:text-white"><?= e($customer['company_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= e(ucfirst($customer['industry'] ?? '-')) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-slate-800 dark:text-white"><?= e($customer['contact_name']) ?></p>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-slate-800 dark:text-white"><?= e($customer['email']) ?></p>
                        <?php if (!empty($customer['phone'])): ?>
                        <p class="text-xs text-slate-500 mt-1">
                            <i class="fas fa-phone text-[10px] mr-1"></i><?= e($customer['phone']) ?>
                        </p>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php if (!empty($customer['address_street'])): ?>
                        <p class="text-slate-600 dark:text-slate-300 text-xs"><?= e($customer['address_street']) ?></p>
                        <p class="text-slate-600 dark:text-slate-300 text-xs"><?= e($customer['address_zip']) ?> <?= e($customer['address_city']) ?></p>
                        <?php else: ?>
                        <span class="text-slate-400 text-xs">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <a href="https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud" target="_blank" 
                           class="text-primary-600 hover:text-primary-700 hover:underline">
                            <?= e($customer['subdomain']) ?>
                            <i class="fas fa-external-link text-[10px] ml-1"></i>
                        </a>
                        <?php if (!empty($customer['custom_domain'])): ?>
                        <p class="text-xs text-slate-400 mt-1"><?= e($customer['custom_domain']) ?></p>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php
                        $planColors = [
                            'starter' => 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200',
                            'professional' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
                            'enterprise' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                        ];
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $planColors[$customer['plan']] ?? $planColors['starter'] ?>">
                            <?= ucfirst($customer['plan']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                            'trial' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                            'paused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                        ];
                        $statusLabels = ['active' => 'Aktiv', 'trial' => 'Trial', 'cancelled' => 'Gekündigt', 'paused' => 'Pausiert'];
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $statusColors[$customer['subscription_status']] ?? $statusColors['trial'] ?>">
                            <?= $statusLabels[$customer['subscription_status']] ?? $customer['subscription_status'] ?>
                        </span>
                        <?php if ($customer['subscription_status'] === 'trial' && $customer['trial_days_left'] !== null): ?>
                        <p class="text-xs mt-1 <?= $customer['trial_days_left'] <= 3 ? 'text-red-500' : 'text-slate-400' ?>">
                            <?= $customer['trial_days_left'] ?> Tage
                        </p>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-semibold text-slate-800 dark:text-white"><?= number_format($customer['lead_count'], 0, ',', '.') ?></span>
                        <span class="text-slate-400">/</span>
                        <span class="text-slate-500"><?= number_format($customer['conversion_count'], 0, ',', '.') ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <p class="text-xs text-slate-600 dark:text-slate-300"><?= date('d.m.Y', strtotime($customer['created_at'])) ?></p>
                        <p class="text-xs text-slate-400"><?= timeAgo($customer['created_at']) ?></p>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="/admin/customer-detail.php?id=<?= $customer['id'] ?>" 
                               class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all" 
                               title="Details">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/login-as-customer.php?id=<?= $customer['id'] ?>" 
                               class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all" 
                               title="Als Kunde einloggen">
                                <i class="fas fa-user-secret"></i>
                            </a>
                            <a href="https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud" target="_blank"
                               class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-all" 
                               title="Seite öffnen">
                                <i class="fas fa-external-link"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($allCustomers)): ?>
                <tr>
                    <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>Noch keine Kunden vorhanden</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (count($allCustomers) >= 50): ?>
    <div class="p-4 border-t border-slate-200 dark:border-slate-700 text-center">
        <a href="/admin/customers.php" class="text-primary-600 hover:text-primary-700 font-medium">
            <i class="fas fa-arrow-right mr-2"></i>Alle <?= $stats['total_customers'] ?> Kunden anzeigen
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<footer class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 mt-8">
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Brand -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-rocket text-white"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white">Leadbusiness</h4>
                        <p class="text-xs text-slate-500">Admin Panel</p>
                    </div>
                </div>
                <p class="text-sm text-slate-500">
                    Empfehlungsprogramm-Plattform für professionelle Kundenakquise.
                </p>
            </div>
            
            <!-- Schnellzugriff -->
            <div>
                <h5 class="font-semibold text-slate-800 dark:text-white mb-3">Schnellzugriff</h5>
                <ul class="space-y-2 text-sm">
                    <li><a href="/admin/customers.php" class="text-slate-500 hover:text-primary-600 transition-colors"><i class="fas fa-building w-5"></i>Kunden</a></li>
                    <li><a href="/admin/leads.php" class="text-slate-500 hover:text-primary-600 transition-colors"><i class="fas fa-users w-5"></i>Leads</a></li>
                    <li><a href="/admin/broadcasts.php" class="text-slate-500 hover:text-primary-600 transition-colors"><i class="fas fa-bullhorn w-5"></i>Broadcasts</a></li>
                    <li><a href="/admin/payments.php" class="text-slate-500 hover:text-primary-600 transition-colors"><i class="fas fa-credit-card w-5"></i>Zahlungen</a></li>
                </ul>
            </div>
            
            <!-- System -->
            <div>
                <h5 class="font-semibold text-slate-800 dark:text-white mb-3">System</h5>
                <ul class="space-y-2 text-sm">
                    <li><a href="/admin/settings.php" class="text-slate-500 hover:text-primary-600 transition-colors"><i class="fas fa-cog w-5"></i>Einstellungen</a></li>
                    <li><a href="/admin/logs.php" class="text-slate-500 hover:text-primary-600 transition-colors"><i class="fas fa-file-alt w-5"></i>Logs</a></li>
                    <li><a href="/admin/email-queue.php" class="text-slate-500 hover:text-primary-600 transition-colors"><i class="fas fa-envelope w-5"></i>E-Mail Queue</a></li>
                    <li><a href="/admin/fraud-review.php" class="text-slate-500 hover:text-primary-600 transition-colors"><i class="fas fa-shield w-5"></i>Fraud Review</a></li>
                </ul>
            </div>
            
            <!-- Status -->
            <div>
                <h5 class="font-semibold text-slate-800 dark:text-white mb-3">Status</h5>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Cron-Jobs</span>
                        <?php if ($cronHealthy): ?>
                        <span class="flex items-center gap-1 text-green-500">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>OK
                        </span>
                        <?php else: ?>
                        <span class="flex items-center gap-1 text-amber-500">
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>Prüfen
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">Kunden aktiv</span>
                        <span class="text-slate-800 dark:text-white font-medium"><?= $stats['active_customers'] ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">MRR</span>
                        <span class="text-green-600 font-medium"><?= number_format($stats['mrr'], 0, ',', '.') ?> €</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-500">PHP Version</span>
                        <span class="text-slate-800 dark:text-white"><?= phpversion() ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Trennlinie und Copyright -->
        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <p class="text-sm text-slate-500">
                    &copy; <?= date('Y') ?> Leadbusiness. Alle Rechte vorbehalten.
                </p>
                <div class="flex items-center gap-4 text-sm text-slate-500">
                    <span><i class="fas fa-clock mr-1"></i><?= date('d.m.Y H:i') ?></span>
                    <span><i class="fas fa-database mr-1"></i>MySQL</span>
                    <span><i class="fab fa-php mr-1"></i>PHP <?= PHP_MAJOR_VERSION ?>.<?= PHP_MINOR_VERSION ?></span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
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
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: isDark ? '#94a3b8' : '#64748b', maxTicksLimit: 7 } },
            y: { beginAtZero: true, grid: { color: isDark ? '#334155' : '#e2e8f0' }, ticks: { color: isDark ? '#94a3b8' : '#64748b', stepSize: 1 } }
        }
    }
});
</script>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
