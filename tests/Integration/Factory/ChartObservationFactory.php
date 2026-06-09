<?php
namespace App\Tests\Integration\Factory;

use App\Entity\Asset;
use App\Entity\ChartObservation;
use App\Entity\Order;
use App\Entity\Position;
use App\Entity\Timeframe;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class ChartObservationFactory
{
    public static function create(
        EntityManagerInterface $em,
        Asset                  $asset,
        Timeframe              $timeframe,
        DateTimeImmutable      $observedAt,
        ?string                $trend      = null,
        ?string                $comment    = null,
        ?Order                 $order      = null,
        ?Position              $position   = null
    ): ChartObservation {
        $obs = new ChartObservation();
        $obs->setAsset($asset);
        $obs->setTimeframe($timeframe);
        $obs->setObservedAt($observedAt);
        $obs->setTrend($trend);
        $obs->setComment($comment);
        $obs->setOrder($order);
        $obs->setPosition($position);

        $em->persist($obs);
        $em->flush();
        return $obs;
    }
}
