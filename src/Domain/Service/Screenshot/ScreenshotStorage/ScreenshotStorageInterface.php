<?php

namespace App\Domain\Service\Screenshot\ScreenshotStorage;

interface ScreenshotStorageInterface
{
    public function store(
        string $key,
        string $binaryContent,
        ?string $mimeType = null
    ): void;

    public function read(string $key): string;

    public function delete(string $key): void;
}
