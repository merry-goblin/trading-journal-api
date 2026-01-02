<?php

namespace App\DTO\Timeframe;

use Symfony\Component\Validator\Constraints as Assert;

class TimeframeInput
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 50)]
    public string $label;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $seconds;
}
