<?php

namespace App\DTO\Position;

use Symfony\Component\Validator\Constraints as Assert;

class PositionInput
{
    #[Assert\NotNull] #[Assert\Positive]
    public int $assetId;

    #[Assert\NotNull] #[Assert\Positive]
    public int $timeframeId;

    public ?int $originOrderId = null;

    #[Assert\NotBlank]
    public string $openedAt;

    public ?string $closedAt      = null;
    public ?string $direction     = null;

    #[Assert\NotBlank]
    public string $entryPrice;

    public ?string $exitPrice     = null;
    public ?string $stopLoss      = null;
    public ?string $takeProfit    = null;

    #[Assert\NotBlank]
    public string $volume;

    public ?string $riskAmount    = null;
    public ?string $pnl           = null;
    public ?string $pnlPercent    = null;
    public ?string $rr            = null;
    public ?string $comment       = null;
    public bool    $isBacktest    = false;
}
