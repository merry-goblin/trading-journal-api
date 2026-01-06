<?php

namespace App\Tests\Integration;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class AbstractTestApiController extends WebTestCase
{
    protected EntityManagerInterface $em;
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $this->em = static::getContainer()
            ->get(EntityManagerInterface::class);

        $schemaTool = new SchemaTool($this->em);
        $schemaTool->dropSchema(
            $this->em->getMetadataFactory()->getAllMetadata()
        );
        $schemaTool->createSchema(
            $this->em->getMetadataFactory()->getAllMetadata()
        );
    }

    protected function requestUrl(string $method, string $path, array $headers, ?string $content = null): void
    {
        $this->client->request(
            $method,
            $path,
            [],
            [],
            $headers,
            $content
        );
    }

    /* =====================
     * JSON assertions
     * ===================== */

    protected function assertJsonResponse(int $status = 200): array
    {
        $this->assertResponseStatusCodeSame($status);
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $json = $this->client->getResponse()->getContent();
        $this->assertJson($json);

        return json_decode($json, true);
    }

    protected function assertJsonError(string $message, int $status): void
    {
        $data = $this->assertJsonResponse($status);
        $this->assertSame($message, $data['error']);
    }

}
