<?php
/**
 * Admin Kunden-Detailansicht
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
$customerId = intval($_GET['id'] ?? 0);

// Kunde laden
$customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);

if (!$customer) {
    header('Location: /admin/customers.php');
    exit;
}

$pageTitle = $customer['company_name'];

// Aktionen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'change_status':
            $newStatus = sanitize($_POST['status'] ?? '');
            if (in_array($newStatus, ['active', 'trial', 'cancelled', 'paused'])) {
                $db->execute("UPDATE customers SET subscription_status = ? WHERE id = ?", [$newStatus, $customerId]);
                $_SESSION['flash_success'] = 'Status wurde aktualisiert.';
            }
            break;
            
        case 'change_plan':
            $newPlan = sanitize($_POST['plan'] ?? '');
            if (in_array($newPlan, ['starter', 'professional', 'enterprise'])) {
                $db->execute("UPDATE customers SET plan = ? WHERE id = ?", [$newPlan, $customerId]);
                $_SESSION['flash_success'] = 'Plan wurde aktualisiert.';
            }
            break;
            
        case 'delete_customer':
            // Soft Delete: Nur Status ändern
            $db->execute("UPDATE customers SET subscription_status = 'cancelled' WHERE id = ?", [$customerId]);
            $_SESSION['flash_success'] = 'Kunde wurde deaktiviert.';
            header('Location: /admin/customers.php');
            exit;
    }
    
    // Refresh
    header("Location: /admin/customer-detail.php?id=$customerId");
    exit;
}

// Statistiken
$stats = [
    'leads' => $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE customer_id = ?", [$customerId]) ?? 0,
    'active_leads' => $db->fetchColumn("SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status = 'active'", [$customerId]) ?? 0,
    'conversions' => $db->fetchColumn("SELECT COUNT(*) FROM conversions WHERE customer_id = ? AND status = 'confirmed'", [$customerId]) ?? 0,
    'clicks' => $db->fetchColumn("SELECT COALESCE(SUM(clicks), 0) FROM leads WHERE customer_id = ?", [$customerId]) ?? 0,
    'rewards_sent' => $db->fetchColumn("SELECT COUNT(*) FROM reward_deliveries WHERE customer_id = ? AND status = 'sent'", [$customerId]) ?? 0,
    'campaigns' => $db->fetchColumn("SELECT COUNT(*) FROM campaigns WHERE customer_id = ?", [$customerId]) ?? 0,
];

// Conversion Rate
$stats['conversion_rate'] = $stats['clicks'] > 0 ? round(($stats['conversions'] / $stats['clicks']) * 100, 1) : 0;

// Top Leads
$topLeads = $db->fetchAll("
    SELECT * FROM leads 
    WHERE customer_id = ? 
    ORDER BY conversions DESC, clicks DESC 
    LIMIT 10
", [$customerId]);

// Belohnungsstufen
$rewards = $db->fetchAll("
    SELECT r.*, 
           (SELECT COUNT(*) FROM reward_deliveries WHERE reward_id = r.id) as delivery_count
    FROM rewards r
    WHERE r.customer_id = ?
    ORDER BY r.level
", [$customerId]);

// Letzte Aktivitäten
$recentActivities = $db->fetchAll("
    (SELECT 'lead' as type, email as title, created_at FROM leads WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5)
    UNION ALL
    (SELECT 'conversion' as type, CONCAT('Conversion für Lead #', lead_id) as title, created_at FROM conversions WHERE customer_id = ? ORDER BY created_at DESC LIMIT 5)
    ORDER BY created_at DESC
    LIMIT 10
", [$customerId, $customerId]);

// Zahlungen
$payments = $db->fetchAll("
    SELECT * FROM payments WHERE customer_id = ? ORDER BY created_at DESC LIMIT 10
", [$customerId]);

include __DIR__ . '/../../includes/admin-header.php';
?>

<!-- Breadcrumb -->
<div class="mb-6">
    <a href="/admin/customers.php" class="text-slate-500 hover:text-primary-600 transition-colors">
        <i class="fas fa-arrow-left mr-2"></i>Zurück zur Übersicht
    </a>
</div>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i>
    <?= e($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<!-- Header Card -->
<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div class="flex items-center gap-4">
            <?php if (!empty($customer['logo_url'])): ?>
            <img src="<?= e($customer['logo_url']) ?>" alt="" class="w-16 h-16 rounded-xl object-cover">
            <?php else: ?>
            <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                <span class="text-primary-600 dark:text-primary-400 font-bold text-xl">
                    <?= strtoupper(substr($customer['company_name'], 0, 2)) ?>
                </span>
            </div>
            <?php endif; ?>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white"><?= e($customer['company_name']) ?></h2>
                <p class="text-slate-500">
                    <a href="https://<?= e($customer['subdomain']) ?>.empfohlen.de" target="_blank" class="hover:text-primary-600">
                        <?= e($customer['subdomain']) ?>.empfohlen.de <i class="fas fa-external-link text-xs"></i>
                    </a>
                </p>
                <p class="text-sm text-slate-400 mt-1">
                    <?= e($customer['contact_name']) ?> &bull; <?= e($customer['email']) ?>
                </p>
            </div>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <!-- Plan Badge -->
            <?php
            $planColors = [
                'starter' => 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200',
                'professional' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300',
                'enterprise' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
            ];
            ?>
            <span class="px-3 py-1.5 text-sm font-medium rounded-full <?= $planColors[$customer['plan']] ?>">
                <i class="fas fa-gem mr-1"></i><?= ucfirst($customer['plan']) ?>
            </span>
            
            <!-- Status Badge -->
            <?php
            $statusColors = [
                'active' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                'trial' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                'paused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
            ];
            $statusLabels = ['active' => 'Aktiv', 'trial' => 'Trial', 'cancelled' => 'Gekündigt', 'paused' => 'Pausiert'];
            ?>
            <span class="px-3 py-1.5 text-sm font-medium rounded-full <?= $statusColors[$customer['subscription_status']] ?>">
                <?= $statusLabels[$customer['subscription_status']] ?>
            </span>
            
            <!-- Aktionen -->
            <div class="relative" x-data="{ open: false }">
                <button onclick="this.nextElementSibling.classList.toggle('hidden')" 
                        class="px-4 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 rounded-lg transition-all">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="hidden absolute right-0 mt-2 w-48 bg-white dark:bg-slate-800 rounded-lg shadow-lg border border-slate-200 dark:border-slate-700 z-10">
                    <form method="POST" class="p-2">
                        <input type="hidden" name="action" value="change_status">
                        <p class="px-3 py-1 text-xs text-slate-400 uppercase">Status ändern</p>
                        <button type="submit" name="status" value="active" class="w-full text-left px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 rounded">
                            <i class="fas fa-check text-green-500 w-5"></i> Aktiv
                        </button>
                        <button type="submit" name="status" value="paused" class="w-full text-left px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 rounded">
                            <i class="fas fa-pause text-amber-500 w-5"></i> Pausiert
                        </button>
                        <button type="submit" name="status" value="cancelled" class="w-full text-left px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 rounded">
                            <i class="fas fa-times text-red-500 w-5"></i> Gekündigt
                        </button>
                    </form>
                    <hr class="border-slate-200 dark:border-slate-700">
                    <form method="POST" class="p-2">
                        <input type="hidden" name="action" value="change_plan">
                        <p class="px-3 py-1 text-xs text-slate-400 uppercase">Plan ändern</p>
                        <button type="submit" name="plan" value="starter" class="w-full text-left px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 rounded">
                            Starter
                        </button>
                        <button type="submit" name="plan" value="professional" class="w-full text-left px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 rounded">
                            Professional
                        </button>
                        <button type="submit" name="plan" value="enterprise" class="w-full text-left px-3 py-2 text-sm hover:bg-slate-100 dark:hover:bg-slate-700 rounded">
                            Enterprise
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['leads'], 0, ',', '.') ?></p>
        <p class="text-sm text-slate-500">Empfehler</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-green-600"><?= number_format($stats['active_leads'], 0, ',', '.') ?></p>
        <p class="text-sm text-slate-500">Aktive Leads</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= number_format($stats['clicks'], 0, ',', '.') ?></p>
        <p class="text-sm text-slate-500">Klicks</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-purple-600"><?= number_format($stats['conversions'], 0, ',', '.') ?></p>
        <p class="text-sm text-slate-500">Conversions</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['conversion_rate'] ?>%</p>
        <p class="text-sm text-slate-500">Conv. Rate</p>
    </div>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
        <p class="text-2xl font-bold text-amber-600"><?= number_format($stats['rewards_sent'], 0, ',', '.') ?></p>
        <p class="text-sm text-slate-500">Belohnungen</p>
    </div>
</div>

<!-- Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Left Column -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Top Leads -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-semibold text-slate-800 dark:text-white">
                    <i class="fas fa-trophy text-amber-500 mr-2"></i>Top Empfehler
                </h3>
            </div>
            <div class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php foreach ($topLeads as $index => $lead): ?>
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 flex items-center justify-center bg-slate-100 dark:bg-slate-700 rounded-full text-sm font-medium text-slate-600 dark:text-slate-300">
                            <?= $index + 1 ?>
                        </span>
                        <div>
                            <p class="font-medium text-slate-800 dark:text-white"><?= e($lead['name'] ?: $lead['email']) ?></p>
                            <p class="text-xs text-slate-500"><?= e($lead['referral_code']) ?></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-slate-800 dark:text-white"><?= $lead['conversions'] ?> Conv.</p>
                        <p class="text-xs text-slate-500"><?= $lead['clicks'] ?> Klicks</p>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($topLeads)): ?>
                <div class="p-8 text-center text-slate-500">
                    <i class="fas fa-users text-3xl mb-2"></i>
                    <p>Noch keine Empfehler</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Belohnungen -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-semibold text-slate-800 dark:text-white">
                    <i class="fas fa-gift text-purple-500 mr-2"></i>Belohnungsstufen
                </h3>
            </div>
            <div class="p-4">
                <?php if (!empty($rewards)): ?>
                <div class="space-y-3">
                    <?php foreach ($rewards as $reward): ?>
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 flex items-center justify-center bg-purple-100 dark:bg-purple-900/30 rounded-full text-purple-600 dark:text-purple-400 font-bold">
                                <?= $reward['level'] ?>
                            </span>
                            <div>
                                <p class="font-medium text-slate-800 dark:text-white"><?= e($reward['name']) ?></p>
                                <p class="text-xs text-slate-500"><?= $reward['required_conversions'] ?> Empfehlungen nötig</p>
                            </div>
                        </div>
                        <span class="text-sm text-slate-500">
                            <?= $reward['delivery_count'] ?>× vergeben
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-center text-slate-500 py-4">Keine Belohnungen konfiguriert</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div class="space-y-6">
        
        <!-- Kundeninfos -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-semibold text-slate-800 dark:text-white">
                    <i class="fas fa-info-circle text-primary-500 mr-2"></i>Kundeninformationen
                </h3>
            </div>
            <div class="p-4 space-y-4 text-sm">
                <div>
                    <p class="text-slate-500">E-Mail</p>
                    <p class="text-slate-800 dark:text-white"><?= e($customer['email']) ?></p>
                </div>
                <div>
                    <p class="text-slate-500">Telefon</p>
                    <p class="text-slate-800 dark:text-white"><?= e($customer['phone'] ?: '-') ?></p>
                </div>
                <div>
                    <p class="text-slate-500">Website</p>
                    <p class="text-slate-800 dark:text-white">
                        <?php if ($customer['website']): ?>
                        <a href="<?= e($customer['website']) ?>" target="_blank" class="text-primary-600 hover:underline">
                            <?= e($customer['website']) ?>
                        </a>
                        <?php else: ?>-<?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-slate-500">Branche</p>
                    <p class="text-slate-800 dark:text-white"><?= e(ucfirst($customer['industry'])) ?></p>
                </div>
                <div>
                    <p class="text-slate-500">Adresse</p>
                    <p class="text-slate-800 dark:text-white">
                        <?= e($customer['address_street']) ?><br>
                        <?= e($customer['address_zip']) ?> <?= e($customer['address_city']) ?>
                    </p>
                </div>
                <div>
                    <p class="text-slate-500">Registriert</p>
                    <p class="text-slate-800 dark:text-white"><?= date('d.m.Y H:i', strtotime($customer['created_at'])) ?></p>
                </div>
                <div>
                    <p class="text-slate-500">Letzter Login</p>
                    <p class="text-slate-800 dark:text-white">
                        <?= $customer['last_login_at'] ? date('d.m.Y H:i', strtotime($customer['last_login_at'])) : 'Nie' ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Letzte Aktivitäten -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-semibold text-slate-800 dark:text-white">
                    <i class="fas fa-clock text-primary-500 mr-2"></i>Letzte Aktivitäten
                </h3>
            </div>
            <div class="divide-y divide-slate-200 dark:divide-slate-700 max-h-64 overflow-y-auto">
                <?php foreach ($recentActivities as $activity): ?>
                <div class="flex items-center gap-3 p-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center <?= $activity['type'] === 'lead' ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-green-100 dark:bg-green-900/30' ?>">
                        <i class="fas <?= $activity['type'] === 'lead' ? 'fa-user-plus text-blue-600' : 'fa-handshake text-green-600' ?> text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-slate-800 dark:text-white truncate"><?= e($activity['title']) ?></p>
                        <p class="text-xs text-slate-500"><?= timeAgo($activity['created_at']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($recentActivities)): ?>
                <div class="p-4 text-center text-slate-500 text-sm">
                    Keine Aktivitäten
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Zahlungen -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
            <div class="p-4 border-b border-slate-200 dark:border-slate-700">
                <h3 class="font-semibold text-slate-800 dark:text-white">
                    <i class="fas fa-credit-card text-green-500 mr-2"></i>Zahlungen
                </h3>
            </div>
            <div class="divide-y divide-slate-200 dark:divide-slate-700 max-h-48 overflow-y-auto">
                <?php foreach ($payments as $payment): ?>
                <div class="flex items-center justify-between p-3">
                    <div>
                        <p class="text-sm text-slate-800 dark:text-white"><?= number_format($payment['amount'], 2, ',', '.') ?> €</p>
                        <p class="text-xs text-slate-500"><?= date('d.m.Y', strtotime($payment['created_at'])) ?></p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full <?= $payment['status'] === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-300' ?>">
                        <?= $payment['status'] === 'completed' ? 'Bezahlt' : $payment['status'] ?>
                    </span>
                </div>
                <?php endforeach; ?>
                
                <?php if (empty($payments)): ?>
                <div class="p-4 text-center text-slate-500 text-sm">
                    Keine Zahlungen
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
