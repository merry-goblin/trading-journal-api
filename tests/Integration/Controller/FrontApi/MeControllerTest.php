<?php

namespace App\Tests\Integration\Controller\FrontApi;

use App\Tests\Integration\AbstractTestApiController;
use App\Tests\Integration\Factory\UserFactory;
use App\Tests\Integration\JwtTestTrait;
use App\Tests\Service\TestPasswordHasher;
use Symfony\Component\HttpFoundation\Response;

class MeControllerTest extends AbstractTestApiController
{
    use JwtTestTrait;

    /* me */

    public function testAuthenticatedUserCanAccessMeEndpoint(): void
    {
        // Fake DB data
        $hasher = self::getContainer()->get(TestPasswordHasher::class);
        UserFactory::create($this->em, $hasher, 'test@example.com', 'password123', ['ROLE_USER']);
        $this->authenticateAsUser('test@example.com', 'password123');

        // Start test
        $method = 'GET';
        $path = '/frontApi/me';
        $headers = $this->getJwtAuthHeaders();
        $this->requestUrl($method, $path, $headers);

        // Assertions
        $data = $this->assertJsonResponse(Response::HTTP_OK);
        $this->assertArrayHasKey('id', $data);
        $this->assertIsInt($data['id']);
        $this->assertSame('test@example.com', $data['email']);
    }

    public function testMeRequiresAuthentication(): void
    {
        // Start test
        $method = 'GET';
        $path = '/frontApi/me';
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
        ];
        $this->requestUrl($method, $path, $headers);

        // Assertions
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
