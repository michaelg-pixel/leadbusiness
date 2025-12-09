<?php
/**
 * Leadbusiness - Leads-Verwaltung (Empfehler-Liste)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

// Auth prüfen
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
    $params[] = $searchPattern;
    $params[] = $searchPattern;
    $params[] = $searchPattern;
}

// Count
$totalLeads = $db->fetch("SELECT COUNT(*) as cnt FROM leads WHERE {$where}", $params)['cnt'];
$totalPages = ceil($totalLeads / $perPage);
$offset = ($page - 1) * $perPage;

// Leads laden
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

$pageTitle = 'Empfehler verwalten';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Leadbusiness</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50">
    
    <div class="flex h-screen">
        
        <!-- Sidebar (wie in index.php) -->
        <aside class="w-64 bg-white border-r hidden lg:block">
            <div class="p-6 border-b">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-share-nodes text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Leadbusiness</span>
                </a>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-home w-5"></i><span>Übersicht</span>
                </a>
                <a href="/dashboard/leads.php" class="flex items-center gap-3 px-4 py-3 text-indigo-600 bg-indigo-50 rounded-xl font-medium">
                    <i class="fas fa-users w-5"></i><span>Empfehler</span>
                </a>
                <a href="/dashboard/rewards.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-gift w-5"></i><span>Belohnungen</span>
                </a>
                <a href="/dashboard/design.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-palette w-5"></i><span>Design</span>
                </a>
                <a href="/dashboard/settings.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-cog w-5"></i><span>Einstellungen</span>
                </a>
            </nav>
            
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white w-64">
                <a href="/dashboard/logout.php" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-sign-out-alt"></i>Abmelden
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            
            <header class="bg-white border-b px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">Empfehler</h1>
                    
                    <?php if ($customer['plan'] === 'professional'): ?>
                    <a href="/dashboard/export.php?type=leads" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">
                        <i class="fas fa-download mr-2"></i>Exportieren
                    </a>
                    <?php endif; ?>
                </div>
            </header>
            
            <div class="p-6">
                
                <!-- Status Filter -->
                <div class="flex flex-wrap gap-4 mb-6">
                    <a href="?status=all" class="px-4 py-2 rounded-xl font-medium <?= $status === 'all' ? 'bg-indigo-500 text-white' : 'bg-white text-gray-600' ?>">
                        Alle (<?= array_sum($statusCounts) ?>)
                    </a>
                    <a href="?status=active" class="px-4 py-2 rounded-xl font-medium <?= $status === 'active' ? 'bg-green-500 text-white' : 'bg-white text-gray-600' ?>">
                        Aktiv (<?= $statusCounts['active'] ?>)
                    </a>
                    <a href="?status=pending" class="px-4 py-2 rounded-xl font-medium <?= $status === 'pending' ? 'bg-yellow-500 text-white' : 'bg-white text-gray-600' ?>">
                        Ausstehend (<?= $statusCounts['pending'] ?>)
                    </a>
                    <a href="?status=inactive" class="px-4 py-2 rounded-xl font-medium <?= $status === 'inactive' ? 'bg-gray-500 text-white' : 'bg-white text-gray-600' ?>">
                        Inaktiv (<?= $statusCounts['inactive'] ?>)
                    </a>
                </div>
                
                <!-- Search -->
                <div class="bg-white rounded-xl p-4 mb-6">
                    <form method="GET" class="flex gap-4">
                        <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Suche nach E-Mail, Name oder Code..."
                               class="flex-1 px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <button type="submit" class="px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                            <i class="fas fa-search mr-2"></i>Suchen
                        </button>
                    </form>
                </div>
                
                <!-- Leads Table -->
                <div class="bg-white rounded-xl overflow-hidden shadow-sm">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Empfehler</th>
                                <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">Code</th>
                                <th class="text-center px-6 py-4 text-sm font-semibold text-gray-600">Klicks</th>
                                <th class="text-center px-6 py-4 text-sm font-semibold text-gray-600">Empfehlungen</th>
                                <th class="text-center px-6 py-4 text-sm font-semibold text-gray-600">Stufe</th>
                                <th class="text-center px-6 py-4 text-sm font-semibold text-gray-600">Status</th>
                                <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">Registriert</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php if (empty($leads)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Keine Empfehler gefunden.
                                </td>
                            </tr>
                            <?php else: ?>
                            <?php foreach ($leads as $lead): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900"><?= htmlspecialchars($lead['name'] ?: '-') ?></div>
                                    <div class="text-sm text-gray-500"><?= htmlspecialchars($lead['email']) ?></div>
                                </td>
                                <td class="px-6 py-4">
                                    <code class="text-sm bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($lead['referral_code']) ?></code>
                                </td>
                                <td class="px-6 py-4 text-center"><?= number_format($lead['clicks']) ?></td>
                                <td class="px-6 py-4 text-center font-semibold text-green-600"><?= number_format($lead['conversions']) ?></td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($lead['current_reward_level'] > 0): ?>
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">
                                        Stufe <?= $lead['current_reward_level'] ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php
                                    $statusColors = [
                                        'active' => 'bg-green-100 text-green-700',
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'inactive' => 'bg-gray-100 text-gray-700',
                                        'blocked' => 'bg-red-100 text-red-700'
                                    ];
                                    $statusLabels = [
                                        'active' => 'Aktiv',
                                        'pending' => 'Ausstehend',
                                        'inactive' => 'Inaktiv',
                                        'blocked' => 'Gesperrt'
                                    ];
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$lead['status']] ?>">
                                        <?= $statusLabels[$lead['status']] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-500">
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
                <div class="flex justify-center gap-2 mt-6">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?= $i ?>&status=<?= $status ?>&search=<?= urlencode($search) ?>" 
                       class="px-4 py-2 rounded-lg <?= $i === $page ? 'bg-indigo-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-100' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
                
            </div>
        </main>
    </div>
</body>
</html>
