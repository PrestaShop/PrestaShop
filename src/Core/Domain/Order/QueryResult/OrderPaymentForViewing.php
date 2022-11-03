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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use DateTimeImmutable;

class OrderPaymentForViewing
{
    /**
     * @var int
     */
    private $paymentId;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $paymentMethod;

    /**
     * @var string
     */
    private $transactionId;

    /**
     * @var string
     */
    private $amount;

    /**
     * @var string|null
     */
    private $invoiceNumber;

    /**
     * @var string
     */
    private $cardNumber;

    /**
     * @var string
     */
    private $cardBrand;

    /**
     * @var string
     */
    private $cardExpiration;

    /**
     * @var string
     */
    private $cardHolder;

    /**
     * @var string|null
     */
    protected $employeeName;

    /**
     * @param int $paymentId
     * @param DateTimeImmutable $date
     * @param string $paymentMethod
     * @param string $transactionId
     * @param string $amount
     * @param string|null $invoiceNumber
     * @param string $cardNumber
     * @param string $cardBrand
     * @param string $cardExpiration
     * @param string $cardHolder
     * @param string|null $employeeName
     */
    public function __construct(
        int $paymentId,
        DateTimeImmutable $date,
        string $paymentMethod,
        string $transactionId,
        string $amount,
        ?string $invoiceNumber,
        string $cardNumber,
        string $cardBrand,
        string $cardExpiration,
        string $cardHolder,
        ?string $employeeName = null
    ) {
        $this->paymentId = $paymentId;
        $this->date = $date;
        $this->paymentMethod = $paymentMethod;
        $this->transactionId = $transactionId;
        $this->amount = $amount;
        $this->invoiceNumber = $invoiceNumber;
        $this->cardNumber = $cardNumber;
        $this->cardBrand = $cardBrand;
        $this->cardExpiration = $cardExpiration;
        $this->cardHolder = $cardHolder;
        $this->employeeName = $employeeName;
    }

    /**
     * @return int
     */
    public function getPaymentId(): int
    {
        return $this->paymentId;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return string|null
     */
    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    /**
     * @return string
     */
    public function getCardNumber(): string
    {
        return $this->cardNumber;
    }

    /**
     * @return string
     */
    public function getCardBrand(): string
    {
        return $this->cardBrand;
    }

    /**
     * @return string
     */
    public function getCardExpiration(): string
    {
        return $this->cardExpiration;
    }

    /**
     * @return string
     */
    public function getCardHolder(): string
    {
        return $this->cardHolder;
    }

    /**
     * @return string|null
     */
    public function getEmployeeName(): ?string
    {
        return $this->employeeName;
    }
}
