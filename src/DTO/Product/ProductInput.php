<?php

namespace App\DTO\Product;

use Symfony\Component\Validator\Constraints as Assert;

class ProductInput
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 255)]
    public string $name;

    #[Assert\NotNull]
    #[Assert\Positive]
    public float $price;

    public string $description;

    public static function fromArray(array $data): self
    {
        $input = new self();
        $input->name = $data['name'] ?? null;
        $input->price = $data['price'] ?? null;
        $input->description = $data['description'] ?? null;
        return $input;
    }
}
