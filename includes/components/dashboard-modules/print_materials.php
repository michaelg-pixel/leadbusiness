<?php
/**
 * Dashboard Module: Print Materials
 * Verfügbar für: Professional, Enterprise
 * Druckfertige Vorlagen: Poster, Flyer, Tischaufsteller, Visitenkarten
 */

if (!isset($customer) || !isset($campaign)) {
    return;
}

// Plan-Check
$isAvailable = in_array($customer['plan'], ['professional', 'enterprise']);

$referralUrl = "https://{$customer['subdomain']}.empfehlungen.cloud";
$companyName = htmlspecialchars($customer['company_name']);
$primaryColor = $customer['primary_color'] ?? '#667eea';
$logoUrl = $customer['logo_url'] ?? null;

// Verfügbare Vorlagen
$templates = [
    'poster_a4' => [
        'name' => 'Poster A4',
        'description' => 'Für Schaufenster, Empfang & Wände',
        'icon' => 'fas fa-file-image',
        'size' => '210 × 297 mm',
        'preview' => '/assets/images/templates/poster-preview.png',
        'popular' => true
    ],
    'poster_a3' => [
        'name' => 'Poster A3',
        'description' => 'Großformat für maximale Sichtbarkeit',
        'icon' => 'fas fa-expand',
        'size' => '297 × 420 mm',
        'preview' => '/assets/images/templates/poster-a3-preview.png',
        'popular' => false
    ],
    'flyer_a5' => [
        'name' => 'Flyer A5',
        'description' => 'Zum Verteilen & Mitnehmen',
        'icon' => 'fas fa-file-alt',
        'size' => '148 × 210 mm',
        'preview' => '/assets/images/templates/flyer-preview.png',
        'popular' => true
    ],
    'flyer_a6' => [
        'name' => 'Handzettel A6',
        'description' => 'Kompakt für die Theke',
        'icon' => 'fas fa-sticky-note',
        'size' => '105 × 148 mm',
        'preview' => '/assets/images/templates/flyer-a6-preview.png',
        'popular' => false
    ],
    'table_tent' => [
        'name' => 'Tischaufsteller',
        'description' => 'Für Tresen, Empfang & Tische',
        'icon' => 'fas fa-campground',
        'size' => '100 × 210 mm',
        'preview' => '/assets/images/templates/table-tent-preview.png',
        'popular' => true
    ],
    'business_card' => [
        'name' => 'Visitenkarten-Rückseite',
        'description' => 'QR-Code für Ihre Visitenkarten',
        'icon' => 'fas fa-id-card',
        'size' => '85 × 55 mm',
        'preview' => '/assets/images/templates/business-card-preview.png',
        'popular' => false
    ],
    'sticker_round' => [
        'name' => 'Aufkleber rund',
        'description' => 'Für Verpackungen & Produkte',
        'icon' => 'fas fa-circle',
        'size' => 'Ø 50 mm',
        'preview' => '/assets/images/templates/sticker-preview.png',
        'popular' => false
    ],
    'invoice_insert' => [
        'name' => 'Rechnungsbeileger',
        'description' => 'Beilage für Rechnungen & Lieferungen',
        'icon' => 'fas fa-receipt',
        'size' => 'DIN lang',
        'preview' => '/assets/images/templates/invoice-insert-preview.png',
        'popular' => true
    ]
];

// Beliebte Vorlagen zuerst
$popularTemplates = array_filter($templates, fn($t) => $t['popular']);
$otherTemplates = array_filter($templates, fn($t) => !$t['popular']);
?>

<div class="dashboard-module module-print-materials p-6" data-module="print_materials">
    <div class="module-header flex justify-between items-center mb-6">
        <div>
            <h3 class="module-title text-lg font-bold text-gray-800 dark:text-white">
                <i class="fas fa-print text-primary-500 mr-2"></i>
                Druckmaterialien
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Professionelle Vorlagen für Ihr Empfehlungsprogramm
            </p>
        </div>
        <span class="badge bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-1 rounded-full text-xs font-medium">
            <i class="fas fa-star mr-1"></i> Professional
        </span>
    </div>
    
    <?php if (!$isAvailable): ?>
    <!-- Upgrade-Hinweis für Starter -->
    <div class="upgrade-notice bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 text-center">
        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-lock text-blue-500 text-2xl"></i>
        </div>
        <h4 class="font-bold text-gray-800 dark:text-white mb-2">
            Druckvorlagen freischalten
        </h4>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
            Mit dem Professional-Plan erhalten Sie Zugang zu <?= count($templates) ?> professionellen Druckvorlagen.
        </p>
        <a href="/dashboard/upgrade.php" class="btn btn-primary">
            <i class="fas fa-arrow-up mr-2"></i>
            Jetzt upgraden
        </a>
    </div>
    
    <?php else: ?>
    
    <div class="module-content">
        <!-- Logo-Hinweis wenn kein Logo vorhanden -->
        <?php if (!$logoUrl): ?>
        <div class="logo-notice mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl flex items-start gap-3">
            <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5"></i>
            <div>
                <p class="text-sm text-amber-800 dark:text-amber-200 font-medium">
                    Kein Logo hochgeladen
                </p>
                <p class="text-sm text-amber-600 dark:text-amber-300 mt-1">
                    Für professionelle Druckmaterialien empfehlen wir, Ihr Logo hochzuladen.
                </p>
                <a href="/dashboard/design.php" class="text-sm text-amber-700 dark:text-amber-200 font-medium hover:underline mt-2 inline-block">
                    Logo hochladen →
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Beliebte Vorlagen -->
        <div class="popular-templates mb-6">
            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">
                <i class="fas fa-fire text-orange-500 mr-2"></i>
                Beliebt
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($popularTemplates as $key => $template): ?>
                <div class="template-card group bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer"
                     onclick="downloadTemplate('<?= $key ?>')">
                    <div class="flex items-start gap-4">
                        <div class="template-icon w-14 h-14 bg-white dark:bg-gray-600 rounded-lg shadow-sm flex items-center justify-center flex-shrink-0 group-hover:shadow-md transition">
                            <i class="<?= $template['icon'] ?> text-gray-400 dark:text-gray-300 text-xl"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-semibold text-gray-800 dark:text-white">
                                <?= $template['name'] ?>
                            </h5>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                                <?= $template['description'] ?>
                            </p>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                <?= $template['size'] ?>
                            </p>
                        </div>
                        <div class="download-icon opacity-0 group-hover:opacity-100 transition">
                            <i class="fas fa-download text-primary-500"></i>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Weitere Vorlagen -->
        <div class="other-templates">
            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-3">
                Weitere Vorlagen
            </h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <?php foreach ($otherTemplates as $key => $template): ?>
                <div class="template-card-small group bg-gray-50 dark:bg-gray-700/50 rounded-lg p-3 hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer text-center"
                     onclick="downloadTemplate('<?= $key ?>')">
                    <div class="w-10 h-10 bg-white dark:bg-gray-600 rounded-lg shadow-sm flex items-center justify-center mx-auto mb-2 group-hover:shadow-md transition">
                        <i class="<?= $template['icon'] ?> text-gray-400 dark:text-gray-300"></i>
                    </div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200 truncate">
                        <?= $template['name'] ?>
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        <?= $template['size'] ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Format-Auswahl Modal Trigger -->
        <div class="format-options mt-6 p-4 bg-gray-100 dark:bg-gray-700 rounded-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        <i class="fas fa-cog mr-2"></i>
                        Download-Format
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Standard: PDF (druckfertig)
                    </p>
                </div>
                <select id="downloadFormat" class="text-sm bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg px-3 py-1.5">
                    <option value="pdf">PDF (empfohlen)</option>
                    <option value="png">PNG (300 dpi)</option>
                    <option value="jpg">JPG (300 dpi)</option>
                </select>
            </div>
        </div>
        
        <!-- Drucktipps -->
        <div class="print-tips mt-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl">
            <h4 class="font-semibold text-green-800 dark:text-green-200 mb-2">
                <i class="fas fa-lightbulb text-green-500 mr-2"></i>
                Drucktipps
            </h4>
            <ul class="text-sm text-green-700 dark:text-green-300 space-y-1">
                <li><i class="fas fa-check text-green-500 mr-2"></i>Verwenden Sie Papier ab 120 g/m² für beste Qualität</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Laminieren Sie Aufsteller für längere Haltbarkeit</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Platzieren Sie Materialien an gut sichtbaren Stellen</li>
            </ul>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Download Modal -->
<div id="downloadModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-spinner fa-spin text-primary-500 text-2xl" id="downloadSpinner"></i>
                <i class="fas fa-check text-primary-500 text-2xl hidden" id="downloadSuccess"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-2" id="downloadTitle">
                Vorlage wird erstellt...
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400" id="downloadMessage">
                Bitte warten Sie einen Moment.
            </p>
        </div>
        <div class="mt-6 hidden" id="downloadActions">
            <button onclick="closeDownloadModal()" class="btn btn-outline w-full">
                Schließen
            </button>
        </div>
    </div>
</div>

<script>
const customerId = <?= (int)$customer['id'] ?>;

function downloadTemplate(templateType) {
    const format = document.getElementById('downloadFormat').value;
    
    // Modal anzeigen
    showDownloadModal();
    
    // Download starten
    fetch(`/api/print-template.php?customer_id=${customerId}&template=${templateType}&format=${format}`)
        .then(response => {
            if (!response.ok) throw new Error('Download fehlgeschlagen');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${templateType}-<?= e($customer['subdomain']) ?>.${format}`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showDownloadSuccess(templateType);
            trackDownload(templateType, format);
        })
        .catch(error => {
            console.error('Download error:', error);
            showDownloadError();
        });
}

function showDownloadModal() {
    const modal = document.getElementById('downloadModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    
    document.getElementById('downloadSpinner').classList.remove('hidden');
    document.getElementById('downloadSuccess').classList.add('hidden');
    document.getElementById('downloadActions').classList.add('hidden');
    document.getElementById('downloadTitle').textContent = 'Vorlage wird erstellt...';
    document.getElementById('downloadMessage').textContent = 'Bitte warten Sie einen Moment.';
}

function showDownloadSuccess(templateType) {
    document.getElementById('downloadSpinner').classList.add('hidden');
    document.getElementById('downloadSuccess').classList.remove('hidden');
    document.getElementById('downloadActions').classList.remove('hidden');
    document.getElementById('downloadTitle').textContent = 'Download gestartet!';
    document.getElementById('downloadMessage').textContent = 'Ihre Vorlage wurde erfolgreich erstellt.';
    
    // Auto-close nach 2 Sekunden
    setTimeout(closeDownloadModal, 2000);
}

function showDownloadError() {
    document.getElementById('downloadSpinner').classList.add('hidden');
    document.getElementById('downloadActions').classList.remove('hidden');
    document.getElementById('downloadTitle').textContent = 'Download fehlgeschlagen';
    document.getElementById('downloadMessage').textContent = 'Bitte versuchen Sie es erneut.';
}

function closeDownloadModal() {
    const modal = document.getElementById('downloadModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function trackDownload(template, format) {
    fetch('/api/track-event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            customer_id: customerId,
            action: 'print_template_download',
            template: template,
            format: format
        })
    }).catch(() => {});
}

// ESC zum Schließen
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeDownloadModal();
});
</script>

<style>
.template-card:hover .download-icon {
    transform: translateX(0);
}

.template-card-small:hover {
    transform: translateY(-2px);
}
</style>
