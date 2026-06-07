<?php

namespace App\DTO\Order;

class OrderOutput
{
    public int $id;
    public int $assetId;
    public int $timeframeId;
    public string $createdAt;
    public string $orderType;
    public string $direction;
    public ?string $price;
    public ?string $stopPrice;
    public string $size;
    public ?string $stopLoss;
    public ?string $takeProfit;
    public string $status;
    public ?string $comment;
}
