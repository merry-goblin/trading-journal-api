<?php

namespace App\Tests\E2E;

trait E2EAuthTrait
{
    protected function authHeaders(
        string $method,
        string $uri,
        string $body = ''
    ): array {
        $timestamp = time();
        $token = 'test-token';
        $secret = 'test-secret';

        $signature = hash_hmac(
            'sha256',
            strtoupper($method) . $uri . $timestamp . $body,
            $secret
        );

        return [
            'HTTP_X_API_TOKEN' => $token,
            'HTTP_X_API_TIMESTAMP' => (string) $timestamp,
            'HTTP_X_API_SIGNATURE' => $signature,
        ];
    }
}
