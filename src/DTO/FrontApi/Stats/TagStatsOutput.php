<?php
namespace App\DTO\FrontApi\Stats;
class TagStatsOutput
{
    public int $tagId;
    public string $tagLabel;
    public string $tagType;
    public int $count = 0;
    public int $winCount = 0;
    public float $winrate = 0.0;
    public float $totalPnl = 0.0;
    public ?float $avgRr = null;
}
