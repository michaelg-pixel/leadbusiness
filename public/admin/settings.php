<?php
/**
 * Admin Einstellungen
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
$pageTitle = 'Einstellungen';

// Aktionen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_settings':
            $settings = [
                'site_name' => sanitize($_POST['site_name'] ?? 'Leadbusiness'),
                'site_domain' => sanitize($_POST['site_domain'] ?? 'empfohlen.de'),
                'admin_email' => sanitize($_POST['admin_email'] ?? ''),
                'mailgun_from_name' => sanitize($_POST['mailgun_from_name'] ?? ''),
                'mailgun_from_email' => sanitize($_POST['mailgun_from_email'] ?? ''),
                'digistore_vendor_id' => sanitize($_POST['digistore_vendor_id'] ?? ''),
                'default_plan' => sanitize($_POST['default_plan'] ?? 'starter'),
                'trial_days' => intval($_POST['trial_days'] ?? 14),
            ];
            
            foreach ($settings as $key => $value) {
                $existing = $db->fetchColumn("SELECT COUNT(*) FROM system_settings WHERE setting_key = ?", [$key]);
                if ($existing) {
                    $db->execute("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?", [$value, $key]);
                } else {
                    $db->execute("INSERT INTO system_settings (setting_key, setting_value) VALUES (?, ?)", [$key, $value]);
                }
            }
            $_SESSION['flash_success'] = 'Einstellungen wurden gespeichert.';
            break;
            
        case 'change_password':
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            $admin = $db->fetch("SELECT * FROM admin_users WHERE id = ?", [$_SESSION['admin_id']]);
            
            if (!password_verify($currentPassword, $admin['password_hash'])) {
                $_SESSION['flash_error'] = 'Aktuelles Passwort ist falsch.';
            } elseif (strlen($newPassword) < 8) {
                $_SESSION['flash_error'] = 'Neues Passwort muss mindestens 8 Zeichen haben.';
            } elseif ($newPassword !== $confirmPassword) {
                $_SESSION['flash_error'] = 'Passwörter stimmen nicht überein.';
            } else {
                $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
                $db->execute("UPDATE admin_users SET password_hash = ? WHERE id = ?", [$hash, $_SESSION['admin_id']]);
                $_SESSION['flash_success'] = 'Passwort wurde geändert.';
            }
            break;
            
        case 'add_domain':
            $domain = sanitize($_POST['domain'] ?? '');
            if ($domain) {
                $existing = $db->fetchColumn("SELECT COUNT(*) FROM email_domain_blacklist WHERE domain = ?", [$domain]);
                if (!$existing) {
                    $db->execute("INSERT INTO email_domain_blacklist (domain, source) VALUES (?, 'manual')", [$domain]);
                    $_SESSION['flash_success'] = "Domain '$domain' wurde zur Blacklist hinzugefügt.";
                }
            }
            break;
            
        case 'remove_domain':
            $domainId = intval($_POST['domain_id'] ?? 0);
            if ($domainId) {
                $db->execute("DELETE FROM email_domain_blacklist WHERE id = ?", [$domainId]);
                $_SESSION['flash_success'] = 'Domain wurde entfernt.';
            }
            break;
            
        case 'unblock_ip':
            $ipId = intval($_POST['ip_id'] ?? 0);
            if ($ipId) {
                $db->execute("DELETE FROM blocked_ips WHERE id = ?", [$ipId]);
                $_SESSION['flash_success'] = 'IP wurde entsperrt.';
            }
            break;
    }
    
    header('Location: /admin/settings.php');
    exit;
}

// Einstellungen laden
$settings = [];
$rows = $db->fetchAll("SELECT setting_key, setting_value FROM system_settings");
foreach ($rows as $row) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

// E-Mail Blacklist
$blacklistedDomains = $db->fetchAll("SELECT * FROM email_domain_blacklist ORDER BY domain LIMIT 100");

// Gesperrte IPs
$blockedIPs = $db->fetchAll("SELECT * FROM blocked_ips ORDER BY created_at DESC LIMIT 50");

// Admin-Info
$adminInfo = $db->fetch("SELECT * FROM admin_users WHERE id = ?", [$_SESSION['admin_id']]);

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

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <!-- System-Einstellungen -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-cog text-primary-500 mr-2"></i>System-Einstellungen
            </h3>
        </div>
        <form method="POST" class="p-4 space-y-4">
            <input type="hidden" name="action" value="update_settings">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Site Name</label>
                    <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? 'Leadbusiness') ?>"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Domain</label>
                    <input type="text" name="site_domain" value="<?= e($settings['site_domain'] ?? 'empfohlen.de') ?>"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
            </div>
            
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Admin E-Mail</label>
                <input type="email" name="admin_email" value="<?= e($settings['admin_email'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            </div>
            
            <hr class="border-slate-200 dark:border-slate-700">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Mailgun Absendername</label>
                    <input type="text" name="mailgun_from_name" value="<?= e($settings['mailgun_from_name'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Mailgun Absender-E-Mail</label>
                    <input type="email" name="mailgun_from_email" value="<?= e($settings['mailgun_from_email'] ?? '') ?>"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
            </div>
            
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Digistore24 Vendor ID</label>
                <input type="text" name="digistore_vendor_id" value="<?= e($settings['digistore_vendor_id'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Standard-Plan</label>
                    <select name="default_plan" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                        <option value="starter" <?= ($settings['default_plan'] ?? '') === 'starter' ? 'selected' : '' ?>>Starter</option>
                        <option value="professional" <?= ($settings['default_plan'] ?? '') === 'professional' ? 'selected' : '' ?>>Professional</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Trial-Tage</label>
                    <input type="number" name="trial_days" value="<?= e($settings['trial_days'] ?? 14) ?>" min="0" max="90"
                           class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
                </div>
            </div>
            
            <button type="submit" class="w-full px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all">
                <i class="fas fa-save mr-2"></i>Einstellungen speichern
            </button>
        </form>
    </div>
    
    <!-- Passwort ändern -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-key text-amber-500 mr-2"></i>Passwort ändern
            </h3>
        </div>
        <form method="POST" class="p-4 space-y-4">
            <input type="hidden" name="action" value="change_password">
            
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Admin-Konto</label>
                <p class="text-slate-800 dark:text-white font-medium"><?= e($adminInfo['email']) ?></p>
                <p class="text-xs text-slate-500">Letzter Login: <?= $adminInfo['last_login_at'] ? date('d.m.Y H:i', strtotime($adminInfo['last_login_at'])) : 'Nie' ?></p>
            </div>
            
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Aktuelles Passwort</label>
                <input type="password" name="current_password" required
                       class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Neues Passwort</label>
                <input type="password" name="new_password" required minlength="8"
                       class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            </div>
            
            <div>
                <label class="block text-sm text-slate-600 dark:text-slate-400 mb-1">Passwort bestätigen</label>
                <input type="password" name="confirm_password" required minlength="8"
                       class="w-full px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            </div>
            
            <button type="submit" class="w-full px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-all">
                <i class="fas fa-lock mr-2"></i>Passwort ändern
            </button>
        </form>
    </div>
    
    <!-- E-Mail Blacklist -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-ban text-red-500 mr-2"></i>E-Mail Domain Blacklist
                <span class="text-sm font-normal text-slate-500">(<?= count($blacklistedDomains) ?>)</span>
            </h3>
        </div>
        
        <!-- Domain hinzufügen -->
        <form method="POST" class="p-4 border-b border-slate-200 dark:border-slate-700">
            <input type="hidden" name="action" value="add_domain">
            <div class="flex gap-2">
                <input type="text" name="domain" placeholder="beispiel-domain.de" required
                       class="flex-1 px-3 py-2 border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white text-sm">
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm">
                    <i class="fas fa-plus mr-1"></i>Hinzufügen
                </button>
            </div>
        </form>
        
        <!-- Liste -->
        <div class="max-h-64 overflow-y-auto">
            <?php foreach ($blacklistedDomains as $domain): ?>
            <div class="flex items-center justify-between px-4 py-2 hover:bg-slate-50 dark:hover:bg-slate-700/30 border-b border-slate-100 dark:border-slate-700 last:border-0">
                <span class="text-sm text-slate-800 dark:text-white font-mono"><?= e($domain['domain']) ?></span>
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="remove_domain">
                    <input type="hidden" name="domain_id" value="<?= $domain['id'] ?>">
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm" title="Entfernen">
                        <i class="fas fa-times"></i>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($blacklistedDomains)): ?>
            <p class="p-4 text-center text-slate-500 text-sm">Keine Domains blockiert</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Gesperrte IPs -->
    <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
        <div class="p-4 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-semibold text-slate-800 dark:text-white">
                <i class="fas fa-shield text-purple-500 mr-2"></i>Gesperrte IPs
                <span class="text-sm font-normal text-slate-500">(<?= count($blockedIPs) ?>)</span>
            </h3>
        </div>
        
        <div class="max-h-80 overflow-y-auto">
            <?php foreach ($blockedIPs as $ip): ?>
            <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/30 border-b border-slate-100 dark:border-slate-700 last:border-0">
                <div>
                    <p class="text-sm text-slate-800 dark:text-white font-mono"><?= e(substr($ip['ip_hash'], 0, 20)) ?>...</p>
                    <p class="text-xs text-slate-500">
                        <?= ucfirst($ip['reason']) ?> &bull; 
                        <?= $ip['blocked_until'] ? 'Bis ' . date('d.m.Y', strtotime($ip['blocked_until'])) : 'Permanent' ?>
                    </p>
                </div>
                <form method="POST" class="inline">
                    <input type="hidden" name="action" value="unblock_ip">
                    <input type="hidden" name="ip_id" value="<?= $ip['id'] ?>">
                    <button type="submit" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-xs" 
                            onclick="return confirm('IP entsperren?')">
                        <i class="fas fa-unlock mr-1"></i>Entsperren
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($blockedIPs)): ?>
            <p class="p-4 text-center text-slate-500 text-sm">Keine IPs gesperrt</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- System Info -->
<div class="mt-6 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
    <div class="p-4 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white">
            <i class="fas fa-info-circle text-primary-500 mr-2"></i>System-Information
        </h3>
    </div>
    <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
        <div>
            <p class="text-slate-500">PHP Version</p>
            <p class="text-slate-800 dark:text-white font-mono"><?= phpversion() ?></p>
        </div>
        <div>
            <p class="text-slate-500">MySQL Version</p>
            <p class="text-slate-800 dark:text-white font-mono"><?= $db->fetchColumn("SELECT VERSION()") ?></p>
        </div>
        <div>
            <p class="text-slate-500">Server</p>
            <p class="text-slate-800 dark:text-white font-mono"><?= php_uname('s') ?></p>
        </div>
        <div>
            <p class="text-slate-500">Zeitzone</p>
            <p class="text-slate-800 dark:text-white font-mono"><?= date_default_timezone_get() ?></p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../includes/admin-footer.php'; ?>
