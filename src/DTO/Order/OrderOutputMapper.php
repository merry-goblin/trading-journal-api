<?php

namespace App\DTO\Order;

use App\Entity\Order;

class OrderOutputMapper implements OrderOutputMapperInterface
{
    public function fromEntity(Order $order): OrderOutput
    {
        $dto = new OrderOutput();
        $dto->id          = $order->getId();
        $dto->assetId     = $order->getAsset()?->getId();
        $dto->timeframeId = $order->getTimeframe()?->getId();
        $dto->createdAt   = $order->getCreatedAt()?->format('Y-m-d H:i:s');
        $dto->orderType   = $order->getOrderType();
        $dto->direction   = $order->getDirection();
        $dto->price       = $order->getPrice();
        $dto->size        = $order->getSize();
        $dto->stopLoss    = $order->getStopLoss();
        $dto->takeProfit  = $order->getTakeProfit();
        $dto->status      = $order->getStatus();
        $dto->comment     = $order->getComment();
        return $dto;
    }
}
