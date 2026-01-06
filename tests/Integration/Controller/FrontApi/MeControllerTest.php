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

    public function testMe(): void
    {
        // Fake DB data
        $hasher = self::getContainer()->get(TestPasswordHasher::class);
        UserFactory::create($this->em, $hasher, 'test@example.com', 'password123', ['ROLE_USER']);
        $this->authenticateAsUser('test@example.com', 'password123');

        // Start test
        $method = 'GET';
        $path = '/frontApi/me';
        $headers = $this->getJwtAuthHeaders();
        $jsonContent = json_encode([
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $this->requestUrl($method, $path, $headers, $jsonContent);

        // Assertions
        $data = $this->assertJsonResponse(Response::HTTP_OK);
        var_dump($data);
        //$this->assertArrayHasKey('email', $data);
    }

}
