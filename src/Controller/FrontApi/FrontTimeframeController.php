<?php

namespace App\Controller\FrontApi;

use App\Repository\Timeframe\TimeframeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class FrontTimeframeController extends AbstractController
{
    #[Route('/frontApi/timeframes', name: 'front_timeframes', methods: ['GET'])]
    public function list(TimeframeRepositoryInterface $repository): JsonResponse
    {
        $timeframes = $repository->findAll();
        return $this->json(array_map(fn($tf) => [
            'id'      => $tf->getId(),
            'label'   => $tf->getLabel(),
            'seconds' => $tf->getSeconds(),
        ], $timeframes));
    }
}
