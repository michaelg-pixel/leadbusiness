<?php
/**
 * Leadbusiness - Dashboard Share Page
 * 
 * Hilft Kunden, ihren Empfehlungslink zu teilen
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

// Setup-Wizard: Schritt als erledigt markieren
$setupWizard = new \Leadbusiness\SetupWizard($customer);
if (isset($_GET['mark_done'])) {
    $setupWizard->markAsReviewed('share');
    redirect('/dashboard/share.php');
}

$referralUrl = 'https://' . $customer['subdomain'] . '.empfehlungen.cloud';
$pageTitle = 'Link teilen';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<div class="max-w-4xl mx-auto">
    
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">
            <i class="fas fa-share-alt text-primary-500 mr-2"></i>Empfehlungslink teilen
        </h1>
        <p class="text-slate-500 dark:text-slate-400">
            Teilen Sie Ihren Link mit Ihren Kunden, um neue Empfehler zu gewinnen.
        </p>
    </div>
    
    <!-- Main Link Card -->
    <div class="bg-gradient-to-br from-primary-500 to-purple-600 rounded-2xl p-8 text-white mb-8 relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -right-20 -top-20 w-60 h-60 bg-white rounded-full"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-white rounded-full"></div>
        </div>
        
        <div class="relative">
            <h2 class="text-lg font-semibold mb-4">Ihr persönlicher Empfehlungslink</h2>
            
            <div class="bg-white/20 backdrop-blur rounded-xl p-4 flex items-center justify-between gap-4 mb-6">
                <code class="text-lg font-mono truncate flex-1"><?= e($referralUrl) ?></code>
                <button onclick="copyLink()" id="copyBtn"
                        class="px-4 py-2 bg-white text-primary-600 rounded-lg font-medium hover:bg-white/90 transition-colors flex items-center gap-2">
                    <i class="fas fa-copy" id="copyIcon"></i>
                    <span id="copyText">Kopieren</span>
                </button>
            </div>
            
            <!-- Quick Share Buttons -->
            <div class="flex flex-wrap gap-3">
                <a href="https://wa.me/?text=<?= urlencode('Ich kann dir ' . $customer['company_name'] . ' empfehlen! Nutze meinen Link: ' . $referralUrl) ?>" 
                   target="_blank" rel="noopener"
                   class="px-4 py-2 bg-green-500 hover:bg-green-600 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </a>
                <a href="mailto:?subject=<?= urlencode('Empfehlung: ' . $customer['company_name']) ?>&body=<?= urlencode('Hallo, ich kann dir ' . $customer['company_name'] . ' empfehlen! Nutze meinen Link: ' . $referralUrl) ?>"
                   class="px-4 py-2 bg-blue-500 hover:bg-blue-600 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="fas fa-envelope"></i> E-Mail
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($referralUrl) ?>" 
                   target="_blank" rel="noopener"
                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="fab fa-facebook"></i> Facebook
                </a>
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($referralUrl) ?>" 
                   target="_blank" rel="noopener"
                   class="px-4 py-2 bg-blue-700 hover:bg-blue-800 rounded-lg flex items-center gap-2 transition-colors">
                    <i class="fab fa-linkedin"></i> LinkedIn
                </a>
            </div>
        </div>
    </div>
    
    <!-- QR Code & Preview -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">
        
        <!-- QR Code -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-qrcode text-primary-500"></i>
                QR-Code
            </h3>
            <div class="bg-white p-4 rounded-xl inline-block mb-4">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=<?= urlencode($referralUrl) ?>" 
                     alt="QR Code" class="w-48 h-48">
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">
                Drucken Sie den QR-Code aus und platzieren Sie ihn in Ihrem Geschäft, auf Visitenkarten oder Flyern.
            </p>
            <a href="https://api.qrserver.com/v1/create-qr-code/?size=500x500&format=png&data=<?= urlencode($referralUrl) ?>" 
               download="qr-code-<?= e($customer['subdomain']) ?>.png"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600 transition-colors text-sm font-medium">
                <i class="fas fa-download"></i> QR-Code herunterladen
            </a>
        </div>
        
        <!-- Preview -->
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                <i class="fas fa-eye text-primary-500"></i>
                Vorschau Ihrer Seite
            </h3>
            <div class="bg-slate-100 dark:bg-slate-700 rounded-xl overflow-hidden mb-4">
                <div class="bg-slate-200 dark:bg-slate-600 px-3 py-2 flex items-center gap-2">
                    <div class="flex gap-1">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                    </div>
                    <div class="flex-1 bg-white dark:bg-slate-500 rounded px-2 py-1 text-xs text-slate-500 dark:text-slate-300 truncate">
                        <?= e($referralUrl) ?>
                    </div>
                </div>
                <div class="aspect-video bg-gradient-to-br from-primary-100 to-purple-100 dark:from-primary-900/30 dark:to-purple-900/30 flex items-center justify-center">
                    <div class="text-center">
                        <?php if ($customer['logo_url']): ?>
                        <img src="<?= e($customer['logo_url']) ?>" alt="Logo" class="h-12 mx-auto mb-2">
                        <?php endif; ?>
                        <p class="text-slate-600 dark:text-slate-400 font-medium"><?= e($customer['company_name']) ?></p>
                    </div>
                </div>
            </div>
            <a href="<?= e($referralUrl) ?>" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white rounded-lg transition-colors text-sm font-medium">
                <i class="fas fa-external-link-alt"></i> Seite öffnen
            </a>
        </div>
    </div>
    
    <!-- Sharing Tips -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700 mb-8">
        <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-lightbulb text-amber-500"></i>
            Tipps für mehr Empfehlungen
        </h3>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-blue-500 mb-3">
                    <i class="fas fa-envelope"></i>
                </div>
                <h4 class="font-semibold text-slate-800 dark:text-white text-sm mb-1">E-Mail-Signatur</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Fügen Sie Ihren Empfehlungslink in Ihre E-Mail-Signatur ein.
                </p>
            </div>
            
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-green-500 mb-3">
                    <i class="fas fa-receipt"></i>
                </div>
                <h4 class="font-semibold text-slate-800 dark:text-white text-sm mb-1">Nach dem Kauf</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Informieren Sie zufriedene Kunden direkt nach dem Kauf.
                </p>
            </div>
            
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center text-purple-500 mb-3">
                    <i class="fas fa-share-alt"></i>
                </div>
                <h4 class="font-semibold text-slate-800 dark:text-white text-sm mb-1">Social Media</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Teilen Sie das Programm regelmäßig in Ihren sozialen Netzwerken.
                </p>
            </div>
            
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center text-amber-500 mb-3">
                    <i class="fas fa-store"></i>
                </div>
                <h4 class="font-semibold text-slate-800 dark:text-white text-sm mb-1">Im Geschäft</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Hängen Sie den QR-Code gut sichtbar in Ihrem Geschäft auf.
                </p>
            </div>
            
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-10 h-10 bg-pink-100 dark:bg-pink-900/30 rounded-lg flex items-center justify-center text-pink-500 mb-3">
                    <i class="fas fa-gift"></i>
                </div>
                <h4 class="font-semibold text-slate-800 dark:text-white text-sm mb-1">Belohnungen betonen</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Kommunizieren Sie die attraktiven Belohnungen für Empfehler.
                </p>
            </div>
            
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center text-cyan-500 mb-3">
                    <i class="fas fa-comments"></i>
                </div>
                <h4 class="font-semibold text-slate-800 dark:text-white text-sm mb-1">Persönlich ansprechen</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Bitten Sie zufriedene Stammkunden persönlich um eine Empfehlung.
                </p>
            </div>
        </div>
    </div>
    
    <!-- Text Templates -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700">
        <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-file-alt text-primary-500"></i>
            Vorlagen zum Teilen
        </h3>
        
        <div class="space-y-4">
            <!-- Template 1 -->
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="flex items-start justify-between gap-4 mb-2">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">WhatsApp / SMS</span>
                    <button onclick="copyTemplate(this, 'template1')" class="text-primary-500 hover:text-primary-600 text-sm">
                        <i class="fas fa-copy"></i> Kopieren
                    </button>
                </div>
                <p id="template1" class="text-sm text-slate-700 dark:text-slate-300">
                    Hey! 👋 Ich kann dir <?= e($customer['company_name']) ?> nur empfehlen. Wenn du dich über meinen Link anmeldest, bekommen wir beide eine Belohnung: <?= $referralUrl ?>
                </p>
            </div>
            
            <!-- Template 2 -->
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="flex items-start justify-between gap-4 mb-2">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">E-Mail</span>
                    <button onclick="copyTemplate(this, 'template2')" class="text-primary-500 hover:text-primary-600 text-sm">
                        <i class="fas fa-copy"></i> Kopieren
                    </button>
                </div>
                <p id="template2" class="text-sm text-slate-700 dark:text-slate-300">
                    Hallo,<br><br>ich möchte dir <?= e($customer['company_name']) ?> empfehlen. Ich bin sehr zufrieden und denke, dass es auch für dich interessant sein könnte.<br><br>Wenn du dich über meinen persönlichen Link anmeldest, erhalten wir beide eine Belohnung:<br><?= $referralUrl ?><br><br>Viele Grüße
                </p>
            </div>
            
            <!-- Template 3 -->
            <div class="p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="flex items-start justify-between gap-4 mb-2">
                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase">Social Media</span>
                    <button onclick="copyTemplate(this, 'template3')" class="text-primary-500 hover:text-primary-600 text-sm">
                        <i class="fas fa-copy"></i> Kopieren
                    </button>
                </div>
                <p id="template3" class="text-sm text-slate-700 dark:text-slate-300">
                    🌟 Empfehlung: Ich bin großer Fan von <?= e($customer['company_name']) ?> und kann es nur weiterempfehlen! Über meinen Link gibt's für euch sogar eine Belohnung: <?= $referralUrl ?> #Empfehlung #<?= preg_replace('/[^a-zA-Z0-9]/', '', $customer['company_name']) ?>
                </p>
            </div>
        </div>
    </div>
    
</div>

<script>
function copyLink() {
    const url = '<?= e($referralUrl) ?>';
    navigator.clipboard.writeText(url).then(() => {
        document.getElementById('copyIcon').className = 'fas fa-check';
        document.getElementById('copyText').textContent = 'Kopiert!';
        setTimeout(() => {
            document.getElementById('copyIcon').className = 'fas fa-copy';
            document.getElementById('copyText').textContent = 'Kopieren';
        }, 2000);
    });
}

function copyTemplate(btn, templateId) {
    const text = document.getElementById(templateId).innerText;
    navigator.clipboard.writeText(text).then(() => {
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Kopiert!';
        setTimeout(() => {
            btn.innerHTML = originalHtml;
        }, 2000);
    });
}
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
