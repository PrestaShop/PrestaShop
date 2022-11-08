<?php

namespace PrestaShop\PrestaShop\Core\Order\Repository;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use Order;

interface OrderRepositoryInterface
{
    public function get(OrderId $orderId): Order;
}
