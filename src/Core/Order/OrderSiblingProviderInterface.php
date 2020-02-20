<?php

namespace PrestaShop\PrestaShop\Core\Order;

interface OrderSiblingProviderInterface
{
    /**
     * @param int $orderId
     *
     * @return int
     */
    public function getNextOrder(int $orderId): int;

    /**
     * @param int $orderId
     *
     * @return int
     */
    public function getPreviousOrder(int $orderId): int;
}
