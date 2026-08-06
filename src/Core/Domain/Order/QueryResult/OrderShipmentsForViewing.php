<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use PrestaShopBundle\Entity\Shipment;

class OrderShipmentsForViewing
{
    /**
     * @var Shipment[]
     */
    private array $shipments;

    private int $totalCount;

    private int $fulfilledCount;

    /**
     * @param Shipment[] $shipments
     */
    public function __construct(array $shipments)
    {
        $this->shipments = $shipments;
        $this->totalCount = count($shipments);
        $this->fulfilledCount = 0;

        foreach ($shipments as $shipment) {
            if ($shipment->getTrackingNumber() !== null && $shipment->getPackedAt() !== null) {
                ++$this->fulfilledCount;
            }
        }
    }

    /**
     * @return Shipment[]
     */
    public function getShipments(): array
    {
        return $this->shipments;
    }

    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    public function getFulfilledCount(): int
    {
        return $this->fulfilledCount;
    }

    /**
     * Returns true if all shipments have been fulfilled.
     * A fulfilled shipment has both a tracking_number and a packed_at date set.
     */
    public function areAllShipmentsPacked(): bool
    {
        return $this->totalCount > 0 && $this->fulfilledCount === $this->totalCount;
    }
}
