<?php

namespace App\DTO\Timeframe;

use App\Entity\Timeframe;

interface TimeframeOutputMapperInterface
{
    public function fromEntity(Timeframe $asset): TimeframeOutput;
}
