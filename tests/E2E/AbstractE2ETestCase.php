<?php

namespace App\Tests\E2E;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractE2ETestCase extends WebTestCase
{
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        parent::setUp();

        self::ensureKernelShutdown();
        $client = self::createClient();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);

        $this->resetDatabase();
    }

    /**
     * Recrée la base de données pour chaque test
     */
    protected function resetDatabase(): void
    {
        $connection = $this->em->getConnection();

        $schemaManager = $connection->createSchemaManager();
        $schema = $schemaManager->introspectSchema();

        foreach ($schema->getTables() as $table) {
            $connection->executeStatement('DROP TABLE ' . $table->getName());
        }

        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->createSchema($this->em->getMetadataFactory()->getAllMetadata());
    }

    /**
     * Helper HTTP générique
     */
    protected function request(
        string $method,
        string $uri,
        array $headers = [],
        ?array $json = null
    ): Response {
        $server = array_merge(
            ['CONTENT_TYPE' => 'application/json'],
            $headers
        );

        self::getClient()->request(
            $method,
            $uri,
            [],
            [],
            $server,
            $json ? json_encode($json) : null
        );

        return self::getClient()->getResponse();
    }

    protected function assertJsonResponse(int $statusCode): array
    {
        $response = self::getClient()->getResponse();

        self::assertSame($statusCode, $response->getStatusCode());
        self::assertJson($response->getContent());

        return json_decode($response->getContent(), true);
    }
}
