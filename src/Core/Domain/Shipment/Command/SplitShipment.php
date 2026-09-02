<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailQuantity;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

class SplitShipment
{
    /**
     * @var ShipmentId
     */
    private $shipmentId;

    /**
     * @var OrderDetailQuantity
     */
    private $orderDetailQuantity;

    /**
     * @var CarrierId
     */
    private $carrierId;

    public function __construct(
        int $shipmentId,
        array $orderDetailQuantity,
        int $carrierId
    ) {
        $this->shipmentId = new ShipmentId($shipmentId);
        $this->orderDetailQuantity = new OrderDetailQuantity(
            $orderDetailQuantity,
        );
        $this->carrierId = new CarrierId($carrierId);
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }

    public function getOrderDetailQuantity(): OrderDetailQuantity
    {
        return $this->orderDetailQuantity;
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }
}
