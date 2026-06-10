<?php

namespace App\Controller\FrontApi;

use App\Domain\Service\Import\ImportServiceInterface;
use App\DTO\FrontApi\Import\ImportPositionItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class FrontImportController extends AbstractController
{
    #[Route('/frontApi/import/positions', name: 'front_import_positions', methods: ['POST'])]
    public function positions(
        Request $request,
        ImportServiceInterface $service
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['timeframeId'], $data['positions']) || !is_array($data['positions']))
            return $this->json(['error' => 'timeframeId et positions[] requis'], 400);

        $items = [];
        foreach ($data['positions'] as $row) {
            $item = new ImportPositionItem();
            $item->symbol      = $row['symbol']      ?? '';
            $item->direction   = $row['direction']   ?? 'long';
            $item->openedAt    = $row['openedAt']    ?? '';
            $item->closedAt    = $row['closedAt']    ?? '';
            $item->entryPrice  = $row['entryPrice']  ?? '0';
            $item->exitPrice   = $row['exitPrice']   ?? '0';
            $item->stopLoss    = $row['stopLoss']    ?? null;
            $item->takeProfit  = $row['takeProfit']  ?? null;
            $item->volume      = $row['volume']      ?? '0';
            $item->pnl         = $row['pnl']         ?? '0';

            if (!$item->symbol || !$item->openedAt || !$item->closedAt) continue;
            $items[] = $item;
        }

        if (empty($items))
            return $this->json(['error' => 'Aucune position valide dans le payload'], 422);

        $result = $service->importPositions($items, (int)$data['timeframeId']);
        return $this->json($result, $result['created'] > 0 ? 201 : 422);
    }
}
