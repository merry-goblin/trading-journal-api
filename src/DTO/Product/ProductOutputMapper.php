<?php

namespace App\DTO\Product;

use App\Entity\Product;

class ProductOutputMapper
{
    public function fromEntity(Product $product): ProductOutput
    {
        $dto = new ProductOutput();
        $dto->id = $product->getId();
        $dto->name = $product->getName();
        $dto->price = $product->getPrice();

        return $dto;
    }
}
