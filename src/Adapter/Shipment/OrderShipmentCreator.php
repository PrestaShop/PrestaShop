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
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;

class OrderShipmentCreator
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    public function __construct(ShipmentRepository $shipmentRepository)
    {
        $this->shipmentRepository = $shipmentRepository;
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
                $orderDetailProduct = $this->findMatchingOrderDetail($product, $orderDetailProducts);
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

    /**
     * Returns the single order detail matching a cart product line, or null when none does.
     *
     * A product line is only fully identified by its product, combination AND customization: the very same
     * product and combination can be ordered several times with different customizations, each one having its
     * own order detail.
     *
     * @param array{
     *     id_customization: int|string|null,
     *     id_product_attribute: int|string|null,
     *     id_product: int|string
     * } $product
     * @param array<array{
     *     id_customization: int|string,
     *     id_order_detail: int|string,
     *     product_id: int|string,
     *     product_attribute_id: int|string|null,
     *     product_quantity: int|string
     * }> $orderDetailProducts
     *
     * @return array<string, mixed>|null
     */
    private function findMatchingOrderDetail(array $product, array $orderDetailProducts): ?array
    {
        foreach ($orderDetailProducts as $orderDetailProduct) {
            if (
                (int) $product['id_product'] === (int) $orderDetailProduct['product_id']
                && (int) ($product['id_product_attribute'] ?? 0) === (int) ($orderDetailProduct['product_attribute_id'] ?? 0)
                && (int) ($product['id_customization'] ?? 0) === (int) ($orderDetailProduct['id_customization'] ?? 0)
            ) {
                return $orderDetailProduct;
            }
        }

        return null;
    }
}
