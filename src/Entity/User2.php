<?php

namespace App\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class User2 implements UserInterface, PasswordAuthenticatedUserInterface
{
    private ?int $id = null;
    private string $email;
    private array $roles = ['ROLE_USER'];
    private string $password;

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function eraseCredentials(): void {}

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
