<?php
/**
 * Leadbusiness - Initialisierung
 * 
 * Diese Datei wird von allen PHP-Dateien eingebunden.
 * Sie lädt alle notwendigen Klassen und stellt globale Funktionen bereit.
 */

// Fehlerreporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Pfade bestimmen
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('PUBLIC_PATH', ROOT_PATH . '/public');

// Konfigurationen laden
require_once CONFIG_PATH . '/database.php';
require_once CONFIG_PATH . '/settings.php';

// Klassen laden
require_once INCLUDES_PATH . '/Database.php';
require_once INCLUDES_PATH . '/helpers.php';

/**
 * Datenbank-Instanz abrufen (Shortcut)
 * Verwendet den vollständigen Namespace
 */
function db(): \Leadbusiness\Database {
    return \Leadbusiness\Database::getInstance();
}

// Session starten falls noch nicht geschehen
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRF Token generieren falls nicht vorhanden
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Globale Settings verfügbar machen
global $settings;
