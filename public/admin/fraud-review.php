<?php
/**
 * Admin Fraud Review
 * Leadbusiness - Empfehlungsprogramm
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = Database::getInstance();
$pageTitle = 'Fraud Review';

// Aktionen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fraudId = intval($_POST['fraud_id'] ?? 0);
    $action = $_POST['action'] ?? '';
    $leadId = intval($_POST['lead_id'] ?? 0);
    
    if ($fraudId && in_array($action, ['approve', 'block', 'ignore'])) {
        // Fraud Log aktualisieren
        $newAction = match($action) {
            'approve' => 'allowed',
            'block' => 'blocked',
            'ignore' => 'flagged'
        };
        
        $db->execute(
            "UPDATE fraud_log SET action_taken = ?, reviewed_by = ?, reviewed_at = NOW() WHERE id = ?",
            [$newAction, $_SESSION['admin_id'], $fraudId]
        );
        
        // Lead-Status aktualisieren
        if ($leadId && $action === 'block') {
            $db->execute("UPDATE leads SET status = 'blocked' WHERE id = ?", [$leadId]);
        } elseif ($leadId && $action === 'approve') {
            $db->execute("UPDATE leads SET status = 'active', fraud_score = 0 WHERE id = ?", [$leadId]);
        }
        
        $_SESSION['flash_success'] = 'Fraud-Fall wurde bearbeitet.';
        header('Location: /admin/fraud-review.php');
        exit;
    }
}

// Filter
$filter = sanitize($_GET['filter'] ?? 'pending');
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 20;

// Query
$whereClause = match($filter) {
    'pending' => "WHERE action_taken = 'review' AND reviewed_at IS NULL",
    'approved' => "WHERE action_taken = 'allowed'",
    'blocked' => "WHERE action_taken = 'blocked'",
    'all' => "",
    default => "WHERE action_taken = 'review' AND reviewed_at IS NULL"
};

// Statistiken
$stats = [
    'pending' => $db->fetchColumn("SELECT COUNT(*) FROM fraud_log WHERE action_taken = 'review' AND reviewed_at IS NULL") ?? 0,
    'approved' => $db->fetchColumn("SELECT COUNT(*) FROM fraud_log WHERE action_taken = 'allowed'") ?? 0,
    'blocked' => $db->fetchColumn("SELECT COUNT(*) FROM fraud_log WHERE action_taken = 'blocked'") ?? 0,
    'total' => $db->fetchColumn("SELECT COUNT(*) FROM fraud_log") ?? 0,
];

// Fraud Cases laden
$totalCount = $db->fetchColumn("SELECT COUNT(*) FROM fraud_log $whereClause");
$totalPages = ceil($totalCount / $perPage);
$offset = ($page - 1) * $perPage;

$fraudCases = $db->fetchAll("
    SELECT f.*, 
           l.email as lead_email, l.name as lead_name, l.referral_code, l.status as lead_status,
           c.company_name, c.subdomain,
           r.email as referrer_email, r.name as referrer_name
    FROM fraud_log f
    LEFT JOIN leads l ON f.lead_id = l.id
    LEFT JOIN customers c ON f.customer_id = c.id
    LEFT JOIN leads r ON f.referrer_id = r.id
    $whereClause
    ORDER BY f.created_at DESC
    LIMIT $perPage OFFSET $offset
");

// Fraud Type Labels
$fraudTypeLabels = [
    'fast_conversion' => ['label' => 'Schnelle Conversion', 'icon' => 'fa-bolt', 'color' => 'amber'],
    'same_ip' => ['label' => 'Gleiche IP', 'icon' => 'fa-network-wired', 'color' => 'red'],
    'same_subnet' => ['label' => 'Gleiches Subnet', 'icon' => 'fa-sitemap', 'color' => 'orange'],
    'ip_abuse' => ['label' => 'IP Missbrauch', 'icon' => 'fa-ban', 'color' => 'red'],
    'self_referral' => ['label' => 'Selbst-Empfehlung', 'icon' => 'fa-user-xmark', 'color' => 'red'],
    'suspicious_email' => ['label' => 'Verdächtige E-Mail', 'icon' => 'fa-envelope-circle-check', 'color' => 'amber'],
    'same_fingerprint' => ['label' => 'Gleicher Fingerprint', 'icon' => 'fa-fingerprint', 'color' => 'red'],
    'referrer_limit' => ['label' => 'Referrer Limit', 'icon' => 'fa-gauge-high', 'color' => 'orange'],
    'vpn_detected' => ['label' => 'VPN erkannt', 'icon' => 'fa-shield-halved', 'color' => 'amber'],
    'disposable_email' => ['label' => 'Wegwerf-E-Mail', 'icon' => 'fa-trash', 'color' => 'red'],
    'bot_detected' => ['label' => 'Bot erkannt', 'icon' => 'fa-robot', 'color' => 'red'],
    'rate_limit' => ['label' => 'Rate Limit', 'icon' => 'fa-clock', 'color' => 'amber'],
    'manual_flag' => ['label' => 'Manuell markiert', 'icon' => 'fa-flag', 'color' => 'blue'],
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i>
    <?= e($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<!-- Stats -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <a href="?filter=pending" class="bg-white dark:bg-slate-800 rounded-xl p-4 border-2 <?= $filter === 'pending' ? 'border-amber-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-amber-500 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-amber-600"><?= $stats['pending'] ?></p>
                <p class="text-sm text-slate-500">Offen</p>
            </div>
            <i class="fas fa-clock text-amber-500 text-2xl"></i>
        </div>
    </a>
    <a href="?filter=approved" class="bg-white dark:bg-slate-800 rounded-xl p-4 border-2 <?= $filter === 'approved' ? 'border-green-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-green-500 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-green-600"><?= $stats['approved'] ?></p>
                <p class="text-sm text-slate-500">Freigegeben</p>
            </div>
            <i class="fas fa-check text-green-500 text-2xl"></i>
        </div>
    </a>
    <a href="?filter=blocked" class="bg-white dark:bg-slate-800 rounded-xl p-4 border-2 <?= $filter === 'blocked' ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-red-500 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-red-600"><?= $stats['blocked'] ?></p>
                <p class="text-sm text-slate-500">Blockiert</p>
            </div>
            <i class="fas fa-ban text-red-500 text-2xl"></i>
        </div>
    </a>
    <a href="?filter=all" class="bg-white dark:bg-slate-800 rounded-xl p-4 border-2 <?= $filter === 'all' ? 'border-primary-500' : 'border-slate-200 dark:border-slate-700' ?> hover:border-primary-500 transition-all">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['total'] ?></p>
                <p class="text-sm text-slate-500">Gesamt</p>
            </div>
            <i class="fas fa-list text-slate-500 text-2xl"></i>
        </div>
    </a>
</div>

<!-- Fraud Cases -->
<div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white">
            <i class="fas fa-shield-exclamation text-red-500 mr-2"></i>
            Fraud-Fälle
            <?php if ($filter !== 'all'): ?>
            <span class="text-sm font-normal text-slate-500">
                (<?= match($filter) { 'pending' => 'Offen', 'approved' => 'Freigegeben', 'blocked' => 'Blockiert', default => 'Alle' } ?>)
            </span>
            <?php endif; ?>
        </h3>
    </div>
    
    <?php if (empty($fraudCases)): ?>
    <div class="p-12 text-center">
        <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-check text-green-600 dark:text-green-400 text-2xl"></i>
        </div>
        <p class="text-slate-600 dark:text-slate-400 mb-2">Keine Fraud-Fälle gefunden</p>
        <p class="text-sm text-slate-500">Alles sieht gut aus! 🎉</p>
    </div>
    <?php else: ?>
    
    <div class="divide-y divide-slate-200 dark:divide-slate-700">
        <?php foreach ($fraudCases as $case): ?>
        <?php 
        $typeInfo = $fraudTypeLabels[$case['fraud_type']] ?? ['label' => $case['fraud_type'], 'icon' => 'fa-exclamation', 'color' => 'slate'];
        $details = json_decode($case['details'] ?? '{}', true);
        ?>
        <div class="p-4 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-all">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Left: Info -->
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <!-- Score Badge -->
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold 
                                     <?php if ($case['score'] >= 80): ?>bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300
                                     <?php elseif ($case['score'] >= 50): ?>bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300
                                     <?php else: ?>bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300<?php endif; ?>">
                            Score: <?= $case['score'] ?>
                        </span>
                        
                        <!-- Type Badge -->
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-<?= $typeInfo['color'] ?>-100 text-<?= $typeInfo['color'] ?>-700 dark:bg-<?= $typeInfo['color'] ?>-900/30 dark:text-<?= $typeInfo['color'] ?>-300">
                            <i class="fas <?= $typeInfo['icon'] ?> mr-1"></i>
                            <?= $typeInfo['label'] ?>
                        </span>
                        
                        <!-- Status -->
                        <?php if ($case['reviewed_at']): ?>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium 
                                     <?= $case['action_taken'] === 'allowed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' ?>">
                            <?= $case['action_taken'] === 'allowed' ? 'Freigegeben' : 'Blockiert' ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Lead Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-slate-500">Lead:</span>
                            <span class="text-slate-800 dark:text-white font-medium">
                                <?= e($case['lead_email'] ?? 'Unbekannt') ?>
                            </span>
                            <?php if ($case['lead_status'] === 'blocked'): ?>
                            <span class="text-red-500 text-xs">(blockiert)</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="text-slate-500">Referrer:</span>
                            <span class="text-slate-800 dark:text-white">
                                <?= e($case['referrer_email'] ?? '-') ?>
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-500">Kunde:</span>
                            <a href="/admin/customer-detail.php?id=<?= $case['customer_id'] ?>" class="text-primary-600 hover:underline">
                                <?= e($case['company_name'] ?? 'Unbekannt') ?>
                            </a>
                        </div>
                        <div>
                            <span class="text-slate-500">Zeit:</span>
                            <span class="text-slate-800 dark:text-white">
                                <?= date('d.m.Y H:i', strtotime($case['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <!-- Details -->
                    <?php if (!empty($details)): ?>
                    <div class="mt-2 text-xs text-slate-500">
                        <button onclick="this.nextElementSibling.classList.toggle('hidden')" class="hover:text-primary-600">
                            <i class="fas fa-chevron-down mr-1"></i>Details anzeigen
                        </button>
                        <pre class="hidden mt-2 p-2 bg-slate-100 dark:bg-slate-700 rounded text-xs overflow-x-auto"><?= e(json_encode($details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Right: Actions -->
                <?php if (!$case['reviewed_at']): ?>
                <div class="flex items-center gap-2">
                    <form method="POST" class="inline">
                        <input type="hidden" name="fraud_id" value="<?= $case['id'] ?>">
                        <input type="hidden" name="lead_id" value="<?= $case['lead_id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-all"
                                onclick="return confirm('Lead freigeben?')">
                            <i class="fas fa-check mr-1"></i>Freigeben
                        </button>
                    </form>
                    <form method="POST" class="inline">
                        <input type="hidden" name="fraud_id" value="<?= $case['id'] ?>">
                        <input type="hidden" name="lead_id" value="<?= $case['lead_id'] ?>">
                        <input type="hidden" name="action" value="block">
                        <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-all"
                                onclick="return confirm('Lead blockieren?')">
                            <i class="fas fa-ban mr-1"></i>Blockieren
                        </button>
                    </form>
                    <form method="POST" class="inline">
                        <input type="hidden" name="fraud_id" value="<?= $case['id'] ?>">
                        <input type="hidden" name="action" value="ignore">
                        <button type="submit" class="px-4 py-2 bg-slate-200 dark:bg-slate-600 hover:bg-slate-300 dark:hover:bg-slate-500 text-slate-700 dark:text-slate-200 rounded-lg text-sm transition-all">
                            <i class="fas fa-eye-slash mr-1"></i>Ignorieren
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between">
        <p class="text-sm text-slate-500">
            Seite <?= $page ?> von <?= $totalPages ?>
        </p>
        <div class="flex items-center gap-2">
            <?php if ($page > 1): ?>
            <a href="?filter=<?= $filter ?>&page=<?= $page - 1 ?>" class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            
            <?php if ($page < $totalPages): ?>
            <a href="?filter=<?= $filter ?>&page=<?= $page + 1 ?>" class="px-3 py-1 border border-slate-200 dark:border-slate-600 rounded-lg text-sm hover:bg-slate-50 dark:hover:bg-slate-700">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
