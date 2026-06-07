<?php

namespace App\DTO\Position;

use Symfony\Component\Validator\Constraints as Assert;

class PositionInput
{
    public int $id;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $assetId;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $timeframeId;

    #[Assert\Positive]
    public ?int $originOrderId = null;

    #[Assert\NotBlank]
    public string $openedAt;    // format Y.m.d H:i:s

    public ?string $closedAt    = null;

    #[Assert\Choice(choices: ['long', 'short'])]
    public ?string $direction   = null;

    #[Assert\NotBlank]
    public string $entryPrice;

    public ?string $exitPrice   = null;
    public ?string $stopLoss    = null;
    public ?string $takeProfit  = null;

    #[Assert\NotBlank]
    public string $volume;

    public ?string $riskAmount  = null;
    public ?string $pnl         = null;
    public ?string $pnlPercent  = null;
    public ?string $rr          = null;
    public ?string $comment     = null;
}
