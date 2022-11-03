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

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Invoice\ValueObject\OrderInvoiceId;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderDiscountType;
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
     * @var DecimalNumber|null
     */
    private $value;

    /**
     * @var OrderInvoiceId|null
     */
    private $orderInvoiceId;

    /**
     * @param int $orderId
     * @param string $cartRuleName
     * @param string $cartRuleType
     * @param string|null $value
     * @param int|null $orderInvoiceId
     */
    public function __construct(
        int $orderId,
        string $cartRuleName,
        string $cartRuleType,
        ?string $value,
        $orderInvoiceId = null
    ) {
        $this->assertCartRuleNameIsNotEmpty($cartRuleName);
        $this->assertCartRuleTypeAndValueCombination($cartRuleType, $value);

        $this->orderId = new OrderId($orderId);
        $this->cartRuleName = $cartRuleName;
        $this->cartRuleType = $cartRuleType;
        $this->value = null !== $value ? new DecimalNumber($value) : null;
        $this->orderInvoiceId = $orderInvoiceId ? new OrderInvoiceId($orderInvoiceId) : null;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getCartRuleName(): string
    {
        return $this->cartRuleName;
    }

    /**
     * @return string
     */
    public function getCartRuleType(): string
    {
        return $this->cartRuleType;
    }

    /**
     * @return DecimalNumber|null
     */
    public function getDiscountValue(): ?DecimalNumber
    {
        return $this->value;
    }

    /**
     * @return OrderInvoiceId|null
     */
    public function getOrderInvoiceId(): ?OrderInvoiceId
    {
        return $this->orderInvoiceId;
    }

    /**
     * @param string $cartRuleName
     */
    private function assertCartRuleNameIsNotEmpty($cartRuleName): void
    {
        if (!is_string($cartRuleName) || empty($cartRuleName)) {
            throw new OrderConstraintException('Cart rule name cannot be empty');
        }
    }

    private function assertCartRuleTypeAndValueCombination(string $cartRuleType, ?string $value): void
    {
        $isNullValueAllowed = OrderDiscountType::FREE_SHIPPING === $cartRuleType;

        if (!$isNullValueAllowed && null === $value) {
            throw new OrderConstraintException(
                sprintf(
                    'Null values are not allowed for "%s" discount types.',
                    implode(',', [
                        OrderDiscountType::DISCOUNT_AMOUNT,
                        OrderDiscountType::DISCOUNT_PERCENT,
                    ])
                )
            );
        }
    }
}
