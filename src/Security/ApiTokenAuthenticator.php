<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use DateTime;
use DateTimeZone;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->getPathInfo(), '/api');
    }

    public function authenticate(Request $request): Passport
    {
        $token = $request->headers->get('X-API-TOKEN');
        $signature = $request->headers->get('X-API-SIGNATURE');
        $timestamp = $request->headers->get('X-API-TIMESTAMP');

        $this->validateApiToken($token);
        $this->validateTimestamp($timestamp);
        $this->validateHmacSignature($request, $timestamp, $signature);

        // Ici on valide le token
        return new SelfValidatingPassport(
            new UserBadge($token, function () use ($token) {
                // Vérification simple avec une clé stockée dans .env
                $validToken = $_ENV['API_TOKEN'] ?? null;

                if ($token !== $validToken) {
                    throw new AuthenticationException('Invalid API token');
                }

                // Pas besoin d'entité User, on retourne un "pseudo utilisateur"
                return new \Symfony\Component\Security\Core\User\InMemoryUser('api-user', null, ['ROLE_API']);
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?JsonResponse
    {
        return null; // Laisse la requête continuer normalement
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?JsonResponse
    {
        return new JsonResponse([
            'error' => 'Invalid API Token',
            'message' => $exception->getMessage(),
        ], 401);
    }

    private function validateApiToken(?string $requestToken): void
    {
        if (!$requestToken) {
            throw new AuthenticationException('No API token provided');
        }

        $validToken = $_ENV['API_TOKEN'] ?? null;
        if ($requestToken !== $validToken) {
            throw new AuthenticationException('Invalid API token');
        }
    }

    private function validateTimestamp(?string $requestTimestamp)
    {
        if (!$requestTimestamp) {
            throw new AuthenticationException('No timestamp provided');
        }

        $requestDateTime = DateTime::createFromFormat('Y.m.d H:i:s', $requestTimestamp);
        $now = new DateTime('now', new DateTimeZone('UTC'));

        $diff = abs($now->getTimestamp() - $requestDateTime->getTimestamp());
        if ($diff > 30) {
            throw new AuthenticationException('Timestamp is invalid');
        }
    }

    private function validateHmacSignature(Request $request, string $requestTimestamp, ?string $requestSignature): void
    {
        if (!$requestSignature) {
            throw new AuthenticationException('No Hmac signature provided');
        }

        // HMAC Calculation
        $hmacSecretKey = $_ENV['HMAC_SECRET_KEY'] ?? null;
        $canonical = $requestTimestamp
            . "\n" . $request->getMethod()
            . "\n" . $request->getPathInfo()
            . "\n" . $request->getContent();

        // Hash
        $signature = hash_hmac('sha256', $canonical, $hmacSecretKey);
        if (!hash_equals($signature, $requestSignature)) {
            throw new AuthenticationException("Invalid signature");
        }
    }
}
