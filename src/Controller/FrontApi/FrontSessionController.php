<?php

namespace App\Controller\FrontApi;

use App\Domain\Service\Session\SessionServiceInterface;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class FrontSessionController extends AbstractController
{
    #[Route('/frontApi/session/{date}', name: 'front_session_get', methods: ['GET'])]
    public function get(string $date, SessionServiceInterface $service): JsonResponse
    {
        try {
            $d = new DateTimeImmutable($date);
        } catch (\Throwable) {
            return $this->json(['error' => 'Format de date invalide (YYYY-MM-DD)'], 400);
        }
        return $this->json($service->getOrEmpty($d));
    }

    #[Route('/frontApi/session/{date}', name: 'front_session_update', methods: ['PATCH'])]
    public function update(string $date, Request $request, SessionServiceInterface $service): JsonResponse
    {
        try {
            $d = new DateTimeImmutable($date);
        } catch (\Throwable) {
            return $this->json(['error' => 'Format de date invalide (YYYY-MM-DD)'], 400);
        }
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) return $this->json(['error' => 'Invalid JSON'], 400);
        return $this->json($service->update($d, $data));
    }
}
