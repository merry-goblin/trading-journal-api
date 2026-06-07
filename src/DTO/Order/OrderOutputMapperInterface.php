<?php

namespace App\DTO\Order;

use App\Entity\Order;

interface OrderOutputMapperInterface
{
    public function fromEntity(Order $order): OrderOutput;
}
