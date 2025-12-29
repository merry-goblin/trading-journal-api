<?php

namespace App\DTO\Asset;

use App\DTO\AbstractMapper;

class AssetInputMapper extends AbstractMapper implements AssetInputMapperInterface
{
    public function fromArray(array $data): AssetInput
    {
        $dto = new AssetInput();

        $dto->symbol = $this->stringOrEmpty($data['symbol'] ?? null);
        $dto->type = $this->stringOrEmpty($data['type'] ?? null);
        $dto->description = $this->stringOrEmpty($data['description'] ?? null);

        return $dto;
    }
}
