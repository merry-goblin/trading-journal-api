<?php

namespace App\Controller\FrontApi;

use App\Domain\Service\Stats\StatsServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class FrontStatsController extends AbstractController
{
    /**
     * Construit le tableau de filtres commun a partir de la requete.
     * isBacktest : null = tous, false = live, true = backtest.
     */
    private function buildFilters(Request $r): array
    {
        $filters = [];

        // isBacktest : parametre absent => false (live par defaut)
        //              parametre present => null si valeur vide (tous), sinon bool
        if ($r->query->has('isBacktest')) {
            $raw = $r->query->get('isBacktest');
            $filters['isBacktest'] = ($raw === '' || $raw === 'null')
                ? null
                : filter_var($raw, FILTER_VALIDATE_BOOLEAN);
        } else {
            $filters['isBacktest'] = false;
        }

        if ($r->query->has('tagId') && $r->query->get('tagId') !== '')
            $filters['tagId'] = $r->query->getInt('tagId');

        if ($r->query->has('direction') && $r->query->get('direction') !== '')
            $filters['direction'] = $r->query->get('direction');

        if ($r->query->has('planRespected') && $r->query->get('planRespected') !== '') {
            $pr = $r->query->get('planRespected');
            $filters['planRespected'] = filter_var($pr, FILTER_VALIDATE_BOOLEAN);
        }

        if ($r->query->has('dateFrom') && $r->query->get('dateFrom') !== '')
            $filters['dateFrom'] = $r->query->get('dateFrom');

        if ($r->query->has('dateTo') && $r->query->get('dateTo') !== '')
            $filters['dateTo'] = $r->query->get('dateTo');

        return $filters;
    }

    #[Route('/frontApi/stats', name: 'front_stats', methods: ['GET'])]
    public function global(Request $request, StatsServiceInterface $service): JsonResponse
    {
        return $this->json($service->getGlobalStats($this->buildFilters($request)));
    }

    #[Route('/frontApi/stats/by-tag', name: 'front_stats_by_tag', methods: ['GET'])]
    public function byTag(Request $request, StatsServiceInterface $service): JsonResponse
    {
        return $this->json($service->getStatsByTag($this->buildFilters($request)));
    }

    #[Route('/frontApi/equity', name: 'front_equity', methods: ['GET'])]
    public function equity(Request $request, StatsServiceInterface $service): JsonResponse
    {
        return $this->json($service->getEquityCurve($this->buildFilters($request)));
    }

    #[Route('/frontApi/stats/rr-distribution', name: 'front_stats_rr', methods: ['GET'])]
    public function rrDistribution(Request $request, StatsServiceInterface $service): JsonResponse
    {
        return $this->json($service->getRRDistribution($this->buildFilters($request)));
    }

    #[Route('/frontApi/stats/temporal', name: 'front_stats_temporal', methods: ['GET'])]
    public function temporal(Request $request, StatsServiceInterface $service): JsonResponse
    {
        return $this->json($service->getTemporalStats($this->buildFilters($request)));
    }
}
