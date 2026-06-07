<?php

namespace App\DTO\Position;

use App\Entity\Position;

class PositionOutputMapper implements PositionOutputMapperInterface
{
    public function fromEntity(Position $position): PositionOutput
    {
        $dto = new PositionOutput();
        $dto->id            = $position->getId();
        $dto->assetId       = $position->getAsset()?->getId();
        $dto->timeframeId   = $position->getTimeframe()?->getId();
        $dto->originOrderId = $position->getOriginOrder()?->getId();
        $dto->openedAt      = $position->getOpenedAt()?->format('Y-m-d H:i:s');
        $dto->closedAt      = $position->getClosedAt()?->format('Y-m-d H:i:s');
        $dto->direction     = $position->getDirection();
        $dto->entryPrice    = $position->getEntryPrice();
        $dto->exitPrice     = $position->getExitPrice();
        $dto->stopLoss      = $position->getStopLoss();
        $dto->takeProfit    = $position->getTakeProfit();
        $dto->volume        = $position->getVolume();
        $dto->riskAmount    = $position->getRiskAmount();
        $dto->pnl           = $position->getPnl();
        $dto->pnlPercent    = $position->getPnlPercent();
        $dto->rr            = $position->getRr();
        $dto->comment       = $position->getComment();
        $dto->planRespected = $position->isPlanRespected();
        $dto->higherTfBias  = $position->getHigherTfBias();
        $dto->entryTfBias   = $position->getEntryTfBias();
        $dto->setupQuality  = $position->getSetupQuality();
        $dto->emotionScore  = $position->getEmotionScore();
        return $dto;
    }
}
