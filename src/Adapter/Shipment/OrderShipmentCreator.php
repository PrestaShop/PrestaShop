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
        foreach ($productsHandledByCarrier as $carrierId => $products) {
            $shipment = new Shipment();
            $shipment->setOrderId((int) $order->id);
            $shipment->setCarrierId((int) $carrierId);
            $shipment->setAddressId((int) $order->id_address_delivery);
            $shipment->setTrackingNumber(null);
            $shipment->setShippingCostTaxExcluded((float) $products['total_shipping_tax_excl']);
            $shipment->setShippingCostTaxIncluded((float) $products['total_shipping_tax_incl']);
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
            $orderCarrier->shipping_cost_tax_excl = (float) $products['total_shipping_tax_excl'];
            $orderCarrier->shipping_cost_tax_incl = (float) $products['total_shipping_tax_incl'];
            $orderCarrier->add();
            // match products with order details to get quantities & orderDetailId
            foreach (OrderDetail::getList($order->id) as $orderDetailProduct) {
                foreach ($products['product_list'] as $product) {
                    if (!$this->needShipmentProductCreation($product, $orderDetailProduct)) {
                        continue;
                    }

                    $quantity = $orderDetailProduct['product_quantity'];
                    $orderDetailId = $orderDetailProduct['id_order_detail'];

                    $shipmentProduct = (new ShipmentProduct())
                        ->setShipment($shipment)
                        ->setOrderDetailId($orderDetailId)
                        ->setQuantity($quantity);

                    $shipment->addShipmentProduct($shipmentProduct);
                }
            }

            $this->shipmentRepository->save($shipment);
        }
    }

    /**
     * @param array{
     *     id_customization: int,
     *     id_product_attribute: int,
     *     id_product: int
     * } $product
     * @param array{
     *     id_customization: int,
     *     id_order_detail: int,
     *     product_id: int,
     *     product_attribute_id: int,
     *     product_quantity: int
     * } $orderDetailProduct
     *
     * @return bool
     */
    private function needShipmentProductCreation(array $product, array $orderDetailProduct): bool
    {
        if (!empty($product['id_customization'])) {
            return $product['id_customization'] === $orderDetailProduct['id_customization'];
        }
        if (!empty($product['id_product_attribute'])) {
            return $product['id_product_attribute'] === $orderDetailProduct['product_attribute_id'];
        }

        return $product['id_product'] === $orderDetailProduct['product_id'];
    }
}
