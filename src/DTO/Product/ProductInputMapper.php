<?php

namespace App\DTO\Product;

class ProductInputMapper
{
    public function fromArray(array $data): ProductInput
    {
        $dto = new ProductInput();
        $dto->name = $data['name'] ?? null;
        $dto->price = $data['price'] ?? null;
        $dto->description = $data['description'] ?? null;

        return $dto;
    }
}