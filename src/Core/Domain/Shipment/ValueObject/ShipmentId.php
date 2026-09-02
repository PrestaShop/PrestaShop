<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentException;

/**
 * Class ShipmentId
 */
class ShipmentId
{
    /**
     * @var int
     */
    private $shipmentId;

    /**
     * @param int $shipmentId
     *
     * @throws ShipmentException
     */
    public function __construct(int $shipmentId)
    {
        if (0 >= $shipmentId) {
            throw new ShipmentException('ShipmentException id must be greater than zero.');
        }

        $this->shipmentId = $shipmentId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->shipmentId;
    }
}
