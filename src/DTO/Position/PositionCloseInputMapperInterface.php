<?php
namespace App\DTO\Position;
interface PositionCloseInputMapperInterface
{
    public function fromArray(array $data): PositionCloseInput;
}
