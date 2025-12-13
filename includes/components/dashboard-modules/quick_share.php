<?php
/**
 * Dashboard Module: Quick Share
 * Verfügbar für: Alle Tarife
 * Zeigt die wichtigsten Share-Buttons basierend auf Branche
 */

if (!isset($customer) || !isset($campaign)) {
    return;
}

$referralUrl = "https://{$customer['subdomain']}.empfehlungen.cloud";
$companyName = htmlspecialchars($customer['company_name']);

// Share-Reihenfolge aus Layout oder Default
$shareOrder = $dashboardLayout['share_order'] ?? ['whatsapp', 'facebook', 'email', 'linkedin', 'sms', 'link_copy'];
$businessType = $dashboardLayout['business_type'] ?? 'hybrid';

// Alle Share-Plattformen definieren
$allPlatforms = [
    'whatsapp' => [
        'name' => 'WhatsApp',
        'icon' => 'fab fa-whatsapp',
        'color' => '#25D366',
        'url' => "https://wa.me/?text=" . urlencode("Ich empfehle {$customer['company_name']}! 🎁 Hier anmelden und Belohnung sichern: {$referralUrl}")
    ],
    'facebook' => [
        'name' => 'Facebook',
        'icon' => 'fab fa-facebook-f',
        'color' => '#1877F2',
        'url' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($referralUrl)
    ],
    'linkedin' => [
        'name' => 'LinkedIn',
        'icon' => 'fab fa-linkedin-in',
        'color' => '#0A66C2',
        'url' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($referralUrl)
    ],
    'twitter' => [
        'name' => 'X',
        'icon' => 'fab fa-x-twitter',
        'color' => '#000000',
        'url' => "https://twitter.com/intent/tweet?text=" . urlencode("Ich empfehle {$customer['company_name']}! 🎁") . "&url=" . urlencode($referralUrl)
    ],
    'email' => [
        'name' => 'E-Mail',
        'icon' => 'fas fa-envelope',
        'color' => '#6366f1',
        'url' => "mailto:?subject=" . urlencode("Empfehlung: {$customer['company_name']}") . "&body=" . urlencode("Hallo,\n\nich möchte dir {$customer['company_name']} empfehlen!\n\nMelde dich hier an und sichere dir eine tolle Belohnung:\n{$referralUrl}\n\nViele Grüße")
    ],
    'sms' => [
        'name' => 'SMS',
        'icon' => 'fas fa-comment-sms',
        'color' => '#10b981',
        'url' => "sms:?body=" . urlencode("Hey! Ich empfehle dir {$customer['company_name']}. Hier anmelden: {$referralUrl}")
    ],
    'telegram' => [
        'name' => 'Telegram',
        'icon' => 'fab fa-telegram-plane',
        'color' => '#0088cc',
        'url' => "https://t.me/share/url?url=" . urlencode($referralUrl) . "&text=" . urlencode("Ich empfehle {$customer['company_name']}! 🎁")
    ],
    'xing' => [
        'name' => 'Xing',
        'icon' => 'fab fa-xing',
        'color' => '#006567',
        'url' => "https://www.xing.com/spi/shares/new?url=" . urlencode($referralUrl)
    ],
    'link_copy' => [
        'name' => 'Link kopieren',
        'icon' => 'fas fa-copy',
        'color' => '#6b7280',
        'url' => '#',
        'onclick' => 'copyShareLink()'
    ]
];

// Nur die ersten 5 + "Mehr" Button anzeigen
$visiblePlatforms = array_slice($shareOrder, 0, 5);
$hasMore = count($shareOrder) > 5;
?>

<div class="dashboard-module module-quick-share" data-module="quick_share">
    <div class="module-header">
        <h3 class="module-title">
            <i class="fas fa-share-alt"></i>
            Schnell teilen
        </h3>
    </div>
    
    <div class="module-content">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            <?php if ($businessType === 'offline'): ?>
                Teilen Sie Ihr Empfehlungsprogramm mit zufriedenen Kunden:
            <?php elseif ($businessType === 'online'): ?>
                Verbreiten Sie Ihren Empfehlungslink in Ihren Netzwerken:
            <?php else: ?>
                Teilen Sie Ihr Empfehlungsprogramm:
            <?php endif; ?>
        </p>
        
        <!-- Share Buttons Grid -->
        <div class="share-buttons-grid grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php foreach ($visiblePlatforms as $platformKey): ?>
                <?php 
                if (!isset($allPlatforms[$platformKey])) continue;
                $platform = $allPlatforms[$platformKey];
                $isLinkCopy = $platformKey === 'link_copy';
                ?>
                
                <a href="<?= $isLinkCopy ? 'javascript:void(0)' : htmlspecialchars($platform['url']) ?>"
                   <?= $isLinkCopy ? '' : 'target="_blank" rel="noopener noreferrer"' ?>
                   <?= isset($platform['onclick']) ? "onclick=\"{$platform['onclick']}; return false;\"" : "onclick=\"trackShareClick('{$platformKey}')\"" ?>
                   class="share-button flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-white font-medium transition-all hover:opacity-90 hover:-translate-y-0.5 hover:shadow-lg"
                   style="background-color: <?= $platform['color'] ?>"
                   id="shareBtn_<?= $platformKey ?>">
                    <i class="<?= $platform['icon'] ?> text-lg"></i>
                    <span class="text-sm"><?= $platform['name'] ?></span>
                </a>
            <?php endforeach; ?>
            
            <?php if ($hasMore): ?>
            <button onclick="openAllShareOptions()"
                    class="share-button flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium transition-all hover:bg-gray-300 dark:hover:bg-gray-500">
                <i class="fas fa-ellipsis-h text-lg"></i>
                <span class="text-sm">Mehr</span>
            </button>
            <?php endif; ?>
        </div>
        
        <!-- Referral Link (kompakt) -->
        <div class="referral-link-compact mt-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-xl">
            <div class="flex items-center gap-2">
                <i class="fas fa-link text-gray-400"></i>
                <input type="text" 
                       value="<?= htmlspecialchars($referralUrl) ?>" 
                       readonly
                       id="shareUrlInput"
                       class="flex-1 bg-transparent text-sm text-gray-700 dark:text-gray-200 font-mono outline-none truncate">
                <button onclick="copyShareLink()" 
                        id="copyLinkBtnCompact"
                        class="px-3 py-1.5 bg-primary-500 hover:bg-primary-600 text-white text-sm rounded-lg transition">
                    <i class="fas fa-copy"></i>
                </button>
            </div>
        </div>
        
        <!-- Tipp basierend auf Business-Typ -->
        <?php if ($businessType === 'offline'): ?>
        <div class="share-tip mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg text-sm">
            <i class="fas fa-lightbulb text-green-500 mr-2"></i>
            <strong>Tipp:</strong> WhatsApp und SMS funktionieren besonders gut bei persönlichen Kontakten nach einem Termin.
        </div>
        <?php elseif ($businessType === 'online'): ?>
        <div class="share-tip mt-4 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg text-sm">
            <i class="fas fa-lightbulb text-purple-500 mr-2"></i>
            <strong>Tipp:</strong> Fügen Sie den Link in Ihre E-Mail-Signatur und Social-Media-Bio ein.
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Share Modal für "Mehr" -->
<div id="shareModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50" onclick="closeShareModal(event)">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 max-h-[80vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white">
                <i class="fas fa-share-alt text-primary-500 mr-2"></i>
                Alle Teilen-Optionen
            </h3>
            <button onclick="closeShareModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="grid grid-cols-2 gap-3">
            <?php foreach ($shareOrder as $platformKey): ?>
                <?php 
                if (!isset($allPlatforms[$platformKey])) continue;
                $platform = $allPlatforms[$platformKey];
                $isLinkCopy = $platformKey === 'link_copy';
                ?>
                
                <a href="<?= $isLinkCopy ? 'javascript:void(0)' : htmlspecialchars($platform['url']) ?>"
                   <?= $isLinkCopy ? '' : 'target="_blank" rel="noopener noreferrer"' ?>
                   <?= isset($platform['onclick']) ? "onclick=\"{$platform['onclick']}; closeShareModal(); return false;\"" : "onclick=\"trackShareClick('{$platformKey}'); closeShareModal();\"" ?>
                   class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white"
                         style="background-color: <?= $platform['color'] ?>">
                        <i class="<?= $platform['icon'] ?>"></i>
                    </div>
                    <span class="font-medium text-gray-800 dark:text-white"><?= $platform['name'] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function copyShareLink() {
    const input = document.getElementById('shareUrlInput');
    const btn = document.getElementById('copyLinkBtnCompact');
    const shareBtnCopy = document.getElementById('shareBtn_link_copy');
    
    navigator.clipboard.writeText(input.value).then(() => {
        // Button-Feedback
        if (btn) {
            btn.innerHTML = '<i class="fas fa-check"></i>';
            setTimeout(() => { btn.innerHTML = '<i class="fas fa-copy"></i>'; }, 2000);
        }
        if (shareBtnCopy) {
            const originalText = shareBtnCopy.innerHTML;
            shareBtnCopy.innerHTML = '<i class="fas fa-check text-lg"></i><span class="text-sm">Kopiert!</span>';
            setTimeout(() => { shareBtnCopy.innerHTML = originalText; }, 2000);
        }
        
        // Notification
        showNotification('Link in Zwischenablage kopiert!', 'success');
        
        // Track
        trackShareClick('link_copy');
    }).catch(() => {
        // Fallback
        input.select();
        document.execCommand('copy');
        showNotification('Link kopiert!', 'success');
    });
}

function trackShareClick(platform) {
    fetch('/api/track-share.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            customer_id: <?= (int)$customer['id'] ?>,
            platform: platform,
            source: 'dashboard_quick_share'
        })
    }).catch(() => {});
}

function openAllShareOptions() {
    document.getElementById('shareModal').classList.remove('hidden');
    document.getElementById('shareModal').classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeShareModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('shareModal').classList.add('hidden');
    document.getElementById('shareModal').classList.remove('flex');
    document.body.style.overflow = '';
}

// ESC zum Schließen
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeShareModal();
});

// Notification Helper (falls nicht global vorhanden)
function showNotification(message, type = 'info') {
    if (typeof window.showToast === 'function') {
        window.showToast(message, type);
        return;
    }
    
    const notification = document.createElement('div');
    notification.className = `fixed bottom-4 right-4 px-4 py-3 rounded-lg text-white z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>${message}`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<style>
.share-button {
    min-height: 52px;
}

@media (max-width: 640px) {
    .share-buttons-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>
