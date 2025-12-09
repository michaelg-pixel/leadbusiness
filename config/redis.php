<?php
/**
 * Redis-Konfiguration
 * 
 * Für Rate Limiting und Caching.
 * Falls Redis nicht verfügbar ist, wird auf Datenbank-basiertes Rate Limiting zurückgegriffen.
 */

return [
    'enabled' => (bool)(getenv('REDIS_ENABLED') ?: false),
    'host' => getenv('REDIS_HOST') ?: '127.0.0.1',
    'port' => (int)(getenv('REDIS_PORT') ?: 6379),
    'password' => getenv('REDIS_PASSWORD') ?: null,
    'database' => (int)(getenv('REDIS_DB') ?: 0),
    'prefix' => 'leadbusiness:',
    
    // Timeouts
    'timeout' => 2.0,
    'read_timeout' => 2.0,
    
    // Fallback auf Datenbank wenn Redis nicht verfügbar
    'fallback_to_database' => true
];
