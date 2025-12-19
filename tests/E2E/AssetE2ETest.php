<?php

namespace App\Tests\E2E;

class AssetE2ETest extends AbstractE2ETestCase
{
    use E2EAuthTrait;

    public function testCreateAndRetrieveAsset(): void
    {
        $headers = $this->authHeaders('POST', '/api/asset');

        $this->request(
            'POST',
            '/api/asset',
            $headers,
            ['symbol' => 'EURUSD']
        );

        $data = $this->assertJsonResponse(200);

        self::assertSame('EURUSD', $data['symbol']);

        // Lecture
        $headers = $this->authHeaders('GET', '/api/assets');

        $this->request('GET', '/api/assets', $headers);

        $list = $this->assertJsonResponse(200);

        self::assertCount(1, $list);
    }
}