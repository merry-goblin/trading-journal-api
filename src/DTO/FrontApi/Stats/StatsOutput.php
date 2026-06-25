<?php

namespace App\DTO\FrontApi\Stats;

class StatsOutput
{
    public int     $totalTrades   = 0;
    public int     $winCount      = 0;
    public int     $lossCount     = 0;
    public float   $winrate       = 0.0;
    public float   $totalPnl      = 0.0;
    public float   $avgPnl        = 0.0;
    public ?float  $avgRr         = null;

    // Gain/perte moyen(ne) par trade
    public ?float  $avgWin        = null;
    public ?float  $avgLoss       = null;

    // Esperance = winrate * avgWin + lossrate * avgLoss
    public ?float  $expectancy    = null;

    // Profit Factor = gain brut / |perte brute|
    public ?float  $profitFactor  = null;

    public int     $maxWinStreak  = 0;
    public int     $maxLossStreak = 0;
    public ?float  $disciplineScore = null;
    public ?int    $bestTradeId   = null;
    public ?float  $bestTradePnl  = null;
    public ?int    $worstTradeId  = null;
    public ?float  $worstTradePnl = null;
}
