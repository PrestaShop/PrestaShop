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
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Money;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;

/**
 * Builds cart rule actions.
 */
class CartRuleActionBuilder implements CartRuleActionBuilderInterface
{
    /**
     * @var bool|null
     */
    private $freeShipping;

    /**
     * @var Reduction|null
     */
    private $reduction;

    /**
     * @var CurrencyId|null
     */
    private $currencyId;

    /**
     * @var bool|null
     */
    private $taxIncluded;

    /**
     * @var bool|null
     */
    private $excludeDiscountedProducts;

    /**
     * @var GiftProduct|null
     */
    private $giftProduct;

    /**
     * {@inheritdoc}
     */
    public function setFreeShipping(bool $freeShipping): CartRuleActionBuilderInterface
    {
        $this->freeShipping = $freeShipping;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPercentageDiscount(string $reductionValue, bool $excludeDiscountedProducts): CartRuleActionBuilderInterface
    {
        if ($this->reduction) {
            throw new CartRuleConstraintException('Cart rule cannot have both percentage and amount discount actions.', CartRuleConstraintException::INCOMPATIBLE_CART_RULE_ACTIONS);
        }

        $this->reduction = new Reduction(Reduction::TYPE_PERCENTAGE, $reductionValue);
        $this->excludeDiscountedProducts = $excludeDiscountedProducts;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAmountDiscount(
        string $reductionValue,
        int $currencyId,
        bool $taxIncluded
    ): CartRuleActionBuilderInterface {
        if ($this->reduction) {
            throw new CartRuleConstraintException('Cart rule cannot have both percentage and amount discount actions.', CartRuleConstraintException::INCOMPATIBLE_CART_RULE_ACTIONS);
        }

        $this->reduction = new Reduction(Reduction::TYPE_AMOUNT, $reductionValue);
        $this->currencyId = new CurrencyId($currencyId);
        $this->taxIncluded = $taxIncluded;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftProduct(int $productId, ?int $combinationId = null): CartRuleActionBuilderInterface
    {
        $this->giftProduct = new GiftProduct($productId, $combinationId);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function build(): CartRuleActionInterface
    {
        $this->assertCartRuleActionsAreValid();

        if (null === $this->reduction) {
            return $this->freeShipping ? new FreeShippingAction($this->giftProduct) : new GiftProductAction($this->giftProduct);
        }

        if ($this->reduction->getType() === Reduction::TYPE_AMOUNT) {
            return new AmountDiscountAction(
                new Money($this->reduction->getValue(), $this->currencyId, $this->taxIncluded),
                $this->freeShipping,
                $this->giftProduct
            );
        }

        return new PercentageDiscountAction(
            $this->reduction->getValue(),
            $this->excludeDiscountedProducts,
            $this->freeShipping,
            $this->giftProduct
        );
    }

    /**
     * @throws CartRuleConstraintException
     */
    private function assertCartRuleActionsAreValid(): void
    {
        if (null === $this->reduction && null === $this->giftProduct && !$this->freeShipping) {
            throw new CartRuleConstraintException('Cart rule must have at least one action', CartRuleConstraintException::MISSING_ACTION);
        }
    }
}
