<?php
/**
 * Leadbusiness - Belohnungen verwalten
 * Mit Dark/Light Mode
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

$maxRewards = $customer['plan'] === 'starter' ? 3 : 5;

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
        $requiredConversions = intval($_POST['required_conversions'] ?? 0);
        $rewardType = $_POST['reward_type'] ?? '';
        $description = trim($_POST['description'] ?? '');
        $rewardValue = trim($_POST['reward_value'] ?? '');
        
        if ($level < 1 || $level > $maxRewards) {
            $error = "Ungültige Stufe. Max. {$maxRewards} Stufen in Ihrem Plan.";
        } elseif ($requiredConversions < 1) {
            $error = "Bitte geben Sie die Anzahl der Empfehlungen an.";
        } elseif (empty($description)) {
            $error = "Bitte geben Sie eine Beschreibung an.";
        } else {
            $data = [
                'level' => $level,
                'required_conversions' => $requiredConversions,
                'reward_type' => $rewardType,
                'description' => $description,
                'reward_value' => $rewardValue,
                'is_active' => 1
            ];
            
            if ($rewardId) {
                $db->update('rewards', $data, 'id = ? AND campaign_id = ?', [$rewardId, $campaign['id']]);
                $message = 'Belohnung aktualisiert!';
            } else {
                $existing = $db->fetch("SELECT id FROM rewards WHERE campaign_id = ? AND level = ?", [$campaign['id'], $level]);
                if ($existing) {
                    $error = "Stufe {$level} existiert bereits.";
                } else {
                    $data['campaign_id'] = $campaign['id'];
                    $data['customer_id'] = $customerId;
                    $data['created_at'] = date('Y-m-d H:i:s');
                    $db->insert('rewards', $data);
                    $message = 'Belohnung hinzugefügt!';
                }
            }
            
            $rewards = $db->fetchAll("SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC", [$campaign['id']]);
        }
    }
    
    if ($action === 'delete_reward') {
        $rewardId = intval($_POST['reward_id'] ?? 0);
        $db->query("DELETE FROM rewards WHERE id = ? AND campaign_id = ?", [$rewardId, $campaign['id']]);
        $message = 'Belohnung gelöscht!';
        $rewards = $db->fetchAll("SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC", [$campaign['id']]);
    }
}

$rewardTypes = [
    'discount' => 'Rabatt (%)',
    'coupon_code' => 'Gutschein-Code',
    'free_product' => 'Gratis-Produkt',
    'free_service' => 'Gratis-Service',
    'digital_download' => 'Digital-Download',
    'voucher' => 'Wertgutschein (€)'
];

$pageTitle = 'Belohnungen';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<?php if ($message): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl mb-6">
    <i class="fas fa-check-circle mr-2"></i><?= e($message) ?>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-6">
    <i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?>
</div>
<?php endif; ?>

<!-- Info -->
<div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-4 mb-6">
    <p class="text-primary-800 dark:text-primary-300 text-sm">
        <i class="fas fa-info-circle mr-2"></i>
        Ihr Plan erlaubt bis zu <strong><?= $maxRewards ?> Belohnungsstufen</strong>. 
        Derzeit haben Sie <?= count($rewards) ?> von <?= $maxRewards ?> konfiguriert.
    </p>
</div>

<!-- Rewards List -->
<div class="space-y-4 mb-8">
    <?php foreach ($rewards as $reward): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 text-xl font-bold">
                    <?= $reward['level'] ?>
                </div>
                <div>
                    <h3 class="font-semibold text-slate-800 dark:text-white">Stufe <?= $reward['level'] ?></h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Bei <?= $reward['required_conversions'] ?> Empfehlungen</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="editReward(<?= htmlspecialchars(json_encode($reward)) ?>)" 
                        class="p-2 text-slate-400 hover:text-primary-500 transition-colors">
                    <i class="fas fa-edit"></i>
                </button>
                <form method="POST" class="inline" onsubmit="return confirm('Wirklich löschen?')">
                    <input type="hidden" name="action" value="delete_reward">
                    <input type="hidden" name="reward_id" value="<?= $reward['id'] ?>">
                    <button type="submit" class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mt-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-lg">
            <p class="text-slate-800 dark:text-slate-200"><?= e($reward['description']) ?></p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Typ: <?= $rewardTypes[$reward['reward_type']] ?? $reward['reward_type'] ?>
                <?php if ($reward['reward_value']): ?>
                 • Wert: <?= e($reward['reward_value']) ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($rewards)): ?>
    <div class="bg-white dark:bg-slate-800 rounded-xl p-12 text-center border border-slate-200 dark:border-slate-700">
        <i class="fas fa-gift text-4xl text-slate-300 dark:text-slate-600 mb-4"></i>
        <p class="text-slate-500 dark:text-slate-400">Noch keine Belohnungen konfiguriert.</p>
    </div>
    <?php endif; ?>
</div>

<?php if (count($rewards) < $maxRewards): ?>
<!-- Add New Reward -->
<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
    <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
        <i class="fas fa-plus-circle text-primary-500 mr-2"></i>
        <span id="formTitle">Neue Belohnungsstufe</span>
    </h3>
    
    <form method="POST" id="rewardForm">
        <input type="hidden" name="action" value="save_reward">
        <input type="hidden" name="reward_id" id="rewardId" value="">
        
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stufe</label>
                <select name="level" id="levelInput" required 
                        class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <?php for ($i = 1; $i <= $maxRewards; $i++): ?>
                    <option value="<?= $i ?>">Stufe <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nach X Empfehlungen</label>
                <input type="number" name="required_conversions" id="requiredInput" min="1" max="100" required
                       class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500" 
                       placeholder="z.B. 3">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Belohnungstyp</label>
                <select name="reward_type" id="typeInput" required 
                        class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500">
                    <?php foreach ($rewardTypes as $key => $label): ?>
                    <option value="<?= $key ?>"><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Wert (optional)</label>
                <input type="text" name="reward_value" id="valueInput"
                       class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500" 
                       placeholder="z.B. 10% oder GUTSCHEIN123">
            </div>
        </div>
        
        <div class="mt-4">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Beschreibung (für Empfehler sichtbar)</label>
            <input type="text" name="description" id="descInput" required
                   class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-primary-500" 
                   placeholder="z.B. 10% Rabatt auf Ihren nächsten Einkauf">
        </div>
        
        <div class="mt-6 flex gap-3">
            <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all shadow-lg shadow-primary-600/30">
                <i class="fas fa-save mr-2"></i>Speichern
            </button>
            <button type="button" onclick="resetForm()" class="px-6 py-2 bg-slate-200 dark:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-500 transition-all">
                Abbrechen
            </button>
        </div>
    </form>
</div>
<?php endif; ?>

<script>
    function editReward(reward) {
        document.getElementById('rewardId').value = reward.id;
        document.getElementById('levelInput').value = reward.level;
        document.getElementById('requiredInput').value = reward.required_conversions;
        document.getElementById('typeInput').value = reward.reward_type;
        document.getElementById('valueInput').value = reward.reward_value || '';
        document.getElementById('descInput').value = reward.description;
        document.getElementById('formTitle').textContent = 'Belohnung bearbeiten';
        document.getElementById('rewardForm').scrollIntoView({ behavior: 'smooth' });
    }
    
    function resetForm() {
        document.getElementById('rewardId').value = '';
        document.getElementById('formTitle').textContent = 'Neue Belohnungsstufe';
        document.getElementById('rewardForm').reset();
    }
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
