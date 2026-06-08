<?php

namespace App\DTO\Order;

use App\DTO\AbstractMapper;

class OrderInputMapper extends AbstractMapper implements OrderInputMapperInterface
{
    public function fromArray(array $data): OrderInput
    {
        $dto = new OrderInput();
        $dto->assetId     = $this->intOrEmpty($data['assetId'] ?? null);
        $dto->timeframeId = $this->intOrEmpty($data['timeframeId'] ?? null);
        $dto->createdAt   = $this->stringOrEmpty($data['createdAt'] ?? null);
        $dto->orderType   = $this->stringOrEmpty($data['orderType'] ?? null);
        $dto->direction   = $this->stringOrEmpty($data['direction'] ?? null);
        $dto->price       = $this->stringOrNull($data['price'] ?? null);
        $dto->size        = $this->stringOrEmpty($data['size'] ?? null);
        $dto->stopLoss    = $this->stringOrNull($data['stopLoss'] ?? null);
        $dto->takeProfit  = $this->stringOrNull($data['takeProfit'] ?? null);
        $dto->status      = $this->stringOrEmpty($data['status'] ?? 'pending');
        $dto->comment     = $this->stringOrNull($data['comment'] ?? null);
        return $dto;
    }
}
