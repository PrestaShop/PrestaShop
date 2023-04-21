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

//@todo: is it worth re-designing it to return $reduction_product property instead, so it doesn't depend on legacy obj model and easier to test?
//       in that case, what about the naming. ReductionProductValueProvider/extractor?
class DiscountApplicationFiller
{
    /**
     * @param CartRule $cartRule
     * @param CartRuleActionInterface $cartRuleAction
     */
    public function fillDiscountApplicationType(
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
