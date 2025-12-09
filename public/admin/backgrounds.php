<?php
/**
 * Admin Hintergrundbilder Verwaltung
 */

require_once __DIR__ . '/../../includes/init.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: /admin/login.php');
    exit;
}

$db = db();
$pageTitle = 'Hintergrundbilder';
$uploadDir = __DIR__ . '/../assets/backgrounds/';

// Aktionen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'upload':
            $industry = sanitize($_POST['industry'] ?? '');
            $displayName = sanitize($_POST['display_name'] ?? '');
            
            if (!empty($_FILES['image']['tmp_name']) && !empty($industry)) {
                $file = $_FILES['image'];
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                
                if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $_SESSION['flash_error'] = 'Nur JPG, PNG oder WebP erlaubt.';
                } elseif ($file['size'] > 5 * 1024 * 1024) {
                    $_SESSION['flash_error'] = 'Datei zu groß (max. 5 MB).';
                } else {
                    $targetDir = $uploadDir . $industry . '/';
                    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                    
                    $existingCount = $db->fetchColumn("SELECT COUNT(*) FROM background_images WHERE industry = ?", [$industry]);
                    $filename = 'bg-' . ($existingCount + 1) . '.' . $ext;
                    
                    if (move_uploaded_file($file['tmp_name'], $targetDir . $filename)) {
                        $db->execute("INSERT INTO background_images (industry, filename, display_name, sort_order, is_active) VALUES (?, ?, ?, ?, 1)",
                            [$industry, $filename, $displayName ?: 'Bild ' . ($existingCount + 1), $existingCount + 1]);
                        $_SESSION['flash_success'] = 'Bild wurde hochgeladen.';
                    } else {
                        $_SESSION['flash_error'] = 'Fehler beim Hochladen.';
                    }
                }
            }
            break;
            
        case 'toggle':
            $imageId = intval($_POST['image_id'] ?? 0);
            if ($imageId) {
                $db->execute("UPDATE background_images SET is_active = NOT is_active WHERE id = ?", [$imageId]);
                $_SESSION['flash_success'] = 'Status wurde geändert.';
            }
            break;
            
        case 'set_default':
            $imageId = intval($_POST['image_id'] ?? 0);
            $industry = sanitize($_POST['industry'] ?? '');
            if ($imageId && $industry) {
                $db->execute("UPDATE background_images SET is_default = 0 WHERE industry = ?", [$industry]);
                $db->execute("UPDATE background_images SET is_default = 1 WHERE id = ?", [$imageId]);
                $_SESSION['flash_success'] = 'Standard-Bild wurde gesetzt.';
            }
            break;
            
        case 'delete':
            $imageId = intval($_POST['image_id'] ?? 0);
            if ($imageId) {
                $image = $db->fetch("SELECT * FROM background_images WHERE id = ?", [$imageId]);
                if ($image) {
                    $filePath = $uploadDir . $image['industry'] . '/' . $image['filename'];
                    if (file_exists($filePath)) unlink($filePath);
                    $db->execute("DELETE FROM background_images WHERE id = ?", [$imageId]);
                    $_SESSION['flash_success'] = 'Bild wurde gelöscht.';
                }
            }
            break;
    }
    
    header('Location: /admin/backgrounds.php');
    exit;
}

$industries = [
    'zahnarzt' => 'Zahnarzt', 'friseur' => 'Friseur', 'handwerker' => 'Handwerker',
    'coach' => 'Coach', 'restaurant' => 'Restaurant', 'fitness' => 'Fitness',
    'onlineshop' => 'Online-Shop', 'onlinemarketing' => 'Online-Marketing',
    'newsletter' => 'Newsletter', 'software' => 'Software/SaaS', 'allgemein' => 'Allgemein'
];

$images = $db->fetchAll("SELECT * FROM background_images ORDER BY industry, sort_order");
$imagesByIndustry = [];
foreach ($images as $img) $imagesByIndustry[$img['industry']][] = $img;

$stats = [
    'total' => count($images),
    'active' => $db->fetchColumn("SELECT COUNT(*) FROM background_images WHERE is_active = 1") ?? 0,
    'industries' => count(array_unique(array_column($images, 'industry'))),
];

include __DIR__ . '/../../includes/admin-header.php';
?>

<?php if (isset($_SESSION['flash_success'])): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-check-circle mr-2"></i><?= e($_SESSION['flash_success']) ?>
</div>
<?php unset($_SESSION['flash_success']); endif; ?>

<?php if (isset($_SESSION['flash_error'])): ?>
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
    <i class="fas fa-exclamation-circle mr-2"></i><?= e($_SESSION['flash_error']) ?>
</div>
<?php unset($_SESSION['flash_error']); endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4"><i class="fas fa-chart-bar text-primary-500 mr-2"></i>Übersicht</h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div><p class="text-2xl font-bold text-slate-800 dark:text-white"><?= $stats['total'] ?></p><p class="text-xs text-slate-500">Gesamt</p></div>
            <div><p class="text-2xl font-bold text-green-600"><?= $stats['active'] ?></p><p class="text-xs text-slate-500">Aktiv</p></div>
            <div><p class="text-2xl font-bold text-primary-600"><?= $stats['industries'] ?></p><p class="text-xs text-slate-500">Branchen</p></div>
        </div>
    </div>
    
    <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl p-6 border border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4"><i class="fas fa-upload text-primary-500 mr-2"></i>Neues Bild hochladen</h3>
        <form method="POST" enctype="multipart/form-data" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="action" value="upload">
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Branche</label>
                <select name="industry" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    <option value="">Wählen...</option>
                    <?php foreach ($industries as $key => $label): ?><option value="<?= $key ?>"><?= $label ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="flex-1 min-w-[150px]">
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Anzeigename</label>
                <input type="text" name="display_name" placeholder="z.B. Modern & Hell" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Bild (1920×1080, max 5MB)</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm">
            </div>
            <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all"><i class="fas fa-upload mr-2"></i>Hochladen</button>
        </form>
    </div>
</div>

<?php foreach ($industries as $industryKey => $industryLabel): ?>
<div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 mb-6">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white">
            <i class="fas fa-folder text-amber-500 mr-2"></i><?= $industryLabel ?>
            <span class="text-sm font-normal text-slate-500 ml-2">(<?= count($imagesByIndustry[$industryKey] ?? []) ?> Bilder)</span>
        </h3>
    </div>
    <div class="p-4">
        <?php if (empty($imagesByIndustry[$industryKey])): ?>
        <p class="text-slate-500 text-center py-8"><i class="fas fa-image text-3xl mb-2 block opacity-50"></i>Keine Bilder vorhanden</p>
        <?php else: ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            <?php foreach ($imagesByIndustry[$industryKey] as $img): ?>
            <div class="relative group rounded-lg overflow-hidden border-2 <?= $img['is_default'] ? 'border-primary-500' : 'border-slate-200 dark:border-slate-600' ?>">
                <div class="aspect-video bg-slate-100 dark:bg-slate-700">
                    <img src="/assets/backgrounds/<?= e($img['industry']) ?>/<?= e($img['filename']) ?>" alt="<?= e($img['display_name']) ?>" class="w-full h-full object-cover <?= !$img['is_active'] ? 'opacity-50' : '' ?>">
                </div>
                <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center gap-2">
                    <?php if (!$img['is_default']): ?>
                    <form method="POST" class="inline"><input type="hidden" name="action" value="set_default"><input type="hidden" name="image_id" value="<?= $img['id'] ?>"><input type="hidden" name="industry" value="<?= $img['industry'] ?>"><button type="submit" class="p-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg" title="Als Standard"><i class="fas fa-star"></i></button></form>
                    <?php endif; ?>
                    <form method="POST" class="inline"><input type="hidden" name="action" value="toggle"><input type="hidden" name="image_id" value="<?= $img['id'] ?>"><button type="submit" class="p-2 <?= $img['is_active'] ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700' ?> text-white rounded-lg" title="<?= $img['is_active'] ? 'Deaktivieren' : 'Aktivieren' ?>"><i class="fas <?= $img['is_active'] ? 'fa-eye-slash' : 'fa-eye' ?>"></i></button></form>
                    <form method="POST" class="inline" onsubmit="return confirm('Wirklich löschen?')"><input type="hidden" name="action" value="delete"><input type="hidden" name="image_id" value="<?= $img['id'] ?>"><button type="submit" class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg" title="Löschen"><i class="fas fa-trash"></i></button></form>
                </div>
                <div class="p-2 bg-white dark:bg-slate-800">
                    <p class="text-sm font-medium text-slate-800 dark:text-white truncate"><?= e($img['display_name']) ?></p>
                    <div class="flex items-center gap-2 mt-1">
                        <?php if ($img['is_default']): ?><span class="text-xs px-1.5 py-0.5 bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300 rounded">Standard</span><?php endif; ?>
                        <?php if (!$img['is_active']): ?><span class="text-xs px-1.5 py-0.5 bg-slate-100 text-slate-600 dark:bg-slate-600 dark:text-slate-300 rounded">Inaktiv</span><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
