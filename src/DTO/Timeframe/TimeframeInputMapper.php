<?php

namespace App\DTO\Timeframe;

use App\DTO\AbstractMapper;

class TimeframeInputMapper extends AbstractMapper implements TimeframeInputMapperInterface
{
    public function fromArray(array $data): TimeframeInput
    {
        $dto = new TimeframeInput();

        $dto->label = $this->stringOrEmpty($data['label'] ?? null);
        $dto->seconds = $this->intOrEmpty($data['seconds'] ?? null);

        return $dto;
    }
}
