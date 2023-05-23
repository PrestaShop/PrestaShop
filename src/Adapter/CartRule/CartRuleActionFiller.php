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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CartRule;

use CartRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\Discount;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

class CartRuleActionFiller
{
    /**
     * @param CartRule $cartRule
     * @param CartRuleAction $cartRuleAction
     *
     * @return string[] list of updatable properties which were filled
     */
    public function fillUpdatableProperties(
        CartRule $cartRule,
        CartRuleAction $cartRuleAction
    ): array {
        $discount = $cartRuleAction->getDiscount();

        if ($discount) {
            $this->fillDiscount($cartRule, $discount);
        }

        $giftProduct = $cartRuleAction->getGiftProduct();
        if (null !== $giftProduct) {
            $cartRule->gift_product = $giftProduct->getProductId()->getValue();
            $cartRule->gift_product_attribute = $giftProduct->getCombinationId() ? $giftProduct->getCombinationId()->getValue() : null;
        } else {
            $cartRule->gift_product = null;
            $cartRule->gift_product_attribute = null;
        }

        $cartRule->free_shipping = $cartRuleAction->isFreeShipping();

        // always return all the properties related to the action, because when one action is set the other one must be reset,
        // so we always end up updating all of them when action related field is being updated.
        return [
            'reduction_amount',
            'reduction_percent',
            'reduction_currency',
            'reduction_tax',
            'reduction_exclude_special',
            'reduction_product',
            'free_shipping',
            'gift_product',
            'gift_product_attribute',
        ];
    }

    /**
     * @param CartRule $cartRule
     * @param ?Discount $discount
     */
    private function fillDiscount(CartRule $cartRule, ?Discount $discount): void
    {
        // when there is no discount action, we reset all the related properties to defaults
        if (!$discount) {
            $cartRule->reduction_amount = 0;
            $cartRule->reduction_currency = 0;
            $cartRule->reduction_tax = false;
            $cartRule->reduction_percent = 0;
            $cartRule->reduction_exclude_special = false;
            $cartRule->reduction_product = LegacyDiscountApplicationType::ORDER_WITHOUT_SHIPPING;

            return;
        }

        $percentageDiscount = $discount->getPercentageDiscount();
        $amountDiscount = $discount->getAmountDiscount();

        if ($amountDiscount) {
            $this->fillAmountDiscount($cartRule, $amountDiscount);
        } else {
            $this->fillPercentageDiscount($cartRule, $percentageDiscount);
        }

        $this->fillDiscountApplicationType($cartRule, $discount->getDiscountApplicationType());
    }

    /**
     * @param CartRule $cartRule
     * @param DiscountApplicationType $discountApplicationType
     */
    private function fillDiscountApplicationType(
        CartRule $cartRule,
        DiscountApplicationType $discountApplicationType
    ): void {
        switch ($discountApplicationType->getType()) {
            case DiscountApplicationType::SELECTED_PRODUCTS:
                $discountApplicationValue = LegacyDiscountApplicationType::SELECTED_PRODUCTS;
                break;
            case DiscountApplicationType::CHEAPEST_PRODUCT:
                $discountApplicationValue = LegacyDiscountApplicationType::CHEAPEST_PRODUCT;
                break;
            case DiscountApplicationType::SPECIFIC_PRODUCT:
                $discountApplicationValue = $discountApplicationType->getProductId()->getValue();
                break;
            default:
                $discountApplicationValue = LegacyDiscountApplicationType::ORDER_WITHOUT_SHIPPING;
        }

        //@todo: specific/selected/cheapest product is selected it might be that product_restriction muts be set to true.
        $cartRule->reduction_product = $discountApplicationValue;
    }

    private function fillAmountDiscount(CartRule $cartRule, Money $amountDiscount): void
    {
        $cartRule->reduction_amount = (float) (string) $amountDiscount->getAmount();
        $cartRule->reduction_currency = $amountDiscount->getCurrencyId()->getValue();
        $cartRule->reduction_tax = $amountDiscount->isTaxIncluded();
        $cartRule->reduction_percent = 0;
        $cartRule->reduction_exclude_special = false;
    }

    private function fillPercentageDiscount(CartRule $cartRule, PercentageDiscount $percentageDiscount): void
    {
        $cartRule->reduction_percent = (float) (string) $percentageDiscount->getPercentage();
        $cartRule->reduction_exclude_special = !$percentageDiscount->applyToDiscountedProducts();
        $cartRule->reduction_amount = 0;
        $cartRule->reduction_currency = 0;
        $cartRule->reduction_tax = false;
    }
}
