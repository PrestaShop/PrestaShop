<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Query;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailId;

class GetShipmentsForOrderDetail
{
    private OrderId $orderId;

    private OrderDetailId $orderDetailId;

    public function __construct(int $orderId, int $orderDetailId)
    {
        $this->orderId = new OrderId($orderId);
        $this->orderDetailId = new OrderDetailId($orderDetailId);
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getOrderDetailId(): OrderDetailId
    {
        return $this->orderDetailId;
    }
}
