<?php
namespace App\Domain\Service\Position;
use App\DTO\Position\PositionCloseInput;
use App\DTO\Position\PositionInput;
use App\Entity\Position;

interface PositionServiceInterface
{
    public function list(): array;
    public function get(int $id): Position;
    public function create(PositionInput $input): Position;
    public function close(int $id, PositionCloseInput $input): Position;
}
