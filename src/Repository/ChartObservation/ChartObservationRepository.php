<?php

namespace App\Repository\ChartObservation;

use App\Entity\ChartObservation;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<ChartObservation> */
class ChartObservationRepository extends ServiceEntityRepository implements ChartObservationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ChartObservation::class);
    }

    public function findByPosition(int $positionId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.position = :posId')
            ->setParameter('posId', $positionId)
            ->orderBy('o.observedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByOrder(int $orderId): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.order = :ordId')
            ->setParameter('ordId', $orderId)
            ->orderBy('o.observedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByPositionOrOrder(int $positionId, ?int $orderId): array
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.observedAt', 'ASC');

        if ($orderId !== null) {
            $qb->andWhere('o.position = :posId OR o.order = :ordId')
               ->setParameter('posId', $positionId)
               ->setParameter('ordId', $orderId);
        } else {
            $qb->andWhere('o.position = :posId')
               ->setParameter('posId', $positionId);
        }

        return $qb->getQuery()->getResult();
    }

    public function findWithFilters(array $filters = [], int $page = 1, int $limit = 20): array
    {
        $qb = $this->createQueryBuilder('o')
            ->orderBy('o.observedAt', 'DESC');

        if (!empty($filters['assetId']))
            $qb->andWhere('o.asset = :assetId')
               ->setParameter('assetId', $filters['assetId']);

        if (!empty($filters['dateFrom']))
            $qb->andWhere('o.observedAt >= :dateFrom')
               ->setParameter('dateFrom', new DateTimeImmutable($filters['dateFrom']));

        if (!empty($filters['dateTo']))
            $qb->andWhere('o.observedAt <= :dateTo')
               ->setParameter('dateTo', new DateTimeImmutable($filters['dateTo'] . ' 23:59:59'));

        if (!empty($filters['trend']))
            $qb->andWhere('o.trend = :trend')
               ->setParameter('trend', $filters['trend']);

        match ($filters['type'] ?? '') {
            'free'     => $qb->andWhere('o.order IS NULL AND o.position IS NULL'),
            'position' => $qb->andWhere('o.position IS NOT NULL'),
            'order'    => $qb->andWhere('o.order IS NOT NULL AND o.position IS NULL'),
            default    => null,
        };

        $qb->setFirstResult(($page - 1) * $limit)
           ->setMaxResults($limit);

        return $qb->getQuery()->getResult();
    }
}
