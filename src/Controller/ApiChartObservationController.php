<?php
namespace App\Controller;

use App\DTO\ChartObservation\ChartObservationInputMapperInterface;
use App\DTO\ChartObservation\ChartObservationOutputMapperInterface;
use App\Domain\Service\ChartObservation\ChartObservationServiceInterface;
use App\Entity\ChartObservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ApiChartObservationController extends AbstractController
{
    #[Route('/api/chart-observations', name: 'listObservations', methods: ['GET'])]
    public function list(
        ChartObservationServiceInterface    $service,
        ChartObservationOutputMapperInterface $outputMapper
    ): JsonResponse {
        $obs = $service->list();
        return $this->json(array_map(fn(ChartObservation $o) => $outputMapper->fromEntity($o), $obs));
    }

    #[Route('/api/chart-observation/{id}', name: 'findObservationById', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(
        ChartObservationServiceInterface    $service,
        ChartObservationOutputMapperInterface $outputMapper,
        int $id
    ): JsonResponse {
        return $this->json($outputMapper->fromEntity($service->get($id)));
    }

    #[Route('/api/chart-observation', name: 'createObservation', methods: ['POST'])]
    public function create(
        Request                              $request,
        ChartObservationInputMapperInterface  $inputMapper,
        ChartObservationOutputMapperInterface $outputMapper,
        ChartObservationServiceInterface     $service
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!$data) return $this->json(['error' => 'Invalid JSON'], 400);
        $input = $inputMapper->fromArray($data);
        $obs   = $service->create($input);
        return $this->json($outputMapper->fromEntity($obs), 201);
    }
}
