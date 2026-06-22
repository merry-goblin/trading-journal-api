<?php

namespace App\Controller\FrontApi;

use App\Domain\Service\Stats\StatsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class FrontStatsController extends AbstractController
{
    #[Route('/frontApi/stats', name: 'front_stats', methods: ['GET'])]
    public function global(Request $request, StatsServiceInterface $service): JsonResponse
    {
        $q         = $request->query;
        $tagId     = $q->has('tagId')     ? $q->getInt('tagId')  : null;
        $isBacktest= $q->has('isBacktest') ? filter_var($q->get('isBacktest'), FILTER_VALIDATE_BOOLEAN) : false;
        return $this->json($service->getGlobalStats($tagId ?: null, $isBacktest));
    }

    #[Route('/frontApi/stats/by-tag', name: 'front_stats_by_tag', methods: ['GET'])]
    public function byTag(StatsServiceInterface $service): JsonResponse
    {
        return $this->json($service->getStatsByTag());
    }

    #[Route('/frontApi/equity', name: 'front_equity', methods: ['GET'])]
    public function equity(Request $request, StatsServiceInterface $service): JsonResponse
    {
        $q         = $request->query;
        $tagId     = $q->has('tagId')     ? $q->getInt('tagId')  : null;
        $isBacktest= $q->has('isBacktest') ? filter_var($q->get('isBacktest'), FILTER_VALIDATE_BOOLEAN) : false;
        return $this->json($service->getEquityCurve($tagId ?: null, $isBacktest));
    }
}
