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

/**
 * Represents a reduction value for a cart rule. It can reduce certain percentage of price or a specific amount of Money.
 * Both percentage and amount discounts has a type, which indicates what the discount should be applied to.
 *
 * @see Money
 * @see PercentageDiscount
 * @see DiscountApplicationType
 */
class Discount
{
    /**
     * @var DiscountApplicationType
     */
    private $discountApplicationType;

    /**
     * @var Money|null
     */
    private $amountDiscount;

    /**
     * @var PercentageDiscount|null
     */
    private $percentageDiscount;

    /**
     * Static factory method to build amount reduction type discount
     *
     * @param Money $amountDiscount
     * @param DiscountApplicationType $discountApplicationType
     *
     * @return self
     *
     * @throws CartRuleConstraintException
     */
    public static function buildAmountDiscount(
        Money $amountDiscount,
        DiscountApplicationType $discountApplicationType
    ): self {
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

        return new self(
            $discountApplicationType,
            $amountDiscount,
            null
        );
    }

    /**
     * Static factory method to build percentage reduction type discount
     *
     * @param DecimalNumber $reductionValue
     * @param bool $applyToDiscountedProducts
     * @param DiscountApplicationType $discountApplicationType
     *
     * @return self
     *
     * @throws CartRuleConstraintException
     */
    public static function buildPercentageDiscount(
        DecimalNumber $reductionValue,
        bool $applyToDiscountedProducts,
        DiscountApplicationType $discountApplicationType
    ): self {
        return new self(
            $discountApplicationType,
            null,
            new PercentageDiscount(
                $reductionValue,
                $applyToDiscountedProducts
            )
        );
    }

    public function getDiscountApplicationType(): DiscountApplicationType
    {
        return $this->discountApplicationType;
    }

    public function getAmountDiscount(): ?Money
    {
        return $this->amountDiscount;
    }

    public function getPercentageDiscount(): ?PercentageDiscount
    {
        return $this->percentageDiscount;
    }

    /**
     * Constructor is private intentionally. Use corresponding static method to build this class.
     *
     * @see buildAmountDiscount
     * @see buildPercentageDiscount
     *
     * @param DiscountApplicationType $discountApplicationType
     * @param Money|null $amountDiscount
     * @param PercentageDiscount|null $percentageDiscount
     *
     * @throws CartRuleConstraintException
     */
    private function __construct(
        DiscountApplicationType $discountApplicationType,
        ?Money $amountDiscount,
        ?PercentageDiscount $percentageDiscount
    ) {
        if (($amountDiscount && $percentageDiscount) || (!$amountDiscount && !$percentageDiscount)) {
            throw new CartRuleConstraintException(
                sprintf('Only one of the following must be set for %s: $amountDiscount or $percentageDiscount', self::class),
                CartRuleConstraintException::INVALID_PRICE_DISCOUNT
            );
        }

        $this->discountApplicationType = $discountApplicationType;
        $this->amountDiscount = $amountDiscount;
        $this->percentageDiscount = $percentageDiscount;
    }
}
