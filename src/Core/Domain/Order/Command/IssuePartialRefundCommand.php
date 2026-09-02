<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use InvalidArgumentException;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailRefund;

/**
 * Issues partial refund for given order.
 */
class IssuePartialRefundCommand extends AbstractRefundCommand
{
    /**
     * @var DecimalNumber
     */
    private $shippingCostRefundAmount;

    /**
     * The expected format for $orderDetailRefunds is an associative array indexed
     * by OrderDetail id containing two fields amount and quantity
     *
     * ex: $orderDetailRefunds = [
     *      {orderId} => [
     *          'quantity' => 2,
     *          'amount' => 23.56,
     *      ],
     * ];
     *
     * @param int $orderId
     * @param array $orderDetailRefunds
     * @param string $shippingCostRefundAmount
     * @param bool $restockRefundedProducts
     * @param bool $generateVoucher
     * @param bool $generateCreditSlip
     * @param int $voucherRefundType
     * @param string|null $voucherRefundAmount
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     */
    public function __construct(
        int $orderId,
        array $orderDetailRefunds,
        string $shippingCostRefundAmount,
        bool $restockRefundedProducts,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType,
        ?string $voucherRefundAmount = null
    ) {
        parent::__construct(
            $orderId,
            $orderDetailRefunds,
            $restockRefundedProducts,
            $generateCreditSlip,
            $generateVoucher,
            $voucherRefundType,
            $voucherRefundAmount
        );
        try {
            $this->shippingCostRefundAmount = new DecimalNumber($shippingCostRefundAmount);
        } catch (InvalidArgumentException) {
            throw new InvalidAmountException();
        }
    }

    /**
     * @return DecimalNumber
     */
    public function getShippingCostRefundAmount(): DecimalNumber
    {
        return $this->shippingCostRefundAmount;
    }

    /**
     * {@inheritdoc}
     */
    protected function setOrderDetailRefunds(array $orderDetailRefunds)
    {
        $this->orderDetailRefunds = [];
        foreach ($orderDetailRefunds as $orderDetailId => $detailRefund) {
            $this->orderDetailRefunds[] = OrderDetailRefund::createPartialRefund(
                $orderDetailId,
                $detailRefund['quantity'],
                $detailRefund['amount']
            );
        }
    }
}
