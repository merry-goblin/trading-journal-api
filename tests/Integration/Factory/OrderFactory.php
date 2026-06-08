<?php

namespace App\Tests\Integration\Factory;

use App\Entity\Order;
use App\Entity\Asset;
use App\Entity\Timeframe;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class OrderFactory
{
    public static function create(
        EntityManagerInterface $em,
        Asset $asset,
        Timeframe $timeframe,
        DateTimeImmutable $createdAt,
        string $orderType,
        string $direction,
        string $price,
        string $size,
        string $stopLoss,
        string $takeProfit,
        string $status,
        string $comment,
    ): Order {
        $order = new Order();
        $order->setAsset($asset);
        $order->setTimeframe($timeframe);
        $order->setCreatedAt($createdAt);
        $order->setOrderType($orderType);
        $order->setDirection($direction);
        $order->setPrice($price);
        $order->setSize($size);
        $order->setStopLoss($stopLoss);
        $order->setTakeProfit($takeProfit);
        $order->setStatus($status);
        $order->setComment($comment);

        $em->persist($order);
        $em->flush();

        return $order;
    }
}
