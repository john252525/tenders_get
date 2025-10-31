<?php

namespace TenderService\Database;

use PDO;
use PDOException;
use TenderService\Interfaces\DatabaseInterface;

class Connection implements DatabaseInterface
{
    private PDO $pdo;
    private static ?self $instance = null;

    private function __construct(array $config)
    {
        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
        
        $this->pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    }

    public static function getInstance(array $config): self
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    public function execute(string $sql, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($params);
    }

    public function query(string $sql, array $params = []): array
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}
