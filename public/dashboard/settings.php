<?php
/**
 * Leadbusiness - Einstellungen
 * Mit Logo-Upload und Setup-Wizard Integration
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/SetupWizard.php';
require_once __DIR__ . '/../../includes/helpers.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

$setupWizard = new \Leadbusiness\SetupWizard($customer);

$message = '';
$error = '';

// POST: Einstellungen speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $companyName = trim($_POST['company_name'] ?? '');
        $contactName = trim($_POST['contact_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $emailSenderName = trim($_POST['email_sender_name'] ?? '');
        
        if (empty($companyName) || empty($contactName)) {
            $error = 'Firmenname und Ansprechpartner sind Pflichtfelder.';
        } else {
            $db->update('customers', [
                'company_name' => $companyName,
                'contact_name' => $contactName,
                'phone' => $phone,
                'website' => $website,
                'email_sender_name' => $emailSenderName ?: $companyName,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$customerId]);
            
            $message = 'Profil aktualisiert!';
            $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
        }
    }
    
    if ($action === 'upload_logo') {
        if (!empty($_FILES['logo']['tmp_name'])) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!in_array($_FILES['logo']['type'], $allowedTypes)) {
                $error = 'Ungültiges Bildformat. Erlaubt: JPG, PNG, GIF, WebP';
            } elseif ($_FILES['logo']['size'] > 2 * 1024 * 1024) {
                $error = 'Logo zu groß. Maximal 2MB erlaubt.';
            } else {
                $uploadDir = __DIR__ . '/../uploads/logos/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                
                $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $filename = $customer['subdomain'] . '-logo-' . time() . '.' . $extension;
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadDir . $filename)) {
                    // Altes Logo löschen
                    if ($customer['logo_url'] && file_exists(__DIR__ . '/..' . $customer['logo_url'])) {
                        @unlink(__DIR__ . '/..' . $customer['logo_url']);
                    }
                    
                    $logoUrl = '/uploads/logos/' . $filename;
                    $db->update('customers', [
                        'logo_url' => $logoUrl,
                        'updated_at' => date('Y-m-d H:i:s')
                    ], 'id = ?', [$customerId]);
                    
                    $message = 'Logo erfolgreich hochgeladen!';
                    $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
                } else {
                    $error = 'Fehler beim Hochladen des Logos.';
                }
            }
        } else {
            $error = 'Bitte wählen Sie ein Logo aus.';
        }
    }
    
    if ($action === 'delete_logo') {
        if ($customer['logo_url'] && file_exists(__DIR__ . '/..' . $customer['logo_url'])) {
            @unlink(__DIR__ . '/..' . $customer['logo_url']);
        }
        
        $db->update('customers', [
            'logo_url' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customerId]);
        
        $message = 'Logo wurde entfernt.';
        $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
    }
    
    if ($action === 'update_address') {
        $street = trim($_POST['address_street'] ?? '');
        $zip = trim($_POST['address_zip'] ?? '');
        $city = trim($_POST['address_city'] ?? '');
        $taxId = trim($_POST['tax_id'] ?? '');
        
        if (empty($street) || empty($zip) || empty($city)) {
            $error = 'Adresse ist unvollständig.';
        } else {
            $db->update('customers', [
                'address_street' => $street,
                'address_zip' => $zip,
                'address_city' => $city,
                'tax_id' => $taxId,
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$customerId]);
            
            $message = 'Adresse aktualisiert!';
            $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
        }
    }
    
    if ($action === 'update_features') {
        $liveCounter = isset($_POST['live_counter_enabled']);
        $leaderboard = isset($_POST['leaderboard_enabled']);
        $weeklyDigest = isset($_POST['weekly_digest_enabled']);
        
        $db->update('customers', [
            'live_counter_enabled' => $liveCounter ? 1 : 0,
            'leaderboard_enabled' => $leaderboard ? 1 : 0,
            'weekly_digest_enabled' => ($weeklyDigest && $customer['plan'] !== 'starter') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ], 'id = ?', [$customerId]);
        
        $message = 'Einstellungen gespeichert!';
        $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
    }
    
    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (!password_verify($currentPassword, $customer['password_hash'])) {
            $error = 'Aktuelles Passwort ist falsch.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'Neues Passwort muss mindestens 8 Zeichen haben.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Passwörter stimmen nicht überein.';
        } else {
            $db->update('customers', [
                'password_hash' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]),
                'updated_at' => date('Y-m-d H:i:s')
            ], 'id = ?', [$customerId]);
            
            $message = 'Passwort geändert!';
        }
    }
    
    if ($action === 'show_wizard') {
        $setupWizard->show();
        $message = 'Einrichtungs-Checkliste wird wieder angezeigt.';
    }
}

$pageTitle = 'Einstellungen';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<div class="max-w-3xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">
            <i class="fas fa-cog text-primary-500 mr-2"></i>Einstellungen
        </h1>
        <p class="text-slate-500 dark:text-slate-400">
            Verwalten Sie Ihre Unternehmensdaten, Logo und Einstellungen.
        </p>
    </div>
    
    <?php if ($message): ?>
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
        <i class="fas fa-check-circle"></i><?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
        <i class="fas fa-exclamation-circle"></i><?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    
    <!-- Plan Info -->
    <div class="bg-gradient-to-r from-primary-500 to-purple-600 rounded-2xl p-6 text-white mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-bold text-lg"><?= ucfirst($customer['plan']) ?>-Plan</h3>
                <p class="text-white/80 text-sm">
                    <?php if ($customer['subscription_ends_at']): ?>
                    Aktiv bis: <?= date('d.m.Y', strtotime($customer['subscription_ends_at'])) ?>
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($customer['plan'] === 'starter'): ?>
            <a href="/dashboard/upgrade.php" class="px-4 py-2 bg-white text-primary-600 rounded-lg font-medium hover:bg-gray-100 transition-colors">
                <i class="fas fa-crown mr-1"></i> Upgrade
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Logo Upload -->
    <div id="logo" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-image text-primary-500"></i>
            Logo
            <?php if (empty($customer['logo_url'])): ?>
            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs font-medium rounded-full">
                Nicht hochgeladen
            </span>
            <?php endif; ?>
        </h3>
        
        <div class="flex items-start gap-6">
            <!-- Logo Preview -->
            <div class="flex-shrink-0">
                <div class="w-24 h-24 bg-slate-100 dark:bg-slate-700 rounded-xl flex items-center justify-center overflow-hidden">
                    <?php if ($customer['logo_url']): ?>
                    <img src="<?= htmlspecialchars($customer['logo_url']) ?>" alt="Logo" class="max-w-full max-h-full object-contain">
                    <?php else: ?>
                    <i class="fas fa-image text-slate-300 dark:text-slate-500 text-3xl"></i>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Upload Form -->
            <div class="flex-1">
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="upload_logo">
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Logo hochladen
                        </label>
                        <input type="file" name="logo" accept="image/*" 
                               class="block w-full text-sm text-slate-500 dark:text-slate-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-lg file:border-0
                                      file:text-sm file:font-medium
                                      file:bg-primary-50 dark:file:bg-primary-900/30 file:text-primary-600 dark:file:text-primary-400
                                      hover:file:bg-primary-100 dark:hover:file:bg-primary-900/50
                                      file:cursor-pointer">
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">JPG, PNG, GIF oder WebP. Max. 2MB.</p>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors text-sm font-medium">
                            <i class="fas fa-upload mr-1"></i> Hochladen
                        </button>
                        <?php if ($customer['logo_url']): ?>
                        <button type="submit" name="action" value="delete_logo" 
                                onclick="return confirm('Logo wirklich löschen?')"
                                class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                            <i class="fas fa-trash mr-1"></i> Entfernen
                        </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Profil -->
    <div id="website" class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-building text-primary-500"></i>
            Unternehmensdaten
        </h3>
        
        <form method="POST">
            <input type="hidden" name="action" value="update_profile">
            
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Firmenname *</label>
                    <input type="text" name="company_name" value="<?= htmlspecialchars($customer['company_name']) ?>" required
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Ansprechpartner *</label>
                    <input type="text" name="contact_name" value="<?= htmlspecialchars($customer['contact_name']) ?>" required
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
                <div id="phone">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Telefon
                        <?php if (empty($customer['phone'])): ?>
                        <span class="text-xs text-amber-500 ml-1">(optional)</span>
                        <?php endif; ?>
                    </label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white"
                           placeholder="+49 123 456789">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        Website
                        <?php if (empty($customer['website'])): ?>
                        <span class="text-xs text-amber-500 ml-1">(optional)</span>
                        <?php endif; ?>
                    </label>
                    <input type="url" name="website" value="<?= htmlspecialchars($customer['website'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white"
                           placeholder="https://www.ihre-website.de">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">E-Mail-Absendername</label>
                    <input type="text" name="email_sender_name" value="<?= htmlspecialchars($customer['email_sender_name'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white" 
                           placeholder="z.B. Zahnarztpraxis Dr. Müller">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Dieser Name erscheint als Absender bei E-Mails an Ihre Empfehler.</p>
                </div>
            </div>
            
            <button type="submit" class="mt-4 px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors font-medium">
                <i class="fas fa-save mr-1"></i> Speichern
            </button>
        </form>
    </div>
    
    <!-- Impressum -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-map-marker-alt text-primary-500"></i>
            Impressumsadresse
        </h3>
        
        <form method="POST">
            <input type="hidden" name="action" value="update_address">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Straße & Hausnummer *</label>
                    <input type="text" name="address_street" value="<?= htmlspecialchars($customer['address_street']) ?>" required
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">PLZ *</label>
                        <input type="text" name="address_zip" value="<?= htmlspecialchars($customer['address_zip']) ?>" required
                               class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Stadt *</label>
                        <input type="text" name="address_city" value="<?= htmlspecialchars($customer['address_city']) ?>" required
                               class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">USt-IdNr. (optional)</label>
                    <input type="text" name="tax_id" value="<?= htmlspecialchars($customer['tax_id'] ?? '') ?>"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white" 
                           placeholder="DE123456789">
                </div>
            </div>
            
            <button type="submit" class="mt-4 px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors font-medium">
                <i class="fas fa-save mr-1"></i> Speichern
            </button>
        </form>
    </div>
    
    <!-- Features -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-sliders-h text-primary-500"></i>
            Funktionen
        </h3>
        
        <form method="POST">
            <input type="hidden" name="action" value="update_features">
            
            <div class="space-y-4">
                <label class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl cursor-pointer">
                    <div>
                        <div class="font-medium text-slate-800 dark:text-white">Live-Counter</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">"47 Personen nehmen bereits teil"</div>
                    </div>
                    <input type="checkbox" name="live_counter_enabled" <?= $customer['live_counter_enabled'] ? 'checked' : '' ?>
                           class="w-5 h-5 text-primary-500 rounded border-slate-300 dark:border-slate-600">
                </label>
                
                <label class="flex items-center justify-between p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl cursor-pointer">
                    <div>
                        <div class="font-medium text-slate-800 dark:text-white">Leaderboard</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">Top Empfehler öffentlich anzeigen</div>
                    </div>
                    <input type="checkbox" name="leaderboard_enabled" <?= $customer['leaderboard_enabled'] ? 'checked' : '' ?>
                           class="w-5 h-5 text-primary-500 rounded border-slate-300 dark:border-slate-600">
                </label>
                
                <label class="flex items-center justify-between p-4 rounded-xl cursor-pointer <?= $customer['plan'] === 'starter' ? 'bg-slate-100 dark:bg-slate-700 opacity-60' : 'bg-slate-50 dark:bg-slate-700/50' ?>">
                    <div>
                        <div class="font-medium text-slate-800 dark:text-white flex items-center gap-2">
                            Wöchentlicher Digest
                            <?php if ($customer['plan'] === 'starter'): ?>
                            <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 text-xs rounded-full">Pro</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">Wöchentliche E-Mail mit Statistiken an aktive Empfehler</div>
                    </div>
                    <input type="checkbox" name="weekly_digest_enabled" 
                           <?= $customer['weekly_digest_enabled'] ? 'checked' : '' ?>
                           <?= $customer['plan'] === 'starter' ? 'disabled' : '' ?>
                           class="w-5 h-5 text-primary-500 rounded border-slate-300 dark:border-slate-600">
                </label>
            </div>
            
            <button type="submit" class="mt-4 px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors font-medium">
                <i class="fas fa-save mr-1"></i> Speichern
            </button>
        </form>
    </div>
    
    <!-- Passwort -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-lock text-primary-500"></i>
            Passwort ändern
        </h3>
        
        <form method="POST">
            <input type="hidden" name="action" value="change_password">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Aktuelles Passwort</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Neues Passwort</label>
                    <input type="password" name="new_password" required minlength="8"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Passwort bestätigen</label>
                    <input type="password" name="confirm_password" required
                           class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
            </div>
            
            <button type="submit" class="mt-4 px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-colors font-medium">
                <i class="fas fa-key mr-1"></i> Passwort ändern
            </button>
        </form>
    </div>
    
    <!-- Setup Wizard wieder anzeigen -->
    <?php if ($setupWizard->isHidden()): ?>
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-rocket text-primary-500"></i>
            Einrichtungs-Checkliste
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            Sie haben die Einrichtungs-Checkliste ausgeblendet. Möchten Sie sie wieder anzeigen?
        </p>
        <form method="POST">
            <input type="hidden" name="action" value="show_wizard">
            <button type="submit" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors text-sm font-medium">
                <i class="fas fa-eye mr-1"></i> Checkliste wieder anzeigen
            </button>
        </form>
    </div>
    <?php endif; ?>
    
    <!-- Subdomain Info -->
    <div class="bg-slate-100 dark:bg-slate-700/50 rounded-2xl p-6">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-2 flex items-center gap-2">
            <i class="fas fa-globe text-primary-500"></i>
            Ihre Empfehlungsseite
        </h3>
        <p class="text-slate-600 dark:text-slate-300 mb-2">
            <a href="https://<?= htmlspecialchars($customer['subdomain']) ?>.empfehlungen.cloud" target="_blank" class="text-primary-500 hover:underline">
                <?= htmlspecialchars($customer['subdomain']) ?>.empfehlungen.cloud
                <i class="fas fa-external-link-alt text-xs ml-1"></i>
            </a>
        </p>
        <p class="text-sm text-slate-500 dark:text-slate-400">Die Subdomain kann nach der Einrichtung nicht mehr geändert werden.</p>
    </div>
    
</div>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
