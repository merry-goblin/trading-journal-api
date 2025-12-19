<?php
namespace App\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

trait DatabaseTestTrait
{
    protected static bool $schemaCreated = false;

    protected function initDatabase(): void
    {
        if (self::$schemaCreated) {
            return;
        }

        $em = self::getContainer()->get(EntityManagerInterface::class);

        $metadata = $em->getMetadataFactory()->getAllMetadata();

        if (!empty($metadata)) {
            $schemaTool = new SchemaTool($em);
            $schemaTool->createSchema($metadata);
        }

        self::$schemaCreated = true;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get(EntityManagerInterface::class);
    }
}
