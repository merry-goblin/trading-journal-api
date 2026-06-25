<?php

namespace App\Repository\Position;

use App\Entity\Position;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Position> */
class PositionRepository extends ServiceEntityRepository implements PositionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Position::class);
    }

    public function findByKey(
        string $symbol,
        string $direction,
        string $openedAt,
        string $entryPrice
    ): ?Position {
        return $this->createQueryBuilder('p')
            ->join('p.asset', 'a')
            ->andWhere('a.symbol = :symbol')
            ->andWhere('p.direction = :direction')
            ->andWhere('p.openedAt = :openedAt')
            ->andWhere('p.entryPrice = :entryPrice')
            ->setParameter('symbol',     $symbol)
            ->setParameter('direction',  $direction)
            ->setParameter('openedAt',   new DateTimeImmutable($openedAt))
            ->setParameter('entryPrice', $entryPrice)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function existsByKey(
        string $symbol,
        string $direction,
        string $openedAt,
        string $entryPrice
    ): bool {
        return $this->findByKey($symbol, $direction, $openedAt, $entryPrice) !== null;
    }

    public function findClosed(?bool $isBacktest = false): array
    {
        $qb = $this->createQueryBuilder('p')
            ->andWhere('p.closedAt IS NOT NULL')
            ->andWhere('p.pnl IS NOT NULL');
        if ($isBacktest !== null)
            $qb->andWhere('p.isBacktest = :isBt')->setParameter('isBt', $isBacktest);
        return $qb->orderBy('p.closedAt', 'ASC')->getQuery()->getResult();
    }

    public function findClosedByTag(int $tagId, ?bool $isBacktest = false): array
    {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.tags', 't')
            ->andWhere('t.id = :tagId')
            ->andWhere('p.closedAt IS NOT NULL')
            ->andWhere('p.pnl IS NOT NULL')
            ->setParameter('tagId', $tagId);
        if ($isBacktest !== null)
            $qb->andWhere('p.isBacktest = :isBt')->setParameter('isBt', $isBacktest);
        return $qb->orderBy('p.closedAt', 'ASC')->getQuery()->getResult();
    }

    public function findWithFilters(array $filters = [], int $page = 1, int $limit = 20): array
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($filters['assetId']))
            $qb->andWhere('p.asset = :assetId')->setParameter('assetId', $filters['assetId']);
        if (!empty($filters['direction']))
            $qb->andWhere('p.direction = :dir')->setParameter('dir', $filters['direction']);
        if (!empty($filters['dateFrom']))
            $qb->andWhere('p.openedAt >= :dateFrom')
               ->setParameter('dateFrom', new DateTimeImmutable($filters['dateFrom']));
        if (!empty($filters['dateTo']))
            $qb->andWhere('p.openedAt <= :dateTo')
               ->setParameter('dateTo', new DateTimeImmutable($filters['dateTo']));
        if (array_key_exists('planRespected', $filters) && $filters['planRespected'] !== null)
            $qb->andWhere('p.planRespected = :pr')
               ->setParameter('pr', (bool)$filters['planRespected']);
        if (!empty($filters['tagId']))
            $qb->innerJoin('p.tags', 't')
               ->andWhere('t.id = :tagId')
               ->setParameter('tagId', $filters['tagId']);
        if (array_key_exists('isBacktest', $filters) && $filters['isBacktest'] !== null)
            $qb->andWhere('p.isBacktest = :isBt')
               ->setParameter('isBt', (bool)$filters['isBacktest']);

        $qb->orderBy('p.openedAt', 'DESC')
           ->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
