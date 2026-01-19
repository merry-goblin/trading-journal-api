<?php

namespace App\Domain\Service\Screenshot\ScreenshotStorage;

use SplFileObject;
use RuntimeException;

class LocalScreenshotStorage implements ScreenshotStorageInterface
{
    public function __construct(
        private readonly string $basePath // ex: /var/data/screenshots
    ) {}

    public function store(
        string $key,
        SplFileObject $file,
        ?string $mimeType = null
    ): void {
        $destinationPath = $this->basePath . '/' . $key;
        $directory = dirname($destinationPath);

        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new RuntimeException('Unable to create directory: ' . $directory);
        }

        $destination = new SplFileObject($destinationPath, 'wb');

        $file->rewind();
        while (!$file->eof()) {
            $destination->fwrite($file->fread(8192));
        }
    }

    public function read(string $key): SplFileObject
    {
        $path = $this->basePath . '/' . $key;

        if (!is_file($path)) {
            throw new RuntimeException('Screenshot not found: ' . $key);
        }

        return new SplFileObject($path, 'rb');
    }

    public function delete(string $key): void
    {
        $path = $this->basePath . '/' . $key;

        if (is_file($path)) {
            unlink($path);
        }
    }
}
