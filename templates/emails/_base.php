<?php
/**
 * Leadbusiness - E-Mail Basis-Template
 * 
 * Dieses Template wird von allen anderen E-Mail-Templates verwendet.
 * Es enthält das grundlegende HTML-Gerüst mit Header und Footer.
 * 
 * Verfügbare Variablen:
 * - $subject: E-Mail-Betreff
 * - $preheader: Vorschautext (optional)
 * - $content: Hauptinhalt der E-Mail
 * - $company_name: Name des Unternehmens
 * - $company_logo: URL zum Logo (optional)
 * - $primary_color: Hauptfarbe (Hex, z.B. #667eea)
 * - $footer_address: Impressums-Adresse
 * - $unsubscribe_url: Abmelde-Link
 */

// Defaults
$primary_color = $primary_color ?? '#667eea';
$company_logo = $company_logo ?? null;
$preheader = $preheader ?? '';
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
        
        /* Mobile */
        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; padding: 10px !important; }
            .content { padding: 20px !important; }
            .button { width: 100% !important; }
            .mobile-hide { display: none !important; }
            .mobile-center { text-align: center !important; }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    
    <!-- Preheader (versteckter Vorschautext) -->
    <?php if (!empty($preheader)): ?>
    <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;">
        <?= htmlspecialchars($preheader) ?>
        &nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;
    </div>
    <?php endif; ?>
    
    <!-- Outer Container -->
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f4f6f9;">
        <tr>
            <td style="padding: 20px 10px;">
                
                <!-- Main Container -->
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" class="container" style="margin: 0 auto; max-width: 600px;">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center; background: linear-gradient(135deg, <?= htmlspecialchars($primary_color) ?> 0%, #764ba2 100%); border-radius: 12px 12px 0 0;">
                            <?php if ($company_logo): ?>
                            <img src="<?= htmlspecialchars($company_logo) ?>" alt="<?= htmlspecialchars($company_name) ?>" style="max-height: 50px; max-width: 200px; margin-bottom: 10px;">
                            <?php else: ?>
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700;">
                                <?= htmlspecialchars($company_name) ?>
                            </h1>
                            <?php endif; ?>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td class="content" style="padding: 40px; background-color: #ffffff;">
                            <?= $content ?>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; background-color: #f8fafc; border-radius: 0 0 12px 12px; text-align: center;">
                            <p style="margin: 0 0 10px 0; color: #64748b; font-size: 13px; line-height: 1.5;">
                                <?= htmlspecialchars($company_name) ?><br>
                                <?= htmlspecialchars($footer_address ?? '') ?>
                            </p>
                            
                            <?php if (!empty($unsubscribe_url)): ?>
                            <p style="margin: 15px 0 0 0; color: #94a3b8; font-size: 12px;">
                                <a href="<?= htmlspecialchars($unsubscribe_url) ?>" style="color: #94a3b8; text-decoration: underline;">
                                    E-Mail-Benachrichtigungen anpassen
                                </a>
                            </p>
                            <?php endif; ?>
                            
                            <p style="margin: 15px 0 0 0; color: #cbd5e1; font-size: 11px;">
                                Powered by <a href="https://empfehlungen.cloud" style="color: #94a3b8; text-decoration: none;">Leadbusiness</a>
                            </p>
                        </td>
                    </tr>
                    
                </table>
                
            </td>
        </tr>
    </table>
    
</body>
</html>
