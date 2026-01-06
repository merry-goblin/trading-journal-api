<?php

namespace App\Controller\FrontApi;

use App\DTO\User\UserOutputMapperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MeController extends AbstractController
{
    public function __construct(
        private TokenStorageInterface $tokenStorage
    ) {}

    #[Route('/frontApi/me', name: 'front_api_me', methods: ['GET'])]
    public function me(UserOutputMapperInterface $outputMapper): JsonResponse
    {
        $user = $this->tokenStorage->getToken()?->getUser();

        // Response
        $output = $user ? $outputMapper->fromEntity($user) : null;
        return $this->json($output);
    }
}
