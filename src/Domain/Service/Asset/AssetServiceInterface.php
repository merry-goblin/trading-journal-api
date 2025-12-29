<?php

namespace App\Domain\Service\Asset;

use App\DTO\Asset\AssetInput;
use App\Entity\Asset;

interface AssetServiceInterface
{
    public function list(): array;

    public function get(int $id): ?Asset;

    public function getBySymbol(string $symbol): ?Asset;

    /**
     * @throws SymbolAlreadyExistsException
     */
    public function create(AssetInput $input): Asset;
}
