<?php

namespace App\Repository\ChartObservation;

use Doctrine\DBAL\LockMode;

interface ChartObservationRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
    public function findAll(): array;
    public function findByPosition(int $positionId): array;
    public function findByOrder(int $orderId): array;
}
