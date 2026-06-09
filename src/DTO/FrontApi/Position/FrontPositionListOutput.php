<?php
namespace App\DTO\FrontApi\Position;
class FrontPositionListOutput
{
    public int $id;
    public ?string $assetSymbol;
    public ?string $timeframeLabel;
    public ?string $direction;
    public ?string $openedAt;
    public ?string $closedAt;
    public ?string $entryPrice;
    public ?string $exitPrice;
    public ?string $pnl;
    public ?string $rr;
    public ?bool $planRespected;
    public ?int $setupQuality;
    /** @var string[] */
    public array $tagLabels = [];
}
