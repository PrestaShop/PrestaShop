<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Command;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\ShipmentId;

class SwitchShipmentCarrierCommand
{
    /** @var ShipmentId */
    private $shipmentId;

    /** @var CarrierId */
    private $carrierId;

    /**
     * @throws ShipmentException
     * @throws CarrierConstraintException
     */
    public function __construct(int $shipmentId, int $carrierId)
    {
        $this->shipmentId = new ShipmentId($shipmentId);
        $this->carrierId = new CarrierId($carrierId);
    }

    public function getShipmentId(): ShipmentId
    {
        return $this->shipmentId;
    }

    public function getCarrierId(): CarrierId
    {
        return $this->carrierId;
    }
}
