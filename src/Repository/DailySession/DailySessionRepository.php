<?php

namespace App\Repository\DailySession;

use App\Entity\DailySession;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<DailySession> */
class DailySessionRepository extends ServiceEntityRepository
    implements DailySessionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DailySession::class);
    }

    public function findByDate(DateTimeImmutable $date): ?DailySession
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.date = :date')
            ->setParameter('date', $date->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function save(DailySession $session): void
    {
        $this->getEntityManager()->persist($session);
        $this->getEntityManager()->flush();
    }
}
