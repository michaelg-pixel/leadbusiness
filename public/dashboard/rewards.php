<?php
/**
 * Leadbusiness - Belohnungen verwalten
 * Mit Dark/Light Mode und Plan-basierter Stufenanzahl
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

$campaign = $db->fetch(
    "SELECT * FROM campaigns WHERE customer_id = ? AND is_default = 1",
    [$customerId]
);

// Plan-basierte Limits für Belohnungsstufen
$planLimits = [
    'starter' => 3,
    'professional' => 5,
    'enterprise' => 10
];
$maxRewards = $planLimits[$customer['plan']] ?? 3;
$currentPlan = $customer['plan'] ?? 'starter';

$rewards = $db->fetchAll(
    "SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC",
    [$campaign['id']]
);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_reward') {
        $rewardId = intval($_POST['reward_id'] ?? 0);
        $level = intval($_POST['level'] ?? 0);
        $conversionsRequired = intval($_POST['conversions_required'] ?? 0);
        $rewardType = $_POST['reward_type'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        
        // Reward-spezifische Felder
        $discountPercent = intval($_POST['discount_percent'] ?? 0);
        $couponCode = trim($_POST['coupon_code'] ?? '');
        $couponValidityDays = intval($_POST['coupon_validity_days'] ?? 30);
        $voucherAmount = floatval($_POST['voucher_amount'] ?? 0);
        $downloadFileUrl = trim($_POST['download_file_url'] ?? '');
        $redeemUrl = trim($_POST['redeem_url'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $requiresAddress = isset($_POST['requires_address']) ? 1 : 0;
        
        if ($level < 1 || $level > $maxRewards) {
            $error = "Ungültige Stufe. Ihr {$currentPlan}-Plan erlaubt max. {$maxRewards} Stufen.";
        } elseif ($conversionsRequired < 1) {
            $error = "Bitte geben Sie die Anzahl der benötigten Empfehlungen an.";
        } elseif (empty($title)) {
            $error = "Bitte geben Sie einen Titel für die Belohnung an.";
        } else {
            $data = [
                'level' => $level,
                'conversions_required' => $conversionsRequired,
                'reward_type' => $rewardType,
                'title' => $title,
                'description' => $description,
                'discount_percent' => $discountPercent ?: null,
                'coupon_code' => $couponCode ?: null,
                'coupon_validity_days' => $couponValidityDays ?: null,
                'voucher_amount' => $voucherAmount ?: null,
                'download_file_url' => $downloadFileUrl ?: null,
                'redeem_url' => $redeemUrl ?: null,
                'instructions' => $instructions ?: null,
                'requires_address' => $requiresAddress,
                'is_active' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            if ($rewardId) {
                // Existierende Belohnung bearbeiten
                $db->update('rewards', $data, 'id = ? AND campaign_id = ?', [$rewardId, $campaign['id']]);
                $message = 'Belohnung erfolgreich aktualisiert!';
            } else {
                // Prüfen ob Stufe bereits existiert
                $existing = $db->fetch("SELECT id FROM rewards WHERE campaign_id = ? AND level = ?", [$campaign['id'], $level]);
                if ($existing) {
                    $error = "Stufe {$level} existiert bereits. Bitte wählen Sie eine andere Stufe.";
                } else {
                    // Prüfen ob Maximum erreicht
                    $currentCount = $db->fetch("SELECT COUNT(*) as cnt FROM rewards WHERE campaign_id = ?", [$campaign['id']]);
                    if ($currentCount['cnt'] >= $maxRewards) {
                        $error = "Sie haben bereits {$maxRewards} Belohnungsstufen erreicht. " . 
                                ($currentPlan !== 'enterprise' ? "Upgrade auf einen höheren Plan für mehr Stufen." : "");
                    } else {
                        $data['campaign_id'] = $campaign['id'];
                        $data['customer_id'] = $customerId;
                        $data['created_at'] = date('Y-m-d H:i:s');
                        $db->insert('rewards', $data);
                        $message = 'Neue Belohnungsstufe hinzugefügt!';
                    }
                }
            }
            
            // Rewards neu laden
            $rewards = $db->fetchAll("SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC", [$campaign['id']]);
        }
    }
    
    if ($action === 'delete_reward') {
        $rewardId = intval($_POST['reward_id'] ?? 0);
        $db->query("DELETE FROM rewards WHERE id = ? AND campaign_id = ?", [$rewardId, $campaign['id']]);
        $message = 'Belohnung erfolgreich gelöscht!';
        $rewards = $db->fetchAll("SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC", [$campaign['id']]);
    }
    
    if ($action === 'toggle_active') {
        $rewardId = intval($_POST['reward_id'] ?? 0);
        $newStatus = intval($_POST['new_status'] ?? 0);
        $db->update('rewards', ['is_active' => $newStatus], 'id = ? AND campaign_id = ?', [$rewardId, $campaign['id']]);
        $message = $newStatus ? 'Belohnung aktiviert!' : 'Belohnung deaktiviert!';
        $rewards = $db->fetchAll("SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC", [$campaign['id']]);
    }
}

// Belohnungstypen mit Icons und Beschreibungen
$rewardTypes = [
    'discount' => ['label' => 'Rabatt (%)', 'icon' => 'fa-percent', 'color' => 'text-green-500'],
    'coupon_code' => ['label' => 'Gutschein-Code', 'icon' => 'fa-ticket', 'color' => 'text-blue-500'],
    'free_product' => ['label' => 'Gratis-Produkt', 'icon' => 'fa-box-open', 'color' => 'text-purple-500'],
    'free_service' => ['label' => 'Gratis-Service', 'icon' => 'fa-concierge-bell', 'color' => 'text-pink-500'],
    'digital_download' => ['label' => 'Digital-Download', 'icon' => 'fa-download', 'color' => 'text-cyan-500'],
    'voucher' => ['label' => 'Wertgutschein (€)', 'icon' => 'fa-euro-sign', 'color' => 'text-amber-500'],
    'video_course' => ['label' => 'Video-Kurs', 'icon' => 'fa-video', 'color' => 'text-red-500'],
    'coaching_session' => ['label' => 'Coaching-Session', 'icon' => 'fa-user-tie', 'color' => 'text-indigo-500'],
    'webinar_access' => ['label' => 'Webinar-Zugang', 'icon' => 'fa-chalkboard-teacher', 'color' => 'text-orange-500'],
    'exclusive_content' => ['label' => 'Exklusiver Content', 'icon' => 'fa-star', 'color' => 'text-yellow-500'],
    'membership_upgrade' => ['label' => 'Mitgliedschafts-Upgrade', 'icon' => 'fa-crown', 'color' => 'text-amber-600']
];

$pageTitle = 'Belohnungen verwalten';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<?php if ($message): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl mb-6 flex items-center">
    <i class="fas fa-check-circle mr-3 text-lg"></i>
    <span><?= e($message) ?></span>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-6 flex items-center">
    <i class="fas fa-exclamation-circle mr-3 text-lg"></i>
    <span><?= e($error) ?></span>
</div>
<?php endif; ?>

<!-- Plan Info Header -->
<div class="bg-gradient-to-r from-primary-50 to-amber-50 dark:from-primary-900/20 dark:to-amber-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-5 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-semibold uppercase tracking-wider <?= $currentPlan === 'enterprise' ? 'text-amber-600 dark:text-amber-400' : ($currentPlan === 'professional' ? 'text-primary-600 dark:text-primary-400' : 'text-slate-600 dark:text-slate-400') ?>">
                    <?= ucfirst($currentPlan) ?>-Plan
                </span>
                <?php if ($currentPlan === 'enterprise'): ?>
                <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 rounded text-xs">
                    <i class="fas fa-crown mr-1"></i>Premium
                </span>
                <?php endif; ?>
            </div>
            <p class="text-slate-700 dark:text-slate-300">
                <span class="font-semibold text-lg"><?= count($rewards) ?></span> 
                <span class="text-slate-500 dark:text-slate-400">von</span>
                <span class="font-semibold text-lg"><?= $maxRewards ?></span>
                <span class="text-slate-500 dark:text-slate-400">Belohnungsstufen konfiguriert</span>
            </p>
        </div>
        
        <!-- Progress Bar -->
        <div class="flex-1 max-w-xs">
            <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-primary-500 to-amber-500 rounded-full transition-all duration-500"
                     style="width: <?= min(100, (count($rewards) / $maxRewards) * 100) ?>%"></div>
            </div>
        </div>
        
        <?php if ($currentPlan !== 'enterprise'): ?>
        <a href="/dashboard/upgrade.php" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-lg font-medium text-sm transition-all shadow-lg shadow-amber-500/25 whitespace-nowrap">
            <i class="fas fa-arrow-up"></i>
            <?php if ($currentPlan === 'starter'): ?>
            Auf Professional upgraden
            <?php else: ?>
            Auf Enterprise upgraden
            <?php endif; ?>
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Rewards List -->
<div class="space-y-4 mb-8">
    <?php if (empty($rewards)): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
        <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-gift text-2xl text-amber-500"></i>
        </div>
        <h3 class="text-lg font-semibold text-slate-800 dark:text-white mb-2">Noch keine Belohnungen</h3>
        <p class="text-slate-500 dark:text-slate-400 mb-4">Fügen Sie Belohnungsstufen hinzu, um Ihre Empfehler zu motivieren.</p>
        <button onclick="document.getElementById('rewardForm').scrollIntoView({behavior: 'smooth'})" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
            <i class="fas fa-plus"></i>
            Erste Belohnung erstellen
        </button>
    </div>
    <?php else: ?>
    
    <?php foreach ($rewards as $index => $reward): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden <?= !$reward['is_active'] ? 'opacity-60' : '' ?>">
        <div class="p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <!-- Level Badge -->
                    <div class="relative">
                        <div class="w-14 h-14 bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl flex items-center justify-center text-white text-xl font-bold shadow-lg shadow-amber-500/30">
                            <?= $reward['level'] ?>
                        </div>
                        <?php if (!$reward['is_active']): ?>
                        <div class="absolute -top-1 -right-1 w-5 h-5 bg-slate-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-pause text-white text-xs"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <h3 class="font-semibold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                            <?= e($reward['title']) ?>
                            <?php if (!$reward['is_active']): ?>
                            <span class="text-xs px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded">Pausiert</span>
                            <?php endif; ?>
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 flex items-center gap-3">
                            <span class="flex items-center gap-1">
                                <i class="fas fa-users text-primary-500"></i>
                                Bei <?= $reward['conversions_required'] ?> Empfehlung<?= $reward['conversions_required'] > 1 ? 'en' : '' ?>
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fas <?= $rewardTypes[$reward['reward_type']]['icon'] ?? 'fa-gift' ?> <?= $rewardTypes[$reward['reward_type']]['color'] ?? 'text-slate-500' ?>"></i>
                                <?= $rewardTypes[$reward['reward_type']]['label'] ?? $reward['reward_type'] ?>
                            </span>
                        </p>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-1">
                    <!-- Toggle Active -->
                    <form method="POST" class="inline">
                        <input type="hidden" name="action" value="toggle_active">
                        <input type="hidden" name="reward_id" value="<?= $reward['id'] ?>">
                        <input type="hidden" name="new_status" value="<?= $reward['is_active'] ? 0 : 1 ?>">
                        <button type="submit" title="<?= $reward['is_active'] ? 'Deaktivieren' : 'Aktivieren' ?>"
                                class="p-2 rounded-lg <?= $reward['is_active'] ? 'text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' ?> transition-colors">
                            <i class="fas <?= $reward['is_active'] ? 'fa-toggle-on text-xl' : 'fa-toggle-off text-xl' ?>"></i>
                        </button>
                    </form>
                    
                    <!-- Edit -->
                    <button onclick='editReward(<?= json_encode($reward, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' 
                            title="Bearbeiten"
                            class="p-2 rounded-lg text-slate-400 hover:text-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                    
                    <!-- Delete -->
                    <form method="POST" class="inline" onsubmit="return confirm('Belohnung wirklich löschen? Diese Aktion kann nicht rückgängig gemacht werden.')">
                        <input type="hidden" name="action" value="delete_reward">
                        <input type="hidden" name="reward_id" value="<?= $reward['id'] ?>">
                        <button type="submit" title="Löschen"
                                class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Reward Details -->
            <?php if ($reward['description'] || $reward['discount_percent'] || $reward['voucher_amount'] || $reward['coupon_code']): ?>
            <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
                <?php if ($reward['description']): ?>
                <p class="text-slate-700 dark:text-slate-300"><?= e($reward['description']) ?></p>
                <?php endif; ?>
                
                <div class="flex flex-wrap gap-3 mt-2 text-sm">
                    <?php if ($reward['discount_percent']): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded">
                        <i class="fas fa-percent"></i>
                        <?= $reward['discount_percent'] ?>% Rabatt
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($reward['voucher_amount']): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded">
                        <i class="fas fa-euro-sign"></i>
                        <?= number_format($reward['voucher_amount'], 2, ',', '.') ?> € Gutschein
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($reward['coupon_code']): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded font-mono">
                        <i class="fas fa-ticket"></i>
                        <?= e($reward['coupon_code']) ?>
                    </span>
                    <?php endif; ?>
                    
                    <?php if ($reward['requires_address']): ?>
                    <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded">
                        <i class="fas fa-map-marker-alt"></i>
                        Adresse erforderlich
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Connector to next level -->
        <?php if ($index < count($rewards) - 1): ?>
        <div class="flex justify-center -mb-2">
            <div class="w-0.5 h-4 bg-slate-200 dark:bg-slate-700"></div>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    
    <?php endif; ?>
</div>

<!-- Add/Edit Form -->
<?php if (count($rewards) < $maxRewards || true): // Always show form for editing ?>
<div id="rewardFormContainer" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white flex items-center gap-2">
            <i class="fas fa-plus-circle text-primary-500" id="formIcon"></i>
            <span id="formTitle"><?= count($rewards) >= $maxRewards ? 'Belohnung bearbeiten' : 'Neue Belohnungsstufe hinzufügen' ?></span>
        </h3>
    </div>
    
    <?php if (count($rewards) >= $maxRewards): ?>
    <div class="p-6" id="maxReachedNotice">
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4 text-center">
            <i class="fas fa-exclamation-triangle text-amber-500 text-2xl mb-2"></i>
            <p class="text-amber-700 dark:text-amber-300 font-medium">Maximum erreicht</p>
            <p class="text-amber-600 dark:text-amber-400 text-sm mt-1">
                Sie haben bereits <?= $maxRewards ?> Belohnungsstufen (Maximum für <?= ucfirst($currentPlan) ?>-Plan).
            </p>
            <?php if ($currentPlan !== 'enterprise'): ?>
            <a href="/dashboard/upgrade.php" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm transition-colors">
                <i class="fas fa-arrow-up"></i>
                Jetzt upgraden für mehr Stufen
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <form method="POST" id="rewardForm" class="p-6 <?= count($rewards) >= $maxRewards ? 'hidden' : '' ?>">
        <input type="hidden" name="action" value="save_reward">
        <input type="hidden" name="reward_id" id="rewardId" value="">
        
        <div class="grid md:grid-cols-3 gap-4 mb-4">
            <!-- Level -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    <i class="fas fa-layer-group mr-1 text-slate-400"></i>
                    Stufe *
                </label>
                <select name="level" id="levelInput" required 
                        class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <?php for ($i = 1; $i <= $maxRewards; $i++): ?>
                    <option value="<?= $i ?>" <?= $i === count($rewards) + 1 ? 'selected' : '' ?>>Stufe <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            
            <!-- Required Conversions -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    <i class="fas fa-users mr-1 text-slate-400"></i>
                    Benötigte Empfehlungen *
                </label>
                <input type="number" name="conversions_required" id="conversionsInput" min="1" max="1000" required
                       class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                       placeholder="z.B. 3">
            </div>
            
            <!-- Reward Type -->
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    <i class="fas fa-gift mr-1 text-slate-400"></i>
                    Belohnungstyp *
                </label>
                <select name="reward_type" id="typeInput" required onchange="updateRewardFields()"
                        class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <?php foreach ($rewardTypes as $key => $type): ?>
                    <option value="<?= $key ?>"><?= $type['label'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <!-- Title -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                <i class="fas fa-heading mr-1 text-slate-400"></i>
                Titel der Belohnung *
            </label>
            <input type="text" name="title" id="titleInput" required maxlength="255"
                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                   placeholder="z.B. 10% Rabatt auf Ihren nächsten Einkauf">
        </div>
        
        <!-- Description -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                <i class="fas fa-align-left mr-1 text-slate-400"></i>
                Beschreibung (optional)
            </label>
            <textarea name="description" id="descInput" rows="2"
                      class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none" 
                      placeholder="Detaillierte Beschreibung für Ihre Empfehler..."></textarea>
        </div>
        
        <!-- Type-specific fields -->
        <div id="discountFields" class="type-fields mb-4 hidden">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                <i class="fas fa-percent mr-1 text-green-500"></i>
                Rabatt in Prozent
            </label>
            <input type="number" name="discount_percent" id="discountPercentInput" min="1" max="100"
                   class="w-full md:w-48 px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                   placeholder="z.B. 10">
        </div>
        
        <div id="couponFields" class="type-fields mb-4 hidden">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        <i class="fas fa-ticket mr-1 text-blue-500"></i>
                        Gutschein-Code
                    </label>
                    <input type="text" name="coupon_code" id="couponCodeInput" maxlength="50"
                           class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 font-mono uppercase" 
                           placeholder="z.B. WILLKOMMEN10">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        <i class="fas fa-calendar mr-1 text-slate-400"></i>
                        Gültigkeit (Tage)
                    </label>
                    <input type="number" name="coupon_validity_days" id="couponValidityInput" min="1" max="365" value="30"
                           class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
        </div>
        
        <div id="voucherFields" class="type-fields mb-4 hidden">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                <i class="fas fa-euro-sign mr-1 text-amber-500"></i>
                Gutschein-Wert in Euro
            </label>
            <input type="number" name="voucher_amount" id="voucherAmountInput" min="1" max="10000" step="0.01"
                   class="w-full md:w-48 px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                   placeholder="z.B. 50.00">
        </div>
        
        <div id="downloadFields" class="type-fields mb-4 hidden">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                <i class="fas fa-link mr-1 text-cyan-500"></i>
                Download-URL
            </label>
            <input type="url" name="download_file_url" id="downloadUrlInput"
                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                   placeholder="https://beispiel.de/download/ebook.pdf">
        </div>
        
        <!-- General URL for redeeming -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                <i class="fas fa-external-link-alt mr-1 text-slate-400"></i>
                Einlöse-URL (optional)
            </label>
            <input type="url" name="redeem_url" id="redeemUrlInput"
                   class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                   placeholder="https://shop.beispiel.de/checkout">
        </div>
        
        <!-- Instructions -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                <i class="fas fa-info-circle mr-1 text-slate-400"></i>
                Einlöse-Anleitung (optional)
            </label>
            <textarea name="instructions" id="instructionsInput" rows="2"
                      class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none" 
                      placeholder="Anleitung zum Einlösen der Belohnung..."></textarea>
        </div>
        
        <!-- Requires Address -->
        <div class="mb-6">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="requires_address" id="requiresAddressInput" value="1"
                       class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500 dark:bg-slate-700">
                <span class="text-sm text-slate-700 dark:text-slate-300">
                    <i class="fas fa-map-marker-alt mr-1 text-slate-400"></i>
                    Empfehler muss Adresse angeben (z.B. für physische Produkte)
                </span>
            </label>
        </div>
        
        <!-- Buttons -->
        <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all shadow-lg shadow-primary-600/30 font-medium flex items-center gap-2">
                <i class="fas fa-save"></i>
                <span id="submitText">Belohnung speichern</span>
            </button>
            <button type="button" onclick="resetForm()" class="px-6 py-2.5 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-500 transition-all font-medium">
                Abbrechen
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<!-- Tips -->
<div class="mt-8 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800/50 dark:to-slate-900/50 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
    <h4 class="font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
        <i class="fas fa-lightbulb text-amber-500"></i>
        Tipps für erfolgreiche Belohnungen
    </h4>
    <div class="grid md:grid-cols-2 gap-4 text-sm text-slate-600 dark:text-slate-400">
        <div class="flex items-start gap-2">
            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
            <span>Staffeln Sie die Belohnungen: Kleine Belohnung bei 3, größere bei 5 und 10 Empfehlungen.</span>
        </div>
        <div class="flex items-start gap-2">
            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
            <span>Bieten Sie sowohl sofort einlösbare als auch größere Prämien an.</span>
        </div>
        <div class="flex items-start gap-2">
            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
            <span>Klare, konkrete Beschreibungen motivieren mehr als vage Versprechen.</span>
        </div>
        <div class="flex items-start gap-2">
            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
            <span>Belohnungen mit exklusivem Charakter (z.B. "nur für Empfehler") wirken besonders.</span>
        </div>
    </div>
</div>

<script>
    const maxRewards = <?= $maxRewards ?>;
    const currentRewardCount = <?= count($rewards) ?>;
    
    function updateRewardFields() {
        // Hide all type-specific fields
        document.querySelectorAll('.type-fields').forEach(el => el.classList.add('hidden'));
        
        const type = document.getElementById('typeInput').value;
        
        switch(type) {
            case 'discount':
                document.getElementById('discountFields').classList.remove('hidden');
                break;
            case 'coupon_code':
                document.getElementById('couponFields').classList.remove('hidden');
                break;
            case 'voucher':
                document.getElementById('voucherFields').classList.remove('hidden');
                break;
            case 'digital_download':
                document.getElementById('downloadFields').classList.remove('hidden');
                break;
        }
    }
    
    function editReward(reward) {
        // Show form if hidden
        document.getElementById('rewardForm').classList.remove('hidden');
        const maxNotice = document.getElementById('maxReachedNotice');
        if (maxNotice) maxNotice.classList.add('hidden');
        
        // Fill form
        document.getElementById('rewardId').value = reward.id;
        document.getElementById('levelInput').value = reward.level;
        document.getElementById('conversionsInput').value = reward.conversions_required;
        document.getElementById('typeInput').value = reward.reward_type;
        document.getElementById('titleInput').value = reward.title || '';
        document.getElementById('descInput').value = reward.description || '';
        document.getElementById('discountPercentInput').value = reward.discount_percent || '';
        document.getElementById('couponCodeInput').value = reward.coupon_code || '';
        document.getElementById('couponValidityInput').value = reward.coupon_validity_days || 30;
        document.getElementById('voucherAmountInput').value = reward.voucher_amount || '';
        document.getElementById('downloadUrlInput').value = reward.download_file_url || '';
        document.getElementById('redeemUrlInput').value = reward.redeem_url || '';
        document.getElementById('instructionsInput').value = reward.instructions || '';
        document.getElementById('requiresAddressInput').checked = reward.requires_address == 1;
        
        // Update UI
        document.getElementById('formTitle').textContent = 'Belohnung bearbeiten';
        document.getElementById('formIcon').className = 'fas fa-edit text-primary-500';
        document.getElementById('submitText').textContent = 'Änderungen speichern';
        
        updateRewardFields();
        document.getElementById('rewardFormContainer').scrollIntoView({ behavior: 'smooth' });
    }
    
    function resetForm() {
        document.getElementById('rewardForm').reset();
        document.getElementById('rewardId').value = '';
        document.getElementById('formTitle').textContent = 'Neue Belohnungsstufe hinzufügen';
        document.getElementById('formIcon').className = 'fas fa-plus-circle text-primary-500';
        document.getElementById('submitText').textContent = 'Belohnung speichern';
        
        // Select next available level
        const nextLevel = Math.min(currentRewardCount + 1, maxRewards);
        document.getElementById('levelInput').value = nextLevel;
        
        // Show/hide form based on limit
        if (currentRewardCount >= maxRewards) {
            document.getElementById('rewardForm').classList.add('hidden');
            const maxNotice = document.getElementById('maxReachedNotice');
            if (maxNotice) maxNotice.classList.remove('hidden');
        }
        
        updateRewardFields();
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        updateRewardFields();
    });
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
