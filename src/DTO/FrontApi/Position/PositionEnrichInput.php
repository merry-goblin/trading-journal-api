<?php

namespace App\DTO\FrontApi\Position;

use Symfony\Component\Validator\Constraints as Assert;

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

    public ?bool $isBacktest = null;
    public bool $hasIsBacktest = false;

    /** @var int[]|null */
    public ?array $tagIds = null;
    public bool $hasTagIds = false;
}
