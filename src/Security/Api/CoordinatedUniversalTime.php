<?php

namespace App\Security\Api;

use DateTimeImmutable;
use DateTimeZone;

final class CoordinatedUniversalTime implements ClockInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
