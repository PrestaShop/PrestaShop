<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order;

use Order;
use PrestaShop\PrestaShop\Core\Order\OrderSiblingProviderInterface;

class OrderSiblingProvider implements OrderSiblingProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getNextOrderId(int $orderId): int
    {
        $order = new Order($orderId);

        return (int) $order->getNextOrderId();
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousOrderId(int $orderId): int
    {
        $order = new Order($orderId);

        return (int) $order->getPreviousOrderId();
    }
}
