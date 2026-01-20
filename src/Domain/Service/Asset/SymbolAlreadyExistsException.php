<?php

namespace App\Domain\Service\Asset;

use Exception;
use App\Domain\Exception\ApiExceptionInterface;

class SymbolAlreadyExistsException extends Exception implements ApiExceptionInterface 
{
    public function getStatusCode(): int
    {
        return 409;
    }

    public function getErrorCode(): string
    {
        return 'Symbol duplication';
    }
}
