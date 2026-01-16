<?php

namespace App\Tests\Unit\DTO\Screenshot;

use App\Entity\Asset;
use App\Entity\ChartObservation;
use App\Entity\Position;
use App\Entity\Screenshot;
use App\Entity\Timeframe;
use PHPUnit\Framework\TestCase;

use App\DTO\Screenshot\ScreenshotOutput;
use App\DTO\Screenshot\ScreenshotOutputMapper;

use DateTimeImmutable;

class ScreenshotOutputMapperTest extends TestCase
{
    /* fromEntity method */

    public function testFromEntityWithStandardEntity(): void
    {
        // Mock data
        $asset = $this->createAsset(1, 'EURUSD', 'forex', 'Euro vs US Dollar');
        $timeframe = $this->createTimeframe(1, 'M1', 60);
        $screenshot = $this->createScreenshot(
            1,
            'C:\Users\kelle\AppData\Roaming\MetaQuotes\Terminal\D0E8209F77C8CF37AD8BF550E51FF075\MQL5\Files\EURUSD_H4_2025-12-17_01-58-38.png',
            new DateTimeImmutable('2025-12-29 00:14:00'),
            $asset,
            $timeframe,
            null,
            null,
            '',
            new DateTimeImmutable('2025-11-25 00:00:00'),
            new DateTimeImmutable('2025-12-17 01:58:38'),
            'manual'
        );

        // Start test
        $screenshotOutputMapper = new ScreenshotOutputMapper();
        $screenshotOutput = $screenshotOutputMapper->fromEntity($screenshot);

        // Assertions
        $this->assertInstanceOf(ScreenshotOutput::class, $screenshotOutput);
        $this->assertSame(1, $screenshotOutput->id);
        $this->assertSame('C:\Users\kelle\AppData\Roaming\MetaQuotes\Terminal\D0E8209F77C8CF37AD8BF550E51FF075\MQL5\Files\EURUSD_H4_2025-12-17_01-58-38.png', $screenshotOutput->filePath);
        $this->assertSame('2025-12-29 00:14:00', $screenshotOutput->createdAt);
        $this->assertSame(1, $screenshotOutput->assetId);
        $this->assertSame(1, $screenshotOutput->timeframeId);
        $this->assertSame(null, $screenshotOutput->observationId);
        $this->assertSame(null, $screenshotOutput->positionId);
        $this->assertSame('', $screenshotOutput->description);
        $this->assertSame('2025-11-25 00:00:00', $screenshotOutput->periodStart);
        $this->assertSame('2025-12-17 01:58:38', $screenshotOutput->periodEnd);
        $this->assertSame('manual', $screenshotOutput->source);
    }

    public function testFromEntityWithEmptyEntity(): void
    {
        // Mock data
        $asset = $this->createAsset(1, 'EURUSD', 'forex', 'Euro vs US Dollar');
        $timeframe = $this->createTimeframe(1, 'M1', 60);
        $screenshot = $this->createScreenshot(
            1,
            '',
            new DateTimeImmutable('2026-01-01 00:00:00'),
            $asset,
            $timeframe,
            null,
            null,
            '',
            new DateTimeImmutable('2026-01-01 00:00:00'),
            new DateTimeImmutable('2026-01-01 00:00:00'),
            ''
        );

        // Start test
        $screenshotOutputMapper = new ScreenshotOutputMapper();
        $screenshotOutput = $screenshotOutputMapper->fromEntity($screenshot);

        // Assertions
        $this->assertInstanceOf(ScreenshotOutput::class, $screenshotOutput);
        $this->assertSame(1, $screenshotOutput->id);
        $this->assertSame('', $screenshotOutput->filePath);
        $this->assertSame('2026-01-01 00:00:00', $screenshotOutput->createdAt);
        $this->assertSame(1, $screenshotOutput->assetId);
        $this->assertSame(1, $screenshotOutput->timeframeId);
        $this->assertSame(null, $screenshotOutput->observationId);
        $this->assertSame(null, $screenshotOutput->positionId);
        $this->assertSame('', $screenshotOutput->description);
        $this->assertSame('2026-01-01 00:00:00', $screenshotOutput->periodStart);
        $this->assertSame('2026-01-01 00:00:00', $screenshotOutput->periodEnd);
        $this->assertSame('', $screenshotOutput->source);
    }

    /* private methods */

    private function createAsset(
        int $id,
        string $symbol,
        ?string $type,
        ?string $description
    ): Asset {
        $asset = new Asset();
        $asset->setId($id);
        $asset->setSymbol($symbol);
        $asset->setType($type);
        $asset->setDescription($description);

        return $asset;
    }

    private function createTimeframe(
        int $id,
        string $label,
        int $seconds
    ): Timeframe {
        $timeframe = new Timeframe();
        $timeframe->setId($id);
        $timeframe->setLabel($label);
        $timeframe->setSeconds($seconds);

        return $timeframe;
    }

    private function createScreenshot(
        int $id,
        string $filePath,
        DateTimeImmutable $createdAt,
        Asset $asset,
        Timeframe $timeframe,
        ?ChartObservation $observation,
        ?Position $position,
        ?string $description,
        DateTimeImmutable $periodStart,
        DateTimeImmutable $periodEnd,
        string $source
    ): Screenshot {
        $screenshot = new Screenshot();
        $screenshot->setId($id);
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

        return $screenshot;
    }
}
