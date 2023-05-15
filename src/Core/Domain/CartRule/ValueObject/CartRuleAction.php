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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;

class CartRuleAction
{
    /**
     * @var bool
     */
    private $freeShipping;

    /**
     * @var GiftProduct|null
     */
    private $giftProduct;

    /**
     * @var Money|null
     */
    private $amountDiscount;

    /**
     * @var PercentageDiscount|null
     */
    private $percentageDiscount;

    /**
     * @var DiscountApplicationType|null
     */
    private $discountApplicationType;

    /**
     * Constructor is private intentionally. Use static builders to build this class.
     *
     * @see buildFreeShipping
     * @see buildGiftProduct
     * @see buildAmountDiscount
     * @see buildPercentageDiscount
     *
     * @param bool $freeShipping
     * @param GiftProduct|null $giftProduct
     * @param Money|null $amountDiscount
     * @param PercentageDiscount|null $percentageDiscount
     * @param DiscountApplicationType|null $discountApplicationType
     */
    private function __construct(
        bool $freeShipping,
        ?GiftProduct $giftProduct = null,
        ?Money $amountDiscount = null,
        ?PercentageDiscount $percentageDiscount = null,
        ?DiscountApplicationType $discountApplicationType = null
    ) {
        $this->freeShipping = $freeShipping;
        $this->giftProduct = $giftProduct;
        $this->amountDiscount = $amountDiscount;
        $this->percentageDiscount = $percentageDiscount;
        $this->discountApplicationType = $discountApplicationType;
    }

    /**
     * Builds cart rule action which provides free shipping, and can also offer a gift product
     *
     * @param GiftProduct|null $giftProduct
     *
     * @return CartRuleAction
     */
    public static function buildFreeShipping(?GiftProduct $giftProduct = null): CartRuleAction
    {
        return new CartRuleAction(
            true,
            $giftProduct
        );
    }

    /**
     * Builds cart rule action which offers only a gift product
     *
     * @param GiftProduct $giftProduct
     *
     * @return CartRuleAction
     */
    public static function buildGiftProduct(GiftProduct $giftProduct): CartRuleAction
    {
        return new CartRuleAction(
            false,
            $giftProduct
        );
    }

    /**
     * Builds cart rule action which provides amount discount in certain currency, and can as well offer free shipping and/or a gift product
     *
     * @param Money $amountDiscount
     * @param bool $freeShipping
     * @param DiscountApplicationType $discountApplicationType
     * @param GiftProduct|null $giftProduct
     *
     * @return CartRuleAction
     *
     * @throws CartRuleConstraintException
     */
    public static function buildAmountDiscount(
        Money $amountDiscount,
        bool $freeShipping,
        DiscountApplicationType $discountApplicationType,
        ?GiftProduct $giftProduct = null
    ): CartRuleAction {
        $unsupportedTypeMessages = [
            DiscountApplicationType::CHEAPEST_PRODUCT => 'Cart rule, which is applied to cheapest product, cannot be applied to amount discount type.',
            DiscountApplicationType::SELECTED_PRODUCTS => 'Cart rule, which is applied to selected products, cannot be applied to amount discount type.',
        ];

        if (isset($unsupportedTypeMessages[$discountApplicationType->getType()])) {
            throw new CartRuleConstraintException(
                $unsupportedTypeMessages[$discountApplicationType->getType()],
                CartRuleConstraintException::INVALID_DISCOUNT_APPLICATION_TYPE
            );
        }

        return new CartRuleAction(
            $freeShipping,
            $giftProduct,
            $amountDiscount,
            null,
            $discountApplicationType
        );
    }

    /**
     * Builds cart rule action which provides percentage discount, and can as well offer free shipping and/or a gift product
     *
     * @param DecimalNumber $reductionValue
     * @param bool $applyToDiscountedProducts
     * @param bool $freeShipping
     * @param DiscountApplicationType $discountApplicationType
     * @param GiftProduct|null $giftProduct
     *
     * @return CartRuleAction
     */
    public static function buildPercentageDiscount(
        DecimalNumber $reductionValue,
        bool $applyToDiscountedProducts,
        bool $freeShipping,
        DiscountApplicationType $discountApplicationType,
        ?GiftProduct $giftProduct = null
    ): CartRuleAction {
        return new CartRuleAction(
            $freeShipping,
            $giftProduct,
            null,
            new PercentageDiscount(
                $reductionValue,
                $applyToDiscountedProducts
            ),
            $discountApplicationType
        );
    }

    public function isFreeShipping(): bool
    {
        return $this->freeShipping;
    }

    public function getPercentageDiscount(): ?PercentageDiscount
    {
        return $this->percentageDiscount;
    }

    public function getAmountDiscount(): ?Money
    {
        return $this->amountDiscount;
    }

    public function getGiftProduct(): ?GiftProduct
    {
        return $this->giftProduct;
    }

    public function getDiscountApplicationType(): ?DiscountApplicationType
    {
        return $this->discountApplicationType;
    }
}
