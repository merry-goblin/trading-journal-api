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

    public function getGlobalStats(?int $tagId = null, ?bool $isBacktest = false): StatsOutput
    {
        $positions = $tagId !== null
            ? $this->positionRepository->findClosedByTag($tagId, $isBacktest)
            : $this->positionRepository->findClosed($isBacktest);

        $dto = new StatsOutput();
        $dto->totalTrades = count($positions);
        if ($dto->totalTrades === 0) return $dto;

        $pnls = array_map(fn(Position $p) => floatval($p->getPnl()), $positions);
        $rrs  = array_values(array_filter(
            array_map(fn(Position $p) => $p->getRr() !== null ? floatval($p->getRr()) : null, $positions),
            fn($v) => $v !== null
        ));

        $dto->winCount  = count(array_filter($pnls, fn($v) => $v > 0));
        $dto->lossCount = $dto->totalTrades - $dto->winCount;
        $dto->winrate   = round($dto->winCount / $dto->totalTrades * 100, 2);
        $dto->totalPnl  = round(array_sum($pnls), 2);
        $dto->avgPnl    = round($dto->totalPnl / $dto->totalTrades, 2);
        $dto->avgRr     = count($rrs) > 0 ? round(array_sum($rrs) / count($rrs), 2) : null;

        $maxPnl = max($pnls); $minPnl = min($pnls);
        foreach ($positions as $p) {
            if ($dto->bestTradeId === null && floatval($p->getPnl()) == $maxPnl) {
                $dto->bestTradeId = $p->getId(); $dto->bestTradePnl = $maxPnl;
            }
            if ($dto->worstTradeId === null && floatval($p->getPnl()) == $minPnl) {
                $dto->worstTradeId = $p->getId(); $dto->worstTradePnl = $minPnl;
            }
        }

        $win = 0; $loss = 0; $maxW = 0; $maxL = 0;
        foreach ($pnls as $pnl) {
            if ($pnl > 0) { $win++; $loss = 0; $maxW = max($maxW, $win); }
            else          { $loss++; $win = 0;  $maxL = max($maxL, $loss); }
        }
        $dto->maxWinStreak = $maxW; $dto->maxLossStreak = $maxL;

        $withData = array_filter($positions, fn(Position $p) => $p->isPlanRespected() !== null);
        if (count($withData) > 0) {
            $respected = array_filter($withData, fn(Position $p) => $p->isPlanRespected() === true);
            $dto->disciplineScore = round(count($respected) / count($withData) * 100, 1);
        }
        return $dto;
    }

    public function getStatsByTag(): array
    {
        $positions = $this->positionRepository->findClosed(false);
        $tagData   = [];
        foreach ($positions as $position) {
            foreach ($position->getTags() as $tag) {
                $key = $tag->getId();
                if (!isset($tagData[$key]))
                    $tagData[$key] = ['id'=>$tag->getId(),'label'=>$tag->getLabel(),'type'=>$tag->getType(),'count'=>0,'winCount'=>0,'totalPnl'=>0.0,'totalRr'=>0.0,'rrCount'=>0];
                $tagData[$key]['count']++;
                $pnl = floatval($position->getPnl() ?? 0);
                $tagData[$key]['totalPnl'] += $pnl;
                if ($pnl > 0) $tagData[$key]['winCount']++;
                if ($position->getRr() !== null) { $tagData[$key]['totalRr'] += floatval($position->getRr()); $tagData[$key]['rrCount']++; }
            }
        }
        return array_values(array_map(function (array $d) {
            $dto = new TagStatsOutput();
            $dto->tagId=$d['id']; $dto->tagLabel=$d['label']; $dto->tagType=$d['type'];
            $dto->count=$d['count']; $dto->winCount=$d['winCount'];
            $dto->winrate=$d['count']>0?round($d['winCount']/$d['count']*100,2):0.0;
            $dto->totalPnl=round($d['totalPnl'],2);
            $dto->avgRr=$d['rrCount']>0?round($d['totalRr']/$d['rrCount'],2):null;
            return $dto;
        }, $tagData));
    }

    public function getEquityCurve(?int $tagId = null, ?bool $isBacktest = false): array
    {
        $positions = $tagId !== null
            ? $this->positionRepository->findClosedByTag($tagId, $isBacktest)
            : $this->positionRepository->findClosed($isBacktest);

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
}
