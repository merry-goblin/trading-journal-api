<?php

namespace App\DTO\Asset;

use App\Entity\Asset;

interface AssetOutputMapperInterface
{
    public function fromEntity(Asset $asset): AssetOutput;
}
