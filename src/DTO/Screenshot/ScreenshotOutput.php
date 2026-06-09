<?php
namespace App\DTO\Screenshot;

class ScreenshotOutput
{
    public int $id;
    public string $filePath;
    public ?string $createdAt;
    public int $assetId;
    public int $timeframeId;
    public int $observationId;
    public ?string $description;
    public ?string $periodStart;
    public ?string $periodEnd;
    public string $source;
}
