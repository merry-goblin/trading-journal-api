<?php

namespace App\DTO\User;

use Symfony\Component\Validator\Constraints as Assert;

class UserOutput
{
    public int $id;

    #[Assert\NotBlank]
    #[Assert\Length(min: 7, max: 180)]
    public string $email;
}
