<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\QueryResult;

class ShipmentForEditing
{
    public function __construct(
        private int $carrierId,
        private string $trackingNumber,
        private array $selectedProducts,
    ) {
    }

    /**
     * @return int
     */
    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    /**
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }

    /**
     * @return int[]
     */
    public function getProductsIds()
    {
        return $this->selectedProducts;
    }

    public function toArray(): array
    {
        return [
            'tracking_number' => $this->getTrackingNumber(),
            'carrier' => $this->getCarrierId(),
            'selectedProducts' => $this->getProductsIds(),
        ];
    }
}
