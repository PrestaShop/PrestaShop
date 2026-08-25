<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use Order;
use OrderCarrier;
use OrderDetail;
use PrestaShop\PrestaShop\Adapter\Order\OrderDetailMatcher;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;

class OrderShipmentCreator
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var OrderDetailMatcher
     */
    private $orderDetailMatcher;

    public function __construct(ShipmentRepository $shipmentRepository, OrderDetailMatcher $orderDetailMatcher)
    {
        $this->shipmentRepository = $shipmentRepository;
        $this->orderDetailMatcher = $orderDetailMatcher;
    }

    public function addShipmentOrder(Order $order, array $productsHandledByCarrier): void
    {
        // Order details are the same for every carrier group, fetch them once
        $orderDetailProducts = OrderDetail::getList($order->id);

        foreach ($productsHandledByCarrier as $carrierId => $products) {
            $shipment = new Shipment();
            $shipment->setOrderId((int) $order->id);
            $shipment->setCarrierId((int) $carrierId);
            $shipment->setAddressId((int) $order->id_address_delivery);
            $shipment->setTrackingNumber(null);
            $shipment->setShippingCostTaxExcluded(0);
            $shipment->setShippingCostTaxIncluded(0);
            $shipment->setDeliveredAt(null);
            $shipment->setShippedAt(null);
            $shipment->setCancelledAt(null);

            $productWeight = array_map(function ($product) {
                return $product['weight'] * $product['quantity'];
            }, $products['product_list']);

            // add OrderCarrier here for keep the compatibility for legacy
            $orderCarrier = new OrderCarrier();
            $orderCarrier->id_order = (int) $order->id;
            $orderCarrier->id_carrier = $carrierId;
            $orderCarrier->weight = (float) $productWeight[0];
            $orderCarrier->shipping_cost_tax_excl = 0;
            $orderCarrier->shipping_cost_tax_incl = 0;
            $orderCarrier->add();
            // match products with order details to get quantities & orderDetailId
            foreach ($products['product_list'] as $product) {
                $orderDetailProduct = $this->orderDetailMatcher->matchCartProduct($orderDetailProducts, $product);
                if ($orderDetailProduct === null) {
                    continue;
                }

                $shipmentProduct = (new ShipmentProduct())
                    ->setShipment($shipment)
                    ->setOrderDetailId((int) $orderDetailProduct['id_order_detail'])
                    ->setQuantity((int) $orderDetailProduct['product_quantity']);

                $shipment->addShipmentProduct($shipmentProduct);
            }

            $this->shipmentRepository->save($shipment);
        }
    }
}
