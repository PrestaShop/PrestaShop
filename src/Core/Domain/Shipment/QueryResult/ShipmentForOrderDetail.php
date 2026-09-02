<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult;

class ShipmentForOrderDetail
{
    public function __construct(
        private int $shipmentId,
        private int $quantity,
    ) {
    }

    public function getShipmentId(): int
    {
        return $this->shipmentId;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return array{
     *     shipment_id: int,
     *     quantity: int,
     * }
     */
    public function toArray(): array
    {
        return [
            'shipment_id' => $this->getShipmentId(),
            'quantity' => $this->getQuantity(),
        ];
    }
}
