<?php

namespace App\DTO\Screenshot;

use App\Entity\Screenshot;

class ScreenshotOutputMapper implements ScreenshotOutputMapperInterface
{
    public function fromEntity(Screenshot $screenshot): ScreenshotOutput
    {
        $dto = new ScreenshotOutput();
        $dto->id = $screenshot->getId();
        $dto->filePath = $screenshot->getFilePath();
        $dto->createdAt = $screenshot->getCreatedAt()?->format('Y-m-d H:i:s');
        $dto->assetId = $screenshot->getAsset()?->getId();
        $dto->timeframeId = $screenshot->getTimeframe()?->getId();
        $dto->observationId = $screenshot->getObservation()?->getId();
        $dto->positionId = $screenshot->getPosition()?->getId();
        $dto->description = $screenshot->getDescription();
        $dto->periodStart = $screenshot->getPeriodStart()?->format('Y-m-d H:i:s');
        $dto->periodEnd = $screenshot->getPeriodEnd()?->format('Y-m-d H:i:s');
        $dto->source = $screenshot->getSource();

        return $dto;
    }
}
