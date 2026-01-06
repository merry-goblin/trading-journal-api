<?php

namespace App\Tests\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class TestPasswordHasher
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function hash(User $user, string $plain): string
    {
        return $this->hasher->hashPassword($user, $plain);
    }
}
