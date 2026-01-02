<?php

namespace App\Controller\FrontApi;

use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController
{
    #[Route('/frontApi/login', name: 'front_api_login', methods: ['POST'])]
    public function login(): Response
    {
        // Cette méthode ne sera jamais appelée
        throw new LogicException('This method should never be reached.');
    }
}
