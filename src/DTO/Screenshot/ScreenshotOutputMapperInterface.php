<?php

namespace App\DTO\Screenshot;

use App\Entity\Screenshot;

interface ScreenshotOutputMapperInterface
{
    public function fromEntity(Screenshot $screenshot): ScreenshotOutput;
}
