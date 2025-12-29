<?php

namespace App\DTO\Screenshot;

use Symfony\Component\Validator\Constraints as Assert;

class ScreenshotInput
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $filePath;

    public ?string $createdAt;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $assetId;

    #[Assert\NotNull]
    #[Assert\Positive]
    public int $timeframeId;

    #[Assert\Positive]
    public ?int $observationId;

    #[Assert\Positive]
    public ?int $positionId;

    public string $description;

    #[Assert\NotNull]
    public string $periodStart;

    #[Assert\NotNull]
    public string $periodEnd;

    #[Assert\NotBlank]
    public string $source; // manual, auto, import

}
