<?php 

namespace App\Domain\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends DomainException
{
    private ConstraintViolationListInterface $violations;

    public function __construct(
        ConstraintViolationListInterface $violations,
        string $message = 'Validation failed'
    ) {
        parent::__construct($message);
        $this->violations = $violations;
    }

    public function getStatusCode(): int
    {
        return 422;
    }

    public function getErrorCode(): string
    {
        return 'validation_failed';
    }

    public function getViolations(): ConstraintViolationListInterface
    {
        return $this->violations;
    }
}
