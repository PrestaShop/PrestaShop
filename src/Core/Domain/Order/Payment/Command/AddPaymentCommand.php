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

namespace PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Adds payment for given order.
 */
class AddPaymentCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var DateTimeImmutable
     */
    private $paymentDate;

    /**
     * @var string
     */
    private $paymentMethod;

    /**
     * @var float
     */
    private $paymentAmount;

    /**
     * @var CurrencyId
     */
    private $paymentCurrencyId;

    /**
     * @var string|null
     */
    private $transactionId;

    /**
     * @var null
     */
    private $orderInvoiceId;

    /**
     * @param int $orderId
     * @param string $paymentDate
     * @param string $paymentMethod
     * @param float $paymentAmount
     * @param float $paymentCurrencyId
     * @param null $orderInvoiceId
     * @param string $transactionId
     */
    public function __construct(
        $orderId,
        $paymentDate,
        $paymentMethod,
        $paymentAmount,
        $paymentCurrencyId,
        $orderInvoiceId = null,
        $transactionId = ''
    ) {
        $this->assertAmountIsNotNegative($paymentAmount);
        $this->assertPaymentMethodIsGenericName($paymentMethod);
        $this->assertTransactionIdIsString($transactionId);

        $this->orderId = new OrderId($orderId);
        $this->paymentDate = new DateTimeImmutable($paymentDate);
        $this->paymentMethod = $paymentMethod;
        $this->paymentAmount = $paymentAmount;
        $this->paymentCurrencyId = new CurrencyId($paymentCurrencyId);
        $this->orderInvoiceId = $orderInvoiceId;
        $this->transactionId = $transactionId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @return float
     */
    public function getPaymentAmount()
    {
        return $this->paymentAmount;
    }

    /**
     * @return CurrencyId
     */
    public function getPaymentCurrencyId()
    {
        return $this->paymentCurrencyId;
    }

    /**
     * @return null
     */
    public function getOrderInvoiceId()
    {
        return $this->orderInvoiceId;
    }

    /**
     * @return string|null
     */
    public function getPaymentTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param float $paymentAmount
     */
    private function assertAmountIsNotNegative($paymentAmount)
    {
        if (!is_float($paymentAmount) || 0 >= $paymentAmount) {
            throw new OrderConstraintException('The amount is invalid.');
        }
    }

    /**
     * @param string $paymentMethod
     */
    private function assertPaymentMethodIsGenericName($paymentMethod)
    {
        if (empty($paymentMethod) || preg_match('/^[^<>={}]*$/u', $paymentMethod)) {
            throw new OrderConstraintException('The selected payment method is invalid.');
        }
    }

    /**
     * @param string $transactionId
     */
    private function assertTransactionIdIsString($transactionId)
    {
        if (!is_string($transactionId)) {
            throw new OrderException('The transaction ID is invalid.');
        }
    }
}
