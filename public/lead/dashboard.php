<?php
/**
 * Leadbusiness - Lead Portal Dashboard
 * 
 * Professionelles Dashboard für Empfehler mit:
 * - Übersicht (Stats, Fortschritt)
 * - Empfehlungs-Historie
 * - Belohnungs-Center
 * - Einstellungen
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/services/LeadAuthService.php';
require_once __DIR__ . '/../../includes/services/GamificationService.php';
require_once __DIR__ . '/../../includes/services/BadgeService.php';
require_once __DIR__ . '/../../includes/services/LeaderboardService.php';
require_once __DIR__ . '/../../includes/services/QRCodeService.php';

$db = Database::getInstance();
$auth = new LeadAuthService();

// Auth Check
$lead = $auth->check();
if (!$lead) {
    header('Location: /lead/login.php');
    exit;
}

// Tab auswählen
$activeTab = $_GET['tab'] ?? 'overview';
$validTabs = ['overview', 'history', 'rewards', 'settings'];
if (!in_array($activeTab, $validTabs)) {
    $activeTab = 'overview';
}

// Kunden-Daten
$customer = [
    'company_name' => $lead['company_name'],
    'subdomain' => $lead['subdomain'],
    'logo_url' => $lead['logo_url'],
    'primary_color' => $lead['primary_color'] ?? '#667eea',
    'leaderboard_enabled' => $lead['leaderboard_enabled'] ?? true
];

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

// Freigeschaltete Belohnungen
$unlockedRewards = $db->fetchAll(
    "SELECT rd.*, r.description, r.reward_type, r.level, r.required_conversions,
            r.download_file_path, r.download_file_name, r.instructions
     FROM reward_deliveries rd
     JOIN rewards r ON rd.reward_id = r.id
     WHERE rd.lead_id = ?
     ORDER BY rd.created_at DESC",
    [$lead['id']]
);

// Empfehlungs-Historie
$conversions = $db->fetchAll(
    "SELECT c.*, l.name as referred_name, l.email as referred_email, l.created_at as referred_at
     FROM conversions c
     JOIN leads l ON c.referred_lead_id = l.id
     WHERE c.lead_id = ?
     ORDER BY c.created_at DESC
     LIMIT 50",
    [$lead['id']]
);

// Aktivitäts-Log (letzte 20)
$activities = $db->fetchAll(
    "SELECT * FROM lead_activity_log 
     WHERE lead_id = ? 
     ORDER BY created_at DESC 
     LIMIT 20",
    [$lead['id']]
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
                        primary: '<?= htmlspecialchars($customer['primary_color']) ?>',
                        'primary-dark': '<?= adjustBrightness($customer['primary_color'], -20) ?>'
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
        .tab-active { border-bottom: 2px solid <?= htmlspecialchars($customer['primary_color']) ?>; color: <?= htmlspecialchars($customer['primary_color']) ?>; }
        .share-btn { transition: all 0.2s ease; }
        .share-btn:hover { transform: translateY(-2px); }
        .badge-card { transition: all 0.2s ease; }
        .badge-card:hover { transform: scale(1.05); }
        .card { @apply bg-white rounded-2xl shadow-sm; }
        .reward-card { transition: all 0.2s ease; }
        .reward-card:hover { transform: translateY(-2px); box-shadow: 0 10px 40px rgba(0,0,0,0.1); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white border-b sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <?php if ($customer['logo_url']): ?>
                <img src="<?= htmlspecialchars($customer['logo_url']) ?>" 
                     alt="<?= htmlspecialchars($customer['company_name']) ?>" 
                     class="h-10 object-contain">
                <?php else: ?>
                <span class="text-xl font-bold text-gray-900"><?= htmlspecialchars($customer['company_name']) ?></span>
                <?php endif; ?>
                
                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-2 text-sm text-gray-500">
                        <i class="fas fa-star text-yellow-500"></i>
                        <span><?= number_format($stats['points']) ?> Punkte</span>
                    </div>
                    
                    <div class="relative group">
                        <button class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold text-sm">
                                <?= strtoupper(substr($lead['name'] ?: $lead['email'], 0, 1)) ?>
                            </div>
                            <span class="hidden md:block text-sm font-medium text-gray-700">
                                <?= htmlspecialchars($lead['name'] ?: 'Empfehler') ?>
                            </span>
                            <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                        </button>
                        
                        <!-- Dropdown -->
                        <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border hidden group-hover:block">
                            <a href="?tab=settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-cog w-5"></i> Einstellungen
                            </a>
                            <hr class="my-1">
                            <a href="/lead/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt w-5"></i> Abmelden
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs -->
            <nav class="flex gap-6 mt-4 -mb-px overflow-x-auto">
                <a href="?tab=overview" 
                   class="pb-3 px-1 text-sm font-medium whitespace-nowrap <?= $activeTab === 'overview' ? 'tab-active' : 'text-gray-500 hover:text-gray-700' ?>">
                    <i class="fas fa-home mr-2"></i>Übersicht
                </a>
                <a href="?tab=history" 
                   class="pb-3 px-1 text-sm font-medium whitespace-nowrap <?= $activeTab === 'history' ? 'tab-active' : 'text-gray-500 hover:text-gray-700' ?>">
                    <i class="fas fa-history mr-2"></i>Empfehlungen
                    <?php if (count($conversions) > 0): ?>
                    <span class="ml-1 px-2 py-0.5 bg-primary/10 text-primary text-xs rounded-full"><?= count($conversions) ?></span>
                    <?php endif; ?>
                </a>
                <a href="?tab=rewards" 
                   class="pb-3 px-1 text-sm font-medium whitespace-nowrap <?= $activeTab === 'rewards' ? 'tab-active' : 'text-gray-500 hover:text-gray-700' ?>">
                    <i class="fas fa-gift mr-2"></i>Belohnungen
                    <?php if (count($unlockedRewards) > 0): ?>
                    <span class="ml-1 px-2 py-0.5 bg-green-100 text-green-600 text-xs rounded-full"><?= count($unlockedRewards) ?></span>
                    <?php endif; ?>
                </a>
                <a href="?tab=settings" 
                   class="pb-3 px-1 text-sm font-medium whitespace-nowrap <?= $activeTab === 'settings' ? 'tab-active' : 'text-gray-500 hover:text-gray-700' ?>">
                    <i class="fas fa-cog mr-2"></i>Einstellungen
                </a>
            </nav>
        </div>
    </header>
    
    <main class="max-w-6xl mx-auto px-4 py-8">
        
        <?php if ($activeTab === 'overview'): ?>
        <!-- ==================== ÜBERSICHT TAB ==================== -->
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="card p-5 text-center">
                <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['conversions']) ?></div>
                <div class="text-sm text-gray-500 mt-1">Erfolgreiche Empfehlungen</div>
            </div>
            <div class="card p-5 text-center">
                <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['clicks']) ?></div>
                <div class="text-sm text-gray-500 mt-1">Link-Klicks</div>
            </div>
            <div class="card p-5 text-center">
                <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['points']) ?></div>
                <div class="text-sm text-gray-500 mt-1">Gesammelte Punkte</div>
            </div>
            <div class="card p-5 text-center">
                <div class="text-3xl font-bold text-primary"><?= $stats['current_streak'] ?></div>
                <div class="text-sm text-gray-500 mt-1">Wochen-Streak 🔥</div>
            </div>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Linke Spalte: Fortschritt + Share -->
            <div class="md:col-span-2 space-y-6">
                
                <!-- Progress to Next Reward -->
                <?php if ($stats['progress'] && !$stats['progress']['completed']): ?>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>Nächste Belohnung
                    </h2>
                    
                    <div class="flex items-center gap-4 mb-4">
                        <div class="flex-1">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">
                                    <?= $stats['progress']['current'] ?> von <?= $stats['progress']['required'] ?> Empfehlungen
                                </span>
                                <span class="font-semibold text-primary"><?= $stats['progress']['progress'] ?>%</span>
                            </div>
                            <div class="h-4 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-primary to-primary-dark rounded-full transition-all duration-500"
                                     style="width: <?= $stats['progress']['progress'] ?>%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($stats['progress']['next_reward']): ?>
                    <div class="flex items-center gap-3 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                        <div class="text-3xl">🎁</div>
                        <div class="flex-1">
                            <div class="font-semibold text-gray-900">
                                <?= htmlspecialchars($stats['progress']['next_reward']['description']) ?>
                            </div>
                            <div class="text-sm text-gray-500">
                                Nur noch <?= $stats['progress']['remaining'] ?> Empfehlung<?= $stats['progress']['remaining'] > 1 ? 'en' : '' ?>!
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-bold text-primary">Stufe <?= $stats['progress']['next_reward']['level'] ?></span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <!-- Share Section -->
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-share-alt text-primary mr-2"></i>Jetzt teilen & verdienen
                    </h2>
                    
                    <!-- Referral Link -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ihr persönlicher Empfehlungslink</label>
                        <div class="flex items-center gap-2">
                            <input type="text" value="<?= htmlspecialchars($referralLink) ?>" readonly id="referralLink"
                                class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-mono">
                            <button onclick="copyLink()" class="px-5 py-3 bg-primary text-white rounded-xl hover:opacity-90 transition">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Share Buttons Grid -->
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
                        
                        <button onclick="showQR()" 
                           class="share-btn flex flex-col items-center gap-2 p-4 bg-gray-800 text-white rounded-xl hover:bg-gray-900">
                            <i class="fas fa-qrcode text-2xl"></i>
                            <span class="text-xs">QR-Code</span>
                        </button>
                    </div>
                </div>
                
                <!-- Badges -->
                <?php if (!empty($badgeProgress)): ?>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-medal text-yellow-500 mr-2"></i>Ihre Badges
                    </h2>
                    
                    <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
                        <?php foreach ($badgeProgress as $bp): ?>
                        <div class="badge-card text-center p-4 rounded-xl <?= $bp['earned'] ? 'bg-yellow-50 border-2 border-yellow-200' : 'bg-gray-100 opacity-60' ?>">
                            <div class="text-3xl mb-2"><?= $bp['badge']['icon'] ?></div>
                            <div class="text-xs font-medium text-gray-900"><?= htmlspecialchars($bp['badge']['name']) ?></div>
                            <?php if (!$bp['earned']): ?>
                            <div class="mt-2 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full" style="width: <?= $bp['progress_percent'] ?>%"></div>
                            </div>
                            <div class="text-xs text-gray-500 mt-1"><?= $bp['progress_percent'] ?>%</div>
                            <?php else: ?>
                            <div class="text-xs text-green-600 mt-1">✓ Erreicht</div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Rechte Spalte: Leaderboard + Rewards Preview -->
            <div class="space-y-6">
                
                <!-- Leaderboard -->
                <?php if ($customer['leaderboard_enabled'] && !empty($leaderboard)): ?>
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-ranking-star text-primary mr-2"></i>Rangliste
                        </h2>
                        <?php if ($rank): ?>
                        <span class="text-sm bg-primary/10 text-primary px-3 py-1 rounded-full font-medium">
                            #<?= $rank['rank'] ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="space-y-2">
                        <?php foreach ($leaderboard as $index => $leader): ?>
                        <div class="flex items-center gap-3 p-3 rounded-xl <?= $leader['id'] == $lead['id'] ? 'bg-primary/10 border border-primary/20' : 'bg-gray-50' ?>">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                <?php 
                                if ($index === 0) echo 'bg-yellow-400 text-white';
                                elseif ($index === 1) echo 'bg-gray-300 text-white';
                                elseif ($index === 2) echo 'bg-amber-600 text-white';
                                else echo 'bg-gray-200 text-gray-600';
                                ?>">
                                <?= $index + 1 ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 truncate">
                                    <?= htmlspecialchars($leader['display_name']) ?>
                                    <?= $leader['id'] == $lead['id'] ? '<span class="text-primary">(Sie)</span>' : '' ?>
                                </div>
                            </div>
                            <div class="text-sm font-semibold text-gray-600">
                                <?= $leader['conversions'] ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Rewards Preview -->
                <div class="card p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-gift text-primary mr-2"></i>Belohnungen
                        </h2>
                        <a href="?tab=rewards" class="text-sm text-primary hover:underline">Alle anzeigen →</a>
                    </div>
                    
                    <div class="space-y-3">
                        <?php foreach (array_slice($rewards, 0, 3) as $reward): ?>
                        <div class="flex items-center gap-3 p-3 rounded-xl border-2 
                            <?= $stats['reward_level'] >= $reward['level'] ? 'border-green-400 bg-green-50' : 'border-gray-200' ?>">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg
                                <?= $stats['reward_level'] >= $reward['level'] ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' ?>">
                                <?= $stats['reward_level'] >= $reward['level'] ? '✓' : $reward['level'] ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900 text-sm truncate"><?= htmlspecialchars($reward['description']) ?></div>
                                <div class="text-xs text-gray-500"><?= $reward['required_conversions'] ?> Empf.</div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Letzte Aktivität -->
                <?php if (!empty($conversions)): ?>
                <div class="card p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-clock text-gray-400 mr-2"></i>Letzte Empfehlungen
                    </h2>
                    
                    <div class="space-y-3">
                        <?php foreach (array_slice($conversions, 0, 3) as $conv): ?>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-gray-900 truncate">
                                    <?= htmlspecialchars($conv['referred_name'] ?: 'Neuer Kontakt') ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?= timeAgo($conv['created_at']) ?>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full <?= $conv['status'] === 'confirmed' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' ?>">
                                <?= $conv['status'] === 'confirmed' ? 'Bestätigt' : 'Ausstehend' ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <a href="?tab=history" class="block mt-4 text-center text-sm text-primary hover:underline">
                        Alle Empfehlungen anzeigen →
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php elseif ($activeTab === 'history'): ?>
        <!-- ==================== HISTORIE TAB ==================== -->
        <?php include __DIR__ . '/partials/history.php'; ?>
        
        <?php elseif ($activeTab === 'rewards'): ?>
        <!-- ==================== BELOHNUNGEN TAB ==================== -->
        <?php include __DIR__ . '/partials/rewards.php'; ?>
        
        <?php elseif ($activeTab === 'settings'): ?>
        <!-- ==================== EINSTELLUNGEN TAB ==================== -->
        <?php include __DIR__ . '/partials/settings.php'; ?>
        
        <?php endif; ?>
        
    </main>
    
    <!-- QR Modal -->
    <div id="qrModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl p-8 text-center max-w-sm w-full">
            <h3 class="text-xl font-bold mb-4">Ihr QR-Code</h3>
            <div class="bg-white p-4 rounded-xl inline-block mb-4">
                <img src="<?= htmlspecialchars($qrCodeUrl ?? '') ?>" alt="QR Code" class="w-48 h-48">
            </div>
            <p class="text-sm text-gray-500 mb-6">Scannen Sie den Code, um Ihren Empfehlungslink zu öffnen</p>
            <div class="flex gap-3">
                <button onclick="downloadQR()" class="flex-1 px-4 py-2 bg-primary text-white rounded-lg hover:opacity-90">
                    <i class="fas fa-download mr-2"></i>Download
                </button>
                <button onclick="closeQR()" class="flex-1 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Schließen
                </button>
            </div>
        </div>
    </div>
    
    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 bg-gray-900 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-20 opacity-0 transition-all duration-300 z-50">
        <span id="toastMessage"></span>
    </div>
    
    <script>
        const referralLink = '<?= htmlspecialchars($referralLink) ?>';
        const leadId = <?= $lead['id'] ?>;
        
        function copyLink() {
            navigator.clipboard.writeText(referralLink).then(() => {
                showToast('Link kopiert!');
                trackShare('copy');
            });
        }
        
        function showQR() {
            document.getElementById('qrModal').classList.remove('hidden');
            document.getElementById('qrModal').classList.add('flex');
        }
        
        function closeQR() {
            document.getElementById('qrModal').classList.add('hidden');
            document.getElementById('qrModal').classList.remove('flex');
        }
        
        function downloadQR() {
            const link = document.createElement('a');
            link.href = '<?= htmlspecialchars($qrCodeUrl ?? '') ?>';
            link.download = 'mein-empfehlungs-qrcode.png';
            link.click();
            trackShare('qr_download');
        }
        
        function showToast(message) {
            const toast = document.getElementById('toast');
            document.getElementById('toastMessage').textContent = message;
            toast.classList.remove('translate-y-20', 'opacity-0');
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }
        
        function trackShare(platform) {
            fetch('/api/tracking/share.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    lead_id: leadId,
                    platform: platform
                })
            }).catch(() => {});
        }
        
        // Confetti bei neuer Belohnung (falls Parameter gesetzt)
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('reward_unlocked') === '1') {
            setTimeout(() => {
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 }
                });
            }, 500);
        }
    </script>
    
</body>
</html>

<?php
// Helper function
function adjustBrightness($hex, $percent) {
    $hex = ltrim($hex, '#');
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));
    
    return sprintf('#%02x%02x%02x', $r, $g, $b);
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'Gerade eben';
    if ($diff < 3600) return floor($diff / 60) . ' Min.';
    if ($diff < 86400) return floor($diff / 3600) . ' Std.';
    if ($diff < 604800) return floor($diff / 86400) . ' Tage';
    
    return date('d.m.Y', $time);
}
?>
