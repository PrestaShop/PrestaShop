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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

/**
 * Contains data about order for viewing
 */
class OrderForViewing
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @var OrderCustomerForViewing
     */
    private $customer;

    /**
     * @var OrderShippingAddressForViewing
     */
    private $shippingAddress;

    /**
     * @var OrderInvoiceAddressForViewing
     */
    private $invoiceAddress;
    /**
     * @var string
     */
    private $reference;

    /**
     * @var OrderProductsForViewing
     */
    private $products;

    /**
     * @var string
     */
    private $taxMethod;

    /**
     * @var OrderHistoryForViewing
     */
    private $history;

    /**
     * @var OrderDocumentsForViewing
     */
    private $documents;

    /**
     * @var OrderShippingForViewing
     */
    private $shipping;

    /**
     * @var OrderReturnsForViewing
     */
    private $returns;

    /**
     * @var OrderPaymentsForViewing
     */
    private $payments;

    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var OrderMessagesForViewing
     */
    private $messages;

    /**
     * @var int
     */
    private $currencyId;

    /**
     * @var bool
     */
    private $isDelivered;

    /**
     * @var bool
     */
    private $isShipped;

    /**
     * @var OrderPricesForViewing
     */
    private $prices;

    /**
     * @var bool
     */
    private $isTaxIncluded;

    /**
     * @var bool
     */
    private $hasBeenPaid;

    /**
     * @var bool
     */
    private $hasInvoice;

    /**
     * @var OrderDiscountsForViewing
     */
    private $discounts;

    /**
     * @var LinkedOrdersForViewing
     */
    private $linkedOrders;

    /**
     * @var DateTimeImmutable
     */
    private $createdAt;

    /**
     * @var bool
     */
    private $isVirtual;

    /**
     * @var int
     */
    private $carrierId;

    /**
     * @var string
     */
    private $carrierName;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var bool
     */
    private $invoiceManagementIsEnabled;

    /**
     * @var OrderSourcesForViewing
     */
    private $sources;

    /**
     * @var string
     */
    private $shippingAddressFormatted;

    /**
     * @var string
     */
    private $invoiceAddressFormatted;

    /**
     * @var string
     */
    private $note;

    /**
     * @var bool
     */
    private $invoice;

    /**
     * @param int $orderId
     * @param int $currencyId
     * @param int $carrierId
     * @param string $carrierName
     * @param int $shopId
     * @param string $reference
     * @param bool $isVirtual
     * @param string $taxMethod
     * @param bool $isTaxIncluded
     * @param bool $isValid
     * @param bool $hasBeenPaid
     * @param bool $hasInvoice
     * @param bool $isDelivered
     * @param bool $isShipped
     * @param bool $invoiceManagementIsEnabled
     * @param DateTimeImmutable $createdAt
     * @param OrderCustomerForViewing|null $customer
     * @param OrderShippingAddressForViewing $shippingAddress
     * @param OrderInvoiceAddressForViewing $invoiceAddress
     * @param OrderProductsForViewing $products
     * @param OrderHistoryForViewing $history
     * @param OrderDocumentsForViewing $documents
     * @param OrderShippingForViewing $shipping
     * @param OrderReturnsForViewing $returns
     * @param OrderPaymentsForViewing $payments
     * @param OrderMessagesForViewing $messages
     * @param OrderPricesForViewing $prices
     * @param OrderDiscountsForViewing $discounts
     * @param OrderSourcesForViewing $sources
     * @param LinkedOrdersForViewing $linkedOrders
     * @param string $shippingAddressFormatted
     * @param string $invoiceAddressFormatted
     * @param string $note
     * @param bool $invoice
     */
    public function __construct(
        int $orderId,
        int $currencyId,
        int $carrierId,
        string $carrierName,
        int $shopId,
        string $reference,
        bool $isVirtual,
        string $taxMethod,
        bool $isTaxIncluded,
        bool $isValid,
        bool $hasBeenPaid,
        bool $hasInvoice,
        bool $isDelivered,
        bool $isShipped,
        bool $invoiceManagementIsEnabled,
        DateTimeImmutable $createdAt,
        ?OrderCustomerForViewing $customer,
        OrderShippingAddressForViewing $shippingAddress,
        OrderInvoiceAddressForViewing $invoiceAddress,
        OrderProductsForViewing $products,
        OrderHistoryForViewing $history,
        OrderDocumentsForViewing $documents,
        OrderShippingForViewing $shipping,
        OrderReturnsForViewing $returns,
        OrderPaymentsForViewing $payments,
        OrderMessagesForViewing $messages,
        OrderPricesForViewing $prices,
        OrderDiscountsForViewing $discounts,
        OrderSourcesForViewing $sources,
        LinkedOrdersForViewing $linkedOrders,
        string $shippingAddressFormatted = '',
        string $invoiceAddressFormatted = '',
        string $note = '',
        bool $invoice = true
    ) {
        $this->reference = $reference;
        $this->customer = $customer;
        $this->shippingAddress = $shippingAddress;
        $this->invoiceAddress = $invoiceAddress;
        $this->products = $products;
        $this->taxMethod = $taxMethod;
        $this->history = $history;
        $this->documents = $documents;
        $this->shipping = $shipping;
        $this->returns = $returns;
        $this->payments = $payments;
        $this->isValid = $isValid;
        $this->messages = $messages;
        $this->orderId = $orderId;
        $this->currencyId = $currencyId;
        $this->isDelivered = $isDelivered;
        $this->isShipped = $isShipped;
        $this->prices = $prices;
        $this->isTaxIncluded = $isTaxIncluded;
        $this->hasBeenPaid = $hasBeenPaid;
        $this->hasInvoice = $hasInvoice;
        $this->discounts = $discounts;
        $this->createdAt = $createdAt;
        $this->isVirtual = $isVirtual;
        $this->carrierId = $carrierId;
        $this->carrierName = $carrierName;
        $this->shopId = $shopId;
        $this->invoiceManagementIsEnabled = $invoiceManagementIsEnabled;
        $this->sources = $sources;
        $this->linkedOrders = $linkedOrders;
        $this->shippingAddressFormatted = $shippingAddressFormatted;
        $this->invoiceAddressFormatted = $invoiceAddressFormatted;
        $this->note = $note;
        $this->invoice = $invoice;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * @return int
     */
    public function getCarrierId(): int
    {
        return $this->carrierId;
    }

    public function getCarrierName(): string
    {
        return $this->carrierName;
    }

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->isValid;
    }

    /**
     * @return OrderCustomerForViewing|null
     */
    public function getCustomer(): ?OrderCustomerForViewing
    {
        return $this->customer;
    }

    /**
     * @return OrderShippingAddressForViewing
     */
    public function getShippingAddress(): OrderShippingAddressForViewing
    {
        return $this->shippingAddress;
    }

    /**
     * @return OrderInvoiceAddressForViewing
     */
    public function getInvoiceAddress(): OrderInvoiceAddressForViewing
    {
        return $this->invoiceAddress;
    }

    /**
     * @return OrderProductsForViewing
     */
    public function getProducts(): OrderProductsForViewing
    {
        return $this->products;
    }

    /**
     * @return string
     */
    public function getTaxMethod(): string
    {
        return $this->taxMethod;
    }

    /**
     * @return OrderHistoryForViewing
     */
    public function getHistory(): OrderHistoryForViewing
    {
        return $this->history;
    }

    /**
     * @return OrderDocumentsForViewing
     */
    public function getDocuments(): OrderDocumentsForViewing
    {
        return $this->documents;
    }

    /**
     * @return OrderShippingForViewing
     */
    public function getShipping(): OrderShippingForViewing
    {
        return $this->shipping;
    }

    /**
     * @return OrderReturnsForViewing
     */
    public function getReturns(): OrderReturnsForViewing
    {
        return $this->returns;
    }

    /**
     * @return OrderPaymentsForViewing
     */
    public function getPayments(): OrderPaymentsForViewing
    {
        return $this->payments;
    }

    /**
     * @return bool
     */
    public function hasPayments(): bool
    {
        return count($this->payments->getPayments()) > 0;
    }

    /**
     * @return OrderMessagesForViewing
     */
    public function getMessages(): OrderMessagesForViewing
    {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->isDelivered;
    }

    /**
     * @return bool
     */
    public function isShipped(): bool
    {
        return $this->isShipped;
    }

    /**
     * @return OrderPricesForViewing
     */
    public function getPrices(): OrderPricesForViewing
    {
        return $this->prices;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->isTaxIncluded;
    }

    /**
     * @return bool
     */
    public function hasBeenPaid(): bool
    {
        return $this->hasBeenPaid;
    }

    /**
     * @return bool
     */
    public function hasInvoice(): bool
    {
        return $this->hasInvoice;
    }

    /**
     * @return OrderDiscountsForViewing
     */
    public function getDiscounts(): OrderDiscountsForViewing
    {
        return $this->discounts;
    }

    /**
     * @return LinkedOrdersForViewing
     */
    public function getLinkedOrders(): LinkedOrdersForViewing
    {
        return $this->linkedOrders;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return bool
     */
    public function isVirtual(): bool
    {
        return $this->isVirtual;
    }

    /**
     * @return bool
     */
    public function isInvoiceManagementIsEnabled(): bool
    {
        return $this->invoiceManagementIsEnabled;
    }

    /**
     * @return OrderSourcesForViewing
     */
    public function getSources(): OrderSourcesForViewing
    {
        return $this->sources;
    }

    /**
     * @return bool
     */
    public function isRefundable(): bool
    {
        /** @var OrderProductForViewing $product */
        foreach ($this->products->getProducts() as $product) {
            if ($product->getQuantity() > $product->getQuantityRefunded()) {
                return true;
            }
        }

        return $this->prices->getShippingRefundableAmountRaw()->isGreaterThanZero();
    }

    /**
     * @return string
     */
    public function getShippingAddressFormatted(): string
    {
        return $this->shippingAddressFormatted;
    }

    /**
     * @return string
     */
    public function getInvoiceAddressFormatted(): string
    {
        return $this->invoiceAddressFormatted;
    }

    /**
     * @return string
     */
    public function getNote(): string
    {
        return $this->note;
    }

    /**
     * @return bool
     */
    public function getInvoice(): bool
    {
        return $this->invoice;
    }
}
