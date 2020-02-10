<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidRefundException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderDetailRefund;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Issues partial refund for given order.
 */
class IssuePartialRefundCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var array
     */
    private $orderDetailRefunds;

    /**
     * @var float
     */
    private $shippingCostRefundAmount;

    /**
     * @var bool
     */
    private $restockRefundedProducts;

    /**
     * @var bool
     */
    private $generateCreditSlip;

    /**
     * @var bool
     */
    private $generateVoucher;

    /**
     * @var int
     */
    private $voucherRefundType;

    /**
     * @var float|null
     */
    private $voucherRefundAmount;

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
     * @param float $shippingCostRefundAmount
     * @param bool $restockRefundedProducts
     * @param bool $generateVoucher
     * @param bool $generateCreditSlip
     * @param int $voucherRefundType
     * @param float|null $voucherRefundAmount
     *
     * @throws InvalidRefundException
     * @throws OrderException
     */
    public function __construct(
        int $orderId,
        array $orderDetailRefunds,
        float $shippingCostRefundAmount,
        bool $restockRefundedProducts,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType,
        float $voucherRefundAmount = null
    ) {
        $this->orderId = new OrderId($orderId);
        $this->shippingCostRefundAmount = $shippingCostRefundAmount;
        $this->restockRefundedProducts = $restockRefundedProducts;
        $this->generateCreditSlip = $generateCreditSlip;
        $this->generateVoucher = $generateVoucher;
        $this->voucherRefundType = $voucherRefundType;
        $this->voucherRefundAmount = $voucherRefundAmount;
        $this->setOrderDetailRefunds($orderDetailRefunds);
        if (!$this->generateCreditSlip && !$this->generateVoucher) {
            throw new InvalidRefundException(InvalidRefundException::NO_GENERATION);
        }
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return array
     */
    public function getOrderDetailRefunds(): array
    {
        return $this->orderDetailRefunds;
    }

    /**
     * @param array $orderDetailRefunds
     *
     * @throws InvalidRefundException
     * @throws OrderException
     */
    private function setOrderDetailRefunds(array $orderDetailRefunds)
    {
        $this->orderDetailRefunds = [];
        if (0 >= count($orderDetailRefunds)) {
            throw new InvalidRefundException(InvalidRefundException::NO_REFUNDS);
        }

        foreach ($orderDetailRefunds as $orderDetailId => $detailRefund) {
            $this->orderDetailRefunds[] = OrderDetailRefund::createPartialRefund(
                $orderDetailId,
                $detailRefund['quantity'],
                $detailRefund['amount']
            );
        }
    }

    /**
     * @return float
     */
    public function getShippingCostRefundAmount(): float
    {
        return $this->shippingCostRefundAmount;
    }

    /**
     * @return bool
     */
    public function restockRefundedProducts(): bool
    {
        return $this->restockRefundedProducts;
    }

    /**
     * @return bool
     */
    public function generateCreditSlip(): bool
    {
        return $this->generateCreditSlip;
    }

    /**
     * @return bool
     */
    public function generateVoucher(): bool
    {
        return $this->generateVoucher;
    }

    /**
     * @return int
     */
    public function getVoucherRefundType(): int
    {
        return $this->voucherRefundType;
    }

    /**
     * @return float|null
     */
    public function getVoucherRefundAmount(): ?float
    {
        return $this->voucherRefundAmount;
    }
}
