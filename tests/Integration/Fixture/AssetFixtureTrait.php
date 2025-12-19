<?php

namespace App\Tests\Integration\Fixture;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;

trait AssetFixtureTrait
{
    protected function createAsset(
        EntityManagerInterface $em,
        string $symbol,
        string $type,
        string $description
    ): Asset {
        $asset = new Asset();
        $asset->setSymbol($symbol);
        $asset->setType($type);
        $asset->setDescription($description);

        $em->persist($asset);
        $em->flush();

        return $asset;
    }
}
