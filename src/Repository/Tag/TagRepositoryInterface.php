<?php
namespace App\Repository\Tag;
use Doctrine\DBAL\LockMode;
interface TagRepositoryInterface
{
    public function find(mixed $id, LockMode|int|null $lockMode = null, ?int $lockVersion = null): ?object;
    public function findAll(): array;
    public function findOneBy(array $criteria, array|null $orderBy = null): object|null;
}
