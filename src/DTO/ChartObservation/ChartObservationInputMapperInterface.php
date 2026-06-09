<?php
namespace App\DTO\ChartObservation;
interface ChartObservationInputMapperInterface
{
    public function fromArray(array $data): ChartObservationInput;
}
