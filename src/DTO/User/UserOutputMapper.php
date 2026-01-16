<?php

namespace App\DTO\User;

use App\Entity\User;

class UserOutputMapper implements UserOutputMapperInterface
{
    public function fromEntity(User $user): UserOutput
    {
        $dto = new UserOutput();
        $dto->id = $user->getId();
        $dto->email = $user->getEmail();

        return $dto;
    }
}
