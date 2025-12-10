<?php
/**
 * Admin Globale Lead-Übersicht
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'Alle Leads';

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="leads-export-' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    fputcsv($output, ['ID', 'E-Mail', 'Name', 'Referral Code', 'Kunde', 'Branche', 'Klicks', 'Conversions', 'Status', 'Fraud Score', 'Registriert', 'Letzte Aktivität'], ';');
    
    $allLeads = $db->fetchAll("
        SELECT l.*, c.company_name, c.industry 
        FROM leads l 
        LEFT JOIN customers c ON l.customer_id = c.id 
        ORDER BY l.created_at DESC
    ");
    
    foreach ($allLeads as $lead) {
        fputcsv($output, [
            $lead['id'],
            $lead['email'],
            $lead['name'],
            $lead['referral_code'],
            $lead['company_name'],
            $lead['industry'],
            $lead['clicks'],
            $lead['conversions'],
            $lead['status'],
            $lead['fraud_score'],
            $lead['created_at'],
            $lead['last_activity_at']
        ], ';');
    }
    fclose($output);
    exit;
}

// Aktionen verarbeiten
if (isPost()) {
    $action = $_POST['action'] ?? '';
    $leadId = intval($_POST['lead_id'] ?? 0);
    
    switch ($action) {
        case 'block':
            if ($leadId) {
                $db->execute("UPDATE leads SET status = 'blocked' WHERE id = ?", [$leadId]);
                $_SESSION['flash_success'] = 'Lead wurde blockiert.';
            }
            break;
            
        case 'activate':
            if ($leadId) {
                $db->execute("UPDATE leads SET status = 'active' WHERE id = ?", [$leadId]);
                $_SESSION['flash_success'] = 'Lead wurde aktiviert.';
            }
            break;
            
        case 'delete':
            if ($leadId) {
                $db->execute("DELETE FROM leads WHERE id = ?", [$leadId]);
                $_SESSION['flash_success'] = 'Lead wurde gelöscht.';
            }
            break;
            
        case 'bulk_block':
            $ids = $_POST['selected_ids'] ?? [];
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $db->execute("UPDATE leads SET status = 'blocked' WHERE id IN ($placeholders)", $ids);
                $_SESSION['flash_success'] = count($ids) . ' Leads wurden blockiert.';
            }
            break;
            
        case 'bulk_delete':
            $ids = $_POST['selected_ids'] ?? [];
            if (!empty($ids)) {
                $placeholders = implode(',', array_fill(0, count($ids), '?'));
                $db->execute("DELETE FROM leads WHERE id IN ($placeholders)", $ids);
                $_SESSION['flash_success'] = count($ids) . ' Leads wurden gelöscht.';
            }
            break;
    }
    
    header('Location: /admin/leads.php' . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// Filter
$search = sanitize($_GET['search'] ?? '');
$status = sanitize($_GET['status'] ?? 'all');
$customer = intval($_GET['customer'] ?? 0);
$industry = sanitize($_GET['industry'] ?? 'all');
$fraud = sanitize($_GET['fraud'] ?? 'all');
$sort = sanitize($_GET['sort'] ?? 'newest');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 30;

// Query bauen
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(l.email LIKE ? OR l.name LIKE ? OR l.referral_code LIKE ? OR c.company_name LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if ($status !== 'all') {
    $where[] = "l.status = ?";
    $params[] = $status;
}

if ($customer > 0) {
    $where[] = "l.customer_id = ?";
    $params[] = $customer;
}

if ($industry !== 'all') {
    $where[] = "c.industry = ?";
    $params[] = $industry;
}

if ($fraud !== 'all') {
    switch ($fraud) {
        case 'high':
            $where[] = "l.fraud_score >= 50";
            break;
        case 'medium':
            $where[] = "l.fraud_score >= 30 AND l.fraud_score < 50";
            break;
        case 'low':
            $where[] = "l.fraud_score < 30";
            break;
    }
}

$whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

// Sortierung
$orderBy = match($sort) {
    'oldest' => 'l.created_at ASC',
    'email' => 'l.email ASC',
    'clicks' => 'l.clicks DESC',
    'conversions' => 'l.conversions DESC',
    'fraud' => 'l.fraud_score DESC',
    'activity' => 'l.last_activity_at DESC',
    default => 'l.created_at DESC'
};

// Statistiken
$stats = [
    'total' => $db->fetchColumn("SELECT COUNT(*) FROM leads") ?? 0,
    'active' => $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE status = 'active'") ?? 0,
    'pending' => $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE status = 'pending'") ?? 0,
    'blocked' => $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE status = 'blocked'") ?? 0,
    'inactive' => $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE status = 'inactive'") ?? 0,
];

// Heute neue Leads
$stats['new_today'] = $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE DATE(created_at) = CURDATE()") ?? 0;
$stats['new_week'] = $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)") ?? 0;

// Gesamt-Klicks und Conversions
$stats['total_clicks'] = $db->fetchColumn("SELECT COALESCE(SUM(clicks), 0) FROM leads") ?? 0;
$stats['total_conversions'] = $db->fetchColumn("SELECT COALESCE(SUM(conversions), 0) FROM leads") ?? 0;

// Conversion Rate
$stats['conversion_rate'] = $stats['total_clicks'] > 0 
    ? round(($stats['total_conversions'] / $stats['total_clicks']) * 100, 2) 
    : 0;

// High Fraud Score
$stats['high_fraud'] = $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE fraud_score >= 50") ?? 0;

// Leads pro Branche
$leadsByIndustry = $db->fetchAll("
    SELECT c.industry, COUNT(l.id) as lead_count, SUM(l.conversions) as conversion_count
    FROM leads l
    JOIN customers c ON l.customer_id = c.id
    WHERE c.industry IS NOT NULL AND c.industry != ''
    GROUP BY c.industry
    ORDER BY lead_count DESC
    LIMIT 10
");

// Chart: Neue Leads pro Tag (letzte 14 Tage)
$chartData = $db->fetchAll("
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM leads 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date
");

$chartLabels = [];
$chartValues = [];
for ($i = 13; $i >= 0; $i--) {
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

// Top Leads (meiste Conversions)
$topLeads = $db->fetchAll("
    SELECT l.*, c.company_name, c.subdomain
    FROM leads l
    JOIN customers c ON l.customer_id = c.id
    ORDER BY l.conversions DESC
    LIMIT 10
");

// Kunden für Filter
$customers = $db->fetchAll("SELECT id, company_name FROM customers ORDER BY company_name");

// Branchen für Filter
$industries = $db->fetchAll("SELECT DISTINCT industry FROM customers WHERE industry IS NOT NULL AND industry != '' ORDER BY industry");

// Leads laden
$totalCount = $db->fetchColumn("
    SELECT COUNT(*) FROM leads l 
    LEFT JOIN customers c ON l.customer_id = c.id 
    $whereClause
", $params);

$totalPages = ceil($totalCount / $perPage);
$offset = ($page - 1) * $perPage;

$leads = $db->fetchAll("
    SELECT l.*, 
           c.company_name, c.subdomain, c.industry as customer_industry,
           r.email as referrer_email, r.name as referrer_name
    FROM leads l 
    LEFT JOIN customers c ON l.customer_id = c.id 
    LEFT JOIN leads r ON l.referred_by_id = r.id
    $whereClause
    ORDER BY $orderBy
    LIMIT $perPage OFFSET $offset
", $params);

// Status Config
$statusConfig = [
    'active' => ['label' => 'Aktiv', 'color' => 'green', 'icon' => 'fa-check-circle'],
    'pending' => ['label' => 'Ausstehend', 'color' => 'amber', 'icon' => 'fa-clock'],
    'inactive' => ['label' => 'Inaktiv', 'color' => 'slate', 'icon' => 'fa-pause-circle'],
    'blocked' => ['label' => 'Blockiert', 'color' => 'red', 'icon' => 'fa-ban']
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i><?= e($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">
            <i class="fas fa-users text-purple-500 mr-2"></i>Alle Leads
        </h1>
        <p class="text-slate-500">Globale Übersicht aller Empfehler im System</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="?export=csv" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all">
            <i class="fas fa-download mr-2"></i>CSV Export
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <a href="?status=all" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $status === 'all' ? 'border-primary-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-primary-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-slate-600"></i>
            </div>
            <span class="text-xs text-green-500">+<?= $stats['new_today'] ?> heute</span>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Gesamt</p>
    </a>
    
    <a href="?status=active" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $status === 'active' ? 'border-green-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-green-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-green-600"><?= number_format($stats['active'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Aktiv</p>
    </a>
    
    <a href="?status=pending" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $status === 'pending' ? 'border-amber-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-amber-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-amber-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-amber-600"><?= number_format($stats['pending'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Ausstehend</p>
    </a>
    
    <a href="?status=blocked" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $status === 'blocked' ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-red-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-ban text-red-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-red-600"><?= number_format($stats['blocked'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Blockiert</p>
    </a>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-mouse-pointer text-purple-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total_clicks'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Klicks</p>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-handshake text-white/80"></i>
            <span class="text-xs bg-white/20 px-2 py-0.5 rounded"><?= $stats['conversion_rate'] ?>%</span>
        </div>
        <h3 class="text-2xl font-bold"><?= number_format($stats['total_conversions'], 0, ',', '.') ?></h3>
        <p class="text-sm text-white/80">Conversions</p>
    </div>
</div>

<!-- Charts & Top Lists -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Chart -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-chart-line text-purple-500 mr-2"></i>Neue Leads (letzte 14 Tage)
        </h3>
        <div class="h-64">
            <canvas id="leadsChart"></canvas>
        </div>
    </div>
    
    <!-- Top Leads -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-trophy text-amber-500 mr-2"></i>Top Empfehler
            </h3>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-700 max-h-72 overflow-y-auto">
            <?php foreach ($topLeads as $index => $lead): ?>
            <div class="flex items-center justify-between p-3 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                <div class="flex items-center gap-3">
                    <span class="w-6 h-6 flex items-center justify-center bg-<?= $index < 3 ? 'amber' : 'slate' ?>-100 dark:bg-<?= $index < 3 ? 'amber' : 'slate' ?>-900/30 rounded-full text-xs font-bold text-<?= $index < 3 ? 'amber' : 'slate' ?>-600">
                        <?= $index + 1 ?>
                    </span>
                    <div>
                        <p class="text-sm font-medium text-slate-800 dark:text-white truncate max-w-[140px]"><?= e($lead['name'] ?: $lead['email']) ?></p>
                        <p class="text-xs text-slate-500"><?= e($lead['company_name']) ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-green-600"><?= $lead['conversions'] ?></p>
                    <p class="text-xs text-slate-500"><?= $lead['clicks'] ?> Klicks</p>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($topLeads)): ?>
            <div class="p-6 text-center text-slate-500">
                <p class="text-sm">Noch keine Leads</p>
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
                       placeholder="E-Mail, Name, Referral Code, Firma..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                              bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                              focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Status</label>
            <select name="status" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Alle Status</option>
                <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>✅ Aktiv</option>
                <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>⏳ Ausstehend</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>⏸️ Inaktiv</option>
                <option value="blocked" <?= $status === 'blocked' ? 'selected' : '' ?>>🚫 Blockiert</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Kunde</label>
            <select name="customer" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <option value="0">Alle Kunden</option>
                <?php foreach ($customers as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $customer == $c['id'] ? 'selected' : '' ?>><?= e($c['company_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Branche</label>
            <select name="industry" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <option value="all">Alle Branchen</option>
                <?php foreach ($industries as $ind): ?>
                <option value="<?= e($ind['industry']) ?>" <?= $industry === $ind['industry'] ? 'selected' : '' ?>><?= e(ucfirst($ind['industry'])) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Fraud</label>
            <select name="fraud" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <option value="all" <?= $fraud === 'all' ? 'selected' : '' ?>>Alle</option>
                <option value="high" <?= $fraud === 'high' ? 'selected' : '' ?>>🔴 Hoch (≥50)</option>
                <option value="medium" <?= $fraud === 'medium' ? 'selected' : '' ?>>🟡 Mittel (30-49)</option>
                <option value="low" <?= $fraud === 'low' ? 'selected' : '' ?>>🟢 Niedrig (&lt;30)</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400 mb-1">Sortierung</label>
            <select name="sort" class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Neueste zuerst</option>
                <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Älteste zuerst</option>
                <option value="conversions" <?= $sort === 'conversions' ? 'selected' : '' ?>>Meiste Conversions</option>
                <option value="clicks" <?= $sort === 'clicks' ? 'selected' : '' ?>>Meiste Klicks</option>
                <option value="fraud" <?= $sort === 'fraud' ? 'selected' : '' ?>>Höchster Fraud Score</option>
                <option value="activity" <?= $sort === 'activity' ? 'selected' : '' ?>>Letzte Aktivität</option>
            </select>
        </div>
        
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
            <i class="fas fa-filter mr-2"></i>Filtern
        </button>
        
        <?php if (!empty($search) || $status !== 'all' || $customer > 0 || $industry !== 'all' || $fraud !== 'all'): ?>
        <a href="/admin/leads.php" class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-800">
            <i class="fas fa-times mr-1"></i>Reset
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Bulk Actions -->
<form method="POST" id="bulkForm">
    <input type="hidden" name="action" id="bulkAction" value="">
    
    <!-- Leads Table -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                    <input type="checkbox" id="selectAll" class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                    Alle auswählen
                </label>
                <span id="selectedCount" class="text-sm text-slate-500 hidden">
                    <span class="font-medium text-primary-600">0</span> ausgewählt
                </span>
            </div>
            <div id="bulkActions" class="hidden flex items-center gap-2">
                <button type="button" onclick="submitBulkAction('bulk_block')" class="px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-lg text-sm transition-all">
                    <i class="fas fa-ban mr-1"></i>Blockieren
                </button>
                <button type="button" onclick="submitBulkAction('bulk_delete')" class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm transition-all">
                    <i class="fas fa-trash mr-1"></i>Löschen
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 dark:bg-slate-700/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase w-10"></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Lead</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Kunde</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Referral Code</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Klicks</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Conv.</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Fraud</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Registriert</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php foreach ($leads as $lead): ?>
                    <?php $sc = $statusConfig[$lead['status']] ?? ['label' => $lead['status'], 'color' => 'slate', 'icon' => 'fa-question']; ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-4 py-3">
                            <input type="checkbox" name="selected_ids[]" value="<?= $lead['id'] ?>" class="lead-checkbox rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-slate-800 dark:text-white"><?= e($lead['email']) ?></div>
                            <?php if ($lead['name']): ?>
                            <div class="text-xs text-slate-500"><?= e($lead['name']) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3">
                            <a href="/admin/customer-detail.php?id=<?= $lead['customer_id'] ?>" class="text-sm text-primary-600 hover:underline">
                                <?= e($lead['company_name']) ?>
                            </a>
                            <div class="text-xs text-slate-500"><?= e(ucfirst($lead['customer_industry'] ?? '')) ?></div>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded"><?= e($lead['referral_code']) ?></code>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-medium text-slate-800 dark:text-white"><?= number_format($lead['clicks'], 0, ',', '.') ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold <?= $lead['conversions'] > 0 ? 'text-green-600' : 'text-slate-400' ?>"><?= $lead['conversions'] ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php
                            $fraudColor = 'green';
                            if ($lead['fraud_score'] >= 50) $fraudColor = 'red';
                            elseif ($lead['fraud_score'] >= 30) $fraudColor = 'amber';
                            ?>
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-<?= $fraudColor ?>-100 dark:bg-<?= $fraudColor ?>-900/30 text-<?= $fraudColor ?>-700 dark:text-<?= $fraudColor ?>-300 text-xs font-bold">
                                <?= $lead['fraud_score'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium rounded-full 
                                         bg-<?= $sc['color'] ?>-100 text-<?= $sc['color'] ?>-700 
                                         dark:bg-<?= $sc['color'] ?>-900/30 dark:text-<?= $sc['color'] ?>-300">
                                <i class="fas <?= $sc['icon'] ?>"></i>
                                <?= $sc['label'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-slate-800 dark:text-white"><?= date('d.m.Y', strtotime($lead['created_at'])) ?></div>
                            <div class="text-xs text-slate-500"><?= timeAgo($lead['created_at']) ?></div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <?php if ($lead['status'] !== 'blocked'): ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="block">
                                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                                    <button type="submit" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all" 
                                            title="Blockieren">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                </form>
                                <?php else: ?>
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="activate">
                                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                                    <button type="submit" class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-all" 
                                            title="Aktivieren">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <form method="POST" class="inline">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="lead_id" value="<?= $lead['id'] ?>">
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" 
                                            title="Löschen"
                                            onclick="return confirm('Lead wirklich löschen?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if (empty($leads)): ?>
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-slate-500">
                            <i class="fas fa-inbox text-4xl mb-3"></i>
                            <p>Keine Leads gefunden</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <p class="text-sm text-slate-500">
                Zeige <?= ($offset + 1) ?> - <?= min($offset + $perPage, $totalCount) ?> von <?= number_format($totalCount, 0, ',', '.') ?> Leads
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
</form>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Leads Chart
const ctx = document.getElementById('leadsChart').getContext('2d');
const isDark = document.documentElement.classList.contains('dark');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Neue Leads',
            data: <?= json_encode($chartValues) ?>,
            borderColor: '#a855f7',
            backgroundColor: 'rgba(168, 85, 247, 0.1)',
            fill: true,
            tension: 0.4,
            pointRadius: 3,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: isDark ? '#94a3b8' : '#64748b' } },
            y: { beginAtZero: true, grid: { color: isDark ? '#334155' : '#e2e8f0' }, ticks: { color: isDark ? '#94a3b8' : '#64748b', stepSize: 1 } }
        }
    }
});

// Bulk Selection
const selectAllCheckbox = document.getElementById('selectAll');
const leadCheckboxes = document.querySelectorAll('.lead-checkbox');
const selectedCountEl = document.getElementById('selectedCount');
const bulkActionsEl = document.getElementById('bulkActions');

function updateSelectionUI() {
    const checked = document.querySelectorAll('.lead-checkbox:checked');
    const count = checked.length;
    
    if (count > 0) {
        selectedCountEl.classList.remove('hidden');
        selectedCountEl.querySelector('span').textContent = count;
        bulkActionsEl.classList.remove('hidden');
        bulkActionsEl.classList.add('flex');
    } else {
        selectedCountEl.classList.add('hidden');
        bulkActionsEl.classList.add('hidden');
        bulkActionsEl.classList.remove('flex');
    }
    
    selectAllCheckbox.checked = count === leadCheckboxes.length && count > 0;
    selectAllCheckbox.indeterminate = count > 0 && count < leadCheckboxes.length;
}

selectAllCheckbox.addEventListener('change', function() {
    leadCheckboxes.forEach(cb => cb.checked = this.checked);
    updateSelectionUI();
});

leadCheckboxes.forEach(cb => cb.addEventListener('change', updateSelectionUI));

function submitBulkAction(action) {
    if (confirm('Aktion für alle ausgewählten Leads ausführen?')) {
        document.getElementById('bulkAction').value = action;
        document.getElementById('bulkForm').submit();
    }
}
</script>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
