<?php

namespace PrestaShop\PrestaShop\Adapter\Order;

use Order;
use PrestaShop\PrestaShop\Core\Order\OrderSiblingProviderInterface;

class OrderSiblingProvider implements OrderSiblingProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getNextOrder(int $orderId): int
    {
        $order = new Order($orderId);

        return $order->getNextOrderId();
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousOrder(int $orderId): int
    {
        $order = new Order($orderId);

        return $order->getPreviousOrderId();
    }
}
