<?php

namespace App\Controller\FrontApi;

use App\Domain\Exception\ValidationException\ChartObservationValidationException;
use App\Domain\Service\ChartObservation\ChartObservationServiceInterface;
use App\DTO\ChartObservation\ChartObservationInput;
use App\DTO\FrontApi\Observation\FrontObservationCreateInputMapperInterface;
use App\DTO\FrontApi\Position\FrontScreenshotOutput;
use App\Entity\ChartObservation;
use App\Repository\ChartObservation\ChartObservationRepositoryInterface;
use App\Repository\Order\OrderRepositoryInterface;
use App\Repository\Position\PositionRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class FrontObservationController extends AbstractController
{
    #[Route('/frontApi/observations', name: 'front_observations', methods: ['GET'])]
    public function list(
        Request $request,
        ChartObservationRepositoryInterface $repository
    ): JsonResponse {
        $q = $request->query;
        $filters = [];
        if ($q->has('assetId'))  $filters['assetId']  = $q->getInt('assetId');
        if ($q->has('dateFrom')) $filters['dateFrom'] = $q->get('dateFrom');
        if ($q->has('dateTo'))   $filters['dateTo']   = $q->get('dateTo');
        if ($q->has('trend'))    $filters['trend']    = $q->get('trend');
        if ($q->has('type'))     $filters['type']     = $q->get('type');

        $page  = max(1, $q->getInt('page',  1));
        $limit = min(50, max(1, $q->getInt('limit', 20)));

        return $this->json(array_map(
            fn(ChartObservation $obs) => $this->toOutput($obs),
            $repository->findWithFilters($filters, $page, $limit)
        ));
    }

    #[Route('/frontApi/observation/{id}', name: 'front_observation_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(
        int $id,
        ChartObservationRepositoryInterface $repository
    ): JsonResponse {
        $obs = $repository->find($id);
        if (!$obs) return $this->json(['error' => 'Not Found'], 404);
        return $this->json($this->toOutput($obs));
    }

    #[Route('/frontApi/observation', name: 'front_observation_create', methods: ['POST'])]
    public function create(
        Request $request,
        FrontObservationCreateInputMapperInterface $inputMapper,
        ChartObservationServiceInterface $service,
        PositionRepositoryInterface $positionRepository,
        OrderRepositoryInterface $orderRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) return $this->json(['error' => 'Invalid JSON'], 400);

        $input = $inputMapper->fromArray($data);

        if (!$input->positionId && !$input->orderId)
            return $this->json(['error' => 'positionId ou orderId requis'], 422);

        // Inférer assetId et timeframeId depuis la position ou l'ordre
        $assetId     = null;
        $timeframeId = null;

        if ($input->positionId) {
            $position    = $positionRepository->find($input->positionId);
            $assetId     = $position?->getAsset()?->getId();
            $timeframeId = $position?->getTimeframe()?->getId();
        } elseif ($input->orderId) {
            $order       = $orderRepository->find($input->orderId);
            $assetId     = $order?->getAsset()?->getId();
            $timeframeId = $order?->getTimeframe()?->getId();
        }

        if (!$assetId || !$timeframeId)
            return $this->json(['error' => 'Position ou ordre introuvable'], 404);

        // Réutiliser ChartObservationService::create()
        $obsInput = new ChartObservationInput();
        $obsInput->assetId     = $assetId;
        $obsInput->timeframeId = $timeframeId;
        $obsInput->observedAt  = $input->observedAt;
        $obsInput->trend       = $input->trend;
        $obsInput->comment     = $input->comment;
        $obsInput->positionId  = $input->positionId;
        $obsInput->orderId     = $input->orderId;
        $obsInput->imageData   = $input->imageData;
        $obsInput->imageMime   = $input->imageMime;
        $obsInput->periodStart = $input->periodStart;
        $obsInput->periodEnd   = $input->periodEnd;

        try {
            $observation = $service->create($obsInput);
        } catch (ChartObservationValidationException $e) {
            return $this->json([
                'error'   => 'validation_failed',
                'details' => (string) $e->getMessage(),
            ], 422);
        }

        return $this->json($this->toOutput($observation), 201);
    }

    // ── Mapper privé ──────────────────────────────────────────────

    private function toOutput(ChartObservation $obs): array
    {
        $orderId    = $obs->getOrder()?->getId();
        $positionId = $obs->getPosition()?->getId();

        $context = match (true) {
            $positionId !== null => 'position',
            $orderId !== null    => 'order',
            default              => 'free',
        };

        $screenshots = array_map(function ($s) {
            $sc = new FrontScreenshotOutput();
            $sc->id          = $s->getId();
            $sc->filePath    = $s->getFilePath();
            $sc->createdAt   = $s->getCreatedAt()?->format('Y-m-d H:i:s');
            $sc->source      = $s->getSource();
            $sc->periodStart = $s->getPeriodStart()?->format('Y-m-d H:i:s');
            $sc->periodEnd   = $s->getPeriodEnd()?->format('Y-m-d H:i:s');
            $sc->description = $s->getDescription();
            return $sc;
        }, $obs->getScreenshots()->toArray());

        return [
            'id'             => $obs->getId(),
            'observedAt'     => $obs->getObservedAt()?->format('Y-m-d H:i:s'),
            'trend'          => $obs->getTrend(),
            'comment'        => $obs->getComment(),
            'orderId'        => $orderId,
            'positionId'     => $positionId,
            'assetSymbol'    => $obs->getAsset()?->getSymbol(),
            'timeframeLabel' => $obs->getTimeframe()?->getLabel(),
            'screenshotCount'=> count($screenshots),
            'screenshots'    => $screenshots,
            'context'        => $context,
        ];
    }
}
