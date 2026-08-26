<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shipment\CommandHandler;

use Order;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration as AdapterConfiguration;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\Shipment\CommandHandler\CreateShipmentHandler;
use PrestaShop\PrestaShop\Adapter\Shipment\OrderShippingTotalUpdater;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\Calculator\ShippingCostCalculatorInterface;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ShippingCost\ShippingCostPrice;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\CannotFindProductInOrderException;
use PrestaShop\PrestaShop\Core\Domain\Shipment\Command\CreateShipment;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;

class CreateShipmentHandlerTest extends TestCase
{
    private const ORDER_ID = 42;

    private const CARRIER_ID = 7;

    private const PRODUCT_ID = 14;

    /**
     * The shipping cost is computed from the weight and price of the order detail the shipment is created for.
     * Matching on the product alone picked the first line, hence the weight of another customization.
     */
    public function testItComputesTheShippingCostFromTheCustomizedLine(): void
    {
        $products = $this->handleAndCaptureProducts(new CreateShipment(self::ORDER_ID, self::CARRIER_ID, self::PRODUCT_ID, 1, 0, 5));

        $this->assertCount(1, $products);
        $this->assertSame(2.5, $products[0]['weight']);
        $this->assertSame(0, $products[0]['id_product_attribute']);
    }

    public function testItComputesTheShippingCostFromTheCombinationLine(): void
    {
        $products = $this->handleAndCaptureProducts(new CreateShipment(self::ORDER_ID, self::CARRIER_ID, self::PRODUCT_ID, 1, 3));

        $this->assertCount(1, $products);
        $this->assertSame(4.0, $products[0]['weight']);
        $this->assertSame(3, $products[0]['id_product_attribute']);
    }

    public function testItComputesTheShippingCostFromThePlainLine(): void
    {
        $products = $this->handleAndCaptureProducts(new CreateShipment(self::ORDER_ID, self::CARRIER_ID, self::PRODUCT_ID, 1));

        $this->assertCount(1, $products);
        $this->assertSame(1.0, $products[0]['weight']);
    }

    public function testItFailsWhenNoOrderDetailMatchesTheCustomization(): void
    {
        $this->expectException(CannotFindProductInOrderException::class);

        $this->handleAndCaptureProducts(new CreateShipment(self::ORDER_ID, self::CARRIER_ID, self::PRODUCT_ID, 1, 0, 999));
    }

    /**
     * @return array<array<string, mixed>> the physical products the shipping cost was computed from
     */
    private function handleAndCaptureProducts(CreateShipment $command): array
    {
        $order = $this->createMock(Order::class);
        $order->id = self::ORDER_ID;
        $order->id_address_delivery = 1;
        $order->id_currency = 1;
        $order->id_customer = 1;
        $order->total_products = 100.0;
        // Same product, three lines told apart by their combination and their customization only
        $order->method('getProductsDetail')->willReturn([
            ['id_order_detail' => 1, 'product_id' => 14, 'product_attribute_id' => 0, 'id_customization' => 0, 'product_weight' => 1.0, 'is_virtual' => false],
            ['id_order_detail' => 2, 'product_id' => 14, 'product_attribute_id' => 0, 'id_customization' => 5, 'product_weight' => 2.5, 'is_virtual' => false],
            ['id_order_detail' => 3, 'product_id' => 14, 'product_attribute_id' => 3, 'id_customization' => 0, 'product_weight' => 4.0, 'is_virtual' => false],
        ]);

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->method('get')->willReturn($order);

        $configuration = $this->createMock(AdapterConfiguration::class);
        $configuration->method('get')->willReturn(true);

        $capturedProducts = [];
        $calculator = $this->createMock(ShippingCostCalculatorInterface::class);
        $calculator->method('compute')->willReturnCallback(
            function (ShippingCostPrice $context) use (&$capturedProducts): void {
                $capturedProducts = $context->getPhysicalProducts();
            }
        );

        $shipmentRepository = $this->createMock(ShipmentRepository::class);
        $shipmentRepository->method('save')->willReturn(1);

        $handler = new CreateShipmentHandler(
            $shipmentRepository,
            $orderRepository,
            $calculator,
            $configuration,
            $this->createMock(OrderShippingTotalUpdater::class)
        );

        $handler->handle($command);

        return $capturedProducts;
    }
}
