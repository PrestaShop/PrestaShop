<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Query;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Get shipments for order.
 */
class GetOrderShipments
{
    /**
     * @var OrderId
     */
    private $orderId;

    private bool $includeDeleted;

    public function __construct(int $orderId, bool $includeDeleted = false)
    {
        $this->orderId = new OrderId($orderId);
        $this->includeDeleted = $includeDeleted;
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function includeDeleted(): bool
    {
        return $this->includeDeleted;
    }
}
