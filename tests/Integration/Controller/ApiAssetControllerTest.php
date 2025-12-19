<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractTestApiController;
use App\Tests\Integration\ApiTestAuthTrait;
use App\Tests\Integration\Factory\AssetFactory;

use PHPUnit\Framework\Attributes\DataProvider;

class ApiAssetControllerTest extends AbstractTestApiController
{
    use ApiTestAuthTrait;

    public function testListReturnsEmptyArrayWhenNoAssetsExist(): void
    {
        // Start test
        $this->requestAssetsUrl($this->getAuthHeaders('GET', '/api/assets'));

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
        $this->requestAssetsUrl($this->getAuthHeaders('GET', '/api/assets'));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(
            ['EURUSD', 'EURGBP'],
            array_column($data, 'symbol')
        );
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
        $this->requestAssetsUrl($headers);

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

    /* private */

    private function requestAssetsUrl(array $headers): void
    {
        $this->client->request(
            'GET',
            '/api/assets',
            [],
            [],
            $headers
        );
    }
}
