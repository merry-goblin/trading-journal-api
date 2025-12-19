<?php

namespace App\DTO\Asset;

class AssetInputMapper
{
    public function fromArray(array $data): AssetInput
    {
        $dto = new AssetInput();

        $dto->symbol = $this->stringOrEmpty($data['symbol'] ?? null);
        $dto->type = $this->stringOrEmpty($data['type'] ?? null);
        $dto->description = $this->stringOrEmpty($data['description'] ?? null);

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
}
