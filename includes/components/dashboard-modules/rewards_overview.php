<?php
/**
 * Dashboard Module: Rewards Overview
 * Verfügbar für: Alle Tarife
 * Zeigt die konfigurierten Belohnungsstufen
 */

if (!isset($customer) || !isset($campaign)) {
    return;
}

// Belohnungen laden
$rewards = $db->fetchAll(
    "SELECT * FROM rewards WHERE customer_id = ? AND is_active = TRUE ORDER BY required_referrals ASC",
    [$customer['id']]
) ?: [];

// Branchenspezifische Texte
$customerTerm = $dashboardLayout['customer_term'] ?? 'Kunden';
$referralTerm = $dashboardLayout['referral_term'] ?? 'Empfehlung';

// Plan-Limits
$maxRewards = $customer['plan'] === 'starter' ? 3 : ($customer['plan'] === 'professional' ? 5 : 10);
$currentCount = count($rewards);
$canAddMore = $currentCount < $maxRewards;

// Reward Icons basierend auf Typ
$rewardIcons = [
    'discount' => 'fas fa-percent',
    'voucher' => 'fas fa-ticket-alt',
    'free_product' => 'fas fa-gift',
    'free_service' => 'fas fa-concierge-bell',
    'download' => 'fas fa-download',
    'cashback' => 'fas fa-money-bill-wave',
    'exclusive' => 'fas fa-crown',
    'default' => 'fas fa-star'
];
?>

<div class="dashboard-module module-rewards-overview" data-module="rewards_overview">
    <div class="module-header flex justify-between items-center">
        <h3 class="module-title">
            <i class="fas fa-gift"></i>
            Belohnungsstufen
        </h3>
        <a href="/dashboard/rewards.php" class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
            Bearbeiten <i class="fas fa-arrow-right ml-1"></i>
        </a>
    </div>
    
    <div class="module-content">
        <?php if (empty($rewards)): ?>
            <!-- Keine Belohnungen konfiguriert -->
            <div class="empty-state text-center py-6">
                <div class="w-16 h-16 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-gift text-amber-500 text-2xl"></i>
                </div>
                <p class="text-gray-600 dark:text-gray-300 font-medium mb-2">
                    Noch keine Belohnungen eingerichtet
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Motivieren Sie Ihre <?= htmlspecialchars($customerTerm) ?> mit attraktiven Prämien!
                </p>
                <a href="/dashboard/rewards.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Belohnungen einrichten
                </a>
            </div>
        <?php else: ?>
            <!-- Belohnungen anzeigen -->
            <div class="rewards-list space-y-3">
                <?php foreach ($rewards as $index => $reward): ?>
                    <?php 
                    $icon = $rewardIcons[$reward['type'] ?? 'default'] ?? $rewardIcons['default'];
                    $isFirst = $index === 0;
                    ?>
                    <div class="reward-item flex items-center gap-4 p-3 rounded-xl transition
                                <?= $isFirst ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800' : 'bg-gray-50 dark:bg-gray-700/50' ?>">
                        
                        <!-- Stufen-Badge -->
                        <div class="reward-level flex-shrink-0 w-12 h-12 rounded-xl flex flex-col items-center justify-center
                                    <?= $isFirst ? 'bg-primary-500 text-white' : 'bg-gray-200 dark:bg-gray-600 text-gray-600 dark:text-gray-300' ?>">
                            <span class="text-lg font-bold"><?= (int)$reward['required_referrals'] ?></span>
                            <span class="text-[10px] uppercase tracking-wide"><?= $reward['required_referrals'] == 1 ? 'Empf.' : 'Empf.' ?></span>
                        </div>
                        
                        <!-- Reward Info -->
                        <div class="reward-info flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <i class="<?= $icon ?> text-gray-400"></i>
                                <span class="font-medium text-gray-800 dark:text-white truncate">
                                    <?= htmlspecialchars($reward['title'] ?? $reward['name'] ?? 'Belohnung') ?>
                                </span>
                            </div>
                            <?php if (!empty($reward['description'])): ?>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate mt-0.5">
                                <?= htmlspecialchars($reward['description']) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Status/Aktionen -->
                        <div class="reward-status flex-shrink-0">
                            <?php if ($isFirst): ?>
                            <span class="text-xs text-primary-600 dark:text-primary-400 font-medium">
                                Erste Stufe
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Neue Stufe hinzufügen -->
            <?php if ($canAddMore): ?>
            <a href="/dashboard/rewards.php?action=add" 
               class="add-reward-btn flex items-center justify-center gap-2 mt-4 p-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl text-gray-500 dark:text-gray-400 hover:border-primary-400 hover:text-primary-500 transition">
                <i class="fas fa-plus"></i>
                <span>Stufe hinzufügen (<?= $currentCount ?>/<?= $maxRewards ?>)</span>
            </a>
            <?php else: ?>
            <div class="limit-reached mt-4 p-3 bg-gray-100 dark:bg-gray-700 rounded-xl text-center text-sm text-gray-500 dark:text-gray-400">
                <i class="fas fa-lock mr-2"></i>
                Maximum erreicht (<?= $maxRewards ?> Stufen im <?= ucfirst($customer['plan']) ?>-Plan)
                <?php if ($customer['plan'] !== 'enterprise'): ?>
                <a href="/dashboard/upgrade.php" class="text-primary-600 dark:text-primary-400 hover:underline ml-1">
                    Upgrade für mehr
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <!-- Belohnungs-Statistik -->
        <?php 
        $deliveredRewards = $db->fetch(
            "SELECT COUNT(*) as count FROM reward_deliveries rd 
             JOIN leads l ON rd.lead_id = l.id 
             WHERE l.customer_id = ? AND rd.status = 'delivered'",
            [$customer['id']]
        );
        $pendingRewards = $db->fetch(
            "SELECT COUNT(*) as count FROM reward_deliveries rd 
             JOIN leads l ON rd.lead_id = l.id 
             WHERE l.customer_id = ? AND rd.status = 'pending'",
            [$customer['id']]
        );
        ?>
        <?php if (($deliveredRewards['count'] ?? 0) > 0 || ($pendingRewards['count'] ?? 0) > 0): ?>
        <div class="rewards-stats mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500 dark:text-gray-400">
                    <i class="fas fa-check-circle text-green-500 mr-1"></i>
                    <?= (int)($deliveredRewards['count'] ?? 0) ?> Belohnungen versendet
                </span>
                <?php if (($pendingRewards['count'] ?? 0) > 0): ?>
                <span class="text-amber-600 dark:text-amber-400">
                    <i class="fas fa-clock mr-1"></i>
                    <?= (int)$pendingRewards['count'] ?> ausstehend
                </span>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
