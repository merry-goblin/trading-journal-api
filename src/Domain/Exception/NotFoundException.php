<?php

namespace App\Domain\Exception;

abstract class NotFoundException extends DomainException
{
    public function getStatusCode(): int
    {
        return 404;
    }

    public function getErrorCode(): string
    {
        return 'Not Found';
    }
}
