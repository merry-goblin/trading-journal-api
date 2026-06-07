<?php

namespace App\DTO\Position;

use App\DTO\AbstractMapper;

class PositionInputMapper extends AbstractMapper implements PositionInputMapperInterface
{
    public function fromArray(array $data): PositionInput
    {
        $dto = new PositionInput();
        $dto->assetId       = $this->intOrEmpty($data['assetId'] ?? null);
        $dto->timeframeId   = $this->intOrEmpty($data['timeframeId'] ?? null);
        $dto->originOrderId = $this->intOrNull($data['originOrderId'] ?? null);
        $dto->openedAt      = $this->stringOrEmpty($data['openedAt'] ?? null);
        $dto->closedAt      = $this->stringOrNull($data['closedAt'] ?? null);
        $dto->direction     = $this->stringOrNull($data['direction'] ?? null);
        $dto->entryPrice    = $this->stringOrEmpty($data['entryPrice'] ?? null);
        $dto->exitPrice     = $this->stringOrNull($data['exitPrice'] ?? null);
        $dto->stopLoss      = $this->stringOrNull($data['stopLoss'] ?? null);
        $dto->takeProfit    = $this->stringOrNull($data['takeProfit'] ?? null);
        $dto->volume        = $this->stringOrEmpty($data['volume'] ?? null);
        $dto->riskAmount    = $this->stringOrNull($data['riskAmount'] ?? null);
        $dto->pnl           = $this->stringOrNull($data['pnl'] ?? null);
        $dto->pnlPercent    = $this->stringOrNull($data['pnlPercent'] ?? null);
        $dto->rr            = $this->stringOrNull($data['rr'] ?? null);
        $dto->comment       = $this->stringOrNull($data['comment'] ?? null);
        return $dto;
    }
}
