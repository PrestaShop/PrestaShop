<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailQuantity;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

class MergeProductsToShipment
{
    /**
     * @var ShipmentId
     */
    private $sourceShipmentId;

    /**
     * @var ShipmentId
     */
    private $targetShipmentId;

    /**
     * @var OrderDetailQuantity
     */
    private $orderDetailQuantities;

    public function __construct(int $sourceShipmentId, int $targetShipmentId, array $orderDetailQuantities)
    {
        $this->sourceShipmentId = new ShipmentId($sourceShipmentId);
        $this->targetShipmentId = new ShipmentId($targetShipmentId);
        $this->orderDetailQuantities = new OrderDetailQuantity(
            $orderDetailQuantities,
        );
    }

    public function getSourceShipmentId(): ShipmentId
    {
        return $this->sourceShipmentId;
    }

    public function getTargetShipmentId(): ShipmentId
    {
        return $this->targetShipmentId;
    }

    public function getOrderDetailQuantity(): OrderDetailQuantity
    {
        return $this->orderDetailQuantities;
    }
}
