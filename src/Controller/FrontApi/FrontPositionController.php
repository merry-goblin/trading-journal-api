<?php

namespace App\Controller\FrontApi;

use App\DTO\FrontApi\Position\PositionEnrichInputMapperInterface;
use App\Domain\Service\FrontPosition\FrontPositionServiceInterface;
use App\Domain\Service\Position\PositionServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FrontPositionController extends AbstractController
{
    #[Route('/frontApi/positions', name: 'front_positions', methods: ['GET'])]
    public function list(Request $request, FrontPositionServiceInterface $service): JsonResponse
    {
        $filters = [];
        $q = $request->query;
        if ($q->has('assetId'))      $filters['assetId']      = $q->getInt('assetId');
        if ($q->has('direction'))     $filters['direction']     = $q->get('direction');
        if ($q->has('dateFrom'))      $filters['dateFrom']      = $q->get('dateFrom');
        if ($q->has('dateTo'))        $filters['dateTo']        = $q->get('dateTo');
        if ($q->has('planRespected')) $filters['planRespected'] = $q->get('planRespected');
        if ($q->has('tagId'))         $filters['tagId']         = $q->getInt('tagId');
        if ($q->has('isBacktest'))    $filters['isBacktest'] = filter_var($q->get('isBacktest'), FILTER_VALIDATE_BOOLEAN);
        $page  = max(1, $q->getInt('page',  1));
        $limit = min(100, max(1, $q->getInt('limit', 20)));
        return $this->json($service->list($filters, $page, $limit));
    }

    #[Route('/frontApi/position/{id}', name: 'front_position_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(FrontPositionServiceInterface $service, int $id): JsonResponse
    {
        return $this->json($service->getDetail($id));
    }

    #[Route('/frontApi/position/{id}', name: 'front_position_enrich', methods: ['PATCH'], requirements: ['id' => '\\d+'])]
    public function enrich(
        Request $request,
        PositionEnrichInputMapperInterface $inputMapper,
        FrontPositionServiceInterface $service,
        int $id
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) return $this->json(['error' => 'Invalid JSON'], 400);
        return $this->json($service->enrich($id, $inputMapper->fromArray($data)));
    }

    #[Route('/frontApi/position/{id}', name: 'front_position_delete', methods: ['DELETE'], requirements: ['id' => '\\d+'])]
    public function delete(PositionServiceInterface $service, int $id): JsonResponse
    {
        $service->delete($id);
        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
