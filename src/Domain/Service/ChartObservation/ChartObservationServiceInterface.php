<?php
namespace App\Domain\Service\ChartObservation;
use App\DTO\ChartObservation\ChartObservationInput;
use App\Entity\ChartObservation;

interface ChartObservationServiceInterface
{
    public function list(): array;
    public function get(int $id): ChartObservation;
    public function create(ChartObservationInput $input): ChartObservation;
}
