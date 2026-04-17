<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

class DeleteProductFromShipment
{
    /** @var ShipmentId */
    private $shipmentId;

    /** @var OrderDetailId */
    private $orderDetailId;

    public function __construct(int $shipmentId, int $orderDetailId)
    {
        $this->shipmentId = new ShipmentId($shipmentId);
        $this->orderDetailId = new OrderDetailId($orderDetailId);
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }

    public function getOrderDetailId(): OrderDetailId
    {
        return $this->orderDetailId;
    }
}
