<?php

namespace App\Domain\Service\Screenshot\ScreenshotStorage;

use RuntimeException;

class LocalScreenshotStorage implements ScreenshotStorageInterface
{
    public function __construct(private string $basePath) {}

    public function store(string $storageKey, string $binaryContent, string $mimeType): void
    {
        $fullPath = $this->getFullPath($storageKey);
        $dir      = dirname($fullPath);

        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Impossible de créer le dossier : ' . $dir);
        }

        if (file_put_contents($fullPath, $binaryContent) === false) {
            throw new RuntimeException('Impossible d\'écrire le fichier : ' . $fullPath);
        }
    }

    public function getFullPath(string $storageKey): string
    {
        // Normalise les séparateurs (clés stockées avec /  sur Windows comme sur Linux)
        $normalised = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $storageKey);
        return rtrim($this->basePath, '/\\') . DIRECTORY_SEPARATOR . $normalised;
    }
}
