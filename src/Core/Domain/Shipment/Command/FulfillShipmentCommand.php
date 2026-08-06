<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\TrackingNumber;

class FulfillShipmentCommand
{
    /**
     * @var ShipmentId
     */
    private $shipmentId;

    /**
     * @var TrackingNumber
     */
    private $trackingNumber;

    public function __construct(int $shipmentId, string $trackingNumber)
    {
        $this->shipmentId = new ShipmentId($shipmentId);
        $this->trackingNumber = new TrackingNumber($trackingNumber);
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }

    public function getTrackingNumber(): TrackingNumber
    {
        return $this->trackingNumber;
    }
}
