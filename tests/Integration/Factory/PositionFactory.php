<?php

namespace App\Tests\Integration\Factory;

use App\Entity\Asset;
use App\Entity\Order;
use App\Entity\Position;
use App\Entity\Timeframe;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class PositionFactory
{
    public static function create(
        EntityManagerInterface $em,
        Asset                  $asset,
        Timeframe              $timeframe,
        DateTimeImmutable      $openedAt,
        string                 $entryPrice,
        string                 $volume,
        ?string                $direction    = null,
        ?DateTimeImmutable     $closedAt     = null,
        ?string                $exitPrice    = null,
        ?string                $stopLoss     = null,
        ?string                $takeProfit   = null,
        ?Order                 $originOrder  = null,
        ?string                $riskAmount   = null,
        ?string                $pnl          = null,
        ?string                $pnlPercent   = null,
        ?string                $rr           = null,
        ?string                $comment      = null
    ): Position {
        $position = new Position();
        $position->setAsset($asset);
        $position->setTimeframe($timeframe);
        $position->setOpenedAt($openedAt);
        $position->setEntryPrice($entryPrice);
        $position->setVolume($volume);
        $position->setDirection($direction);
        $position->setClosedAt($closedAt);
        $position->setExitPrice($exitPrice);
        $position->setStopLoss($stopLoss);
        $position->setTakeProfit($takeProfit);
        $position->setOriginOrder($originOrder);
        $position->setRiskAmount($riskAmount);
        $position->setPnl($pnl);
        $position->setPnlPercent($pnlPercent);
        $position->setRr($rr);
        $position->setComment($comment);

        $em->persist($position);
        $em->flush();

        return $position;
    }
}
