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

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

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
    private $shippingCostRefund;

    /**
     * @var bool
     */
    private $restockRefundedProducts;

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
     * @param int $orderId
     * @param array $orderDetailRefunds
     * @param float $shippingCostRefund
     * @param bool $restockRefundedProducts
     * @param bool $generateVoucher
     * @param int $voucherRefundType
     * @param float|null $voucherRefundAmount
     */
    public function __construct(
        int $orderId,
        array $orderDetailRefunds,
        float $shippingCostRefund,
        bool $restockRefundedProducts,
        bool $generateVoucher,
        int $voucherRefundType,
        float $voucherRefundAmount = null
    ) {
        $this->orderId = new OrderId($orderId);
        $this->orderDetailRefunds = $orderDetailRefunds;
        $this->shippingCostRefund = $shippingCostRefund;
        $this->restockRefundedProducts = $restockRefundedProducts;
        $this->generateVoucher = $generateVoucher;
        $this->voucherRefundType = $voucherRefundType;
        $this->voucherRefundAmount = $voucherRefundAmount;
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
     * @return float
     */
    public function getShippingCostRefundAmount(): float
    {
        return $this->shippingCostRefund;
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
