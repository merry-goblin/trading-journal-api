<?php

namespace App\DTO\FrontApi\Observation;

interface FrontObservationCreateInputMapperInterface
{
    public function fromArray(array $data): FrontObservationCreateInput;
}
