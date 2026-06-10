<?php

namespace App\Domain\Service\Screenshot\ScreenshotStorage;

interface ScreenshotStorageInterface
{
    public function store(string $storageKey, string $binaryContent, string $mimeType): void;

    /** Retourne le chemin absolu du fichier sur le disque. */
    public function getFullPath(string $storageKey): string;

    /** Retourne le contenu binaire du fichier. */
    public function read(string $storageKey): string;

    /** Supprime le fichier. */
    public function delete(string $storageKey): void;
}
