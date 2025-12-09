<?php
/**
 * Admin Kundenverwaltung
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'Kunden';

// Filter & Suche
$search = sanitize($_GET['search'] ?? '');
$status = sanitize($_GET['status'] ?? 'all');
$plan = sanitize($_GET['plan'] ?? 'all');
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

$whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

// Sortierung
$orderBy = match($sort) {
    'oldest' => 'created_at ASC',
    'name' => 'company_name ASC',
    'leads' => 'total_leads DESC',
    default => 'created_at DESC'
};

// Gesamt zählen
$totalCount = $db->fetchColumn("SELECT COUNT(*) FROM customers $whereClause", $params);
$totalPages = ceil($totalCount / $perPage);
$offset = ($page - 1) * $perPage;

// Kunden laden
$customers = $db->fetchAll("
    SELECT c.*,
           (SELECT COUNT(*) FROM campaigns WHERE customer_id = c.id) as campaign_count
    FROM customers c
    $whereClause
    ORDER BY $orderBy
    LIMIT $perPage OFFSET $offset
", $params);

// Statistiken
$planStats = $db->fetchAll("SELECT plan, COUNT(*) as count FROM customers GROUP BY plan");
$statusStats = $db->fetchAll("SELECT subscription_status, COUNT(*) as count FROM customers GROUP BY subscription_status");

include __DIR__ . '/../../includes/admin-header.php';
?>

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
            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Aktiv</option>
            <option value="trial" <?= $status === 'trial' ? 'selected' : '' ?>>Trial</option>
            <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Gekündigt</option>
            <option value="paused" <?= $status === 'paused' ? 'selected' : '' ?>>Pausiert</option>
        </select>
        
        <select name="plan" onchange="this.form.submit()"
                class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <option value="all" <?= $plan === 'all' ? 'selected' : '' ?>>Alle Pläne</option>
            <option value="starter" <?= $plan === 'starter' ? 'selected' : '' ?>>Starter</option>
            <option value="professional" <?= $plan === 'professional' ? 'selected' : '' ?>>Professional</option>
            <option value="enterprise" <?= $plan === 'enterprise' ? 'selected' : '' ?>>Enterprise</option>
        </select>
        
        <select name="sort" onchange="this.form.submit()"
                class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Neueste zuerst</option>
            <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Älteste zuerst</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A-Z</option>
            <option value="leads" <?= $sort === 'leads' ? 'selected' : '' ?>>Meiste Leads</option>
        </select>
        
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
            <i class="fas fa-filter mr-2"></i>Filtern
        </button>
        
        <?php if (!empty($search) || $status !== 'all' || $plan !== 'all'): ?>
        <a href="/admin/customers.php" class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white">
            <i class="fas fa-times mr-1"></i>Filter zurücksetzen
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $totalCount ?></p>
        <p class="text-sm text-slate-500">Gesamt</p>
    </div>
    <?php 
    $activeCount = 0;
    foreach ($statusStats as $stat) {
        if ($stat['subscription_status'] === 'active') $activeCount = $stat['count'];
    }
    ?>
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-green-600"><?= $activeCount ?></p>
        <p class="text-sm text-slate-500">Aktiv</p>
    </div>
    <?php 
    $starterCount = 0;
    $proCount = 0;
    foreach ($planStats as $stat) {
        if ($stat['plan'] === 'starter') $starterCount = $stat['count'];
        if ($stat['plan'] === 'professional') $proCount = $stat['count'];
    }
    ?>
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $starterCount ?></p>
        <p class="text-sm text-slate-500">Starter</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-lg p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-primary-600"><?= $proCount ?></p>
        <p class="text-sm text-slate-500">Professional</p>
    </div>
</div>

<!-- Customers Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Kunde</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Plan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Leads</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Conversions</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Registriert</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($customers as $customer): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <?php if (!empty($customer['logo_url'])): ?>
                            <img src="<?= e($customer['logo_url']) ?>" alt="" class="w-10 h-10 rounded-lg object-cover">
                            <?php else: ?>
                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                                <span class="text-primary-600 dark:text-primary-400 font-medium text-sm">
                                    <?= strtoupper(substr($customer['company_name'], 0, 2)) ?>
                                </span>
                            </div>
                            <?php endif; ?>
                            <div>
                                <p class="font-medium text-slate-800 dark:text-white"><?= e($customer['company_name']) ?></p>
                                <p class="text-xs text-slate-500">
                                    <a href="https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud" target="_blank" class="hover:text-primary-600">
                                        <?= e($customer['subdomain']) ?>.empfehlungen.cloud <i class="fas fa-external-link text-[10px]"></i>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
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
                    <td class="px-6 py-4">
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
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                        <?= number_format($customer['total_leads'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 text-slate-600 dark:text-slate-300">
                        <?= number_format($customer['total_conversions'], 0, ',', '.') ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500">
                        <?= date('d.m.Y', strtotime($customer['created_at'])) ?>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="/admin/customer-detail.php?id=<?= $customer['id'] ?>" 
                               class="p-2 text-slate-400 hover:text-primary-600 transition-colors" title="Details anzeigen">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud" target="_blank"
                               class="p-2 text-slate-400 hover:text-green-600 transition-colors" title="Seite öffnen">
                                <i class="fas fa-external-link"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($customers)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
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
