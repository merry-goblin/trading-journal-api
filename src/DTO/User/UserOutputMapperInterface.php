<?php

namespace App\DTO\User;

use App\Entity\User;

interface UserOutputMapperInterface
{
    public function fromEntity(User $user): UserOutput;
}
