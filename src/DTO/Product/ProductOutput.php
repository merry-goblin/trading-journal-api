<?php

namespace App\DTO\Product;

use App\Entity\Product;

class ProductOutput
{
    public int $id;
    public string $name;
    public float $price;

    public static function fromEntity(Product $p): self
    {
        $dto = new self();
        $dto->id = $p->getId();
        $dto->name = $p->getName();
        $dto->price = $p->getPrice();
        return $dto;
    }
}
