<?php
namespace App\Repository\Position;
use Doctrine\DBAL\LockMode;
interface PositionRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
    public function findAll(): array;
    /** Positions fermées (closedAt non null) triées par date desc */
    public function findClosed(): array;
    /** Liste filtrée et paginée pour Vue.js */
    public function findWithFilters(array $filters, int $page, int $limit): array;
}
