<?php
/**
 * Leadbusiness - One-Click Unsubscribe Endpoint
 * 
 * Erfüllt die Gmail/Yahoo 2024 Anforderungen für One-Click Unsubscribe
 * sowie RFC 8058 (One-Click Unsubscribe)
 * 
 * Unterstützt:
 * - POST-Request (One-Click via E-Mail-Client)
 * - GET-Request (Klick auf Abmeldelink in E-Mail)
 */

require_once __DIR__ . '/../includes/Database.php';

// CORS für One-Click
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');

$db = Database::getInstance();
$method = $_SERVER['REQUEST_METHOD'];

// Token aus URL oder POST-Body
$token = $_GET['token'] ?? $_POST['token'] ?? null;
$leadId = $_GET['lid'] ?? $_POST['lid'] ?? null;

// Validierung
if (empty($token) && empty($leadId)) {
    http_response_code(400);
    showErrorPage('Ungültiger Abmeldelink', 'Der Abmeldelink ist ungültig oder abgelaufen.');
    exit;
}

// Lead finden
if ($token) {
    $lead = $db->fetch(
        "SELECT l.*, c.company_name, c.logo_url, c.primary_color, c.subdomain 
         FROM leads l 
         JOIN customers c ON l.customer_id = c.id 
         WHERE l.unsubscribe_token = ?",
        [$token]
    );
} else {
    // Fallback: Lead-ID mit Validierung
    $lead = $db->fetch(
        "SELECT l.*, c.company_name, c.logo_url, c.primary_color, c.subdomain 
         FROM leads l 
         JOIN customers c ON l.customer_id = c.id 
         WHERE l.id = ?",
        [$leadId]
    );
}

if (!$lead) {
    http_response_code(404);
    showErrorPage('Lead nicht gefunden', 'Der Abmeldelink ist ungültig oder die E-Mail-Adresse wurde bereits entfernt.');
    exit;
}

// Bereits abgemeldet?
if ($lead['email_unsubscribed']) {
    showSuccessPage($lead, true);
    exit;
}

// POST = One-Click Unsubscribe (direkt ausführen)
if ($method === 'POST') {
    executeUnsubscribe($db, $lead);
    
    // RFC 8058: Erfolg mit 200 OK signalisieren
    http_response_code(200);
    header('Content-Type: text/plain');
    echo "Unsubscribed successfully";
    exit;
}

// GET = Bestätigungsseite anzeigen (mit Bestätigungsbutton)
if (isset($_GET['confirm']) && $_GET['confirm'] === '1') {
    executeUnsubscribe($db, $lead);
    showSuccessPage($lead, false);
    exit;
}

// Bestätigungsseite anzeigen
showConfirmPage($lead, $token);

/**
 * Abmeldung durchführen
 */
function executeUnsubscribe($db, $lead) {
    $db->update('leads', [
        'email_unsubscribed' => 1,
        'email_unsubscribed_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ], 'id = ?', [$lead['id']]);
    
    // Log
    $db->insert('activity_log', [
        'customer_id' => $lead['customer_id'],
        'lead_id' => $lead['id'],
        'action' => 'email_unsubscribed',
        'details' => json_encode([
            'method' => $_SERVER['REQUEST_METHOD'],
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ]),
        'created_at' => date('Y-m-d H:i:s')
    ]);
}

/**
 * Bestätigungsseite anzeigen
 */
function showConfirmPage($lead, $token) {
    $primaryColor = $lead['primary_color'] ?? '#667eea';
    $confirmUrl = "/unsubscribe?token=" . urlencode($token) . "&confirm=1";
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>E-Mail-Abmeldung - <?= htmlspecialchars($lead['company_name']) ?></title>
        <style>
            :root { --primary: <?= htmlspecialchars($primaryColor) ?>; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                padding: 40px;
                max-width: 500px;
                width: 100%;
                text-align: center;
            }
            .logo { max-height: 50px; margin-bottom: 20px; }
            .icon {
                width: 80px;
                height: 80px;
                background: #fef3c7;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 24px;
            }
            .icon svg { width: 40px; height: 40px; color: #d97706; }
            h1 { font-size: 1.5rem; color: #0f172a; margin-bottom: 12px; }
            p { color: #64748b; line-height: 1.6; margin-bottom: 24px; }
            .email-box {
                background: #f1f5f9;
                border-radius: 8px;
                padding: 12px 20px;
                margin-bottom: 24px;
                font-family: monospace;
                color: #334155;
            }
            .btn {
                display: inline-block;
                padding: 14px 32px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 1rem;
                text-decoration: none;
                transition: all 0.2s;
                cursor: pointer;
                border: none;
            }
            .btn-danger {
                background: #ef4444;
                color: white;
            }
            .btn-danger:hover { background: #dc2626; }
            .btn-secondary {
                background: #e2e8f0;
                color: #475569;
                margin-left: 12px;
            }
            .btn-secondary:hover { background: #cbd5e1; }
            .buttons { margin-top: 20px; }
            .note {
                margin-top: 24px;
                padding-top: 24px;
                border-top: 1px solid #e2e8f0;
                font-size: 0.875rem;
                color: #94a3b8;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <?php if ($lead['logo_url']): ?>
                <img src="<?= htmlspecialchars($lead['logo_url']) ?>" alt="<?= htmlspecialchars($lead['company_name']) ?>" class="logo">
            <?php endif; ?>
            
            <div class="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            
            <h1>E-Mail-Benachrichtigungen abmelden?</h1>
            <p>
                Sie sind dabei, sich von allen E-Mail-Benachrichtigungen des Empfehlungsprogramms 
                von <strong><?= htmlspecialchars($lead['company_name']) ?></strong> abzumelden.
            </p>
            
            <div class="email-box">
                <?= htmlspecialchars($lead['email']) ?>
            </div>
            
            <p>
                Sie erhalten dann keine weiteren E-Mails mehr über Ihre Empfehlungen, 
                Belohnungen oder Ihren Fortschritt.
            </p>
            
            <div class="buttons">
                <a href="<?= htmlspecialchars($confirmUrl) ?>" class="btn btn-danger">
                    Ja, abmelden
                </a>
                <a href="https://<?= htmlspecialchars($lead['subdomain']) ?>.empfohlen.de" class="btn btn-secondary">
                    Abbrechen
                </a>
            </div>
            
            <p class="note">
                Sie können sich jederzeit erneut für E-Mail-Benachrichtigungen anmelden, 
                indem Sie sich in Ihrem Dashboard einloggen.
            </p>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Erfolgsseite anzeigen
 */
function showSuccessPage($lead, $alreadyUnsubscribed = false) {
    $primaryColor = $lead['primary_color'] ?? '#667eea';
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Abmeldung erfolgreich - <?= htmlspecialchars($lead['company_name']) ?></title>
        <style>
            :root { --primary: <?= htmlspecialchars($primaryColor) ?>; }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                padding: 40px;
                max-width: 500px;
                width: 100%;
                text-align: center;
            }
            .logo { max-height: 50px; margin-bottom: 20px; }
            .icon {
                width: 80px;
                height: 80px;
                background: #dcfce7;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 24px;
            }
            .icon svg { width: 40px; height: 40px; color: #16a34a; }
            h1 { font-size: 1.5rem; color: #0f172a; margin-bottom: 12px; }
            p { color: #64748b; line-height: 1.6; margin-bottom: 16px; }
            .email-box {
                background: #f1f5f9;
                border-radius: 8px;
                padding: 12px 20px;
                margin-bottom: 24px;
                font-family: monospace;
                color: #334155;
            }
            .btn {
                display: inline-block;
                padding: 14px 32px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 1rem;
                text-decoration: none;
                transition: all 0.2s;
                background: var(--primary);
                color: white;
            }
            .btn:hover { opacity: 0.9; }
        </style>
    </head>
    <body>
        <div class="card">
            <?php if ($lead['logo_url']): ?>
                <img src="<?= htmlspecialchars($lead['logo_url']) ?>" alt="<?= htmlspecialchars($lead['company_name']) ?>" class="logo">
            <?php endif; ?>
            
            <div class="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            
            <h1><?= $alreadyUnsubscribed ? 'Bereits abgemeldet' : 'Erfolgreich abgemeldet' ?></h1>
            
            <div class="email-box">
                <?= htmlspecialchars($lead['email']) ?>
            </div>
            
            <p>
                <?php if ($alreadyUnsubscribed): ?>
                    Diese E-Mail-Adresse wurde bereits von den Benachrichtigungen abgemeldet.
                <?php else: ?>
                    Sie erhalten keine E-Mail-Benachrichtigungen mehr vom Empfehlungsprogramm 
                    von <?= htmlspecialchars($lead['company_name']) ?>.
                <?php endif; ?>
            </p>
            
            <p>
                Ihr Konto und Ihre gesammelten Belohnungen bleiben erhalten. 
                Sie können sich jederzeit wieder anmelden.
            </p>
            
            <a href="https://<?= htmlspecialchars($lead['subdomain']) ?>.empfohlen.de" class="btn">
                Zur Empfehlungsseite
            </a>
        </div>
    </body>
    </html>
    <?php
}

/**
 * Fehlerseite anzeigen
 */
function showErrorPage($title, $message) {
    ?>
    <!DOCTYPE html>
    <html lang="de">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .card {
                background: white;
                border-radius: 16px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.1);
                padding: 40px;
                max-width: 500px;
                width: 100%;
                text-align: center;
            }
            .icon {
                width: 80px;
                height: 80px;
                background: #fee2e2;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 24px;
            }
            .icon svg { width: 40px; height: 40px; color: #dc2626; }
            h1 { font-size: 1.5rem; color: #0f172a; margin-bottom: 12px; }
            p { color: #64748b; line-height: 1.6; }
            .btn {
                display: inline-block;
                margin-top: 24px;
                padding: 14px 32px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 1rem;
                text-decoration: none;
                background: #667eea;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="card">
            <div class="icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p><?= htmlspecialchars($message) ?></p>
            <a href="https://empfehlungen.cloud" class="btn">Zur Startseite</a>
        </div>
    </body>
    </html>
    <?php
}
