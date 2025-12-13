<?php
/**
 * Leadbusiness - Plan Upgrade
 * Zeigt Plan-Vergleich und Upgrade-Optionen
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$currentPlan = $customer['plan'] ?? 'starter';
$planLimits = getPlanLimits();

// Digistore24 Produkt-IDs (diese müssen in Digistore24 angelegt werden)
$digistoreProducts = [
    'professional' => 'PRODUCT_ID_PROFESSIONAL', // Hier echte ID eintragen
    'enterprise' => 'PRODUCT_ID_ENTERPRISE',     // Hier echte ID eintragen
];

$pageTitle = 'Plan Upgrade';

include __DIR__ . '/../../includes/dashboard-header.php';
?>

<!-- Header -->
<div class="text-center mb-8">
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">
        Wählen Sie den passenden Plan
    </h1>
    <p class="text-slate-600 dark:text-slate-400">
        Ihr aktueller Plan: 
        <span class="font-semibold text-primary-600 dark:text-primary-400"><?= ucfirst($currentPlan) ?></span>
    </p>
</div>

<!-- Plan Cards -->
<div class="grid md:grid-cols-3 gap-6 mb-8">
    
    <!-- Starter Plan -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 <?= $currentPlan === 'starter' ? 'border-primary-500 ring-2 ring-primary-500/20' : 'border-slate-200 dark:border-slate-700' ?> overflow-hidden">
        <?php if ($currentPlan === 'starter'): ?>
        <div class="bg-primary-500 text-white text-center text-sm font-medium py-1.5">
            <i class="fas fa-check-circle mr-1"></i> Ihr aktueller Plan
        </div>
        <?php endif; ?>
        
        <div class="p-6">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Starter</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">Für den Einstieg</p>
            
            <div class="mb-6">
                <span class="text-4xl font-bold text-slate-800 dark:text-white">49€</span>
                <span class="text-slate-500 dark:text-slate-400">/Monat</span>
            </div>
            
            <ul class="space-y-3 text-sm text-slate-600 dark:text-slate-300 mb-6">
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Bis zu <strong>200</strong> Empfehler
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    <strong>3</strong> Belohnungsstufen
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    1 Kampagne
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    E-Mail-Versand inklusive
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Share-Grafiken & QR-Code
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Basis-Gamification
                </li>
                <li class="flex items-center gap-2 text-slate-400 dark:text-slate-500">
                    <i class="fas fa-times w-4"></i>
                    Lead-Export
                </li>
                <li class="flex items-center gap-2 text-slate-400 dark:text-slate-500">
                    <i class="fas fa-times w-4"></i>
                    Eigene Domain
                </li>
            </ul>
            
            <?php if ($currentPlan === 'starter'): ?>
            <button disabled class="w-full py-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
                Aktueller Plan
            </button>
            <?php else: ?>
            <button disabled class="w-full py-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
                Downgrade nicht verfügbar
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Professional Plan -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 <?= $currentPlan === 'professional' ? 'border-primary-500 ring-2 ring-primary-500/20' : 'border-amber-400' ?> overflow-hidden relative">
        <?php if ($currentPlan !== 'professional'): ?>
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 text-white text-center text-sm font-medium py-1.5">
            <i class="fas fa-star mr-1"></i> Beliebteste Wahl
        </div>
        <?php else: ?>
        <div class="bg-primary-500 text-white text-center text-sm font-medium py-1.5">
            <i class="fas fa-check-circle mr-1"></i> Ihr aktueller Plan
        </div>
        <?php endif; ?>
        
        <div class="p-6">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Professional</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">Für wachsende Unternehmen</p>
            
            <div class="mb-6">
                <span class="text-4xl font-bold text-slate-800 dark:text-white">99€</span>
                <span class="text-slate-500 dark:text-slate-400">/Monat</span>
            </div>
            
            <ul class="space-y-3 text-sm text-slate-600 dark:text-slate-300 mb-6">
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Bis zu <strong>5.000</strong> Empfehler
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    <strong>5</strong> Belohnungsstufen
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Unbegrenzte Kampagnen
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Alle Templates
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    <strong>Eigenes Hintergrundbild</strong>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Lead-Export
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Webhooks & API
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Branding entfernen
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Erweiterte Gamification
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Prioritäts-Support
                </li>
            </ul>
            
            <?php if ($currentPlan === 'professional'): ?>
            <button disabled class="w-full py-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
                Aktueller Plan
            </button>
            <?php elseif ($currentPlan === 'starter'): ?>
            <a href="https://www.digistore24.com/product/<?= $digistoreProducts['professional'] ?>?custom=upgrade_<?= $customer['id'] ?>" 
               target="_blank"
               class="block w-full py-3 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white text-center font-semibold rounded-lg transition-all shadow-lg shadow-amber-500/30">
                <i class="fas fa-arrow-up mr-2"></i>Jetzt upgraden
            </a>
            <?php else: ?>
            <button disabled class="w-full py-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
                Downgrade nicht verfügbar
            </button>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Enterprise Plan -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 <?= $currentPlan === 'enterprise' ? 'border-primary-500 ring-2 ring-primary-500/20' : 'border-slate-200 dark:border-slate-700' ?> overflow-hidden">
        <?php if ($currentPlan === 'enterprise'): ?>
        <div class="bg-primary-500 text-white text-center text-sm font-medium py-1.5">
            <i class="fas fa-check-circle mr-1"></i> Ihr aktueller Plan
        </div>
        <?php else: ?>
        <div class="bg-gradient-to-r from-purple-500 to-indigo-500 text-white text-center text-sm font-medium py-1.5">
            <i class="fas fa-crown mr-1"></i> Premium
        </div>
        <?php endif; ?>
        
        <div class="p-6">
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-1">Enterprise</h3>
            <p class="text-slate-500 dark:text-slate-400 text-sm mb-4">Für Großunternehmen</p>
            
            <div class="mb-6">
                <span class="text-2xl font-bold text-slate-800 dark:text-white">Auf Anfrage</span>
            </div>
            
            <ul class="space-y-3 text-sm text-slate-600 dark:text-slate-300 mb-6">
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    <strong>Unbegrenzte</strong> Empfehler
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    <strong>10</strong> Belohnungsstufen
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Unbegrenzte Kampagnen
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Alles aus Professional
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    <strong>Dedizierter Support</strong>
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Custom Integrationen
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    SLA-Garantie
                </li>
                <li class="flex items-center gap-2">
                    <i class="fas fa-check text-green-500 w-4"></i>
                    Onboarding-Call
                </li>
            </ul>
            
            <?php if ($currentPlan === 'enterprise'): ?>
            <button disabled class="w-full py-3 bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 rounded-lg cursor-not-allowed">
                Aktueller Plan
            </button>
            <?php else: ?>
            <a href="mailto:support@empfehlungen.cloud?subject=Enterprise-Plan%20Anfrage&body=Ich%20interessiere%20mich%20f%C3%BCr%20den%20Enterprise-Plan.%0A%0AUnternehmen:%20<?= urlencode($customer['company_name']) ?>%0AKunden-ID:%20<?= $customer['id'] ?>" 
               class="block w-full py-3 bg-gradient-to-r from-purple-500 to-indigo-500 hover:from-purple-600 hover:to-indigo-600 text-white text-center font-semibold rounded-lg transition-all shadow-lg shadow-purple-500/30">
                <i class="fas fa-envelope mr-2"></i>Kontakt aufnehmen
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Feature Comparison Table -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden mb-8">
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white">
            <i class="fas fa-table text-primary-500 mr-2"></i>
            Detaillierter Feature-Vergleich
        </h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-200 dark:border-slate-700">
                    <th class="text-left px-6 py-4 text-slate-600 dark:text-slate-400 font-medium">Feature</th>
                    <th class="text-center px-6 py-4 text-slate-600 dark:text-slate-400 font-medium <?= $currentPlan === 'starter' ? 'bg-primary-50 dark:bg-primary-900/20' : '' ?>">Starter</th>
                    <th class="text-center px-6 py-4 text-slate-600 dark:text-slate-400 font-medium <?= $currentPlan === 'professional' ? 'bg-primary-50 dark:bg-primary-900/20' : '' ?>">Professional</th>
                    <th class="text-center px-6 py-4 text-slate-600 dark:text-slate-400 font-medium <?= $currentPlan === 'enterprise' ? 'bg-primary-50 dark:bg-primary-900/20' : '' ?>">Enterprise</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Maximale Empfehler</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">200</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">5.000</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">Unbegrenzt</td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Belohnungsstufen</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">3</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">5</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">10</td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Kampagnen</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">1</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">Unbegrenzt</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">Unbegrenzt</td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">E-Mail-Versand</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Eigenes Hintergrundbild</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-times text-slate-300 dark:text-slate-600"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Lead-Export</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-times text-slate-300 dark:text-slate-600"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Webhooks & API</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-times text-slate-300 dark:text-slate-600"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Branding entfernen</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-times text-slate-300 dark:text-slate-600"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Broadcast E-Mails</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-times text-slate-300 dark:text-slate-600"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>"><i class="fas fa-check text-green-500"></i></td>
                </tr>
                <tr class="border-b border-slate-100 dark:border-slate-700/50">
                    <td class="px-6 py-3 text-slate-700 dark:text-slate-300">Support</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'starter' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">E-Mail</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'professional' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">Priorität</td>
                    <td class="px-6 py-3 text-center <?= $currentPlan === 'enterprise' ? 'bg-primary-50/50 dark:bg-primary-900/10' : '' ?>">Dediziert</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- FAQ -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
    <div class="px-6 py-4 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-700">
        <h3 class="font-semibold text-slate-800 dark:text-white">
            <i class="fas fa-question-circle text-primary-500 mr-2"></i>
            Häufige Fragen zum Upgrade
        </h3>
    </div>
    
    <div class="divide-y divide-slate-200 dark:divide-slate-700">
        <details class="group">
            <summary class="px-6 py-4 cursor-pointer flex items-center justify-between text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                <span>Wie funktioniert das Upgrade?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-6 pb-4 text-sm text-slate-600 dark:text-slate-400">
                Nach dem Kauf wird Ihr Account automatisch auf den neuen Plan umgestellt. 
                Alle zusätzlichen Features sind sofort verfügbar.
            </div>
        </details>
        
        <details class="group">
            <summary class="px-6 py-4 cursor-pointer flex items-center justify-between text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                <span>Werden meine bestehenden Daten übernommen?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-6 pb-4 text-sm text-slate-600 dark:text-slate-400">
                Ja, alle Ihre Empfehler, Kampagnen und Einstellungen bleiben vollständig erhalten.
            </div>
        </details>
        
        <details class="group">
            <summary class="px-6 py-4 cursor-pointer flex items-center justify-between text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                <span>Kann ich auch wieder downgraden?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-6 pb-4 text-sm text-slate-600 dark:text-slate-400">
                Ein Downgrade ist nicht automatisch möglich. Bitte kontaktieren Sie unseren Support, 
                wenn Sie Ihren Plan ändern möchten.
            </div>
        </details>
        
        <details class="group">
            <summary class="px-6 py-4 cursor-pointer flex items-center justify-between text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50">
                <span>Was passiert, wenn ich das Limit überschreite?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="px-6 pb-4 text-sm text-slate-600 dark:text-slate-400">
                Wir informieren Sie rechtzeitig, wenn Sie sich Ihren Limits nähern. 
                Bestehende Empfehler bleiben aktiv, aber neue Anmeldungen werden pausiert, 
                bis Sie upgraden oder Kapazitäten freigeben.
            </div>
        </details>
    </div>
</div>

<!-- Contact -->
<div class="mt-8 text-center text-sm text-slate-500 dark:text-slate-400">
    <p>
        Noch Fragen? Kontaktieren Sie uns: 
        <a href="mailto:support@empfehlungen.cloud" class="text-primary-600 hover:text-primary-700 dark:text-primary-400">
            support@empfehlungen.cloud
        </a>
    </p>
</div>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
