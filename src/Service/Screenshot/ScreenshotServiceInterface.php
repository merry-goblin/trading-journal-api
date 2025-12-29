<?php

namespace App\Service\Screenshot;

use App\DTO\Screenshot\ScreenshotInput;
use App\Entity\Screenshot;

interface ScreenshotServiceInterface
{
    public function list(): array;

    public function get(int $id): ?Screenshot;

    /**
     * @throws SymbolAlreadyExistsException
     */
    public function create(ScreenshotInput $input): Screenshot;
}
