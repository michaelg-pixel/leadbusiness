<?php
/**
 * Leadbusiness - Design-Einstellungen
 * Mit Dark/Light Mode
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/services/BackgroundService.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

$backgrounds = $db->fetchAll(
    "SELECT * FROM background_images WHERE industry = ? AND is_active = 1 ORDER BY sort_order",
    [$customer['industry']]
);

$allgemeinBackgrounds = $db->fetchAll(
    "SELECT * FROM background_images WHERE industry = 'allgemein' AND is_active = 1 ORDER BY sort_order"
);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $backgroundId = intval($_POST['background_id'] ?? 0);
    $primaryColor = $_POST['primary_color'] ?? '#0ea5e9';
    
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $primaryColor)) {
        $primaryColor = '#0ea5e9';
    }
    
    $updateData = [
        'primary_color' => $primaryColor,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if ($backgroundId > 0) {
        $updateData['background_image_id'] = $backgroundId;
        $updateData['custom_background_url'] = null;
    }
    
    if ($customer['plan'] === 'professional' && !empty($_FILES['custom_background']['tmp_name'])) {
        $file = $_FILES['custom_background'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        
        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'Ungültiges Bildformat. Erlaubt: JPG, PNG, WebP';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error = 'Bild zu groß. Maximal 5MB erlaubt.';
        } else {
            $uploadDir = __DIR__ . '/../uploads/backgrounds/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $customer['subdomain'] . '-bg-' . time() . '.' . $extension;
            
            if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $updateData['custom_background_url'] = '/uploads/backgrounds/' . $filename;
                $updateData['background_image_id'] = null;
            } else {
                $error = 'Fehler beim Hochladen.';
            }
        }
    }
    
    if (empty($error)) {
        $db->update('customers', $updateData, 'id = ?', [$customerId]);
        $message = 'Design gespeichert!';
        $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
    }
}

$backgroundService = new BackgroundService();
$currentBgUrl = $backgroundService->getCustomerBackgroundUrl($customer);

$pageTitle = 'Design';

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

<!-- Preview -->
<div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
    <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
        <i class="fas fa-eye text-primary-500 mr-2"></i>Aktuelle Vorschau
    </h3>
    <div class="relative rounded-xl overflow-hidden h-48" 
         style="background-image: url('<?= e($currentBgUrl) ?>'); background-size: cover; background-position: center;">
        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
            <div class="text-center text-white">
                <div class="text-2xl font-bold mb-2"><?= e($customer['company_name']) ?></div>
                <a href="https://<?= e($customer['subdomain']) ?>.empfohlen.de" target="_blank" 
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium"
                   style="background-color: <?= e($customer['primary_color']) ?>;">
                    <i class="fas fa-external-link-alt"></i>Live-Seite ansehen
                </a>
            </div>
        </div>
    </div>
</div>

<form method="POST" enctype="multipart/form-data">
    
    <!-- Hintergrundbilder -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-image text-primary-500 mr-2"></i>Hintergrundbild wählen
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Passend zu Ihrer Branche: <?= ucfirst($customer['industry']) ?></p>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php foreach ($backgrounds as $bg): ?>
            <label class="rounded-xl overflow-hidden cursor-pointer transition-all hover:scale-[1.02] border-2 <?= $customer['background_image_id'] == $bg['id'] ? 'border-primary-500 ring-2 ring-primary-500' : 'border-transparent' ?>">
                <input type="radio" name="background_id" value="<?= $bg['id'] ?>" class="sr-only"
                       <?= $customer['background_image_id'] == $bg['id'] ? 'checked' : '' ?>>
                <img src="/assets/backgrounds/<?= e($bg['industry']) ?>/<?= e($bg['filename']) ?>" 
                     alt="<?= e($bg['display_name']) ?>" class="w-full h-28 object-cover">
                <div class="p-2 text-center text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-700"><?= e($bg['display_name']) ?></div>
            </label>
            <?php endforeach; ?>
            
            <?php foreach ($allgemeinBackgrounds as $bg): ?>
            <label class="rounded-xl overflow-hidden cursor-pointer transition-all hover:scale-[1.02] border-2 <?= $customer['background_image_id'] == $bg['id'] ? 'border-primary-500 ring-2 ring-primary-500' : 'border-transparent' ?>">
                <input type="radio" name="background_id" value="<?= $bg['id'] ?>" class="sr-only"
                       <?= $customer['background_image_id'] == $bg['id'] ? 'checked' : '' ?>>
                <img src="/assets/backgrounds/allgemein/<?= e($bg['filename']) ?>" 
                     alt="<?= e($bg['display_name']) ?>" class="w-full h-28 object-cover">
                <div class="p-2 text-center text-sm text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-700"><?= e($bg['display_name']) ?></div>
            </label>
            <?php endforeach; ?>
        </div>
        
        <?php if ($customer['plan'] === 'professional'): ?>
        <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                <i class="fas fa-crown text-amber-500 mr-1"></i>
                Eigenes Hintergrundbild hochladen
            </label>
            <input type="file" name="custom_background" accept="image/jpeg,image/png,image/webp"
                   class="w-full px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Empfohlen: 1920x1080px, max. 5MB, JPG/PNG/WebP</p>
            <?php if ($customer['custom_background_url']): ?>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                <i class="fas fa-check-circle mr-1"></i>
                Eigenes Bild aktiv: <?= e(basename($customer['custom_background_url'])) ?>
            </p>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="mt-6 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
            <p class="text-amber-800 dark:text-amber-300 text-sm">
                <i class="fas fa-lock mr-2"></i>
                <strong>Professional-Feature:</strong> Eigene Hintergrundbilder hochladen.
            </p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Hauptfarbe -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4">
            <i class="fas fa-palette text-primary-500 mr-2"></i>Hauptfarbe
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Diese Farbe wird für Buttons und Akzente verwendet.</p>
        
        <div class="flex items-center gap-4 mb-4">
            <input type="color" name="primary_color" id="colorPicker" value="<?= e($customer['primary_color']) ?>"
                   class="w-16 h-12 rounded cursor-pointer border-0">
            <input type="text" id="colorText" value="<?= e($customer['primary_color']) ?>" 
                   class="px-4 py-2 border border-slate-200 dark:border-slate-600 rounded-lg w-32 bg-white dark:bg-slate-700 text-slate-800 dark:text-white" readonly>
        </div>
        
        <!-- Quick Colors -->
        <div class="flex flex-wrap gap-2">
            <?php 
            $quickColors = ['#0ea5e9', '#0284c7', '#0369a1', '#10b981', '#059669', '#f59e0b', '#ef4444', '#8b5cf6'];
            foreach ($quickColors as $color): 
            ?>
            <button type="button" onclick="setColor('<?= $color ?>')" 
                    class="w-10 h-10 rounded-lg border-2 border-white dark:border-slate-600 shadow-md hover:scale-110 transition-transform" 
                    style="background-color: <?= $color ?>"></button>
            <?php endforeach; ?>
        </div>
    </div>
    
    <button type="submit" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition-all shadow-lg shadow-primary-600/30">
        <i class="fas fa-save mr-2"></i>Änderungen speichern
    </button>
    
</form>

<script>
    document.getElementById('colorPicker').addEventListener('input', function(e) {
        document.getElementById('colorText').value = e.target.value;
    });
    
    function setColor(color) {
        document.getElementById('colorPicker').value = color;
        document.getElementById('colorText').value = color;
    }
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
