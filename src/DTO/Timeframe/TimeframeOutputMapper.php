<?php

namespace App\DTO\Timeframe;

use App\Entity\Timeframe;

class TimeframeOutputMapper implements TimeframeOutputMapperInterface
{
    public function fromEntity(Timeframe $asset): TimeframeOutput
    {
        $dto = new TimeframeOutput();
        $dto->id = $asset->getId();
        $dto->label = $asset->getLabel();
        $dto->seconds = $asset->getSeconds();

        return $dto;
    }
}
