<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Entity;

use DateTime;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\Shipment;
use PrestaShopBundle\Entity\ShipmentProduct;

class ShipmentTest extends TestCase
{
    public function testShipmentCreation(): void
    {
        $shipment = new Shipment();

        $shipment->setCarrierId(1);
        $shipment->setAddressId(1);
        $shipment->setOrderId(1);
        $shipment->setShippingCostTaxExcluded(10.5);
        $shipment->setShippingCostTaxIncluded(12.6);
        $shipment->setTrackingNumber('FR123456789');

        $this->assertEquals(1, $shipment->getCarrierId());
        $this->assertEquals(1, $shipment->getAddressId());
        $this->assertEquals(1, $shipment->getOrderId());
        $this->assertEquals(10.5, $shipment->getShippingCostTaxExcluded());
        $this->assertEquals(12.6, $shipment->getShippingCostTaxIncluded());
        $this->assertEquals('FR123456789', $shipment->getTrackingNumber());
    }

    public function testShipmentLifecycle(): void
    {
        $shipment = new Shipment();
        $shipment->setPackedAt(new DateTime('2024-02-01 10:00:00'));
        $shipment->setShippedAt(new DateTime('2024-02-02 12:00:00'));
        $shipment->setDeliveredAt(new DateTime('2024-02-03 15:00:00'));
        $shipment->setCancelledAt(new DateTime('2024-02-04 18:00:00'));

        $this->assertEquals(
            '2024-02-01 10:00:00',
            $shipment->getPackedAt()->format('Y-m-d H:i:s')
        );
        $this->assertEquals(
            '2024-02-02 12:00:00',
            $shipment->getShippedAt()->format('Y-m-d H:i:s')
        );
        $this->assertEquals(
            '2024-02-03 15:00:00',
            $shipment->getDeliveredAt()->format('Y-m-d H:i:s')
        );
        $this->assertEquals(
            '2024-02-04 18:00:00',
            $shipment->getCancelledAt()->format('Y-m-d H:i:s')
        );
    }

    public function testShipmentProductAssociation(): void
    {
        $shipment = new Shipment();
        $shipmentProduct = new ShipmentProduct();

        $shipmentProduct->setShipment($shipment);
        $shipmentProduct->setOrderDetailId(101);
        $shipmentProduct->setQuantity(3);
        $shipmentProduct->setShipment($shipment);

        $this->assertEquals(101, $shipmentProduct->getOrderDetailId());
        $this->assertEquals(3, $shipmentProduct->getQuantity());
        $this->assertSame($shipment, $shipmentProduct->getShipment());
    }

    public function testAddProductsToShipment(): void
    {
        $shipment = new Shipment();

        for ($i = 0; $i < 5; ++$i) {
            $shipmentProduct = new ShipmentProduct();
            $shipmentProduct->setQuantity($i + 1);
            $shipmentProduct->setOrderDetailId($i);
            $shipmentProduct->setShipment($shipment);
            $shipment->addShipmentProduct($shipmentProduct);
        }

        $this->assertCount(5, $shipment->getProducts());
        $this->assertEquals(1, $shipment->getProducts()[0]->getQuantity());
        $this->assertEquals(2, $shipment->getProducts()[1]->getQuantity());
        $this->assertEquals(3, $shipment->getProducts()[2]->getQuantity());
        $this->assertEquals(4, $shipment->getProducts()[3]->getQuantity());
    }
}
