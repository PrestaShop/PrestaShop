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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;

use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;

/**
 * Builds cart rule actions.
 */
final class CartRuleActionBuilder implements CartRuleActionBuilderInterface
{
    /**
     * @var bool
     */
    private $isFreeShipping = false;

    /**
     * @var PercentageDiscount|null
     */
    private $percentageDiscount;

    /**
     * @var MoneyAmountCondition|null
     */
    private $amountDiscount;

    /**
     * @var GiftProduct|null
     */
    private $giftProduct;

    /**
     * {@inheritdoc}
     */
    public function setFreeShipping(bool $isFreeShipping): CartRuleActionBuilderInterface
    {
        $this->isFreeShipping = $isFreeShipping;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPercentageDiscount(PercentageDiscount $percentageDiscount): CartRuleActionBuilderInterface
    {
        $this->percentageDiscount = $percentageDiscount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmountDiscount(MoneyAmountCondition $amount): CartRuleActionBuilderInterface
    {
        $this->amountDiscount = $amount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftProduct(GiftProduct $giftProduct): CartRuleActionBuilderInterface
    {
        $this->giftProduct = $giftProduct;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): CartRuleActionInterface
    {
        $this->assertCartRuleActionsAreValid();

        if (null !== $this->percentageDiscount) {
            $action = new PercentageDiscountAction(
                $this->percentageDiscount,
                $this->isFreeShipping,
                $this->giftProduct
            );
        } elseif (null !== $this->amountDiscount) {
            $action = new AmountDiscountAction(
                $this->amountDiscount,
                $this->isFreeShipping,
                $this->giftProduct
            );
        } elseif (true === $this->isFreeShipping) {
            $action = new FreeShippingAction($this->giftProduct);
        } else {
            $action = new GiftProductAction($this->giftProduct);
        }

        return $action;
    }

    /**
     * @throws CartRuleConstraintException
     */
    private function assertCartRuleActionsAreValid()
    {
        if (null !== $this->percentageDiscount && null !== $this->amountDiscount) {
            throw new CartRuleConstraintException('Cart rule cannot have both percentage and amount discount actions.', CartRuleConstraintException::INCOMPATIBLE_CART_RULE_ACTIONS);
        }

        if (null === $this->percentageDiscount &&
            null === $this->amountDiscount &&
            null === $this->giftProduct &&
            false === $this->isFreeShipping
        ) {
            throw new CartRuleConstraintException('Cart rule must have at least one action', CartRuleConstraintException::MISSING_ACTION);
        }
    }
}
