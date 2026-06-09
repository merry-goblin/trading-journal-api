<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractTestApiController;
use App\Tests\Integration\ApiTestAuthTrait;
use App\Tests\Integration\Factory\AssetFactory;
use App\Tests\Integration\Factory\ChartObservationFactory;
use App\Tests\Integration\Factory\ScreenshotFactory;
use App\Tests\Integration\Factory\TimeframeFactory;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;

class ApiScreenshotControllerTest extends AbstractTestApiController
{
    use ApiTestAuthTrait;

    /* list */

    public function testListReturnsEmptyArrayWhenNoAssetsExist(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/screenshots';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function testListReturnsAssetsWhenTheyExist(): void
    {
        // Fake DB data
        $asset1 = AssetFactory::create($this->em, 'EURUSD');
        $asset2 = AssetFactory::create($this->em, 'EURGBP');
        $timeframe1 = TimeframeFactory::create($this->em, 'M1');
        $timeframe2 = TimeframeFactory::create($this->em, 'M5', 300);
        $obs1 = ChartObservationFactory::create(
            $this->em, $asset1, $timeframe1,
            new \DateTimeImmutable('2025-12-29 00:14:00'),
            'bull'
        );
        $obs2 = ChartObservationFactory::create(
            $this->em, $asset2, $timeframe2,
            new \DateTimeImmutable('2025-12-29 00:14:00'),
            'bear'
        );
        ScreenshotFactory::create(
            $this->em,
            'EURUSD/M1/123456789.png',
            new DateTimeImmutable('2025-12-29 00:14:00'),
            $asset1,
            $timeframe1,
            $obs1,
            '',
            new DateTimeImmutable('2025-11-25 00:00:00'),
            new DateTimeImmutable('2025-12-17 01:58:38'),
            'manual'
        );
        ScreenshotFactory::create(
            $this->em,
            'EURGBP/M5/123456789.png',
            new DateTimeImmutable('2025-12-29 00:14:00'),
            $asset2,
            $timeframe2,
            $obs2,
            '',
            new DateTimeImmutable('2025-11-25 00:00:00'),
            new DateTimeImmutable('2025-12-17 01:58:38'),
            'manual'
        );

        // Start test
        $method = 'GET';
        $path = '/api/screenshots';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(
            ['EURUSD/M1/123456789.png', 'EURGBP/M5/123456789.png'],
            array_column($data, 'filePath')
        );
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('createdAt', $data[0]);
        $this->assertArrayHasKey('assetId', $data[0]);
        $this->assertArrayHasKey('timeframeId', $data[0]);
        $this->assertArrayHasKey('observationId', $data[0]);
        $this->assertArrayHasKey('description', $data[0]);
        $this->assertArrayHasKey('periodStart', $data[0]);
        $this->assertArrayHasKey('periodEnd', $data[0]);
        $this->assertArrayHasKey('source', $data[0]);
    }

    #[DataProvider('invalidAuthHeadersProvider')]
    public function testListWithInvalidAuthReturns401($invalidHeaders): void
    {
        // Modify headers
        $headers = $this->getAuthHeaders('GET', '/api/screenshots'); // valid headers
        foreach ($invalidHeaders as $key => $value) {
            if ($value === null) {
                unset($headers[$key]);
            } else {
                $headers[$key] = $value;
            }
        }

        // Start test
        $method = 'GET';
        $path = '/api/screenshots';
        $this->requestUrl($method, $path, $headers);

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
        $timeframe = TimeframeFactory::create($this->em, 'M1');
        $obs = ChartObservationFactory::create(
            $this->em, $asset, $timeframe,
            new \DateTimeImmutable('2025-12-29 00:14:00'),
            'bull'
        );
        $screenshot = ScreenshotFactory::create(
            $this->em,
            'EURUSD/M1/123456789.png',
            new DateTimeImmutable('2025-12-29 00:14:00'),
            $asset,
            $timeframe,
            $obs,
            '',
            new DateTimeImmutable('2025-11-25 00:00:00'),
            new DateTimeImmutable('2025-12-17 01:58:38'),
            'manual'
        );
        $obs = ChartObservationFactory::create(
            $this->em, $asset, $timeframe,
            new \DateTimeImmutable('2025-12-29 00:14:00'), 'bull'
        );

        // Start test
        $method = 'GET';
        $path = '/api/screenshot/'.$screenshot->getId();
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertSame('EURUSD/M1/123456789.png', $data['filePath']);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('createdAt', $data);
        $this->assertArrayHasKey('assetId', $data);
        $this->assertArrayHasKey('timeframeId', $data);
        $this->assertArrayHasKey('observationId', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('periodStart', $data);
        $this->assertArrayHasKey('periodEnd', $data);
        $this->assertArrayHasKey('source', $data);
    }

    public function testShowByIdReturns404WhenNotFound(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/screenshot/9999';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Not Found', $data['error']);
    }

    public function testShowByIdReturns404WhenIdNotANumber(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/screenshot/FOO';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Http Error', $data['error']);
    }

    /* create */

    public function testCreateScreenshotReturnsCreatedScreenshot(): void
    {
        // Fake DB data
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $obs = ChartObservationFactory::create(
            $this->em, $asset, $timeframe,
            new \DateTimeImmutable('2025-12-29 00:14:00'),
            'bull'
        );
        $payload = [
            'createdAt' => '2025-12-29 00:14:00',
            'assetId'       => $asset->getId(),
            'timeframeId'   => $timeframe->getId(),
            'observationId' => $obs->getId(),
            'description' => '',
            'periodStart' => '2025-11-25 00:00:00',
            'periodEnd' => '2025-12-17 01:58:38',
            'source' => 'manual',
            'image'       => [
                'mime' => 'image/png',
                'data' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
            ]
        ];
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path = '/api/screenshot';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse(201);
        $this->assertIsArray($data);
        $this->assertSame($asset->getId(), $data['assetId']);
        $this->assertSame($obs->getId(),   $data['observationId']);
    }

    /*public function testCreateWithInvalidJsonReturns400(): void
    {
        $jsonContent = '{invalid_json';

        // Start test
        $method = 'POST';
        $path = '/api/screenshot';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateWithInvalidPayloadReturns422(): void
    {
        $payload = [
            'createdAt' => '',
            'assetId' => null,
            'timeframeId' => null,
            'observationId' => null,
            'description' => '',
            'periodStart' => '',
            'periodEnd' => '',
            'source' => ''
        ];
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path = '/api/screenshot';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $this->assertResponseStatusCodeSame(422);
    }*/
}
