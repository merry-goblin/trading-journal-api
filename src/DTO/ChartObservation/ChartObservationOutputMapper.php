<?php
namespace App\DTO\ChartObservation;
use App\Entity\ChartObservation;

class ChartObservationOutputMapper implements ChartObservationOutputMapperInterface
{
    public function fromEntity(ChartObservation $observation): ChartObservationOutput
    {
        $dto = new ChartObservationOutput();
        $dto->id              = $observation->getId();
        $dto->assetId         = $observation->getAsset()?->getId();
        $dto->timeframeId     = $observation->getTimeframe()?->getId();
        $dto->observedAt      = $observation->getObservedAt()?->format('Y-m-d H:i:s');
        $dto->trend           = $observation->getTrend();
        $dto->comment         = $observation->getComment();
        $dto->orderId         = $observation->getOrder()?->getId();
        $dto->positionId      = $observation->getPosition()?->getId();
        $dto->screenshotCount = $observation->getScreenshots()->count();
        return $dto;
    }
}
