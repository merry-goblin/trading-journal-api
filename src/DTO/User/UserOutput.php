<?php

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserOutput
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 7, max: 180)]
    public string $email;

    #[Assert\NotNull]
    #[Assert\Length(min: 1, max: 255)]
    public string $password;

    #[Assert\NotBlank]
    public string $roles;
}
