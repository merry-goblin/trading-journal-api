<?php

namespace App\DTO\Timeframe;

interface TimeframeInputMapperInterface
{
    public function fromArray(array $data): TimeframeInput;
}
