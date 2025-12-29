<?php

namespace App\Tests\Unit\DTO\Asset;

use PHPUnit\Framework\TestCase;

use App\DTO\Asset\AssetInput;
use App\DTO\Asset\AssetInputMapper;

class AssetInputMapperTest extends TestCase
{
    /* fromArray method */

    public function testFromArrayWithStandardArray(): void
    {
        // Mock data
        $fromArray = $this->createArray('EURUSD', 'forex', 'desc');

        // Start test
        $assetInputMapper = new AssetInputMapper();
        $assetInput = $assetInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(AssetInput::class, $assetInput);
        $this->assertSame('EURUSD', $assetInput->symbol);
        $this->assertSame('forex', $assetInput->type);
        $this->assertSame('desc', $assetInput->description);
    }

    public function testFromArrayWithEmptyArray(): void
    {
        // Mock data
        $fromArray = [];

        // Start test
        $assetInputMapper = new AssetInputMapper();
        $assetInput = $assetInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(AssetInput::class, $assetInput);
        $this->assertSame('', $assetInput->symbol);
        $this->assertSame('', $assetInput->type);
        $this->assertSame('', $assetInput->description);

    }

    public function testFromArrayWithNullValues(): void
    {
        // Mock data
        $fromArray = $this->createArray(null, null, null);

        // Start test
        $assetInputMapper = new AssetInputMapper();
        $assetInput = $assetInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(AssetInput::class, $assetInput);
        $this->assertSame('', $assetInput->symbol);
        $this->assertSame('', $assetInput->type);
        $this->assertSame('', $assetInput->description);
    }

    public function testFromArrayWithWeirdValues(): void
    {
        // Mock data
        $fromArray = $this->createArray(123, true, ['foo']);

        // Start test
        $assetInputMapper = new AssetInputMapper();
        $assetInput = $assetInputMapper->fromArray($fromArray);

        // Assertions
        $this->assertInstanceOf(AssetInput::class, $assetInput);
        $this->assertSame('123', $assetInput->symbol);
        $this->assertSame('1', $assetInput->type);
        $this->assertSame('', $assetInput->description);
    }

    /* private methods */

    private function createArray(
        mixed $symbol,
        mixed $type,
        mixed $description
    ): array {
        return [
            'symbol' => $symbol,
            'type' => $type,
            'description' => $description,
        ];
    }
}
