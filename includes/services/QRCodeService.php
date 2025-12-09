<?php
/**
 * Leadbusiness - QR Code Service
 * 
 * Generiert QR-Codes für Empfehlungslinks
 * Nutzt die Google Charts API (keine externe Bibliothek nötig)
 */

class QRCodeService {
    
    private $db;
    private $cacheDir;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->cacheDir = __DIR__ . '/../../public/uploads/qrcodes/';
        
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    /**
     * QR-Code für einen Referral-Link generieren
     */
    public function generate($url, $size = 300, $options = []) {
        // Cache-Key basierend auf URL und Größe
        $cacheKey = md5($url . $size . json_encode($options));
        $cacheFile = $this->cacheDir . $cacheKey . '.png';
        
        // Cache prüfen
        if (file_exists($cacheFile) && filemtime($cacheFile) > strtotime('-7 days')) {
            return '/uploads/qrcodes/' . $cacheKey . '.png';
        }
        
        // QR-Code generieren
        $qrContent = $this->generateQRImage($url, $size, $options);
        
        if ($qrContent) {
            file_put_contents($cacheFile, $qrContent);
            return '/uploads/qrcodes/' . $cacheKey . '.png';
        }
        
        return null;
    }
    
    /**
     * QR-Code Bild generieren (via Google Charts API)
     */
    private function generateQRImage($url, $size, $options = []) {
        $apiUrl = 'https://chart.googleapis.com/chart';
        
        $params = [
            'cht' => 'qr',
            'chs' => $size . 'x' . $size,
            'chl' => $url,
            'choe' => 'UTF-8',
            'chld' => 'M|2' // Error correction level M, margin 2
        ];
        
        $fullUrl = $apiUrl . '?' . http_build_query($params);
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FOLLOWLOCATION => true
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            return $response;
        }
        
        return null;
    }
    
    /**
     * QR-Code für einen Lead generieren
     */
    public function generateForLead($leadId, $size = 300) {
        $lead = $this->db->fetch(
            "SELECT l.referral_code, c.subdomain 
             FROM leads l
             JOIN campaigns ca ON l.campaign_id = ca.id
             JOIN customers c ON ca.customer_id = c.id
             WHERE l.id = ?",
            [$leadId]
        );
        
        if (!$lead) {
            return null;
        }
        
        $url = 'https://' . $lead['subdomain'] . '.empfohlen.de/r/' . $lead['referral_code'];
        
        return $this->generate($url, $size);
    }
    
    /**
     * QR-Code für eine Kampagne generieren (Haupt-Link)
     */
    public function generateForCampaign($campaignId, $size = 300) {
        $campaign = $this->db->fetch(
            "SELECT ca.*, c.subdomain 
             FROM campaigns ca
             JOIN customers c ON ca.customer_id = c.id
             WHERE ca.id = ?",
            [$campaignId]
        );
        
        if (!$campaign) {
            return null;
        }
        
        $url = 'https://' . $campaign['subdomain'] . '.empfohlen.de';
        
        return $this->generate($url, $size);
    }
    
    /**
     * QR-Code als Data-URL (Base64)
     */
    public function generateAsDataUrl($url, $size = 200) {
        $qrContent = $this->generateQRImage($url, $size);
        
        if ($qrContent) {
            return 'data:image/png;base64,' . base64_encode($qrContent);
        }
        
        return null;
    }
    
    /**
     * QR-Code mit Logo in der Mitte (für Kunden-Dashboard)
     */
    public function generateWithLogo($url, $logoUrl, $size = 300) {
        // Standard QR-Code generieren
        $qrPath = $this->generate($url, $size);
        
        if (!$qrPath || empty($logoUrl)) {
            return $qrPath;
        }
        
        // Logo-Overlay hinzufügen
        $qrFullPath = __DIR__ . '/../../public' . $qrPath;
        
        if (!file_exists($qrFullPath)) {
            return $qrPath;
        }
        
        $qrImage = imagecreatefrompng($qrFullPath);
        
        // Logo laden
        $logoFullPath = __DIR__ . '/../../public' . $logoUrl;
        if (!file_exists($logoFullPath)) {
            return $qrPath;
        }
        
        $logoInfo = getimagesize($logoFullPath);
        switch ($logoInfo['mime']) {
            case 'image/png':
                $logoImage = imagecreatefrompng($logoFullPath);
                break;
            case 'image/jpeg':
                $logoImage = imagecreatefromjpeg($logoFullPath);
                break;
            default:
                return $qrPath;
        }
        
        // Logo skalieren (max 20% des QR-Codes)
        $logoSize = round($size * 0.2);
        $logoResized = imagecreatetruecolor($logoSize, $logoSize);
        
        // Transparenz erhalten
        imagealphablending($logoResized, false);
        imagesavealpha($logoResized, true);
        
        imagecopyresampled(
            $logoResized, $logoImage,
            0, 0, 0, 0,
            $logoSize, $logoSize,
            imagesx($logoImage), imagesy($logoImage)
        );
        
        // Weißer Hintergrund für Logo
        $logoX = ($size - $logoSize) / 2;
        $logoY = ($size - $logoSize) / 2;
        
        $white = imagecolorallocate($qrImage, 255, 255, 255);
        $padding = 5;
        imagefilledrectangle(
            $qrImage,
            $logoX - $padding, $logoY - $padding,
            $logoX + $logoSize + $padding, $logoY + $logoSize + $padding,
            $white
        );
        
        // Logo auf QR-Code platzieren
        imagecopy($qrImage, $logoResized, $logoX, $logoY, 0, 0, $logoSize, $logoSize);
        
        // Speichern
        $cacheKey = md5($url . $size . $logoUrl) . '-logo';
        $cacheFile = $this->cacheDir . $cacheKey . '.png';
        imagepng($qrImage, $cacheFile);
        
        imagedestroy($qrImage);
        imagedestroy($logoImage);
        imagedestroy($logoResized);
        
        return '/uploads/qrcodes/' . $cacheKey . '.png';
    }
    
    /**
     * Cache leeren
     */
    public function clearCache($olderThanDays = 30) {
        $files = glob($this->cacheDir . '*.png');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < strtotime("-{$olderThanDays} days")) {
                unlink($file);
                $deleted++;
            }
        }
        
        return $deleted;
    }
    
    /**
     * QR-Code Download für Lead
     */
    public function downloadForLead($leadId, $format = 'png') {
        $qrPath = $this->generateForLead($leadId, 500);
        
        if (!$qrPath) {
            return null;
        }
        
        $fullPath = __DIR__ . '/../../public' . $qrPath;
        
        return [
            'path' => $fullPath,
            'filename' => 'mein-empfehlungslink-qrcode.' . $format,
            'mime' => 'image/' . $format
        ];
    }
}
