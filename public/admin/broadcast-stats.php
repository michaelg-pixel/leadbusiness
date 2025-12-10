<?php
/**
 * Admin Broadcast Statistiken
 * Leadbusiness - Detaillierte Analyse pro Broadcast
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'Broadcast Statistiken';

$broadcastId = intval($_GET['id'] ?? 0);

if (!$broadcastId) {
    header('Location: /admin/broadcasts.php');
    exit;
}

// Broadcast laden
$broadcast = $db->fetch("SELECT * FROM admin_broadcasts WHERE id = ?", [$broadcastId]);

if (!$broadcast) {
    $_SESSION['flash_error'] = 'Broadcast nicht gefunden.';
    header('Location: /admin/broadcasts.php');
    exit;
}

// Aktionen verarbeiten
if (isPost()) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'resend_failed') {
        // Fehlgeschlagene E-Mails erneut versuchen
        $db->execute("
            UPDATE admin_broadcast_recipients 
            SET status = 'pending', error_message = NULL, failed_at = NULL 
            WHERE broadcast_id = ? AND status IN ('failed', 'bounced')
        ", [$broadcastId]);
        
        $_SESSION['flash_success'] = 'Fehlgeschlagene E-Mails werden erneut versendet.';
        header("Location: /admin/broadcast-stats.php?id=$broadcastId");
        exit;
    }
    
    if ($action === 'export_csv') {
        // CSV Export
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="broadcast-' . $broadcastId . '-recipients.csv"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM für Excel
        
        fputcsv($output, ['E-Mail', 'Status', 'Gesendet', 'Geöffnet', 'Geklickt', 'Öffnungen', 'Klicks'], ';');
        
        $recipients = $db->fetchAll("
            SELECT email, status, sent_at, opened_at, clicked_at, opened_count, clicked_count
            FROM admin_broadcast_recipients 
            WHERE broadcast_id = ?
            ORDER BY sent_at DESC
        ", [$broadcastId]);
        
        foreach ($recipients as $r) {
            fputcsv($output, [
                $r['email'],
                $r['status'],
                $r['sent_at'] ? date('d.m.Y H:i', strtotime($r['sent_at'])) : '-',
                $r['opened_at'] ? date('d.m.Y H:i', strtotime($r['opened_at'])) : '-',
                $r['clicked_at'] ? date('d.m.Y H:i', strtotime($r['clicked_at'])) : '-',
                $r['opened_count'],
                $r['clicked_count']
            ], ';');
        }
        
        fclose($output);
        exit;
    }
}

// Statistiken berechnen
$stats = $db->fetch("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
        SUM(CASE WHEN status = 'opened' THEN 1 ELSE 0 END) as opened,
        SUM(CASE WHEN status = 'clicked' THEN 1 ELSE 0 END) as clicked,
        SUM(CASE WHEN status = 'bounced' THEN 1 ELSE 0 END) as bounced,
        SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
        SUM(CASE WHEN status = 'unsubscribed' THEN 1 ELSE 0 END) as unsubscribed,
        SUM(opened_count) as total_opens,
        SUM(clicked_count) as total_clicks
    FROM admin_broadcast_recipients 
    WHERE broadcast_id = ?
", [$broadcastId]);

// Raten berechnen
$deliveredTotal = $stats['delivered'] + $stats['opened'] + $stats['clicked'];
$openedTotal = $stats['opened'] + $stats['clicked'];
$rates = [
    'delivery' => $stats['sent'] > 0 ? round(($deliveredTotal / $stats['sent']) * 100, 1) : 0,
    'open' => $deliveredTotal > 0 ? round(($openedTotal / $deliveredTotal) * 100, 1) : 0,
    'click' => $openedTotal > 0 ? round(($stats['clicked'] / $openedTotal) * 100, 1) : 0,
    'bounce' => $stats['sent'] > 0 ? round(($stats['bounced'] / $stats['sent']) * 100, 1) : 0,
];

// Zeitlicher Verlauf (Öffnungen pro Stunde in den ersten 48h)
$openingTrend = $db->fetchAll("
    SELECT 
        DATE_FORMAT(opened_at, '%Y-%m-%d %H:00:00') as hour,
        COUNT(*) as opens
    FROM admin_broadcast_recipients 
    WHERE broadcast_id = ? AND opened_at IS NOT NULL
    GROUP BY hour
    ORDER BY hour
    LIMIT 48
", [$broadcastId]);

// Top geklickte Links
$clickedLinks = [];
$recipients = $db->fetchAll("
    SELECT clicked_links FROM admin_broadcast_recipients 
    WHERE broadcast_id = ? AND clicked_links IS NOT NULL
", [$broadcastId]);

foreach ($recipients as $r) {
    $links = json_decode($r['clicked_links'], true) ?? [];
    foreach ($links as $url => $count) {
        if (!isset($clickedLinks[$url])) {
            $clickedLinks[$url] = 0;
        }
        $clickedLinks[$url] += $count;
    }
}
arsort($clickedLinks);
$clickedLinks = array_slice($clickedLinks, 0, 10, true);

// Empfänger-Liste (paginiert)
$filter = sanitize($_GET['filter'] ?? 'all');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 50;

$whereRecipient = ['broadcast_id = ?'];
$paramsRecipient = [$broadcastId];

if ($filter !== 'all') {
    $whereRecipient[] = 'status = ?';
    $paramsRecipient[] = $filter;
}

$whereClause = implode(' AND ', $whereRecipient);

$totalRecipients = $db->fetchColumn("SELECT COUNT(*) FROM admin_broadcast_recipients WHERE $whereClause", $paramsRecipient);
$totalPages = ceil($totalRecipients / $perPage);
$offset = ($page - 1) * $perPage;

$recipients = $db->fetchAll("
    SELECT r.*, c.company_name, c.contact_name
    FROM admin_broadcast_recipients r
    LEFT JOIN customers c ON r.customer_id = c.id
    WHERE r.$whereClause
    ORDER BY r.sent_at DESC, r.id DESC
    LIMIT $perPage OFFSET $offset
", $paramsRecipient);

// Status Config
$statusConfig = [
    'pending' => ['label' => 'Wartend', 'color' => 'slate', 'icon' => 'fa-clock'],
    'sent' => ['label' => 'Gesendet', 'color' => 'blue', 'icon' => 'fa-paper-plane'],
    'delivered' => ['label' => 'Zugestellt', 'color' => 'cyan', 'icon' => 'fa-check'],
    'opened' => ['label' => 'Geöffnet', 'color' => 'green', 'icon' => 'fa-envelope-open'],
    'clicked' => ['label' => 'Geklickt', 'color' => 'emerald', 'icon' => 'fa-mouse-pointer'],
    'bounced' => ['label' => 'Bounced', 'color' => 'orange', 'icon' => 'fa-exclamation-triangle'],
    'failed' => ['label' => 'Fehlgeschlagen', 'color' => 'red', 'icon' => 'fa-times-circle'],
    'unsubscribed' => ['label' => 'Abgemeldet', 'color' => 'purple', 'icon' => 'fa-user-minus']
];

$broadcastStatusConfig = [
    'draft' => ['label' => 'Entwurf', 'color' => 'slate'],
    'scheduled' => ['label' => 'Geplant', 'color' => 'blue'],
    'sending' => ['label' => 'Wird gesendet', 'color' => 'amber'],
    'sent' => ['label' => 'Gesendet', 'color' => 'green'],
    'paused' => ['label' => 'Pausiert', 'color' => 'orange'],
    'cancelled' => ['label' => 'Abgebrochen', 'color' => 'red']
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- Flash Messages -->
<?php if (isset($_SESSION['flash_success'])): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i><?= e($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<!-- Breadcrumb -->
<div class="mb-6">
    <a href="/admin/broadcasts.php" class="text-slate-500 hover:text-primary-600">
        <i class="fas fa-arrow-left mr-2"></i>Zurück zu Broadcasts
    </a>
</div>

<!-- Header -->
<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">
            <?= e($broadcast['name']) ?>
        </h1>
        <p class="text-slate-500"><?= e($broadcast['subject']) ?></p>
        <div class="flex items-center gap-4 mt-2 text-sm text-slate-500">
            <?php $bsc = $broadcastStatusConfig[$broadcast['status']] ?? ['label' => $broadcast['status'], 'color' => 'slate']; ?>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-<?= $bsc['color'] ?>-100 text-<?= $bsc['color'] ?>-700">
                <?= $bsc['label'] ?>
            </span>
            <span><i class="fas fa-calendar mr-1"></i><?= date('d.m.Y H:i', strtotime($broadcast['created_at'])) ?></span>
            <?php if ($broadcast['started_at']): ?>
            <span><i class="fas fa-play mr-1"></i>Gestartet: <?= date('d.m.Y H:i', strtotime($broadcast['started_at'])) ?></span>
            <?php endif; ?>
            <?php if ($broadcast['completed_at']): ?>
            <span><i class="fas fa-check mr-1"></i>Abgeschlossen: <?= date('d.m.Y H:i', strtotime($broadcast['completed_at'])) ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <?php if ($stats['failed'] > 0 || $stats['bounced'] > 0): ?>
        <form method="POST" class="inline">
            <input type="hidden" name="action" value="resend_failed">
            <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-all" onclick="return confirm('Fehlgeschlagene E-Mails erneut senden?')">
                <i class="fas fa-redo mr-2"></i>Fehlgeschlagene erneut senden
            </button>
        </form>
        <?php endif; ?>
        <form method="POST" class="inline">
            <input type="hidden" name="action" value="export_csv">
            <button type="submit" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-white rounded-lg transition-all">
                <i class="fas fa-download mr-2"></i>CSV Export
            </button>
        </form>
    </div>
</div>

<!-- Haupt-Statistiken -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['total'], 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500">Empfänger</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-2xl font-bold text-blue-600"><?= number_format($broadcast['sent_count'], 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500">Gesendet</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-2xl font-bold text-cyan-600"><?= number_format($deliveredTotal, 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500">Zugestellt</div>
        <div class="text-xs text-cyan-500"><?= $rates['delivery'] ?>%</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-2xl font-bold text-green-600"><?= number_format($openedTotal, 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500">Geöffnet</div>
        <div class="text-xs text-green-500"><?= $rates['open'] ?>%</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-2xl font-bold text-emerald-600"><?= number_format($stats['clicked'], 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500">Geklickt</div>
        <div class="text-xs text-emerald-500"><?= $rates['click'] ?>%</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-2xl font-bold text-orange-600"><?= number_format($stats['bounced'], 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500">Bounced</div>
        <div class="text-xs text-orange-500"><?= $rates['bounce'] ?>%</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-2xl font-bold text-red-600"><?= number_format($stats['failed'], 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500">Fehlgeschlagen</div>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="text-2xl font-bold text-purple-600"><?= number_format($stats['unsubscribed'], 0, ',', '.') ?></div>
        <div class="text-sm text-slate-500">Abgemeldet</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Öffnungs-Trend Chart -->
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-chart-line text-primary-500 mr-2"></i>Öffnungs-Verlauf
        </h3>
        <?php if (!empty($openingTrend)): ?>
        <canvas id="openingChart" height="200"></canvas>
        <?php else: ?>
        <div class="text-center text-slate-500 py-8">
            <i class="fas fa-chart-line text-4xl mb-2 opacity-30"></i>
            <p>Noch keine Öffnungsdaten</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Top Links -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-link text-blue-500 mr-2"></i>Top geklickte Links
        </h3>
        <?php if (!empty($clickedLinks)): ?>
        <div class="space-y-3">
            <?php foreach ($clickedLinks as $url => $clicks): ?>
            <div class="flex items-center justify-between gap-2">
                <a href="<?= e($url) ?>" target="_blank" class="text-sm text-primary-600 hover:underline truncate max-w-[200px]" title="<?= e($url) ?>">
                    <?= e(strlen($url) > 40 ? substr($url, 0, 40) . '...' : $url) ?>
                </a>
                <span class="text-sm font-medium text-slate-600 dark:text-slate-400"><?= $clicks ?> Klicks</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center text-slate-500 py-4">
            <i class="fas fa-link text-2xl mb-2 opacity-30"></i>
            <p>Noch keine Klicks</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Empfänger-Tabelle -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white">
            <i class="fas fa-users text-green-500 mr-2"></i>Empfänger
        </h3>
        
        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2">
            <a href="?id=<?= $broadcastId ?>&filter=all" class="px-3 py-1.5 text-sm rounded-lg <?= $filter === 'all' ? 'bg-primary-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' ?>">
                Alle (<?= $stats['total'] ?>)
            </a>
            <a href="?id=<?= $broadcastId ?>&filter=pending" class="px-3 py-1.5 text-sm rounded-lg <?= $filter === 'pending' ? 'bg-slate-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' ?>">
                Wartend (<?= $stats['pending'] ?>)
            </a>
            <a href="?id=<?= $broadcastId ?>&filter=opened" class="px-3 py-1.5 text-sm rounded-lg <?= $filter === 'opened' ? 'bg-green-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' ?>">
                Geöffnet (<?= $stats['opened'] ?>)
            </a>
            <a href="?id=<?= $broadcastId ?>&filter=clicked" class="px-3 py-1.5 text-sm rounded-lg <?= $filter === 'clicked' ? 'bg-emerald-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' ?>">
                Geklickt (<?= $stats['clicked'] ?>)
            </a>
            <a href="?id=<?= $broadcastId ?>&filter=bounced" class="px-3 py-1.5 text-sm rounded-lg <?= $filter === 'bounced' ? 'bg-orange-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' ?>">
                Bounced (<?= $stats['bounced'] ?>)
            </a>
            <a href="?id=<?= $broadcastId ?>&filter=failed" class="px-3 py-1.5 text-sm rounded-lg <?= $filter === 'failed' ? 'bg-red-600 text-white' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200' ?>">
                Fehlgeschlagen (<?= $stats['failed'] ?>)
            </a>
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Empfänger</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Gesendet</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Geöffnet</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Geklickt</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Details</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($recipients as $r): ?>
                <?php $sc = $statusConfig[$r['status']] ?? ['label' => $r['status'], 'color' => 'slate', 'icon' => 'fa-question']; ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3">
                        <div class="font-medium text-slate-800 dark:text-white"><?= e($r['email']) ?></div>
                        <?php if ($r['company_name']): ?>
                        <div class="text-sm text-slate-500"><?= e($r['company_name']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-<?= $sc['color'] ?>-100 text-<?= $sc['color'] ?>-700 dark:bg-<?= $sc['color'] ?>-900/30 dark:text-<?= $sc['color'] ?>-300">
                            <i class="fas <?= $sc['icon'] ?>"></i>
                            <?= $sc['label'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center text-sm text-slate-600 dark:text-slate-400">
                        <?= $r['sent_at'] ? date('d.m. H:i', strtotime($r['sent_at'])) : '-' ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($r['opened_at']): ?>
                        <span class="text-sm text-green-600"><?= date('d.m. H:i', strtotime($r['opened_at'])) ?></span>
                        <?php if ($r['opened_count'] > 1): ?>
                        <span class="text-xs text-slate-500 block"><?= $r['opened_count'] ?>x</span>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <?php if ($r['clicked_at']): ?>
                        <span class="text-sm text-emerald-600"><?= date('d.m. H:i', strtotime($r['clicked_at'])) ?></span>
                        <?php if ($r['clicked_count'] > 1): ?>
                        <span class="text-xs text-slate-500 block"><?= $r['clicked_count'] ?>x</span>
                        <?php endif; ?>
                        <?php else: ?>
                        <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-500">
                        <?php if ($r['bounce_reason']): ?>
                        <span class="text-orange-600" title="<?= e($r['bounce_reason']) ?>">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            <?= e(substr($r['bounce_reason'], 0, 50)) ?>...
                        </span>
                        <?php elseif ($r['error_message']): ?>
                        <span class="text-red-600" title="<?= e($r['error_message']) ?>">
                            <i class="fas fa-times-circle mr-1"></i>
                            <?= e(substr($r['error_message'], 0, 50)) ?>...
                        </span>
                        <?php elseif ($r['mailgun_message_id']): ?>
                        <span class="text-xs text-slate-400 font-mono"><?= e(substr($r['mailgun_message_id'], 0, 20)) ?>...</span>
                        <?php else: ?>
                        -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                
                <?php if (empty($recipients)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                        Keine Empfänger gefunden
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Seite <?= $page ?> von <?= $totalPages ?> (<?= number_format($totalRecipients, 0, ',', '.') ?> Empfänger)
        </p>
        <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>" class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>" class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- E-Mail Vorschau Modal -->
<div id="previewModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold">E-Mail Vorschau</h3>
            <button onclick="closePreview()" class="text-slate-400 hover:text-slate-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-4 overflow-y-auto max-h-[70vh]">
            <div class="mb-4">
                <span class="text-sm text-slate-500">Betreff:</span>
                <p class="font-medium"><?= e($broadcast['subject']) ?></p>
            </div>
            <div class="border border-slate-200 dark:border-slate-700 rounded-lg overflow-hidden">
                <iframe id="previewFrame" class="w-full h-[500px] bg-white"></iframe>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($openingTrend)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('openingChart').getContext('2d');
    
    const data = <?= json_encode($openingTrend) ?>;
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.map(d => {
                const date = new Date(d.hour);
                return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' }) + ' ' + 
                       date.toLocaleTimeString('de-DE', { hour: '2-digit', minute: '2-digit' });
            }),
            datasets: [{
                label: 'Öffnungen',
                data: data.map(d => d.opens),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
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
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    ticks: {
                        maxTicksLimit: 12
                    }
                }
            }
        }
    });
});

function showPreview() {
    document.getElementById('previewModal').classList.remove('hidden');
    const frame = document.getElementById('previewFrame');
    frame.srcdoc = <?= json_encode($broadcast['body_html']) ?>;
}

function closePreview() {
    document.getElementById('previewModal').classList.add('hidden');
}
</script>
<?php endif; ?>

<!-- Vorschau Button -->
<div class="fixed bottom-6 right-6">
    <button onclick="showPreview()" class="px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-full shadow-lg transition-all">
        <i class="fas fa-eye mr-2"></i>E-Mail Vorschau
    </button>
</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
