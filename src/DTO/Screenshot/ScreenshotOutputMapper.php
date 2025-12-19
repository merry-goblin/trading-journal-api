<?php

namespace App\DTO\Screenshot;

use App\Entity\Screenshot;

class ScreenshotOutputMapper
{
    public function fromEntity(Screenshot $screenshot): ScreenshotOutput
    {
        $dto = new ScreenshotOutput();
        $dto->id = $screenshot->getId();
        $dto->filePath = $screenshot->getFilePath();
        $dto->createdAt = $screenshot->getCreatedAt() ? $screenshot->getCreatedAt()->format('Y-m-d H:i:s') : null;
        $dto->assetId = $screenshot->getAsset() ? $screenshot->getAsset()->getId() : null;
        $dto->timeframeId = $screenshot->getTimeframe() ? $screenshot->getTimeframe()->getId() : null;
        $dto->observationId = $screenshot->getObservation() ? $screenshot->getObservation()->getId() : null;
        $dto->positionId = $screenshot->getPosition() ? $screenshot->getPosition()->getId() : null;
        $dto->description = $screenshot->getDescription();
        $dto->periodStart = $screenshot->getPeriodStart() ? $screenshot->getPeriodStart()->format('Y-m-d H:i:s') : null;
        $dto->periodEnd = $screenshot->getPeriodEnd() ? $screenshot->getPeriodEnd()->format('Y-m-d H:i:s') : null;
        $dto->source = $screenshot->getSource();

        return $dto;
    }
}
