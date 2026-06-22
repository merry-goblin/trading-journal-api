<?php

namespace App\Repository\Position;

use Doctrine\DBAL\LockMode;

interface PositionRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
    public function findAll(): array;
    public function findClosed(?bool $isBacktest = false): array;
    public function findClosedByTag(int $tagId, ?bool $isBacktest = false): array;
    public function existsByKey(string $symbol, string $direction, string $openedAt, string $entryPrice): bool;
    public function findWithFilters(array $filters, int $page, int $limit): array;
}
