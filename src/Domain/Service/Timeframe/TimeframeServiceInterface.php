<?php

namespace App\Domain\Service\Timeframe;

use App\DTO\Timeframe\TimeframeInput;
use App\Entity\Timeframe;

interface TimeframeServiceInterface
{
    public function list(): array;

    public function get(int $id): ?Timeframe;

    public function getByLabel(string $label): ?Timeframe;

    /**
     * @throws LabelAlreadyExistsException
     */
    public function create(TimeframeInput $input): Timeframe;
}
