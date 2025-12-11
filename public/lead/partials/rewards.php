<?php
/**
 * Lead Dashboard - Rewards Tab
 * Belohnungs-Center mit Einlösemöglichkeit
 */

// Sortieren: Freigeschaltete oben
$sortedRewards = [];
foreach ($rewards as $reward) {
    $unlocked = array_filter($unlockedRewards, fn($ur) => $ur['reward_id'] == $reward['id']);
    $reward['is_unlocked'] = !empty($unlocked);
    $reward['delivery'] = !empty($unlocked) ? array_values($unlocked)[0] : null;
    $sortedRewards[] = $reward;
}

usort($sortedRewards, function($a, $b) {
    if ($a['is_unlocked'] && !$b['is_unlocked']) return -1;
    if (!$a['is_unlocked'] && $b['is_unlocked']) return 1;
    return $a['level'] - $b['level'];
});
?>

<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ihre Belohnungen</h1>
            <p class="text-gray-500">Hier können Sie Ihre freigeschalteten Belohnungen einlösen</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg">
                <span class="font-bold"><?= count($unlockedRewards) ?></span>
                <span class="text-sm">Freigeschaltet</span>
            </div>
            <div class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg">
                <span class="font-bold"><?= count($rewards) - count($unlockedRewards) ?></span>
                <span class="text-sm">Noch offen</span>
            </div>
        </div>
    </div>
    
    <!-- Freigeschaltete Belohnungen -->
    <?php 
    $unlockedList = array_filter($sortedRewards, fn($r) => $r['is_unlocked']);
    if (!empty($unlockedList)): 
    ?>
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-gift text-green-500"></i>
            Jetzt einlösen
        </h2>
        
        <div class="grid md:grid-cols-2 gap-4">
            <?php foreach ($unlockedList as $reward): ?>
            <div class="reward-card card p-6 border-2 border-green-400 bg-gradient-to-br from-green-50 to-white relative overflow-hidden">
                <!-- Stufen-Badge -->
                <div class="absolute top-4 right-4">
                    <span class="px-3 py-1 bg-green-500 text-white text-sm font-bold rounded-full">
                        Stufe <?= $reward['level'] ?>
                    </span>
                </div>
                
                <!-- Icon -->
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-4">
                    <?php
                    $icon = match($reward['reward_type'] ?? 'discount') {
                        'discount' => '💰',
                        'coupon' => '🎟️',
                        'product' => '🎁',
                        'service' => '⭐',
                        'download' => '📥',
                        default => '🎁'
                    };
                    echo "<span class='text-3xl'>$icon</span>";
                    ?>
                </div>
                
                <!-- Content -->
                <h3 class="text-xl font-bold text-gray-900 mb-2 pr-20">
                    <?= htmlspecialchars($reward['description']) ?>
                </h3>
                
                <p class="text-gray-500 text-sm mb-4">
                    Erreicht mit <?= $reward['required_conversions'] ?> Empfehlungen
                </p>
                
                <!-- Status & Aktion -->
                <?php if ($reward['delivery']): ?>
                    <?php if ($reward['delivery']['status'] === 'claimed'): ?>
                    <div class="flex items-center gap-2 text-green-600 mb-4">
                        <i class="fas fa-check-circle"></i>
                        <span>Bereits eingelöst am <?= date('d.m.Y', strtotime($reward['delivery']['claimed_at'])) ?></span>
                    </div>
                    <?php elseif ($reward['delivery']['status'] === 'sent' || $reward['delivery']['status'] === 'delivered'): ?>
                    <div class="bg-white border border-gray-200 rounded-xl p-4 mb-4">
                        <?php if ($reward['reward_type'] === 'download' && $reward['download_file_path']): ?>
                        <!-- Download-Button -->
                        <a href="/lead/download.php?token=<?= htmlspecialchars($reward['delivery']['download_token']) ?>" 
                           class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-primary text-white rounded-lg hover:opacity-90 transition">
                            <i class="fas fa-download"></i>
                            <?= htmlspecialchars($reward['download_file_name'] ?: 'Datei herunterladen') ?>
                        </a>
                        <?php if ($reward['delivery']['download_expires_at']): ?>
                        <p class="text-xs text-gray-500 text-center mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            Gültig bis <?= date('d.m.Y H:i', strtotime($reward['delivery']['download_expires_at'])) ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php elseif ($reward['reward_type'] === 'coupon'): ?>
                        <!-- Gutschein-Code -->
                        <div class="text-center">
                            <p class="text-sm text-gray-500 mb-2">Ihr Gutschein-Code:</p>
                            <div class="flex items-center justify-center gap-2">
                                <code class="px-4 py-2 bg-gray-100 rounded-lg font-mono text-lg font-bold text-gray-900" id="code-<?= $reward['id'] ?>">
                                    <?= htmlspecialchars($reward['coupon_code'] ?? 'BONUS-XXXX') ?>
                                </code>
                                <button onclick="copyCode('code-<?= $reward['id'] ?>')" 
                                        class="p-2 bg-gray-200 hover:bg-gray-300 rounded-lg transition">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <?php else: ?>
                        <!-- Anleitung zur Einlösung -->
                        <?php if ($reward['instructions']): ?>
                        <div class="prose prose-sm">
                            <p class="text-gray-600"><?= nl2br(htmlspecialchars($reward['instructions'])) ?></p>
                        </div>
                        <?php else: ?>
                        <p class="text-gray-600 text-center">
                            <i class="fas fa-info-circle text-primary mr-2"></i>
                            Die Einlöseinformationen wurden Ihnen per E-Mail zugesendet.
                        </p>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Als eingelöst markieren -->
                    <?php if ($reward['delivery']['status'] !== 'claimed'): ?>
                    <button onclick="markAsClaimed(<?= $reward['delivery']['id'] ?>)" 
                            class="w-full px-4 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 transition text-sm">
                        <i class="fas fa-check mr-2"></i>Als eingelöst markieren
                    </button>
                    <?php endif; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Alle Belohnungsstufen -->
    <div class="space-y-4">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <i class="fas fa-list-check text-gray-400"></i>
            Alle Belohnungsstufen
        </h2>
        
        <div class="card overflow-hidden">
            <div class="divide-y">
                <?php foreach ($rewards as $index => $reward): 
                    $isUnlocked = $stats['conversions'] >= $reward['required_conversions'];
                    $progressPercent = min(100, ($stats['conversions'] / $reward['required_conversions']) * 100);
                ?>
                <div class="p-5 flex items-center gap-4 <?= $isUnlocked ? 'bg-green-50' : '' ?>">
                    <!-- Level Badge -->
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl flex items-center justify-center text-xl font-bold
                        <?= $isUnlocked ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500' ?>">
                        <?= $isUnlocked ? '✓' : $reward['level'] ?>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="font-semibold text-gray-900"><?= htmlspecialchars($reward['description']) ?></h3>
                            <?php if ($isUnlocked): ?>
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs rounded-full">Freigeschaltet</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center gap-4 mt-2">
                            <span class="text-sm text-gray-500">
                                <?= $reward['required_conversions'] ?> Empfehlungen benötigt
                            </span>
                            
                            <?php if (!$isUnlocked): ?>
                            <!-- Progress Bar -->
                            <div class="flex-1 max-w-32">
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full transition-all duration-500" 
                                         style="width: <?= $progressPercent ?>%"></div>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">
                                <?= $stats['conversions'] ?>/<?= $reward['required_conversions'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action -->
                    <div class="flex-shrink-0">
                        <?php if ($isUnlocked): ?>
                        <a href="#" class="text-primary hover:underline text-sm">Details →</a>
                        <?php else: ?>
                        <span class="text-sm text-gray-400">
                            Noch <?= $reward['required_conversions'] - $stats['conversions'] ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Info-Box -->
    <div class="card p-6 bg-blue-50 border border-blue-200">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-info text-blue-600"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-1">Wie funktionieren Belohnungen?</h3>
                <p class="text-sm text-gray-600">
                    Je mehr Personen sich über Ihren Link anmelden, desto mehr Belohnungen schalten Sie frei.
                    Sobald eine Stufe erreicht ist, erhalten Sie automatisch eine E-Mail mit den Details zur Einlösung.
                    Alle Belohnungen bleiben dauerhaft in Ihrem Konto gespeichert.
                </p>
            </div>
        </div>
    </div>
    
</div>

<script>
function copyCode(elementId) {
    const codeElement = document.getElementById(elementId);
    navigator.clipboard.writeText(codeElement.textContent.trim()).then(() => {
        showToast('Code kopiert!');
    });
}

function markAsClaimed(deliveryId) {
    if (!confirm('Möchten Sie diese Belohnung als eingelöst markieren?')) return;
    
    fetch('/api/rewards/claim.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ delivery_id: deliveryId })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast('Als eingelöst markiert!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.error || 'Fehler aufgetreten');
        }
    })
    .catch(() => showToast('Fehler beim Speichern'));
}
</script>
