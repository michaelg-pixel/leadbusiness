<?php
/**
 * Leadbusiness - Kunden-Dashboard
 * 
 * Übersichtsseite für eingeloggte Kunden
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/services/LeaderboardService.php';

// Auth prüfen
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];

$db = Database::getInstance();

// Statistiken laden
$stats = $db->fetch(
    "SELECT 
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status IN ('active', 'pending')) as total_leads,
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND status = 'active') as active_leads,
        (SELECT SUM(conversions) FROM leads WHERE customer_id = ?) as total_conversions,
        (SELECT SUM(clicks) FROM leads WHERE customer_id = ?) as total_clicks,
        (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND DATE(created_at) = CURDATE()) as leads_today,
        (SELECT COUNT(*) FROM conversions c 
         JOIN leads l ON c.referrer_id = l.id 
         WHERE l.customer_id = ? AND c.status = 'confirmed' AND DATE(c.created_at) = CURDATE()) as conversions_today
    ",
    [$customerId, $customerId, $customerId, $customerId, $customerId, $customerId]
);

// Conversion Rate berechnen
$conversionRate = 0;
if ($stats['total_clicks'] > 0) {
    $conversionRate = round(($stats['total_conversions'] / $stats['total_clicks']) * 100, 1);
}

// Standard-Kampagne
$campaign = $db->fetch(
    "SELECT * FROM campaigns WHERE customer_id = ? AND is_default = 1",
    [$customerId]
);

// Letzte Aktivitäten
$recentActivity = $db->fetchAll(
    "SELECT 
        'lead' as type,
        l.name,
        l.email,
        l.created_at,
        'Neuer Empfehler' as action
     FROM leads l
     WHERE l.customer_id = ?
     UNION ALL
     SELECT 
        'conversion' as type,
        l.name,
        l.email,
        c.created_at,
        'Erfolgreiche Empfehlung' as action
     FROM conversions c
     JOIN leads l ON c.referrer_id = l.id
     WHERE l.customer_id = ? AND c.status = 'confirmed'
     ORDER BY created_at DESC
     LIMIT 10",
    [$customerId, $customerId]
);

// Top Empfehler
$topLeads = $db->fetchAll(
    "SELECT name, email, conversions, clicks, total_points
     FROM leads
     WHERE customer_id = ? AND status = 'active' AND conversions > 0
     ORDER BY conversions DESC
     LIMIT 5",
    [$customerId]
);

// Chart-Daten (letzte 14 Tage)
$chartData = $db->fetchAll(
    "SELECT 
        DATE(created_at) as date,
        COUNT(*) as count
     FROM leads
     WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
     GROUP BY DATE(created_at)
     ORDER BY date ASC",
    [$customerId]
);

// Willkommens-Nachricht für neue Kunden
$isNewCustomer = isset($_GET['welcome']);

$pageTitle = 'Dashboard';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> | Leadbusiness</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f4ff',
                            100: '#e0e9ff',
                            500: '#667eea',
                            600: '#5a67d8',
                            700: '#4c51bf',
                        }
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .stat-card:hover { transform: translateY(-2px); }
    </style>
</head>
<body class="bg-gray-50">
    
    <div class="flex h-screen">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r hidden lg:block">
            <div class="p-6 border-b">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-share-nodes text-white"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Leadbusiness</span>
                </a>
            </div>
            
            <nav class="p-4 space-y-1">
                <a href="/dashboard" class="flex items-center gap-3 px-4 py-3 text-primary-600 bg-primary-50 rounded-xl font-medium">
                    <i class="fas fa-home w-5"></i>
                    <span>Übersicht</span>
                </a>
                <a href="/dashboard/leads.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-users w-5"></i>
                    <span>Empfehler</span>
                </a>
                <a href="/dashboard/rewards.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-gift w-5"></i>
                    <span>Belohnungen</span>
                </a>
                <a href="/dashboard/design.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-palette w-5"></i>
                    <span>Design</span>
                </a>
                <a href="/dashboard/graphics.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-images w-5"></i>
                    <span>Grafiken</span>
                </a>
                <a href="/dashboard/settings.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 rounded-xl">
                    <i class="fas fa-cog w-5"></i>
                    <span>Einstellungen</span>
                </a>
            </nav>
            
            <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white w-64">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 font-semibold">
                        <?= strtoupper(substr($customer['company_name'], 0, 2)) ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-gray-900 truncate"><?= htmlspecialchars($customer['company_name']) ?></div>
                        <div class="text-xs text-gray-500"><?= ucfirst($customer['plan']) ?>-Plan</div>
                    </div>
                </div>
                <a href="/dashboard/logout.php" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-sign-out-alt"></i>
                    Abmelden
                </a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            
            <!-- Top Bar -->
            <header class="bg-white border-b px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Willkommen zurück!</h1>
                        <p class="text-gray-500">Hier ist die Übersicht Ihres Empfehlungsprogramms.</p>
                    </div>
                    
                    <div class="flex items-center gap-4">
                        <a href="https://<?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de" 
                           target="_blank"
                           class="flex items-center gap-2 px-4 py-2 bg-primary-500 text-white rounded-xl hover:bg-primary-600">
                            <i class="fas fa-external-link-alt"></i>
                            <span>Empfehlungsseite</span>
                        </a>
                    </div>
                </div>
            </header>
            
            <div class="p-6">
                
                <?php if ($isNewCustomer): ?>
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-primary-500 to-purple-600 rounded-2xl p-6 text-white mb-6">
                    <div class="flex items-start gap-4">
                        <div class="text-4xl">🎉</div>
                        <div>
                            <h2 class="text-xl font-bold mb-2">Herzlichen Glückwunsch!</h2>
                            <p class="text-white/90 mb-4">
                                Ihr Empfehlungsprogramm ist eingerichtet und bereit. 
                                Teilen Sie jetzt Ihren Link mit Ihren Kunden:
                            </p>
                            <div class="flex items-center gap-2 bg-white/20 rounded-lg px-4 py-2 inline-flex">
                                <code class="text-sm"><?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de</code>
                                <button onclick="copyLink()" class="text-white/80 hover:text-white">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm transition-transform">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-users text-blue-500 text-xl"></i>
                            </div>
                            <?php if ($stats['leads_today'] > 0): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                +<?= $stats['leads_today'] ?> heute
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_leads']) ?></div>
                        <div class="text-sm text-gray-500">Empfehler gesamt</div>
                    </div>
                    
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm transition-transform">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-user-check text-green-500 text-xl"></i>
                            </div>
                            <?php if ($stats['conversions_today'] > 0): ?>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                +<?= $stats['conversions_today'] ?> heute
                            </span>
                            <?php endif; ?>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_conversions'] ?? 0) ?></div>
                        <div class="text-sm text-gray-500">Erfolgreiche Empfehlungen</div>
                    </div>
                    
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm transition-transform">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-mouse-pointer text-yellow-500 text-xl"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_clicks'] ?? 0) ?></div>
                        <div class="text-sm text-gray-500">Link-Klicks</div>
                    </div>
                    
                    <div class="stat-card bg-white rounded-2xl p-6 shadow-sm transition-transform">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-percentage text-purple-500 text-xl"></i>
                            </div>
                        </div>
                        <div class="text-3xl font-bold text-gray-900"><?= $conversionRate ?>%</div>
                        <div class="text-sm text-gray-500">Conversion-Rate</div>
                    </div>
                    
                </div>
                
                <!-- Charts & Activity -->
                <div class="grid lg:grid-cols-3 gap-6 mb-8">
                    
                    <!-- Chart -->
                    <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Neue Empfehler (14 Tage)</h3>
                        <canvas id="leadsChart" height="200"></canvas>
                    </div>
                    
                    <!-- Top Leads -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Top Empfehler</h3>
                        
                        <?php if (empty($topLeads)): ?>
                        <p class="text-gray-500 text-sm">Noch keine aktiven Empfehler.</p>
                        <?php else: ?>
                        <div class="space-y-4">
                            <?php foreach ($topLeads as $index => $lead): ?>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm
                                    <?= $index === 0 ? 'bg-yellow-400 text-white' : 'bg-gray-200 text-gray-600' ?>">
                                    <?= $index + 1 ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-gray-900 truncate">
                                        <?= htmlspecialchars($lead['name'] ?: 'Anonymer Empfehler') ?>
                                    </div>
                                    <div class="text-xs text-gray-500"><?= $lead['conversions'] ?> Empfehlungen</div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <a href="/dashboard/leads.php" class="mt-4 text-primary-500 text-sm font-medium hover:underline inline-block">
                            Alle Empfehler ansehen →
                        </a>
                    </div>
                    
                </div>
                
                <!-- Recent Activity -->
                <div class="bg-white rounded-2xl p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Letzte Aktivitäten</h3>
                    
                    <?php if (empty($recentActivity)): ?>
                    <p class="text-gray-500">Noch keine Aktivitäten. Teilen Sie Ihren Empfehlungslink!</p>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recentActivity as $activity): ?>
                        <div class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center
                                <?= $activity['type'] === 'conversion' ? 'bg-green-100 text-green-500' : 'bg-blue-100 text-blue-500' ?>">
                                <i class="fas <?= $activity['type'] === 'conversion' ? 'fa-check' : 'fa-user-plus' ?>"></i>
                            </div>
                            <div class="flex-1">
                                <div class="font-medium text-gray-900"><?= htmlspecialchars($activity['action']) ?></div>
                                <div class="text-sm text-gray-500">
                                    <?= htmlspecialchars($activity['name'] ?: $activity['email']) ?> · 
                                    <?= timeAgo($activity['created_at']) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
            </div>
            
        </main>
        
    </div>
    
    <script>
        // Chart
        const chartData = <?= json_encode($chartData) ?>;
        const labels = chartData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' });
        });
        const data = chartData.map(d => d.count);
        
        new Chart(document.getElementById('leadsChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Neue Empfehler',
                    data: data,
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
        
        // Copy Link
        function copyLink() {
            const link = '<?= htmlspecialchars($customer['subdomain']) ?>.empfohlen.de';
            navigator.clipboard.writeText('https://' + link);
            alert('Link kopiert!');
        }
    </script>
    
</body>
</html>
