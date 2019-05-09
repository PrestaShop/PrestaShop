<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Adds cart rule to given order.
 */
class AddCartRuleToOrderCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var string
     */
    private $cartRuleName;

    /**
     * @var string
     */
    private $cartRuleType;

    /**
     * @var float
     */
    private $value;

    /**
     * @var int|null
     */
    private $orderInvoiceId;

    /**
     * @param int $orderId
     */
    public function __construct($orderId, $cartRuleName, $cartRuleType, $value, $orderInvoiceId = null)
    {
        $this->assertCartRuleNameIsNotEmpty($cartRuleName);

        $this->orderId = new OrderId($orderId);
        $this->cartRuleName = $cartRuleName;
        $this->cartRuleType = $cartRuleType;
        $this->value = $value;
        $this->orderInvoiceId = $orderInvoiceId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getCartRuleName()
    {
        return $this->cartRuleName;
    }

    /**
     * @return string
     */
    public function getCartRuleType()
    {
        return $this->cartRuleType;
    }

    /**
     * @return float
     */
    public function getDiscountValue()
    {
        return $this->value;
    }

    /**
     * @return int|null
     */
    public function getOrderInvoiceId()
    {
        return $this->orderInvoiceId;
    }

    /**
     * @param string $cartRuleName
     */
    private function assertCartRuleNameIsNotEmpty($cartRuleName)
    {
        if (!is_string($cartRuleName) || empty($cartRuleName)) {
            throw new OrderConstraintException('Cart rule name cannot be empty');
        }
    }
}
