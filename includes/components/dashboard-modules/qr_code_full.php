<?php
/**
 * Dashboard Module: QR-Code (Erweitert)
 * Verfügbar für: Professional, Enterprise
 * Features: QR-Code + Print-Vorlagen (Poster, Flyer, Tischaufsteller)
 */

if (!isset($customer) || !isset($campaign)) {
    return;
}

$referralUrl = "https://{$customer['subdomain']}.empfohlen.de";
$qrCodeUrl = "/api/qr-code.php?url=" . urlencode($referralUrl) . "&size=300";
$companyName = htmlspecialchars($customer['company_name']);
$primaryColor = $customer['primary_color'] ?? '#667eea';
?>

<div class="dashboard-module module-qr-code-full" data-module="qr_code_full">
    <div class="module-header flex justify-between items-center">
        <h3 class="module-title">
            <i class="fas fa-qrcode"></i>
            QR-Code & Druckmaterialien
        </h3>
        <span class="badge badge-pro">
            <i class="fas fa-star mr-1"></i> Professional
        </span>
    </div>
    
    <div class="module-content">
        <!-- QR-Code Preview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Linke Seite: QR-Code -->
            <div class="qr-preview-section">
                <div class="qr-code-wrapper bg-white p-6 rounded-lg shadow-sm text-center">
                    <img src="<?= htmlspecialchars($qrCodeUrl) ?>" 
                         alt="QR-Code für <?= $companyName ?>"
                         class="qr-code-image mx-auto"
                         id="qrCodeImageFull"
                         style="width: 180px; height: 180px;">
                    
                    <p class="text-gray-800 font-semibold mt-3"><?= $companyName ?></p>
                    <p class="text-gray-500 text-sm">Empfehlen & Belohnung erhalten!</p>
                </div>
                
                <div class="qr-download-buttons mt-4 grid grid-cols-2 gap-2">
                    <button onclick="downloadQRCodeFull('png', 500)" class="btn btn-sm btn-outline">
                        <i class="fas fa-image mr-1"></i> PNG klein
                    </button>
                    <button onclick="downloadQRCodeFull('png', 1000)" class="btn btn-sm btn-outline">
                        <i class="fas fa-image mr-1"></i> PNG groß
                    </button>
                    <button onclick="downloadQRCodeFull('svg')" class="btn btn-sm btn-outline">
                        <i class="fas fa-file-code mr-1"></i> SVG
                    </button>
                    <button onclick="downloadQRCodeFull('pdf')" class="btn btn-sm btn-outline">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                </div>
            </div>
            
            <!-- Rechte Seite: Print-Vorlagen -->
            <div class="print-templates-section">
                <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3">
                    <i class="fas fa-print mr-2"></i>
                    Druckfertige Vorlagen
                </h4>
                
                <div class="templates-grid space-y-3">
                    <!-- Poster A4 -->
                    <div class="template-item flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer"
                         onclick="downloadPrintTemplate('poster_a4')">
                        <div class="flex items-center">
                            <div class="template-icon w-12 h-16 bg-white rounded shadow-sm flex items-center justify-center mr-3">
                                <i class="fas fa-file-image text-gray-400 text-xl"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Poster A4</p>
                                <p class="text-xs text-gray-500">Ideal für Schaufenster & Wände</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-gray-400"></i>
                    </div>
                    
                    <!-- Flyer A5 -->
                    <div class="template-item flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer"
                         onclick="downloadPrintTemplate('flyer_a5')">
                        <div class="flex items-center">
                            <div class="template-icon w-10 h-14 bg-white rounded shadow-sm flex items-center justify-center mr-3">
                                <i class="fas fa-file-alt text-gray-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Flyer A5</p>
                                <p class="text-xs text-gray-500">Zum Verteilen & Mitnehmen</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-gray-400"></i>
                    </div>
                    
                    <!-- Tischaufsteller -->
                    <div class="template-item flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer"
                         onclick="downloadPrintTemplate('table_tent')">
                        <div class="flex items-center">
                            <div class="template-icon w-12 h-10 bg-white rounded shadow-sm flex items-center justify-center mr-3">
                                <i class="fas fa-tent text-gray-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Tischaufsteller</p>
                                <p class="text-xs text-gray-500">Für Tresen, Empfang, Tische</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-gray-400"></i>
                    </div>
                    
                    <!-- Visitenkarte -->
                    <div class="template-item flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition cursor-pointer"
                         onclick="downloadPrintTemplate('business_card')">
                        <div class="flex items-center">
                            <div class="template-icon w-14 h-8 bg-white rounded shadow-sm flex items-center justify-center mr-3">
                                <i class="fas fa-id-card text-gray-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 dark:text-white">Visitenkarten-Zusatz</p>
                                <p class="text-xs text-gray-500">Rückseiten-Design</p>
                            </div>
                        </div>
                        <i class="fas fa-download text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Anleitung -->
        <div class="print-instructions mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg">
            <h4 class="font-semibold text-gray-800 dark:text-white mb-2">
                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                So nutzen Sie die Vorlagen
            </h4>
            <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-1">
                <li><i class="fas fa-check text-green-500 mr-2"></i>Laden Sie die gewünschte Vorlage herunter</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Drucken Sie auf hochwertigem Papier (min. 120g/m²)</li>
                <li><i class="fas fa-check text-green-500 mr-2"></i>Platzieren Sie die Materialien sichtbar für Kunden</li>
                <li><i class="fas fa-lightbulb text-yellow-500 mr-2"></i><strong>Tipp:</strong> Laminieren Sie Aufsteller für längere Haltbarkeit</li>
            </ul>
        </div>
    </div>
</div>

<script>
function downloadQRCodeFull(format, size = 1000) {
    const url = '<?= htmlspecialchars($referralUrl) ?>';
    const downloadUrl = `/api/qr-code.php?url=${encodeURIComponent(url)}&size=${size}&format=${format}&download=1`;
    
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = `qr-code-<?= htmlspecialchars($customer['subdomain']) ?>.${format}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    trackEvent('qr_download', { format: format, size: size });
}

function downloadPrintTemplate(templateType) {
    const customerId = <?= (int)$customer['id'] ?>;
    const downloadUrl = `/api/print-template.php?customer_id=${customerId}&template=${templateType}`;
    
    // Loading-State anzeigen
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird erstellt...';
    button.disabled = true;
    
    // Download starten
    fetch(downloadUrl)
        .then(response => {
            if (!response.ok) throw new Error('Download fehlgeschlagen');
            return response.blob();
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `${templateType}-<?= htmlspecialchars($customer['subdomain']) ?>.pdf`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            trackEvent('print_template_download', { template: templateType });
        })
        .catch(error => {
            console.error('Download error:', error);
            showNotification('Download fehlgeschlagen. Bitte versuchen Sie es erneut.', 'error');
        })
        .finally(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        });
}

function trackEvent(event, data = {}) {
    if (typeof gtag === 'function') {
        gtag('event', event, data);
    }
    // Eigenes Tracking
    fetch('/api/track-event.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ event, ...data, customer_id: <?= (int)$customer['id'] ?> })
    }).catch(() => {});
}
</script>

<style>
.template-item:hover .template-icon {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}
</style>
