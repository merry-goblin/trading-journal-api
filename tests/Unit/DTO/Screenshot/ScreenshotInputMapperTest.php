<?php

namespace App\Tests\Unit\DTO\Screenshot;

use PHPUnit\Framework\TestCase;

use App\DTO\Screenshot\ScreenshotInput;
use App\DTO\Screenshot\ScreenshotInputMapper;

use TypeError;

class ScreenshotInputMapperTest extends TestCase
{
    /* fromArray method */

    public function testFromArrayWithStandardArray(): void
    {
        // Mock data
        $fromArray = $this->createArray(
            '2025-12-29 00:14:00',
            1,
            1,
            null,
            null,
            '',
            '2025-11-25 00:00:00',
            '2025-12-17 01:58:38',
            'manual'
        );

        // Start test
        $screenshotInputMapper = new ScreenshotInputMapper();
        $screenshotInput = $screenshotInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(ScreenshotInput::class, $screenshotInput);
        $this->assertSame('2025-12-29 00:14:00', $screenshotInput->createdAt);
        $this->assertSame(1, $screenshotInput->assetId);
        $this->assertSame(1, $screenshotInput->timeframeId);
        $this->assertSame(null, $screenshotInput->observationId);
        $this->assertSame(null, $screenshotInput->positionId);
        $this->assertSame('', $screenshotInput->description);
        $this->assertSame('2025-11-25 00:00:00', $screenshotInput->periodStart);
        $this->assertSame('2025-12-17 01:58:38', $screenshotInput->periodEnd);
        $this->assertSame('manual', $screenshotInput->source);
    }

    public function testFromArrayWithEmptyValuesForEmptiableParameters(): void
    {
        // Mock data
        $fromArray = $this->createArray(
            '',
            null,
            null,
            null,
            null,
            '',
            null,
            null,
            ''
        );

        // Start test
        $screenshotInputMapper = new ScreenshotInputMapper();
        $screenshotInput = $screenshotInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(ScreenshotInput::class, $screenshotInput);
        $this->assertSame('', $screenshotInput->createdAt);
        $this->assertSame(0, $screenshotInput->assetId);
        $this->assertSame(0, $screenshotInput->timeframeId);
        $this->assertSame(null, $screenshotInput->observationId);
        $this->assertSame(null, $screenshotInput->positionId);
        $this->assertSame('', $screenshotInput->description);
        $this->assertSame('', $screenshotInput->periodStart);
        $this->assertSame('', $screenshotInput->periodEnd);
        $this->assertSame('', $screenshotInput->source);

    }

    public function testFromArrayWithEmptyArray(): void
    {
        // Mock data
        $fromArray = [];

        // Start test
        $screenshotInputMapper = new ScreenshotInputMapper();
        $screenshotInput = $screenshotInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(ScreenshotInput::class, $screenshotInput);
        $this->assertSame('', $screenshotInput->createdAt);
        $this->assertSame(0, $screenshotInput->assetId);
        $this->assertSame(0, $screenshotInput->timeframeId);
        $this->assertSame(null, $screenshotInput->observationId);
        $this->assertSame(null, $screenshotInput->positionId);
        $this->assertSame('', $screenshotInput->description);
        $this->assertSame('', $screenshotInput->periodStart);
        $this->assertSame('', $screenshotInput->periodEnd);
        $this->assertSame('', $screenshotInput->source);
    }

    /* private methods */

    private function createArray(
        mixed $createdAt,
        mixed $assetId,
        mixed $timeframeId,
        mixed $observationId,
        mixed $positionId,
        mixed $description,
        mixed $periodStart,
        mixed $periodEnd,
        mixed $source
    ): array {
        return [
            'createdAt' => $createdAt,
            'assetId' => $assetId,
            'timeframeId' => $timeframeId,
            'observationId' => $observationId,
            'positionId' => $positionId,
            'description' => $description,
            'periodStart' => $periodStart,
            'periodEnd' => $periodEnd,
            'source' => $source,
        ];
    }
}
