<?php

namespace App\Tests\Integration\FrontApi;

use App\Tests\Integration\AbstractTestApiController;
use App\Tests\Integration\Factory\UserFactory;
use App\Tests\Service\TestPasswordHasher;

class AuthControllerTest extends AbstractTestApiController
{
    public function testLoginSuccessReturnsJWTToken(): void
    {
        // Fake DB data
        $hasher = self::getContainer()->get(TestPasswordHasher::class);
        UserFactory::create($this->em, $hasher, 'test@example.com', 'password123', ['ROLE_USER']);

        // Start test
        $method = 'POST';
        $path = '/frontApi/login';
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];
        $jsonContent = json_encode([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $this->requestUrl($method, $path, $headers, $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse();
        $this->assertArrayHasKey('token', $data);
    }

    public function testLoginFailureReturns401(): void
    {
        // Fake DB data
        $hasher = self::getContainer()->get(TestPasswordHasher::class);
        UserFactory::create($this->em, $hasher, 'test@example.com', 'password123', ['ROLE_USER']);

        // Start test
        $method = 'POST';
        $path = '/frontApi/login';
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];
        $jsonContent = json_encode([
            'email' => 'test@example.com',
            'password' => 'wrong123',
        ]);
        $this->requestUrl($method, $path, $headers, $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse(401);
        $this->assertSame(401, $data['code']);
        $this->assertSame('Invalid credentials.', $data['message']);
    }

}
