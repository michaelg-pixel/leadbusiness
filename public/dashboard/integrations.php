<?php
/**
 * Leadbusiness - Dashboard Integrations
 * 
 * E-Mail-Tool und andere Integrationen verwalten
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/SetupWizard.php';
require_once __DIR__ . '/../../includes/helpers.php';

use Leadbusiness\Auth;
use Leadbusiness\Database;
use Leadbusiness\SetupWizard;

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

$setupWizard = new SetupWizard($customer);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save_email_tool') {
        $emailTool = $_POST['email_tool'] ?? '';
        $apiKey = $_POST['api_key'] ?? '';
        $apiSecret = $_POST['api_secret'] ?? '';
        $tagId = $_POST['tag_id'] ?? '';
        
        if (empty($emailTool) || $emailTool === 'none') {
            $db->execute(
                "UPDATE customers SET email_tool = NULL, email_tool_api_key = NULL, email_tool_settings = NULL WHERE id = ?",
                [$customerId]
            );
            $message = 'E-Mail-Tool-Verbindung wurde entfernt.';
            $messageType = 'success';
        } else {
            $settings = json_encode([
                'api_secret' => $apiSecret,
                'tag_id' => $tagId
            ]);
            
            $db->execute(
                "UPDATE customers SET email_tool = ?, email_tool_api_key = ?, email_tool_settings = ? WHERE id = ?",
                [$emailTool, $apiKey, $settings, $customerId]
            );
            $message = 'E-Mail-Tool wurde erfolgreich verbunden.';
            $messageType = 'success';
            
            $setupWizard->markAsReviewed('email_tool');
        }
        
        $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
    }
}

$currentTool = $customer['email_tool'] ?? '';
$toolSettings = json_decode($customer['email_tool_settings'] ?? '{}', true);

$emailTools = [
    'klicktipp' => [
        'name' => 'KlickTipp',
        'icon' => 'fa-paper-plane',
        'color' => 'orange',
        'description' => 'Beliebtes E-Mail-Marketing-Tool aus Deutschland',
        'fields' => [
            ['name' => 'api_key', 'label' => 'API-Key', 'type' => 'text', 'required' => true],
            ['name' => 'api_secret', 'label' => 'API-Secret', 'type' => 'password', 'required' => false],
        ],
        'docs_url' => 'https://www.klicktipp.com/api'
    ],
    'quentn' => [
        'name' => 'Quentn',
        'icon' => 'fa-bolt',
        'color' => 'blue',
        'description' => 'DSGVO-konformes Marketing-Automation-Tool',
        'fields' => [
            ['name' => 'api_key', 'label' => 'API-Key', 'type' => 'text', 'required' => true],
            ['name' => 'api_secret', 'label' => 'Base-URL', 'type' => 'text', 'required' => true, 'placeholder' => 'https://ihre-instanz.quentn.com'],
        ],
        'docs_url' => 'https://docs.quentn.com/api'
    ],
    'cleverreach' => [
        'name' => 'CleverReach',
        'icon' => 'fa-envelope-open-text',
        'color' => 'green',
        'description' => 'E-Mail-Marketing Made in Germany',
        'fields' => [
            ['name' => 'api_key', 'label' => 'OAuth Token', 'type' => 'text', 'required' => true],
        ],
        'docs_url' => 'https://rest.cleverreach.com/documentation/'
    ],
    'activecampaign' => [
        'name' => 'ActiveCampaign',
        'icon' => 'fa-rocket',
        'color' => 'indigo',
        'description' => 'Leistungsstarke Marketing-Automation',
        'fields' => [
            ['name' => 'api_key', 'label' => 'API-Key', 'type' => 'text', 'required' => true],
            ['name' => 'api_secret', 'label' => 'API-URL', 'type' => 'text', 'required' => true, 'placeholder' => 'https://account.api-us1.com'],
        ],
        'docs_url' => 'https://developers.activecampaign.com/'
    ],
];

$pageTitle = 'Integrationen';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<div class="max-w-4xl mx-auto">
    
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">
            <i class="fas fa-plug text-primary-500 mr-2"></i>Integrationen
        </h1>
        <p class="text-slate-500 dark:text-slate-400">
            Verbinden Sie Ihr E-Mail-Marketing-Tool, um Empfehler automatisch zu synchronisieren.
        </p>
    </div>
    
    <?php if ($message): ?>
    <div class="mb-6 p-4 rounded-xl <?= $messageType === 'success' ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-200 dark:border-red-800' ?>">
        <div class="flex items-center gap-2">
            <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
            <?= e($message) ?>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6 mb-8">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center text-blue-500 flex-shrink-0">
                <i class="fas fa-info-circle text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold text-blue-800 dark:text-blue-200 mb-1">Wie funktioniert die Integration?</h3>
                <p class="text-sm text-blue-700 dark:text-blue-300">
                    Wenn Sie ein E-Mail-Tool verbinden, werden neue Empfehler automatisch in Ihre Liste übertragen. 
                    Die Belohnungs- und Reminder-E-Mails werden weiterhin von Leadbusiness versendet.
                </p>
            </div>
        </div>
    </div>
    
    <?php if ($currentTool && isset($emailTools[$currentTool])): ?>
    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-6 mb-8">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center text-green-500 flex-shrink-0">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-green-800 dark:text-green-200">Verbunden mit <?= e($emailTools[$currentTool]['name']) ?></h3>
                    <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                        Neue Empfehler werden automatisch synchronisiert.
                    </p>
                </div>
            </div>
            <form method="POST" onsubmit="return confirm('Verbindung wirklich trennen?')">
                <input type="hidden" name="action" value="save_email_tool">
                <input type="hidden" name="email_tool" value="none">
                <button type="submit" class="text-sm text-red-500 hover:text-red-600 font-medium">
                    <i class="fas fa-unlink mr-1"></i> Trennen
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="grid sm:grid-cols-2 gap-6 mb-8">
        <?php foreach ($emailTools as $toolKey => $tool): 
            $isConnected = ($currentTool === $toolKey);
            $colorClasses = [
                'orange' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-500',
                'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-500',
                'green' => 'bg-green-100 dark:bg-green-900/30 text-green-500',
                'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-500',
            ];
        ?>
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border-2 transition-all
            <?= $isConnected ? 'border-green-500 dark:border-green-500' : 'border-slate-200 dark:border-slate-700 hover:border-primary-300 dark:hover:border-primary-600' ?>">
            
            <div class="flex items-start gap-4 mb-4">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center <?= $colorClasses[$tool['color']] ?? $colorClasses['blue'] ?>">
                    <i class="fas <?= $tool['icon'] ?> text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <h3 class="font-bold text-slate-800 dark:text-white"><?= e($tool['name']) ?></h3>
                        <?php if ($isConnected): ?>
                        <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-xs font-medium rounded-full">
                            Verbunden
                        </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400"><?= e($tool['description']) ?></p>
                </div>
            </div>
            
            <?php if (!$isConnected): ?>
            <button onclick="openToolModal('<?= $toolKey ?>')" 
                    class="w-full py-2 px-4 bg-primary-500 hover:bg-primary-600 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plug mr-1"></i> Verbinden
            </button>
            <?php else: ?>
            <button onclick="openToolModal('<?= $toolKey ?>')" 
                    class="w-full py-2 px-4 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-cog mr-1"></i> Einstellungen
            </button>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-code text-primary-500"></i>
            Webhook (für Entwickler)
        </h3>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
            Nutzen Sie Webhooks, um Empfehler-Events an Ihre eigenen Systeme zu senden.
        </p>
        
        <?php if ($customer['plan'] !== 'starter'): ?>
        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
            <code class="text-sm text-slate-700 dark:text-slate-300 break-all">
                POST https://empfehlungen.cloud/api/webhooks/<?= e($customer['api_key'] ?? 'DEIN_API_KEY') ?>
            </code>
        </div>
        <a href="/dashboard/webhooks.php" class="mt-4 text-primary-500 hover:text-primary-600 text-sm font-medium inline-block">
            Webhook konfigurieren →
        </a>
        <?php else: ?>
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
            <p class="text-sm text-amber-700 dark:text-amber-300">
                <i class="fas fa-crown text-amber-500 mr-1"></i>
                Webhooks sind im Professional-Plan verfügbar.
                <a href="/dashboard/upgrade.php" class="underline font-medium">Jetzt upgraden</a>
            </p>
        </div>
        <?php endif; ?>
    </div>
</div>

<div id="toolModal" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-md w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h3 id="modalTitle" class="font-bold text-lg text-slate-800 dark:text-white"></h3>
                <button onclick="closeToolModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <form method="POST" class="p-6">
            <input type="hidden" name="action" value="save_email_tool">
            <input type="hidden" name="email_tool" id="modalToolKey" value="">
            
            <div id="modalFields" class="space-y-4 mb-6"></div>
            <div id="modalDocsLink" class="mb-6"></div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeToolModal()" 
                        class="flex-1 py-2 px-4 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors">
                    Abbrechen
                </button>
                <button type="submit" 
                        class="flex-1 py-2 px-4 bg-primary-500 text-white rounded-lg font-medium hover:bg-primary-600 transition-colors">
                    <i class="fas fa-save mr-1"></i> Speichern
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const emailTools = <?= json_encode($emailTools) ?>;
const currentApiKey = '<?= e($customer['email_tool_api_key'] ?? '') ?>';
const currentSettings = <?= json_encode($toolSettings) ?>;

function openToolModal(toolKey) {
    const tool = emailTools[toolKey];
    if (!tool) return;
    
    document.getElementById('modalTitle').textContent = tool.name + ' verbinden';
    document.getElementById('modalToolKey').value = toolKey;
    
    let fieldsHtml = '';
    tool.fields.forEach(field => {
        const value = field.name === 'api_key' ? currentApiKey : (currentSettings[field.name] || '');
        fieldsHtml += `
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    ${field.label} ${field.required ? '*' : ''}
                </label>
                <input type="${field.type}" name="${field.name}" value="${value}"
                       ${field.required ? 'required' : ''}
                       placeholder="${field.placeholder || ''}"
                       class="w-full px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white">
            </div>
        `;
    });
    document.getElementById('modalFields').innerHTML = fieldsHtml;
    
    document.getElementById('modalDocsLink').innerHTML = `
        <a href="${tool.docs_url}" target="_blank" rel="noopener" 
           class="text-sm text-primary-500 hover:text-primary-600">
            <i class="fas fa-external-link-alt mr-1"></i> API-Dokumentation öffnen
        </a>
    `;
    
    document.getElementById('toolModal').classList.remove('hidden');
    document.getElementById('toolModal').classList.add('flex');
}

function closeToolModal() {
    document.getElementById('toolModal').classList.add('hidden');
    document.getElementById('toolModal').classList.remove('flex');
}

document.getElementById('toolModal').addEventListener('click', function(e) {
    if (e.target === this) closeToolModal();
});
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
