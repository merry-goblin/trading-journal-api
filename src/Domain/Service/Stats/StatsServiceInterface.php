<?php

namespace App\Domain\Service\Stats;

use App\DTO\FrontApi\Stats\StatsOutput;

interface StatsServiceInterface
{
    public function getGlobalStats(?int $tagId = null, ?bool $isBacktest = false): StatsOutput;
    public function getStatsByTag(): array;
    public function getEquityCurve(?int $tagId = null, ?bool $isBacktest = false): array;
}
