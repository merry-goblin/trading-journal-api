<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Asset;

class AssetFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $asset = new Asset();
        $asset->setSymbol('EURUSD');
        $asset->setType('forex');
        $manager->persist($asset);

        $manager->flush();
    }
}
