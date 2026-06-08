<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractTestApiController;
use App\Tests\Integration\ApiTestAuthTrait;
use App\Tests\Integration\Factory\AssetFactory;
use App\Tests\Integration\Factory\OrderFactory;
use App\Tests\Integration\Factory\PositionFactory;
use App\Tests\Integration\Factory\TimeframeFactory;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;

class ApiPositionControllerTest extends AbstractTestApiController
{
    use ApiTestAuthTrait;

    // ── Helpers ─────────────────────────────────────────────────

    private function basePayload(int $assetId, int $timeframeId): array
    {
        return [
            'assetId'     => $assetId,
            'timeframeId' => $timeframeId,
            'openedAt'    => '2026-06-08 15:32:00',
            'entryPrice'  => '7410.86',
            'volume'      => '15.00',
            'direction'   => 'long',
            'stopLoss'    => '7359.90',
            'takeProfit'  => '7499.10',
        ];
    }

    private function requiredKeys(): array
    {
        return [
            'id', 'assetId', 'timeframeId', 'originOrderId',
            'openedAt', 'closedAt', 'direction',
            'entryPrice', 'exitPrice', 'stopLoss', 'takeProfit',
            'volume', 'riskAmount', 'pnl', 'pnlPercent', 'rr', 'comment',
            'planRespected', 'higherTfBias', 'entryTfBias',
            'setupQuality', 'emotionScore',
        ];
    }

    // ── list ────────────────────────────────────────────────────

    public function testListReturnsEmptyArrayWhenNoPositionsExist(): void
    {
        // Start test
        $method = 'GET';
        $path   = '/api/positions';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function testListReturnsPositionsWhenTheyExist(): void
    {
        // Fake DB data
        $asset     = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00',
            'long', new DateTimeImmutable('2026-06-08 16:10:00'),
            '7476.50', '7359.90', '7499.10', null,
            '150.00', '480.00', '3.20', '2.10', ''
        );
        PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-09 15:45:00'), '7530.00', '10.00',
            'short', null,
            null, '7560.00', '7480.00', null,
            '100.00', null, null, null, ''
        );

        // Start test
        $method = 'GET';
        $path   = '/api/positions';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(
            ['long', 'short'],
            array_column($data, 'direction')
        );
        foreach ($this->requiredKeys() as $key) {
            $this->assertArrayHasKey($key, $data[0]);
        }
    }

    #[DataProvider('invalidAuthHeadersProvider')]
    public function testListWithInvalidAuthReturns401($invalidHeaders): void
    {
        $headers = $this->getAuthHeaders('GET', '/api/positions');
        foreach ($invalidHeaders as $key => $value) {
            if ($value === null) {
                unset($headers[$key]);
            } else {
                $headers[$key] = $value;
            }
        }

        // Start test
        $method = 'GET';
        $path   = '/api/positions';
        $this->requestUrl($method, $path, $headers);

        // Assertions
        $this->assertResponseStatusCodeSame(401);
    }

    public static function invalidAuthHeadersProvider(): iterable
    {
        yield 'missing token'     => [['HTTP_X_API_TOKEN'     => null]];
        yield 'invalid token'     => [['HTTP_X_API_TOKEN'     => 'invalid_token']];
        yield 'missing timestamp' => [['HTTP_X_API_TIMESTAMP' => null]];
        yield 'invalid timestamp' => [['HTTP_X_API_TIMESTAMP' => '2000.01.01 00:00:00']];
        yield 'missing signature' => [['HTTP_X_API_SIGNATURE' => null]];
        yield 'invalid signature' => [['HTTP_X_API_SIGNATURE' => 'invalid_signature']];
    }

    // ── show ────────────────────────────────────────────────────

    public function testShowByIdReturnsPosition(): void
    {
        // Fake DB data
        $asset     = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $position  = PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00',
            'long', null, null, '7359.90', '7499.10'
        );

        // Start test
        $method = 'GET';
        $path   = '/api/position/' . $position->getId();
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertSame($position->getId(), $data['id']);
        $this->assertSame($asset->getId(), $data['assetId']);
        $this->assertSame('long', $data['direction']);
        foreach ($this->requiredKeys() as $key) {
            $this->assertArrayHasKey($key, $data);
        }
    }

    public function testShowByIdReturns404WhenNotFound(): void
    {
        // Start test
        $method = 'GET';
        $path   = '/api/position/9999';
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
        $path   = '/api/position/FOO';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Http Error', $data['error']);
    }

    // ── create ──────────────────────────────────────────────────

    public function testCreatePositionReturnsCreatedPosition(): void
    {
        // Fake DB data
        $asset     = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);

        $payload     = $this->basePayload($asset->getId(), $timeframe->getId());
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path   = '/api/position';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse(201);
        $this->assertIsArray($data);
        $this->assertSame($asset->getId(), $data['assetId']);
        $this->assertSame('long', $data['direction']);
        $this->assertSame('7410.86', $data['entryPrice']);
        $this->assertNull($data['planRespected']);
        $this->assertNull($data['higherTfBias']);
        $this->assertNull($data['entryTfBias']);
        $this->assertNull($data['setupQuality']);
        $this->assertNull($data['emotionScore']);
    }

    public function testCreatePositionWithOriginOrderReturnsLinkedPosition(): void
    {
        // Fake DB data
        $asset     = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $order     = OrderFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:30:00'),
            'limit', 'long', '7410.86', '15.00',
            '7359.90', '7499.10', 'filled', ''
        );

        $payload = array_merge(
            $this->basePayload($asset->getId(), $timeframe->getId()),
            ['originOrderId' => $order->getId()]
        );
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path   = '/api/position';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse(201);
        $this->assertSame($order->getId(), $data['originOrderId']);
    }

    public function testCreateWithInvalidJsonReturns400(): void
    {
        $jsonContent = '{invalid_json';

        // Start test
        $method = 'POST';
        $path   = '/api/position';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateWithInvalidPayloadReturns422(): void
    {
        // Payload sans assetId, timeframeId, openedAt, entryPrice et volume
        $payload     = ['direction' => 'long', 'comment' => 'test'];
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path   = '/api/position';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse(422);
        $this->assertSame('validation_failed', $data['error']);
        $this->assertArrayHasKey('assetId',    $data['details']);
        $this->assertArrayHasKey('timeframeId', $data['details']);
        $this->assertArrayHasKey('openedAt',    $data['details']);
        $this->assertArrayHasKey('entryPrice',  $data['details']);
        $this->assertArrayHasKey('volume',      $data['details']);
    }
}
