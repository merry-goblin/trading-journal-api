<?php

namespace App\Domain\Service\Session;

use App\Entity\DailySession;
use DateTimeImmutable;

interface SessionServiceInterface
{
    public function getOrEmpty(DateTimeImmutable $date): array;
    public function update(DateTimeImmutable $date, array $data): array;
}
