<?php
namespace App\DTO\FrontApi\Position;
class FrontPositionDetailOutput
{
    public int $id;
    public ?int $assetId;
    public ?string $assetSymbol;
    public ?int $timeframeId;
    public ?string $timeframeLabel;
    public ?int $originOrderId;
    public ?string $openedAt;
    public ?string $closedAt;
    public ?string $direction;
    public ?string $entryPrice;
    public ?string $exitPrice;
    public ?string $stopLoss;
    public ?string $takeProfit;
    public ?string $volume;
    public ?string $riskAmount;
    public ?string $pnl;
    public ?string $pnlPercent;
    public ?string $rr;
    public ?string $comment;
    // Champs d'analyse generique
    public ?bool $planRespected;
    public ?string $higherTfBias;
    public ?string $entryTfBias;
    public ?int $setupQuality;
    public ?int $emotionScore;
    /** @var FrontTagOutput[] */
    public array $tags = [];
    /** @var FrontObservationOutput[] */
    public array $observations = [];
}
