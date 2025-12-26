<?php

namespace App\DTO\Timeframe;

class TimeframeInputMapper implements TimeframeInputMapperInterface
{
    public function fromArray(array $data): TimeframeInput
    {
        $dto = new TimeframeInput();

        $dto->label = $this->stringOrEmpty($data['label'] ?? null);
        $dto->seconds = $this->intOrEmpty($data['seconds'] ?? null);

        return $dto;
    }

    private function stringOrEmpty(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_scalar($value)) {
            return trim((string) $value);
        }

        // arrays, objects, etc.
        return '';
    }

    private function intOrEmpty(mixed $value): int
    {
        if ($value === null) {
            return 0;
        }

        if (is_scalar($value)) {
            return intval(trim((string) $value));
        }

        // arrays, objects, etc.
        return 0;
    }
}
