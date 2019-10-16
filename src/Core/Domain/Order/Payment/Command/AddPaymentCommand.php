<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
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
     * @var Number
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
     * @param int|null $orderInvoiceId
     * @param string|null $transactionId transaction ID, usually payment ID from payment gateway
     */
    public function __construct(
        $orderId,
        $paymentDate,
        $paymentMethod,
        $paymentAmount,
        $paymentCurrencyId,
        $orderInvoiceId = null,
        $transactionId = null
    ) {
        $this->assertPaymentMethodIsGenericName($paymentMethod);

        $this->orderId = new OrderId($orderId);
        $this->paymentDate = new DateTimeImmutable($paymentDate);
        $this->paymentMethod = $paymentMethod;
        $this->paymentAmount = new Number($paymentAmount);
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
     * @return Number
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
     * @param string $paymentMethod
     */
    private function assertPaymentMethodIsGenericName($paymentMethod)
    {
        if (empty($paymentMethod) || !preg_match('/^[^<>={}]*$/u', $paymentMethod)) {
            throw new OrderConstraintException('The selected payment method is invalid.');
        }
    }
}
