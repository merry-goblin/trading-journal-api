<?php

namespace App\DTO\FrontApi\Observation;

use App\DTO\AbstractMapper;

class FrontObservationCreateInputMapper extends AbstractMapper
    implements FrontObservationCreateInputMapperInterface
{
    public function fromArray(array $data): FrontObservationCreateInput
    {
        $dto = new FrontObservationCreateInput();
        $dto->positionId  = $this->intOrNull($data['positionId']  ?? null);
        $dto->orderId     = $this->intOrNull($data['orderId']     ?? null);
        $dto->observedAt  = $this->stringOrEmpty($data['observedAt'] ?? null);
        $dto->trend       = $this->stringOrNull($data['trend']    ?? null);
        $dto->comment     = $this->stringOrNull($data['comment']  ?? null);
        $dto->imageData   = $this->stringOrNull($data['image']['data'] ?? null);
        $dto->imageMime   = $this->stringOrNull($data['image']['mime'] ?? null);
        $dto->periodStart = $this->stringOrNull($data['periodStart'] ?? null);
        $dto->periodEnd   = $this->stringOrNull($data['periodEnd']   ?? null);
        return $dto;
    }
}
