<?php

namespace App\DTO\Position;

use App\Entity\Position;

interface PositionOutputMapperInterface
{
    public function fromEntity(Position $position): PositionOutput;
}
