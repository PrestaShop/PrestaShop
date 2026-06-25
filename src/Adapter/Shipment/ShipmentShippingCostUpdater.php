<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shipment;

use Order;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\ShippingCalculationRequest;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotFindProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;

/**
 * Recalculates shipping cost for all active shipments of an order and updates the order totals.
 */
class ShipmentShippingCostUpdater
{
    public function __construct(
        private readonly ShipmentRepository $shipmentRepository,
        private readonly OrderRepository $orderRepository,
        private readonly ShippingCostCalculatorInterface $shippingCostCalculator,
        private readonly OrderShippingTotalUpdater $orderShippingTotalUpdater,
    ) {
    }

    public function recalculateForOrder(int $orderId): Order
    {
        $order = $this->orderRepository->get(new OrderId($orderId));
        $productsDetail = $order->getProductsDetail();

        foreach ($this->shipmentRepository->findByOrderId($orderId) as $shipment) {
            $this->recalculateShipment($shipment, $order, $productsDetail);
        }

        return $this->orderShippingTotalUpdater->update($order);
    }

    /**
     * @param array<array<string, mixed>> $productsDetail
     */
    private function recalculateShipment(Shipment $shipment, Order $order, array $productsDetail): void
    {
        $products = [];
        $shipmentTotalProducts = 0.00;

        foreach ($shipment->getProducts() as $shipmentProduct) {
            $product = $this->findOrderProductByDetailId($productsDetail, $shipmentProduct->getOrderDetailId());
            if ($product === null) {
                throw new CannotFindProductInOrderException(
                    sprintf('Product with order detail id %d not found in order %d', $shipmentProduct->getOrderDetailId(), (int) $order->id)
                );
            }

            $quantity = $shipmentProduct->getQuantity();
            $unitPriceTaxExcl = (float) ($product['unit_price_tax_excl'] ?? 0);
            $shipmentTotalProducts += $unitPriceTaxExcl * $quantity;

            $products[] = [
                'id_product' => (int) $product['product_id'],
                'id_product_attribute' => (int) ($product['product_attribute_id'] ?? 0),
                'quantity' => $quantity,
                'weight' => (float) ($product['product_weight'] ?? 0),
                'weight_attribute' => null,
                'is_virtual' => (bool) ($product['is_virtual'] ?? false),
                'additional_shipping_cost' => (float) ($product['additional_shipping_cost'] ?? 0),
                'price_wt' => (float) ($product['unit_price_tax_incl'] ?? 0),
            ];
        }

        if (empty($products)) {
            return;
        }

        $request = new ShippingCalculationRequest(
            products: $products,
            carrierId: $shipment->getCarrierId(),
            zoneId: null,
            addressId: $shipment->getAddressId(),
            countryZoneId: 0,
            currencyId: (int) $order->id_currency,
            customerId: (int) $order->id_customer,
            orderTotal: $shipmentTotalProducts,
        );

        $context = ShippingCostPrice::createFromRequest($request);
        $this->shippingCostCalculator->compute($context);

        if ($context->getTaxExcluded() !== null && $context->getTaxIncluded() !== null) {
            $shipment->setShippingCostTaxExcluded((float) (string) $context->getTaxExcluded());
            $shipment->setShippingCostTaxIncluded((float) (string) $context->getTaxIncluded());
            $this->shipmentRepository->save($shipment);
        }
    }

    /**
     * @param array<array<string, mixed>> $products
     *
     * @return array<string, mixed>|null
     */
    private function findOrderProductByDetailId(array $products, int $orderDetailId): ?array
    {
        foreach ($products as $product) {
            if ((int) $product['id_order_detail'] === $orderDetailId) {
                return $product;
            }
        }

        return null;
    }
}
