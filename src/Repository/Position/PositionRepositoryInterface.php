<?php

namespace App\Repository\Position;

use Doctrine\DBAL\LockMode;

interface PositionRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
    public function findAll(): array;
    /** Positions fermees (closedAt non null) triees par closedAt ASC */
    public function findClosed(): array;
    /** Positions fermees avec un tag specifique, triees par closedAt ASC */
    public function findClosedByTag(int $tagId): array;
    /** Liste filtree et paginee pour Vue.js */
    public function findWithFilters(array $filters, int $page, int $limit): array;
}
