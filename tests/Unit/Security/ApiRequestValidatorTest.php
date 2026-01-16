<?php

namespace App\Tests\Unit\Security;

use App\Security\Api\ApiRequestValidator;
use App\Security\Api\ClockInterface;
use App\Security\Api\Exception\InvalidSignatureException;
use App\Security\Api\Exception\InvalidTimestampException;
use App\Security\Api\Exception\InvalidTokenException;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ApiRequestValidatorTest extends TestCase
{
    private const API_TOKEN = 'test-api-token';
    private const HMAC_SECRET = 'super-secret';

    /* ---------------------------------
     * SUCCESS
     * --------------------------------- */

    public function testValidateReturnsSuccessWithValidParameters(): void
    {
        // Mock data
        $request = $this->createValidRequest();
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        // Dependency injections
        $clockSystem = $this->createMock(ClockInterface::class);
        $clockSystem->expects(self::once())
            ->method('now')
            ->willReturn($now)
        ;

        // Start test
        $validator = new ApiRequestValidator(self::API_TOKEN, self::HMAC_SECRET, $clockSystem, 30);
        $validator->validate($request);

        // Assertions
        $this->assertTrue(true); // no exception
    }

    /* ---------------------------------
     * TOKEN
     * --------------------------------- */

    public function testValidateReturnsExceptionWithMissingApiToken(): void
    {
        // Mock data
        $request = $this->createValidRequest();
        $request->headers->remove('X-API-TOKEN');

        // Dependency injections
        $clockSystem = $this->createStub(ClockInterface::class);

        // Start test
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('Invalid API token');

        // Start test
        $validator = new ApiRequestValidator(self::API_TOKEN, self::HMAC_SECRET, $clockSystem, 30);
        $validator->validate($request);
    }

    public function testReturnsExceptionWithInvalidApiToken(): void
    {
        // Mock data
        $request = $this->createValidRequest();
        $request->headers->set('X-API-TOKEN', 'wrong-token');

        // Dependency injections
        $clockSystem = $this->createStub(ClockInterface::class);

        // Assertions
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('Invalid API token');

        // Start test
        $validator = new ApiRequestValidator(self::API_TOKEN, self::HMAC_SECRET, $clockSystem, 30);
        $validator->validate($request);
    }

    /* ---------------------------------
     * TIMESTAMP
     * --------------------------------- */

    public function testValidateReturnsExceptionWithMissingTimestamp(): void
    {
        // Mock data
        $request = $this->createValidRequest();
        $request->headers->remove('X-API-TIMESTAMP');

        // Dependency injections
        $clockSystem = $this->createStub(ClockInterface::class);

        // Assertions
        $this->expectException(InvalidTimestampException::class);
        $this->expectExceptionMessage('Missing timestamp');

        // Start test
        $validator = new ApiRequestValidator(self::API_TOKEN, self::HMAC_SECRET, $clockSystem, 30);
        $validator->validate($request);
    }

    public function testReturnsExceptionWithInvalidTimestampFormat(): void
    {
        // Mock data
        $request = $this->createValidRequest();
        $request->headers->set('X-API-TIMESTAMP', 'invalid-date');

        // Dependency injections
        $clockSystem = $this->createStub(ClockInterface::class);

        // Assertions
        $this->expectException(InvalidTimestampException::class);
        $this->expectExceptionMessage('Invalid timestamp format');

        // Start test
        $validator = new ApiRequestValidator(self::API_TOKEN, self::HMAC_SECRET, $clockSystem, 30);
        $validator->validate($request);
    }

    public function testReturnsExceptionWithExpiredTimestamp(): void
    {
        // Mock data
        $request = $this->createValidRequest();
        $oldTimestamp = gmdate('Y.m.d H:i:s', time() - 100);
        $request->headers->set('X-API-TIMESTAMP', $oldTimestamp);
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        // Dependency injections
        $clockSystem = $this->createMock(ClockInterface::class);
        $clockSystem->expects(self::once())
            ->method('now')
            ->willReturn($now)
        ;

        // Assertions
        $this->expectException(InvalidTimestampException::class);
        $this->expectExceptionMessage('Timestamp expired');

        // Start test
        $validator = new ApiRequestValidator(self::API_TOKEN, self::HMAC_SECRET, $clockSystem, 30);
        $validator->validate($request);
    }

    /* ---------------------------------
     * SIGNATURE
     * --------------------------------- */

    public function testValidateReturnsExceptionWithMissingSignature(): void
    {
        // Mock data
        $request = $this->createValidRequest();
        $request->headers->remove('X-API-SIGNATURE');
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        // Dependency injections
        $clockSystem = $this->createMock(ClockInterface::class);
        $clockSystem->expects(self::once())
            ->method('now')
            ->willReturn($now)
        ;

        // Assertions
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Missing signature');

        // Start test
        $validator = new ApiRequestValidator(self::API_TOKEN, self::HMAC_SECRET, $clockSystem, 30);
        $validator->validate($request);
    }

    public function testInvalidSignature(): void
    {
        // Mock data
        $request = $this->createValidRequest();
        $request->headers->set('X-API-SIGNATURE', 'bad-signature');
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        // Dependency injections
        $clockSystem = $this->createMock(ClockInterface::class);
        $clockSystem->expects(self::once())
            ->method('now')
            ->willReturn($now)
        ;

        // Assertions
        $this->expectException(InvalidSignatureException::class);
        $this->expectExceptionMessage('Invalid HMAC signature');

        // Start test
        $validator = new ApiRequestValidator(self::API_TOKEN, self::HMAC_SECRET, $clockSystem, 30);
        $validator->validate($request);
    }

    /* ---------------------------------
     * HELPERS
     * --------------------------------- */

    private function createValidRequest(): Request
    {
        $timestamp = gmdate('Y.m.d H:i:s');

        $request = new Request(
            [],
            [],
            [],
            [],
            [],
            [
                'REQUEST_METHOD' => 'POST',
                'REQUEST_URI' => '/api/test',
            ],
            '{"foo":"bar"}'
        );

        $canonical = $timestamp."\nPOST\n/api/test\n".$request->getContent();
        $signature = hash_hmac('sha256', $canonical, self::HMAC_SECRET);

        $request->headers->set('X-API-TOKEN', self::API_TOKEN);
        $request->headers->set('X-API-TIMESTAMP', $timestamp);
        $request->headers->set('X-API-SIGNATURE', $signature);

        return $request;
    }
}
