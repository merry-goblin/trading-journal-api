<?php
namespace App\DTO\ChartObservation;
use App\Entity\ChartObservation;
interface ChartObservationOutputMapperInterface
{
    public function fromEntity(ChartObservation $observation): ChartObservationOutput;
}
