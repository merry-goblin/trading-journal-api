<?php

namespace App\Repository\DailySession;

use App\Entity\DailySession;
use DateTimeImmutable;

interface DailySessionRepositoryInterface
{
    public function findByDate(DateTimeImmutable $date): ?DailySession;
    public function save(DailySession $session): void;
}
