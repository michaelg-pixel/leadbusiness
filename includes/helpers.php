<?php
/**
 * Helper-Funktionen
 * 
 * Allgemeine Hilfsfunktionen für das gesamte Projekt.
 */

/**
 * Konfiguration laden
 */
function config(string $key, $default = null)
{
    static $configs = [];
    
    $parts = explode('.', $key);
    $file = $parts[0];
    
    if (!isset($configs[$file])) {
        $path = __DIR__ . "/../config/{$file}.php";
        if (file_exists($path)) {
            $configs[$file] = require $path;
        } else {
            return $default;
        }
    }
    
    $value = $configs[$file];
    
    for ($i = 1; $i < count($parts); $i++) {
        if (!is_array($value) || !isset($value[$parts[$i]])) {
            return $default;
        }
        $value = $value[$parts[$i]];
    }
    
    return $value;
}

/**
 * Datenbank-Instanz abrufen
 * Hinweis: Wird nur definiert wenn nicht bereits in init.php definiert
 */
if (!function_exists('db')) {
    function db(): \Leadbusiness\Database
    {
        return \Leadbusiness\Database::getInstance();
    }
}

/**
 * System-Setting abrufen
 */
function setting(string $key, $default = null)
{
    static $settings = null;
    
    if ($settings === null) {
        $rows = db()->fetchAll("SELECT setting_key, setting_value, setting_type FROM system_settings");
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = match ($row['setting_type']) {
                'boolean' => (bool) $row['setting_value'],
                'integer' => (int) $row['setting_value'],
                'json' => json_decode($row['setting_value'], true),
                default => $row['setting_value']
            };
        }
    }
    
    return $settings[$key] ?? $default;
}

/**
 * URL generieren
 */
function url(string $path = ''): string
{
    $baseUrl = config('settings.site_url', '');
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Asset-URL generieren
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Redirect
 */
function redirect(string $url, int $status = 302): void
{
    header("Location: {$url}", true, $status);
    exit;
}

/**
 * JSON-Response
 */
function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Erfolgs-JSON
 */
function jsonSuccess(array $data = [], string $message = 'Erfolgreich'): void
{
    jsonResponse(['success' => true, 'message' => $message, 'data' => $data]);
}

/**
 * Fehler-JSON
 */
function jsonError(string $message, int $status = 400, array $errors = []): void
{
    jsonResponse(['success' => false, 'message' => $message, 'errors' => $errors], $status);
}

/**
 * String escapen für HTML
 */
function e(?string $string): string
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Input aus Request abrufen
 */
function input(string $key, $default = null)
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Input sanitizen
 */
function sanitize(?string $value): string
{
    if ($value === null) return '';
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

/**
 * Alle Inputs abrufen
 */
function allInput(): array
{
    return array_merge($_GET, $_POST);
}

/**
 * Request ist POST?
 */
function isPost(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Request ist AJAX?
 */
function isAjax(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * IP-Adresse hashen
 */
function hashIp(string $ip): string
{
    return hash('sha256', $ip . config('security.ip_salt', 'leadbusiness'));
}

/**
 * Client-IP abrufen
 */
function getClientIp(): string
{
    $headers = [
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR'
    ];
    
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            $ip = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return $ip;
            }
        }
    }
    
    return '0.0.0.0';
}

/**
 * Referral-Code generieren
 */
function generateReferralCode(int $length = null): string
{
    $length = $length ?? config('settings.referral_code.length', 8);
    $charset = config('settings.referral_code.charset', 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789');
    $prefix = config('settings.referral_code.prefix', '');
    
    $code = '';
    $charsetLength = strlen($charset);
    
    for ($i = 0; $i < $length; $i++) {
        $code .= $charset[random_int(0, $charsetLength - 1)];
    }
    
    return $prefix . $code;
}

/**
 * Eindeutigen Referral-Code generieren
 */
function generateUniqueReferralCode(): string
{
    $maxAttempts = 10;
    $attempt = 0;
    
    do {
        $code = generateReferralCode();
        $exists = db()->fetchColumn("SELECT COUNT(*) FROM leads WHERE referral_code = ?", [$code]);
        $attempt++;
    } while ($exists && $attempt < $maxAttempts);
    
    if ($exists) {
        throw new Exception('Konnte keinen eindeutigen Referral-Code generieren');
    }
    
    return $code;
}

/**
 * Zufälliges Token generieren
 */
function generateToken(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

/**
 * Subdomain aus Host extrahieren
 */
function getSubdomain(): ?string
{
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $baseDomain = config('settings.subdomain.base_domain', 'empfehlungen.cloud');
    
    if (str_ends_with($host, '.' . $baseDomain)) {
        return substr($host, 0, -(strlen($baseDomain) + 1));
    }
    
    return null;
}

/**
 * Subdomain validieren
 */
function validateSubdomain(string $subdomain): array
{
    $errors = [];
    $settings = config('settings.subdomain');
    
    if (strlen($subdomain) < ($settings['min_length'] ?? 3)) {
        $errors[] = "Subdomain muss mindestens 3 Zeichen lang sein";
    }
    
    if (strlen($subdomain) > ($settings['max_length'] ?? 50)) {
        $errors[] = "Subdomain darf maximal 50 Zeichen lang sein";
    }
    
    if (!preg_match('/^[a-z0-9][a-z0-9-]*[a-z0-9]$/', $subdomain) && strlen($subdomain) > 2) {
        $errors[] = "Subdomain darf nur Kleinbuchstaben, Zahlen und Bindestriche enthalten";
    }
    
    $reserved = $settings['reserved'] ?? ['www', 'admin', 'api', 'app', 'dashboard', 'mail', 'smtp', 'ftp', 'test', 'dev', 'staging'];
    if (in_array($subdomain, $reserved)) {
        $errors[] = "Diese Subdomain ist reserviert";
    }
    
    if (empty($errors)) {
        $exists = db()->fetchColumn("SELECT COUNT(*) FROM customers WHERE subdomain = ?", [$subdomain]);
        if ($exists) {
            $errors[] = "Diese Subdomain ist bereits vergeben";
        }
    }
    
    return $errors;
}

/**
 * E-Mail validieren
 */
function validateEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Slug generieren
 */
function slugify(string $text, string $divider = '-'): string
{
    $text = str_replace(
        ['ä', 'ö', 'ü', 'ß', 'Ä', 'Ö', 'Ü'],
        ['ae', 'oe', 'ue', 'ss', 'ae', 'oe', 'ue'],
        $text
    );
    
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, $divider);
    $text = preg_replace('~-+~', $divider, $text);
    
    return strtolower($text);
}

/**
 * Datum formatieren
 */
function formatDate(?string $date, string $format = 'd.m.Y'): string
{
    return $date ? date($format, strtotime($date)) : '';
}

/**
 * Datum und Zeit formatieren
 */
function formatDateTime(?string $date, string $format = 'd.m.Y H:i'): string
{
    return $date ? date($format, strtotime($date)) : '';
}

/**
 * Relative Zeit formatieren
 */
function timeAgo(?string $datetime): string
{
    if (!$datetime) return '';
    
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'gerade eben';
    if ($diff < 3600) {
        $minutes = floor($diff / 60);
        return "vor {$minutes} " . ($minutes === 1 ? 'Minute' : 'Minuten');
    }
    if ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "vor {$hours} " . ($hours === 1 ? 'Stunde' : 'Stunden');
    }
    if ($diff < 604800) {
        $days = floor($diff / 86400);
        return "vor {$days} " . ($days === 1 ? 'Tag' : 'Tagen');
    }
    if ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return "vor {$weeks} " . ($weeks === 1 ? 'Woche' : 'Wochen');
    }
    
    return formatDate($datetime);
}

/**
 * Zahl formatieren
 */
function formatNumber(int|float $number, int $decimals = 0): string
{
    return number_format($number, $decimals, ',', '.');
}

/**
 * Währung formatieren
 */
function formatCurrency(float $amount, string $currency = 'EUR'): string
{
    return formatNumber($amount, 2) . ' €';
}

/**
 * Prozent formatieren
 */
function formatPercent(float $value, int $decimals = 1): string
{
    return formatNumber($value, $decimals) . ' %';
}

/**
 * Dateigröße formatieren
 */
function formatFileSize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, 2) . ' ' . $units[$i];
}

/**
 * Name anonymisieren für Leaderboard
 */
function anonymizeName(string $name): string
{
    $parts = explode(' ', trim($name));
    
    if (count($parts) >= 2) {
        return $parts[0] . ' ' . mb_substr($parts[count($parts) - 1], 0, 1) . '.';
    }
    
    return mb_substr($name, 0, 3) . '...';
}

/**
 * Debug-Log
 */
function debug_log(mixed $data, string $label = ''): void
{
    if (!config('settings.debug')) return;
    
    $logFile = __DIR__ . '/../logs/debug.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) mkdir($logDir, 0755, true);
    
    $timestamp = date('Y-m-d H:i:s');
    $message = $label ? "[{$label}] " : '';
    $message .= is_string($data) ? $data : print_r($data, true);
    
    file_put_contents($logFile, "[{$timestamp}] {$message}\n", FILE_APPEND);
}

/**
 * Flash-Nachricht setzen
 */
function flash(string $type, string $message): void
{
    if (!isset($_SESSION['flash'])) $_SESSION['flash'] = [];
    $_SESSION['flash'][$type] = $message;
}

/**
 * Flash-Nachrichten abrufen und löschen
 */
function getFlash(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Plan-Features abrufen
 */
function getPlanFeatures(string $plan): array
{
    return config("settings.plans.{$plan}.features", []);
}

/**
 * Branche-Name abrufen
 */
function getIndustryName(string $key): string
{
    return config("settings.industries.{$key}", $key);
}

/**
 * Share-Plattform Info abrufen
 */
function getSharePlatform(string $key): array
{
    return config("settings.share_platforms.{$key}", [
        'name' => $key,
        'icon' => 'fas fa-share',
        'color' => '#6B7280'
    ]);
}
