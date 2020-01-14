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

namespace PrestaShop\PrestaShop\Adapter\Order\Refund;

use OrderDetail;

/**
 * Container of all the necessary information for an order refund
 */
class OrderRefundDetail
{
    /**
     * @var OrderDetail[]
     */
    private $orderDetails;

    /**
     * @var array
     */
    private $productRefunds;

    /**
     * @var float
     */
    private $refundedAmount;

    /**
     * @var float
     */
    private $refundedShipping;

    /**
     * @var float
     */
    private $voucherAmount;

    /**
     * @var bool
     */
    private $voucherChosen;

    /**
     * @var bool
     */
    private $isTaxIncluded;

    public function __construct(
        array $orderDetails,
        array $productRefunds,
        float $refundedAmount,
        float $refundedShipping,
        float $voucherAmount,
        bool $voucherChosen,
        bool $isTaxIncluded
    ) {
        $this->orderDetails = $orderDetails;
        $this->productRefunds = $productRefunds;
        $this->refundedAmount = $refundedAmount;
        $this->refundedShipping = $refundedShipping;
        $this->voucherAmount = $voucherAmount;
        $this->voucherChosen = $voucherChosen;
        $this->isTaxIncluded = $isTaxIncluded;
    }

    /**
     * @return OrderDetail[]
     */
    public function getOrderDetails(): array
    {
        return $this->orderDetails;
    }

    /**
     * @return array
     */
    public function getProductRefunds(): array
    {
        return $this->productRefunds;
    }

    /**
     * @return float
     */
    public function getRefundedAmount(): float
    {
        return $this->refundedAmount;
    }

    /**
     * @return float
     */
    public function getRefundedShipping(): float
    {
        return $this->refundedShipping;
    }

    /**
     * @return float
     */
    public function getVoucherAmount(): float
    {
        return $this->voucherAmount;
    }

    /**
     * @return bool
     */
    public function isVoucherChosen(): bool
    {
        return $this->voucherChosen;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->isTaxIncluded;
    }

    /**
     * @param int $orderDetailId
     *
     * @return OrderDetail|null
     */
    public function getOrderDetailById(int $orderDetailId): ?OrderDetail
    {
        return isset($this->orderDetails[$orderDetailId]) ? $this->orderDetails[$orderDetailId] : null;
    }
}
