<?php

namespace App\DTO\Screenshot;

class ScreenshotInputMapper
{
    public function fromArray(array $data): ScreenshotInput
    {
        $dto = new ScreenshotInput();
        $dto->filePath = $data['filePath'] ?? '';
        $dto->createdAt = $data['createdAt'] ?? null;
        $dto->assetId = $data['assetId'] ?? null;
        $dto->timeframeId = $data['timeframeId'] ?? null;
        $dto->observationId = $data['observationId'] ?? null;
        $dto->positionId = $data['positionId'] ?? null;
        $dto->description = $data['description'] ?? '';
        $dto->periodStart = $data['periodStart'] ?? null;
        $dto->periodEnd = $data['periodEnd'] ?? null;
        $dto->source = $data['source'] ?? '';

        return $dto;
    }
}