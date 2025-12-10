<?php
/**
 * Leadbusiness - AVV PDF Download Handler
 * 
 * Liefert das AVV-PDF aus und bietet einen Fallback,
 * falls das PDF noch nicht hochgeladen wurde.
 */

$pdfPath = __DIR__ . '/downloads/avv-leadbusiness.pdf';

// Prüfen ob PDF existiert
if (file_exists($pdfPath)) {
    // PDF ausliefern
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="AVV-Leadbusiness-empfehlungen-cloud.pdf"');
    header('Content-Length: ' . filesize($pdfPath));
    header('Cache-Control: private, max-age=0, must-revalidate');
    header('Pragma: public');
    
    readfile($pdfPath);
    exit;
}

// Fallback: PDF noch nicht vorhanden - zur AVV-Seite weiterleiten
header('Location: /avv?pdf=pending');
exit;
