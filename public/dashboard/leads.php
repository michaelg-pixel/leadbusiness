<?php
/**
 * Leadbusiness - Leads-Verwaltung
 * Mit Dark/Light Mode
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

use Leadbusiness\Auth;
use Leadbusiness\Database;

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

// Filter
$status = $_GET['status'] ?? 'all';
$search = trim($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

// Query bauen
$where = "customer_id = ?";
$params = [$customerId];

if ($status !== 'all') {
    $where .= " AND status = ?";
    $params[] = $status;
}

if ($search) {
    $where .= " AND (email LIKE ? OR name LIKE ? OR referral_code LIKE ?)";
    $searchPattern = "%{$search}%";
    $params = array_merge($params, [$searchPattern, $searchPattern, $searchPattern]);
}

$totalLeads = $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE {$where}", $params);
$totalPages = ceil($totalLeads / $perPage);
$offset = ($page - 1) * $perPage;

$leads = $db->fetchAll(
    "SELECT * FROM leads WHERE {$where} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}",
    $params
);

// Stats
$statusStats = $db->fetchAll(
    "SELECT status, COUNT(*) as count FROM leads WHERE customer_id = ? GROUP BY status",
    [$customerId]
);

$statusCounts = ['active' => 0, 'pending' => 0, 'inactive' => 0, 'blocked' => 0];
foreach ($statusStats as $s) {
    $statusCounts[$s['status']] = $s['count'];
}

$pageTitle = 'Empfehler';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<!-- Status Filter -->
<div class="flex flex-wrap gap-3 mb-6">
    <a href="?status=all" class="px-4 py-2 rounded-xl font-medium transition-all <?= $status === 'all' ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-primary-500' ?>">
        Alle (<?= array_sum($statusCounts) ?>)
    </a>
    <a href="?status=active" class="px-4 py-2 rounded-xl font-medium transition-all <?= $status === 'active' ? 'bg-green-600 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-green-500' ?>">
        Aktiv (<?= $statusCounts['active'] ?>)
    </a>
    <a href="?status=pending" class="px-4 py-2 rounded-xl font-medium transition-all <?= $status === 'pending' ? 'bg-amber-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-amber-500' ?>">
        Ausstehend (<?= $statusCounts['pending'] ?>)
    </a>
    <a href="?status=inactive" class="px-4 py-2 rounded-xl font-medium transition-all <?= $status === 'inactive' ? 'bg-slate-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:border-slate-500' ?>">
        Inaktiv (<?= $statusCounts['inactive'] ?>)
    </a>
</div>

<!-- Search & Export -->
<div class="bg-white dark:bg-slate-800 rounded-xl p-4 mb-6 border border-slate-200 dark:border-slate-700">
    <form method="GET" class="flex flex-col sm:flex-row gap-4">
        <input type="hidden" name="status" value="<?= e($status) ?>">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" name="search" value="<?= e($search) ?>" 
                   placeholder="Suche nach E-Mail, Name oder Code..."
                   class="w-full pl-10 pr-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                          bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                          focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
        <button type="submit" class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-all">
            Suchen
        </button>
        <?php if ($customer['plan'] === 'professional'): ?>
        <a href="/dashboard/export.php?type=leads" 
           class="px-6 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-all text-center">
            <i class="fas fa-download mr-2"></i>Export
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- Leads Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl overflow-hidden shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Empfehler</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Code</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Klicks</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Empfehlungen</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Stufe</th>
                    <th class="text-center px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Status</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Registriert</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($leads)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">
                        <i class="fas fa-users text-4xl mb-3 opacity-50 block"></i>
                        Keine Empfehler gefunden.
                    </td>
                </tr>
                <?php else: ?>
                <?php foreach ($leads as $lead): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-medium text-slate-800 dark:text-white"><?= e($lead['name'] ?: '-') ?></div>
                        <div class="text-sm text-slate-500 dark:text-slate-400"><?= e($lead['email']) ?></div>
                    </td>
                    <td class="px-6 py-4">
                        <code class="text-sm bg-slate-100 dark:bg-slate-600 text-slate-700 dark:text-slate-200 px-2 py-1 rounded"><?= e($lead['referral_code']) ?></code>
                    </td>
                    <td class="px-6 py-4 text-center text-slate-600 dark:text-slate-300"><?= number_format($lead['clicks'], 0, ',', '.') ?></td>
                    <td class="px-6 py-4 text-center font-semibold text-green-600 dark:text-green-400"><?= number_format($lead['conversions'], 0, ',', '.') ?></td>
                    <td class="px-6 py-4 text-center">
                        <?php if ($lead['current_reward_level'] > 0): ?>
                        <span class="px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full text-sm font-medium">
                            Stufe <?= $lead['current_reward_level'] ?>
                        </span>
                        <?php else: ?>
                        <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <?php
                        $statusColors = [
                            'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                            'inactive' => 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-300',
                            'blocked' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                        ];
                        $statusLabels = ['active' => 'Aktiv', 'pending' => 'Ausstehend', 'inactive' => 'Inaktiv', 'blocked' => 'Gesperrt'];
                        ?>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium <?= $statusColors[$lead['status']] ?>">
                            <?= $statusLabels[$lead['status']] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm text-slate-500 dark:text-slate-400">
                        <?= date('d.m.Y', strtotime($lead['created_at'])) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Seite <?= $page ?> von <?= $totalPages ?> (<?= $totalLeads ?> Empfehler)
        </p>
        <div class="flex gap-2">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>" 
               class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
            <a href="?page=<?= $i ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>" 
               class="px-3 py-1 border rounded-lg text-sm transition-all <?= $i === $page ? 'bg-primary-600 border-primary-600 text-white' : 'border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>" 
               class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
