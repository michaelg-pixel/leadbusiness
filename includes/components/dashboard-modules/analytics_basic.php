<?php
/**
 * Dashboard Module: Analytics Basic
 * Verfügbar für: Professional, Enterprise
 * Zeigt Statistiken und Trends
 */

if (!isset($customer) || !isset($db)) {
    return;
}

// Plan-Check
$isAvailable = in_array($customer['plan'], ['professional', 'enterprise']);

// Branchenspezifische Texte
$customerTerm = $dashboardLayout['customer_term'] ?? 'Kunden';

// Statistiken nur laden wenn verfügbar
$analytics = [];
$chartData = [];
$topSources = [];

if ($isAvailable) {
    // 30-Tage Übersicht
    $analytics = $db->fetch(
        "SELECT 
            -- Aktuelle Periode (letzte 30 Tage)
            (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as leads_30d,
            (SELECT COALESCE(SUM(conversions), 0) FROM leads WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as conversions_30d,
            (SELECT COALESCE(SUM(clicks), 0) FROM leads WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) as clicks_30d,
            
            -- Vorherige Periode (30-60 Tage)
            (SELECT COUNT(*) FROM leads WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)) as leads_prev,
            (SELECT COALESCE(SUM(conversions), 0) FROM leads WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)) as conversions_prev,
            (SELECT COALESCE(SUM(clicks), 0) FROM leads WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)) as clicks_prev,
            
            -- Gesamt
            (SELECT COUNT(*) FROM leads WHERE customer_id = ?) as leads_total,
            (SELECT COALESCE(SUM(conversions), 0) FROM leads WHERE customer_id = ?) as conversions_total,
            (SELECT COALESCE(SUM(clicks), 0) FROM leads WHERE customer_id = ?) as clicks_total
        ",
        [$customer['id'], $customer['id'], $customer['id'], $customer['id'], $customer['id'], $customer['id'], $customer['id'], $customer['id'], $customer['id']]
    );
    
    // Chart-Daten (letzte 14 Tage)
    $chartData = $db->fetchAll(
        "SELECT DATE(created_at) as date, COUNT(*) as leads, COALESCE(SUM(conversions), 0) as conversions
         FROM leads
         WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
         GROUP BY DATE(created_at)
         ORDER BY date ASC",
        [$customer['id']]
    );
    
    // Top Share-Quellen
    $topSources = $db->fetchAll(
        "SELECT platform, COUNT(*) as count
         FROM share_tracking
         WHERE customer_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
         GROUP BY platform
         ORDER BY count DESC
         LIMIT 5",
        [$customer['id']]
    );
}

// Trend berechnen
function calculateTrend($current, $previous) {
    if ($previous == 0) return $current > 0 ? 100 : 0;
    return round((($current - $previous) / $previous) * 100, 1);
}

$leadsTrend = calculateTrend($analytics['leads_30d'] ?? 0, $analytics['leads_prev'] ?? 0);
$conversionsTrend = calculateTrend($analytics['conversions_30d'] ?? 0, $analytics['conversions_prev'] ?? 0);
$clicksTrend = calculateTrend($analytics['clicks_30d'] ?? 0, $analytics['clicks_prev'] ?? 0);

// Conversion Rate
$conversionRate = ($analytics['clicks_30d'] ?? 0) > 0 
    ? round((($analytics['conversions_30d'] ?? 0) / $analytics['clicks_30d']) * 100, 1) 
    : 0;

// Source Icons
$sourceIcons = [
    'whatsapp' => ['icon' => 'fab fa-whatsapp', 'color' => '#25D366'],
    'facebook' => ['icon' => 'fab fa-facebook', 'color' => '#1877F2'],
    'linkedin' => ['icon' => 'fab fa-linkedin', 'color' => '#0A66C2'],
    'twitter' => ['icon' => 'fab fa-x-twitter', 'color' => '#000000'],
    'email' => ['icon' => 'fas fa-envelope', 'color' => '#6366f1'],
    'sms' => ['icon' => 'fas fa-comment-sms', 'color' => '#10b981'],
    'link_copy' => ['icon' => 'fas fa-copy', 'color' => '#6b7280'],
    'qr_code' => ['icon' => 'fas fa-qrcode', 'color' => '#8b5cf6'],
    'telegram' => ['icon' => 'fab fa-telegram', 'color' => '#0088cc'],
    'direct' => ['icon' => 'fas fa-link', 'color' => '#64748b']
];
?>

<div class="dashboard-module module-analytics-basic p-6" data-module="analytics_basic">
    <div class="module-header flex justify-between items-center mb-6">
        <div>
            <h3 class="module-title text-lg font-bold text-gray-800 dark:text-white">
                <i class="fas fa-chart-line text-primary-500 mr-2"></i>
                Analytics
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Performance der letzten 30 Tage
            </p>
        </div>
        
        <?php if ($isAvailable): ?>
        <div class="flex items-center gap-2">
            <select id="analyticsPeriod" onchange="updateAnalytics(this.value)" 
                    class="text-sm bg-gray-100 dark:bg-gray-700 border-0 rounded-lg px-3 py-1.5">
                <option value="7">7 Tage</option>
                <option value="30" selected>30 Tage</option>
                <option value="90">90 Tage</option>
            </select>
        </div>
        <?php else: ?>
        <span class="badge bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-medium">
            <i class="fas fa-star mr-1"></i> Professional
        </span>
        <?php endif; ?>
    </div>
    
    <?php if (!$isAvailable): ?>
    <!-- Upgrade-Hinweis -->
    <div class="upgrade-notice bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 text-center">
        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-chart-pie text-blue-500 text-2xl"></i>
        </div>
        <h4 class="font-bold text-gray-800 dark:text-white mb-2">
            Detaillierte Analytics freischalten
        </h4>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
            Verstehen Sie Ihre Performance mit Trends, Charts und Top-Quellen.
        </p>
        <a href="/dashboard/upgrade.php" class="btn btn-primary btn-sm">
            <i class="fas fa-arrow-up mr-2"></i>
            Jetzt upgraden
        </a>
    </div>
    
    <?php else: ?>
    
    <div class="module-content">
        <!-- KPI-Grid -->
        <div class="kpi-grid grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Empfehler -->
            <div class="kpi-card p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-blue-600 dark:text-blue-400">Neue Empfehler</span>
                    <span class="text-xs px-1.5 py-0.5 rounded <?= $leadsTrend >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' ?>">
                        <?= $leadsTrend >= 0 ? '+' : '' ?><?= $leadsTrend ?>%
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= number_format($analytics['leads_30d'] ?? 0, 0, ',', '.') ?>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    vs. <?= number_format($analytics['leads_prev'] ?? 0, 0, ',', '.') ?> vorher
                </div>
            </div>
            
            <!-- Conversions -->
            <div class="kpi-card p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-green-600 dark:text-green-400">Neue <?= e($customerTerm) ?></span>
                    <span class="text-xs px-1.5 py-0.5 rounded <?= $conversionsTrend >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' ?>">
                        <?= $conversionsTrend >= 0 ? '+' : '' ?><?= $conversionsTrend ?>%
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= number_format($analytics['conversions_30d'] ?? 0, 0, ',', '.') ?>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    vs. <?= number_format($analytics['conversions_prev'] ?? 0, 0, ',', '.') ?> vorher
                </div>
            </div>
            
            <!-- Klicks -->
            <div class="kpi-card p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-purple-600 dark:text-purple-400">Link-Klicks</span>
                    <span class="text-xs px-1.5 py-0.5 rounded <?= $clicksTrend >= 0 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' ?>">
                        <?= $clicksTrend >= 0 ? '+' : '' ?><?= $clicksTrend ?>%
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= number_format($analytics['clicks_30d'] ?? 0, 0, ',', '.') ?>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    vs. <?= number_format($analytics['clicks_prev'] ?? 0, 0, ',', '.') ?> vorher
                </div>
            </div>
            
            <!-- Conversion Rate -->
            <div class="kpi-card p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-amber-600 dark:text-amber-400">Conv.-Rate</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        Klicks → <?= e($customerTerm) ?>
                    </span>
                </div>
                <div class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= $conversionRate ?>%
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    <?= $conversionRate > 10 ? '🎉 Sehr gut!' : ($conversionRate > 5 ? '👍 Gut' : '📈 Ausbaufähig') ?>
                </div>
            </div>
        </div>
        
        <!-- Chart -->
        <div class="chart-section bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                    Entwicklung (14 Tage)
                </h4>
                <div class="flex items-center gap-4 text-xs">
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-blue-500 rounded"></span>
                        Empfehler
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-3 h-3 bg-green-500 rounded"></span>
                        <?= e($customerTerm) ?>
                    </span>
                </div>
            </div>
            <div class="h-48">
                <canvas id="analyticsChart"></canvas>
            </div>
        </div>
        
        <!-- Top Quellen -->
        <?php if (!empty($topSources)): ?>
        <div class="top-sources">
            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">
                <i class="fas fa-share-alt mr-2"></i>
                Top Sharing-Quellen
            </h4>
            <div class="space-y-2">
                <?php 
                $maxCount = $topSources[0]['count'] ?? 1;
                foreach ($topSources as $source): 
                    $sourceInfo = $sourceIcons[$source['platform']] ?? ['icon' => 'fas fa-link', 'color' => '#6b7280'];
                    $percentage = round(($source['count'] / $maxCount) * 100);
                ?>
                <div class="source-item flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                         style="background-color: <?= $sourceInfo['color'] ?>20">
                        <i class="<?= $sourceInfo['icon'] ?>" style="color: <?= $sourceInfo['color'] ?>"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-200 capitalize">
                                <?= str_replace('_', ' ', $source['platform']) ?>
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                <?= number_format($source['count'], 0, ',', '.') ?>
                            </span>
                        </div>
                        <div class="h-1.5 bg-gray-200 dark:bg-gray-600 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-500"
                                 style="width: <?= $percentage ?>%; background-color: <?= $sourceInfo['color'] ?>"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="no-sources text-center py-4 text-sm text-gray-500 dark:text-gray-400">
            <i class="fas fa-info-circle mr-2"></i>
            Noch keine Sharing-Daten verfügbar
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php if ($isAvailable): ?>
<script>
// Chart initialisieren
document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?= json_encode($chartData) ?>;
    const isDark = document.documentElement.classList.contains('dark');
    
    // Labels für letzte 14 Tage
    const labels = [];
    const leadsData = [];
    const conversionsData = [];
    
    for (let i = 13; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        const dateStr = date.toISOString().split('T')[0];
        labels.push(date.toLocaleDateString('de-DE', { day: '2-digit', month: '2-digit' }));
        
        const found = chartData.find(d => d.date === dateStr);
        leadsData.push(found ? parseInt(found.leads) : 0);
        conversionsData.push(found ? parseInt(found.conversions) : 0);
    }
    
    const ctx = document.getElementById('analyticsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Empfehler',
                        data: leadsData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 2,
                        pointHoverRadius: 5
                    },
                    {
                        label: '<?= e($customerTerm) ?>',
                        data: conversionsData,
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 2,
                        pointHoverRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: isDark ? '#ffffff' : '#1e293b',
                        bodyColor: isDark ? '#94a3b8' : '#64748b',
                        borderColor: isDark ? '#334155' : '#e2e8f0',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { 
                            color: isDark ? '#94a3b8' : '#64748b',
                            maxRotation: 0
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: isDark ? '#334155' : '#e2e8f0' },
                        ticks: { 
                            color: isDark ? '#94a3b8' : '#64748b',
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});

function updateAnalytics(period) {
    // AJAX-Update für andere Zeiträume
    // Für MVP: Seite neu laden mit Parameter
    window.location.href = `/dashboard/?period=${period}`;
}
</script>
<?php endif; ?>

<style>
.kpi-card {
    transition: transform 0.2s ease;
}

.kpi-card:hover {
    transform: translateY(-2px);
}

.source-item:hover .h-1\.5 > div {
    opacity: 0.8;
}
</style>
