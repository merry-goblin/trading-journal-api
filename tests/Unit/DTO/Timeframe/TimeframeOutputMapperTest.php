<?php

namespace App\Tests\Unit\DTO\Timeframe;

use App\Entity\Timeframe;
use PHPUnit\Framework\TestCase;

use App\DTO\Timeframe\TimeframeOutput;
use App\DTO\Timeframe\TimeframeOutputMapper;

class TimeframeOutputMapperTest extends TestCase
{
    /* fromEntity method */

    public function testFromEntityWithStandardEntity(): void
    {
        // Mock data
        $entity = $this->createTimeframe(1, 'M1', 60);

        // Start test
        $timeframeOutputMapper = new TimeframeOutputMapper();
        $timeframeOutput = $timeframeOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(TimeframeOutput::class, $timeframeOutput);
        $this->assertSame(1, $timeframeOutput->id);
        $this->assertSame('M1', $timeframeOutput->label);
        $this->assertSame(60, $timeframeOutput->seconds);
    }

    public function testFromEntityWithEmptyEntity(): void
    {
        // Mock data
        $entity = $this->createTimeframe(0, '', 0);

        // Start test
        $timeframeOutputMapper = new TimeframeOutputMapper();
        $timeframeOutput = $timeframeOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(TimeframeOutput::class, $timeframeOutput);
        $this->assertSame(0, $timeframeOutput->id);
        $this->assertSame('', $timeframeOutput->label);
        $this->assertSame(0, $timeframeOutput->seconds);

    }

    public function testFromEntityWithWeirdValues(): void
    {
        // Mock data
        $entity = $this->createTimeframe(-123, true, 0);

        // Start test
        $timeframeOutputMapper = new TimeframeOutputMapper();
        $timeframeOutput = $timeframeOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(TimeframeOutput::class, $timeframeOutput);
        $this->assertSame(-123, $timeframeOutput->id);
        $this->assertSame('1', $timeframeOutput->label);
        $this->assertSame(0, $timeframeOutput->seconds);
    }

    /* private methods */

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
}
