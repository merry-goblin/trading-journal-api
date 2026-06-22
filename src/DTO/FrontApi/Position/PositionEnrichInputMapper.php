<?php

namespace App\DTO\FrontApi\Position;

use App\DTO\AbstractMapper;

class PositionEnrichInputMapper extends AbstractMapper implements PositionEnrichInputMapperInterface
{
    public function fromArray(array $data): PositionEnrichInput
    {
        $dto = new PositionEnrichInput();

        if (array_key_exists('planRespected', $data)) {
            $dto->planRespected = $data['planRespected'] !== null ? (bool)$data['planRespected'] : null;
            $dto->hasPlanRespected = true;
        }
        if (array_key_exists('higherTfBias', $data)) {
            $dto->higherTfBias = $this->stringOrNull($data['higherTfBias']);
            $dto->hasHigherTfBias = true;
        }
        if (array_key_exists('entryTfBias', $data)) {
            $dto->entryTfBias = $this->stringOrNull($data['entryTfBias']);
            $dto->hasEntryTfBias = true;
        }
        if (array_key_exists('setupQuality', $data)) {
            $dto->setupQuality = $this->intOrNull($data['setupQuality']);
            $dto->hasSetupQuality = true;
        }
        if (array_key_exists('emotionScore', $data)) {
            $dto->emotionScore = $this->intOrNull($data['emotionScore']);
            $dto->hasEmotionScore = true;
        }
        if (array_key_exists('comment', $data)) {
            $dto->comment = $this->stringOrNull($data['comment']);
            $dto->hasComment = true;
        }
        if (array_key_exists('isBacktest', $data)) {
            $dto->isBacktest = $data['isBacktest'] !== null ? (bool)$data['isBacktest'] : null;
            $dto->hasIsBacktest = true;
        }
        if (array_key_exists('tagIds', $data)) {
            $dto->tagIds = is_array($data['tagIds']) ? array_map('intval', $data['tagIds']) : null;
            $dto->hasTagIds = true;
        }

        return $dto;
    }
}
