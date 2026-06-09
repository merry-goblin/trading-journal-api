<?php
namespace App\DTO\ChartObservation;
use Symfony\Component\Validator\Constraints as Assert;

class ChartObservationInput
{
    #[Assert\NotNull] #[Assert\Positive]
    public int $assetId;

    #[Assert\NotNull] #[Assert\Positive]
    public int $timeframeId;

    #[Assert\NotBlank]
    public string $observedAt;

    /** bull | bear | neutral — nullable pour les observations automatiques */
    #[Assert\Choice(choices: ['bull', 'bear', 'neutral'], message: 'Valeur attendue : bull, bear ou neutral.')]
    public ?string $trend = null;

    public ?string $comment = null;

    /** FK nullable vers Order */
    #[Assert\Positive]
    public ?int $orderId = null;

    /** FK nullable vers Position */
    #[Assert\Positive]
    public ?int $positionId = null;

    // Image optionnelle inline (si fournie : mime, periodStart et periodEnd obligatoires)
    public ?string $imageData  = null;
    public ?string $imageMime  = null;
    public ?string $periodStart = null;
    public ?string $periodEnd   = null;
}
