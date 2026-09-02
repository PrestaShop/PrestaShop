<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
