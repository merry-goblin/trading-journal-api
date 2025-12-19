<?php

namespace App\DTO\Asset;

use App\Entity\Asset;

class AssetOutputMapper implements AssetOutputMapperInterface
{
    public function fromEntity(Asset $asset): AssetOutput
    {
        $dto = new AssetOutput();
        $dto->id = $asset->getId();
        $dto->symbol = $asset->getSymbol();
        $dto->type = $asset->getType();
        $dto->description = $asset->getDescription();

        return $dto;
    }
}
