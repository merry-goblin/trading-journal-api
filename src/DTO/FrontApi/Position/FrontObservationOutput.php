<?php
namespace App\DTO\FrontApi\Position;
class FrontObservationOutput
{
    public int $id;
    public ?string $observedAt;
    public ?string $trend;
    public ?string $comment;
    /** @var FrontScreenshotOutput[] */
    public array $screenshots = [];
}
