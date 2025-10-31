<?php

namespace TenderService\Interfaces;

interface DatabaseInterface
{
    public function execute(string $sql, array $params = []): bool;
    public function query(string $sql, array $params = []): array;
    public function lastInsertId(): string;
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollBack(): bool;
}
