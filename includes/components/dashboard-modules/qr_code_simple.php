<?php
/**
 * Dashboard Module: QR-Code (Basis)
 * Verfügbar für: Alle Tarife
 * Optimiert für: Offline-Businesses
 */

if (!isset($customer) || !isset($campaign)) {
    return;
}

$referralUrl = "https://{$customer['subdomain']}.empfohlen.de";
$qrCodeUrl = "/api/qr-code.php?url=" . urlencode($referralUrl) . "&size=300";
?>

<div class="dashboard-module module-qr-code-simple" data-module="qr_code_simple">
    <div class="module-header">
        <h3 class="module-title">
            <i class="fas fa-qrcode"></i>
            Ihr QR-Code
        </h3>
    </div>
    
    <div class="module-content">
        <div class="qr-code-container text-center">
            <div class="qr-code-wrapper bg-white p-4 rounded-lg inline-block shadow-sm">
                <img src="<?= htmlspecialchars($qrCodeUrl) ?>" 
                     alt="QR-Code für <?= htmlspecialchars($customer['company_name']) ?>"
                     class="qr-code-image"
                     id="qrCodeImage"
                     style="width: 200px; height: 200px;">
            </div>
            
            <p class="text-gray-600 dark:text-gray-400 mt-3 text-sm">
                Kunden scannen → Empfehlungsseite öffnet sich
            </p>
        </div>
        
        <div class="qr-actions mt-4 flex flex-col sm:flex-row gap-2 justify-center">
            <button onclick="downloadQRCode('png')" 
                    class="btn btn-primary">
                <i class="fas fa-download mr-2"></i>
                PNG herunterladen
            </button>
            
            <button onclick="downloadQRCode('svg')" 
                    class="btn btn-outline">
                <i class="fas fa-file-code mr-2"></i>
                SVG (Druck)
            </button>
        </div>
        
        <div class="qr-tip mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm">
            <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
            <strong>Tipp:</strong> Drucken Sie den QR-Code auf Visitenkarten, 
            Rechnungen oder als Aufsteller am Tresen.
        </div>
    </div>
</div>

<script>
function downloadQRCode(format) {
    const url = '<?= htmlspecialchars($referralUrl) ?>';
    const downloadUrl = `/api/qr-code.php?url=${encodeURIComponent(url)}&size=1000&format=${format}&download=1`;
    
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = `qr-code-<?= htmlspecialchars($customer['subdomain']) ?>.${format}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    // Analytics
    if (typeof trackEvent === 'function') {
        trackEvent('qr_download', { format: format });
    }
}
</script>
