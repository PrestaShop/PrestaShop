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

class OrderPaymentsForViewing
{
    /**
     * hint - collection would be better
     *
     * @var OrderPaymentForViewing[]
     */
    private $payments = [];

    /**
     * @var string|null
     */
    private $amountToPay;

    /**
     * @var string|null
     */
    private $paidAmount;

    /**
     * @var array
     */
    private $paymentMismatchOrderIds;

    /**
     * @param OrderPaymentForViewing[] $payments
     * @param string|null $amountToPay
     * @param string|null $paidAmount
     * @param int[] $paymentMismatchOrderIds
     */
    public function __construct(
        array $payments,
        ?string $amountToPay,
        ?string $paidAmount,
        array $paymentMismatchOrderIds
    ) {
        foreach ($payments as $payment) {
            $this->add($payment);
        }

        $this->amountToPay = $amountToPay;
        $this->paidAmount = $paidAmount;
        $this->paymentMismatchOrderIds = $paymentMismatchOrderIds;
    }

    /**
     * @return OrderPaymentForViewing[]
     */
    public function getPayments(): array
    {
        return $this->payments;
    }

    /**
     * @return string|null
     */
    public function getAmountToPay(): ?string
    {
        return $this->amountToPay;
    }

    /**
     * @return string|null
     */
    public function getPaidAmount(): ?string
    {
        return $this->paidAmount;
    }

    /**
     * @return array
     */
    public function getPaymentMismatchOrderIds(): array
    {
        return $this->paymentMismatchOrderIds;
    }

    /**
     * @param OrderPaymentForViewing $payment
     */
    private function add(OrderPaymentForViewing $payment): void
    {
        $this->payments[] = $payment;
    }
}
