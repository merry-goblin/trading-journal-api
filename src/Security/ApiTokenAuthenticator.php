<?php

namespace App\Security;

use App\Security\Api\ApiRequestValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private ApiRequestValidator $validator
    ) {}

    public function supports(Request $request): bool
    {
        return str_starts_with($request->getPathInfo(), '/api');
    }

    public function authenticate(Request $request): Passport
    {
        $this->validator->validate($request);

        return new SelfValidatingPassport(
            new UserBadge('api-user', fn () =>
                new InMemoryUser('api-user', null, ['ROLE_API'])
            )
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        return null;
    }

    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): JsonResponse {
        return new JsonResponse([
            'error' => 'authentication_failed',
            'message' => $exception->getMessage(),
        ], 401);
    }
}
