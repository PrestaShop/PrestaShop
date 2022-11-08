<?php

namespace PrestaShop\PrestaShop\Core\Order\Repository;

use Order;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

interface OrderRepositoryInterface
{
    public function get(OrderId $orderId): Order;
}
