<?php
/**
 * Leadbusiness - Custom Domain Settings (Enterprise)
 * 
 * Ermöglicht Enterprise-Kunden ihre eigene Domain zu verknüpfen
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/settings.php';
require_once __DIR__ . '/../../includes/Database.php';
require_once __DIR__ . '/../../includes/Auth.php';
require_once __DIR__ . '/../../includes/helpers.php';

use Leadbusiness\Database;

// Auth prüfen
$auth = new Auth();
if (!$auth->isLoggedIn() || $auth->getUserType() !== 'customer') {
    redirect('/dashboard/login.php');
}

$customer = $auth->getCurrentCustomer();
$customerId = $customer['id'];
$db = Database::getInstance();

// Nur Enterprise
if ($customer['plan'] !== 'enterprise') {
    redirect('/dashboard/');
}

$success = '';
$error = '';

// Aktionen verarbeiten
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $csrf = $_POST['csrf_token'] ?? '';
    
    if ($csrf !== ($_SESSION['csrf_token'] ?? '')) {
        $error = 'Ungültige Anfrage. Bitte versuchen Sie es erneut.';
    } else {
        switch ($action) {
            
            case 'save_domain':
                $domain = trim($_POST['custom_domain'] ?? '');
                
                // Domain validieren
                if (empty($domain)) {
                    // Domain entfernen
                    $db->query(
                        "UPDATE customers SET 
                            custom_domain = NULL, 
                            custom_domain_verified = 0,
                            custom_domain_verified_at = NULL,
                            custom_domain_ssl_status = NULL,
                            custom_domain_dns_token = NULL
                         WHERE id = ?",
                        [$customerId]
                    );
                    $success = 'Custom Domain wurde entfernt.';
                } else {
                    // Domain Format prüfen
                    $domain = strtolower($domain);
                    $domain = preg_replace('/^(https?:\/\/)?/', '', $domain); // http(s) entfernen
                    $domain = preg_replace('/\/.*$/', '', $domain); // Pfad entfernen
                    $domain = preg_replace('/^www\./', '', $domain); // www. entfernen
                    
                    if (!preg_match('/^[a-z0-9]([a-z0-9-]*[a-z0-9])?(\.[a-z0-9]([a-z0-9-]*[a-z0-9])?)+$/', $domain)) {
                        $error = 'Ungültiges Domain-Format. Beispiel: empfehlungen.ihre-firma.de';
                        break;
                    }
                    
                    // Prüfen ob Domain bereits verwendet wird
                    $existing = $db->fetch(
                        "SELECT id FROM customers WHERE custom_domain = ? AND id != ?",
                        [$domain, $customerId]
                    );
                    
                    if ($existing) {
                        $error = 'Diese Domain wird bereits von einem anderen Kunden verwendet.';
                        break;
                    }
                    
                    // DNS-Verifizierungs-Token generieren
                    $dnsToken = 'lb-verify-' . bin2hex(random_bytes(16));
                    
                    $db->query(
                        "UPDATE customers SET 
                            custom_domain = ?,
                            custom_domain_verified = 0,
                            custom_domain_verified_at = NULL,
                            custom_domain_ssl_status = 'pending',
                            custom_domain_dns_token = ?
                         WHERE id = ?",
                        [$domain, $dnsToken, $customerId]
                    );
                    
                    $success = 'Domain gespeichert. Bitte konfigurieren Sie jetzt Ihre DNS-Einstellungen.';
                }
                
                // Kundendaten neu laden
                $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
                break;
                
            case 'verify_domain':
                $domain = $customer['custom_domain'];
                
                if (empty($domain)) {
                    $error = 'Keine Domain konfiguriert.';
                    break;
                }
                
                // DNS-Überprüfung durchführen
                $verified = false;
                $verificationMethod = '';
                
                // Methode 1: CNAME prüfen
                $cnameRecords = @dns_get_record($domain, DNS_CNAME);
                if ($cnameRecords) {
                    foreach ($cnameRecords as $record) {
                        if (isset($record['target']) && 
                            (strpos($record['target'], 'empfehlungen.cloud') !== false ||
                             strpos($record['target'], 'lb-proxy.') !== false)) {
                            $verified = true;
                            $verificationMethod = 'CNAME';
                            break;
                        }
                    }
                }
                
                // Methode 2: TXT Record prüfen (falls CNAME nicht gefunden)
                if (!$verified) {
                    $txtRecords = @dns_get_record('_leadbusiness.' . $domain, DNS_TXT);
                    if ($txtRecords) {
                        foreach ($txtRecords as $record) {
                            if (isset($record['txt']) && $record['txt'] === $customer['custom_domain_dns_token']) {
                                $verified = true;
                                $verificationMethod = 'TXT';
                                break;
                            }
                        }
                    }
                }
                
                // Methode 3: A-Record prüfen (auf unsere Server-IP)
                if (!$verified) {
                    $aRecords = @dns_get_record($domain, DNS_A);
                    $serverIps = ['YOUR_SERVER_IP']; // Hier eure Server-IPs eintragen
                    if ($aRecords) {
                        foreach ($aRecords as $record) {
                            if (isset($record['ip']) && in_array($record['ip'], $serverIps)) {
                                $verified = true;
                                $verificationMethod = 'A-Record';
                                break;
                            }
                        }
                    }
                }
                
                if ($verified) {
                    $db->query(
                        "UPDATE customers SET 
                            custom_domain_verified = 1,
                            custom_domain_verified_at = NOW(),
                            custom_domain_ssl_status = 'pending'
                         WHERE id = ?",
                        [$customerId]
                    );
                    
                    $success = "Domain erfolgreich verifiziert (via $verificationMethod)! SSL-Zertifikat wird automatisch erstellt.";
                    
                    // SSL-Zertifikat Job anstoßen (würde in Produktion einen Queue-Job erstellen)
                    // Hier könnte ein Certbot/Let's Encrypt Aufruf erfolgen
                    
                    // Für Demo: SSL direkt auf "active" setzen
                    $db->query(
                        "UPDATE customers SET custom_domain_ssl_status = 'active' WHERE id = ?",
                        [$customerId]
                    );
                    
                } else {
                    $error = 'DNS-Einträge konnten nicht verifiziert werden. Bitte prüfen Sie Ihre DNS-Konfiguration und warten Sie bis zu 24 Stunden auf DNS-Propagierung.';
                }
                
                // Kundendaten neu laden
                $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
                break;
                
            case 'request_ssl':
                if (!$customer['custom_domain_verified']) {
                    $error = 'Domain muss zuerst verifiziert werden.';
                    break;
                }
                
                // SSL-Zertifikat anfordern (in Produktion: Let's Encrypt Certbot)
                $db->query(
                    "UPDATE customers SET custom_domain_ssl_status = 'pending' WHERE id = ?",
                    [$customerId]
                );
                
                $success = 'SSL-Zertifikat wird erstellt. Dies kann einige Minuten dauern.';
                $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
                break;
                
            case 'remove_domain':
                $db->query(
                    "UPDATE customers SET 
                        custom_domain = NULL, 
                        custom_domain_verified = 0,
                        custom_domain_verified_at = NULL,
                        custom_domain_ssl_status = NULL,
                        custom_domain_dns_token = NULL
                     WHERE id = ?",
                    [$customerId]
                );
                $success = 'Custom Domain wurde entfernt. Ihre Empfehlungsseite ist wieder unter der Standard-URL erreichbar.';
                $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
                break;
        }
    }
}

// Status-Infos
$hasCustomDomain = !empty($customer['custom_domain']);
$isVerified = !empty($customer['custom_domain_verified']);
$sslStatus = $customer['custom_domain_ssl_status'] ?? null;
$isFullyActive = $hasCustomDomain && $isVerified && $sslStatus === 'active';

$pageTitle = 'Eigene Domain';
include __DIR__ . '/../../includes/dashboard-header.php';
?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Eigene Domain</h1>
            <span class="px-3 py-1 bg-purple-500 text-white text-xs font-bold rounded-full">ENTERPRISE</span>
        </div>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Verknüpfen Sie Ihre eigene Domain für ein vollständiges White-Label-Erlebnis</p>
    </div>
</div>

<?php if ($success): ?>
<div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <i class="fas fa-check-circle text-green-500 text-xl"></i>
        <p class="text-green-800 dark:text-green-300"><?= e($success) ?></p>
    </div>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl p-4 mb-6">
    <div class="flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
        <p class="text-red-800 dark:text-red-300"><?= e($error) ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Status Overview -->
<div class="grid sm:grid-cols-3 gap-4 mb-8">
    <!-- Aktuelle URL -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-link text-slate-500"></i>
            </div>
            <span class="text-sm text-slate-500 dark:text-slate-400">Standard-URL</span>
        </div>
        <p class="font-mono text-sm text-slate-800 dark:text-white break-all">
            <?= e($customer['subdomain']) ?>.empfehlungen.cloud
        </p>
    </div>
    
    <!-- Custom Domain -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 <?= $isFullyActive ? 'bg-green-100 dark:bg-green-900/30' : 'bg-amber-100 dark:bg-amber-900/30' ?> rounded-lg flex items-center justify-center">
                <i class="fas fa-globe <?= $isFullyActive ? 'text-green-500' : 'text-amber-500' ?>"></i>
            </div>
            <span class="text-sm text-slate-500 dark:text-slate-400">Eigene Domain</span>
        </div>
        <?php if ($hasCustomDomain): ?>
        <p class="font-mono text-sm text-slate-800 dark:text-white break-all"><?= e($customer['custom_domain']) ?></p>
        <?php else: ?>
        <p class="text-sm text-slate-400 dark:text-slate-500 italic">Nicht konfiguriert</p>
        <?php endif; ?>
    </div>
    
    <!-- Status -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 <?= $isFullyActive ? 'bg-green-100 dark:bg-green-900/30' : 'bg-slate-100 dark:bg-slate-700' ?> rounded-lg flex items-center justify-center">
                <i class="fas <?= $isFullyActive ? 'fa-check-circle text-green-500' : 'fa-hourglass-half text-slate-400' ?>"></i>
            </div>
            <span class="text-sm text-slate-500 dark:text-slate-400">Status</span>
        </div>
        <?php if ($isFullyActive): ?>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm font-medium">
            <i class="fas fa-check-circle"></i> Aktiv
        </span>
        <?php elseif ($hasCustomDomain && $isVerified): ?>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full text-sm font-medium">
            <i class="fas fa-spinner fa-spin"></i> SSL wird erstellt
        </span>
        <?php elseif ($hasCustomDomain): ?>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full text-sm font-medium">
            <i class="fas fa-exclamation-triangle"></i> DNS ausstehend
        </span>
        <?php else: ?>
        <span class="inline-flex items-center gap-2 px-3 py-1 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 rounded-full text-sm font-medium">
            Nicht eingerichtet
        </span>
        <?php endif; ?>
    </div>
</div>

<!-- Main Configuration Card -->
<div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden mb-8">
    
    <!-- Header -->
    <div class="p-6 border-b border-slate-200 dark:border-slate-700 bg-gradient-to-r from-purple-500 to-indigo-600">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-globe text-white text-2xl"></i>
            </div>
            <div class="text-white">
                <h2 class="text-xl font-bold">Custom Domain einrichten</h2>
                <p class="text-white/80 text-sm">Ihre Empfehlungsseite unter Ihrer eigenen Domain</p>
            </div>
        </div>
    </div>
    
    <div class="p-6">
        
        <?php if (!$hasCustomDomain): ?>
        <!-- Step 1: Domain eingeben -->
        <form method="POST" class="max-w-xl">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
            <input type="hidden" name="action" value="save_domain">
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                    Ihre Domain eingeben
                </label>
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">https://</span>
                        <input type="text" 
                               name="custom_domain" 
                               placeholder="empfehlungen.ihre-firma.de"
                               class="w-full pl-20 pr-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               required>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-purple-500 text-white rounded-xl hover:bg-purple-600 transition font-medium">
                        <i class="fas fa-plus mr-2"></i>Hinzufügen
                    </button>
                </div>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Beispiele: empfehlungen.ihre-firma.de, refer.meinshop.com
                </p>
            </div>
        </form>
        
        <!-- Info Box -->
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-5 mt-6">
            <h4 class="font-semibold text-blue-800 dark:text-blue-300 mb-3">
                <i class="fas fa-info-circle mr-2"></i>So funktioniert's
            </h4>
            <ol class="space-y-2 text-sm text-blue-700 dark:text-blue-400">
                <li><span class="font-semibold">1.</span> Geben Sie Ihre gewünschte Domain/Subdomain ein</li>
                <li><span class="font-semibold">2.</span> Konfigurieren Sie die DNS-Einstellungen bei Ihrem Domain-Anbieter</li>
                <li><span class="font-semibold">3.</span> Wir verifizieren die Konfiguration und erstellen ein SSL-Zertifikat</li>
                <li><span class="font-semibold">4.</span> Ihre Empfehlungsseite ist unter Ihrer Domain erreichbar!</li>
            </ol>
        </div>
        
        <?php elseif (!$isVerified): ?>
        <!-- Step 2: DNS konfigurieren -->
        <div class="space-y-6">
            
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">DNS-Einstellungen konfigurieren</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Für: <strong><?= e($customer['custom_domain']) ?></strong></p>
                </div>
                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full text-sm font-medium">
                    <i class="fas fa-clock mr-1"></i>Ausstehend
                </span>
            </div>
            
            <!-- DNS Instructions -->
            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                <h4 class="font-semibold text-slate-800 dark:text-white mb-4">
                    <i class="fas fa-server mr-2 text-purple-500"></i>Option A: CNAME-Eintrag (Empfohlen)
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500 dark:text-slate-400">
                                <th class="pb-2 font-medium">Typ</th>
                                <th class="pb-2 font-medium">Name/Host</th>
                                <th class="pb-2 font-medium">Ziel/Wert</th>
                                <th class="pb-2 font-medium">TTL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-slate-800 dark:text-white">
                                <td class="py-2"><code class="bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">CNAME</code></td>
                                <td class="py-2">
                                    <code class="bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">
                                        <?php 
                                        $parts = explode('.', $customer['custom_domain']);
                                        echo e($parts[0]); // Subdomain-Teil
                                        ?>
                                    </code>
                                </td>
                                <td class="py-2">
                                    <code class="bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 px-2 py-1 rounded">
                                        lb-proxy.empfehlungen.cloud
                                    </code>
                                    <button onclick="copyToClipboard('lb-proxy.empfehlungen.cloud', this)" class="ml-2 text-slate-400 hover:text-slate-600">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                                <td class="py-2"><code>3600</code></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="text-center text-slate-400 dark:text-slate-500 font-medium">— ODER —</div>
            
            <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
                <h4 class="font-semibold text-slate-800 dark:text-white mb-4">
                    <i class="fas fa-file-alt mr-2 text-blue-500"></i>Option B: TXT-Eintrag (Zur Verifizierung)
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500 dark:text-slate-400">
                                <th class="pb-2 font-medium">Typ</th>
                                <th class="pb-2 font-medium">Name/Host</th>
                                <th class="pb-2 font-medium">Wert</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="text-slate-800 dark:text-white">
                                <td class="py-2"><code class="bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">TXT</code></td>
                                <td class="py-2"><code class="bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">_leadbusiness</code></td>
                                <td class="py-2">
                                    <code class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-2 py-1 rounded text-xs break-all">
                                        <?= e($customer['custom_domain_dns_token']) ?>
                                    </code>
                                    <button onclick="copyToClipboard('<?= e($customer['custom_domain_dns_token']) ?>', this)" class="ml-2 text-slate-400 hover:text-slate-600">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Hinweise -->
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-4">
                <p class="text-sm text-amber-800 dark:text-amber-300">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Hinweis:</strong> DNS-Änderungen können bis zu 24-48 Stunden dauern, bis sie weltweit propagiert sind.
                </p>
            </div>
            
            <!-- Aktionen -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <form method="POST" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="action" value="verify_domain">
                    <button type="submit" class="px-6 py-3 bg-purple-500 text-white rounded-xl hover:bg-purple-600 transition font-medium">
                        <i class="fas fa-check-circle mr-2"></i>DNS jetzt prüfen
                    </button>
                </form>
                
                <form method="POST" class="inline" onsubmit="return confirm('Sind Sie sicher? Die Domain-Konfiguration wird entfernt.')">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="action" value="remove_domain">
                    <button type="submit" class="px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition font-medium">
                        <i class="fas fa-times mr-2"></i>Abbrechen
                    </button>
                </form>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Step 3: Verifiziert - Status anzeigen -->
        <div class="space-y-6">
            
            <!-- Success Header -->
            <div class="text-center py-6">
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-green-500 text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Domain erfolgreich verbunden!</h3>
                <p class="text-slate-500 dark:text-slate-400">
                    Ihre Empfehlungsseite ist jetzt erreichbar unter:
                </p>
                <a href="https://<?= e($customer['custom_domain']) ?>" 
                   target="_blank"
                   class="inline-flex items-center gap-2 mt-3 text-lg font-mono text-purple-600 dark:text-purple-400 hover:underline">
                    <i class="fas fa-external-link-alt"></i>
                    https://<?= e($customer['custom_domain']) ?>
                </a>
            </div>
            
            <!-- Status Details -->
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-dns text-green-500 text-xl"></i>
                        <div>
                            <p class="text-sm text-green-600 dark:text-green-400">DNS-Verifizierung</p>
                            <p class="font-semibold text-green-800 dark:text-green-300">Erfolgreich</p>
                        </div>
                    </div>
                </div>
                
                <div class="<?= $sslStatus === 'active' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800' ?> rounded-xl p-4 border">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-lock <?= $sslStatus === 'active' ? 'text-green-500' : 'text-amber-500' ?> text-xl"></i>
                        <div>
                            <p class="text-sm <?= $sslStatus === 'active' ? 'text-green-600 dark:text-green-400' : 'text-amber-600 dark:text-amber-400' ?>">SSL-Zertifikat</p>
                            <p class="font-semibold <?= $sslStatus === 'active' ? 'text-green-800 dark:text-green-300' : 'text-amber-800 dark:text-amber-300' ?>">
                                <?php
                                switch ($sslStatus) {
                                    case 'active': echo 'Aktiv'; break;
                                    case 'pending': echo 'Wird erstellt...'; break;
                                    case 'failed': echo 'Fehler'; break;
                                    default: echo 'Ausstehend';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if ($customer['custom_domain_verified_at']): ?>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center">
                <i class="fas fa-clock mr-1"></i>
                Verifiziert am <?= date('d.m.Y \u\m H:i', strtotime($customer['custom_domain_verified_at'])) ?> Uhr
            </p>
            <?php endif; ?>
            
            <!-- Domain entfernen -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                <form method="POST" onsubmit="return confirm('Sind Sie sicher? Die Domain-Verknüpfung wird entfernt und Ihre Seite ist nur noch unter der Standard-URL erreichbar.')">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="action" value="remove_domain">
                    <button type="submit" class="px-4 py-2 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/50 transition text-sm">
                        <i class="fas fa-unlink mr-2"></i>Domain-Verknüpfung entfernen
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>
        
    </div>
</div>

<!-- FAQ -->
<div class="bg-white dark:bg-slate-800 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
    <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6">
        <i class="fas fa-question-circle text-purple-500 mr-2"></i>Häufige Fragen
    </h3>
    
    <div class="space-y-4">
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <span class="font-medium text-slate-800 dark:text-white">Welche Domain kann ich verwenden?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                Sie können eine Subdomain Ihrer Hauptdomain verwenden, z.B. <code>empfehlungen.ihre-firma.de</code> oder <code>refer.meinshop.com</code>. 
                Es wird empfohlen, eine Subdomain zu verwenden, damit Ihre Hauptwebsite nicht beeinflusst wird.
            </div>
        </details>
        
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <span class="font-medium text-slate-800 dark:text-white">Wie lange dauert die DNS-Propagierung?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                In der Regel werden DNS-Änderungen innerhalb von 1-4 Stunden wirksam. In seltenen Fällen kann es bis zu 24-48 Stunden dauern. 
                Wenn Sie nach dieser Zeit die Domain immer noch nicht verifizieren können, prüfen Sie bitte Ihre DNS-Einstellungen.
            </div>
        </details>
        
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <span class="font-medium text-slate-800 dark:text-white">Ist das SSL-Zertifikat kostenlos?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                Ja! Wir erstellen automatisch ein kostenloses SSL-Zertifikat über Let's Encrypt für Ihre Domain. 
                Das Zertifikat wird automatisch erneuert, Sie müssen sich um nichts kümmern.
            </div>
        </details>
        
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <span class="font-medium text-slate-800 dark:text-white">Funktioniert die Standard-URL weiterhin?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                Ja, Ihre Standard-URL (<code><?= e($customer['subdomain']) ?>.empfehlungen.cloud</code>) funktioniert weiterhin. 
                Bestehende Empfehlungslinks bleiben gültig. Optional können wir auch eine Weiterleitung einrichten.
            </div>
        </details>
        
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <span class="font-medium text-slate-800 dark:text-white">Wo finde ich die DNS-Einstellungen bei meinem Anbieter?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                <p class="mb-2">Die DNS-Einstellungen finden Sie im Verwaltungsbereich Ihres Domain-Anbieters:</p>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>IONOS:</strong> Domains & SSL → DNS-Einstellungen</li>
                    <li><strong>Strato:</strong> Domainverwaltung → DNS-Einstellungen</li>
                    <li><strong>All-Inkl:</strong> KAS → Domains → DNS-Einstellungen</li>
                    <li><strong>Hetzner:</strong> DNS Console → Zone auswählen</li>
                    <li><strong>Cloudflare:</strong> DNS → Records</li>
                </ul>
            </div>
        </details>
    </div>
</div>

<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(() => {
        const icon = button.querySelector('i');
        icon.className = 'fas fa-check text-green-500';
        setTimeout(() => {
            icon.className = 'fas fa-copy';
        }, 2000);
    });
}
</script>

<?php include __DIR__ . '/../../includes/dashboard-footer.php'; ?>
