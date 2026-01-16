<?php

namespace App\Domain\Service\Screenshot\ScreenshotStorage;

interface ScreenshotStorageInterface
{
    public function store(
        string $key,
        \SplFileObject $file,
        ?string $mimeType = null
    ): void;

    public function read(string $key): \SplFileObject;

    public function delete(string $key): void;
}
