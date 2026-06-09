<?php
namespace App\DTO\Position;
use App\DTO\AbstractMapper;

class PositionCloseInputMapper extends AbstractMapper implements PositionCloseInputMapperInterface
{
    public function fromArray(array $data): PositionCloseInput
    {
        $dto = new PositionCloseInput();
        $dto->closedAt   = $this->stringOrEmpty($data['closedAt'] ?? null);
        $dto->exitPrice  = $this->stringOrEmpty($data['exitPrice'] ?? null);
        $dto->pnl        = $this->stringOrNull($data['pnl'] ?? null);
        $dto->pnlPercent = $this->stringOrNull($data['pnlPercent'] ?? null);
        $dto->rr         = $this->stringOrNull($data['rr'] ?? null);
        return $dto;
    }
}
