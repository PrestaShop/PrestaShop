<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Query;

use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

/**
 * Get shipment.
 */
class GetShipmentProducts
{
    /**
     * @var ShipmentId
     */
    private $shipmentId;

    public function __construct(int $shipmentId)
    {
        $this->shipmentId = new ShipmentId($shipmentId);
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }
}
