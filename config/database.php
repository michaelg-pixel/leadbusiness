<?php
/**
 * Datenbank-Konfiguration
 * 
 * Diese Datei enthält die Verbindungsdaten zur MySQL-Datenbank.
 */

return [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'port' => getenv('DB_PORT') ?: 3306,
    'database' => getenv('DB_NAME') ?: 'empfehlungen',
    'username' => getenv('DB_USER') ?: 'empfehlungen',
    'password' => getenv('DB_PASS') ?: 'g6nhXr2qcYQcqKoXhSnv',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]
];
