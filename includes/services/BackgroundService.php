<?php
/**
 * Leadbusiness - Background Service
 * 
 * Verwaltet Branchen-Hintergrundbilder für Empfehlungsseiten
 */

class BackgroundService {
    
    private $db;
    private $basePath;
    private $baseUrl;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->basePath = __DIR__ . '/../../public/assets/backgrounds/';
        $this->baseUrl = '/assets/backgrounds/';
    }
    
    /**
     * Alle Hintergrundbilder nach Branche gruppiert abrufen
     */
    public function getAllGroupedByIndustry() {
        $backgrounds = $this->db->fetchAll(
            "SELECT * FROM background_images 
             WHERE is_active = 1 
             ORDER BY industry, sort_order, id"
        );
        
        $grouped = [];
        foreach ($backgrounds as $bg) {
            $bg['url'] = $this->getImageUrl($bg);
            $grouped[$bg['industry']][] = $bg;
        }
        
        return $grouped;
    }
    
    /**
     * Hintergrundbilder für eine Branche abrufen
     */
    public function getByIndustry($industry) {
        $backgrounds = $this->db->fetchAll(
            "SELECT * FROM background_images 
             WHERE industry = ? AND is_active = 1 
             ORDER BY sort_order, id",
            [$industry]
        );
        
        foreach ($backgrounds as &$bg) {
            $bg['url'] = $this->getImageUrl($bg);
        }
        
        return $backgrounds;
    }
    
    /**
     * Standard-Hintergrundbild für eine Branche abrufen
     */
    public function getDefaultForIndustry($industry) {
        $background = $this->db->fetch(
            "SELECT * FROM background_images 
             WHERE industry = ? AND is_default = 1 AND is_active = 1 
             LIMIT 1",
            [$industry]
        );
        
        if (!$background) {
            // Erstes Bild der Branche als Fallback
            $background = $this->db->fetch(
                "SELECT * FROM background_images 
                 WHERE industry = ? AND is_active = 1 
                 ORDER BY sort_order, id 
                 LIMIT 1",
                [$industry]
            );
        }
        
        if (!$background) {
            // Allgemeines Bild als Fallback
            $background = $this->db->fetch(
                "SELECT * FROM background_images 
                 WHERE industry = 'allgemein' AND is_default = 1 AND is_active = 1 
                 LIMIT 1"
            );
        }
        
        if ($background) {
            $background['url'] = $this->getImageUrl($background);
        }
        
        return $background;
    }
    
    /**
     * Hintergrundbild per ID abrufen
     */
    public function getById($id) {
        $background = $this->db->fetch(
            "SELECT * FROM background_images WHERE id = ?",
            [$id]
        );
        
        if ($background) {
            $background['url'] = $this->getImageUrl($background);
        }
        
        return $background;
    }
    
    /**
     * Hintergrundbild-URL für einen Kunden ermitteln
     */
    public function getCustomerBackgroundUrl($customer) {
        // Custom Background hat Priorität (Professional)
        if (!empty($customer['custom_background_url'])) {
            return $customer['custom_background_url'];
        }
        
        // Gewähltes Hintergrundbild
        if (!empty($customer['background_image_id'])) {
            $background = $this->getById($customer['background_image_id']);
            if ($background) {
                return $background['url'];
            }
        }
        
        // Standard-Hintergrundbild der Branche
        $background = $this->getDefaultForIndustry($customer['industry']);
        if ($background) {
            return $background['url'];
        }
        
        // Absoluter Fallback
        return '/assets/backgrounds/allgemein/bg-1.jpg';
    }
    
    /**
     * Bild-URL generieren
     */
    private function getImageUrl($background) {
        return $this->baseUrl . $background['industry'] . '/' . $background['filename'];
    }
    
    /**
     * Neues Hintergrundbild hinzufügen (Admin)
     */
    public function add($industry, $filename, $displayName, $isDefault = false) {
        // Wenn als Default, andere Default-Markierung entfernen
        if ($isDefault) {
            $this->db->query(
                "UPDATE background_images SET is_default = 0 WHERE industry = ?",
                [$industry]
            );
        }
        
        // Nächste Sort-Order ermitteln
        $maxOrder = $this->db->fetch(
            "SELECT MAX(sort_order) as max_order FROM background_images WHERE industry = ?",
            [$industry]
        );
        $sortOrder = ($maxOrder['max_order'] ?? 0) + 1;
        
        return $this->db->insert('background_images', [
            'industry' => $industry,
            'filename' => $filename,
            'display_name' => $displayName,
            'sort_order' => $sortOrder,
            'is_default' => $isDefault ? 1 : 0,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Hintergrundbild aktualisieren (Admin)
     */
    public function update($id, $data) {
        // Wenn als Default, andere Default-Markierung entfernen
        if (!empty($data['is_default'])) {
            $background = $this->getById($id);
            if ($background) {
                $this->db->query(
                    "UPDATE background_images SET is_default = 0 WHERE industry = ? AND id != ?",
                    [$background['industry'], $id]
                );
            }
        }
        
        return $this->db->update('background_images', $data, 'id = ?', [$id]);
    }
    
    /**
     * Hintergrundbild deaktivieren (Admin)
     */
    public function deactivate($id) {
        return $this->db->update('background_images', ['is_active' => 0], 'id = ?', [$id]);
    }
    
    /**
     * Custom Background hochladen
     */
    public function uploadCustomBackground($file, $subdomain) {
        $uploadDir = __DIR__ . '/../../public/uploads/backgrounds/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Validierung
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            throw new Exception('Ungültiges Bildformat. Erlaubt: JPG, PNG, WebP');
        }
        
        if ($file['size'] > 5 * 1024 * 1024) {
            throw new Exception('Bild zu groß. Maximal 5MB erlaubt.');
        }
        
        // Bildgröße prüfen (min. 1920x1080 empfohlen)
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo[0] < 1200 || $imageInfo[1] < 600) {
            throw new Exception('Bild zu klein. Empfohlen: mindestens 1920x1080 Pixel.');
        }
        
        // Dateiname generieren
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $filename = $subdomain . '-bg-' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Optional: Bild optimieren/skalieren
        $this->optimizeImage($file['tmp_name'], $filepath, $mimeType);
        
        return '/uploads/backgrounds/' . $filename;
    }
    
    /**
     * Bild optimieren (skalieren und komprimieren)
     */
    private function optimizeImage($source, $destination, $mimeType) {
        $maxWidth = 1920;
        $maxHeight = 1080;
        $quality = 85;
        
        list($width, $height) = getimagesize($source);
        
        // Skalierung berechnen
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);
        
        // Quellbild laden
        switch ($mimeType) {
            case 'image/jpeg':
                $srcImage = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $srcImage = imagecreatefrompng($source);
                break;
            case 'image/webp':
                $srcImage = imagecreatefromwebp($source);
                break;
            default:
                // Datei einfach kopieren
                copy($source, $destination);
                return;
        }
        
        // Neues Bild erstellen
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Transparenz erhalten (für PNG)
        if ($mimeType === 'image/png') {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
        }
        
        // Skalieren
        imagecopyresampled(
            $dstImage, $srcImage,
            0, 0, 0, 0,
            $newWidth, $newHeight, $width, $height
        );
        
        // Speichern
        switch ($mimeType) {
            case 'image/jpeg':
                imagejpeg($dstImage, $destination, $quality);
                break;
            case 'image/png':
                imagepng($dstImage, $destination, 9);
                break;
            case 'image/webp':
                imagewebp($dstImage, $destination, $quality);
                break;
        }
        
        imagedestroy($srcImage);
        imagedestroy($dstImage);
    }
    
    /**
     * Statistiken für Admin
     */
    public function getStats() {
        return $this->db->fetch(
            "SELECT 
                COUNT(*) as total,
                COUNT(DISTINCT industry) as industries,
                SUM(is_active) as active,
                SUM(is_default) as defaults
             FROM background_images"
        );
    }
    
    /**
     * Branchen mit Bild-Anzahl
     */
    public function getIndustriesWithCount() {
        return $this->db->fetchAll(
            "SELECT 
                industry,
                COUNT(*) as count,
                SUM(is_active) as active
             FROM background_images
             GROUP BY industry
             ORDER BY industry"
        );
    }
}
