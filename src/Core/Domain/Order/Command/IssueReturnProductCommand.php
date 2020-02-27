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

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidRefundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;

/**
 * Issues return product for given order.
 */
class IssueReturnProductCommand extends IssueStandardRefundCommand
{
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
     * @param bool $restockRefundedProducts
     * @param bool $refundShippingCost
     * @param bool $generateCreditSlip
     * @param bool $generateVoucher
     * @param int $voucherRefundType
     * @param float|null $voucherRefundAmount
     *
     * @throws InvalidRefundException
     * @throws OrderException
     */
    public function __construct(
        int $orderId,
        array $orderDetailRefunds,
        bool $restockRefundedProducts,
        bool $refundShippingCost,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType,
        ?float $voucherRefundAmount = null
    ) {
        parent::__construct(
            $orderId,
            $orderDetailRefunds,
            $refundShippingCost,
            $generateCreditSlip,
            $generateVoucher,
            $voucherRefundType,
            $voucherRefundAmount
        );
        $this->restockRefundedProducts = $restockRefundedProducts;
    }
}
