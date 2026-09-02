<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use Exception;
use Order;
use OrderDetail;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Exception\ShipmentNotFoundException;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;

class ShipmentProductAssigner
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly AdapterConfiguration $configuration,
        private readonly ShipmentShippingCostUpdater $shipmentShippingCostUpdater,
    ) {
    }

    public function assign(?int $shipmentId, Order $order, OrderDetail $orderDetail, ?int $carrierId = null): void
    {
        if (empty($shipmentId)) {
            if (empty($carrierId)) {
                throw new Exception('A carrier ID is required to create a new shipment');
            }

            $shipment = new Shipment();
            $shipment->setOrderId((int) $order->id);
            $shipment->setCarrierId($carrierId);
            $shipment->setAddressId((int) $order->id_address_delivery);
            $shipment->setTrackingNumber(null);
            $shipment->setShippingCostTaxExcluded(0.00);
            $shipment->setShippingCostTaxIncluded(0.00);
            $shipment->setDeliveredAt(null);
            $shipment->setShippedAt(null);
            $shipment->setCancelledAt(null);
        } else {
            $shipment = $this->shipmentRepository->findById($shipmentId);

            if ($shipment === null) {
                throw new ShipmentNotFoundException(sprintf('No shipment with id %d found', $shipmentId));
            }
        }

        $shipmentProduct = new ShipmentProduct();
        $shipmentProduct->setOrderDetailId((int) $orderDetail->id_order_detail);
        $shipmentProduct->setQuantity((int) $orderDetail->product_quantity);
        $shipment->addShipmentProduct($shipmentProduct);

        $this->shipmentRepository->save($shipment);

        if ($this->configuration->get('PS_ORDER_RECALCULATE_SHIPPING')) {
            $this->shipmentShippingCostUpdater->recalculateForOrder((int) $order->id);
        }
    }
}
