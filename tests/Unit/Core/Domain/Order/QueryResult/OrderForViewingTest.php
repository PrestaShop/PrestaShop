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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Core\Domain\Order\QueryResult;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\LinkedOrdersForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderCustomerForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDiscountsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDocumentsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderHistoryForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderInvoiceAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderMessagesForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPricesForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderReturnsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingAddressForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderShippingForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderSourcesForViewing;

class OrderForViewingTest extends TestCase
{
    public function testConstruct(): void
    {
        $mockCreatedAt = new DateTimeImmutable();
        $mockCustomer = $this->createMock(OrderCustomerForViewing::class);
        $mockShippingAddress = $this->createMock(OrderShippingAddressForViewing::class);
        $mockInvoiceAddress = $this->createMock(OrderInvoiceAddressForViewing::class);
        $mockProducts = $this->createMock(OrderProductsForViewing::class);
        $mockHistory = $this->createMock(OrderHistoryForViewing::class);
        $mockDocuments = $this->createMock(OrderDocumentsForViewing::class);
        $mockShipping = $this->createMock(OrderShippingForViewing::class);
        $mockReturns = $this->createMock(OrderReturnsForViewing::class);
        $mockPayments = $this->createMock(OrderPaymentsForViewing::class);
        $mockMessages = $this->createMock(OrderMessagesForViewing::class);
        $mockPrices = $this->createMock(OrderPricesForViewing::class);
        $mockDiscounts = $this->createMock(OrderDiscountsForViewing::class);
        $mockSources = $this->createMock(OrderSourcesForViewing::class);
        $mockLinkedOrders = $this->createMock(LinkedOrdersForViewing::class);

        $instance = new OrderForViewing(
            0,
            1,
            2,
            'a',
            3,
            'b',
            true,
            'c',
            true,
            true,
            true,
            true,
            true,
            true,
            true,
            $mockCreatedAt,
            $mockCustomer,
            $mockShippingAddress,
            $mockInvoiceAddress,
            $mockProducts,
            $mockHistory,
            $mockDocuments,
            $mockShipping,
            $mockReturns,
            $mockPayments,
            $mockMessages,
            $mockPrices,
            $mockDiscounts,
            $mockSources,
            $mockLinkedOrders
        );

        self::assertSame(0, $instance->getId());
        self::assertSame(1, $instance->getCurrencyId());
        self::assertSame(2, $instance->getCarrierId());
        self::assertSame('a', $instance->getCarrierName());
        self::assertSame(3, $instance->getShopId());
        self::assertSame('b', $instance->getReference());
        self::assertSame(true, $instance->isVirtual());
        self::assertSame('c', $instance->getTaxMethod());
        self::assertSame(true, $instance->isTaxIncluded());
        self::assertSame(true, $instance->isValid());
        self::assertSame(true, $instance->hasBeenPaid());
        self::assertSame(true, $instance->hasInvoice());
        self::assertSame(true, $instance->isDelivered());
        self::assertSame(true, $instance->isShipped());
        self::assertSame(true, $instance->isInvoiceManagementIsEnabled());
        self::assertSame($mockCreatedAt, $instance->getCreatedAt());
        self::assertSame($mockCustomer, $instance->getCustomer());
        self::assertSame($mockShippingAddress, $instance->getShippingAddress());
        self::assertSame($mockInvoiceAddress, $instance->getInvoiceAddress());
        self::assertSame($mockProducts, $instance->getProducts());
        self::assertSame($mockHistory, $instance->getHistory());
        self::assertSame($mockDocuments, $instance->getDocuments());
        self::assertSame($mockShipping, $instance->getShipping());
        self::assertSame($mockReturns, $instance->getReturns());
        self::assertSame($mockPayments, $instance->getPayments());
        self::assertSame($mockMessages, $instance->getMessages());
        self::assertSame($mockPrices, $instance->getPrices());
        self::assertSame($mockDiscounts, $instance->getDiscounts());
        self::assertSame($mockSources, $instance->getSources());
        self::assertSame($mockLinkedOrders, $instance->getLinkedOrders());
        self::assertSame('', $instance->getShippingAddressFormatted());
        self::assertSame('', $instance->getInvoiceAddressFormatted());
    }

    public function testConstructWithShippingAddressFormatted(): void
    {
        $createdAt = new DateTimeImmutable();
        $mockCustomer = $this->createMock(OrderCustomerForViewing::class);
        $mockShippingAddress = $this->createMock(OrderShippingAddressForViewing::class);
        $mockInvoiceAddress = $this->createMock(OrderInvoiceAddressForViewing::class);
        $mockProducts = $this->createMock(OrderProductsForViewing::class);
        $mockHistory = $this->createMock(OrderHistoryForViewing::class);
        $mockDocuments = $this->createMock(OrderDocumentsForViewing::class);
        $mockShipping = $this->createMock(OrderShippingForViewing::class);
        $mockReturns = $this->createMock(OrderReturnsForViewing::class);
        $mockPayments = $this->createMock(OrderPaymentsForViewing::class);
        $mockMessages = $this->createMock(OrderMessagesForViewing::class);
        $mockPrices = $this->createMock(OrderPricesForViewing::class);
        $mockDiscounts = $this->createMock(OrderDiscountsForViewing::class);
        $mockSources = $this->createMock(OrderSourcesForViewing::class);
        $mockLinkedOrders = $this->createMock(LinkedOrdersForViewing::class);

        $instance = new OrderForViewing(
            0,
            1,
            2,
            'a',
            3,
            'b',
            true,
            'c',
            true,
            true,
            true,
            true,
            true,
            true,
            true,
            $createdAt,
            $mockCustomer,
            $mockShippingAddress,
            $mockInvoiceAddress,
            $mockProducts,
            $mockHistory,
            $mockDocuments,
            $mockShipping,
            $mockReturns,
            $mockPayments,
            $mockMessages,
            $mockPrices,
            $mockDiscounts,
            $mockSources,
            $mockLinkedOrders,
            'd'
        );

        self::assertSame(0, $instance->getId());
        self::assertSame(1, $instance->getCurrencyId());
        self::assertSame(2, $instance->getCarrierId());
        self::assertSame('a', $instance->getCarrierName());
        self::assertSame(3, $instance->getShopId());
        self::assertSame('b', $instance->getReference());
        self::assertSame(true, $instance->isVirtual());
        self::assertSame('c', $instance->getTaxMethod());
        self::assertSame(true, $instance->isTaxIncluded());
        self::assertSame(true, $instance->isValid());
        self::assertSame(true, $instance->hasBeenPaid());
        self::assertSame(true, $instance->hasInvoice());
        self::assertSame(true, $instance->isDelivered());
        self::assertSame(true, $instance->isShipped());
        self::assertSame(true, $instance->isInvoiceManagementIsEnabled());
        self::assertSame($createdAt, $instance->getCreatedAt());
        self::assertSame($mockCustomer, $instance->getCustomer());
        self::assertSame($mockShippingAddress, $instance->getShippingAddress());
        self::assertSame($mockInvoiceAddress, $instance->getInvoiceAddress());
        self::assertSame($mockProducts, $instance->getProducts());
        self::assertSame($mockHistory, $instance->getHistory());
        self::assertSame($mockDocuments, $instance->getDocuments());
        self::assertSame($mockShipping, $instance->getShipping());
        self::assertSame($mockReturns, $instance->getReturns());
        self::assertSame($mockPayments, $instance->getPayments());
        self::assertSame($mockMessages, $instance->getMessages());
        self::assertSame($mockPrices, $instance->getPrices());
        self::assertSame($mockDiscounts, $instance->getDiscounts());
        self::assertSame($mockSources, $instance->getSources());
        self::assertSame($mockLinkedOrders, $instance->getLinkedOrders());
        self::assertSame('d', $instance->getShippingAddressFormatted());
        self::assertSame('', $instance->getInvoiceAddressFormatted());
    }

    public function testConstructWithInvoiceAddressFormatted(): void
    {
        $createdAt = new DateTimeImmutable();
        $mockCustomer = $this->createMock(OrderCustomerForViewing::class);
        $mockShippingAddress = $this->createMock(OrderShippingAddressForViewing::class);
        $mockInvoiceAddress = $this->createMock(OrderInvoiceAddressForViewing::class);
        $mockProducts = $this->createMock(OrderProductsForViewing::class);
        $mockHistory = $this->createMock(OrderHistoryForViewing::class);
        $mockDocuments = $this->createMock(OrderDocumentsForViewing::class);
        $mockShipping = $this->createMock(OrderShippingForViewing::class);
        $mockReturns = $this->createMock(OrderReturnsForViewing::class);
        $mockPayments = $this->createMock(OrderPaymentsForViewing::class);
        $mockMessages = $this->createMock(OrderMessagesForViewing::class);
        $mockPrices = $this->createMock(OrderPricesForViewing::class);
        $mockDiscounts = $this->createMock(OrderDiscountsForViewing::class);
        $mockSources = $this->createMock(OrderSourcesForViewing::class);
        $mockLinkedOrders = $this->createMock(LinkedOrdersForViewing::class);

        $instance = new OrderForViewing(
            0,
            1,
            2,
            'a',
            3,
            'b',
            true,
            'c',
            true,
            true,
            true,
            true,
            true,
            true,
            true,
            $createdAt,
            $mockCustomer,
            $mockShippingAddress,
            $mockInvoiceAddress,
            $mockProducts,
            $mockHistory,
            $mockDocuments,
            $mockShipping,
            $mockReturns,
            $mockPayments,
            $mockMessages,
            $mockPrices,
            $mockDiscounts,
            $mockSources,
            $mockLinkedOrders,
            'd',
            'e'
        );

        self::assertSame(0, $instance->getId());
        self::assertSame(1, $instance->getCurrencyId());
        self::assertSame(2, $instance->getCarrierId());
        self::assertSame('a', $instance->getCarrierName());
        self::assertSame(3, $instance->getShopId());
        self::assertSame('b', $instance->getReference());
        self::assertSame(true, $instance->isVirtual());
        self::assertSame('c', $instance->getTaxMethod());
        self::assertSame(true, $instance->isTaxIncluded());
        self::assertSame(true, $instance->isValid());
        self::assertSame(true, $instance->hasBeenPaid());
        self::assertSame(true, $instance->hasInvoice());
        self::assertSame(true, $instance->isDelivered());
        self::assertSame(true, $instance->isShipped());
        self::assertSame(true, $instance->isInvoiceManagementIsEnabled());
        self::assertSame($createdAt, $instance->getCreatedAt());
        self::assertSame($mockCustomer, $instance->getCustomer());
        self::assertSame($mockShippingAddress, $instance->getShippingAddress());
        self::assertSame($mockInvoiceAddress, $instance->getInvoiceAddress());
        self::assertSame($mockProducts, $instance->getProducts());
        self::assertSame($mockHistory, $instance->getHistory());
        self::assertSame($mockDocuments, $instance->getDocuments());
        self::assertSame($mockShipping, $instance->getShipping());
        self::assertSame($mockReturns, $instance->getReturns());
        self::assertSame($mockPayments, $instance->getPayments());
        self::assertSame($mockMessages, $instance->getMessages());
        self::assertSame($mockPrices, $instance->getPrices());
        self::assertSame($mockDiscounts, $instance->getDiscounts());
        self::assertSame($mockSources, $instance->getSources());
        self::assertSame($mockLinkedOrders, $instance->getLinkedOrders());
        self::assertSame('d', $instance->getShippingAddressFormatted());
        self::assertSame('e', $instance->getInvoiceAddressFormatted());
    }
}
