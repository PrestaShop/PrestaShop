<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

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

    public function __construct(
        int $orderId,
        int $currencyId,
        string $reference,
        string $taxMethod,
        bool $isValid,
        OrderCustomerForViewing $customer,
        OrderShippingAddressForViewing $shippingAddress,
        OrderInvoiceAddressForViewing $invoiceAddress,
        OrderProductsForViewing $products,
        OrderHistoryForViewing $history,
        OrderDocumentsForViewing $documents,
        OrderShippingForViewing $shipping,
        OrderReturnsForViewing $returns,
        OrderPaymentsForViewing $payments,
        OrderMessagesForViewing $messages
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
     * @return OrderCustomerForViewing
     */
    public function getCustomer(): OrderCustomerForViewing
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
     * @return OrderMessagesForViewing
     */
    public function getMessages(): OrderMessagesForViewing
    {
        return $this->messages;
    }
}
