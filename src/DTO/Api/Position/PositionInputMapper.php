<?php

namespace App\DTO\Api\Position;

class PositionInputMapper
{
    public function fromArray(array $data): PositionInput
    {
        $input = new PositionInput();
        $input->assetId      = (int)($data['assetId']     ?? 0);
        $input->timeframeId  = (int)($data['timeframeId'] ?? 0);
        $input->openedAt     = $data['openedAt']    ?? '';
        $input->direction    = $data['direction']   ?? 'long';
        $input->entryPrice   = $data['entryPrice']  ?? '0';
        $input->stopLoss     = $data['stopLoss']    ?? null;
        $input->takeProfit   = $data['takeProfit']  ?? null;
        $input->volume       = $data['volume']      ?? null;
        $input->originOrderId= isset($data['originOrderId']) ? (int)$data['originOrderId'] : null;
        $input->isBacktest   = filter_var($data['isBacktest'] ?? false, FILTER_VALIDATE_BOOLEAN);

        // tagIds : tableau d'entiers optionnel
        if (!empty($data['tagIds']) && is_array($data['tagIds']))
            $input->tagIds = array_map('intval', $data['tagIds']);

        return $input;
    }
}
