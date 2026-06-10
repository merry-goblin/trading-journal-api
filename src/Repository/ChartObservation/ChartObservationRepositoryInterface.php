<?php

namespace App\Repository\ChartObservation;

use Doctrine\DBAL\LockMode;

interface ChartObservationRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
    public function findAll(): array;

    /** Observations liees a une position (order_id ou position_id) */
    public function findByPosition(int $positionId): array;

    /** Observations liees a un ordre */
    public function findByOrder(int $orderId): array;

    /** Liste filtree et paginee pour Vue.js */
    public function findWithFilters(array $filters, int $page, int $limit): array;
}
