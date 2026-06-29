<?php

namespace App\Domain\Service\Stats;

use App\DTO\FrontApi\Stats\StatsOutput;
use App\DTO\FrontApi\Stats\TagStatsOutput;
use App\Entity\Position;
use App\Repository\Position\PositionRepositoryInterface;

class StatsService implements StatsServiceInterface
{
    public function __construct(
        private PositionRepositoryInterface $positionRepository
    ) {}

    public function getGlobalStats(array $filters = []): StatsOutput
    {
        $positions = $this->positionRepository->findClosedWithFilters($filters);

        $dto = new StatsOutput();
        $dto->totalTrades = count($positions);
        if ($dto->totalTrades === 0) return $dto;

        $pnls = array_map(fn(Position $p) => floatval($p->getPnl()), $positions);
        $rrs  = array_values(array_filter(
            array_map(fn(Position $p) => $p->getRr() !== null ? floatval($p->getRr()) : null, $positions),
            fn($v) => $v !== null
        ));

        $wins   = array_values(array_filter($pnls, fn($v) => $v > 0));
        $losses = array_values(array_filter($pnls, fn($v) => $v <= 0));

        $dto->winCount  = count($wins);
        $dto->lossCount = $dto->totalTrades - $dto->winCount;
        $dto->winrate   = round($dto->winCount / $dto->totalTrades * 100, 2);
        $dto->totalPnl  = round(array_sum($pnls), 2);
        $dto->avgPnl    = round($dto->totalPnl / $dto->totalTrades, 2);
        $dto->avgRr     = count($rrs) > 0 ? round(array_sum($rrs) / count($rrs), 2) : null;

        $dto->avgWin  = count($wins)   > 0 ? round(array_sum($wins)   / count($wins), 2)   : null;
        $dto->avgLoss = count($losses) > 0 ? round(array_sum($losses) / count($losses), 2) : null;

        if ($dto->avgWin !== null && $dto->avgLoss !== null) {
            $wr = $dto->winrate / 100.0;
            $dto->expectancy = round($wr * $dto->avgWin + (1 - $wr) * $dto->avgLoss, 2);
        }

        $grossProfit = array_sum($wins);
        $grossLoss   = abs(array_sum($losses));
        $dto->profitFactor = $grossLoss > 0 ? round($grossProfit / $grossLoss, 2) : null;

        $maxPnl = max($pnls); $minPnl = min($pnls);
        foreach ($positions as $p) {
            if ($dto->bestTradeId  === null && floatval($p->getPnl()) == $maxPnl)
                { $dto->bestTradeId  = $p->getId(); $dto->bestTradePnl  = $maxPnl; }
            if ($dto->worstTradeId === null && floatval($p->getPnl()) == $minPnl)
                { $dto->worstTradeId = $p->getId(); $dto->worstTradePnl = $minPnl; }
        }

        $win = 0; $loss = 0; $maxW = 0; $maxL = 0;
        foreach ($pnls as $pnl) {
            if ($pnl > 0) { $win++; $loss = 0; $maxW = max($maxW, $win); }
            else          { $loss++; $win = 0;  $maxL = max($maxL, $loss); }
        }
        $dto->maxWinStreak  = $maxW;
        $dto->maxLossStreak = $maxL;

        $withData  = array_filter($positions, fn(Position $p) => $p->isPlanRespected() !== null);
        if (count($withData) > 0) {
            $respected = array_filter($withData, fn(Position $p) => $p->isPlanRespected() === true);
            $dto->disciplineScore = round(count($respected) / count($withData) * 100, 1);
        }

        return $dto;
    }

    public function getStatsByTag(array $filters = []): array
    {
        // Pour by-tag on enleve le filtre tagId (on veut tous les tags)
        $f = array_diff_key($filters, ['tagId' => true]);
        $positions = $this->positionRepository->findClosedWithFilters($f);

        $tagData = [];
        foreach ($positions as $position) {
            foreach ($position->getTags() as $tag) {
                $key = $tag->getId();
                if (!isset($tagData[$key]))
                    $tagData[$key] = [
                        'id' => $tag->getId(), 'label' => $tag->getLabel(),
                        'type' => $tag->getType(), 'count' => 0, 'winCount' => 0,
                        'totalPnl' => 0.0, 'totalRr' => 0.0, 'rrCount' => 0
                    ];
                $tagData[$key]['count']++;
                $pnl = floatval($position->getPnl() ?? 0);
                $tagData[$key]['totalPnl'] += $pnl;
                if ($pnl > 0) $tagData[$key]['winCount']++;
                if ($position->getRr() !== null) {
                    $tagData[$key]['totalRr'] += floatval($position->getRr());
                    $tagData[$key]['rrCount']++;
                }
            }
        }

        return array_values(array_map(function (array $d) {
            $dto = new TagStatsOutput();
            $dto->tagId    = $d['id'];  $dto->tagLabel = $d['label']; $dto->tagType = $d['type'];
            $dto->count    = $d['count']; $dto->winCount = $d['winCount'];
            $dto->winrate  = $d['count'] > 0 ? round($d['winCount'] / $d['count'] * 100, 2) : 0.0;
            $dto->totalPnl = round($d['totalPnl'], 2);
            $dto->avgRr    = $d['rrCount'] > 0 ? round($d['totalRr'] / $d['rrCount'], 2) : null;
            return $dto;
        }, $tagData));
    }

    public function getEquityCurve(array $filters = []): array
    {
        $positions  = $this->positionRepository->findClosedWithFilters($filters);
        $cumulative = 0.0;
        return array_map(function (Position $p) use (&$cumulative) {
            $pnl = floatval($p->getPnl() ?? 0);
            $cumulative += $pnl;
            return [
                'closedAt'   => $p->getClosedAt()?->format('Y-m-d H:i:s'),
                'date'       => $p->getClosedAt()?->format('d/m'),
                'pnl'        => round($pnl, 2),
                'cumulative' => round($cumulative, 2),
                'symbol'     => $p->getAsset()?->getSymbol(),
            ];
        }, $positions);
    }

    public function getRRDistribution(array $filters = []): array
    {
        $positions = $this->positionRepository->findClosedWithFilters($filters);

        $buckets = [
            ['label' => '<-2R',      'min' => -INF, 'max' => -2.0, 'count' => 0, 'wins' => 0],
            ['label' => '-2R→-1R',  'min' => -2.0, 'max' => -1.0, 'count' => 0, 'wins' => 0],
            ['label' => '-1R→0',    'min' => -1.0, 'max' =>  0.0, 'count' => 0, 'wins' => 0],
            ['label' => '0→0.5R',   'min' =>  0.0, 'max' =>  0.5, 'count' => 0, 'wins' => 1],
            ['label' => '0.5→1R',   'min' =>  0.5, 'max' =>  1.0, 'count' => 0, 'wins' => 1],
            ['label' => '1→1.5R',   'min' =>  1.0, 'max' =>  1.5, 'count' => 0, 'wins' => 1],
            ['label' => '1.5→2R',   'min' =>  1.5, 'max' =>  2.0, 'count' => 0, 'wins' => 1],
            ['label' => '2→3R',     'min' =>  2.0, 'max' =>  3.0, 'count' => 0, 'wins' => 1],
            ['label' => '>3R',      'min' =>  3.0, 'max' =>  INF, 'count' => 0, 'wins' => 1],
        ];

        foreach ($positions as $p) {
            if ($p->getRr() === null) continue;
            $rr       = floatval($p->getRr());
            $pnl      = floatval($p->getPnl() ?? 0);
            $signedRR = $pnl >= 0 ? $rr : -$rr;
            foreach ($buckets as &$b) {
                if ($signedRR >= $b['min'] && $signedRR < $b['max']) { $b['count']++; break; }
            }
        }
        unset($b);

        return array_values(array_filter($buckets, fn($b) => $b['count'] > 0));
    }

    public function getTemporalStats(array $filters = []): array
    {
        $positions  = $this->positionRepository->findClosedWithFilters($filters);
        $byHour     = [];
        $byWeekday  = [];

        foreach ($positions as $p) {
            $openedAt = $p->getOpenedAt();
            if (!$openedAt) continue;
            $pnl     = floatval($p->getPnl() ?? 0);
            $hour    = (int)$openedAt->format('G');
            $weekday = (int)$openedAt->format('N');

            if (!isset($byHour[$hour]))    $byHour[$hour]    = ['count' => 0, 'wins' => 0, 'pnl' => 0.0];
            if (!isset($byWeekday[$weekday])) $byWeekday[$weekday] = ['count' => 0, 'wins' => 0, 'pnl' => 0.0];

            $byHour[$hour]['count']++;    $byHour[$hour]['pnl'] += $pnl;
            if ($pnl > 0) $byHour[$hour]['wins']++;
            $byWeekday[$weekday]['count']++; $byWeekday[$weekday]['pnl'] += $pnl;
            if ($pnl > 0) $byWeekday[$weekday]['wins']++;
        }

        ksort($byHour); ksort($byWeekday);
        $wdLabels = [1=>'Lun',2=>'Mar',3=>'Mer',4=>'Jeu',5=>'Ven',6=>'Sam',7=>'Dim'];

        return [
            'byHour' => array_values(array_map(function ($h, $d) {
                return ['hour'=>$h,'label'=>sprintf('%02d:00',$h),'count'=>$d['count'],
                        'pnl'=>round($d['pnl'],2),'winrate'=>round($d['wins']/$d['count']*100,1)];
            }, array_keys($byHour), $byHour)),
            'byWeekday' => array_values(array_map(function ($wd, $d) use ($wdLabels) {
                return ['weekday'=>$wd,'label'=>$wdLabels[$wd]??"J$wd",'count'=>$d['count'],
                        'pnl'=>round($d['pnl'],2),'winrate'=>round($d['wins']/$d['count']*100,1)];
            }, array_keys($byWeekday), $byWeekday)),
        ];
    }
}
