<?php
namespace App\Domain\Service\FrontPosition;
use App\DTO\FrontApi\Position\FrontPositionDetailOutput;
use App\DTO\FrontApi\Position\FrontPositionListOutput;
use App\DTO\FrontApi\Position\PositionEnrichInput;
use App\Entity\Position;
interface FrontPositionServiceInterface
{
    /** @return FrontPositionListOutput[] */
    public function list(array $filters, int $page, int $limit): array;
    public function getDetail(int $id): FrontPositionDetailOutput;
    public function enrich(int $id, PositionEnrichInput $input): FrontPositionDetailOutput;
}
