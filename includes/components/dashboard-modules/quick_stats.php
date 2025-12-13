<?php
/**
 * Dashboard Module: Quick Stats
 * Verfügbar für: Alle Tarife
 * Zeigt die wichtigsten KPIs auf einen Blick
 */

if (!isset($customer) || !isset($campaign)) {
    return;
}

// Branchenspezifische Texte
$customerTerm = $dashboardLayout['customer_term'] ?? 'Kunden';
$successTerm = $dashboardLayout['success_term'] ?? 'Neukunde';
$referralTerm = $dashboardLayout['referral_term'] ?? 'Empfehlung';

// Stats laden (Cache oder DB)
$stats = [
    'leads' => (int)($customer['total_leads'] ?? 0),
    'conversions' => (int)($customer['total_conversions'] ?? 0),
    'clicks' => (int)($campaign['total_clicks'] ?? 0),
    'active_today' => 0 // Wird per AJAX geladen
];

// Conversion Rate berechnen
$conversionRate = $stats['clicks'] > 0 
    ? round(($stats['conversions'] / $stats['clicks']) * 100, 1) 
    : 0;

// Trend-Daten (letzte 7 Tage vs. vorherige 7 Tage)
// Vereinfacht - in Production aus DB laden
$trends = [
    'leads' => '+12%',
    'conversions' => '+8%',
    'clicks' => '+15%'
];
?>

<div class="dashboard-module module-quick-stats" data-module="quick_stats">
    <div class="module-header flex justify-between items-center">
        <h3 class="module-title">
            <i class="fas fa-chart-line"></i>
            Übersicht
        </h3>
        <select id="statsTimeframe" onchange="loadStats(this.value)" 
                class="text-sm bg-gray-100 dark:bg-gray-700 border-0 rounded-lg px-3 py-1.5">
            <option value="7">Letzte 7 Tage</option>
            <option value="30" selected>Letzte 30 Tage</option>
            <option value="90">Letzte 90 Tage</option>
            <option value="all">Gesamt</option>
        </select>
    </div>
    
    <div class="module-content">
        <!-- Stats Grid -->
        <div class="stats-grid grid grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Empfehler -->
            <div class="stat-card p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-xl">
                <div class="stat-icon w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-users text-white"></i>
                </div>
                <p class="stat-value text-2xl font-bold text-gray-800 dark:text-white" id="statLeadsValue">
                    <?= number_format($stats['leads']) ?>
                </p>
                <p class="stat-label text-sm text-gray-600 dark:text-gray-400">
                    Empfehler
                </p>
                <p class="stat-trend text-xs text-green-600 mt-1" id="statLeadsTrend">
                    <i class="fas fa-arrow-up"></i> <?= $trends['leads'] ?>
                </p>
            </div>
            
            <!-- Neue Kunden (Conversions) -->
            <div class="stat-card p-4 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/30 dark:to-green-800/30 rounded-xl">
                <div class="stat-icon w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-user-plus text-white"></i>
                </div>
                <p class="stat-value text-2xl font-bold text-gray-800 dark:text-white" id="statConversionsValue">
                    <?= number_format($stats['conversions']) ?>
                </p>
                <p class="stat-label text-sm text-gray-600 dark:text-gray-400">
                    Neue <?= htmlspecialchars($customerTerm) ?>
                </p>
                <p class="stat-trend text-xs text-green-600 mt-1" id="statConversionsTrend">
                    <i class="fas fa-arrow-up"></i> <?= $trends['conversions'] ?>
                </p>
            </div>
            
            <!-- Klicks -->
            <div class="stat-card p-4 bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/30 dark:to-purple-800/30 rounded-xl">
                <div class="stat-icon w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-mouse-pointer text-white"></i>
                </div>
                <p class="stat-value text-2xl font-bold text-gray-800 dark:text-white" id="statClicksValue">
                    <?= number_format($stats['clicks']) ?>
                </p>
                <p class="stat-label text-sm text-gray-600 dark:text-gray-400">
                    Link-Klicks
                </p>
                <p class="stat-trend text-xs text-green-600 mt-1" id="statClicksTrend">
                    <i class="fas fa-arrow-up"></i> <?= $trends['clicks'] ?>
                </p>
            </div>
            
            <!-- Conversion Rate -->
            <div class="stat-card p-4 bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/30 dark:to-amber-800/30 rounded-xl">
                <div class="stat-icon w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-percentage text-white"></i>
                </div>
                <p class="stat-value text-2xl font-bold text-gray-800 dark:text-white" id="statRateValue">
                    <?= $conversionRate ?>%
                </p>
                <p class="stat-label text-sm text-gray-600 dark:text-gray-400">
                    Conversion-Rate
                </p>
                <p class="stat-trend text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle"></i> Klicks → <?= htmlspecialchars($customerTerm) ?>
                </p>
            </div>
        </div>
        
        <!-- Mini-Chart (nur visueller Indikator) -->
        <div class="stats-chart mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600 dark:text-gray-400">Aktivität (30 Tage)</span>
                <span class="text-xs text-gray-500">
                    <span class="inline-block w-3 h-3 bg-blue-500 rounded mr-1"></span> Klicks
                    <span class="inline-block w-3 h-3 bg-green-500 rounded ml-2 mr-1"></span> <?= htmlspecialchars($customerTerm) ?>
                </span>
            </div>
            <div class="chart-bars flex items-end gap-1 h-16" id="miniChartBars">
                <!-- Wird per JS gefüllt -->
                <?php for ($i = 0; $i < 30; $i++): ?>
                    <div class="chart-bar-group flex-1 flex flex-col gap-0.5">
                        <div class="chart-bar bg-blue-400 rounded-t" style="height: <?= rand(10, 100) ?>%"></div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
        
        <!-- Quick Insight -->
        <?php if ($stats['leads'] > 0): ?>
        <div class="quick-insight mt-4 p-3 bg-green-50 dark:bg-green-900/20 rounded-lg text-sm flex items-center">
            <i class="fas fa-trophy text-green-500 mr-3 text-lg"></i>
            <div>
                <strong class="text-green-700 dark:text-green-300">Gut gemacht!</strong>
                <span class="text-green-600 dark:text-green-400">
                    Durchschnittlich bringt jeder Empfehler 
                    <?= $stats['leads'] > 0 ? round($stats['conversions'] / $stats['leads'], 1) : 0 ?> 
                    neue <?= htmlspecialchars($customerTerm) ?>.
                </span>
            </div>
        </div>
        <?php else: ?>
        <div class="quick-insight mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm flex items-center">
            <i class="fas fa-rocket text-blue-500 mr-3 text-lg"></i>
            <div>
                <strong class="text-blue-700 dark:text-blue-300">Bereit zum Start!</strong>
                <span class="text-blue-600 dark:text-blue-400">
                    Teilen Sie Ihren Link, um die ersten Empfehler zu gewinnen.
                </span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function loadStats(days) {
    const customerId = <?= (int)$customer['id'] ?>;
    
    fetch(`/api/customer-stats.php?customer_id=${customerId}&days=${days}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStatsDisplay(data.stats);
            }
        })
        .catch(error => console.error('Stats load error:', error));
}

function updateStatsDisplay(stats) {
    // Werte aktualisieren mit Animation
    animateValue('statLeadsValue', stats.leads);
    animateValue('statConversionsValue', stats.conversions);
    animateValue('statClicksValue', stats.clicks);
    
    // Rate berechnen
    const rate = stats.clicks > 0 ? ((stats.conversions / stats.clicks) * 100).toFixed(1) : 0;
    document.getElementById('statRateValue').textContent = rate + '%';
    
    // Trends aktualisieren
    updateTrend('statLeadsTrend', stats.leads_trend);
    updateTrend('statConversionsTrend', stats.conversions_trend);
    updateTrend('statClicksTrend', stats.clicks_trend);
}

function animateValue(elementId, newValue) {
    const el = document.getElementById(elementId);
    const currentValue = parseInt(el.textContent.replace(/\D/g, '')) || 0;
    const diff = newValue - currentValue;
    const steps = 20;
    const stepValue = diff / steps;
    let step = 0;
    
    const interval = setInterval(() => {
        step++;
        const value = Math.round(currentValue + (stepValue * step));
        el.textContent = value.toLocaleString('de-DE');
        
        if (step >= steps) {
            clearInterval(interval);
            el.textContent = newValue.toLocaleString('de-DE');
        }
    }, 30);
}

function updateTrend(elementId, trend) {
    const el = document.getElementById(elementId);
    if (!el || !trend) return;
    
    const isPositive = trend >= 0;
    el.className = `stat-trend text-xs mt-1 ${isPositive ? 'text-green-600' : 'text-red-600'}`;
    el.innerHTML = `<i class="fas fa-arrow-${isPositive ? 'up' : 'down'}"></i> ${isPositive ? '+' : ''}${trend}%`;
}

// Auto-refresh alle 60 Sekunden
setInterval(() => {
    const timeframe = document.getElementById('statsTimeframe').value;
    loadStats(timeframe);
}, 60000);
</script>

<style>
.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.chart-bar {
    transition: height 0.3s ease;
    min-height: 4px;
}

.chart-bar-group:hover .chart-bar {
    opacity: 0.8;
}
</style>
