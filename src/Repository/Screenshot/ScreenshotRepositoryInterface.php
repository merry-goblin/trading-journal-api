<?php

namespace App\Repository\Screenshot;

use Doctrine\DBAL\LockMode;

interface ScreenshotRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
    public function findAll(): array;
}
