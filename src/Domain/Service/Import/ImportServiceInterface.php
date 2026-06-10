<?php

namespace App\Domain\Service\Import;

use App\DTO\FrontApi\Import\ImportPositionItem;

interface ImportServiceInterface
{
    /**
     * @param ImportPositionItem[] $items
     * @return array{created: int, errors: string[]}
     */
    public function importPositions(array $items, int $timeframeId): array;
}
