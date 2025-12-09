<?php
/**
 * Database Klasse
 * 
 * PDO-Wrapper für MySQL-Datenbankoperationen.
 */

namespace Leadbusiness;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private array $config;
    
    /**
     * Private Constructor für Singleton
     */
    private function __construct()
    {
        $this->config = require __DIR__ . '/../config/database.php';
        $this->connect();
    }
    
    /**
     * Singleton-Instanz abrufen
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Datenbankverbindung herstellen
     */
    private function connect(): void
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $this->config['host'],
            $this->config['port'],
            $this->config['database'],
            $this->config['charset']
        );
        
        try {
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (PDOException $e) {
            throw new PDOException('Datenbankverbindung fehlgeschlagen: ' . $e->getMessage());
        }
    }
    
    /**
     * PDO-Instanz abrufen
     */
    public function getPdo(): PDO
    {
        return $this->pdo;
    }
    
    /**
     * SELECT-Query ausführen
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Einzelne Zeile abrufen
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }
    
    /**
     * Alle Zeilen abrufen
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Einzelnen Wert abrufen
     */
    public function fetchColumn(string $sql, array $params = [], int $column = 0)
    {
        return $this->query($sql, $params)->fetchColumn($column);
    }
    
    /**
     * INSERT/UPDATE/DELETE ausführen
     */
    public function execute(string $sql, array $params = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
    
    /**
     * INSERT und letzte ID zurückgeben
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->execute($sql, array_values($data));
        
        return (int) $this->pdo->lastInsertId();
    }
    
    /**
     * UPDATE ausführen
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";
        
        return $this->execute($sql, array_merge(array_values($data), $whereParams));
    }
    
    /**
     * DELETE ausführen
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->execute($sql, $params);
    }
    
    /**
     * Transaktion starten
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }
    
    /**
     * Transaktion bestätigen
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }
    
    /**
     * Transaktion zurückrollen
     */
    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
    
    /**
     * Letzte eingefügte ID
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }
    
    /**
     * String escapen
     */
    public function quote(string $string): string
    {
        return $this->pdo->quote($string);
    }
    
    /**
     * Tabelle existiert?
     */
    public function tableExists(string $table): bool
    {
        $sql = "SHOW TABLES LIKE ?";
        return $this->fetchColumn($sql, [$table]) !== false;
    }
    
    /**
     * Zeile nach ID finden
     */
    public function find(string $table, int $id, string $idColumn = 'id'): ?array
    {
        $sql = "SELECT * FROM {$table} WHERE {$idColumn} = ? LIMIT 1";
        return $this->fetch($sql, [$id]);
    }
    
    /**
     * Zeile nach Bedingung finden
     */
    public function findBy(string $table, array $conditions): ?array
    {
        $where = implode(' = ? AND ', array_keys($conditions)) . ' = ?';
        $sql = "SELECT * FROM {$table} WHERE {$where} LIMIT 1";
        return $this->fetch($sql, array_values($conditions));
    }
    
    /**
     * Alle Zeilen mit Bedingung finden
     * FIXED: Explicit nullable types for PHP 8.4 compatibility
     */
    public function findAllBy(string $table, array $conditions, ?string $orderBy = null, ?int $limit = null): array
    {
        $where = implode(' = ? AND ', array_keys($conditions)) . ' = ?';
        $sql = "SELECT * FROM {$table} WHERE {$where}";
        
        if ($orderBy !== null) {
            $sql .= " ORDER BY {$orderBy}";
        }
        if ($limit !== null) {
            $sql .= " LIMIT {$limit}";
        }
        
        return $this->fetchAll($sql, array_values($conditions));
    }
    
    /**
     * Anzahl Zeilen zählen
     */
    public function count(string $table, array $conditions = []): int
    {
        if (empty($conditions)) {
            $sql = "SELECT COUNT(*) FROM {$table}";
            return (int) $this->fetchColumn($sql);
        }
        
        $where = implode(' = ? AND ', array_keys($conditions)) . ' = ?';
        $sql = "SELECT COUNT(*) FROM {$table} WHERE {$where}";
        return (int) $this->fetchColumn($sql, array_values($conditions));
    }
    
    /**
     * Cloning verhindern (Singleton)
     */
    private function __clone() {}
    
    /**
     * Unserialisierung verhindern (Singleton)
     */
    public function __wakeup(): void
    {
        throw new \Exception("Cannot unserialize singleton");
    }
}
