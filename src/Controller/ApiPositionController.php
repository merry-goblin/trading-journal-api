<?php

namespace App\Controller;

use App\DTO\Position\PositionInputMapperInterface;
use App\DTO\Position\PositionOutputMapperInterface;
use App\Domain\Service\Position\PositionServiceInterface;
use App\Entity\Position;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ApiPositionController extends AbstractController
{
    #[Route('/api/positions', name: 'listPositions', methods: ['GET'])]
    public function list(
        PositionServiceInterface    $positionService,
        PositionOutputMapperInterface $outputMapper
    ): JsonResponse {
        $positions = $positionService->list();
        $output    = array_map(fn(Position $p) => $outputMapper->fromEntity($p), $positions);
        return $this->json($output);
    }

    #[Route('/api/position/{id}', name: 'findPositionById', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(
        PositionServiceInterface    $positionService,
        PositionOutputMapperInterface $outputMapper,
        int $id
    ): JsonResponse {
        $position = $positionService->get($id);
        $output   = $outputMapper->fromEntity($position);
        return $this->json($output);
    }

    #[Route('/api/position', name: 'createPosition', methods: ['POST'])]
    public function create(
        Request                      $request,
        PositionInputMapperInterface  $inputMapper,
        PositionOutputMapperInterface $outputMapper,
        PositionServiceInterface     $positionService
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }
        $input    = $inputMapper->fromArray($data);
        $position = $positionService->create($input);
        $output   = $outputMapper->fromEntity($position);
        return $this->json($output, 201);
    }
}
