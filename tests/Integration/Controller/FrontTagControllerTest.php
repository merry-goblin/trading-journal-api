<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractFrontApiTestController;
use App\Tests\Integration\Factory\TagFactory;

class FrontTagControllerTest extends AbstractFrontApiTestController
{
    // ── list ────────────────────────────────────────────────────

    public function testListReturnsEmptyArrayWhenNoTagsExist(): void
    {
        $this->requestFront('GET', '/frontApi/tags');

        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function testListReturnsTagsWhenTheyExist(): void
    {
        TagFactory::create($this->em, 'FVG',   'setup',  'Fair Value Gap');
        TagFactory::create($this->em, 'CHoCH', 'setup',  'Change of Character');
        TagFactory::create($this->em, 'FOMO',  'erreur', 'Entree sur emotion');

        $this->requestFront('GET', '/frontApi/tags');

        $data = $this->assertJsonResponse();
        $this->assertCount(3, $data);
        $this->assertArrayHasKey('id', $data[0]);
        $this->assertArrayHasKey('label', $data[0]);
        $this->assertArrayHasKey('type', $data[0]);
        $this->assertArrayHasKey('description', $data[0]);
    }

    public function testListRequiresAuthentication(): void
    {
        $this->requestFrontWithoutAuth('GET', '/frontApi/tags');
        $this->assertResponseStatusCodeSame(401);
    }

    // ── create ──────────────────────────────────────────────────

    public function testCreateTagReturns201(): void
    {
        $payload = json_encode([
            'label' => 'OB',
            'type' => 'setup',
            'description' => 'Order Block',
        ]);

        $this->requestFront('POST', '/frontApi/tag', $payload);

        $data = $this->assertJsonResponse(201);
        $this->assertSame('OB', $data['label']);
        $this->assertSame('setup', $data['type']);
        $this->assertSame('Order Block', $data['description']);
        $this->assertArrayHasKey('id', $data);
    }

    public function testCreateWithMissingLabelReturns422(): void
    {
        $payload = json_encode(['type' => 'setup']);

        $this->requestFront('POST', '/frontApi/tag', $payload);

        $data = $this->assertJsonResponse(422);
        $this->assertSame('validation_failed', $data['error']);
        $this->assertArrayHasKey('label', $data['details']);
    }

    public function testCreateWithMissingTypeReturns422(): void
    {
        $payload = json_encode(['label' => 'FVG']);

        $this->requestFront('POST', '/frontApi/tag', $payload);

        $data = $this->assertJsonResponse(422);
        $this->assertArrayHasKey('type', $data['details']);
    }

    public function testCreateWithInvalidJsonReturns400(): void
    {
        $this->requestFront('POST', '/frontApi/tag', '{invalid');
        $this->assertResponseStatusCodeSame(400);
    }

    public function testCreateRequiresAuthentication(): void
    {
        $this->requestFrontWithoutAuth('POST', '/frontApi/tag', json_encode(['label' => 'FVG', 'type' => 'setup']));
        $this->assertResponseStatusCodeSame(401);
    }
}
