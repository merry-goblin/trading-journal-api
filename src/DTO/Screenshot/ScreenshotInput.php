<?php

namespace App\DTO\Screenshot;

use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

class ScreenshotInput
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    public string $filePath;

    #[Assert\NotNull]
    public string $createdAt;

    #[Assert\NotNull]
    #[Assert\Positive]
    public float $assetId;

    #[Assert\NotNull]
    #[Assert\Positive]
    public float $timeframeId;

    #[Assert\Positive]
    public float $observationId;

    #[Assert\Positive]
    public float $positionId;

    public string $description;

    #[Assert\NotNull]
    public string $periodStart;

    #[Assert\NotNull]
    public string $periodEnd;
    
    #[Assert\NotBlank]
    public string $source;

}
