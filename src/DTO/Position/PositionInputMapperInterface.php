<?php

namespace App\DTO\Position;

interface PositionInputMapperInterface
{
    public function fromArray(array $data): PositionInput;
}
