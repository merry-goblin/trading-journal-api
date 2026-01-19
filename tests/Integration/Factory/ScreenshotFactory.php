<?php

namespace App\Tests\Integration\Factory;

use App\Entity\Asset;
use App\Entity\ChartObservation;
use App\Entity\Position;
use App\Entity\Screenshot;
use App\Entity\Timeframe;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class ScreenshotFactory
{
    public static function create(
        EntityManagerInterface $em,
        string $filePath,
        DateTimeImmutable $createdAt,
        Asset $asset,
        Timeframe $timeframe,
        ?ChartObservation $observation,
        ?Position $position,
        string $description,
        DateTimeImmutable $periodStart,
        DateTimeImmutable $periodEnd,
        string $source
    ): Screenshot {
        $screenshot = new Screenshot();
        $screenshot->setFilePath($filePath);
        $screenshot->setCreatedAt($createdAt);
        $screenshot->setAsset($asset);
        $screenshot->setTimeframe($timeframe);
        $screenshot->setObservation($observation);
        $screenshot->setPosition($position);
        $screenshot->setDescription($description);
        $screenshot->setPeriodStart($periodStart);
        $screenshot->setPeriodEnd($periodEnd);
        $screenshot->setSource($source);

        $em->persist($screenshot);
        $em->flush();

        return $screenshot;
    }
}
