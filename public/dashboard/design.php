<?php
/**
 * Leadbusiness - Design-Einstellungen
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

// Hintergrundbilder für die Branche laden
$backgrounds = $db->fetchAll(
    "SELECT * FROM background_images WHERE industry = ? AND is_active = 1 ORDER BY sort_order",
    [$customer['industry']]
);

// Zusätzlich allgemeine Bilder laden
$allgemeinBackgrounds = $db->fetchAll(
    "SELECT * FROM background_images WHERE industry = 'allgemein' AND is_active = 1 ORDER BY sort_order"
);

$message = '';
$error = '';

// POST: Design speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $backgroundId = intval($_POST['background_id'] ?? 0);
    $primaryColor = $_POST['primary_color'] ?? '#667eea';
    
    // Farbe validieren
    if (!preg_match('/^#[a-fA-F0-9]{6}$/', $primaryColor)) {
        $primaryColor = '#667eea';
    }
    
    $updateData = [
        'primary_color' => $primaryColor,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    // Hintergrundbild
    if ($backgroundId > 0) {
        $updateData['background_image_id'] = $backgroundId;
        $updateData['custom_background_url'] = null;
    }
    
    // Custom Background (nur Pro)
    if ($customer['plan'] === 'professional' && !empty($_FILES['custom_background']['tmp_name'])) {
        $file = $_FILES['custom_background'];
        
        // Validierung
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            $error = 'Ungültiges Bildformat. Erlaubt: JPG, PNG, WebP';
        } elseif ($file['size'] > 5 * 1024 * 1024) {
            $error = 'Bild zu groß. Maximal 5MB erlaubt.';
        } else {
            $uploadDir = __DIR__ . '/../uploads/backgrounds/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $customer['subdomain'] . '-bg-' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $filepath)) {
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
        
        // Kunden-Daten neu laden
        $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
    }
}

// Aktuelles Hintergrundbild
$backgroundService = new BackgroundService();
$currentBgUrl = $backgroundService->getCustomerBackgroundUrl($customer);

$pageTitle = 'Design';
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
    <style>
        body { font-family: 'Inter', sans-serif; }
        .bg-card { transition: all 0.2s; }
        .bg-card:hover { transform: scale(1.02); }
        .bg-card.selected { ring: 4px; ring-color: #667eea; }
    </style>
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
                <a href="/dashboard/rewards.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-gift w-5"></i><span>Belohnungen</span>
                </a>
                <a href="/dashboard/design.php" class="flex items-center gap-3 px-4 py-3 text-indigo-600 bg-indigo-50 rounded-xl font-medium">
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
                <h1 class="text-2xl font-bold text-gray-900">Design anpassen</h1>
                <p class="text-gray-500">Passen Sie das Aussehen Ihrer Empfehlungsseite an.</p>
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
                
                <!-- Preview -->
                <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Aktuelle Vorschau</h3>
                    <div class="relative rounded-xl overflow-hidden h-48" 
                         style="background-image: url('<?= htmlspecialchars($currentBgUrl) ?>'); background-size: cover; background-position: center;">
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <div class="text-center text-white">
                                <div class="text-2xl font-bold mb-2"><?= htmlspecialchars($customer['company_name']) ?></div>
                                <a href="https://<?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de" 
                                   target="_blank" 
                                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm"
                                   style="background-color: <?= htmlspecialchars($customer['primary_color']) ?>;">
                                    <i class="fas fa-external-link-alt"></i>
                                    Seite ansehen
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form method="POST" enctype="multipart/form-data">
                    
                    <!-- Hintergrundbilder -->
                    <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Hintergrundbild wählen</h3>
                        <p class="text-sm text-gray-500 mb-4">Passend zu Ihrer Branche: <?= ucfirst($customer['industry']) ?></p>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php foreach ($backgrounds as $bg): ?>
                            <label class="bg-card rounded-xl overflow-hidden cursor-pointer <?= $customer['background_image_id'] == $bg['id'] ? 'ring-4 ring-indigo-500' : '' ?>">
                                <input type="radio" name="background_id" value="<?= $bg['id'] ?>" class="sr-only"
                                       <?= $customer['background_image_id'] == $bg['id'] ? 'checked' : '' ?>>
                                <img src="/assets/backgrounds/<?= htmlspecialchars($bg['industry']) ?>/<?= htmlspecialchars($bg['filename']) ?>" 
                                     alt="<?= htmlspecialchars($bg['display_name']) ?>"
                                     class="w-full h-32 object-cover">
                                <div class="p-2 text-center text-sm text-gray-600"><?= htmlspecialchars($bg['display_name']) ?></div>
                            </label>
                            <?php endforeach; ?>
                            
                            <?php foreach ($allgemeinBackgrounds as $bg): ?>
                            <label class="bg-card rounded-xl overflow-hidden cursor-pointer <?= $customer['background_image_id'] == $bg['id'] ? 'ring-4 ring-indigo-500' : '' ?>">
                                <input type="radio" name="background_id" value="<?= $bg['id'] ?>" class="sr-only"
                                       <?= $customer['background_image_id'] == $bg['id'] ? 'checked' : '' ?>>
                                <img src="/assets/backgrounds/allgemein/<?= htmlspecialchars($bg['filename']) ?>" 
                                     alt="<?= htmlspecialchars($bg['display_name']) ?>"
                                     class="w-full h-32 object-cover">
                                <div class="p-2 text-center text-sm text-gray-600"><?= htmlspecialchars($bg['display_name']) ?></div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if ($customer['plan'] === 'professional'): ?>
                        <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-crown text-yellow-500 mr-1"></i>
                                Eigenes Hintergrundbild hochladen
                            </label>
                            <input type="file" name="custom_background" accept="image/jpeg,image/png,image/webp"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <p class="text-xs text-gray-500 mt-1">Empfohlen: 1920x1080px, max. 5MB, JPG/PNG/WebP</p>
                            <?php if ($customer['custom_background_url']): ?>
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                Eigenes Bild aktiv: <?= htmlspecialchars(basename($customer['custom_background_url'])) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <?php else: ?>
                        <div class="mt-6 p-4 bg-yellow-50 rounded-xl">
                            <p class="text-yellow-800 text-sm">
                                <i class="fas fa-lock mr-2"></i>
                                <strong>Professional-Feature:</strong> Eigene Hintergrundbilder hochladen.
                                <a href="/preise" class="underline">Upgrade</a>
                            </p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Hauptfarbe -->
                    <div class="bg-white rounded-xl p-6 shadow-sm mb-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Hauptfarbe</h3>
                        <p class="text-sm text-gray-500 mb-4">Diese Farbe wird für Buttons und Akzente verwendet.</p>
                        
                        <div class="flex items-center gap-4">
                            <input type="color" name="primary_color" value="<?= htmlspecialchars($customer['primary_color']) ?>"
                                   class="w-16 h-12 rounded cursor-pointer border-0">
                            <input type="text" value="<?= htmlspecialchars($customer['primary_color']) ?>" 
                                   class="px-4 py-2 border rounded-lg w-32" readonly>
                        </div>
                        
                        <!-- Quick Colors -->
                        <div class="flex gap-2 mt-4">
                            <?php 
                            $quickColors = ['#667eea', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6'];
                            foreach ($quickColors as $color): 
                            ?>
                            <button type="button" onclick="document.querySelector('[name=primary_color]').value='<?= $color ?>'" 
                                    class="w-8 h-8 rounded-full border-2 border-white shadow" style="background-color: <?= $color ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="px-6 py-3 bg-indigo-500 text-white rounded-xl hover:bg-indigo-600">
                        <i class="fas fa-save mr-2"></i>Änderungen speichern
                    </button>
                    
                </form>
                
            </div>
        </main>
    </div>
    
</body>
</html>
