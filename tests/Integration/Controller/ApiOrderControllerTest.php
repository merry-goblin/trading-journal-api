<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractTestApiController;
use App\Tests\Integration\ApiTestAuthTrait;
use App\Tests\Integration\Factory\AssetFactory;
use App\Tests\Integration\Factory\OrderFactory;
use App\Tests\Integration\Factory\TimeframeFactory;

use PHPUnit\Framework\Attributes\DataProvider;

use DateTimeImmutable;

class ApiOrderControllerTest extends AbstractTestApiController
{
    use ApiTestAuthTrait;

    /* list */

    public function testListReturnsEmptyArrayWhenNoOrdersExist(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/orders';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function testListReturnsOrdersWhenTheyExist(): void
    {
        // Fake DB data
        $assetSP500 = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        OrderFactory::create(
            $this->em, 
            $assetSP500, 
            $timeframe, 
            new DateTimeImmutable('2026-06-08 09:16:00'),
            'limit',
            'long',
            '7410.86',
            '1.0',
            '7359.90',
            '7499.10',
            'filled',
            ''
        );
        OrderFactory::create(
            $this->em, 
            $assetSP500, 
            $timeframe, 
            new DateTimeImmutable('2026-06-08 09:17:00'),
            'limit',
            'short',
            '7410.86',
            '1.0',
            '7499.10',
            '7359.90',
            'filled',
            ''
        );

        // Start test
        $method = 'GET';
        $path = '/api/orders';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(
            ['long', 'short'],
            array_column($data, 'direction')
        );
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('assetId', $data[0]);
        $this->assertArrayHasKey('timeframeId', $data[0]);
        $this->assertArrayHasKey('createdAt', $data[0]);
        $this->assertArrayHasKey('orderType', $data[0]);
        $this->assertArrayHasKey('direction', $data[0]);
        $this->assertArrayHasKey('price', $data[0]);
        $this->assertArrayHasKey('size', $data[0]);
        $this->assertArrayHasKey('stopLoss', $data[0]);
        $this->assertArrayHasKey('takeProfit', $data[0]);
        $this->assertArrayHasKey('status', $data[0]);
        $this->assertArrayHasKey('comment', $data[0]);
    }

    #[DataProvider('invalidAuthHeadersProvider')]
    public function testListWithInvalidAuthReturns401($invalidHeaders): void
    {
        // Modify headers
        $headers = $this->getAuthHeaders('GET', '/api/orders'); // valid headers
        foreach ($invalidHeaders as $key => $value) {
            if ($value === null) {
                unset($headers[$key]);
            } else {
                $headers[$key] = $value;
            }
        }

        // Start test
        $method = 'GET';
        $path = '/api/orders';
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

    public function testShowByIdReturnsOrder(): void
    {
        // Fake DB data
        $assetSP500 = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $order = OrderFactory::create(
            $this->em, 
            $assetSP500, 
            $timeframe, 
            new DateTimeImmutable('2026-06-08 09:16:00'),
            'limit',
            'long',
            '7410.86',
            '1.0',
            '7359.90',
            '7499.10',
            'filled',
            ''
        );

        // Start test
        $method = 'GET';
        $path = '/api/order/'.$order->getId();
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('assetId', $data);
        $this->assertArrayHasKey('timeframeId', $data);
        $this->assertArrayHasKey('createdAt', $data);
        $this->assertArrayHasKey('orderType', $data);
        $this->assertArrayHasKey('direction', $data);
        $this->assertArrayHasKey('price', $data);
        $this->assertArrayHasKey('size', $data);
        $this->assertArrayHasKey('stopLoss', $data);
        $this->assertArrayHasKey('takeProfit', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('comment', $data);
    }

    public function testShowByIdReturns404WhenNotFound(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/order/9999';
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
        $path = '/api/order/FOO';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Http Error', $data['error']);
    }

    /* create */

    public function testCreateOrderReturnsCreatedOrder(): void
    {
        // Fake DB data
        $assetSP500 = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $payload = [
            'assetId' => 1,
            'timeframeId' => 1,
            'createdAt' => '2025-12-29 00:14:00',
            'orderType' => 'limit',
            'direction' => 'long',
            'price' => '7410.86',
            'size' => '1.0',
            'stopLoss' => '7359.90',
            'takeProfit' => '7499.10',
            'status' => 'filled',
            'comment' => '',
        ];
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path = '/api/order';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse(201);
        $this->assertIsArray($data);
        $this->assertSame('limit', $data['orderType']);
    }

    public function testCreateWithInvalidJsonReturns400(): void
    {
        $jsonContent = '{invalid_json';

        // Start test
        $method = 'POST';
        $path = '/api/order';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateWithInvalidPayloadReturns422(): void
    {
        $payload = [
            'symbol' => '',
            'type' => 'forex',
            'description' => ''
        ];
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path = '/api/order';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $this->assertResponseStatusCodeSame(422);
    }

    public function testUpdateOrderStatusReturnsUpdatedOrder(): void
    {
        // Fake DB data
        $asset     = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);

        // POST Payload
        $createPayload = [
            'assetId'     => $asset->getId(),
            'timeframeId' => $timeframe->getId(),
            'createdAt'   => '2025-12-29 00:14:00',
            'orderType'   => 'limit',
            'direction'   => 'long',
            'price'       => '7410.86',
            'size'        => '1.00',
            'stopLoss'    => '7359.90',
            'takeProfit'  => '7499.10',
            'status'      => 'pending',
        ];
        $createJson = json_encode($createPayload);
        $this->requestUrl('POST', '/api/order', $this->getAuthHeaders('POST', '/api/order', $createJson), $createJson);
        $created = json_decode($this->client->getResponse()->getContent(), true);
        $orderId = $created['id'];

        // PATCH Payload
        $patchPayload = ['status' => 'filled'];
        $patchJson    = json_encode($patchPayload);
        
        // Start test
        $method = 'PATCH';
        $path   = '/api/order/' . $orderId . '/status';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $patchJson), $patchJson);

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertSame($orderId, $data['id']);
        $this->assertSame('filled', $data['status']);
    }

    public function testUpdateOrderStatusReturns404WhenNotFound(): void
    {
        // Payload
        $patchPayload = ['status' => 'cancelled'];
        $patchJson    = json_encode($patchPayload);
        
        // Start test
        $method = 'PATCH';
        $path   = '/api/order/9999/status';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $patchJson), $patchJson);

        // Assertions
        $this->assertJsonError('Not Found', 404);
    }

    public function testUpdateOrderStatusReturns400WhenMissingField(): void
    {
        // Fake DB data
        $asset     = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);

        // POST payload
        $createPayload = [
            'assetId'     => $asset->getId(),
            'timeframeId' => $timeframe->getId(),
            'createdAt'   => '2025-12-29 00:14:00',
            'orderType'   => 'limit',
            'direction'   => 'long',
            'price'       => '7410.86',
            'size'        => '1.00',
            'stopLoss'    => '7359.90',
            'takeProfit'  => '7499.10',
            'status'      => 'pending',
        ];
        $createJson = json_encode($createPayload);
        $this->requestUrl('POST', '/api/order', $this->getAuthHeaders('POST', '/api/order', $createJson), $createJson);
        $created = json_decode($this->client->getResponse()->getContent(), true);
        $orderId = $created['id'];

        // PATCH payload without status
        $patchJson = json_encode([]);

        // Start test
        $method = 'PATCH';
        $path   = '/api/order/' . $orderId . '/status';
        $this->requestUrl($method, $path, $this->getAuthHeaders($method, $path, $patchJson), $patchJson);

        // Assertions
        $this->assertResponseStatusCodeSame(400);
    }
}
