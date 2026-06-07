<?php

namespace App\DTO\Order;

interface OrderInputMapperInterface
{
    public function fromArray(array $data): OrderInput;
}
