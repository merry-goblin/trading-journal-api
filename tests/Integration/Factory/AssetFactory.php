<?php

namespace App\Tests\Integration\Factory;

use App\Entity\Asset;
use Doctrine\ORM\EntityManagerInterface;

final class AssetFactory
{
    public static function create(
        EntityManagerInterface $em,
        string $symbol,
        string $type = 'forex',
        string $description = ''
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
