<?php

namespace App\Domain\Service\Screenshot\ScreenshotStorage;

interface ScreenshotStorageInterface
{
    public function store(string $storageKey, string $binaryContent, string $mimeType): void;

    /**
     * Retourne le chemin absolu du fichier sur le disque.
     * Utilise DIRECTORY_SEPARATOR pour la compatibilite Windows/Linux.
     */
    public function getFullPath(string $storageKey): string;
}
