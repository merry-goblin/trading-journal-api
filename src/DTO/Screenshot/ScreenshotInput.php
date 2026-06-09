<?php
namespace App\DTO\Screenshot;
use Symfony\Component\Validator\Constraints as Assert;

class ScreenshotInput
{
    public int $id;
    public ?string $createdAt;

    #[Assert\NotNull] #[Assert\Positive]
    public int $assetId;

    #[Assert\NotNull] #[Assert\Positive]
    public int $timeframeId;

    /** Obligatoire : tout screenshot doit etre lie a une observation */
    #[Assert\NotNull] #[Assert\Positive]
    public int $observationId;

    public ?string $description;

    #[Assert\NotBlank]
    public string $periodStart;

    #[Assert\NotBlank]
    public string $periodEnd;

    #[Assert\NotBlank]
    public string $source;

    #[Assert\NotBlank]
    public string $imageData;

    #[Assert\NotBlank]
    public string $imageMime;
}
