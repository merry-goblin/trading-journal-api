<?php

namespace App\DTO\Screenshot;

use App\DTO\AbstractMapper;

class ScreenshotInputMapper extends AbstractMapper
{
    public function fromArray(array $data): ScreenshotInput
    {
        $dto = new ScreenshotInput();
        $dto->filePath = $this->stringOrEmpty($data['filePath'] ?? '');
        $dto->createdAt = $data['createdAt'] ?? '';
        $dto->assetId = $this->intOrEmpty($data['assetId'] ?? null);
        $dto->timeframeId = $this->intOrEmpty($data['timeframeId'] ?? null);
        $dto->observationId = $this->intOrNull($data['observationId'] ?? null);
        $dto->positionId = $this->intOrNull($data['positionId'] ?? null);
        $dto->description = $this->stringOrEmpty($data['description'] ?? '');
        $dto->periodStart = $this->stringOrEmpty($data['periodStart'] ?? null);
        $dto->periodEnd = $this->stringOrEmpty($data['periodEnd'] ?? null);
        $dto->source = $this->stringOrEmpty($data['source'] ?? '');

        return $dto;
    }
}
