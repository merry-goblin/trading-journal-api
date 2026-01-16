<?php

namespace App\Security\Api;

use DateTimeImmutable;

interface ClockInterface
{
    public function now(): DateTimeImmutable;
}
