<?php
namespace App\DTO\FrontApi\Position;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Tous les champs sont optionnels.
 * Les champs presents dans le payload sont appliques (y compris null = effacer).
 * Les champs absents sont ignores grace aux flags has*.
 */
class PositionEnrichInput
{
    public ?bool $planRespected = null;
    public bool $hasPlanRespected = false;

    #[Assert\Choice(choices: ['bull', 'bear', 'neutral'])]
    public ?string $higherTfBias = null;
    public bool $hasHigherTfBias = false;

    #[Assert\Choice(choices: ['bull', 'bear', 'neutral'])]
    public ?string $entryTfBias = null;
    public bool $hasEntryTfBias = false;

    #[Assert\Range(min: 1, max: 5)]
    public ?int $setupQuality = null;
    public bool $hasSetupQuality = false;

    #[Assert\Range(min: 0, max: 5)]
    public ?int $emotionScore = null;
    public bool $hasEmotionScore = false;

    public ?string $comment = null;
    public bool $hasComment = false;

    /** @var int[]|null — si present, remplace tous les tags existants */
    public ?array $tagIds = null;
    public bool $hasTagIds = false;
}
