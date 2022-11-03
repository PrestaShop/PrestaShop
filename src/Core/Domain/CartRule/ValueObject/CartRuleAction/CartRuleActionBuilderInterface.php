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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;

use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;

/**
 * Describes a builder which builds cart rule actions.
 */
interface CartRuleActionBuilderInterface
{
    /**
     * Set free shipping for cart rule action.
     *
     * @param bool $freeShipping
     *
     * @return CartRuleActionBuilderInterface
     */
    public function setFreeShipping(bool $freeShipping): CartRuleActionBuilderInterface;

    /**
     * Set percentage discount for cart rule action.
     *
     * @param PercentageDiscount $percentageDiscount
     *
     * @return CartRuleActionBuilderInterface
     */
    public function setPercentageDiscount(PercentageDiscount $percentageDiscount): CartRuleActionBuilderInterface;

    /**
     * Set amount discount for cart rule action.
     *
     * @param MoneyAmountCondition $amount
     *
     * @return CartRuleActionBuilderInterface
     */
    public function setAmountDiscount(MoneyAmountCondition $amount): CartRuleActionBuilderInterface;

    /**
     * Set the gift product for cart rule action.
     *
     * @param GiftProduct $giftProduct
     *
     * @return CartRuleActionBuilderInterface
     */
    public function setGiftProduct(GiftProduct $giftProduct): CartRuleActionBuilderInterface;

    /**
     * Build the cart rule action.
     *
     * @return CartRuleActionInterface
     */
    public function build(): CartRuleActionInterface;
}
