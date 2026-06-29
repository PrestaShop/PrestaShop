<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use Order;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;

/**
 * Consolidates the logic for updating order shipping totals based on its shipments.
 */
class OrderShippingTotalUpdater
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
    ) {
    }

    public function update(Order $order): Order
    {
        $totalTaxExcluded = 0.00;
        $totalTaxIncluded = 0.00;

        foreach ($this->shipmentRepository->findByOrderId((int) $order->id) as $shipment) {
            $totalTaxExcluded += $shipment->getShippingCostTaxExcluded();
            $totalTaxIncluded += $shipment->getShippingCostTaxIncluded();
        }

        $order->total_shipping_tax_excl = $totalTaxExcluded;
        $order->total_shipping_tax_incl = $totalTaxIncluded;
        $order->total_shipping = $totalTaxIncluded;
        $order->update();

        return $order;
    }
}
