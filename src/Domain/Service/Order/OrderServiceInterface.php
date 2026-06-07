<?php

namespace App\Domain\Service\Order;

use App\DTO\Order\OrderInput;
use App\Entity\Order;

interface OrderServiceInterface
{
    public function list(): array;
    public function get(int $id): ?Order;
    public function create(OrderInput $input): Order;
    public function updateStatus(int $id, string $status): Order;
}
