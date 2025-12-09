<?php
/**
 * Leadbusiness - Lead Dashboard (Empfehler-Bereich)
 * 
 * Dashboard für Empfehler mit:
 * - Statistiken (Klicks, Conversions, Punkte)
 * - Share-Buttons
 * - Fortschrittsanzeige
 * - Badges
 * - Leaderboard
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/services/GamificationService.php';
require_once __DIR__ . '/../../includes/services/BadgeService.php';
require_once __DIR__ . '/../../includes/services/LeaderboardService.php';
require_once __DIR__ . '/../../includes/services/QRCodeService.php';

$db = Database::getInstance();

// Lead per Referral-Code oder Session identifizieren
session_start();

$leadCode = $_GET['code'] ?? $_SESSION['lead_code'] ?? '';
$lead = null;
$customer = null;

if ($leadCode) {
    $lead = $db->fetch(
        "SELECT l.*, c.company_name, c.subdomain, c.logo_url, c.primary_color,
                c.leaderboard_enabled, ca.id as campaign_id
         FROM leads l
         JOIN campaigns ca ON l.campaign_id = ca.id
         JOIN customers c ON ca.customer_id = c.id
         WHERE l.referral_code = ? AND l.status = 'active'",
        [strtoupper($leadCode)]
    );
    
    if ($lead) {
        $_SESSION['lead_code'] = $lead['referral_code'];
        $customer = [
            'company_name' => $lead['company_name'],
            'subdomain' => $lead['subdomain'],
            'logo_url' => $lead['logo_url'],
            'primary_color' => $lead['primary_color'],
            'leaderboard_enabled' => $lead['leaderboard_enabled']
        ];
    }
}

if (!$lead) {
    // Zur Hauptseite weiterleiten
    header('Location: /');
    exit;
}

// Gamification Stats
$gamification = new GamificationService();
$stats = $gamification->getLeadStats($lead['id']);

// Badges
$badgeService = new BadgeService();
$badges = $badgeService->getLeadBadges($lead['id']);
$badgeProgress = $badgeService->getBadgeProgress($lead['id']);

// Leaderboard
$leaderboardService = new LeaderboardService();
$leaderboard = [];
$rank = null;

if ($customer['leaderboard_enabled']) {
    $leaderboard = $leaderboardService->getAnonymizedLeaderboard($lead['campaign_id'], 5);
    $rank = $leaderboardService->getLeadRank($lead['id']);
}

// Belohnungen
$rewards = $db->fetchAll(
    "SELECT * FROM rewards WHERE campaign_id = ? AND is_active = 1 ORDER BY level ASC",
    [$lead['campaign_id']]
);

// QR Code
$qrService = new QRCodeService();
$qrCodeUrl = $qrService->generateForLead($lead['id'], 200);

// Referral-Link
$referralLink = "https://{$customer['subdomain']}.empfohlen.de/r/{$lead['referral_code']}";

// Share-Texte
$shareText = "Ich empfehle {$customer['company_name']}! Nutze meinen Link:";
$shareTextEncoded = urlencode($shareText . ' ' . $referralLink);

$pageTitle = 'Mein Empfehlungs-Dashboard';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | <?= htmlspecialchars($customer['company_name']) ?></title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '<?= htmlspecialchars($customer['primary_color']) ?>'
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .share-btn { transition: all 0.2s ease; }
        .share-btn:hover { transform: translateY(-2px); }
        .badge-card { transition: all 0.2s ease; }
        .badge-card:hover { transform: scale(1.05); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white border-b sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <?php if ($customer['logo_url']): ?>
            <img src="<?= htmlspecialchars($customer['logo_url']) ?>" 
                 alt="<?= htmlspecialchars($customer['company_name']) ?>" 
                 class="h-10 object-contain">
            <?php else: ?>
            <span class="text-xl font-bold text-gray-900"><?= htmlspecialchars($customer['company_name']) ?></span>
            <?php endif; ?>
            
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">
                    <?= htmlspecialchars($lead['name'] ?: 'Empfehler') ?>
                </span>
                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold text-sm">
                    <?= strtoupper(substr($lead['name'] ?: $lead['email'], 0, 1)) ?>
                </div>
            </div>
        </div>
    </header>
    
    <main class="max-w-4xl mx-auto px-4 py-8">
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-4 shadow-sm text-center">
                <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['conversions']) ?></div>
                <div class="text-sm text-gray-500">Empfehlungen</div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm text-center">
                <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['clicks']) ?></div>
                <div class="text-sm text-gray-500">Link-Klicks</div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm text-center">
                <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['points']) ?></div>
                <div class="text-sm text-gray-500">Punkte</div>
            </div>
            <div class="bg-white rounded-2xl p-4 shadow-sm text-center">
                <div class="text-3xl font-bold text-gray-900"><?= $stats['current_streak'] ?></div>
                <div class="text-sm text-gray-500">Wochen-Streak 🔥</div>
            </div>
        </div>
        
        <!-- Progress to Next Reward -->
        <?php if ($stats['progress'] && !$stats['progress']['completed']): ?>
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Nächste Belohnung</h2>
            
            <div class="flex items-center gap-4 mb-4">
                <div class="flex-1">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">
                            <?= $stats['progress']['current'] ?> von <?= $stats['progress']['required'] ?> Empfehlungen
                        </span>
                        <span class="font-semibold text-primary"><?= $stats['progress']['progress'] ?>%</span>
                    </div>
                    <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-primary rounded-full transition-all duration-500"
                             style="width: <?= $stats['progress']['progress'] ?>%"></div>
                    </div>
                </div>
            </div>
            
            <?php if ($stats['progress']['next_reward']): ?>
            <div class="flex items-center gap-3 p-4 bg-yellow-50 rounded-xl">
                <div class="text-2xl">🎁</div>
                <div>
                    <div class="font-semibold text-gray-900">
                        <?= htmlspecialchars($stats['progress']['next_reward']['description']) ?>
                    </div>
                    <div class="text-sm text-gray-500">
                        Noch <?= $stats['progress']['remaining'] ?> Empfehlung(en) bis Stufe <?= $stats['progress']['next_reward']['level'] ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- Share Section -->
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Jetzt teilen & belohnt werden</h2>
            
            <!-- Referral Link -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ihr persönlicher Empfehlungslink</label>
                <div class="flex items-center gap-2">
                    <input type="text" value="<?= htmlspecialchars($referralLink) ?>" readonly
                        class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm">
                    <button onclick="copyLink()" class="px-4 py-3 bg-primary text-white rounded-xl hover:opacity-90">
                        <i class="fas fa-copy"></i>
                    </button>
                </div>
            </div>
            
            <!-- Share Buttons -->
            <div class="grid grid-cols-3 md:grid-cols-6 gap-3">
                <a href="https://wa.me/?text=<?= $shareTextEncoded ?>" target="_blank"
                   class="share-btn flex flex-col items-center gap-2 p-4 bg-green-500 text-white rounded-xl hover:bg-green-600"
                   onclick="trackShare('whatsapp')">
                    <i class="fab fa-whatsapp text-2xl"></i>
                    <span class="text-xs">WhatsApp</span>
                </a>
                
                <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($referralLink) ?>" target="_blank"
                   class="share-btn flex flex-col items-center gap-2 p-4 bg-blue-600 text-white rounded-xl hover:bg-blue-700"
                   onclick="trackShare('facebook')">
                    <i class="fab fa-facebook text-2xl"></i>
                    <span class="text-xs">Facebook</span>
                </a>
                
                <a href="https://t.me/share/url?url=<?= urlencode($referralLink) ?>&text=<?= urlencode($shareText) ?>" target="_blank"
                   class="share-btn flex flex-col items-center gap-2 p-4 bg-sky-500 text-white rounded-xl hover:bg-sky-600"
                   onclick="trackShare('telegram')">
                    <i class="fab fa-telegram text-2xl"></i>
                    <span class="text-xs">Telegram</span>
                </a>
                
                <a href="mailto:?subject=Empfehlung&body=<?= $shareTextEncoded ?>" 
                   class="share-btn flex flex-col items-center gap-2 p-4 bg-gray-600 text-white rounded-xl hover:bg-gray-700"
                   onclick="trackShare('email')">
                    <i class="fas fa-envelope text-2xl"></i>
                    <span class="text-xs">E-Mail</span>
                </a>
                
                <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($referralLink) ?>" target="_blank"
                   class="share-btn flex flex-col items-center gap-2 p-4 bg-blue-700 text-white rounded-xl hover:bg-blue-800"
                   onclick="trackShare('linkedin')">
                    <i class="fab fa-linkedin text-2xl"></i>
                    <span class="text-xs">LinkedIn</span>
                </a>
                
                <button onclick="copyLink()" 
                   class="share-btn flex flex-col items-center gap-2 p-4 bg-gray-800 text-white rounded-xl hover:bg-gray-900">
                    <i class="fas fa-link text-2xl"></i>
                    <span class="text-xs">Kopieren</span>
                </button>
            </div>
            
            <!-- QR Code -->
            <?php if ($qrCodeUrl): ?>
            <div class="mt-6 text-center">
                <button onclick="showQR()" class="text-primary text-sm hover:underline">
                    <i class="fas fa-qrcode mr-1"></i>
                    QR-Code anzeigen
                </button>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Badges -->
        <?php if (!empty($badges) || !empty($badgeProgress)): ?>
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Ihre Badges</h2>
            
            <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
                <?php foreach ($badgeProgress as $bp): ?>
                <div class="badge-card text-center p-4 rounded-xl <?= $bp['earned'] ? 'bg-yellow-50' : 'bg-gray-100 opacity-50' ?>">
                    <div class="text-3xl mb-2"><?= $bp['badge']['icon'] ?></div>
                    <div class="text-xs font-medium text-gray-900"><?= htmlspecialchars($bp['badge']['name']) ?></div>
                    <?php if (!$bp['earned']): ?>
                    <div class="text-xs text-gray-500 mt-1"><?= $bp['progress_percent'] ?>%</div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Leaderboard -->
        <?php if ($customer['leaderboard_enabled'] && !empty($leaderboard)): ?>
        <div class="bg-white rounded-2xl p-6 shadow-sm mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-900">Rangliste</h2>
                <?php if ($rank): ?>
                <span class="text-sm text-gray-500">
                    Ihr Platz: <strong class="text-primary">#<?= $rank['rank'] ?></strong>
                </span>
                <?php endif; ?>
            </div>
            
            <div class="space-y-3">
                <?php foreach ($leaderboard as $index => $leader): ?>
                <div class="flex items-center gap-3 p-3 rounded-xl <?= $leader['id'] == $lead['id'] ? 'bg-primary/10' : 'bg-gray-50' ?>">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                        <?= $index === 0 ? 'bg-yellow-400 text-white' : ($index === 1 ? 'bg-gray-300 text-white' : ($index === 2 ? 'bg-amber-600 text-white' : 'bg-gray-200 text-gray-600')) ?>">
                        <?= $index + 1 ?>
                    </div>
                    <div class="flex-1">
                        <div class="font-medium text-gray-900">
                            <?= htmlspecialchars($leader['display_name']) ?>
                            <?= $leader['id'] == $lead['id'] ? '(Sie)' : '' ?>
                        </div>
                    </div>
                    <div class="text-sm font-semibold text-gray-600">
                        <?= $leader['conversions'] ?> Empf.
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Rewards Overview -->
        <div class="bg-white rounded-2xl p-6 shadow-sm">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Alle Belohnungsstufen</h2>
            
            <div class="space-y-4">
                <?php foreach ($rewards as $reward): ?>
                <div class="flex items-center gap-4 p-4 rounded-xl border-2 
                    <?= $stats['reward_level'] >= $reward['level'] ? 'border-green-500 bg-green-50' : 'border-gray-200' ?>">
                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl
                        <?= $stats['reward_level'] >= $reward['level'] ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' ?>">
                        <?= $stats['reward_level'] >= $reward['level'] ? '✓' : $reward['level'] ?>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-gray-900"><?= htmlspecialchars($reward['description']) ?></div>
                        <div class="text-sm text-gray-500"><?= $reward['required_conversions'] ?> Empfehlungen</div>
                    </div>
                    <?php if ($stats['reward_level'] >= $reward['level']): ?>
                    <span class="px-3 py-1 bg-green-500 text-white text-xs rounded-full">Freigeschaltet</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    </main>
    
    <!-- QR Modal -->
    <div id="qrModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-6 text-center">
            <h3 class="text-xl font-bold mb-4">Ihr QR-Code</h3>
            <img src="<?= htmlspecialchars($qrCodeUrl ?? '') ?>" alt="QR Code" class="mx-auto mb-4">
            <p class="text-sm text-gray-500 mb-4">Scannen für Ihren Empfehlungslink</p>
            <button onclick="closeQR()" class="px-6 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Schließen</button>
        </div>
    </div>
    
    <script>
        const referralLink = '<?= htmlspecialchars($referralLink) ?>';
        
        function copyLink() {
            navigator.clipboard.writeText(referralLink);
            alert('Link kopiert!');
            trackShare('copy');
        }
        
        function showQR() {
            document.getElementById('qrModal').classList.remove('hidden');
            document.getElementById('qrModal').classList.add('flex');
        }
        
        function closeQR() {
            document.getElementById('qrModal').classList.add('hidden');
            document.getElementById('qrModal').classList.remove('flex');
        }
        
        function trackShare(platform) {
            fetch('/api/tracking/share.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    lead_id: <?= $lead['id'] ?>,
                    platform: platform
                })
            }).catch(() => {});
        }
    </script>
    
</body>
</html>
