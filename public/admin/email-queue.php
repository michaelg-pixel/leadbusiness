<?php
/**
 * Admin E-Mail Queue Management
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'E-Mail Queue';

// Aktionen verarbeiten
if (isPost()) {
    $action = $_POST['action'] ?? '';
    $emailId = intval($_POST['email_id'] ?? 0);
    
    switch ($action) {
        case 'resend':
            if ($emailId) {
                $db->execute("UPDATE email_queue SET status = 'pending', attempts = 0, scheduled_for = NOW() WHERE id = ?", [$emailId]);
                $_SESSION['flash_success'] = 'E-Mail wurde zur erneuten Zustellung markiert.';
            }
            break;
            
        case 'resend_all_failed':
            $count = $db->execute("UPDATE email_queue SET status = 'pending', attempts = 0, scheduled_for = NOW() WHERE status = 'failed'");
            $_SESSION['flash_success'] = 'Alle fehlgeschlagenen E-Mails wurden zur erneuten Zustellung markiert.';
            break;
            
        case 'delete':
            if ($emailId) {
                $db->execute("DELETE FROM email_queue WHERE id = ?", [$emailId]);
                $_SESSION['flash_success'] = 'E-Mail wurde gelöscht.';
            }
            break;
            
        case 'delete_old':
            $days = intval($_POST['days'] ?? 30);
            $db->execute("DELETE FROM email_queue WHERE status = 'sent' AND sent_at < DATE_SUB(NOW(), INTERVAL ? DAY)", [$days]);
            $_SESSION['flash_success'] = "E-Mails älter als $days Tage wurden gelöscht.";
            break;
            
        case 'skip':
            if ($emailId) {
                $db->execute("UPDATE email_queue SET status = 'skipped' WHERE id = ?", [$emailId]);
                $_SESSION['flash_success'] = 'E-Mail wurde übersprungen.';
            }
            break;
    }
    
    header('Location: /admin/email-queue.php' . (isset($_GET['filter']) ? '?filter=' . $_GET['filter'] : ''));
    exit;
}

// Filter
$filter = sanitize($_GET['filter'] ?? 'all');
$search = sanitize($_GET['search'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 30;

// Query bauen
$where = [];
$params = [];

if ($filter !== 'all') {
    $where[] = "status = ?";
    $params[] = $filter;
}

if (!empty($search)) {
    $where[] = "(to_email LIKE ? OR subject LIKE ? OR to_name LIKE ?)";
    $searchTerm = "%{$search}%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = empty($where) ? '' : 'WHERE ' . implode(' AND ', $where);

// Statistiken
$stats = [
    'total' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue") ?? 0,
    'pending' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'pending'") ?? 0,
    'sending' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'sending'") ?? 0,
    'sent' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'sent'") ?? 0,
    'failed' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'failed'") ?? 0,
    'bounced' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'bounced'") ?? 0,
    'skipped' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'skipped'") ?? 0,
];

// Heute
$stats['sent_today'] = $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'sent' AND DATE(sent_at) = CURDATE()") ?? 0;
$stats['failed_today'] = $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'failed' AND DATE(failed_at) = CURDATE()") ?? 0;

// Diese Woche
$stats['sent_week'] = $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'sent' AND sent_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)") ?? 0;

// Erfolgsrate
$totalProcessed = $stats['sent'] + $stats['failed'];
$stats['success_rate'] = $totalProcessed > 0 ? round(($stats['sent'] / $totalProcessed) * 100, 1) : 100;

// E-Mails pro Stunde (letzte 24h)
$emailsPerHour = $db->fetchAll("
    SELECT HOUR(sent_at) as hour, COUNT(*) as count
    FROM email_queue 
    WHERE status = 'sent' AND sent_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
    GROUP BY HOUR(sent_at)
    ORDER BY hour
");

// Chart-Daten: E-Mails letzte 7 Tage
$chartData = $db->fetchAll("
    SELECT DATE(created_at) as date, 
           SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
           SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
           COUNT(*) as total
    FROM email_queue 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    GROUP BY DATE(created_at)
    ORDER BY date
");

$chartLabels = [];
$chartSent = [];
$chartFailed = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $chartLabels[] = date('d.m.', strtotime($date));
    $found = false;
    foreach ($chartData as $row) {
        if ($row['date'] === $date) {
            $chartSent[] = (int)$row['sent'];
            $chartFailed[] = (int)$row['failed'];
            $found = true;
            break;
        }
    }
    if (!$found) {
        $chartSent[] = 0;
        $chartFailed[] = 0;
    }
}

// E-Mails laden
$totalCount = $db->fetchColumn("SELECT COUNT(*) FROM email_queue $whereClause", $params);
$totalPages = ceil($totalCount / $perPage);
$offset = ($page - 1) * $perPage;

$emails = $db->fetchAll("
    SELECT eq.*, 
           c.company_name, c.subdomain,
           l.name as lead_name, l.referral_code
    FROM email_queue eq
    LEFT JOIN customers c ON eq.customer_id = c.id
    LEFT JOIN leads l ON eq.lead_id = l.id
    $whereClause
    ORDER BY eq.created_at DESC
    LIMIT $perPage OFFSET $offset
", $params);

// Status Config
$statusConfig = [
    'pending' => ['label' => 'Wartend', 'color' => 'amber', 'icon' => 'fa-clock'],
    'sending' => ['label' => 'Wird gesendet', 'color' => 'blue', 'icon' => 'fa-spinner fa-spin'],
    'sent' => ['label' => 'Gesendet', 'color' => 'green', 'icon' => 'fa-check-circle'],
    'failed' => ['label' => 'Fehlgeschlagen', 'color' => 'red', 'icon' => 'fa-times-circle'],
    'bounced' => ['label' => 'Bounced', 'color' => 'orange', 'icon' => 'fa-exclamation-triangle'],
    'skipped' => ['label' => 'Übersprungen', 'color' => 'slate', 'icon' => 'fa-forward']
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
            <i class="fas fa-envelope text-primary-500 mr-2"></i>E-Mail Queue
        </h1>
        <p class="text-slate-500">E-Mail-Versand überwachen und verwalten</p>
    </div>
    <div class="flex items-center gap-3">
        <?php if ($stats['failed'] > 0): ?>
        <form method="POST" class="inline">
            <input type="hidden" name="action" value="resend_all_failed">
            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-all"
                    onclick="return confirm('Alle <?= $stats['failed'] ?> fehlgeschlagenen E-Mails erneut senden?')">
                <i class="fas fa-redo mr-2"></i>Alle Fehlgeschlagenen erneut senden
            </button>
        </form>
        <?php endif; ?>
        
        <button onclick="document.getElementById('cleanupModal').classList.remove('hidden');document.getElementById('cleanupModal').classList.add('flex')" 
                class="px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white rounded-lg transition-all">
            <i class="fas fa-broom mr-2"></i>Aufräumen
        </button>
    </div>
</div>

<!-- KPI Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
    <a href="?filter=all" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'all' ? 'border-primary-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-primary-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-inbox text-slate-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Gesamt</p>
    </a>
    
    <a href="?filter=pending" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'pending' ? 'border-amber-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-amber-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-amber-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-amber-600"><?= number_format($stats['pending'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Wartend</p>
    </a>
    
    <a href="?filter=sending" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'sending' ? 'border-blue-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-blue-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-spinner fa-spin text-blue-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-blue-600"><?= number_format($stats['sending'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Wird gesendet</p>
    </a>
    
    <a href="?filter=sent" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'sent' ? 'border-green-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-green-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <span class="text-xs text-green-500">+<?= $stats['sent_today'] ?> heute</span>
        </div>
        <h3 class="text-2xl font-bold text-green-600"><?= number_format($stats['sent'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Gesendet</p>
    </a>
    
    <a href="?filter=failed" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'failed' ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-red-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-red-600"><?= number_format($stats['failed'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Fehlgeschlagen</p>
    </a>
    
    <a href="?filter=bounced" class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border-2 <?= $filter === 'bounced' ? 'border-orange-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-orange-500 transition-all">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-orange-600"></i>
            </div>
        </div>
        <h3 class="text-2xl font-bold text-orange-600"><?= number_format($stats['bounced'], 0, ',', '.') ?></h3>
        <p class="text-sm text-slate-500">Bounced</p>
    </a>
    
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white">
        <div class="flex items-center justify-between mb-2">
            <i class="fas fa-percentage text-white/80"></i>
        </div>
        <h3 class="text-2xl font-bold"><?= $stats['success_rate'] ?>%</h3>
        <p class="text-sm text-white/80">Erfolgsrate</p>
    </div>
</div>

<!-- Chart -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-chart-bar text-primary-500 mr-2"></i>E-Mail-Versand (letzte 7 Tage)
        </h3>
        <div class="h-64">
            <canvas id="emailChart"></canvas>
        </div>
    </div>
    
    <!-- Quick Stats -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-tachometer-alt text-amber-500 mr-2"></i>Schnellübersicht
        </h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-calendar-day text-green-500"></i>
                    <span class="text-sm text-slate-600 dark:text-slate-300">Heute gesendet</span>
                </div>
                <span class="font-bold text-slate-800 dark:text-white"><?= number_format($stats['sent_today'], 0, ',', '.') ?></span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-calendar-week text-blue-500"></i>
                    <span class="text-sm text-slate-600 dark:text-slate-300">Diese Woche</span>
                </div>
                <span class="font-bold text-slate-800 dark:text-white"><?= number_format($stats['sent_week'], 0, ',', '.') ?></span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation text-red-500"></i>
                    <span class="text-sm text-slate-600 dark:text-slate-300">Heute fehlgeschlagen</span>
                </div>
                <span class="font-bold <?= $stats['failed_today'] > 0 ? 'text-red-600' : 'text-slate-800 dark:text-white' ?>"><?= $stats['failed_today'] ?></span>
            </div>
            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                <div class="flex items-center gap-3">
                    <i class="fas fa-forward text-slate-500"></i>
                    <span class="text-sm text-slate-600 dark:text-slate-300">Übersprungen</span>
                </div>
                <span class="font-bold text-slate-800 dark:text-white"><?= number_format($stats['skipped'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Filter & Search -->
<div class="bg-white dark:bg-slate-800 rounded-xl p-4 mb-6 shadow-sm border border-slate-200 dark:border-slate-700">
    <form method="GET" class="flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[250px]">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="<?= e($search) ?>" 
                       placeholder="E-Mail-Adresse oder Betreff suchen..."
                       class="w-full pl-10 pr-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg 
                              bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                              focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>
        </div>
        
        <input type="hidden" name="filter" value="<?= e($filter) ?>">
        
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
            <i class="fas fa-search mr-2"></i>Suchen
        </button>
        
        <?php if (!empty($search)): ?>
        <a href="?filter=<?= e($filter) ?>" class="px-4 py-2 text-slate-600 dark:text-slate-400 hover:text-slate-800">
            <i class="fas fa-times mr-1"></i>Reset
        </a>
        <?php endif; ?>
    </form>
</div>

<!-- E-Mail Table -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Empfänger</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Betreff</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Kunde</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Versuche</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Erstellt</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($emails as $email): ?>
                <?php $sc = $statusConfig[$email['status']] ?? ['label' => $email['status'], 'color' => 'slate', 'icon' => 'fa-question']; ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                    <td class="px-4 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full 
                                     bg-<?= $sc['color'] ?>-100 text-<?= $sc['color'] ?>-700 
                                     dark:bg-<?= $sc['color'] ?>-900/30 dark:text-<?= $sc['color'] ?>-300">
                            <i class="fas <?= $sc['icon'] ?>"></i>
                            <?= $sc['label'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-slate-800 dark:text-white"><?= e($email['to_email']) ?></div>
                        <?php if ($email['to_name']): ?>
                        <div class="text-xs text-slate-500"><?= e($email['to_name']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-slate-800 dark:text-white max-w-xs truncate" title="<?= e($email['subject']) ?>">
                            <?= e($email['subject']) ?>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <?php if ($email['customer_id']): ?>
                        <a href="/admin/customer-detail.php?id=<?= $email['customer_id'] ?>" class="text-sm text-primary-600 hover:underline">
                            <?= e($email['company_name'] ?? 'Kunde #' . $email['customer_id']) ?>
                        </a>
                        <?php else: ?>
                        <span class="text-sm text-slate-400">System</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-sm <?= $email['attempts'] >= $email['max_attempts'] ? 'text-red-600 font-bold' : 'text-slate-600 dark:text-slate-300' ?>">
                            <?= $email['attempts'] ?>/<?= $email['max_attempts'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="text-sm text-slate-800 dark:text-white"><?= date('d.m.Y H:i', strtotime($email['created_at'])) ?></div>
                        <?php if ($email['sent_at']): ?>
                        <div class="text-xs text-green-500">
                            <i class="fas fa-check mr-1"></i>Gesendet: <?= date('H:i', strtotime($email['sent_at'])) ?>
                        </div>
                        <?php elseif ($email['failed_at']): ?>
                        <div class="text-xs text-red-500">
                            <i class="fas fa-times mr-1"></i>Fehlgeschlagen: <?= date('H:i', strtotime($email['failed_at'])) ?>
                        </div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <!-- Details anzeigen -->
                            <button onclick="showEmailDetails(<?= htmlspecialchars(json_encode($email), ENT_QUOTES) ?>)"
                                    class="p-2 text-slate-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-all" 
                                    title="Details anzeigen">
                                <i class="fas fa-eye"></i>
                            </button>
                            
                            <?php if (in_array($email['status'], ['failed', 'bounced'])): ?>
                            <!-- Erneut senden -->
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="resend">
                                <input type="hidden" name="email_id" value="<?= $email['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-lg transition-all" 
                                        title="Erneut senden">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if ($email['status'] === 'pending'): ?>
                            <!-- Überspringen -->
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="skip">
                                <input type="hidden" name="email_id" value="<?= $email['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition-all" 
                                        title="Überspringen">
                                    <i class="fas fa-forward"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <!-- Löschen -->
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="email_id" value="<?= $email['id'] ?>">
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" 
                                        title="Löschen"
                                        onclick="return confirm('E-Mail wirklich löschen?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($emails)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p>Keine E-Mails gefunden</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Zeige <?= ($offset + 1) ?> - <?= min($offset + $perPage, $totalCount) ?> von <?= $totalCount ?>
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

<!-- Email Details Modal -->
<div id="emailModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-envelope-open text-primary-500 mr-2"></i>E-Mail Details
            </h3>
            <button onclick="closeEmailModal()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="emailModalContent" class="p-6"></div>
    </div>
</div>

<!-- Cleanup Modal -->
<div id="cleanupModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-md w-full mx-4">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-broom text-amber-500 mr-2"></i>E-Mail Queue aufräumen
            </h3>
        </div>
        <form method="POST" class="p-6">
            <input type="hidden" name="action" value="delete_old">
            <p class="text-slate-600 dark:text-slate-400 mb-4">
                Alte gesendete E-Mails löschen, um Speicherplatz freizugeben.
            </p>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">E-Mails löschen älter als</label>
                <select name="days" class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700">
                    <option value="7">7 Tage</option>
                    <option value="14">14 Tage</option>
                    <option value="30" selected>30 Tage</option>
                    <option value="60">60 Tage</option>
                    <option value="90">90 Tage</option>
                </select>
            </div>
            <div class="flex items-center justify-end gap-3">
                <button type="button" onclick="document.getElementById('cleanupModal').classList.add('hidden')" 
                        class="px-4 py-2 text-slate-600 hover:text-slate-800">
                    Abbrechen
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg">
                    <i class="fas fa-trash mr-2"></i>Löschen
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Email Chart
const ctx = document.getElementById('emailChart').getContext('2d');
const isDark = document.documentElement.classList.contains('dark');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [
            {
                label: 'Gesendet',
                data: <?= json_encode($chartSent) ?>,
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderRadius: 4
            },
            {
                label: 'Fehlgeschlagen',
                data: <?= json_encode($chartFailed) ?>,
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { 
                position: 'bottom',
                labels: { color: isDark ? '#94a3b8' : '#64748b' }
            }
        },
        scales: {
            x: { 
                grid: { display: false }, 
                ticks: { color: isDark ? '#94a3b8' : '#64748b' },
                stacked: true
            },
            y: { 
                beginAtZero: true, 
                grid: { color: isDark ? '#334155' : '#e2e8f0' }, 
                ticks: { color: isDark ? '#94a3b8' : '#64748b' },
                stacked: true
            }
        }
    }
});

// Email Details Modal
function showEmailDetails(email) {
    const modal = document.getElementById('emailModal');
    const content = document.getElementById('emailModalContent');
    
    const statusColors = {
        pending: 'amber', sending: 'blue', sent: 'green', 
        failed: 'red', bounced: 'orange', skipped: 'slate'
    };
    const color = statusColors[email.status] || 'slate';
    
    content.innerHTML = `
        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-slate-500">Status</p>
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-sm font-medium rounded-full bg-${color}-100 text-${color}-700">
                        ${email.status}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Versuche</p>
                    <p class="font-medium text-slate-800 dark:text-white">${email.attempts} / ${email.max_attempts}</p>
                </div>
            </div>
            
            <div>
                <p class="text-sm text-slate-500">Empfänger</p>
                <p class="font-medium text-slate-800 dark:text-white">${email.to_name ? email.to_name + ' &lt;' + email.to_email + '&gt;' : email.to_email}</p>
            </div>
            
            <div>
                <p class="text-sm text-slate-500">Von</p>
                <p class="font-medium text-slate-800 dark:text-white">${email.from_name ? email.from_name + ' &lt;' + email.from_email + '&gt;' : email.from_email}</p>
            </div>
            
            <div>
                <p class="text-sm text-slate-500">Betreff</p>
                <p class="font-medium text-slate-800 dark:text-white">${email.subject}</p>
            </div>
            
            <div>
                <p class="text-sm text-slate-500 mb-2">Inhalt (HTML)</p>
                <div class="border border-slate-200 dark:border-slate-700 rounded-lg p-4 bg-white max-h-64 overflow-y-auto">
                    ${email.body_html}
                </div>
            </div>
            
            ${email.mailgun_message_id ? `
            <div>
                <p class="text-sm text-slate-500">Mailgun Message ID</p>
                <code class="text-xs bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">${email.mailgun_message_id}</code>
            </div>
            ` : ''}
            
            ${email.mailgun_response ? `
            <div>
                <p class="text-sm text-slate-500">Mailgun Response</p>
                <pre class="text-xs bg-slate-100 dark:bg-slate-700 p-2 rounded overflow-x-auto">${email.mailgun_response}</pre>
            </div>
            ` : ''}
            
            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                <div>
                    <p class="text-sm text-slate-500">Erstellt</p>
                    <p class="text-sm text-slate-800 dark:text-white">${new Date(email.created_at).toLocaleString('de-DE')}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Geplant für</p>
                    <p class="text-sm text-slate-800 dark:text-white">${email.scheduled_for ? new Date(email.scheduled_for).toLocaleString('de-DE') : '-'}</p>
                </div>
                ${email.sent_at ? `
                <div>
                    <p class="text-sm text-slate-500">Gesendet</p>
                    <p class="text-sm text-green-600">${new Date(email.sent_at).toLocaleString('de-DE')}</p>
                </div>
                ` : ''}
                ${email.failed_at ? `
                <div>
                    <p class="text-sm text-slate-500">Fehlgeschlagen</p>
                    <p class="text-sm text-red-600">${new Date(email.failed_at).toLocaleString('de-DE')}</p>
                </div>
                ` : ''}
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeEmailModal() {
    document.getElementById('emailModal').classList.add('hidden');
    document.getElementById('emailModal').classList.remove('flex');
}

document.getElementById('emailModal').addEventListener('click', function(e) {
    if (e.target === this) closeEmailModal();
});

document.getElementById('cleanupModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.add('hidden');
});
</script>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
