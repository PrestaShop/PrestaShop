<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailRefund;

/**
 * Issues standard refund for given order.
 */
class IssueStandardRefundCommand extends AbstractRefundCommand
{
    /**
     * @var bool
     */
    protected $refundShippingCost;

    /**
     * The expected format for $orderDetailRefunds is an associative array indexed
     * by OrderDetail id containing one fields quantity
     *
     * ex: $orderDetailRefunds = [
     *      {orderId} => [
     *          'quantity' => 2,
     *      ],
     * ];
     *
     * @param int $orderId
     * @param array $orderDetailRefunds
     * @param bool $refundShippingCost
     * @param bool $generateCreditSlip
     * @param bool $generateVoucher
     * @param int $voucherRefundType
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     */
    public function __construct(
        int $orderId,
        array $orderDetailRefunds,
        bool $refundShippingCost,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType
    ) {
        parent::__construct(
            $orderId,
            $orderDetailRefunds,
            true,
            $generateCreditSlip,
            $generateVoucher,
            $voucherRefundType
        );
        $this->refundShippingCost = $refundShippingCost;
    }

    /**
     * @return bool
     */
    public function refundShippingCost(): bool
    {
        return $this->refundShippingCost;
    }

    /**
     * {@inheritdoc}
     */
    protected function setOrderDetailRefunds(array $orderDetailRefunds)
    {
        $this->orderDetailRefunds = [];
        foreach ($orderDetailRefunds as $orderDetailId => $detailRefund) {
            $this->orderDetailRefunds[] = OrderDetailRefund::createStandardRefund(
                $orderDetailId,
                $detailRefund['quantity']
            );
        }
    }
}
