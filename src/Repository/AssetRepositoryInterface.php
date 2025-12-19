<?php

namespace App\Repository;

use App\Entity\Asset;
use Doctrine\DBAL\LockMode;

interface AssetRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
    public function findAll(): array;
}
