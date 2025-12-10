<?php
/**
 * Leadbusiness - Custom Domain Settings (Enterprise)
 * 
 * Ermöglicht Enterprise-Kunden ihre eigene Domain zu verknüpfen
 * SSL ist Verantwortung des Kunden (z.B. über Cloudflare)
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

// Server IP für A-Record (hier deine Server-IP eintragen)
$serverIp = '91.99.XXX.XXX'; // TODO: Deine Server-IP

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
                            custom_domain_dns_token = NULL
                         WHERE id = ?",
                        [$customerId]
                    );
                    $success = 'Custom Domain wurde entfernt.';
                } else {
                    // Domain Format prüfen
                    $domain = strtolower($domain);
                    $domain = preg_replace('/^(https?:\/\/)?/', '', $domain);
                    $domain = preg_replace('/\/.*$/', '', $domain);
                    $domain = preg_replace('/^www\./', '', $domain);
                    
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
                            custom_domain_dns_token = ?
                         WHERE id = ?",
                        [$domain, $dnsToken, $customerId]
                    );
                    
                    $success = 'Domain gespeichert. Bitte konfigurieren Sie jetzt Ihre DNS-Einstellungen.';
                }
                
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
                
                // Methode 1: A-Record prüfen (direkt auf unsere IP)
                $aRecords = @dns_get_record($domain, DNS_A);
                if ($aRecords) {
                    foreach ($aRecords as $record) {
                        if (isset($record['ip']) && $record['ip'] === $serverIp) {
                            $verified = true;
                            $verificationMethod = 'A-Record';
                            break;
                        }
                    }
                }
                
                // Methode 2: CNAME prüfen
                if (!$verified) {
                    $cnameRecords = @dns_get_record($domain, DNS_CNAME);
                    if ($cnameRecords) {
                        foreach ($cnameRecords as $record) {
                            if (isset($record['target']) && 
                                strpos($record['target'], 'empfehlungen.cloud') !== false) {
                                $verified = true;
                                $verificationMethod = 'CNAME';
                                break;
                            }
                        }
                    }
                }
                
                // Methode 3: TXT Record prüfen
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
                
                // Methode 4: HTTP-Check (Domain antwortet)
                if (!$verified) {
                    $ch = curl_init();
                    curl_setopt_array($ch, [
                        CURLOPT_URL => 'http://' . $domain,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HEADER => true,
                        CURLOPT_NOBODY => true
                    ]);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);
                    
                    // Wenn die Domain antwortet, ist sie wahrscheinlich korrekt konfiguriert
                    if ($httpCode >= 200 && $httpCode < 500) {
                        $verified = true;
                        $verificationMethod = 'HTTP-Check';
                    }
                }
                
                if ($verified) {
                    $db->query(
                        "UPDATE customers SET 
                            custom_domain_verified = 1,
                            custom_domain_verified_at = NOW()
                         WHERE id = ?",
                        [$customerId]
                    );
                    
                    $success = "Domain erfolgreich verifiziert (via $verificationMethod)! Ihre Empfehlungsseite ist jetzt unter Ihrer Domain erreichbar.";
                    
                } else {
                    $error = 'DNS-Einträge konnten nicht verifiziert werden. Bitte prüfen Sie Ihre DNS-Konfiguration und stellen Sie sicher, dass SSL (z.B. über Cloudflare) aktiviert ist.';
                }
                
                $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
                break;
                
            case 'remove_domain':
                $db->query(
                    "UPDATE customers SET 
                        custom_domain = NULL, 
                        custom_domain_verified = 0,
                        custom_domain_verified_at = NULL,
                        custom_domain_dns_token = NULL
                     WHERE id = ?",
                    [$customerId]
                );
                $success = 'Custom Domain wurde entfernt.';
                $customer = $db->fetch("SELECT * FROM customers WHERE id = ?", [$customerId]);
                break;
        }
    }
}

// Status-Infos
$hasCustomDomain = !empty($customer['custom_domain']);
$isVerified = !empty($customer['custom_domain_verified']);

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
<div class="grid sm:grid-cols-2 gap-4 mb-8">
    <!-- Aktuelle URL -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-link text-slate-500"></i>
            </div>
            <span class="text-sm text-slate-500 dark:text-slate-400">Standard-URL</span>
        </div>
        <p class="font-mono text-sm text-slate-800 dark:text-white break-all">
            https://<?= e($customer['subdomain']) ?>.empfehlungen.cloud
        </p>
    </div>
    
    <!-- Custom Domain Status -->
    <div class="bg-white dark:bg-slate-800 rounded-xl p-5 border border-slate-200 dark:border-slate-700">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 <?= $isVerified ? 'bg-green-100 dark:bg-green-900/30' : ($hasCustomDomain ? 'bg-amber-100 dark:bg-amber-900/30' : 'bg-slate-100 dark:bg-slate-700') ?> rounded-lg flex items-center justify-center">
                <i class="fas <?= $isVerified ? 'fa-check-circle text-green-500' : ($hasCustomDomain ? 'fa-clock text-amber-500' : 'fa-globe text-slate-400') ?>"></i>
            </div>
            <span class="text-sm text-slate-500 dark:text-slate-400">Eigene Domain</span>
        </div>
        <?php if ($isVerified): ?>
        <p class="font-mono text-sm text-green-600 dark:text-green-400 break-all">
            <i class="fas fa-lock mr-1"></i>https://<?= e($customer['custom_domain']) ?>
        </p>
        <?php elseif ($hasCustomDomain): ?>
        <p class="font-mono text-sm text-amber-600 dark:text-amber-400 break-all">
            <?= e($customer['custom_domain']) ?> <span class="text-xs">(ausstehend)</span>
        </p>
        <?php else: ?>
        <p class="text-sm text-slate-400 dark:text-slate-500 italic">Nicht konfiguriert</p>
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
                    <div class="flex-1">
                        <input type="text" 
                               name="custom_domain" 
                               placeholder="empfehlungen.mustermann.de"
                               class="w-full px-4 py-3 border border-slate-300 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               required>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-purple-500 text-white rounded-xl hover:bg-purple-600 transition font-medium whitespace-nowrap">
                        <i class="fas fa-plus mr-2"></i>Hinzufügen
                    </button>
                </div>
                <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">
                    Beispiele: empfehlungen.mustermann.de, refer.meinshop.com
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
                <li><span class="font-semibold">2.</span> Konfigurieren Sie DNS bei Ihrem Anbieter (z.B. Cloudflare)</li>
                <li><span class="font-semibold">3.</span> Aktivieren Sie SSL über Cloudflare (kostenlos) oder Ihren Hoster</li>
                <li><span class="font-semibold">4.</span> Wir verifizieren die Konfiguration</li>
                <li><span class="font-semibold">5.</span> Ihre Empfehlungsseite ist unter Ihrer Domain erreichbar!</li>
            </ol>
        </div>
        
        <?php elseif (!$isVerified): ?>
        <!-- Step 2: DNS konfigurieren -->
        <div class="space-y-6">
            
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-slate-800 dark:text-white">DNS & SSL konfigurieren</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Für: <strong><?= e($customer['custom_domain']) ?></strong></p>
                </div>
                <span class="px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 rounded-full text-sm font-medium">
                    <i class="fas fa-clock mr-1"></i>Ausstehend
                </span>
            </div>
            
            <!-- Empfohlen: Cloudflare -->
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-xl p-5 border-2 border-orange-300 dark:border-orange-700">
                <div class="flex items-center gap-3 mb-4">
                    <img src="https://cdn.simpleicons.org/cloudflare/F38020" alt="Cloudflare" class="w-8 h-8">
                    <div>
                        <h4 class="font-semibold text-slate-800 dark:text-white">Empfohlen: Cloudflare (Kostenlos)</h4>
                        <p class="text-sm text-slate-500">Automatisches SSL + CDN + DDoS-Schutz</p>
                    </div>
                    <span class="ml-auto px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">EMPFOHLEN</span>
                </div>
                
                <ol class="space-y-3 text-sm text-slate-700 dark:text-slate-300">
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        <span>Registrieren Sie sich kostenlos bei <a href="https://cloudflare.com" target="_blank" class="text-orange-600 hover:underline">cloudflare.com</a></span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        <span>Fügen Sie Ihre Domain hinzu und ändern Sie die Nameserver</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-xs font-bold">3</span>
                        <div>
                            <span>Erstellen Sie einen <strong>A-Record</strong> oder <strong>CNAME</strong>:</span>
                            <div class="mt-2 bg-white dark:bg-slate-800 rounded-lg p-3 font-mono text-xs">
                                <div class="grid grid-cols-3 gap-2 mb-2 text-slate-500">
                                    <span>Typ</span><span>Name</span><span>Ziel</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2 text-slate-800 dark:text-white">
                                    <span class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded">A</span>
                                    <span class="bg-slate-100 dark:bg-slate-700 px-2 py-1 rounded"><?= e(explode('.', $customer['custom_domain'])[0]) ?></span>
                                    <span class="bg-orange-100 dark:bg-orange-900/50 px-2 py-1 rounded text-orange-700 dark:text-orange-300"><?= e($serverIp) ?></span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex-shrink-0 w-6 h-6 bg-orange-500 text-white rounded-full flex items-center justify-center text-xs font-bold">4</span>
                        <span>Aktivieren Sie die <strong>orangene Wolke</strong> (Proxy) → SSL ist automatisch aktiv!</span>
                    </li>
                </ol>
            </div>
            
            <!-- Alternative: Eigener Hoster -->
            <details class="bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
                <summary class="p-5 cursor-pointer flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-server text-slate-500"></i>
                        <span class="font-medium text-slate-800 dark:text-white">Alternative: Eigener Hoster</span>
                    </div>
                    <i class="fas fa-chevron-down text-slate-400"></i>
                </summary>
                <div class="p-5 pt-0 border-t border-slate-200 dark:border-slate-700 mt-0">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Wenn Sie Cloudflare nicht nutzen möchten, können Sie die DNS-Einstellungen direkt bei Ihrem Hoster vornehmen:
                    </p>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500 dark:text-slate-400">
                                    <th class="pb-2 font-medium">Typ</th>
                                    <th class="pb-2 font-medium">Name</th>
                                    <th class="pb-2 font-medium">Ziel</th>
                                </tr>
                            </thead>
                            <tbody class="text-slate-800 dark:text-white">
                                <tr>
                                    <td class="py-2"><code class="bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">A</code></td>
                                    <td class="py-2"><code class="bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded"><?= e(explode('.', $customer['custom_domain'])[0]) ?></code></td>
                                    <td class="py-2">
                                        <code class="bg-purple-100 dark:bg-purple-900/30 px-2 py-1 rounded text-purple-700 dark:text-purple-300"><?= e($serverIp) ?></code>
                                        <button onclick="copyToClipboard('<?= e($serverIp) ?>', this)" class="ml-2 text-slate-400 hover:text-slate-600">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <p class="text-sm text-amber-800 dark:text-amber-300">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <strong>Wichtig:</strong> Sie müssen selbst ein SSL-Zertifikat einrichten (z.B. Let's Encrypt über Ihren Hoster).
                        </p>
                    </div>
                </div>
            </details>
            
            <!-- TXT-Verifizierung (Alternative) -->
            <details class="bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
                <summary class="p-5 cursor-pointer flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-file-alt text-slate-500"></i>
                        <span class="font-medium text-slate-800 dark:text-white">Alternative: TXT-Verifizierung</span>
                    </div>
                    <i class="fas fa-chevron-down text-slate-400"></i>
                </summary>
                <div class="p-5 pt-0 border-t border-slate-200 dark:border-slate-700 mt-0">
                    <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                        Zusätzlich zum A-Record können Sie einen TXT-Record zur Verifizierung hinzufügen:
                    </p>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-slate-500 dark:text-slate-400">
                                    <th class="pb-2 font-medium">Typ</th>
                                    <th class="pb-2 font-medium">Name</th>
                                    <th class="pb-2 font-medium">Wert</th>
                                </tr>
                            </thead>
                            <tbody class="text-slate-800 dark:text-white">
                                <tr>
                                    <td class="py-2"><code class="bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">TXT</code></td>
                                    <td class="py-2"><code class="bg-slate-200 dark:bg-slate-700 px-2 py-1 rounded">_leadbusiness</code></td>
                                    <td class="py-2">
                                        <code class="bg-blue-100 dark:bg-blue-900/30 px-2 py-1 rounded text-blue-700 dark:text-blue-300 text-xs break-all"><?= e($customer['custom_domain_dns_token']) ?></code>
                                        <button onclick="copyToClipboard('<?= e($customer['custom_domain_dns_token']) ?>', this)" class="ml-2 text-slate-400 hover:text-slate-600">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </details>
            
            <!-- Hinweise -->
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
                <p class="text-sm text-blue-800 dark:text-blue-300">
                    <i class="fas fa-clock mr-2"></i>
                    <strong>Hinweis:</strong> DNS-Änderungen können bis zu 24 Stunden dauern. Bei Cloudflare ist es meist innerhalb von Minuten aktiv.
                </p>
            </div>
            
            <!-- Aktionen -->
            <div class="flex flex-wrap gap-3 pt-4 border-t border-slate-200 dark:border-slate-700">
                <form method="POST" class="inline">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="action" value="verify_domain">
                    <button type="submit" class="px-6 py-3 bg-purple-500 text-white rounded-xl hover:bg-purple-600 transition font-medium">
                        <i class="fas fa-check-circle mr-2"></i>Domain jetzt prüfen
                    </button>
                </form>
                
                <form method="POST" class="inline" onsubmit="return confirm('Domain-Konfiguration abbrechen?')">
                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                    <input type="hidden" name="action" value="remove_domain">
                    <button type="submit" class="px-6 py-3 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 transition font-medium">
                        <i class="fas fa-times mr-2"></i>Abbrechen
                    </button>
                </form>
            </div>
        </div>
        
        <?php else: ?>
        <!-- Step 3: Verifiziert -->
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
            
            <?php if ($customer['custom_domain_verified_at']): ?>
            <p class="text-sm text-slate-500 dark:text-slate-400 text-center">
                <i class="fas fa-clock mr-1"></i>
                Verifiziert am <?= date('d.m.Y \u\m H:i', strtotime($customer['custom_domain_verified_at'])) ?> Uhr
            </p>
            <?php endif; ?>
            
            <!-- Info -->
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                <p class="text-sm text-green-800 dark:text-green-300">
                    <i class="fas fa-shield-alt mr-2"></i>
                    <strong>SSL-Zertifikat:</strong> Wird von Ihrem DNS-Anbieter (z.B. Cloudflare) bereitgestellt.
                </p>
            </div>
            
            <!-- Domain entfernen -->
            <div class="pt-6 border-t border-slate-200 dark:border-slate-700">
                <form method="POST" onsubmit="return confirm('Domain-Verknüpfung wirklich entfernen?')">
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
                <span class="font-medium text-slate-800 dark:text-white">Warum wird Cloudflare empfohlen?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                Cloudflare bietet <strong>kostenloses SSL</strong>, ist in wenigen Minuten eingerichtet und bietet zusätzlich DDoS-Schutz und schnellere Ladezeiten durch das CDN. 
                Sie behalten volle Kontrolle über Ihre Domain.
            </div>
        </details>
        
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <span class="font-medium text-slate-800 dark:text-white">Muss ich für SSL bezahlen?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                <strong>Nein!</strong> Mit Cloudflare erhalten Sie ein kostenloses SSL-Zertifikat. 
                Alternativ bieten die meisten Hoster auch kostenlose Let's Encrypt Zertifikate an.
            </div>
        </details>
        
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <span class="font-medium text-slate-800 dark:text-white">Funktioniert die Standard-URL weiterhin?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                Ja! Ihre Standard-URL (<code><?= e($customer['subdomain']) ?>.empfehlungen.cloud</code>) funktioniert weiterhin parallel.
                Bestehende Empfehlungslinks bleiben gültig.
            </div>
        </details>
        
        <details class="group">
            <summary class="flex items-center justify-between cursor-pointer p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <span class="font-medium text-slate-800 dark:text-white">Kann ich auch eine Hauptdomain verwenden?</span>
                <i class="fas fa-chevron-down text-slate-400 group-open:rotate-180 transition-transform"></i>
            </summary>
            <div class="p-4 text-sm text-slate-600 dark:text-slate-400">
                Ja, aber wir empfehlen eine <strong>Subdomain</strong> wie <code>empfehlungen.mustermann.de</code>, 
                damit Ihre Hauptwebsite unabhängig bleibt.
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
