<?php
namespace App\Domain\Service\Stats;
use App\DTO\FrontApi\Stats\StatsOutput;
interface StatsServiceInterface
{
    public function getGlobalStats(): StatsOutput;
    public function getStatsByTag(): array;
}
