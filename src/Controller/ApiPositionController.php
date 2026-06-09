<?php
namespace App\Controller;

use App\DTO\Position\PositionCloseInputMapperInterface;
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
        PositionServiceInterface    $service,
        PositionOutputMapperInterface $outputMapper
    ): JsonResponse {
        $positions = $service->list();
        return $this->json(array_map(fn(Position $p) => $outputMapper->fromEntity($p), $positions));
    }

    #[Route('/api/position/{id}', name: 'findPositionById', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(
        PositionServiceInterface    $service,
        PositionOutputMapperInterface $outputMapper,
        int $id
    ): JsonResponse {
        return $this->json($outputMapper->fromEntity($service->get($id)));
    }

    #[Route('/api/position', name: 'createPosition', methods: ['POST'])]
    public function create(
        Request                      $request,
        PositionInputMapperInterface  $inputMapper,
        PositionOutputMapperInterface $outputMapper,
        PositionServiceInterface     $service
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) return $this->json(['error' => 'Invalid JSON'], 400);
        $input    = $inputMapper->fromArray($data);
        $position = $service->create($input);
        return $this->json($outputMapper->fromEntity($position), 201);
    }

    #[Route('/api/position/{id}/close', name: 'closePosition', methods: ['PATCH'], requirements: ['id' => '\\d+'])]
    public function close(
        Request                          $request,
        PositionCloseInputMapperInterface $closeMapper,
        PositionOutputMapperInterface     $outputMapper,
        PositionServiceInterface         $service,
        int $id
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) return $this->json(['error' => 'Invalid JSON'], 400);
        $input    = $closeMapper->fromArray($data);
        $position = $service->close($id, $input);
        return $this->json($outputMapper->fromEntity($position));
    }
}
