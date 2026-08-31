<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shipment;

use Order;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\Shipment\OrderShippingTotalUpdater;
use PrestaShop\PrestaShop\Adapter\Shipment\ShipmentShippingCostUpdater;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPriceInterface;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;

/**
 * The order total handed to the shipping cost pipeline selects the carrier's price range and is
 * compared against the free shipping threshold. Cart::getPackageShippingCost() feeds both with a
 * tax included total, so the recalculation has to use the same basis or an order can end up with a
 * shipping cost taken from a different range than the one the customer was charged.
 */
class ShipmentShippingCostUpdaterTest extends TestCase
{
    public function testTheOrderTotalUsedForTheRangeLookupIsTaxIncluded(): void
    {
        $capturedOrderTotal = null;

        $calculator = $this->createMock(ShippingCostCalculatorInterface::class);
        $calculator->method('compute')->willReturnCallback(
            function (ShippingCostPriceInterface $context) use (&$capturedOrderTotal): void {
                $capturedOrderTotal = (float) (string) $context->getOrderTotal();
            }
        );

        $this->buildUpdater($calculator)->recalculateForOrder(1);

        // 2 x 96.00 tax excluded is 192.00, which would fall in another price range than the
        // 2 x 115.20 tax included the cart used.
        $this->assertSame(230.40, $capturedOrderTotal);
    }

    private function buildUpdater(ShippingCostCalculatorInterface $calculator): ShipmentShippingCostUpdater
    {
        $shipmentProduct = (new ShipmentProduct())
            ->setOrderDetailId(7)
            ->setQuantity(2);

        $shipment = new Shipment();
        $shipment->setOrderId(1);
        $shipment->setCarrierId(2);
        $shipment->setAddressId(3);
        $shipment->addShipmentProduct($shipmentProduct);

        $order = $this->createMock(Order::class);
        $order->method('getProductsDetail')->willReturn([
            [
                'id_order_detail' => 7,
                'product_id' => 11,
                'product_attribute_id' => 0,
                'product_weight' => 1.0,
                'is_virtual' => false,
                'additional_shipping_cost' => 0.0,
                'unit_price_tax_excl' => 96.00,
                'unit_price_tax_incl' => 115.20,
            ],
        ]);
        $order->id = 1;
        $order->id_currency = 1;
        $order->id_customer = 4;

        $shipmentRepository = $this->createMock(ShipmentRepository::class);
        $shipmentRepository->method('findByOrderId')->willReturn([$shipment]);

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->method('get')->willReturn($order);

        $totalUpdater = $this->createMock(OrderShippingTotalUpdater::class);
        $totalUpdater->method('update')->willReturn($order);

        return new ShipmentShippingCostUpdater($shipmentRepository, $orderRepository, $calculator, $totalUpdater);
    }
}
