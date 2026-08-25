<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shipment;

use Carrier;
use Order;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Carrier\Repository\CarrierRepository;
use PrestaShop\PrestaShop\Adapter\Order\OrderDetailMatcher;
use PrestaShop\PrestaShop\Adapter\Order\Repository\OrderRepository;
use PrestaShop\PrestaShop\Adapter\Shipment\OrderShipmentService;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\CarrierId;
use PrestaShopBundle\Entity\Repository\ShipmentRepository;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;

class OrderShipmentServiceTest extends TestCase
{
    private const ORDER_ID = 42;

    private const CARRIER_A_ID = 7;

    private const CARRIER_B_ID = 8;

    /**
     * The same product and combination can be ordered several times with different customizations, each order
     * detail landing in a different shipment. Matching on the product alone returns whichever order detail comes
     * first, hence the carrier of the wrong shipment.
     */
    public function testItReturnsTheCarrierOfTheShipmentHoldingTheCustomizedLine(): void
    {
        $service = $this->buildService();

        $carrier = $service->getCarrierForProduct(self::ORDER_ID, 14, 0, 5);

        $this->assertNotNull($carrier);
        $this->assertSame(self::CARRIER_B_ID, (int) $carrier->id);
    }

    public function testItReturnsTheCarrierOfTheShipmentHoldingTheNonCustomizedLine(): void
    {
        $service = $this->buildService();

        $carrier = $service->getCarrierForProduct(self::ORDER_ID, 14);

        $this->assertNotNull($carrier);
        $this->assertSame(self::CARRIER_A_ID, (int) $carrier->id);
    }

    public function testItDistinguishesCombinationsOfTheSameProduct(): void
    {
        $service = $this->buildService();

        $carrier = $service->getCarrierForProduct(self::ORDER_ID, 14, 3);

        $this->assertNotNull($carrier);
        $this->assertSame(self::CARRIER_B_ID, (int) $carrier->id);
    }

    public function testItReturnsNullWhenNoOrderDetailMatches(): void
    {
        $service = $this->buildService();

        $this->assertNull($service->getCarrierForProduct(self::ORDER_ID, 14, 0, 999));
    }

    /**
     * A virtual product has no shipment, the carrier lookup must return null rather than the carrier of an
     * unrelated line.
     */
    public function testItReturnsNullWhenTheOrderDetailBelongsToNoShipment(): void
    {
        $service = $this->buildService();

        $this->assertNull($service->getCarrierForProduct(self::ORDER_ID, 99));
    }

    /**
     * Callers rendering a carrier per product line go through this instead of calling getCarrierForProduct()
     * in a loop, so it must resolve the very same lines.
     */
    public function testItResolvesEveryProductLineAtOnce(): void
    {
        $productLines = [
            'plain' => ['id_product' => 14, 'id_product_attribute' => null, 'id_customization' => 0],
            'customized' => ['id_product' => 14, 'id_product_attribute' => null, 'id_customization' => 5],
            'combination' => ['id_product' => 14, 'id_product_attribute' => 3, 'id_customization' => 0],
            'virtual' => ['id_product' => 99, 'id_product_attribute' => null, 'id_customization' => 0],
            'not in order' => ['id_product' => 77, 'id_product_attribute' => null, 'id_customization' => 0],
        ];

        $carriers = $this->buildService()->getCarriersForProductLines(self::ORDER_ID, $productLines);

        $this->assertSame(['plain', 'customized', 'combination'], array_keys($carriers));
        $this->assertSame(self::CARRIER_A_ID, (int) $carriers['plain']->id);
        $this->assertSame(self::CARRIER_B_ID, (int) $carriers['customized']->id);
        $this->assertSame(self::CARRIER_B_ID, (int) $carriers['combination']->id);
    }

    /**
     * Two lines shipped by the same carrier must not load it twice.
     */
    public function testItLoadsEachCarrierOnlyOnce(): void
    {
        $carrierRepository = $this->mockCarrierRepository();
        $carrierRepository->expects($this->exactly(2))->method('get');

        $service = new OrderShipmentService(
            $this->mockShipmentRepository(),
            $this->mockOrderRepository(),
            $carrierRepository,
            new OrderDetailMatcher()
        );

        $service->getCarriersForProductLines(self::ORDER_ID, [
            ['id_product' => 14, 'id_product_attribute' => null, 'id_customization' => 0],
            ['id_product' => 14, 'id_product_attribute' => null, 'id_customization' => 5],
            ['id_product' => 14, 'id_product_attribute' => 3, 'id_customization' => 0],
        ]);
    }

    public function testItResolvesNoProductLineOnAnOrderWithoutShipment(): void
    {
        $shipmentRepository = $this->createMock(ShipmentRepository::class);
        $shipmentRepository->method('findByOrderId')->willReturn([]);

        $service = new OrderShipmentService(
            $shipmentRepository,
            $this->mockOrderRepository(),
            $this->mockCarrierRepository(),
            new OrderDetailMatcher()
        );

        $lines = [['id_product' => 14, 'id_product_attribute' => null, 'id_customization' => 0]];

        $this->assertSame([], $service->getCarriersForProductLines(self::ORDER_ID, $lines));
    }

    private function buildService(): OrderShipmentService
    {
        return new OrderShipmentService(
            $this->mockShipmentRepository(),
            $this->mockOrderRepository(),
            $this->mockCarrierRepository(),
            new OrderDetailMatcher()
        );
    }

    /**
     * Order details as returned by the legacy layer: raw database rows, every value being a string.
     */
    private function mockOrderRepository(): OrderRepository&MockObject
    {
        $order = $this->createMock(Order::class);
        $order->id = self::ORDER_ID;
        $order->method('getOrderDetailList')->willReturn([
            // Same product, same (absent) combination, differing only by their customization.
            ['id_order_detail' => '1', 'product_id' => '14', 'product_attribute_id' => '0', 'id_customization' => '0'],
            ['id_order_detail' => '2', 'product_id' => '14', 'product_attribute_id' => '0', 'id_customization' => '5'],
            // Same product again, this time with a combination.
            ['id_order_detail' => '3', 'product_id' => '14', 'product_attribute_id' => '3', 'id_customization' => '0'],
            // Virtual product, part of no shipment.
            ['id_order_detail' => '4', 'product_id' => '99', 'product_attribute_id' => '0', 'id_customization' => '0'],
        ]);

        $orderRepository = $this->createMock(OrderRepository::class);
        $orderRepository->method('get')->willReturn($order);

        return $orderRepository;
    }

    private function mockShipmentRepository(): ShipmentRepository&MockObject
    {
        $shipmentRepository = $this->createMock(ShipmentRepository::class);
        $shipmentRepository->method('findByOrderId')->willReturn([
            $this->buildShipment(self::CARRIER_A_ID, [1]),
            $this->buildShipment(self::CARRIER_B_ID, [2, 3]),
        ]);

        return $shipmentRepository;
    }

    private function mockCarrierRepository(): CarrierRepository&MockObject
    {
        $carrierRepository = $this->createMock(CarrierRepository::class);
        $carrierRepository->method('get')->willReturnCallback(function (CarrierId $carrierId): Carrier {
            $carrier = $this->createMock(Carrier::class);
            $carrier->id = $carrierId->getValue();

            return $carrier;
        });

        return $carrierRepository;
    }

    /**
     * @param int[] $orderDetailIds
     */
    private function buildShipment(int $carrierId, array $orderDetailIds): Shipment
    {
        $shipment = new Shipment();
        $shipment->setOrderId(self::ORDER_ID);
        $shipment->setCarrierId($carrierId);

        foreach ($orderDetailIds as $orderDetailId) {
            $shipment->addShipmentProduct(
                (new ShipmentProduct())->setOrderDetailId($orderDetailId)->setQuantity(1)
            );
        }

        return $shipment;
    }
}
