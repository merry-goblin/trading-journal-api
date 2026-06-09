<?php
namespace App\Controller\FrontApi;
use App\Domain\Service\Stats\StatsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
final class FrontStatsController extends AbstractController
{
    #[Route('/frontApi/stats', name: 'front_stats', methods: ['GET'])]
    public function global(StatsServiceInterface $service): JsonResponse
    {
        return $this->json($service->getGlobalStats());
    }

    #[Route('/frontApi/stats/by-tag', name: 'front_stats_by_tag', methods: ['GET'])]
    public function byTag(StatsServiceInterface $service): JsonResponse
    {
        return $this->json($service->getStatsByTag());
    }
}
