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
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\PercentageDiscount;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Builds cart rule actions.
 */
class CartRuleActionBuilder implements CartRuleActionBuilderInterface
{
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
    public function setAmountDiscount(Money $amount): CartRuleActionBuilderInterface
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
    public function build(
        bool $freeShipping,
        ?string $reductionType = null,
        ?string $reductionValue = null,
        ?int $currencyId = null,
        ?bool $taxIncluded = null,
        ?int $giftProductId = null,
        ?int $giftCombinationId = null,
        ?bool $appliesToDiscountedProducts = null
    ): CartRuleActionInterface {
        $this->assertCartRuleActionsAreValid(
            $freeShipping,
            $reductionType,
            $reductionValue,
            $giftProductId,
            $currencyId,
            $taxIncluded
        );

        $giftProduct = null;
        if (null !== $giftProductId) {
            $giftProduct = new GiftProduct(
                $giftProductId,
                $giftCombinationId
            );
        }

        if (empty($reductionType) && empty($reductionValue) && $freeShipping) {
            $action = new FreeShippingAction($giftProduct);
        } elseif (Reduction::TYPE_AMOUNT === $reductionType) {
            $action = new AmountDiscountAction(new Money($reductionValue, $currencyId, $taxIncluded), $freeShipping, $giftProduct);
        } elseif (Reduction::TYPE_PERCENTAGE === $reductionType) {
            if (null === $appliesToDiscountedProducts) {
                throw new CartRuleConstraintException(
                    '$appliesToDiscountedProducts value is required when reduction is percentage type',
                    CartRuleConstraintException::INVALID_REDUCTION_EXCLUDE_SPECIAL
                );
            }
            $action = new PercentageDiscountAction(
                new PercentageDiscount($reductionValue, $appliesToDiscountedProducts),
                $freeShipping,
                $giftProduct
            );
        } else {
            if (null === $giftProduct) {
                throw new CartRuleConstraintException(
                    'Gift product is required for gift product action',
                    CartRuleConstraintException::INVALID_GIFT_PRODUCT
                );
            }
            $action = new GiftProductAction($giftProduct);
        }

        return $action;
    }

    private function assertCartRuleActionsAreValid(
        bool $freeShipping,
        ?string $reductionType,
        ?string $reductionValue,
        ?int $giftProductId,
        ?int $currencyId,
        ?bool $taxIncluded
    ) {
        $reduction = null;
        if (null !== $reductionType && null !== $reductionValue) {
            // this Reduction class is only initiated for validation
            $reduction = new Reduction($reductionType, $reductionValue);
        }

        if (!$reduction && null === $giftProductId && !$freeShipping) {
            throw new CartRuleConstraintException('Cart rule must have at least one action', CartRuleConstraintException::MISSING_ACTION);
        }

        if (null !== $reductionType) {
            if (null === $reductionValue) {
                throw new DomainConstraintException(
                    'Reduction value is required',
                    $reductionType === Reduction::TYPE_AMOUNT ? DomainConstraintException::INVALID_REDUCTION_AMOUNT : DomainConstraintException::INVALID_REDUCTION_PERCENTAGE
                );
            }

            if (Reduction::TYPE_PERCENTAGE === $reduction->getType()) {
                return;
            }

            // when it is amount type, we require currency and tax inclusion
            if (!$currencyId) {
                throw new CartRuleConstraintException('Amount discount requires currency', CartRuleConstraintException::INVALID_REDUCTION_CURRENCY);
            }

            if (null === $taxIncluded) {
                throw new CartRuleConstraintException('Amount discount requires defining tax inclusion', CartRuleConstraintException::INVALID_REDUCTION_TAX);
            }
        } elseif (!empty($reductionValue)) {
            throw new DomainConstraintException('Reduction type is required', DomainConstraintException::INVALID_REDUCTION_TYPE);
        }
    }
}
