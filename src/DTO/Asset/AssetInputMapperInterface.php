<?php

namespace App\DTO\Asset;

interface AssetInputMapperInterface
{
    public function fromArray(array $data): AssetInput;
}
