<?php
/**
 * Leadbusiness - White-Label Settings (Enterprise)
 * 
 * Ermöglicht Enterprise-Kunden das Branding vollständig anzupassen
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

use Leadbusiness\Database;

// Auth prüfen
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

// Nur Enterprise
if ($customer['plan'] !== 'enterprise') {
    redirect('/dashboard/');
}

$success = '';
$error = '';

// Einstellungen speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf_token'] ?? '';
    
    if ($csrf !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Ungültige Anfrage.';
    } else {
        $action = $_POST['action'] ?? 'save';
        
        if ($action === 'save') {
            $updates = [];
            $params = [];
            
            // Branding-Einstellungen
            $brandingFields = [
                'whitelabel_enabled' => isset($_POST['whitelabel_enabled']) ? 1 : 0,
                'whitelabel_hide_powered_by' => isset($_POST['whitelabel_hide_powered_by']) ? 1 : 0,
                'whitelabel_custom_support_email' => trim($_POST['whitelabel_custom_support_email'] ?? ''),
                'whitelabel_custom_support_url' => trim($_POST['whitelabel_custom_support_url'] ?? ''),
                'whitelabel_custom_privacy_url' => trim($_POST['whitelabel_custom_privacy_url'] ?? ''),
                'whitelabel_custom_terms_url' => trim($_POST['whitelabel_custom_terms_url'] ?? ''),
                'whitelabel_custom_imprint_url' => trim($_POST['whitelabel_custom_imprint_url'] ?? ''),
                'whitelabel_custom_footer_text' => trim($_POST['whitelabel_custom_footer_text'] ?? ''),
            ];
            
            foreach ($brandingFields as $field => $value) {
                $updates[] = "$field = ?";
                $params[] = $value;
            }
            
            $params[] = $customerId;
            
            $db->query(
                "UPDATE customers SET " . implode(', ', $updates) . " WHERE id = ?",
                $params
            );
            
            $success = 'White-Label Einstellungen wurden gespeichert.';
            $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
        }
    }
}

// Spalten prüfen und erstellen falls nicht vorhanden
try {
    $db->query("SELECT whitelabel_enabled FROM customers LIMIT 1");
} catch (\Exception $e) {
    // Spalten erstellen
    $db->query("ALTER TABLE customers 
        ADD COLUMN whitelabel_enabled TINYINT(1) DEFAULT 0,
        ADD COLUMN whitelabel_hide_powered_by TINYINT(1) DEFAULT 0,
        ADD COLUMN whitelabel_custom_support_email VARCHAR(255) NULL,
        ADD COLUMN whitelabel_custom_support_url VARCHAR(500) NULL,
        ADD COLUMN whitelabel_custom_privacy_url VARCHAR(500) NULL,
        ADD COLUMN whitelabel_custom_terms_url VARCHAR(500) NULL,
        ADD COLUMN whitelabel_custom_imprint_url VARCHAR(500) NULL,
        ADD COLUMN whitelabel_custom_footer_text TEXT NULL
    ");
    $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
}

$pageTitle = 'White-Label';
include __DIR__ . '/../../includes/dashboard-header.php';
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">White-Label Branding</h1>
            <span class="px-3 py-1 bg-purple-500 text-white text-xs font-bold rounded-full">ENTERPRISE</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Passen Sie das Erscheinungsbild vollständig an Ihre Marke an</p>
    </div>
</div>

<?php if ($success): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500 text-xl"></i>
        <p class="text-green-800 dark:text-green-300"><?= e($success) ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
        <p class="text-red-800 dark:text-red-300"><?= e($error) ?></p>
    </div>
</div>
<?php endif; ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
    <input type="hidden" name="action" value="save">
    
    <div class="grid lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Hauptschalter -->
        <div class="lg:col-span-2 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fas fa-magic text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold">White-Label Modus</h2>
                        <p class="text-white/80 text-sm">Aktiviert alle White-Label Funktionen für Ihre Empfehlungsseite</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" 
                           name="whitelabel_enabled" 
                           value="1"
                           <?= !empty($customer['whitelabel_enabled']) ? 'checked' : '' ?>
                           class="sr-only peer">
                    <div class="w-14 h-7 bg-white/30 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-white/50"></div>
                </label>
            </div>
        </div>
        
        <!-- Branding-Optionen -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">
                <i class="fas fa-eye-slash text-purple-500 mr-2"></i>Sichtbarkeit
            </h3>
            
            <div class="space-y-4">
                <label class="flex items-start gap-4 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <input type="checkbox" 
                           name="whitelabel_hide_powered_by" 
                           value="1"
                           <?= !empty($customer['whitelabel_hide_powered_by']) ? 'checked' : '' ?>
                           class="w-5 h-5 text-purple-500 rounded border-slate-300 focus:ring-purple-500 mt-0.5">
                    <div>
                        <span class="font-medium text-slate-800 dark:text-white block">"Powered by Leadbusiness" ausblenden</span>
                        <span class="text-sm text-slate-500 dark:text-slate-400">Der Footer-Hinweis auf Leadbusiness wird entfernt</span>
                    </div>
                </label>
            </div>
        </div>
        
        <!-- Support & Kontakt -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">
                <i class="fas fa-headset text-purple-500 mr-2"></i>Support & Kontakt
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Support E-Mail
                    </label>
                    <input type="email" 
                           name="whitelabel_custom_support_email"
                           value="<?= e($customer['whitelabel_custom_support_email'] ?? '') ?>"
                           placeholder="support@ihre-firma.de"
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <p class="mt-1 text-xs text-slate-500">Ersetzt die Leadbusiness Support-Adresse</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Support/Hilfe URL
                    </label>
                    <input type="url" 
                           name="whitelabel_custom_support_url"
                           value="<?= e($customer['whitelabel_custom_support_url'] ?? '') ?>"
                           placeholder="https://ihre-firma.de/hilfe"
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>
        </div>
        
        <!-- Rechtliche Links -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">
                <i class="fas fa-gavel text-purple-500 mr-2"></i>Rechtliche Links
            </h3>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Datenschutzerklärung URL
                    </label>
                    <input type="url" 
                           name="whitelabel_custom_privacy_url"
                           value="<?= e($customer['whitelabel_custom_privacy_url'] ?? '') ?>"
                           placeholder="https://ihre-firma.de/datenschutz"
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        AGB URL
                    </label>
                    <input type="url" 
                           name="whitelabel_custom_terms_url"
                           value="<?= e($customer['whitelabel_custom_terms_url'] ?? '') ?>"
                           placeholder="https://ihre-firma.de/agb"
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                        Impressum URL
                    </label>
                    <input type="url" 
                           name="whitelabel_custom_imprint_url"
                           value="<?= e($customer['whitelabel_custom_imprint_url'] ?? '') ?>"
                           placeholder="https://ihre-firma.de/impressum"
                           class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>
        </div>
        
        <!-- Footer Text -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">
                <i class="fas fa-paragraph text-purple-500 mr-2"></i>Footer-Text
            </h3>
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Eigener Footer-Text
                </label>
                <textarea name="whitelabel_custom_footer_text"
                          rows="3"
                          placeholder="© 2024 Ihre Firma GmbH. Alle Rechte vorbehalten."
                          class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"><?= e($customer['whitelabel_custom_footer_text'] ?? '') ?></textarea>
                <p class="mt-1 text-xs text-slate-500">Wird im Footer der Empfehlungsseite angezeigt</p>
            </div>
        </div>
    </div>
    
    <!-- Speichern Button -->
    <div class="flex justify-end">
        <button type="submit" class="px-8 py-3 bg-purple-500 text-white rounded-xl hover:bg-purple-600 transition font-medium">
            <i class="fas fa-save mr-2"></i>Einstellungen speichern
        </button>
    </div>
</form>

<!-- Preview -->
<div class="mt-8 bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">
        <i class="fas fa-desktop text-purple-500 mr-2"></i>Vorschau
    </h3>
    
    <div class="bg-slate-100 dark:bg-slate-900 rounded-xl p-8 border border-slate-200 dark:border-slate-700">
        <!-- Simulated Footer -->
        <div class="border-t border-slate-300 dark:border-slate-600 pt-6 mt-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-slate-500 dark:text-slate-400">
                <div>
                    <?php if (!empty($customer['whitelabel_custom_footer_text'])): ?>
                    <?= e($customer['whitelabel_custom_footer_text']) ?>
                    <?php else: ?>
                    © <?= date('Y') ?> <?= e($customer['company_name']) ?>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center gap-4">
                    <?php if (!empty($customer['whitelabel_custom_privacy_url'])): ?>
                    <a href="#" class="hover:text-slate-700 dark:hover:text-slate-300">Datenschutz</a>
                    <?php endif; ?>
                    
                    <?php if (!empty($customer['whitelabel_custom_terms_url'])): ?>
                    <a href="#" class="hover:text-slate-700 dark:hover:text-slate-300">AGB</a>
                    <?php endif; ?>
                    
                    <?php if (!empty($customer['whitelabel_custom_imprint_url'])): ?>
                    <a href="#" class="hover:text-slate-700 dark:hover:text-slate-300">Impressum</a>
                    <?php endif; ?>
                </div>
                
                <?php if (empty($customer['whitelabel_hide_powered_by'])): ?>
                <div class="text-xs text-slate-400">
                    Powered by <span class="font-medium">Leadbusiness</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <p class="mt-4 text-sm text-slate-500 dark:text-slate-400 text-center">
        <i class="fas fa-info-circle mr-1"></i>
        So sieht der Footer Ihrer Empfehlungsseite aus
    </p>
</div>

<!-- Feature Overview -->
<div class="mt-8 grid md:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4">
            <i class="fas fa-globe text-purple-500 text-xl"></i>
        </div>
        <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Eigene Domain</h4>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Ihre Empfehlungsseite unter Ihrer eigenen Domain.
            <a href="/dashboard/domain.php" class="text-purple-500 hover:underline">Jetzt einrichten →</a>
        </p>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4">
            <i class="fas fa-palette text-purple-500 text-xl"></i>
        </div>
        <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Eigenes Design</h4>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Logo, Farben und Schriften anpassen.
            <a href="/dashboard/design.php" class="text-purple-500 hover:underline">Zum Design →</a>
        </p>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
        <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4">
            <i class="fas fa-envelope text-purple-500 text-xl"></i>
        </div>
        <h4 class="font-semibold text-slate-800 dark:text-white mb-2">Eigene E-Mails</h4>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            E-Mail-Versand selbst übernehmen.
            <a href="/dashboard/api.php" class="text-purple-500 hover:underline">E-Mail Kontrolle →</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
