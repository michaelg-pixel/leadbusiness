<?php
/**
 * Admin Kundenverwaltung - ERWEITERT
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'Kunden';

// CSV Export
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="kunden-export-' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
    
    fputcsv($output, ['ID', 'Firma', 'E-Mail', 'Kontakt', 'Subdomain', 'Branche', 'Plan', 'Status', 'Leads', 'Conversions', 'Registriert', 'Letzter Login'], ';');
    
    $allCustomers = $db->fetchAll("SELECT * FROM customers ORDER BY created_at DESC");
    foreach ($allCustomers as $c) {
        fputcsv($output, [
            $c['id'],
            $c['company_name'],
            $c['email'],
            $c['contact_name'],
            $c['subdomain'],
            $c['industry'],
            $c['plan'],
            $c['subscription_status'],
            $c['total_leads'],
            $c['total_conversions'],
            $c['created_at'],
            $c['last_login_at'] ?? ''
        ], ';');
    }
    fclose($output);
    exit;
}

// Filter & Suche
$search = sanitize($_GET['search'] ?? '');
$status = sanitize($_GET['status'] ?? 'all');
$plan = sanitize($_GET['plan'] ?? 'all');
$industry = sanitize($_GET['industry'] ?? 'all');
$sort = sanitize($_GET['sort'] ?? 'newest');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

// Query bauen
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(company_name LIKE ? OR email LIKE ? OR subdomain LIKE ? OR contact_name LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

if ($status !== 'all') {
    $where[] = "subscription_status = ?";
    $params[] = $status;
}

if ($plan !== 'all') {
    $where[] = "plan = ?";
    $params[] = $plan;
}

if ($industry !== 'all') {
    $where[] = "industry = ?";
    $params[] = $industry;
}

$whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

// Sortierung
$orderBy = match($sort) {
    'oldest' => 'created_at ASC',
    'name' => 'company_name ASC',
    'leads' => 'total_leads DESC',
    'last_login' => 'last_login_at DESC',
    'activity' => 'last_login_at DESC',
    default => 'created_at DESC'
};

// Gesamt zählen
$totalCount = $db->fetchColumn("SELECT COUNT(*) FROM customers $whereClause", $params);
$totalPages = ceil($totalCount / $perPage);
$offset = ($page - 1) * $perPage;

// Kunden laden mit erweiterten Daten
$customers = $db->fetchAll("
    SELECT c.*,
           (SELECT COUNT(*) FROM campaigns WHERE customer_id = c.id) as campaign_count,
           CASE 
               WHEN c.subscription_status = 'trial' AND c.subscription_ends_at IS NOT NULL 
               THEN DATEDIFF(c.subscription_ends_at, NOW())
               ELSE NULL 
           END as trial_days_left
    FROM customers c
    $whereClause
    ORDER BY $orderBy
    LIMIT $perPage OFFSET $offset
", $params);

// Statistiken
$planStats = $db->fetchAll("SELECT plan, COUNT(*) as count FROM customers GROUP BY plan");
$statusStats = $db->fetchAll("SELECT subscription_status, COUNT(*) as count FROM customers GROUP BY subscription_status");

// Branchen für Filter
$industries = $db->fetchAll("SELECT DISTINCT industry FROM customers WHERE industry IS NOT NULL AND industry != '' ORDER BY industry");

// MRR berechnen
$mrr = $db->fetchColumn("
    SELECT SUM(CASE 
        WHEN plan = 'starter' AND subscription_status = 'active' THEN 49
        WHEN plan = 'professional' AND subscription_status = 'active' THEN 99
        WHEN plan = 'enterprise' AND subscription_status = 'active' THEN 199
        ELSE 0
    END) FROM customers
") ?? 0;

// Heute aktive Kunden
$activeToday = $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE DATE(last_login_at) = CURDATE()") ?? 0;

// Trial-zu-Paid Conversion (letzte 30 Tage)
$trialCount = $db->fetchColumn("SELECT COUNT(*) FROM customers WHERE subscription_status = 'trial'") ?? 0;

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- Header mit Export -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Kundenverwaltung</h1>
        <p class="text-slate-500">Alle Kunden und deren Empfehlungsprogramme verwalten</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="?export=csv" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all">
            <i class="fas fa-download mr-2"></i>CSV Export
        </a>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $totalCount ?></p>
                <p class="text-sm text-slate-500">Kunden gesamt</p>
            </div>
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-primary-600"></i>
            </div>
        </div>
    </div>
    
    <?php 
    $activeCount = 0;
    $trialStatusCount = 0;
    foreach ($statusStats as $stat) {
        if ($stat['subscription_status'] === 'active') $activeCount = $stat['count'];
        if ($stat['subscription_status'] === 'trial') $trialStatusCount = $stat['count'];
    }
    ?>
    
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-green-600"><?= $activeCount ?></p>
                <p class="text-sm text-slate-500">Aktiv (zahlend)</p>
            </div>
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-blue-600"><?= $trialStatusCount ?></p>
                <p class="text-sm text-slate-500">Im Trial</p>
            </div>
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-hourglass-half text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-amber-600"><?= number_format($mrr, 0, ',', '.') ?> €</p>
                <p class="text-sm text-slate-500">MRR</p>
            </div>
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-euro-sign text-amber-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-purple-600"><?= $activeToday ?></p>
                <p class="text-sm text-slate-500">Heute aktiv</p>
            </div>
            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-signal text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white dark:bg-slate-800 rounded-xl p-4 mb-6 shadow-sm border border-slate-200 dark:border-slate-700">
    <form method="GET" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="<?= e($search) ?>" 
                       placeholder="Suche nach Name, E-Mail, Subdomain..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                              bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                              focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <select name="status" onchange="this.form.submit()"
                class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>Alle Status</option>
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>✅ Aktiv</option>
            <option value="trial" <?= $status === 'trial' ? 'selected' : '' ?>>⏳ Trial</option>
            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>❌ Gekündigt</option>
            <option value="paused" <?= $status === 'paused' ? 'selected' : '' ?>>⏸️ Pausiert</option>
        </select>
        
        <select name="plan" onchange="this.form.submit()"
                class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <option value="all" <?= $plan === 'all' ? 'selected' : '' ?>>Alle Pläne</option>
            <option value="starter" <?= $plan === 'starter' ? 'selected' : '' ?>>Starter (49€)</option>
            <option value="professional" <?= $plan === 'professional' ? 'selected' : '' ?>>Professional (99€)</option>
            <option value="enterprise" <?= $plan === 'enterprise' ? 'selected' : '' ?>>Enterprise</option>
        </select>
        
        <select name="industry" onchange="this.form.submit()"
                class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <option value="all" <?= $industry === 'all' ? 'selected' : '' ?>>Alle Branchen</option>
            <?php foreach ($industries as $ind): ?>
            <option value="<?= e($ind['industry']) ?>" <?= $industry === $ind['industry'] ? 'selected' : '' ?>>
                <?= e(ucfirst($ind['industry'])) ?>
            </option>
            <?php endforeach; ?>
        </select>
        
        <select name="sort" onchange="this.form.submit()"
                class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Neueste zuerst</option>
            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Älteste zuerst</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
            <option value="leads" <?= $sort === 'leads' ? 'selected' : '' ?>>Meiste Leads</option>
            <option value="last_login" <?= $sort === 'last_login' ? 'selected' : '' ?>>Letzter Login</option>
        </select>
        
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
            <i class="fas fa-filter mr-2"></i>Filtern
        </button>
        
        <?php if (!empty($search) || $status !== 'all' || $plan !== 'all' || $industry !== 'all'): ?>
        <a href="/admin/customers.php" class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white">
            <i class="fas fa-times mr-1"></i>Reset
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Customers Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Kunde</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Subdomain</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Branche</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Plan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Leads</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Letzter Login</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($customers as $customer): ?>
                <?php
                // Online-Status: In den letzten 15 Minuten aktiv
                $isOnline = $customer['last_login_at'] && 
                            strtotime($customer['last_login_at']) > strtotime('-15 minutes');
                $lastLoginAgo = $customer['last_login_at'] ? timeAgo($customer['last_login_at']) : null;
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <?php if (!empty($customer['logo_url'])): ?>
                                <img src="<?= e($customer['logo_url']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover">
                                <?php else: ?>
                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                    <span class="text-primary-600 dark:text-primary-400 font-medium text-sm">
                                        <?= strtoupper(substr($customer['company_name'], 0, 2)) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                                <?php if ($isOnline): ?>
                                <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white dark:border-slate-800" title="Online"></span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <p class="font-medium text-slate-800 dark:text-white"><?= e($customer['company_name']) ?></p>
                                <p class="text-xs text-slate-500"><?= e($customer['email']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <a href="https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud" target="_blank" 
                           class="text-sm text-primary-600 hover:text-primary-700 hover:underline">
                            <?= e($customer['subdomain']) ?>.empfehlungen.cloud
                            <i class="fas fa-external-link text-[10px] ml-1"></i>
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-sm text-slate-600 dark:text-slate-300"><?= e(ucfirst($customer['industry'] ?? '-')) ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $planColors = [
                            'starter' => 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200',
                            'professional' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
                            'enterprise' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                        ];
                        $planColor = $planColors[$customer['plan']] ?? $planColors['starter'];
                        ?>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full <?= $planColor ?>">
                            <?= ucfirst($customer['plan']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                            'trial' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                            'paused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                        ];
                        $statusColor = $statusColors[$customer['subscription_status']] ?? $statusColors['trial'];
                        $statusLabels = ['active' => 'Aktiv', 'trial' => 'Trial', 'cancelled' => 'Gekündigt', 'paused' => 'Pausiert'];
                        ?>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full <?= $statusColor ?>">
                            <?= $statusLabels[$customer['subscription_status']] ?? $customer['subscription_status'] ?>
                        </span>
                        <?php if ($customer['subscription_status'] === 'trial' && $customer['trial_days_left'] !== null): ?>
                        <span class="block text-xs text-slate-400 mt-1">
                            <?php if ($customer['trial_days_left'] <= 0): ?>
                                <span class="text-red-500">Abgelaufen!</span>
                            <?php elseif ($customer['trial_days_left'] <= 3): ?>
                                <span class="text-amber-500"><?= $customer['trial_days_left'] ?> Tage übrig</span>
                            <?php else: ?>
                                <?= $customer['trial_days_left'] ?> Tage übrig
                            <?php endif; ?>
                        </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-semibold text-slate-800 dark:text-white"><?= number_format($customer['total_leads'], 0, ',', '.') ?></span>
                        <span class="text-slate-400 text-xs">/</span>
                        <span class="text-slate-500 text-sm"><?= number_format($customer['total_conversions'], 0, ',', '.') ?></span>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($customer['last_login_at']): ?>
                        <div class="flex items-center gap-2">
                            <?php if ($isOnline): ?>
                            <span class="flex items-center gap-1 text-green-600 text-sm">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                Online
                            </span>
                            <?php else: ?>
                            <span class="text-sm text-slate-500" title="<?= date('d.m.Y H:i', strtotime($customer['last_login_at'])) ?>">
                                <?= $lastLoginAgo ?>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <span class="text-sm text-slate-400">Nie eingeloggt</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="/admin/customer-detail.php?id=<?= $customer['id'] ?>" 
                               class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all" 
                               title="Details anzeigen">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/admin/login-as-customer.php?id=<?= $customer['id'] ?>" 
                               class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all" 
                               title="Als Kunde einloggen">
                                <i class="fas fa-user-secret"></i>
                            </a>
                            <a href="https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud" target="_blank"
                               class="p-2 text-slate-400 hover:text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-all" 
                               title="Empfehlungsseite öffnen">
                                <i class="fas fa-external-link"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>Keine Kunden gefunden</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Zeige <?= ($offset + 1) ?> - <?= min($offset + $perPage, $totalCount) ?> von <?= $totalCount ?> Kunden
        </p>
        <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" 
               class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
               class="px-3 py-1 border rounded-lg text-sm transition-all <?= $i === $page ? 'bg-primary-600 border-primary-600 text-white' : 'border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" 
               class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
