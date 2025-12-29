<?php

namespace App\Tests\DTO\Timeframe;

use PHPUnit\Framework\TestCase;

use App\DTO\Timeframe\TimeframeInput;
use App\DTO\Timeframe\TimeframeInputMapper;

class TimeframeInputMapperTest extends TestCase
{
    /* fromArray method */

    public function testFromStandardArray(): void
    {
        // Mock data
        $fromArray = $this->createArray('M1', 60);

        // Start test
        $timeframeInputMapper = new TimeframeInputMapper();
        $timeframeInput = $timeframeInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(TimeframeInput::class, $timeframeInput);
        $this->assertSame('M1', $timeframeInput->label);
        $this->assertSame(60, $timeframeInput->seconds);
    }

    public function testFromEmptyArray(): void
    {
        // Mock data
        $fromArray = [];

        // Start test
        $timeframeInputMapper = new TimeframeInputMapper();
        $timeframeInput = $timeframeInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(TimeframeInput::class, $timeframeInput);
        $this->assertSame('', $timeframeInput->label);
        $this->assertSame(0, $timeframeInput->seconds);

    }

    public function testFromArrayWithNullValues(): void
    {
        // Mock data
        $fromArray = $this->createArray(null, null);

        // Start test
        $timeframeInputMapper = new TimeframeInputMapper();
        $timeframeInput = $timeframeInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(TimeframeInput::class, $timeframeInput);
        $this->assertSame('', $timeframeInput->label);
        $this->assertSame(0, $timeframeInput->seconds);
    }

    public function testFromArrayWithWeirdValues(): void
    {
        // Mock data
        $fromArray = $this->createArray(123, ['foo']);
        
        // Start test
        $timeframeInputMapper = new TimeframeInputMapper();
        $timeframeInput = $timeframeInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(TimeframeInput::class, $timeframeInput);
        $this->assertSame('123', $timeframeInput->label);
        $this->assertSame(0, $timeframeInput->seconds);
    }

    /* private methods */

    private function createArray(
        mixed $label,
        mixed $seconds
    ): array {
        return [
            'label' => $label,
            'seconds' => $seconds,
        ];
    }
}
