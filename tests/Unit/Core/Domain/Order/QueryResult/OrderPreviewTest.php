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
