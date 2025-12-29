<?php

namespace App\Tests\Unit\DTO\Asset;

use App\Entity\Asset;
use PHPUnit\Framework\TestCase;

use App\DTO\Asset\AssetOutput;
use App\DTO\Asset\AssetOutputMapper;

class AssetOutputMapperTest extends TestCase
{
    /* fromEntity method */

    public function testFromEntityWithStandardEntity(): void
    {
        // Mock data
        $entity = $this->createAsset(1, 'EURUSD', 'forex', 'Euro vs US Dollar');

        // Start test
        $assetOutputMapper = new AssetOutputMapper();
        $assetOutput = $assetOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(AssetOutput::class, $assetOutput);
        $this->assertSame(1, $assetOutput->id);
        $this->assertSame('EURUSD', $assetOutput->symbol);
        $this->assertSame('forex', $assetOutput->type);
        $this->assertSame('Euro vs US Dollar', $assetOutput->description);
    }

    public function testFromEntityWithEmptyEntity(): void
    {
        // Mock data
        $entity = $this->createAsset(0, '', '', '');

        // Start test
        $assetOutputMapper = new AssetOutputMapper();
        $assetOutput = $assetOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(AssetOutput::class, $assetOutput);
        $this->assertSame(0, $assetOutput->id);
        $this->assertSame('', $assetOutput->symbol);
        $this->assertSame('', $assetOutput->type);
        $this->assertSame('', $assetOutput->description);

    }

    public function testFromEntityWithWeirdValues(): void
    {
        // Mock data
        $entity = $this->createAsset(-123, true, 0.58, '');

        // Start test
        $assetOutputMapper = new AssetOutputMapper();
        $assetOutput = $assetOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(AssetOutput::class, $assetOutput);
        $this->assertSame(-123, $assetOutput->id);
        $this->assertSame('1', $assetOutput->symbol);
        $this->assertSame('0.58', $assetOutput->type);
        $this->assertSame('', $assetOutput->description);
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
}
