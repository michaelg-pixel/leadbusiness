<?php
/**
 * Admin System Logs
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'System Logs';

// Filter
$logType = sanitize($_GET['type'] ?? 'all');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 50;

// Verfügbare Log-Typen
$logTypes = [
    'all' => 'Alle Logs',
    'email' => 'E-Mail-Queue',
    'fraud' => 'Fraud Detection',
    'bot' => 'Bot Detection',
    'rate_limit' => 'Rate Limits',
    'blocked_ips' => 'Gesperrte IPs'
];

// Daten laden basierend auf Typ
$logs = [];
$totalCount = 0;
$offset = ($page - 1) * $perPage;

switch ($logType) {
    case 'email':
        $totalCount = $db->fetchColumn("SELECT COUNT(*) FROM email_queue");
        $logs = $db->fetchAll("
            SELECT eq.*, c.company_name 
            FROM email_queue eq
            LEFT JOIN customers c ON eq.customer_id = c.id
            ORDER BY eq.created_at DESC
            LIMIT ? OFFSET ?", [$perPage, $offset]
        );
        break;
        
    case 'fraud':
        $totalCount = $db->fetchColumn("SELECT COUNT(*) FROM fraud_log");
        $logs = $db->fetchAll("
            SELECT fl.*, c.company_name, l.email as lead_email
            FROM fraud_log fl
            LEFT JOIN customers c ON fl.customer_id = c.id
            LEFT JOIN leads l ON fl.lead_id = l.id
            ORDER BY fl.created_at DESC
            LIMIT ? OFFSET ?", [$perPage, $offset]
        );
        break;
        
    case 'bot':
        $totalCount = $db->fetchColumn("SELECT COUNT(*) FROM bot_detection_log");
        $logs = $db->fetchAll("
            SELECT * FROM bot_detection_log
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?", [$perPage, $offset]
        );
        break;
        
    case 'rate_limit':
        $totalCount = $db->fetchColumn("SELECT COUNT(*) FROM rate_limit_log");
        $logs = $db->fetchAll("
            SELECT * FROM rate_limit_log
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?", [$perPage, $offset]
        );
        break;
        
    case 'blocked_ips':
        $totalCount = $db->fetchColumn("SELECT COUNT(*) FROM blocked_ips");
        $logs = $db->fetchAll("
            SELECT * FROM blocked_ips
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?", [$perPage, $offset]
        );
        break;
        
    default:
        // Alle Logs kombiniert (letzte Aktivitäten)
        $logs = $db->fetchAll("
            (SELECT 'email' as log_type, id, to_email as title, status, created_at FROM email_queue ORDER BY created_at DESC LIMIT 20)
            UNION ALL
            (SELECT 'fraud' as log_type, id, fraud_type as title, action_taken as status, created_at FROM fraud_log ORDER BY created_at DESC LIMIT 20)
            UNION ALL
            (SELECT 'bot' as log_type, id, CONCAT('Score: ', score) as title, action_taken as status, created_at FROM bot_detection_log ORDER BY created_at DESC LIMIT 20)
            ORDER BY created_at DESC
            LIMIT 50
        ");
        $totalCount = count($logs);
}

$totalPages = ceil($totalCount / $perPage);

// Statistiken
$stats = [
    'emails_pending' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'pending'") ?? 0,
    'emails_sent' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'sent'") ?? 0,
    'emails_failed' => $db->fetchColumn("SELECT COUNT(*) FROM email_queue WHERE status = 'failed'") ?? 0,
    'fraud_pending' => $db->fetchColumn("SELECT COUNT(*) FROM fraud_log WHERE action_taken = 'review' AND reviewed_at IS NULL") ?? 0,
    'bots_blocked' => $db->fetchColumn("SELECT COUNT(*) FROM bot_detection_log WHERE action_taken = 'blocked'") ?? 0,
    'ips_blocked' => $db->fetchColumn("SELECT COUNT(*) FROM blocked_ips") ?? 0,
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-amber-600"><?= $stats['emails_pending'] ?></p>
        <p class="text-xs text-slate-500">E-Mails wartend</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-green-600"><?= number_format($stats['emails_sent'], 0, ',', '.') ?></p>
        <p class="text-xs text-slate-500">E-Mails gesendet</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-red-600"><?= $stats['emails_failed'] ?></p>
        <p class="text-xs text-slate-500">E-Mails fehlgeschlagen</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-amber-600"><?= $stats['fraud_pending'] ?></p>
        <p class="text-xs text-slate-500">Fraud offen</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-purple-600"><?= $stats['bots_blocked'] ?></p>
        <p class="text-xs text-slate-500">Bots blockiert</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-red-600"><?= $stats['ips_blocked'] ?></p>
        <p class="text-xs text-slate-500">IPs gesperrt</p>
    </div>
</div>

<!-- Filter Tabs -->
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 mb-6">
    <div class="flex flex-wrap border-b border-slate-200 dark:border-slate-700">
        <?php foreach ($logTypes as $key => $label): ?>
        <a href="?type=<?= $key ?>" 
           class="px-6 py-3 text-sm font-medium transition-all border-b-2 -mb-px
                  <?= $logType === $key 
                      ? 'border-primary-600 text-primary-600' 
                      : 'border-transparent text-slate-500 hover:text-slate-700 dark:hover:text-slate-300' ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Log Table -->
    <div class="overflow-x-auto">
        <?php if ($logType === 'email'): ?>
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">An</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Betreff</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Kunde</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Erstellt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3 text-sm text-slate-800 dark:text-white"><?= e($log['to_email']) ?></td>
                    <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300 max-w-xs truncate"><?= e($log['subject']) ?></td>
                    <td class="px-4 py-3 text-sm text-slate-500"><?= e($log['company_name'] ?? '-') ?></td>
                    <td class="px-4 py-3">
                        <?php
                        $statusColors = [
                            'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                            'sent' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                            'failed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'
                        ];
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $statusColors[$log['status']] ?? '' ?>">
                            <?= ucfirst($log['status']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-500"><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php elseif ($logType === 'fraud'): ?>
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Typ</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Score</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Lead</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Kunde</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Aktion</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Zeit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3 text-sm text-slate-800 dark:text-white"><?= e($log['fraud_type']) ?></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-bold rounded-full 
                                     <?php if ($log['score'] >= 80): ?>bg-red-100 text-red-700
                                     <?php elseif ($log['score'] >= 50): ?>bg-orange-100 text-orange-700
                                     <?php else: ?>bg-amber-100 text-amber-700<?php endif; ?>">
                            <?= $log['score'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300"><?= e($log['lead_email'] ?? '-') ?></td>
                    <td class="px-4 py-3 text-sm text-slate-500"><?= e($log['company_name'] ?? '-') ?></td>
                    <td class="px-4 py-3">
                        <?php
                        $actionColors = [
                            'allowed' => 'bg-green-100 text-green-700',
                            'blocked' => 'bg-red-100 text-red-700',
                            'review' => 'bg-amber-100 text-amber-700',
                            'flagged' => 'bg-slate-100 text-slate-700'
                        ];
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $actionColors[$log['action_taken']] ?? '' ?>">
                            <?= ucfirst($log['action_taken']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-500"><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php elseif ($logType === 'bot'): ?>
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">IP Hash</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Score</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Gründe</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Aktion</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Zeit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3 text-sm text-slate-800 dark:text-white font-mono text-xs"><?= e(substr($log['ip_hash'] ?? '', 0, 16)) ?>...</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs font-bold rounded-full 
                                     <?php if ($log['score'] >= 80): ?>bg-red-100 text-red-700
                                     <?php elseif ($log['score'] >= 50): ?>bg-orange-100 text-orange-700
                                     <?php else: ?>bg-green-100 text-green-700<?php endif; ?>">
                            <?= $log['score'] ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300 max-w-xs truncate">
                        <?php 
                        $reasons = json_decode($log['reasons'] ?? '[]', true);
                        echo e(implode(', ', $reasons ?: []));
                        ?>
                    </td>
                    <td class="px-4 py-3">
                        <?php
                        $actionColors = [
                            'allowed' => 'bg-green-100 text-green-700',
                            'blocked' => 'bg-red-100 text-red-700',
                            'captcha_shown' => 'bg-amber-100 text-amber-700'
                        ];
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $actionColors[$log['action_taken']] ?? '' ?>">
                            <?= ucfirst(str_replace('_', ' ', $log['action_taken'])) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-500"><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php elseif ($logType === 'blocked_ips'): ?>
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">IP Hash</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Grund</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Sperren</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Gesperrt bis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Erstellt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3 text-sm text-slate-800 dark:text-white font-mono text-xs"><?= e(substr($log['ip_hash'], 0, 16)) ?>...</td>
                    <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300"><?= ucfirst($log['reason']) ?></td>
                    <td class="px-4 py-3 text-sm text-slate-500"><?= $log['block_count'] ?>×</td>
                    <td class="px-4 py-3 text-sm text-slate-500">
                        <?= $log['blocked_until'] ? date('d.m.Y H:i', strtotime($log['blocked_until'])) : 'Permanent' ?>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-500"><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php else: ?>
        <table class="w-full">
            <thead class="bg-slate-50 dark:bg-slate-700/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Typ</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Details</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-600 dark:text-slate-300 uppercase">Zeit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                    <td class="px-4 py-3">
                        <?php
                        $typeColors = [
                            'email' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'fraud' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                            'bot' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300'
                        ];
                        $typeIcons = ['email' => 'fa-envelope', 'fraud' => 'fa-shield', 'bot' => 'fa-robot'];
                        ?>
                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $typeColors[$log['log_type']] ?? '' ?>">
                            <i class="fas <?= $typeIcons[$log['log_type']] ?? 'fa-circle' ?> mr-1"></i>
                            <?= ucfirst($log['log_type']) ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-sm text-slate-800 dark:text-white"><?= e($log['title']) ?></td>
                    <td class="px-4 py-3 text-sm text-slate-600 dark:text-slate-300"><?= e($log['status']) ?></td>
                    <td class="px-4 py-3 text-sm text-slate-500"><?= date('d.m.Y H:i', strtotime($log['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    
    <?php if ($totalPages > 1 && $logType !== 'all'): ?>
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <p class="text-sm text-slate-500">Seite <?= $page ?> von <?= $totalPages ?></p>
        <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
            <a href="?type=<?= $logType ?>&page=<?= $page - 1 ?>" class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?type=<?= $logType ?>&page=<?= $page + 1 ?>" class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (empty($logs)): ?>
    <div class="p-12 text-center text-slate-500">
        <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
        <p>Keine Logs gefunden</p>
    </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
