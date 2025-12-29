<?php

namespace App\EventListener;

use App\Domain\Exception\ApiExceptionInterface;
use App\Domain\Exception\ValidationException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Throwable;

#[AsEventListener(event: 'kernel.exception')]
final class ApiExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        // 🔒 Only for API routing
        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $exception = $event->getThrowable();

        [$status, $error, $message, $details] = $this->normalizeException($exception);

        $event->setResponse(new JsonResponse([
            'error'   => $error,
            'message' => $message,
            'status'  => $status,
            'details' => $details,
        ], $status));
    }

    private function normalizeException(Throwable $exception): array
    {
        if ($exception instanceof ApiExceptionInterface) {
            $details = [];
            if ($exception instanceof ValidationException) {
                foreach ($exception->getViolations() as $violation) {
                    $details[$violation->getPropertyPath()] = $violation->getMessage();
                }
            }
            return [
                $exception->getStatusCode(),
                $exception->getErrorCode(),
                $exception->getMessage(),
                $details,
            ];
        }

        // 401
        if ($exception instanceof AuthenticationException) {
            return [401, 'Unauthorized', 'Authentication required', null];
        }

        // 403
        if ($exception instanceof AccessDeniedException) {
            return [403, 'Forbidden', 'Access denied', null];
        }

        // Generic HttpException (400, 405, etc.)
        if ($exception instanceof HttpExceptionInterface) {
            return [
                $exception->getStatusCode(),
                'Http Error',
                $exception->getMessage() ?: 'HTTP error',
                null,
            ];
        }

        throw $exception;// For debug purpose only: throw $exception;

        // 💥 Internal error (500)
        return [
            500,
            'Internal Error',
            'An unexpected error occurred',
            null,
        ];
    }
}
