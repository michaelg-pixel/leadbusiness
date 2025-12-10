<?php
/**
 * Leadbusiness - Vollständiges E-Mail Basis-Template
 * 
 * Enthält alle rechtlich erforderlichen Elemente:
 * - Kunden-Logo
 * - Vollständiges Impressum (Name, Adresse, Kontakt, USt-IdNr.)
 * - Datenschutz-Link
 * - One-Click Abmeldelink
 * - DSGVO-konform
 * 
 * Verfügbare Variablen:
 * - $subject: E-Mail-Betreff
 * - $preheader: Vorschautext (optional)
 * - $content: Hauptinhalt der E-Mail
 * - $company_name: Name des Unternehmens
 * - $company_logo: URL zum Logo (optional)
 * - $primary_color: Hauptfarbe (Hex, z.B. #667eea)
 * - $footer_address: Vollständige Adresse
 * - $footer_email: Kontakt-E-Mail
 * - $footer_phone: Telefonnummer (optional)
 * - $footer_tax_id: USt-IdNr. (optional)
 * - $unsubscribe_url: Abmelde-Link (erforderlich!)
 * - $privacy_url: Datenschutz-Link (erforderlich!)
 */

// Defaults
$primary_color = $primary_color ?? '#667eea';
$company_logo = $company_logo ?? null;
$preheader = $preheader ?? '';
$footer_phone = $footer_phone ?? null;
$footer_tax_id = $footer_tax_id ?? null;
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= htmlspecialchars($subject) ?></title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
    <style>
        /* Reset */
        body, table, td, p, a, li { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        body { margin: 0; padding: 0; width: 100% !important; height: 100% !important; }
        
        /* Typography */
        body, table, td, p { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Links */
        a { color: <?= htmlspecialchars($primary_color) ?>; }
        
        /* Mobile */
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; padding: 10px !important; }
            .content { padding: 25px 20px !important; }
            .button { width: 100% !important; }
            .mobile-hide { display: none !important; }
            .mobile-center { text-align: center !important; }
            .footer-text { font-size: 12px !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Preheader (versteckter Vorschautext für E-Mail-Clients) -->
    <?php if (!empty($preheader)): ?>
    <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all; font-size: 1px; color: #f4f6f9; line-height: 1px;">
        <?= htmlspecialchars($preheader) ?>
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>
    <?php endif; ?>
    
    <!-- Outer Container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f6f9;">
        <tr>
            <td style="padding: 30px 10px;">
                
                <!-- Main Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="container" style="margin: 0 auto; max-width: 600px;">
                    
                    <!-- ============================================ -->
                    <!-- HEADER mit Logo oder Firmenname              -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center; background: linear-gradient(135deg, <?= htmlspecialchars($primary_color) ?> 0%, #764ba2 100%); border-radius: 16px 16px 0 0;">
                            <?php if ($company_logo): ?>
                            <img src="<?= htmlspecialchars($company_logo) ?>" alt="<?= htmlspecialchars($company_name) ?>" style="max-height: 60px; max-width: 220px;">
                            <?php else: ?>
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; font-weight: 700; letter-spacing: -0.5px;">
                                <?= htmlspecialchars($company_name) ?>
                            </h1>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <!-- ============================================ -->
                    <!-- CONTENT (Hauptinhalt)                        -->
                    <!-- ============================================ -->
                    <tr>
                        <td class="content" style="padding: 40px; background-color: #ffffff; font-size: 16px; line-height: 1.7; color: #374151;">
                            <?= $content ?>
                        </td>
                    </tr>
                    
                    <!-- ============================================ -->
                    <!-- FOOTER mit Impressum, Datenschutz, Abmelden  -->
                    <!-- ============================================ -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f8fafc; border-radius: 0 0 16px 16px;">
                            
                            <!-- Trennlinie -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="border-top: 1px solid #e2e8f0; padding-top: 25px;"></td>
                                </tr>
                            </table>
                            
                            <!-- Impressum (rechtlich erforderlich) -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" class="footer-text" style="font-size: 13px; line-height: 1.6; color: #64748b;">
                                <tr>
                                    <td style="text-align: center;">
                                        <!-- Firmenname -->
                                        <p style="margin: 0 0 8px 0; font-weight: 600; color: #475569;">
                                            <?= htmlspecialchars($company_name) ?>
                                        </p>
                                        
                                        <!-- Adresse -->
                                        <p style="margin: 0 0 8px 0;">
                                            <?= htmlspecialchars($footer_address) ?>
                                        </p>
                                        
                                        <!-- Kontakt -->
                                        <?php if ($footer_email || $footer_phone): ?>
                                        <p style="margin: 0 0 8px 0;">
                                            <?php if ($footer_email): ?>
                                                E-Mail: <a href="mailto:<?= htmlspecialchars($footer_email) ?>" style="color: #64748b; text-decoration: none;"><?= htmlspecialchars($footer_email) ?></a>
                                            <?php endif; ?>
                                            <?php if ($footer_email && $footer_phone): ?> | <?php endif; ?>
                                            <?php if ($footer_phone): ?>
                                                Tel: <?= htmlspecialchars($footer_phone) ?>
                                            <?php endif; ?>
                                        </p>
                                        <?php endif; ?>
                                        
                                        <!-- USt-IdNr. (falls vorhanden) -->
                                        <?php if ($footer_tax_id): ?>
                                        <p style="margin: 0 0 8px 0;">
                                            USt-IdNr.: <?= htmlspecialchars($footer_tax_id) ?>
                                        </p>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Datenschutz & Abmelden Links -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 20px;">
                                <tr>
                                    <td style="text-align: center; font-size: 12px;">
                                        <a href="<?= htmlspecialchars($privacy_url) ?>" style="color: #94a3b8; text-decoration: underline;">Datenschutz</a>
                                        <span style="color: #cbd5e1; margin: 0 8px;">|</span>
                                        <a href="<?= htmlspecialchars($unsubscribe_url) ?>" style="color: #94a3b8; text-decoration: underline;">E-Mails abbestellen</a>
                                    </td>
                                </tr>
                            </table>
                            
                            <!-- Powered by -->
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="margin-top: 20px;">
                                <tr>
                                    <td style="text-align: center; font-size: 11px; color: #cbd5e1;">
                                        Empfehlungsprogramm powered by 
                                        <a href="https://empfehlungen.cloud" style="color: #94a3b8; text-decoration: none;">Leadbusiness</a>
                                    </td>
                                </tr>
                            </table>
                            
                        </td>
                    </tr>
                    
                </table>
                
                <!-- Anti-Spam Hinweis (außerhalb der Karte) -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="container" style="margin: 20px auto 0;">
                    <tr>
                        <td style="text-align: center; font-size: 11px; color: #94a3b8; line-height: 1.5;">
                            Sie erhalten diese E-Mail, weil Sie am Empfehlungsprogramm von <?= htmlspecialchars($company_name) ?> teilnehmen.<br>
                            <a href="<?= htmlspecialchars($unsubscribe_url) ?>" style="color: #94a3b8;">Hier abmelden</a>, wenn Sie keine E-Mails mehr erhalten möchten.
                        </td>
                    </tr>
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>
