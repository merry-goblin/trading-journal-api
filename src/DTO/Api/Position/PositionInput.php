<?php

namespace App\DTO\Api\Position;

class PositionInput
{
    public int     $assetId;
    public int     $timeframeId;
    public string  $openedAt;
    public string  $direction;
    public string  $entryPrice;
    public ?string $stopLoss       = null;
    public ?string $takeProfit     = null;
    public ?string $volume         = null;
    public ?int    $originOrderId  = null;
    public bool    $isBacktest     = false;

    /** @var int[]|null IDs de tags a assigner a la creation */
    public ?array  $tagIds         = null;
}
