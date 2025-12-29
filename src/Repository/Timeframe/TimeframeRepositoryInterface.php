<?php

namespace App\Repository\Timeframe;

use Doctrine\DBAL\LockMode;

interface TimeframeRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
    public function findAll(): array;
}
