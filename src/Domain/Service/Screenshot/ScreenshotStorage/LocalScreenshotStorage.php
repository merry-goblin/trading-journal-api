<?php

namespace App\Domain\Service\Screenshot\ScreenshotStorage;

use RuntimeException;

class LocalScreenshotStorage implements ScreenshotStorageInterface
{
    public function __construct(private string $basePath) {}

    public function store(string $storageKey, string $binaryContent, string $mimeType): void
    {
        $fullPath = $this->getFullPath($storageKey);
        $dir = dirname($fullPath);

        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException('Impossible de créer le dossier : ' . $dir);
        }

        if (file_put_contents($fullPath, $binaryContent) === false) {
            throw new RuntimeException('Impossible d\'écrire le fichier : ' . $fullPath);
        }
    }

    public function getFullPath(string $storageKey): string
    {
        $normalised = str_replace(['/', '\\\\'], DIRECTORY_SEPARATOR, $storageKey);
        return rtrim($this->basePath, '/\\\\') . DIRECTORY_SEPARATOR . $normalised;
    }

    public function read(string $storageKey): string
    {
        $fullPath = $this->getFullPath($storageKey);

        if (!file_exists($fullPath)) {
            throw new RuntimeException('Fichier introuvable : ' . $fullPath);
        }

        $content = file_get_contents($fullPath);

        if ($content === false) {
            throw new RuntimeException('Impossible de lire le fichier : ' . $fullPath);
        }

        return $content;
    }

    public function delete(string $storageKey): void
    {
        $fullPath = $this->getFullPath($storageKey);

        if (!file_exists($fullPath)) {
            return;
        }

        if (!unlink($fullPath)) {
            throw new RuntimeException('Impossible de supprimer le fichier : ' . $fullPath);
        }
    }
}
