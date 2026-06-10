<?php

namespace App\DTO\FrontApi\Observation;

use Symfony\Component\Validator\Constraints as Assert;

class FrontObservationCreateInput
{
    /** Fournir positionId OU orderId */
    #[Assert\Positive]
    public ?int $positionId = null;

    #[Assert\Positive]
    public ?int $orderId = null;

    #[Assert\NotBlank]
    public string $observedAt;

    #[Assert\Choice(choices: ['bull', 'bear', 'neutral'])]
    public ?string $trend = null;

    public ?string $comment = null;

    /** Image en base64 (optionnel) */
    public ?string $imageData  = null;
    public ?string $imageMime  = null;
    public ?string $periodStart = null;
    public ?string $periodEnd   = null;
}
