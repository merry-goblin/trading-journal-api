<?php

namespace App\DTO\Position;

class PositionOutput
{
    public int $id;
    public int $assetId;
    public int $timeframeId;
    public ?int $originOrderId;
    public string $openedAt;
    public ?string $closedAt;
    public ?string $direction;
    public string $entryPrice;
    public ?string $exitPrice;
    public ?string $stopLoss;
    public ?string $takeProfit;
    public string $volume;
    public ?string $riskAmount;
    public ?string $pnl;
    public ?string $pnlPercent;
    public ?string $rr;
    public ?string $comment;
    // Champs d'analyse generique (renseignes via Vue.js)
    public ?bool   $planRespected  = null;
    public ?string $higherTfBias   = null;
    public ?string $entryTfBias    = null;
    public ?int    $setupQuality   = null;
    public ?int    $emotionScore   = null;
}
