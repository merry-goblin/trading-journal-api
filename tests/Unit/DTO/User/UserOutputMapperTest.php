<?php

namespace App\Tests\Unit\DTO\User;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

use App\DTO\User\UserOutput;
use App\DTO\User\UserOutputMapper;

class UserOutputMapperTest extends TestCase
{
    /* fromEntity method */

    public function testFromEntityWithStandardEntity(): void
    {
        // Mock data
        $entity = $this->createUser(1, 'test@example.com', 'password123', ['ROLE_USER']);

        // Start test
        $userOutputMapper = new UserOutputMapper();
        $userOutput = $userOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(UserOutput::class, $userOutput);
        $this->assertSame(1, $userOutput->id);
        $this->assertSame('test@example.com', $userOutput->email);
    }

    public function testFromEntityWithEmptyEntity(): void
    {
        // Mock data
        $entity = $this->createUser(0, '', '', []);

        // Start test
        $userOutputMapper = new UserOutputMapper();
        $userOutput = $userOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(UserOutput::class, $userOutput);
        $this->assertSame(0, $userOutput->id);
        $this->assertSame('', $userOutput->email);

    }

    public function testFromEntityWithWeirdValues(): void
    {
        // Mock data
        $entity = $this->createUser(-123, true, '', []);

        // Start test
        $userOutputMapper = new UserOutputMapper();
        $userOutput = $userOutputMapper->fromEntity($entity);

        // Assertions
        $this->assertInstanceOf(UserOutput::class, $userOutput);
        $this->assertSame(-123, $userOutput->id);
        $this->assertSame('1', $userOutput->email);
    }

    /* private methods */

    private function createUser(
        int $id,
        string $email,
        string $password,
        array $roles
    ): User {
        $user = new User();
        $user->setId($id);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setRoles($roles);

        return $user;
    }
}
