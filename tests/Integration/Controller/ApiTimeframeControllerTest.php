<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractTestApiController;
use App\Tests\Integration\ApiTestAuthTrait;
use App\Tests\Integration\Factory\TimeframeFactory;

use PHPUnit\Framework\Attributes\DataProvider;

use InvalidArgumentException;

class ApiTimeframeControllerTest extends AbstractTestApiController
{
    use ApiTestAuthTrait;

    /* list */

    public function testListReturnsEmptyArrayWhenNoTimeframesExist(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/timeframes';
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function testListReturnsTimeframesWhenTheyExist(): void
    {
        // Fake DB data
        TimeframeFactory::create($this->em, 'M1');
        TimeframeFactory::create($this->em, 'M5', 300);

        // Start test
        $method = 'GET';
        $path = '/api/timeframes';
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);
        $this->assertSame(
            ['M1', 'M5'],
            array_column($data, 'label')
        );
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('label', $data[0]);
        $this->assertArrayHasKey('seconds', $data[0]);
    }

    #[DataProvider('invalidAuthHeadersProvider')]
    public function testListWithInvalidAuthReturns401($invalidHeaders): void
    {
        // Modify headers
        $headers = $this->getAuthHeaders('GET', '/api/timeframes'); // valid headers
        foreach ($invalidHeaders as $key => $value) {
            if ($value === null) {
                unset($headers[$key]);
            } else {
                $headers[$key] = $value;
            }
        }

        // Start test
        $method = 'GET';
        $path = '/api/timeframes';
        $this->requestTimeframesUrl($method, $path, $headers);

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

    public function testShowByIdReturnsTimeframe(): void
    {
        // Fake DB data
        $timeframe = TimeframeFactory::create($this->em, 'M1');

        // Start test
        $method = 'GET';
        $path = '/api/timeframe/'.$timeframe->getId();
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertSame('M1', $data['label']);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('seconds', $data);
    }

    public function testShowByIdReturns404WhenNotFound(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/timeframe/9999';
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Not Found', $data['error']);
    }

    public function testShowByIdReturns404WhenIdNotANumber(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/timeframe/FOO';
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $this->assertResponseStatusCodeSame(404);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertSame('Http Error', $data['error']);
    }

    /* showBySymbol */

    public function testShowByLabelReturnsTimeframe(): void
    {
        // Fake DB data
        $timeframe = TimeframeFactory::create($this->em, 'M1');

        // Start test
        $method = 'GET';
        $path = '/api/timeframe/label/'.$timeframe->getLabel();
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertSame('M1', $data['label']);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('seconds', $data);
    }

    public function testShowByLabelReturns404WhenNotFound(): void
    {
        // Start test
        $method = 'GET';
        $path = '/api/timeframe/label/NOTFOUND';
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path));

        // Assertions
        $this->assertResponseStatusCodeSame(404);
    }

    /* create */

    public function testCreateTimeframeReturnsCreatedTimeframe(): void
    {
        $payload = [
            'label' => 'M1',
            'seconds' => 60,
        ];
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path = '/api/timeframe';
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertSame('M1', $data['label']);
    }

    public function testCreateWithInvalidJsonReturns400(): void
    {
        $jsonContent = '{invalid_json';

        // Start test
        $method = 'POST';
        $path = '/api/timeframe';
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateWithInvalidPayloadReturns422(): void
    {
        $payload = [
            'symbol' => '',
            'seconds' => 60
        ];
        $jsonContent = json_encode($payload);

        // Start test
        $method = 'POST';
        $path = '/api/timeframe';
        $this->requestTimeframesUrl($method, $path, $this->getAuthHeaders($method, $path, $jsonContent), $jsonContent);

        // Assertions
        $this->assertResponseStatusCodeSame(422);
    }

    /* private */

    private function requestTimeframesUrl(string $method, string $path, array $headers, ?string $content = null): void
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
}
