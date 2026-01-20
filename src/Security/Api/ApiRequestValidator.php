<?php

namespace App\Security\Api;

use App\Security\Api\Exception\InvalidSignatureException;
use App\Security\Api\Exception\InvalidTimestampException;
use App\Security\Api\Exception\InvalidTokenException;
use Symfony\Component\HttpFoundation\Request;

use DateTimeImmutable;

final class ApiRequestValidator
{
    public function __construct(
        private readonly string $apiToken,
        private readonly string $hmacSecret,
        private readonly ClockInterface $clock,
        private readonly int $timestampTolerance = 30
    ) {}

    public function validate(Request $request): void
    {
        $token = $request->headers->get('X-API-TOKEN');
        $timestamp = $request->headers->get('X-API-TIMESTAMP');
        $signature = $request->headers->get('X-API-SIGNATURE');

        $this->validateToken($token);
        $this->validateTimestamp($timestamp);
        $this->validateSignature($request, $timestamp, $signature);
    }

    private function validateToken(?string $token): void
    {
        if (!$token || !hash_equals($this->apiToken, $token)) {
            throw new InvalidTokenException('Invalid API token');
        }
    }

    private function validateTimestamp(?string $timestamp): void
    {
        if (!$timestamp) {
            throw new InvalidTimestampException('Missing timestamp');
        }

        $date = DateTimeImmutable::createFromFormat('Y.m.d H:i:s', $timestamp);
        if (!$date) {
            throw new InvalidTimestampException('Invalid timestamp format');
        }

        $diff = abs($this->clock->now()->getTimestamp() - $date->getTimestamp());
        if ($diff > $this->timestampTolerance) {
            throw new InvalidTimestampException('Timestamp expired');
        }
    }

    private function validateSignature(
        Request $request,
        string $timestamp,
        ?string $signature
    ): void {
        if (!$signature) {
            throw new InvalidSignatureException('Missing signature');
        }

        $canonical = implode("\n", [
            $timestamp,
            $request->getMethod(),
            $request->getPathInfo(),
            $request->getContent(),
        ]);

        $expected = hash_hmac('sha256', $canonical, $this->hmacSecret);
        
        if (!hash_equals($expected, $signature)) {
            throw new InvalidSignatureException('Invalid HMAC signature');
        }
    }
}
