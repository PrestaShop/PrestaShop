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

use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\GiftProduct;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\MoneyAmountCondition;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;

/**
 * Describes a cart rule action.
 */
interface CartRuleActionInterface
{
    /**
     * Check if this cart rule is giving free shipping.
     *
     * @return bool
     */
    public function isFreeShipping(): bool;

    /**
     * Get the amount discount, which this cart rule action is giving.
     *
     * @return MoneyAmountCondition|null
     */
    public function getAmountDiscount(): ?MoneyAmountCondition;

    /**
     * Get the percentage discount, which this cart rule action is giving.
     *
     * @return PercentageDiscount|null
     */
    public function getPercentageDiscount(): ?PercentageDiscount;

    /**
     * Get the gift product, which this cart rule action is giving.
     *
     * @return GiftProduct|null returns null when not applicable
     */
    public function getGiftProduct(): ?GiftProduct;
}
