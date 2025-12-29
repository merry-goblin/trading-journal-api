<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

use App\DTO\Timeframe\TimeframeInputMapperInterface;
use App\DTO\Timeframe\TimeframeOutputMapperInterface;
use App\Entity\Timeframe;
use App\Service\Timeframe\TimeframeServiceInterface;

final class ApiTimeframeController extends AbstractController
{
    #[Route('/api/timeframes', name: 'listTimeframes', methods: ['GET'])]
    public function list(
        TimeframeServiceInterface $timeframeService,
        TimeframeOutputMapperInterface $outputMapper): JsonResponse
    {
        $timeframes = $timeframeService->list();

        // Response
        $output = array_map(fn(Timeframe $timeframe) => $outputMapper->fromEntity($timeframe), $timeframes);
        return $this->json($output);
    }

    #[Route('/api/timeframe/{id}', name: 'findTimeframeById', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(
        TimeframeServiceInterface $timeframeService,
        TimeframeOutputMapperInterface $outputMapper,
        int $id): JsonResponse
    {
        $timeframe = $timeframeService->get($id);

        // Response
        $output = $outputMapper->fromEntity($timeframe);
        return $this->json($output);
    }

    #[Route('/api/timeframe/label/{symbol}', name: 'findTimeframeByLabel', methods: ['GET'], requirements: ['label' => '[a-zA-Z_]\w+'])]
    public function showByLabel(
        TimeframeServiceInterface $timeframeService,
        TimeframeOutputMapperInterface $outputMapper,
        string $symbol): JsonResponse
    {
        $timeframe = $timeframeService->getByLabel($symbol);

        // Response
        $output = $outputMapper->fromEntity($timeframe);
        return $this->json($output);
    }

    #[Route('/api/timeframe', name: 'createTimeframe', methods: ['POST'])]
    public function create(
        Request $request,
        TimeframeInputMapperInterface $inputMapper,
        TimeframeOutputMapperInterface $outputMapper,
        TimeframeServiceInterface $timeframeService): JsonResponse
    {
        // Input data
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return $this->json(['error' => 'Invalid JSON'], 400);
        }
        $input = $inputMapper->fromArray($data);

        // Entity creation
        $timeframe = $timeframeService->create($input);

        // Response
        $output = $outputMapper->fromEntity($timeframe);
        return $this->json($output);
    }
}
