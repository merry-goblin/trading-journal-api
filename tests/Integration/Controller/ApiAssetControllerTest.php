<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractTestApiController;
use App\Tests\Integration\ApiTestAuthTrait;
use App\Tests\Integration\Factory\AssetFactory;

use PHPUnit\Framework\Attributes\DataProvider;

use InvalidArgumentException;

class ApiAssetControllerTest extends AbstractTestApiController
{
    use ApiTestAuthTrait;

    /* list */

    public function testListReturnsEmptyArrayWhenNoAssetsExist(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/assets';
        $this->requestAssetsUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function testListReturnsAssetsWhenTheyExist(): void
    {
        // Fake DB data
        AssetFactory::create($this->em, 'EURUSD');
        AssetFactory::create($this->em, 'EURGBP');

        // Start test
        $method = 'GET';
        $path = '/api/assets';
        $this->requestAssetsUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(
            ['EURUSD', 'EURGBP'],
            array_column($data, 'symbol')
        );
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('symbol', $data[0]);
        $this->assertArrayHasKey('type', $data[0]);
        $this->assertArrayHasKey('description', $data[0]);
    }

    #[DataProvider('invalidAuthHeadersProvider')]
    public function testListWithInvalidAuthReturns401($invalidHeaders): void
    {
        // Modify headers
        $headers = $this->getAuthHeaders('GET', '/api/assets'); // valid headers
        foreach ($invalidHeaders as $key => $value) {
            if ($value === null) {
                unset($headers[$key]);
            } else {
                $headers[$key] = $value;
            }
        }

        // Start test
        $method = 'GET';
        $path = '/api/assets';
        $this->requestAssetsUrl($method, $path, $headers);

        // Assertions
        $this->assertResponseStatusCodeSame(401);
    }

    public static function invalidAuthHeadersProvider(): iterable
    {
        yield 'missing token' => [['HTTP_X_API_TOKEN' => null]];
        yield 'invalid token' => [['HTTP_X_API_TOKEN' => 'invalid_token']];
        yield 'missing timestamp' => [['HTTP_X_API_TIMESTAMP' => null]];
        yield 'invalid timestamp' => [['HTTP_X_API_TIMESTAMP' => '2000.01.01 00:00:00']];
        yield 'missing signature' => [['HTTP_X_API_SIGNATURE' => null]];
        yield 'invalid signature' => [['HTTP_X_API_SIGNATURE' => 'invalid_signature']];
    }

    /* show */

    public function testShowByIdReturnsAsset(): void
    {
        // Fake DB data
        $asset = AssetFactory::create($this->em, 'EURUSD');

        // Start test
        $method = 'GET';
        $path = '/api/asset/'.$asset->getId();
        $this->requestAssetsUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertSame('EURUSD', $data['symbol']);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('description', $data);
    }

    public function testShowByIdReturns404WhenNotFound(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/asset/9999';
        $this->requestAssetsUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Asset not found', $data['error']);
    }

    /* showBySymbol */

    public function testShowBySymbolReturnsAsset(): void
    {
        // Fake DB data
        $asset = AssetFactory::create($this->em, 'EURUSD');

        // Start test
        $method = 'GET';
        $path = '/api/asset/symbol/'.$asset->getSymbol();
        $this->requestAssetsUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertSame('EURUSD', $data['symbol']);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('description', $data);
    }

    /*public function testShowBySymbolReturns404WhenNotFound(): void
    {
        $this->client->request(
            'GET',
            '/api/asset/symbol/UNKNOWN',
            [],
            [],
            $this->getAuthHeaders('GET', '/api/asset/symbol/UNKNOWN')
        );

        $this->assertResponseStatusCodeSame(404);
    }*/

    /* create */

    /*public function testCreateAssetReturnsCreatedAsset(): void
    {
        $payload = [
            'symbol' => 'EURUSD',
            'type' => 'forex',
            'description' => ''
        ];

        $this->client->request(
            'POST',
            '/api/asset',
            [],
            [],
            array_merge(
                $this->getAuthHeaders('POST', '/api/asset'),
                ['CONTENT_TYPE' => 'application/json']
            ),
            json_encode($payload)
        );

        $this->assertResponseIsSuccessful();

        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('EURUSD', $data['symbol']);
    }

    public function testCreateWithInvalidJsonReturns400(): void
    {
        $this->client->request(
            'POST',
            '/api/asset',
            [],
            [],
            $this->getAuthHeaders('POST', '/api/asset'),
            '{invalid_json'
        );

        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateWithInvalidPayloadReturns422(): void
    {
        $payload = ['symbol' => '']; // invalide

        $this->client->request(
            'POST',
            '/api/asset',
            [],
            [],
            array_merge(
                $this->getAuthHeaders('POST', '/api/asset'),
                ['CONTENT_TYPE' => 'application/json']
            ),
            json_encode($payload)
        );

        $this->assertResponseStatusCodeSame(422);
    }*/


    /* private */

    private function requestAssetsUrl(string $method, string $path, array $headers): void
    {
        $this->client->request(
            $method,
            $path,
            [],
            [],
            $headers
        );
    }
}
