<?php

namespace App\Domain\Service\Screenshot\ScreenshotStorage;

use RuntimeException;

class LocalScreenshotStorage implements ScreenshotStorageInterface
{
    public function __construct(
        private readonly string $basePath
    ) {}

    public function store(
        string $key,
        string $binaryContent,
        ?string $mimeType = null
    ): void {
        $path = $this->basePath . '/' . $key;
        $dir = dirname($path);

        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Unable to create directory: ' . $dir);
        }

        if (file_put_contents($path, $binaryContent) === false) {
            throw new RuntimeException('Unable to write file: ' . $path);
        }
    }

    public function read(string $key): string
    {
        $path = $this->basePath . '/' . $key;

        if (!is_file($path)) {
            throw new RuntimeException('Screenshot not found: ' . $key);
        }

        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException('Unable to read file: ' . $path);
        }

        return $content;
    }

    public function delete(string $key): void
    {
        $path = $this->basePath . '/' . $key;

        if (is_file($path)) {
            unlink($path);
        }
    }
}
