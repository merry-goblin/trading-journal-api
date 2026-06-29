<?php

namespace App\Domain\Service\Stats;

use App\DTO\FrontApi\Stats\StatsOutput;

interface StatsServiceInterface
{
    /**
     * Filtres supportes : isBacktest, tagId, direction, planRespected, dateFrom, dateTo.
     * isBacktest = null => tous modes.
     */
    public function getGlobalStats(array $filters = []): StatsOutput;
    public function getStatsByTag(array $filters = []): array;
    public function getEquityCurve(array $filters = []): array;

    /**
     * Distribution des R:R realises (signes : positif = gagnant, negatif = perdant).
     * @return array{label:string, min:float, max:float, count:int, wins:int}[]
     */
    public function getRRDistribution(array $filters = []): array;

    /**
     * P&L et winrate par heure d'entree et par jour de semaine.
     * @return array{byHour: array, byWeekday: array}
     */
    public function getTemporalStats(array $filters = []): array;
}
