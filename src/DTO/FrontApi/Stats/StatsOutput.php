<?php
namespace App\DTO\FrontApi\Stats;
class StatsOutput
{
    public int $totalTrades = 0;
    public int $winCount = 0;
    public int $lossCount = 0;
    public float $winrate = 0.0;
    public float $totalPnl = 0.0;
    public ?float $avgPnl = null;
    public ?float $avgRr = null;
    public ?int $bestTradeId = null;
    public ?float $bestTradePnl = null;
    public ?int $worstTradeId = null;
    public ?float $worstTradePnl = null;
    public int $maxWinStreak = 0;
    public int $maxLossStreak = 0;
    public ?float $disciplineScore = null;
}
