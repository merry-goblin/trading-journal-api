<?php

namespace App\Tests\Integration\Factory;

use App\Entity\Timeframe;
use Doctrine\ORM\EntityManagerInterface;

final class TimeframeFactory
{
    public static function create(
        EntityManagerInterface $em,
        string $label,
        int $seconds = 60
    ): Timeframe {
        $timeframe = new Timeframe();
        $timeframe->setLabel($label);
        $timeframe->setSeconds($seconds);

        $em->persist($timeframe);
        $em->flush();

        return $timeframe;
    }
}
