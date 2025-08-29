<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
                    if ($product['id_product'] === $orderDetailProduct['product_id']) {
                        $shipmentProduct = new ShipmentProduct();
                        $shipmentProduct->setShipment($shipment);
                        $shipmentProduct->setOrderDetailId($orderDetailProduct['id_order_detail']);
                        $shipmentProduct->setQuantity($orderDetailProduct['product_quantity']);
                        $shipment->addShipmentProduct($shipmentProduct);
                    }
                }
            }

            $this->shipmentRepository->save($shipment);
        }
    }
}
