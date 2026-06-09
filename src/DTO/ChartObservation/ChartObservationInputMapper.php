<?php
namespace App\DTO\ChartObservation;
use App\DTO\AbstractMapper;

class ChartObservationInputMapper extends AbstractMapper implements ChartObservationInputMapperInterface
{
    public function fromArray(array $data): ChartObservationInput
    {
        $dto = new ChartObservationInput();
        $dto->assetId     = $this->intOrEmpty($data['assetId'] ?? null);
        $dto->timeframeId = $this->intOrEmpty($data['timeframeId'] ?? null);
        $dto->observedAt  = $this->stringOrEmpty($data['observedAt'] ?? null);
        $dto->trend       = $this->stringOrNull($data['trend'] ?? null);
        $dto->comment     = $this->stringOrNull($data['comment'] ?? null);
        $dto->orderId     = $this->intOrNull($data['orderId'] ?? null);
        $dto->positionId  = $this->intOrNull($data['positionId'] ?? null);
        $dto->imageData   = $this->stringOrNull($data['image']['data'] ?? null);
        $dto->imageMime   = $this->stringOrNull($data['image']['mime'] ?? null);
        $dto->periodStart = $this->stringOrNull($data['periodStart'] ?? null);
        $dto->periodEnd   = $this->stringOrNull($data['periodEnd'] ?? null);
        return $dto;
    }
}
