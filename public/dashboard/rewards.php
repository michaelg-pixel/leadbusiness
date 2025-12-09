<?php
/**
 * Leadbusiness - Belohnungen verwalten
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

// Standard-Kampagne
$campaign = $db->fetch(
    "SELECT * FROM campaigns WHERE customer_id = ? AND is_default = 1",
    [$customerId]
);

// Max Rewards nach Plan
$maxRewards = $customer['plan'] === 'starter' ? 3 : 5;

// Belohnungen laden
$rewards = $db->fetchAll(
    "SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC",
    [$campaign['id']]
);

// Belohnungs-Statistiken
$rewardStats = $db->fetchAll(
    "SELECT r.id, r.level, r.description,
            COUNT(rd.id) as deliveries,
            SUM(CASE WHEN rd.status = 'sent' THEN 1 ELSE 0 END) as sent,
            SUM(CASE WHEN rd.status = 'redeemed' THEN 1 ELSE 0 END) as redeemed
     FROM rewards r
     LEFT JOIN reward_deliveries rd ON r.id = rd.reward_id
     WHERE r.campaign_id = ?
     GROUP BY r.id
     ORDER BY r.level",
    [$campaign['id']]
);

// POST: Belohnung speichern
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
                // Prüfen ob Level schon existiert
                $existing = $db->fetch(
                    "SELECT id FROM rewards WHERE campaign_id = ? AND level = ?",
                    [$campaign['id'], $level]
                );
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
            
            // Rewards neu laden
            $rewards = $db->fetchAll(
                "SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC",
                [$campaign['id']]
            );
        }
    }
    
    if ($action === 'delete_reward') {
        $rewardId = intval($_POST['reward_id'] ?? 0);
        $db->query(
            "DELETE FROM rewards WHERE id = ? AND campaign_id = ?",
            [$rewardId, $campaign['id']]
        );
        $message = 'Belohnung gelöscht!';
        
        // Rewards neu laden
        $rewards = $db->fetchAll(
            "SELECT * FROM rewards WHERE campaign_id = ? ORDER BY level ASC",
            [$campaign['id']]
        );
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
        
        <!-- Sidebar -->
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
                <a href="/dashboard/leads.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-users w-5"></i><span>Empfehler</span>
                </a>
                <a href="/dashboard/rewards.php" class="flex items-center gap-3 px-4 py-3 text-indigo-600 bg-indigo-50 rounded-xl font-medium">
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
                <h1 class="text-2xl font-bold text-gray-900">Belohnungen</h1>
                <p class="text-gray-500">Verwalten Sie die Belohnungsstufen für Ihre Empfehler.</p>
            </header>
            
            <div class="p-6">
                
                <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($message) ?>
                </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i><?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                
                <!-- Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <p class="text-blue-800 text-sm">
                        <i class="fas fa-info-circle mr-2"></i>
                        Ihr Plan erlaubt bis zu <strong><?= $maxRewards ?> Belohnungsstufen</strong>. 
                        Derzeit haben Sie <?= count($rewards) ?> von <?= $maxRewards ?> konfiguriert.
                    </p>
                </div>
                
                <!-- Rewards List -->
                <div class="space-y-4 mb-8">
                    <?php foreach ($rewards as $reward): ?>
                    <div class="bg-white rounded-xl p-6 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600 text-xl font-bold">
                                    <?= $reward['level'] ?>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Stufe <?= $reward['level'] ?></h3>
                                    <p class="text-sm text-gray-500">Bei <?= $reward['required_conversions'] ?> Empfehlungen</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button onclick="editReward(<?= htmlspecialchars(json_encode($reward)) ?>)" 
                                        class="p-2 text-gray-400 hover:text-indigo-500">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="inline" onsubmit="return confirm('Wirklich löschen?')">
                                    <input type="hidden" name="action" value="delete_reward">
                                    <input type="hidden" name="reward_id" value="<?= $reward['id'] ?>">
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-800"><?= htmlspecialchars($reward['description']) ?></p>
                            <p class="text-sm text-gray-500 mt-1">
                                Typ: <?= $rewardTypes[$reward['reward_type']] ?? $reward['reward_type'] ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (count($rewards) < $maxRewards): ?>
                <!-- Add New Reward -->
                <div class="bg-white rounded-xl p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-900 mb-4">
                        <i class="fas fa-plus-circle text-indigo-500 mr-2"></i>
                        Neue Belohnungsstufe
                    </h3>
                    
                    <form method="POST" id="rewardForm">
                        <input type="hidden" name="action" value="save_reward">
                        <input type="hidden" name="reward_id" id="rewardId" value="">
                        
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stufe</label>
                                <select name="level" id="levelInput" required class="w-full px-4 py-2 border rounded-lg">
                                    <?php for ($i = 1; $i <= $maxRewards; $i++): ?>
                                    <option value="<?= $i ?>">Stufe <?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nach X Empfehlungen</label>
                                <input type="number" name="required_conversions" id="requiredInput" min="1" max="100" required
                                       class="w-full px-4 py-2 border rounded-lg" placeholder="z.B. 3">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Belohnungstyp</label>
                                <select name="reward_type" id="typeInput" required class="w-full px-4 py-2 border rounded-lg">
                                    <?php foreach ($rewardTypes as $key => $label): ?>
                                    <option value="<?= $key ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Wert (optional)</label>
                                <input type="text" name="reward_value" id="valueInput"
                                       class="w-full px-4 py-2 border rounded-lg" placeholder="z.B. 10% oder GUTSCHEIN123">
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Beschreibung (für Empfehler sichtbar)</label>
                            <input type="text" name="description" id="descInput" required
                                   class="w-full px-4 py-2 border rounded-lg" placeholder="z.B. 10% Rabatt auf Ihren nächsten Einkauf">
                        </div>
                        
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="px-6 py-2 bg-indigo-500 text-white rounded-lg hover:bg-indigo-600">
                                <i class="fas fa-save mr-2"></i>Speichern
                            </button>
                            <button type="button" onclick="resetForm()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                Abbrechen
                            </button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
                
            </div>
        </main>
    </div>
    
    <script>
        function editReward(reward) {
            document.getElementById('rewardId').value = reward.id;
            document.getElementById('levelInput').value = reward.level;
            document.getElementById('requiredInput').value = reward.required_conversions;
            document.getElementById('typeInput').value = reward.reward_type;
            document.getElementById('valueInput').value = reward.reward_value || '';
            document.getElementById('descInput').value = reward.description;
            document.getElementById('rewardForm').scrollIntoView({ behavior: 'smooth' });
        }
        
        function resetForm() {
            document.getElementById('rewardId').value = '';
            document.getElementById('rewardForm').reset();
        }
    </script>
    
</body>
</html>
