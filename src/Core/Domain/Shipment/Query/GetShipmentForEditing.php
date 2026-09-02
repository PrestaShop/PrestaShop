<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Query;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

/**
 * Get shipment for editing.
 */
class GetShipmentForEditing
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var ShipmentId
     */
    private $shipmentId;

    public function __construct(int $orderId, int $shipmentId)
    {
        $this->orderId = new OrderId($orderId);
        $this->shipmentId = new ShipmentId($shipmentId);
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }
}
