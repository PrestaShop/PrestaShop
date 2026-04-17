<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

class EditShipment
{
    /**
     * @var ShipmentId
     */
    private $shipmentId;

    /**
     * @var string
     */
    private $trackingNumber;

    /**
     * @var CarrierId
     */
    private $carrierId;

    public function __construct(int $shipmentId, string $trackingNumber, int $carrierId)
    {
        $this->shipmentId = new ShipmentId($shipmentId);
        $this->trackingNumber = $trackingNumber;
        $this->carrierId = new CarrierId(
            $carrierId,
        );
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }
}
