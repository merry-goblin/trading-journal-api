<?php

namespace App\Tests\DTO\Screenshot;

use PHPUnit\Framework\TestCase;

use App\DTO\Screenshot\ScreenshotInput;
use App\DTO\Screenshot\ScreenshotInputMapper;

class ScreenshotInputMapperTest extends TestCase
{
    /* fromArray method */

    public function testFromStandardArray(): void
    {
        // Mock data
        $fromArray = $this->createArray(
            'C:\Users\kelle\AppData\Roaming\MetaQuotes\Terminal\D0E8209F77C8CF37AD8BF550E51FF075\MQL5\Files\EURUSD_H4_2025-12-17_01-58-38.png',
            '2025-12-29 00:14:00',
            1,
            1,
            null,
            null,
            '',
            '2025-11-25 00:00:00',
            '2025-12-17 01:58:38',
            ''
        );

        // Start test
        $screenshotInputMapper = new ScreenshotInputMapper();
        $screenshotInput = $screenshotInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(ScreenshotInput::class, $screenshotInput);
        $this->assertSame('C:\Users\kelle\AppData\Roaming\MetaQuotes\Terminal\D0E8209F77C8CF37AD8BF550E51FF075\MQL5\Files\EURUSD_H4_2025-12-17_01-58-38.png', $screenshotInput->filePath);
    }

    /*public function testFromEmptyArray(): void
    {
        // Mock data
        $fromArray = [];

        // Start test
        $screenshotInputMapper = new ScreenshotInputMapper();
        $screenshotInput = $screenshotInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(ScreenshotInput::class, $screenshotInput);
        $this->assertSame('', $screenshotInput->symbol);
        $this->assertSame('', $screenshotInput->type);
        $this->assertSame('', $screenshotInput->description);

    }

    public function testFromArrayWithNullValues(): void
    {
        // Mock data
        $fromArray = $this->createArray(null, null, null);

        // Start test
        $screenshotInputMapper = new ScreenshotInputMapper();
        $screenshotInput = $screenshotInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(ScreenshotInput::class, $screenshotInput);
        $this->assertSame('', $screenshotInput->symbol);
        $this->assertSame('', $screenshotInput->type);
        $this->assertSame('', $screenshotInput->description);
    }

    public function testFromArrayWithWeirdValues(): void
    {
        // Mock data
        $fromArray = $this->createArray(123, true, ['foo']);
        
        // Start test
        $screenshotInputMapper = new ScreenshotInputMapper();
        $screenshotInput = $screenshotInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(ScreenshotInput::class, $screenshotInput);
        $this->assertSame('123', $screenshotInput->symbol);
        $this->assertSame('1', $screenshotInput->type);
        $this->assertSame('', $screenshotInput->description);
    }*/

    /* private methods */

    private function createArray(
        mixed $filePath,
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
            'filePath' => $filePath,
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
