<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Shipment;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Shipment\ShipmentSplitter;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;
use Symfony\Contracts\Translation\TranslatorInterface;

class ShipmentSplitterTest extends TestCase
{
    private const SOURCE_CARRIER_ID = 2;
    private const TARGET_CARRIER_ID = 3;
    private const ORDER_ID = 17;
    private const ADDRESS_ID = 42;
    private const ORDER_DETAIL_ID = 90023;

    /**
     * The shipment the splitter returns is for a carrier of the caller's choosing, so the cost of the
     * carrier it was split away from says nothing about it. It starts unset, exactly as the shipment
     * CreateShipmentHandler and ShipmentProductAssigner build does, and is filled in afterwards by
     * ShipmentShippingCostUpdater when PS_ORDER_RECALCULATE_SHIPPING allows it.
     */
    public function testSplitDoesNotCarryTheSourceCarriersShippingCostOver(): void
    {
        $source = $this->createSourceShipment(5.0, 6.0);

        $newShipment = $this->createSplitter()->split($source, self::TARGET_CARRIER_ID, [
            $this->shipmentProduct(self::ORDER_DETAIL_ID, 1),
        ]);

        $this->assertSame(0.0, $newShipment->getShippingCostTaxExcluded());
        $this->assertSame(0.0, $newShipment->getShippingCostTaxIncluded());
        $this->assertNotSame($source->getShippingCostTaxExcluded(), $newShipment->getShippingCostTaxExcluded());
        $this->assertNotSame($source->getShippingCostTaxIncluded(), $newShipment->getShippingCostTaxIncluded());
        $this->assertSame(5.0, $source->getShippingCostTaxExcluded(), 'the source keeps what it was charged');
        $this->assertSame(6.0, $source->getShippingCostTaxIncluded(), 'the source keeps what it was charged');
    }

    /**
     * Same carrier on both sides is the case where inheriting the cost looks harmless. It is not: the
     * order would end up billed for that carrier twice.
     */
    public function testSplitOntoTheSameCarrierDoesNotDuplicateTheShippingCostEither(): void
    {
        $source = $this->createSourceShipment(5.0, 6.0);

        $newShipment = $this->createSplitter()->split($source, self::SOURCE_CARRIER_ID, [
            $this->shipmentProduct(self::ORDER_DETAIL_ID, 1),
        ]);

        $this->assertSame(0.0, $newShipment->getShippingCostTaxExcluded());
        $this->assertSame(0.0, $newShipment->getShippingCostTaxIncluded());
    }

    /**
     * Everything else the new shipment needs still comes from the source - the cost is the only thing
     * that does not travel with it.
     */
    public function testSplitStillTakesTheOrderAddressAndProductsFromTheSource(): void
    {
        $source = $this->createSourceShipment(5.0, 6.0);

        $newShipment = $this->createSplitter()->split($source, self::TARGET_CARRIER_ID, [
            $this->shipmentProduct(self::ORDER_DETAIL_ID, 1),
        ]);

        $this->assertSame(self::TARGET_CARRIER_ID, $newShipment->getCarrierId());
        $this->assertSame(self::ORDER_ID, $newShipment->getOrderId());
        $this->assertSame(self::ADDRESS_ID, $newShipment->getAddressId());
        $this->assertNull($newShipment->getTrackingNumber());
        $this->assertCount(1, $newShipment->getProducts());
        $this->assertSame(1, $newShipment->getProducts()->first()->getQuantity());
        $this->assertSame(1, $source->getProducts()->first()->getQuantity(), 'the moved quantity left the source');
    }

    private function createSplitter(): ShipmentSplitter
    {
        return new ShipmentSplitter($this->createMock(TranslatorInterface::class));
    }

    private function createSourceShipment(float $costTaxExcluded, float $costTaxIncluded): Shipment
    {
        $source = new Shipment();
        $source->setOrderId(self::ORDER_ID);
        $source->setCarrierId(self::SOURCE_CARRIER_ID);
        $source->setAddressId(self::ADDRESS_ID);
        $source->setTrackingNumber(null);
        $source->setShippingCostTaxExcluded($costTaxExcluded);
        $source->setShippingCostTaxIncluded($costTaxIncluded);
        $source->addShipmentProduct($this->shipmentProduct(self::ORDER_DETAIL_ID, 2));

        return $source;
    }

    private function shipmentProduct(int $orderDetailId, int $quantity): ShipmentProduct
    {
        return (new ShipmentProduct())
            ->setOrderDetailId($orderDetailId)
            ->setQuantity($quantity);
    }
}
