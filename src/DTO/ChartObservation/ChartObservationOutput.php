<?php
namespace App\DTO\ChartObservation;

class ChartObservationOutput
{
    public int $id;
    public int $assetId;
    public int $timeframeId;
    public string $observedAt;
    public ?string $trend;
    public ?string $comment;
    public ?int $orderId;
    public ?int $positionId;
    public int $screenshotCount;
}
