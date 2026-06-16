<?php

namespace App\Domain\Service\Stats;

use App\DTO\FrontApi\Stats\StatsOutput;

interface StatsServiceInterface
{
    public function getGlobalStats(?int $tagId = null): StatsOutput;
    public function getStatsByTag(): array;
    /** @return array{closedAt:string, date:string, pnl:float, cumulative:float, symbol:string}[] */
    public function getEquityCurve(?int $tagId = null): array;
}
