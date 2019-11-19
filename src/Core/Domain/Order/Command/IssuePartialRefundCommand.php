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

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
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
     * @var int
     */
    private $shippingCostRefund;

    /**
     * @var bool
     */
    private $restockRefundedProducts;

    /**
     * @var bool
     */
    private $generateCartRule;

    /**
     * @var int
     */
    private $taxMethod;

    /**
     * @var int
     */
    private $cartRuleRefundType;

    /**
     * @var float|null
     */
    private $cartRuleRefundAmount;

    /**
     * @param int $orderId
     * @param array $orderDetailRefunds
     * @param int $shippingCostRefund
     * @param bool $restockRefundedProducts
     * @param bool $generateCartRule
     * @param bool $taxMethod
     * @param int $cartRuleRefundType
     * @param float|null $cartRuleRefundAmount
     */
    public function __construct(
        $orderId,
        array $orderDetailRefunds,
        $shippingCostRefund,
        $restockRefundedProducts,
        $generateCartRule,
        $taxMethod,
        $cartRuleRefundType,
        $cartRuleRefundAmount = null
    ) {
        $this->orderId = new OrderId($orderId);
        $this->orderDetailRefunds = $orderDetailRefunds;
        $this->shippingCostRefund = $shippingCostRefund;
        $this->restockRefundedProducts = $restockRefundedProducts;
        $this->generateCartRule = $generateCartRule;
        $this->taxMethod = $taxMethod;
        $this->cartRuleRefundType = $cartRuleRefundType;
        $this->cartRuleRefundAmount = $cartRuleRefundAmount;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return array
     */
    public function getOrderDetailRefunds()
    {
        return $this->orderDetailRefunds;
    }

    /**
     * @return bool
     */
    public function getTaxMethod()
    {
        return $this->taxMethod;
    }

    /**
     * @return int
     */
    public function getShippingCostRefundAmount()
    {
        return $this->shippingCostRefund;
    }

    /**
     * @return bool
     */
    public function restockRefundedProducts()
    {
        return $this->restockRefundedProducts;
    }

    /**
     * @return bool
     */
    public function generateCartRule()
    {
        return $this->generateCartRule;
    }

    /**
     * @return mixed
     */
    public function getCartRuleRefundType()
    {
        return $this->cartRuleRefundType;
    }

    public function getCartRuleRefundAmount()
    {
        return $this->cartRuleRefundAmount;
    }
}
