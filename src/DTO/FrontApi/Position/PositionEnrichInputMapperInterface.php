<?php
namespace App\DTO\FrontApi\Position;
interface PositionEnrichInputMapperInterface
{
    public function fromArray(array $data): PositionEnrichInput;
}
