<?php

namespace App\Domain\Service\Stats;

use App\DTO\FrontApi\Stats\StatsOutput;

interface StatsServiceInterface
{
    public function getGlobalStats(?int $tagId = null, ?bool $isBacktest = false): StatsOutput;
    public function getStatsByTag(): array;
    public function getEquityCurve(?int $tagId = null, ?bool $isBacktest = false): array;

    /**
     * Distribution des R:R realises (signes : positif = gagnant, negatif = perdant).
     * @return array{label:string, min:float, max:float, count:int, wins:int}[]
     */
    public function getRRDistribution(?int $tagId = null, ?bool $isBacktest = false): array;

    /**
     * P&L et winrate par heure d'entree et par jour de semaine.
     * @return array{byHour: array, byWeekday: array}
     */
    public function getTemporalStats(?bool $isBacktest = false): array;
}
