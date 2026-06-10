<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\AbstractFrontApiTestController;
use App\Tests\Integration\Factory\AssetFactory;
use App\Tests\Integration\Factory\PositionFactory;
use App\Tests\Integration\Factory\TagFactory;
use App\Tests\Integration\Factory\TimeframeFactory;
use DateTimeImmutable;

class FrontStatsControllerTest extends AbstractFrontApiTestController
{
    // ── /frontApi/stats ──────────────────────────────────────────

    public function testGlobalStatsReturnsZerosWhenNoPositions(): void
    {
        $this->requestFront('GET', '/frontApi/stats');

        $data = $this->assertJsonResponse();
        $this->assertEquals(0, $data['totalTrades']);
        $this->assertEquals(0, $data['winCount']);
        $this->assertEquals(0.0, $data['winrate']);
        $this->assertEquals(0.0, $data['totalPnl']);
        $this->assertNull($data['avgRr']);
        $this->assertNull($data['bestTradeId']);
        $this->assertNull($data['disciplineScore']);
    }

    public function testGlobalStatsCalculatesWinrateAndPnl(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);

        // 2 gains, 1 perte
        $this->createClosedPosition($asset, $timeframe, '200.00', '2.10', 'long',  '2026-06-08');
        $this->createClosedPosition($asset, $timeframe, '150.00', '1.80', 'short', '2026-06-09');
        $this->createClosedPosition($asset, $timeframe, '-50.00', '0.50', 'long',  '2026-06-10');

        $this->requestFront('GET', '/frontApi/stats');

        $data = $this->assertJsonResponse();
        $this->assertEquals(3,      $data['totalTrades']);
        $this->assertEquals(2,      $data['winCount']);
        $this->assertEquals(1,      $data['lossCount']);
        $this->assertEquals(66.67,  $data['winrate']);
        $this->assertEquals(300.0,  $data['totalPnl']);   // 200 + 150 - 50
        $this->assertEquals(100.0,  $data['avgPnl']);
        $this->assertNotNull($data['bestTradeId']);
        $this->assertEquals(200.0,  $data['bestTradePnl']);
        $this->assertEquals(-50.0,  $data['worstTradePnl']);
    }

    public function testGlobalStatsDisciplineScoreIgnoresNullPlanRespected(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);

        // 1 avec plan respecte, 1 avec plan non respecte, 1 sans donnee
        $p1 = $this->createClosedPosition($asset, $timeframe, '200.00', '2.10', 'long', '2026-06-08');
        $p1->setPlanRespected(true);
        $p2 = $this->createClosedPosition($asset, $timeframe, '-50.00', '0.50', 'long', '2026-06-09');
        $p2->setPlanRespected(false);
        $this->createClosedPosition($asset, $timeframe, '100.00', '1.50', 'long', '2026-06-10');
        // p3 : planRespected = null (pas renseigne)
        $this->em->flush();

        $this->requestFront('GET', '/frontApi/stats');

        $data = $this->assertJsonResponse();
        // 1 respecte sur 2 renseignes = 50%
        $this->assertEquals(50.0, $data['disciplineScore']);
    }

    public function testGlobalStatsRequiresAuthentication(): void
    {
        $this->requestFrontWithoutAuth('GET', '/frontApi/stats');
        $this->assertResponseStatusCodeSame(401);
    }

    // ── /frontApi/stats/by-tag ───────────────────────────────────

    public function testStatsByTagReturnsEmptyWhenNoTaggedPositions(): void
    {
        $this->requestFront('GET', '/frontApi/stats/by-tag');

        $data = $this->assertJsonResponse();
        $this->assertIsArray($data);
        $this->assertCount(0, $data);
    }

    public function testStatsByTagCalculatesCorrectlyPerTag(): void
    {
        $asset = AssetFactory::create($this->em, 'SP500');
        $timeframe = TimeframeFactory::create($this->em, 'M5', 300);
        $fvgTag = TagFactory::create($this->em, 'FVG', 'setup');
        $obTag  = TagFactory::create($this->em, 'OB',  'setup');

        // 2 positions FVG : 1 gain, 1 perte
        $p1 = $this->createClosedPosition($asset, $timeframe, '200.00', '2.10', 'long',  '2026-06-08');
        $p1->addTag($fvgTag);
        $p2 = $this->createClosedPosition($asset, $timeframe, '-50.00', '0.50', 'short', '2026-06-09');
        $p2->addTag($fvgTag);

        // 1 position OB : gain
        $p3 = $this->createClosedPosition($asset, $timeframe, '150.00', '1.80', 'long',  '2026-06-10');
        $p3->addTag($obTag);

        $this->em->flush();

        $this->requestFront('GET', '/frontApi/stats/by-tag');

        $data = $this->assertJsonResponse();
        $this->assertCount(2, $data);

        // Trouver le tag FVG dans les resultats
        $fvgStats = current(array_filter($data, fn($d) => $d['tagLabel'] === 'FVG'));
        $this->assertNotFalse($fvgStats);
        $this->assertEquals(2,    $fvgStats['count']);
        $this->assertEquals(1,    $fvgStats['winCount']);
        $this->assertEquals(50.0, $fvgStats['winrate']);
        $this->assertEquals(150.0,$fvgStats['totalPnl']); // 200 - 50

        // Trouver le tag OB
        $obStats = current(array_filter($data, fn($d) => $d['tagLabel'] === 'OB'));
        $this->assertEquals(1,    $obStats['count']);
        $this->assertEquals(100.0,$obStats['winrate']);
    }

    public function testStatsByTagRequiresAuthentication(): void
    {
        $this->requestFrontWithoutAuth('GET', '/frontApi/stats/by-tag');
        $this->assertResponseStatusCodeSame(401);
    }

    // ── Helper ──────────────────────────────────────────────────

    private function createClosedPosition($asset, $timeframe, string $pnl, string $rr, string $direction, string $date)
    {
        return PositionFactory::create(
            $this->em, $asset, $timeframe,
            new DateTimeImmutable($date . ' 15:32:00'),
            '7410.86', '15.00', $direction,
            new DateTimeImmutable($date . ' 16:10:00'),
            '7476.50', '7359.90', '7499.10',
            null, '150.00', $pnl, null, $rr
        );
    }
}
