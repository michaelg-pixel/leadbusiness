<?php
/**
 * Dashboard Module: Referral Link
 * Verfügbar für: Alle Tarife
 * Optimiert für: Online-Businesses
 */

if (!isset($customer) || !isset($campaign)) {
    return;
}

$referralUrl = "https://{$customer['subdomain']}.empfohlen.de";
$shortUrl = "{$customer['subdomain']}.empfohlen.de";

// Branchenspezifische Texte laden
$industryTexts = $dashboardLayout['customer_term'] ?? 'Kunden';
$shareOrder = $dashboardLayout['share_order'] ?? ['whatsapp', 'facebook', 'email', 'linkedin'];
?>

<div class="dashboard-module module-referral-link" data-module="referral_link">
    <div class="module-header">
        <h3 class="module-title">
            <i class="fas fa-link"></i>
            Ihr Empfehlungslink
        </h3>
    </div>
    
    <div class="module-content">
        <!-- Link Display -->
        <div class="referral-link-box relative">
            <div class="link-container flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg overflow-hidden">
                <div class="link-icon px-4 py-3 bg-gray-200 dark:bg-gray-600">
                    <i class="fas fa-globe text-gray-500 dark:text-gray-300"></i>
                </div>
                <input type="text" 
                       id="referralLinkInput"
                       value="<?= htmlspecialchars($referralUrl) ?>" 
                       readonly
                       class="flex-1 px-4 py-3 bg-transparent text-gray-800 dark:text-white font-mono text-sm outline-none">
                <button onclick="copyReferralLink()" 
                        id="copyLinkBtn"
                        class="px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white transition">
                    <i class="fas fa-copy mr-1"></i>
                    <span class="hidden sm:inline">Kopieren</span>
                </button>
            </div>
            
            <!-- Copy Success Message -->
            <div id="copySuccess" class="hidden absolute -bottom-8 left-0 right-0 text-center">
                <span class="text-sm text-green-600 dark:text-green-400">
                    <i class="fas fa-check-circle mr-1"></i>
                    Link kopiert!
                </span>
            </div>
        </div>
        
        <!-- Quick Share Buttons -->
        <div class="quick-share-section mt-6">
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                Direkt teilen:
            </p>
            
            <div class="share-buttons flex flex-wrap gap-2">
                <?php foreach (array_slice($shareOrder, 0, 4) as $platform): ?>
                    <?php
                    $shareConfig = getShareConfig($platform, $referralUrl, $customer['company_name']);
                    if (!$shareConfig) continue;
                    ?>
                    <a href="<?= htmlspecialchars($shareConfig['url']) ?>" 
                       target="_blank"
                       rel="noopener noreferrer"
                       onclick="trackShare('<?= $platform ?>')"
                       class="share-btn share-btn-<?= $platform ?> flex items-center px-4 py-2 rounded-lg text-white transition hover:opacity-90"
                       style="background-color: <?= $shareConfig['color'] ?>">
                        <i class="<?= $shareConfig['icon'] ?> mr-2"></i>
                        <?= $shareConfig['label'] ?>
                    </a>
                <?php endforeach; ?>
                
                <button onclick="openShareModal()" 
                        class="share-btn-more flex items-center px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                    <i class="fas fa-ellipsis-h mr-2"></i>
                    Mehr
                </button>
            </div>
        </div>
        
        <!-- Statistics Preview -->
        <div class="link-stats mt-6 grid grid-cols-3 gap-4">
            <div class="stat-item text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-2xl font-bold text-gray-800 dark:text-white" id="statClicks">
                    <?= number_format($campaign['total_clicks'] ?? 0) ?>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Klicks</p>
            </div>
            <div class="stat-item text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-2xl font-bold text-gray-800 dark:text-white" id="statLeads">
                    <?= number_format($customer['total_leads'] ?? 0) ?>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Empfehler</p>
            </div>
            <div class="stat-item text-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-2xl font-bold text-primary-600" id="statConversions">
                    <?= number_format($customer['total_conversions'] ?? 0) ?>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400"><?= htmlspecialchars($industryTexts) ?></p>
            </div>
        </div>
        
        <!-- Usage Tips based on industry -->
        <?php if (($dashboardLayout['business_type'] ?? 'hybrid') === 'online'): ?>
        <div class="usage-tip mt-4 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-sm">
            <i class="fas fa-lightbulb text-purple-500 mr-2"></i>
            <strong>Tipp:</strong> Fügen Sie den Link in Ihre E-Mail-Signatur, 
            Social-Media-Profile und auf Ihre Website ein.
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
/**
 * Helper: Share-Konfiguration für verschiedene Plattformen
 */
function getShareConfig($platform, $url, $companyName) {
    $encodedUrl = urlencode($url);
    $text = urlencode("Ich empfehle {$companyName}! Hier anmelden und von tollen Belohnungen profitieren:");
    
    $configs = [
        'whatsapp' => [
            'url' => "https://wa.me/?text={$text}%20{$encodedUrl}",
            'icon' => 'fab fa-whatsapp',
            'label' => 'WhatsApp',
            'color' => '#25D366'
        ],
        'facebook' => [
            'url' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
            'icon' => 'fab fa-facebook-f',
            'label' => 'Facebook',
            'color' => '#1877F2'
        ],
        'linkedin' => [
            'url' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
            'icon' => 'fab fa-linkedin-in',
            'label' => 'LinkedIn',
            'color' => '#0A66C2'
        ],
        'twitter' => [
            'url' => "https://twitter.com/intent/tweet?text={$text}&url={$encodedUrl}",
            'icon' => 'fab fa-x-twitter',
            'label' => 'X',
            'color' => '#000000'
        ],
        'email' => [
            'url' => "mailto:?subject=" . urlencode("Empfehlung: {$companyName}") . "&body={$text}%20{$encodedUrl}",
            'icon' => 'fas fa-envelope',
            'label' => 'E-Mail',
            'color' => '#6366f1'
        ],
        'sms' => [
            'url' => "sms:?body={$text}%20{$encodedUrl}",
            'icon' => 'fas fa-comment-sms',
            'label' => 'SMS',
            'color' => '#10b981'
        ],
        'telegram' => [
            'url' => "https://t.me/share/url?url={$encodedUrl}&text={$text}",
            'icon' => 'fab fa-telegram-plane',
            'label' => 'Telegram',
            'color' => '#0088cc'
        ],
        'xing' => [
            'url' => "https://www.xing.com/spi/shares/new?url={$encodedUrl}",
            'icon' => 'fab fa-xing',
            'label' => 'Xing',
            'color' => '#006567'
        ]
    ];
    
    return $configs[$platform] ?? null;
}
?>

<script>
function copyReferralLink() {
    const input = document.getElementById('referralLinkInput');
    const btn = document.getElementById('copyLinkBtn');
    const success = document.getElementById('copySuccess');
    
    // Copy to clipboard
    navigator.clipboard.writeText(input.value).then(() => {
        // Show success state
        btn.innerHTML = '<i class="fas fa-check mr-1"></i><span class="hidden sm:inline">Kopiert!</span>';
        btn.classList.add('bg-green-600');
        success.classList.remove('hidden');
        
        // Track event
        trackShare('link_copy');
        
        // Reset after 2 seconds
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-copy mr-1"></i><span class="hidden sm:inline">Kopieren</span>';
            btn.classList.remove('bg-green-600');
            success.classList.add('hidden');
        }, 2000);
    }).catch(err => {
        // Fallback for older browsers
        input.select();
        document.execCommand('copy');
        
        btn.innerHTML = '<i class="fas fa-check mr-1"></i>Kopiert!';
        setTimeout(() => {
            btn.innerHTML = '<i class="fas fa-copy mr-1"></i><span class="hidden sm:inline">Kopieren</span>';
        }, 2000);
    });
}

function trackShare(platform) {
    fetch('/api/track-share.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            customer_id: <?= (int)$customer['id'] ?>,
            platform: platform,
            source: 'dashboard'
        })
    }).catch(() => {});
    
    if (typeof gtag === 'function') {
        gtag('event', 'share', { method: platform });
    }
}

function openShareModal() {
    // Falls ein Modal existiert, öffnen
    if (typeof ShareModal !== 'undefined') {
        ShareModal.open();
    } else {
        // Fallback: Alle Share-Optionen anzeigen
        window.location.href = '/dashboard/share.php';
    }
}
</script>

<style>
.share-btn {
    min-width: 100px;
}

.share-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

@media (max-width: 640px) {
    .share-btn {
        flex: 1 1 calc(50% - 0.5rem);
        min-width: auto;
        justify-content: center;
    }
}
</style>
