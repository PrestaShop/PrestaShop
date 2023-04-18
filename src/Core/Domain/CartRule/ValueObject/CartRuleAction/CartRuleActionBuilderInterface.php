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
     * @param string $reductionValue
     * @param bool $applyToDiscountedProducts
     * @param string $discountApplicationType
     * @param int|null $productId
     *
     * @return CartRuleActionBuilderInterface
     */
    public function setPercentageDiscount(
        string $reductionValue,
        bool $applyToDiscountedProducts,
        string $discountApplicationType,
        ?int $productId = null
    ): CartRuleActionBuilderInterface;

    /**
     * Set amount discount for cart rule action.
     *
     * @param string $reductionValue
     * @param int $currencyId
     * @param bool $taxIncluded
     * @param string $discountApplicationType
     * @param int|null $productId
     *
     * @return CartRuleActionBuilderInterface
     */
    public function setAmountDiscount(
        string $reductionValue,
        int $currencyId,
        bool $taxIncluded,
        string $discountApplicationType,
        ?int $productId = null
    ): CartRuleActionBuilderInterface;

    /**
     * Set the gift product for cart rule action.
     *
     * @param int $productId
     * @param int|null $combinationId
     *
     * @return CartRuleActionBuilderInterface
     */
    public function setGiftProduct(int $productId, ?int $combinationId = null): CartRuleActionBuilderInterface;

    /**
     * Build the cart rule action.
     *
     * @return CartRuleActionInterface
     */
    public function build(): CartRuleActionInterface;
}
