<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractFrontApiTestController;
use App\Tests\Integration\Factory\AssetFactory;
use App\Tests\Integration\Factory\ChartObservationFactory;
use App\Tests\Integration\Factory\PositionFactory;
use App\Tests\Integration\Factory\TagFactory;
use App\Tests\Integration\Factory\TimeframeFactory;
use DateTimeImmutable;

class FrontPositionControllerTest extends AbstractFrontApiTestController
{
    // ── list ────────────────────────────────────────────────────

    public function testListReturnsEmptyArrayWhenNoPositionsExist(): void
    {
        $this->requestFront('GET', '/frontApi/positions');

        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function testListReturnsPositionsWhenTheyExist(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00', 'long'
        );
        PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-09 15:32:00'), '7530.00', '10.00', 'short'
        );

        $this->requestFront('GET', '/frontApi/positions');

        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(2, $data);

        $keys = ['id', 'assetSymbol', 'timeframeLabel', 'direction',
                 'openedAt', 'closedAt', 'pnl', 'rr', 'planRespected', 'tagLabels'];
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $data[0]);
        }
    }

    public function testListFiltersByDirection(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00', 'long'
        );
        PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-09 15:32:00'), '7530.00', '10.00', 'short'
        );

        $this->requestFront('GET', '/frontApi/positions?direction=long');

        $data = $this->assertJsonResponse();
        $this->assertCount(1, $data);
        $this->assertSame('long', $data[0]['direction']);
    }

    public function testListRequiresAuthentication(): void
    {
        $this->requestFrontWithoutAuth('GET', '/frontApi/positions');
        $this->assertResponseStatusCodeSame(401);
    }

    // ── show ────────────────────────────────────────────────────

    public function testShowReturnsPositionDetail(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $position = PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00', 'long',
            null, null, '7359.90', '7499.10'
        );

        $this->requestFront('GET', '/frontApi/position/' . $position->getId());

        $data = $this->assertJsonResponse();
        $this->assertSame($position->getId(), $data['id']);
        $this->assertSame('SP500', $data['assetSymbol']);
        $this->assertSame('long', $data['direction']);
        $this->assertSame('7410.86', $data['entryPrice']);
        $this->assertArrayHasKey('tags', $data);
        $this->assertArrayHasKey('observations', $data);
        $this->assertIsArray($data['tags']);
        $this->assertIsArray($data['observations']);
    }

    public function testShowIncludesLinkedObservations(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $position = PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00'
        );
        ChartObservationFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:30:00'),
            'bull', 'CHoCH visible sur H1', null, $position
        );

        $this->requestFront('GET', '/frontApi/position/' . $position->getId());

        $data = $this->assertJsonResponse();
        $this->assertCount(1, $data['observations']);
        $this->assertSame('bull', $data['observations'][0]['trend']);
        $this->assertSame('CHoCH visible sur H1', $data['observations'][0]['comment']);
    }

    public function testShowReturns404WhenNotFound(): void
    {
        $this->requestFront('GET', '/frontApi/position/9999');
        $this->assertResponseStatusCodeSame(404);
    }

    // ── enrich ──────────────────────────────────────────────────

    public function testEnrichUpdatesAnalysisFields(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $tag = TagFactory::create($this->em, 'FVG', 'setup');
        $position = PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00', 'long'
        );

        $payload = json_encode([
            'planRespected' => true,
            'higherTfBias' => 'bull',
            'entryTfBias' => 'bear',
            'setupQuality' => 4,
            'emotionScore' => 2,
            'comment' => 'Bon trade',
            'tagIds' => [$tag->getId()],
        ]);

        $this->requestFront('PATCH', '/frontApi/position/' . $position->getId(), $payload);

        $data = $this->assertJsonResponse();
        $this->assertTrue($data['planRespected']);
        $this->assertSame('bull', $data['higherTfBias']);
        $this->assertSame('bear', $data['entryTfBias']);
        $this->assertSame(4, $data['setupQuality']);
        $this->assertSame(2, $data['emotionScore']);
        $this->assertSame('Bon trade', $data['comment']);
        $this->assertCount(1, $data['tags']);
        $this->assertSame('FVG', $data['tags'][0]['label']);
    }

    public function testEnrichPartialUpdatePreservesOtherFields(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $position = PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00'
        );

        // Premier enrichissement
        $this->requestFront(
            'PATCH',
            '/frontApi/position/' . $position->getId(),
            json_encode(['setupQuality' => 5, 'higherTfBias' => 'bull'])
        );

        // Deuxieme enrichissement : seulement planRespected
        $this->requestFront(
            'PATCH',
            '/frontApi/position/' . $position->getId(),
            json_encode(['planRespected' => true])
        );

        $data = $this->assertJsonResponse();
        $this->assertTrue($data['planRespected']);
        $this->assertSame(5, $data['setupQuality']);    // preservé
        $this->assertSame('bull', $data['higherTfBias']); // preservé
    }

    public function testEnrichReturns404WhenNotFound(): void
    {
        $payload = json_encode(['planRespected' => true]);
        $this->requestFront('PATCH', '/frontApi/position/9999', $payload);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testEnrichWithInvalidBiasReturns422(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $position = PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable('2026-06-08 15:32:00'), '7410.86', '15.00'
        );

        $payload = json_encode(['higherTfBias' => 'invalid_value']);
        $this->requestFront('PATCH', '/frontApi/position/' . $position->getId(), $payload);

        $data = $this->assertJsonResponse(422);
        $this->assertSame('validation_failed', $data['error']);
    }

    public function testEnrichRequiresAuthentication(): void
    {
        $this->requestFrontWithoutAuth('PATCH', '/frontApi/position/1', json_encode(['planRespected' => true]));
        $this->assertResponseStatusCodeSame(401);
    }
}
