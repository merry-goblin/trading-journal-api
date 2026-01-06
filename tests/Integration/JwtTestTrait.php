<?php

namespace App\Tests\Integration;

use Symfony\Component\HttpFoundation\Response;

trait JwtTestTrait
{
    protected string $jwtToken;

    protected function authenticateAsUser(
        string $email = 'test@example.com',
        string $password = 'password123'
    ): void {
        $this->requestUrl(
            'POST',
            '/frontApi/login',
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_ACCEPT' => 'application/json',
            ],
            json_encode([
                'email' => $email,
                'password' => $password,
            ])
        );

        $data = $this->assertJsonResponse(Response::HTTP_OK);
        $this->jwtToken = $data['token'];
    }

    protected function getJwtAuthHeaders(): array
    {
        return [
            'HTTP_AUTHORIZATION' => 'Bearer ' . $this->jwtToken,
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];
    }
}
