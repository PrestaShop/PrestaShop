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
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;

class CartRuleActionFiller
{
    /**
     * @param CartRule $cartRule
     * @param CartRuleActionInterface $cartRuleAction
     *
     * @return string[] list of updatable properties which were filled
     */
    public function fillUpdatableProperties(
        CartRule $cartRule,
        CartRuleActionInterface $cartRuleAction
    ): array {
        $updatableProperties = [];
        $amountDiscount = $cartRuleAction->getAmountDiscount();
        if (null !== $amountDiscount) {
            $cartRule->reduction_amount = (float) (string) $amountDiscount->getAmount();
            $cartRule->reduction_currency = $amountDiscount->getCurrencyId()->getValue();
            $cartRule->reduction_tax = $amountDiscount->isTaxIncluded();
            $cartRule->reduction_percent = 0;
            $cartRule->reduction_exclude_special = false;
            $updatableProperties[] = 'reduction_amount';
            $updatableProperties[] = 'reduction_percent';
            $updatableProperties[] = 'reduction_currency';
            $updatableProperties[] = 'reduction_tax';
            $updatableProperties[] = 'reduction_exclude_special';
        }

        $percentageDiscount = $cartRuleAction->getPercentageDiscount();
        if (null !== $percentageDiscount) {
            $cartRule->reduction_percent = (float) (string) $percentageDiscount->getPercentage();
            $cartRule->reduction_exclude_special = !$percentageDiscount->applyToDiscountedProducts();
            $cartRule->reduction_amount = 0;
            $cartRule->reduction_currency = 0;
            $cartRule->reduction_tax = false;
            $updatableProperties[] = 'reduction_amount';
            $updatableProperties[] = 'reduction_percent';
            $updatableProperties[] = 'reduction_currency';
            $updatableProperties[] = 'reduction_tax';
            $updatableProperties[] = 'reduction_exclude_special';
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
        $updatableProperties[] = 'free_shipping';
        $updatableProperties[] = 'gift_product';
        $updatableProperties[] = 'gift_product_attribute';

        if (null !== $cartRuleAction->getDiscountApplicationType()) {
            $this->fillDiscountApplicationType($cartRule, $cartRuleAction);
            $updatableProperties[] = 'reduction_product';
        }

        return $updatableProperties;
    }

    /**
     * @param CartRule $cartRule
     * @param CartRuleActionInterface $cartRuleAction
     */
    private function fillDiscountApplicationType(
        CartRule $cartRule,
        CartRuleActionInterface $cartRuleAction
    ): void {
        $discountApplicationType = $cartRuleAction->getDiscountApplicationType();

        if ((!$cartRuleAction->getAmountDiscount() && !$cartRuleAction->getPercentageDiscount()) || !$discountApplicationType) {
            return;
        }

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

        $cartRule->reduction_product = $discountApplicationValue;
    }
}
