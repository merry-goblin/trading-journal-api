<?php

namespace App\DTO\Order;

use Symfony\Component\Validator\Constraints as Assert;

class OrderInput
{
    public int $id;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $assetId;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $timeframeId;

    #[Assert\NotBlank]
    public string $createdAt; // format Y.m.d H:i:s (depuis MT5)

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['limit', 'stop', 'market'])]
    public string $orderType;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['long', 'short'])]
    public string $direction;

    public ?string $price      = null;
    public ?string $stopPrice  = null;

    #[Assert\NotBlank]
    public string $size;

    public ?string $stopLoss   = null;
    public ?string $takeProfit = null;

    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['pending', 'filled', 'cancelled'])]
    public string $status;

    public ?string $comment    = null;
}
