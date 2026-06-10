<?php

namespace App\Tests\Integration\Factory;

use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;

final class TagFactory
{
    public static function create(
        EntityManagerInterface $em,
        string $label,
        string $type = 'setup',
        string $description = ''
    ): Tag {
        $tag = new Tag();
        $tag->setLabel($label);
        $tag->setType($type);
        $tag->setDescription($description);

        $em->persist($tag);
        $em->flush();

        return $tag;
    }
}
