<?php
/**
 * Lead Dashboard - History Tab
 * Zeigt alle Empfehlungen mit Details
 */
?>

<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Ihre Empfehlungen</h1>
            <p class="text-gray-500">Übersicht aller Personen, die sich über Ihren Link angemeldet haben</p>
        </div>
        
        <div class="flex items-center gap-3">
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg">
                <span class="font-bold"><?= count(array_filter($conversions, fn($c) => $c['status'] === 'confirmed')) ?></span>
                <span class="text-sm">Bestätigt</span>
            </div>
            <div class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-lg">
                <span class="font-bold"><?= count(array_filter($conversions, fn($c) => $c['status'] === 'pending')) ?></span>
                <span class="text-sm">Ausstehend</span>
            </div>
        </div>
    </div>
    
    <?php if (empty($conversions)): ?>
    <!-- Leerer Zustand -->
    <div class="card p-12 text-center">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-user-friends text-3xl text-gray-400"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Noch keine Empfehlungen</h3>
        <p class="text-gray-500 mb-6 max-w-md mx-auto">
            Teilen Sie Ihren persönlichen Empfehlungslink mit Freunden, Familie und Bekannten. 
            Sobald sich jemand anmeldet, erscheint er hier.
        </p>
        <a href="?tab=overview" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-xl hover:opacity-90">
            <i class="fas fa-share-alt"></i>
            Jetzt teilen
        </a>
    </div>
    
    <?php else: ?>
    
    <!-- Timeline -->
    <div class="card overflow-hidden">
        <div class="p-6 border-b bg-gray-50">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Empfehlungs-Timeline</h2>
                <span class="text-sm text-gray-500"><?= count($conversions) ?> Einträge</span>
            </div>
        </div>
        
        <div class="divide-y">
            <?php foreach ($conversions as $index => $conv): ?>
            <div class="p-6 hover:bg-gray-50 transition">
                <div class="flex items-start gap-4">
                    <!-- Avatar/Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold
                            <?php if ($conv['status'] === 'confirmed'): ?>
                            bg-green-100 text-green-600
                            <?php elseif ($conv['status'] === 'rejected'): ?>
                            bg-red-100 text-red-600
                            <?php else: ?>
                            bg-yellow-100 text-yellow-600
                            <?php endif; ?>">
                            <?= strtoupper(substr($conv['referred_name'] ?: $conv['referred_email'], 0, 1)) ?>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 flex-wrap">
                            <h3 class="font-semibold text-gray-900">
                                <?= htmlspecialchars($conv['referred_name'] ?: 'Unbenannt') ?>
                            </h3>
                            
                            <?php if ($conv['status'] === 'confirmed'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                                <i class="fas fa-check text-xs"></i> Bestätigt
                            </span>
                            <?php elseif ($conv['status'] === 'rejected'): ?>
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">
                                <i class="fas fa-times text-xs"></i> Abgelehnt
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">
                                <i class="fas fa-clock text-xs"></i> Ausstehend
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="text-sm text-gray-500 mt-1">
                            Hat sich am <?= date('d.m.Y', strtotime($conv['referred_at'])) ?> um <?= date('H:i', strtotime($conv['referred_at'])) ?> Uhr angemeldet
                        </p>
                        
                        <?php if ($conv['status'] === 'confirmed' && $conv['confirmed_at']): ?>
                        <p class="text-sm text-green-600 mt-1">
                            <i class="fas fa-check-circle mr-1"></i>
                            Bestätigt am <?= date('d.m.Y', strtotime($conv['confirmed_at'])) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Points -->
                    <div class="flex-shrink-0 text-right">
                        <?php if ($conv['status'] === 'confirmed'): ?>
                        <div class="text-lg font-bold text-primary">+50</div>
                        <div class="text-xs text-gray-500">Punkte</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Statistik-Karten -->
    <div class="grid md:grid-cols-3 gap-4">
        <div class="card p-6 text-center">
            <div class="text-4xl font-bold text-gray-900 mb-2"><?= count($conversions) ?></div>
            <div class="text-gray-500">Gesamt-Anmeldungen</div>
        </div>
        
        <div class="card p-6 text-center">
            <?php
            $conversionRate = $stats['clicks'] > 0 
                ? round(($stats['conversions'] / $stats['clicks']) * 100, 1) 
                : 0;
            ?>
            <div class="text-4xl font-bold text-primary mb-2"><?= $conversionRate ?>%</div>
            <div class="text-gray-500">Conversion-Rate</div>
        </div>
        
        <div class="card p-6 text-center">
            <?php
            $thisMonth = count(array_filter($conversions, function($c) {
                return date('Y-m', strtotime($c['created_at'])) === date('Y-m');
            }));
            ?>
            <div class="text-4xl font-bold text-green-600 mb-2"><?= $thisMonth ?></div>
            <div class="text-gray-500">Diesen Monat</div>
        </div>
    </div>
    
    <?php endif; ?>
    
    <!-- Tipps -->
    <div class="card p-6 bg-gradient-to-r from-primary/5 to-primary/10 border border-primary/20">
        <div class="flex items-start gap-4">
            <div class="flex-shrink-0 w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center">
                <i class="fas fa-lightbulb text-primary"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">💡 Tipp: Mehr Empfehlungen bekommen</h3>
                <ul class="text-sm text-gray-600 space-y-1">
                    <li>• Teilen Sie den Link direkt per WhatsApp mit Freunden</li>
                    <li>• Erwähnen Sie konkret, warum Sie <?= htmlspecialchars($customer['company_name']) ?> empfehlen</li>
                    <li>• Nutzen Sie den QR-Code für persönliche Gespräche</li>
                </ul>
            </div>
        </div>
    </div>
    
</div>
