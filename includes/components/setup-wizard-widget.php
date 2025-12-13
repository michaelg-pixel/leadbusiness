<?php
/**
 * Leadbusiness - Setup Wizard Widget
 * 
 * Zeigt den Einrichtungs-Fortschritt im Dashboard
 * 
 * Usage: include 'setup-wizard-widget.php';
 * Requires: $setupWizard (SetupWizard instance)
 */

if (!isset($setupWizard)) return;

$progress = $setupWizard->getProgress();
$stats = $setupWizard->getStats();
$steps = $setupWizard->getSteps();
$nextStep = $setupWizard->getNextRequiredStep();
$isComplete = $setupWizard->isSetupComplete();
$isHidden = $setupWizard->isHidden();

// Nicht anzeigen wenn ausgeblendet und Setup vollständig
if ($isHidden && $isComplete) return;

// Farben für die Steps
$stepColors = [
    'green' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
    'blue' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
    'cyan' => 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-600 dark:text-cyan-400',
    'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400',
    'purple' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400',
    'amber' => 'bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400',
    'orange' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
    'indigo' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400',
    'pink' => 'bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400',
];
?>

<!-- Setup Wizard Widget -->
<div id="setupWizardWidget" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 mb-6 overflow-hidden">
    
    <!-- Header mit Fortschritt -->
    <div class="bg-gradient-to-r from-primary-500 to-purple-600 p-6 text-white relative overflow-hidden">
        <!-- Hintergrund-Muster -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white rounded-full"></div>
            <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-white rounded-full"></div>
        </div>
        
        <div class="relative">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <?php if ($isComplete): ?>
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold">Einrichtung abgeschlossen! 🎉</h2>
                        <?php else: ?>
                            <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                                <i class="fas fa-rocket text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold">Einrichtung fortsetzen</h2>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!$isComplete && $nextStep): ?>
                    <p class="text-white/80 text-sm mb-4">
                        Nächster Schritt: <strong><?= htmlspecialchars($nextStep['title']) ?></strong>
                    </p>
                    <?php elseif ($isComplete): ?>
                    <p class="text-white/80 text-sm mb-4">
                        Ihr Empfehlungsprogramm ist bereit. Teilen Sie jetzt Ihren Link!
                    </p>
                    <?php endif; ?>
                    
                    <!-- Progress Bar -->
                    <div class="flex items-center gap-4">
                        <div class="flex-1 bg-white/20 rounded-full h-3 overflow-hidden">
                            <div class="bg-white h-full rounded-full transition-all duration-500 ease-out" 
                                 style="width: <?= $progress ?>%"></div>
                        </div>
                        <span class="text-sm font-semibold whitespace-nowrap">
                            <?= $stats['required_completed'] ?>/<?= $stats['required_total'] ?> erledigt
                        </span>
                    </div>
                </div>
                
                <!-- Toggle Button -->
                <button onclick="toggleSetupWizard()" 
                        class="p-2 hover:bg-white/10 rounded-lg transition-colors" 
                        title="Checkliste ein-/ausklappen">
                    <i class="fas fa-chevron-down transition-transform" id="wizardToggleIcon"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Checkliste (einklappbar) -->
    <div id="setupWizardContent" class="p-6">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($steps as $step): 
                $colorClass = $stepColors[$step['color']] ?? $stepColors['blue'];
                $isExternal = !empty($step['external']);
            ?>
            <div class="setup-step group relative flex items-start gap-3 p-4 rounded-xl border-2 transition-all
                <?php if ($step['completed']): ?>
                    border-green-200 dark:border-green-800 bg-green-50/50 dark:bg-green-900/20
                <?php else: ?>
                    border-slate-200 dark:border-slate-600 hover:border-primary-300 dark:hover:border-primary-600 hover:shadow-md
                <?php endif; ?>
                <?= !$step['required'] ? 'opacity-80' : '' ?>">
                
                <!-- Icon -->
                <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                    <?php if ($step['completed']): ?>
                        bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400
                    <?php else: ?>
                        <?= $colorClass ?>
                    <?php endif; ?>">
                    <?php if ($step['completed']): ?>
                        <i class="fas fa-check"></i>
                    <?php else: ?>
                        <i class="fas <?= $step['icon'] ?>"></i>
                    <?php endif; ?>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <h3 class="font-semibold text-sm text-slate-800 dark:text-white truncate">
                            <?= htmlspecialchars($step['title']) ?>
                        </h3>
                        <?php if (!$step['required']): ?>
                        <span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-medium rounded">
                            Optional
                        </span>
                        <?php endif; ?>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">
                        <?= htmlspecialchars($step['description']) ?>
                    </p>
                    
                    <?php if (!$step['completed'] && $step['link']): ?>
                    <a href="<?= htmlspecialchars($step['link']) ?>" 
                       <?= $isExternal ? 'target="_blank" rel="noopener"' : '' ?>
                       class="inline-flex items-center gap-1 text-xs font-medium text-primary-600 dark:text-primary-400 hover:underline mt-2">
                        <?php if ($isExternal): ?>
                            Ansehen <i class="fas fa-external-link-alt text-[10px]"></i>
                        <?php else: ?>
                            Jetzt erledigen <i class="fas fa-arrow-right text-[10px]"></i>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                </div>
                
                <!-- Completed Badge -->
                <?php if ($step['completed']): ?>
                <div class="absolute top-2 right-2">
                    <span class="w-5 h-5 bg-green-500 rounded-full flex items-center justify-center text-white">
                        <i class="fas fa-check text-[10px]"></i>
                    </span>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-6 pt-6 border-t border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400">
                <i class="fas fa-info-circle"></i>
                <span>Optionale Schritte verbessern Ihre Conversion-Rate</span>
            </div>
            
            <div class="flex items-center gap-3">
                <?php if ($isComplete && !$isHidden): ?>
                <button onclick="hideSetupWizard()" 
                        class="text-sm text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300">
                    <i class="fas fa-eye-slash mr-1"></i> Ausblenden
                </button>
                <?php endif; ?>
                
                <?php if ($nextStep && $nextStep['link']): ?>
                <a href="<?= htmlspecialchars($nextStep['link']) ?>" 
                   class="px-4 py-2 bg-primary-500 hover:bg-primary-600 text-white text-sm font-medium rounded-lg transition-colors inline-flex items-center gap-2">
                    <?= htmlspecialchars($nextStep['title']) ?>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle Wizard Content
function toggleSetupWizard() {
    const content = document.getElementById('setupWizardContent');
    const icon = document.getElementById('wizardToggleIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(0deg)';
        localStorage.setItem('setupWizardExpanded', 'true');
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(-90deg)';
        localStorage.setItem('setupWizardExpanded', 'false');
    }
}

// Hide Wizard
function hideSetupWizard() {
    if (confirm('Möchten Sie die Einrichtungs-Checkliste ausblenden? Sie können sie in den Einstellungen wieder aktivieren.')) {
        fetch('/api/setup-wizard/hide', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        }).then(() => {
            document.getElementById('setupWizardWidget').style.display = 'none';
        });
    }
}

// Restore state from localStorage
document.addEventListener('DOMContentLoaded', function() {
    const expanded = localStorage.getItem('setupWizardExpanded');
    if (expanded === 'false') {
        document.getElementById('setupWizardContent').style.display = 'none';
        document.getElementById('wizardToggleIcon').style.transform = 'rotate(-90deg)';
    }
});
</script>
