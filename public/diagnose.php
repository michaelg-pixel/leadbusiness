<?php
/**
 * System-Diagnose für Leadbusiness
 * 
 * Prüft alle wichtigen Komponenten
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System-Diagnose | Leadbusiness</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">🔧 Leadbusiness System-Diagnose</h1>
        
        <?php
        $checks = [];
        
        // 1. PHP Version
        $phpVersion = phpversion();
        $checks['PHP Version'] = [
            'status' => version_compare($phpVersion, '8.0', '>='),
            'value' => $phpVersion,
            'expected' => '>= 8.0'
        ];
        
        // 2. Config-Dateien
        $configFiles = ['database.php', 'settings.php', 'mailgun.php', 'digistore.php'];
        foreach ($configFiles as $file) {
            $path = __DIR__ . '/../config/' . $file;
            $checks["Config: {$file}"] = [
                'status' => file_exists($path),
                'value' => file_exists($path) ? '✓ Existiert' : '✗ Fehlt',
                'expected' => 'Existiert'
            ];
        }
        
        // 3. Settings laden und prüfen
        $settingsPath = __DIR__ . '/../config/settings.php';
        if (file_exists($settingsPath)) {
            require_once $settingsPath;
            global $settings;
            
            $checks['Settings: $settings Variable'] = [
                'status' => isset($settings) && is_array($settings),
                'value' => isset($settings) ? 'Definiert (' . count($settings) . ' Keys)' : 'NICHT definiert',
                'expected' => 'Definiert'
            ];
            
            $checks['Settings: Industries'] = [
                'status' => isset($settings['industries']) && count($settings['industries']) > 0,
                'value' => isset($settings['industries']) ? count($settings['industries']) . ' Branchen' : 'FEHLT',
                'expected' => '11 Branchen'
            ];
            
            // Prüfen ob Industries Icons haben
            if (isset($settings['industries'])) {
                $firstIndustry = reset($settings['industries']);
                $hasIcons = is_array($firstIndustry) && isset($firstIndustry['icon']);
                $checks['Settings: Industry Icons'] = [
                    'status' => $hasIcons,
                    'value' => $hasIcons ? 'Mit Icons' : 'Ohne Icons (alte Version!)',
                    'expected' => 'Mit Icons'
                ];
            }
        }
        
        // 4. Datenbank-Verbindung
        try {
            require_once __DIR__ . '/../config/database.php';
            require_once __DIR__ . '/../includes/Database.php';
            
            // Mit korrektem Namespace aufrufen
            $db = \Leadbusiness\Database::getInstance();
            
            $testQuery = $db->fetchColumn("SELECT COUNT(*) FROM background_images");
            $checks['Datenbank: Verbindung'] = [
                'status' => true,
                'value' => '✓ Verbunden',
                'expected' => 'Verbunden'
            ];
            $checks['Datenbank: Background Images'] = [
                'status' => $testQuery > 0,
                'value' => $testQuery . ' Einträge',
                'expected' => '33 Einträge'
            ];
            
            // Prüfe Filename-Format
            $sampleBg = $db->fetch("SELECT * FROM background_images LIMIT 1");
            $filenameCorrect = $sampleBg && !str_contains($sampleBg['filename'], '/');
            $checks['Datenbank: Filename Format'] = [
                'status' => $filenameCorrect,
                'value' => $sampleBg ? $sampleBg['filename'] : 'N/A',
                'expected' => 'bg-X.jpg (ohne Pfad)'
            ];
            
            // Kunden zählen
            $customerCount = $db->fetchColumn("SELECT COUNT(*) FROM customers");
            $checks['Datenbank: Kunden'] = [
                'status' => true,
                'value' => $customerCount . ' Kunden',
                'expected' => '-'
            ];
            
        } catch (Exception $e) {
            $checks['Datenbank: Verbindung'] = [
                'status' => false,
                'value' => $e->getMessage(),
                'expected' => 'Verbunden'
            ];
        }
        
        // 5. Wichtige Verzeichnisse
        $dirs = [
            'assets/backgrounds' => __DIR__ . '/assets/backgrounds',
            'uploads/logos' => __DIR__ . '/uploads/logos',
            'uploads/backgrounds' => __DIR__ . '/uploads/backgrounds',
        ];
        
        foreach ($dirs as $name => $path) {
            $exists = is_dir($path);
            $writable = $exists && is_writable($path);
            $checks["Verzeichnis: {$name}"] = [
                'status' => $exists,
                'value' => $exists ? ($writable ? '✓ Existiert & beschreibbar' : '⚠️ Existiert, nicht beschreibbar') : '✗ Fehlt',
                'expected' => 'Existiert & beschreibbar'
            ];
        }
        
        // 6. Hintergrundbilder prüfen
        $bgBasePath = __DIR__ . '/assets/backgrounds/';
        $industries = ['zahnarzt', 'friseur', 'handwerker', 'coach', 'restaurant', 'fitness', 'onlineshop', 'onlinemarketing', 'newsletter', 'software', 'allgemein'];
        $missingImages = [];
        $existingImages = 0;
        
        foreach ($industries as $industry) {
            for ($i = 1; $i <= 3; $i++) {
                $imgPath = $bgBasePath . $industry . '/bg-' . $i . '.jpg';
                if (file_exists($imgPath)) {
                    $existingImages++;
                } else {
                    $missingImages[] = "{$industry}/bg-{$i}.jpg";
                }
            }
        }
        
        $checks['Hintergrundbilder'] = [
            'status' => $existingImages === 33,
            'value' => "{$existingImages}/33 vorhanden" . (count($missingImages) > 0 ? " (" . count($missingImages) . " fehlen)" : ""),
            'expected' => '33/33'
        ];
        
        // 7. API-Endpunkte
        $apiFiles = [
            'api/check-subdomain.php' => __DIR__ . '/api/check-subdomain.php',
        ];
        
        foreach ($apiFiles as $name => $path) {
            $checks["API: {$name}"] = [
                'status' => file_exists($path),
                'value' => file_exists($path) ? '✓ Existiert' : '✗ Fehlt',
                'expected' => 'Existiert'
            ];
        }
        
        // 8. Onboarding-Dateien
        $onboardingFiles = [
            'onboarding/index.php' => __DIR__ . '/onboarding/index.php',
            'onboarding/process.php' => __DIR__ . '/onboarding/process.php',
        ];
        
        foreach ($onboardingFiles as $name => $path) {
            if (file_exists($path)) {
                $content = file_get_contents($path);
                // Prüfen ob korrigierte Pfade verwendet werden
                $hasCorrectPaths = str_contains($content, '/../../config/');
                $checks["Onboarding: {$name}"] = [
                    'status' => $hasCorrectPaths,
                    'value' => $hasCorrectPaths ? '✓ Korrekte Pfade' : '⚠️ Alte Pfade (/../config/)',
                    'expected' => 'Korrekte Pfade'
                ];
            } else {
                $checks["Onboarding: {$name}"] = [
                    'status' => false,
                    'value' => '✗ Fehlt',
                    'expected' => 'Existiert'
                ];
            }
        }
        
        // 9. JavaScript-Datei prüfen
        $jsPath = __DIR__ . '/assets/js/onboarding.js';
        if (file_exists($jsPath)) {
            $jsContent = file_get_contents($jsPath);
            $hasTouchSupport = str_contains($jsContent, 'touchend');
            $checks['JavaScript: onboarding.js'] = [
                'status' => $hasTouchSupport,
                'value' => $hasTouchSupport ? '✓ Mit Touch-Support' : '⚠️ Ohne Touch-Support (alte Version)',
                'expected' => 'Mit Touch-Support'
            ];
        }
        
        // Ausgabe
        $passedCount = count(array_filter($checks, fn($c) => $c['status']));
        $totalCount = count($checks);
        $allPassed = $passedCount === $totalCount;
        ?>
        
        <div class="mb-6 p-4 rounded-lg <?= $allPassed ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
            <strong><?= $passedCount ?>/<?= $totalCount ?> Checks bestanden</strong>
            <?php if (!$allPassed): ?>
                <p class="mt-2">Einige Probleme müssen behoben werden. Siehe Details unten.</p>
            <?php endif; ?>
        </div>
        
        <table class="w-full bg-white rounded-lg shadow overflow-hidden">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Check</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Aktuell</th>
                    <th class="px-4 py-3 text-left">Erwartet</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                <?php foreach ($checks as $name => $check): ?>
                <tr class="<?= $check['status'] ? '' : 'bg-red-50' ?>">
                    <td class="px-4 py-3 font-medium"><?= htmlspecialchars($name) ?></td>
                    <td class="px-4 py-3">
                        <?php if ($check['status']): ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                ✓ OK
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                ✗ Fehler
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-sm"><?= htmlspecialchars($check['value']) ?></td>
                    <td class="px-4 py-3 text-sm text-gray-500"><?= htmlspecialchars($check['expected']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <?php if (count($missingImages) > 0 && count($missingImages) <= 33): ?>
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">🖼️ Fehlende Hintergrundbilder (<?= count($missingImages) ?>)</h2>
            <div class="grid grid-cols-3 gap-2 text-sm">
                <?php foreach ($missingImages as $img): ?>
                <div class="bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($img) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="mt-8 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">🛠️ Server-Befehle zum Beheben</h2>
            <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm">
# 1. Git Pull (wichtigster Schritt!)
cd /home/empfehlungen/htdocs/www.empfehlungen.cloud
git checkout -- .
git pull origin main

# 2. Verzeichnisse erstellen (falls nicht vorhanden)
mkdir -p public/assets/backgrounds/{zahnarzt,friseur,handwerker,coach,restaurant,fitness,onlineshop,onlinemarketing,newsletter,software,allgemein}
mkdir -p public/uploads/{logos,backgrounds}

# 3. Berechtigungen setzen
chmod -R 755 public/assets/backgrounds
chmod -R 755 public/uploads
            </pre>
        </div>
        
        <div class="mt-8 bg-blue-50 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4 text-blue-800">📋 Test-Links</h2>
            <ul class="space-y-2">
                <li><a href="/onboarding/" class="text-blue-600 hover:underline">→ Onboarding Wizard</a></li>
                <li><a href="/dashboard/login.php" class="text-blue-600 hover:underline">→ Kunden-Login</a></li>
                <li><a href="/admin/login.php" class="text-blue-600 hover:underline">→ Admin-Login</a></li>
                <li><a href="/api/check-subdomain.php?subdomain=test123" class="text-blue-600 hover:underline">→ Subdomain-API Test</a></li>
            </ul>
        </div>
        
        <p class="mt-8 text-center text-gray-500">
            Diagnose erstellt: <?= date('d.m.Y H:i:s') ?>
        </p>
    </div>
</body>
</html>
