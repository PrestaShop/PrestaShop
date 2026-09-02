<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use PrestaShopBundle\Entity\Repository\ShipmentRepository;

class ShipmentProductQuantityUpdater
{
    public function __construct(private ShipmentRepository $shipmentRepository)
    {
    }

    /**
     * @param array<int, array{
     *     shipment_id: int,
     *     quantity: int
     * }> $shipmentsQuantities
     */
    public function updateShipmentQuantity(int $orderDetailId, array $shipmentsQuantities): void
    {
        foreach ($shipmentsQuantities as $shipmentQuantity) {
            $this->shipmentRepository->updateShipmentProductQuantity($shipmentQuantity['shipment_id'], $orderDetailId, $shipmentQuantity['quantity']);
        }
    }
}
