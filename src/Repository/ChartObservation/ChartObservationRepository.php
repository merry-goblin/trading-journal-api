<?php

namespace App\Repository\ChartObservation;

use App\Entity\ChartObservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ChartObservation>
 */
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
}
