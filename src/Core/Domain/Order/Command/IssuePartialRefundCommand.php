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
        } catch (InvalidArgumentException $e) {
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
