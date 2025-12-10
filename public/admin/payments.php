<?php
/**
 * Admin Zahlungs-Übersicht
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'Zahlungen & Umsatz';

// Filter
$filter = sanitize($_GET['filter'] ?? 'all');
$status = sanitize($_GET['status'] ?? 'all');
$type = sanitize($_GET['type'] ?? 'all');
$search = sanitize($_GET['search'] ?? '');
$dateFrom = sanitize($_GET['date_from'] ?? '');
$dateTo = sanitize($_GET['date_to'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 25;

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="zahlungen-export-' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['ID', 'Digistore Order ID', 'Kunde', 'E-Mail', 'Typ', 'Betrag', 'Status', 'Datum'], ';');
    
    $allPayments = $db->fetchAll("
        SELECT p.*, c.company_name, c.email as customer_email 
        FROM payments p 
        LEFT JOIN customers c ON p.customer_id = c.id 
        ORDER BY p.created_at DESC
    ");
    
    foreach ($allPayments as $p) {
        fputcsv($output, [
            $p['id'],
            $p['digistore_order_id'],
            $p['company_name'] ?? $p['buyer_name'],
            $p['customer_email'] ?? $p['buyer_email'],
            $p['payment_type'],
            number_format($p['amount'], 2, ',', '.') . ' €',
            $p['status'],
            date('d.m.Y H:i', strtotime($p['created_at']))
        ], ';');
    }
    fclose($output);
    exit;
}

// Query bauen
$where = [];
$params = [];

if ($status !== 'all') {
    $where[] = "p.status = ?";
    $params[] = $status;
}

if ($type !== 'all') {
    $where[] = "p.payment_type = ?";
    $params[] = $type;
}

if (!empty($search)) {
    $where[] = "(c.company_name LIKE ? OR c.email LIKE ? OR p.buyer_email LIKE ? OR p.digistore_order_id LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if (!empty($dateFrom)) {
    $where[] = "DATE(p.created_at) >= ?";
    $params[] = $dateFrom;
}

if (!empty($dateTo)) {
    $where[] = "DATE(p.created_at) <= ?";
    $params[] = $dateTo;
}

$whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

// Statistiken
$stats = [];

// Gesamt-Umsatz
$stats['total_revenue'] = $db->fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'") ?? 0;

// Umsatz diesen Monat
$stats['revenue_month'] = $db->fetchColumn("
    SELECT COALESCE(SUM(amount), 0) FROM payments 
    WHERE status = 'completed' 
    AND MONTH(created_at) = MONTH(NOW()) 
    AND YEAR(created_at) = YEAR(NOW())
") ?? 0;

// Umsatz letzten Monat (für Vergleich)
$stats['revenue_last_month'] = $db->fetchColumn("
    SELECT COALESCE(SUM(amount), 0) FROM payments 
    WHERE status = 'completed' 
    AND MONTH(created_at) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
    AND YEAR(created_at) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH))
") ?? 0;

// Umsatz-Änderung in %
$stats['revenue_change'] = $stats['revenue_last_month'] > 0 
    ? round((($stats['revenue_month'] - $stats['revenue_last_month']) / $stats['revenue_last_month']) * 100, 1)
    : 0;

// Umsatz diese Woche
$stats['revenue_week'] = $db->fetchColumn("
    SELECT COALESCE(SUM(amount), 0) FROM payments 
    WHERE status = 'completed' 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
") ?? 0;

// Umsatz heute
$stats['revenue_today'] = $db->fetchColumn("
    SELECT COALESCE(SUM(amount), 0) FROM payments 
    WHERE status = 'completed' 
    AND DATE(created_at) = CURDATE()
") ?? 0;

// Anzahl Zahlungen
$stats['total_payments'] = $db->fetchColumn("SELECT COUNT(*) FROM payments WHERE status = 'completed'") ?? 0;
$stats['pending_payments'] = $db->fetchColumn("SELECT COUNT(*) FROM payments WHERE status = 'pending'") ?? 0;
$stats['refunded'] = $db->fetchColumn("SELECT COUNT(*) FROM payments WHERE status = 'refunded'") ?? 0;
$stats['chargebacks'] = $db->fetchColumn("SELECT COUNT(*) FROM payments WHERE status = 'chargeback'") ?? 0;

// Durchschnittlicher Zahlungswert
$stats['avg_payment'] = $db->fetchColumn("SELECT COALESCE(AVG(amount), 0) FROM payments WHERE status = 'completed'") ?? 0;

// Setup vs Subscription Revenue
$stats['setup_revenue'] = $db->fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND payment_type = 'setup'") ?? 0;
$stats['subscription_revenue'] = $db->fetchColumn("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND payment_type = 'subscription'") ?? 0;

// MRR (aus aktiven Subscriptions)
$stats['mrr'] = $db->fetchColumn("
    SELECT COALESCE(SUM(CASE 
        WHEN plan = 'starter' THEN 49
        WHEN plan = 'professional' THEN 99
        WHEN plan = 'enterprise' THEN 199
        ELSE 0
    END), 0) FROM customers WHERE subscription_status = 'active'
") ?? 0;

// Chart-Daten: Umsatz letzte 30 Tage
$revenueChart = $db->fetchAll("
    SELECT DATE(created_at) as date, SUM(amount) as revenue, COUNT(*) as count
    FROM payments 
    WHERE status = 'completed' 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date
");

$chartLabels = [];
$chartRevenue = [];
$chartCount = [];
for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('d.m.', strtotime($date));
    $found = false;
    foreach ($revenueChart as $row) {
        if ($row['date'] === $date) {
            $chartRevenue[] = (float)$row['revenue'];
            $chartCount[] = (int)$row['count'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $chartRevenue[] = 0;
        $chartCount[] = 0;
    }
}

// Umsatz nach Typ (Pie Chart)
$revenueByType = $db->fetchAll("
    SELECT payment_type, SUM(amount) as total, COUNT(*) as count
    FROM payments 
    WHERE status = 'completed'
    GROUP BY payment_type
");

// Umsatz nach Monat (letzte 12 Monate)
$revenueByMonth = $db->fetchAll("
    SELECT DATE_FORMAT(created_at, '%Y-%m') as month, 
           SUM(amount) as revenue,
           COUNT(*) as count
    FROM payments 
    WHERE status = 'completed' 
    AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month
");

// Zahlungen laden
$totalCount = $db->fetchColumn("
    SELECT COUNT(*) FROM payments p 
    LEFT JOIN customers c ON p.customer_id = c.id 
    $whereClause
", $params);

$totalPages = ceil($totalCount / $perPage);
$offset = ($page - 1) * $perPage;

$payments = $db->fetchAll("
    SELECT p.*, 
           c.company_name, c.email as customer_email, c.subdomain, c.plan as customer_plan
    FROM payments p 
    LEFT JOIN customers c ON p.customer_id = c.id 
    $whereClause
    ORDER BY p.created_at DESC
    LIMIT $perPage OFFSET $offset
", $params);

// Top Kunden nach Umsatz
$topCustomers = $db->fetchAll("
    SELECT c.id, c.company_name, c.email, c.subdomain, c.plan,
           SUM(p.amount) as total_revenue,
           COUNT(p.id) as payment_count
    FROM payments p
    JOIN customers c ON p.customer_id = c.id
    WHERE p.status = 'completed'
    GROUP BY c.id
    ORDER BY total_revenue DESC
    LIMIT 10
");

// Status Labels & Colors
$statusConfig = [
    'completed' => ['label' => 'Abgeschlossen', 'color' => 'green', 'icon' => 'fa-check-circle'],
    'pending' => ['label' => 'Ausstehend', 'color' => 'amber', 'icon' => 'fa-clock'],
    'refunded' => ['label' => 'Erstattet', 'color' => 'blue', 'icon' => 'fa-rotate-left'],
    'chargeback' => ['label' => 'Chargeback', 'color' => 'red', 'icon' => 'fa-exclamation-triangle'],
    'cancelled' => ['label' => 'Storniert', 'color' => 'slate', 'icon' => 'fa-times-circle']
];

$typeConfig = [
    'setup' => ['label' => 'Einrichtung', 'color' => 'purple'],
    'subscription' => ['label' => 'Abo', 'color' => 'primary'],
    'upgrade' => ['label' => 'Upgrade', 'color' => 'amber'],
    'addon' => ['label' => 'Add-on', 'color' => 'teal']
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
            <i class="fas fa-euro-sign text-green-500 mr-2"></i>Zahlungen & Umsatz
        </h1>
        <p class="text-slate-500">Alle Zahlungen, Umsatzstatistiken und Finanzübersicht</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="?export=csv" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all">
            <i class="fas fa-download mr-2"></i>CSV Export
        </a>
    </div>
</div>

<!-- KPI Cards Row 1 -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <!-- Gesamt-Umsatz -->
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-coins text-white/80"></i>
            <span class="text-xs bg-white/20 px-2 py-0.5 rounded">Gesamt</span>
        </div>
        <h3 class="text-2xl font-bold"><?= number_format($stats['total_revenue'], 0, ',', '.') ?> €</h3>
        <p class="text-sm text-white/80"><?= number_format($stats['total_payments'], 0, ',', '.') ?> Zahlungen</p>
    </div>
    
    <!-- MRR -->
    <div class="bg-gradient-to-br from-primary-500 to-primary-600 rounded-xl p-4 text-white">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-chart-line text-white/80"></i>
            <span class="text-xs bg-white/20 px-2 py-0.5 rounded">MRR</span>
        </div>
        <h3 class="text-2xl font-bold"><?= number_format($stats['mrr'], 0, ',', '.') ?> €</h3>
        <p class="text-sm text-white/80">Monatlich wiederkehrend</p>
    </div>
    
    <!-- Dieser Monat -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-alt text-green-600"></i>
            </div>
            <?php if ($stats['revenue_change'] != 0): ?>
            <span class="text-xs font-medium <?= $stats['revenue_change'] > 0 ? 'text-green-500' : 'text-red-500' ?>">
                <?= $stats['revenue_change'] > 0 ? '+' : '' ?><?= $stats['revenue_change'] ?>%
            </span>
            <?php endif; ?>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['revenue_month'], 0, ',', '.') ?> €</h3>
        <p class="text-sm text-slate-500">Dieser Monat</p>
    </div>
    
    <!-- Diese Woche -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-week text-blue-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['revenue_week'], 0, ',', '.') ?> €</h3>
        <p class="text-sm text-slate-500">Diese Woche</p>
    </div>
    
    <!-- Heute -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-day text-purple-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['revenue_today'], 0, ',', '.') ?> €</h3>
        <p class="text-sm text-slate-500">Heute</p>
    </div>
    
    <!-- Durchschnitt -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-calculator text-amber-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['avg_payment'], 0, ',', '.') ?> €</h3>
        <p class="text-sm text-slate-500">Ø Zahlung</p>
    </div>
</div>

<!-- KPI Cards Row 2 -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-rocket text-purple-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['setup_revenue'], 0, ',', '.') ?> €</h3>
                <p class="text-sm text-slate-500">Einrichtungen</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-sync text-primary-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['subscription_revenue'], 0, ',', '.') ?> €</h3>
                <p class="text-sm text-slate-500">Abonnements</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-amber-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-amber-600"><?= $stats['pending_payments'] ?></h3>
                <p class="text-sm text-slate-500">Ausstehend</p>
            </div>
        </div>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-red-600"><?= $stats['chargebacks'] + $stats['refunded'] ?></h3>
                <p class="text-sm text-slate-500">Erstattungen/CB</p>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Umsatz Chart -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-chart-area text-green-500 mr-2"></i>Umsatz (letzte 30 Tage)
        </h3>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
    
    <!-- Top Kunden -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-trophy text-amber-500 mr-2"></i>Top Kunden (Umsatz)
            </h3>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700 max-h-72 overflow-y-auto">
            <?php foreach ($topCustomers as $index => $cust): ?>
            <a href="/admin/customer-detail.php?id=<?= $cust['id'] ?>" class="flex items-center justify-between p-3 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-all">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 flex items-center justify-center bg-slate-100 dark:bg-slate-700 rounded-full text-xs font-medium">
                        <?= $index + 1 ?>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white truncate max-w-[150px]"><?= e($cust['company_name']) ?></p>
                        <p class="text-xs text-slate-500"><?= $cust['payment_count'] ?> Zahlungen</p>
                    </div>
                </div>
                <span class="font-semibold text-green-600"><?= number_format($cust['total_revenue'], 0, ',', '.') ?> €</span>
            </a>
            <?php endforeach; ?>
            <?php if (empty($topCustomers)): ?>
            <div class="p-6 text-center text-slate-500">
                <i class="fas fa-inbox text-2xl mb-2"></i>
                <p class="text-sm">Noch keine Zahlungen</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Filter -->
<div class="bg-white dark:bg-slate-800 rounded-xl p-4 mb-6 shadow-sm border border-slate-200 dark:border-slate-700">
    <form method="GET" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Suche</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="<?= e($search) ?>" 
                       placeholder="Firma, E-Mail, Order ID..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                              bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                              focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Status</label>
            <select name="status" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                   bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Alle Status</option>
                <option value="completed" <?= $status === 'completed' ? 'selected' : '' ?>>✅ Abgeschlossen</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>⏳ Ausstehend</option>
                <option value="refunded" <?= $status === 'refunded' ? 'selected' : '' ?>>↩️ Erstattet</option>
                <option value="chargeback" <?= $status === 'chargeback' ? 'selected' : '' ?>>⚠️ Chargeback</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Typ</label>
            <select name="type" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                   bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>Alle Typen</option>
                <option value="setup" <?= $type === 'setup' ? 'selected' : '' ?>>🚀 Einrichtung</option>
                <option value="subscription" <?= $type === 'subscription' ? 'selected' : '' ?>>🔄 Abo</option>
                <option value="upgrade" <?= $type === 'upgrade' ? 'selected' : '' ?>>⬆️ Upgrade</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Von</label>
            <input type="date" name="date_from" value="<?= e($dateFrom) ?>" 
                   class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                          bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Bis</label>
            <input type="date" name="date_to" value="<?= e($dateTo) ?>" 
                   class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                          bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
        </div>
        
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
            <i class="fas fa-filter mr-2"></i>Filtern
        </button>
        
        <?php if (!empty($search) || $status !== 'all' || $type !== 'all' || !empty($dateFrom) || !empty($dateTo)): ?>
        <a href="/admin/payments.php" class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white">
            <i class="fas fa-times mr-1"></i>Reset
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Payments Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Datum</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Kunde</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Order ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Typ</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Betrag</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($payments as $payment): ?>
                <?php 
                $sc = $statusConfig[$payment['status']] ?? ['label' => $payment['status'], 'color' => 'slate', 'icon' => 'fa-question'];
                $tc = $typeConfig[$payment['payment_type']] ?? ['label' => $payment['payment_type'], 'color' => 'slate'];
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-4 py-3">
                        <div class="text-sm text-slate-800 dark:text-white"><?= date('d.m.Y', strtotime($payment['created_at'])) ?></div>
                        <div class="text-xs text-slate-500"><?= date('H:i', strtotime($payment['created_at'])) ?> Uhr</div>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($payment['customer_id']): ?>
                        <a href="/admin/customer-detail.php?id=<?= $payment['customer_id'] ?>" class="hover:text-primary-600">
                            <div class="text-sm font-medium text-slate-800 dark:text-white"><?= e($payment['company_name'] ?? $payment['buyer_name']) ?></div>
                            <div class="text-xs text-slate-500"><?= e($payment['customer_email'] ?? $payment['buyer_email']) ?></div>
                        </a>
                        <?php else: ?>
                        <div class="text-sm text-slate-800 dark:text-white"><?= e($payment['buyer_name'] ?? '-') ?></div>
                        <div class="text-xs text-slate-500"><?= e($payment['buyer_email']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <code class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded"><?= e($payment['digistore_order_id']) ?></code>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-<?= $tc['color'] ?>-100 text-<?= $tc['color'] ?>-700 dark:bg-<?= $tc['color'] ?>-900/30 dark:text-<?= $tc['color'] ?>-300">
                            <?= $tc['label'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <span class="text-lg font-bold text-slate-800 dark:text-white"><?= number_format($payment['amount'], 2, ',', '.') ?> €</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-<?= $sc['color'] ?>-100 text-<?= $sc['color'] ?>-700 dark:bg-<?= $sc['color'] ?>-900/30 dark:text-<?= $sc['color'] ?>-300">
                            <i class="fas <?= $sc['icon'] ?>"></i>
                            <?= $sc['label'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <button onclick="showPaymentDetails(<?= htmlspecialchars(json_encode($payment), ENT_QUOTES) ?>)"
                                class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all" 
                                title="Details anzeigen">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($payments)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>Keine Zahlungen gefunden</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Zeige <?= ($offset + 1) ?> - <?= min($offset + $perPage, $totalCount) ?> von <?= $totalCount ?> Zahlungen
        </p>
        <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
               class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
               class="px-3 py-1 border rounded-lg text-sm <?= $i === $page ? 'bg-primary-600 border-primary-600 text-white' : 'border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
               class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Payment Details Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-receipt text-green-500 mr-2"></i>Zahlungsdetails
            </h3>
            <button onclick="closePaymentModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="paymentModalContent" class="p-6"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const isDark = document.documentElement.classList.contains('dark');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Umsatz (€)',
            data: <?= json_encode($chartRevenue) ?>,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
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
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y.toLocaleString('de-DE') + ' €';
                    }
                }
            }
        },
        scales: {
            x: { 
                grid: { display: false }, 
                ticks: { color: isDark ? '#94a3b8' : '#64748b', maxTicksLimit: 7 } 
            },
            y: { 
                beginAtZero: true, 
                grid: { color: isDark ? '#334155' : '#e2e8f0' }, 
                ticks: { 
                    color: isDark ? '#94a3b8' : '#64748b',
                    callback: function(value) {
                        return value.toLocaleString('de-DE') + ' €';
                    }
                } 
            }
        }
    }
});

// Payment Details Modal
function showPaymentDetails(payment) {
    const modal = document.getElementById('paymentModal');
    const content = document.getElementById('paymentModalContent');
    
    let ipnData = '';
    if (payment.ipn_data) {
        try {
            const data = typeof payment.ipn_data === 'string' ? JSON.parse(payment.ipn_data) : payment.ipn_data;
            ipnData = `<pre class="mt-4 p-3 bg-slate-100 dark:bg-slate-700 rounded-lg text-xs overflow-x-auto">${JSON.stringify(data, null, 2)}</pre>`;
        } catch(e) {
            ipnData = `<pre class="mt-4 p-3 bg-slate-100 dark:bg-slate-700 rounded-lg text-xs">${payment.ipn_data}</pre>`;
        }
    }
    
    content.innerHTML = `
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-slate-500">Order ID</p>
                <p class="font-medium text-slate-800 dark:text-white">${payment.digistore_order_id}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Produkt ID</p>
                <p class="font-medium text-slate-800 dark:text-white">${payment.digistore_product_id || '-'}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Käufer</p>
                <p class="font-medium text-slate-800 dark:text-white">${payment.buyer_name || '-'}</p>
                <p class="text-sm text-slate-500">${payment.buyer_email}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Betrag</p>
                <p class="text-2xl font-bold text-green-600">${parseFloat(payment.amount).toLocaleString('de-DE', {minimumFractionDigits: 2})} €</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Typ</p>
                <p class="font-medium text-slate-800 dark:text-white">${payment.payment_type}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Status</p>
                <p class="font-medium text-slate-800 dark:text-white">${payment.status}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Erstellt</p>
                <p class="font-medium text-slate-800 dark:text-white">${new Date(payment.created_at).toLocaleString('de-DE')}</p>
            </div>
            <div>
                <p class="text-sm text-slate-500">Affiliate ID</p>
                <p class="font-medium text-slate-800 dark:text-white">${payment.digistore_affiliate_id || '-'}</p>
            </div>
        </div>
        ${ipnData ? '<h4 class="mt-6 font-semibold text-slate-800 dark:text-white">IPN Daten</h4>' + ipnData : ''}
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Close modal on backdrop click
document.getElementById('paymentModal').addEventListener('click', function(e) {
    if (e.target === this) closePaymentModal();
});
</script>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
