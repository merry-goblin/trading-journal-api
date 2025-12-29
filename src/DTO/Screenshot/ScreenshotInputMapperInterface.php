<?php

namespace App\DTO\Screenshot;

interface ScreenshotInputMapperInterface
{
    public function fromArray(array $data): ScreenshotInput;
}
