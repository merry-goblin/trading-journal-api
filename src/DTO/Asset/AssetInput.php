<?php

namespace App\DTO\Asset;

use Symfony\Component\Validator\Constraints as Assert;

class AssetInput
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 50)]
    public string $symbol;

    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 50)]
    public string $type;

    #[Assert\Length(min: 0, max: 255)]
    public string $description;
}
