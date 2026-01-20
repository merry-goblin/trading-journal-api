<?php

namespace App\Domain\Service\Timeframe;

use Exception;

class LabelAlreadyExistsException extends Exception 
{
    public function getStatusCode(): int
    {
        return 409;
    }

    public function getErrorCode(): string
    {
        return 'Label duplication';
    }
}
