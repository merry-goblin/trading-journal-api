<?php

namespace App\Tests\Integration;

trait ApiTestAuthTrait
{
    protected function getAuthHeaders(
        string $method,
        string $path,
        string $content = ''
    ): array 
    {
        $timestamp = (new \DateTime('now', new \DateTimeZone('UTC')))
            ->format('Y.m.d H:i:s');

        $canonical = $timestamp
            . "\n" . $method
            . "\n" . $path
            . "\n" . $content;

        $signature = hash_hmac(
            'sha256',
            $canonical,
            $_ENV['HMAC_SECRET_KEY']
        );

        return [
            'HTTP_X_API_TOKEN' => $_ENV['API_TOKEN'],
            'HTTP_X_API_TIMESTAMP' => $timestamp,
            'HTTP_X_API_SIGNATURE' => $signature,
        ];
    }
}
