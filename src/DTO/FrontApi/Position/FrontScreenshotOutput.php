<?php
namespace App\DTO\FrontApi\Position;
class FrontScreenshotOutput
{
    public int $id;
    public string $filePath;
    public ?string $createdAt;
    public string $source;
    public ?string $periodStart;
    public ?string $periodEnd;
    public ?string $description;
}
