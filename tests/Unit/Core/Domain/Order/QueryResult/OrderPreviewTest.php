<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Order\QueryResult;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewInvoiceDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreviewShippingDetails;

class OrderPreviewTest extends TestCase
{
    public function testConstruct(): void
    {
        $mockInvoiceDetails = $this->createMock(OrderPreviewInvoiceDetails::class);
        $mockShippingDetails = $this->createMock(OrderPreviewShippingDetails::class);

        $instance = new OrderPreview($mockInvoiceDetails, $mockShippingDetails, ['a'], true, false);
        self::assertSame($mockInvoiceDetails, $instance->getInvoiceDetails());
        self::assertSame($mockShippingDetails, $instance->getShippingDetails());
        self::assertSame(['a'], $instance->getProductDetails());
        self::assertTrue($instance->isVirtual());
        self::assertFalse($instance->isTaxIncluded());
        self::assertEquals('', $instance->getInvoiceAddressFormatted());
        self::assertEquals('', $instance->getShippingAddressFormatted());
    }

    public function testConstructWithInvoiceAddressFormatted(): void
    {
        $mockInvoiceDetails = $this->createMock(OrderPreviewInvoiceDetails::class);
        $mockShippingDetails = $this->createMock(OrderPreviewShippingDetails::class);

        $instance = new OrderPreview($mockInvoiceDetails, $mockShippingDetails, ['a'], true, false, 'b');
        self::assertSame($mockInvoiceDetails, $instance->getInvoiceDetails());
        self::assertSame($mockShippingDetails, $instance->getShippingDetails());
        self::assertSame(['a'], $instance->getProductDetails());
        self::assertTrue($instance->isVirtual());
        self::assertFalse($instance->isTaxIncluded());
        self::assertEquals('b', $instance->getInvoiceAddressFormatted());
        self::assertEquals('', $instance->getShippingAddressFormatted());
    }

    public function testConstructWithShippingAddressFormatted(): void
    {
        $mockInvoiceDetails = $this->createMock(OrderPreviewInvoiceDetails::class);
        $mockShippingDetails = $this->createMock(OrderPreviewShippingDetails::class);

        $instance = new OrderPreview($mockInvoiceDetails, $mockShippingDetails, ['a'], true, false, 'b', 'c');
        self::assertSame($mockInvoiceDetails, $instance->getInvoiceDetails());
        self::assertSame($mockShippingDetails, $instance->getShippingDetails());
        self::assertSame(['a'], $instance->getProductDetails());
        self::assertTrue($instance->isVirtual());
        self::assertFalse($instance->isTaxIncluded());
        self::assertEquals('b', $instance->getInvoiceAddressFormatted());
        self::assertEquals('c', $instance->getShippingAddressFormatted());
    }
}
