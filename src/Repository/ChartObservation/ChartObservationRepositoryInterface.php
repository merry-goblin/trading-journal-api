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

    /**
     * Toutes les observations du cycle de vie d'un trade :
     * celles liées à la position ET celles liées à l'ordre d'origine.
     * Si $orderId est null, équivaut à findByPosition().
     */
    public function findByPositionOrOrder(int $positionId, ?int $orderId): array;

    public function findWithFilters(array $filters, int $page, int $limit): array;
}
