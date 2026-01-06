<?php

namespace App\Tests\Integration\Factory;

use App\Entity\User;
use App\Tests\Service\TestPasswordHasher;
use Doctrine\ORM\EntityManagerInterface;

final class UserFactory
{
    public static function create(
        EntityManagerInterface $em,
        TestPasswordHasher $passwordHasher,
        string $email,
        string $password,
        array $roles
    ): User {
        $user = new User();
        $hashedPassword = $passwordHasher->hash($user, $password);
        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setRoles($roles);

        $em->persist($user);
        $em->flush();

        return $user;
    }
}
