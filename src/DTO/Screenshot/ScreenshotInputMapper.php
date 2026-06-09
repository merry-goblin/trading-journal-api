<?php
namespace App\DTO\Screenshot;
use App\DTO\AbstractMapper;

class ScreenshotInputMapper extends AbstractMapper implements ScreenshotInputMapperInterface
{
    public function fromArray(array $data): ScreenshotInput
    {
        $dto = new ScreenshotInput();
        $dto->createdAt    = $data['createdAt'] ?? '';
        $dto->assetId      = $this->intOrEmpty($data['assetId'] ?? null);
        $dto->timeframeId  = $this->intOrEmpty($data['timeframeId'] ?? null);
        $dto->observationId= $this->intOrEmpty($data['observationId'] ?? null);
        $dto->description  = $this->stringOrNull($data['description'] ?? null);
        $dto->periodStart  = $this->stringOrEmpty($data['periodStart'] ?? null);
        $dto->periodEnd    = $this->stringOrEmpty($data['periodEnd'] ?? null);
        $dto->source       = $this->stringOrEmpty($data['source'] ?? '');
        $dto->imageData    = $this->stringOrEmpty($data['image']['data'] ?? '');
        $dto->imageMime    = $this->stringOrEmpty($data['image']['mime'] ?? '');
        return $dto;
    }
}
