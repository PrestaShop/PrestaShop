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

namespace PrestaShop\PrestaShop\Adapter\CartRule\CommandHandler;

use CartRule;
use PrestaShop\PrestaShop\Adapter\CartRule\LegacyDiscountApplicationType;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\AddCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CommandHandler\AddCartRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShopException;

/**
 * Handles adding new cart rule using legacy logic.
 */
final class AddCartRuleHandler implements AddCartRuleHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddCartRuleCommand $command): CartRuleId
    {
        try {
            $cartRule = $this->buildCartRuleFromCommandData($command);

            if (false === $cartRule->validateFields(false) || false === $cartRule->validateFieldsLang(false)) {
                throw new CartRuleConstraintException('Cart rule contains invalid field values');
            }
            if (false === $cartRule->add()) {
                throw new CartRuleException('Failed to add new cart rule');
            }
        } catch (PrestaShopException $e) {
            throw new CartRuleException('An error occurred when trying to add new cart rule');
        }

        return new CartRuleId((int) $cartRule->id);
    }

    /**
     * @param AddCartRuleCommand $command
     *
     * @return CartRule
     *
     * @throws PrestaShopException
     */
    private function buildCartRuleFromCommandData(AddCartRuleCommand $command): CartRule
    {
        $cartRule = new CartRule();

        $cartRule->name = $command->getLocalizedNames();
        $cartRule->description = $command->getDescription();
        $cartRule->code = $command->getCode();
        $cartRule->highlight = $command->isHighlightInCart();
        $cartRule->partial_use = $command->isAllowPartialUse();
        $cartRule->priority = $command->getPriority();
        $cartRule->active = $command->isActive();

        $this->fillCartRuleConditionsFromCommandData($cartRule, $command);
        $this->fillCartRuleActionsFromCommandData($cartRule, $command);

        return $cartRule;
    }

    /**
     * Fills cart rule with conditions data from command.
     *
     * @param CartRule $cartRule
     * @param AddCartRuleCommand $command
     */
    private function fillCartRuleConditionsFromCommandData(CartRule $cartRule, AddCartRuleCommand $command): void
    {
        $cartRule->id_customer = null !== $command->getCustomerId() ? $command->getCustomerId()->getValue() : null;

        $cartRule->date_from = $command->getValidFrom()->format('Y-m-d H:i:s');
        $cartRule->date_to = $command->getValidTo()->format('Y-m-d H:i:s');

        $minimumAmount = $command->getMinimumAmountCondition();
        $cartRule->minimum_amount = (string) $minimumAmount->getMoneyAmount()->getAmount();
        $cartRule->minimum_amount_currency = $minimumAmount->getMoneyAmount()->getCurrencyId()->getValue();
        $cartRule->minimum_amount_shipping = !$minimumAmount->isShippingExcluded();
        $cartRule->minimum_amount_tax = !$minimumAmount->isTaxExcluded();

        $cartRule->quantity = $command->getTotalQuantity();
        $cartRule->quantity_per_user = $command->getQuantityPerUser();

        $cartRule->country_restriction = $command->hasCountryRestriction();
        $cartRule->carrier_restriction = $command->hasCarrierRestriction();
        $cartRule->group_restriction = $command->hasGroupRestriction();
        $cartRule->cart_rule_restriction = $command->hasCartRuleRestriction();
        $cartRule->product_restriction = $command->hasProductRestriction();
        $cartRule->shop_restriction = $command->hasShopRestriction();
    }

    /**
     * Fills cart rule with actions data from command.
     *
     * @param CartRule $cartRule
     * @param AddCartRuleCommand $command
     */
    private function fillCartRuleActionsFromCommandData(CartRule $cartRule, AddCartRuleCommand $command): void
    {
        $cartRuleAction = $command->getCartRuleAction();
        $amountDiscount = $cartRuleAction->getAmountDiscount();
        $percentageDiscount = $cartRuleAction->getPercentageDiscount();
        $giftProduct = $cartRuleAction->getGiftProduct();
        $cartRule->free_shipping = $cartRuleAction->isFreeShipping();

        $cartRule->gift_product = null !== $giftProduct ? $giftProduct->getProductId()->getValue() : null;
        $cartRule->gift_product_attribute = null !== $giftProduct ? $giftProduct->getProductAttributeId() : null;
        $cartRule->reduction_amount = null !== $amountDiscount ?
            (string) $amountDiscount->getMoneyAmount()->getAmount() :
            null;
        $cartRule->reduction_currency = null !== $amountDiscount ?
            $amountDiscount->getMoneyAmount()->getCurrencyId()->getValue() :
            null;

        // Legacy reduction_tax property is true when it's tax included, false when tax excluded.
        $cartRule->reduction_tax = null !== $amountDiscount ? !$amountDiscount->isTaxExcluded() : null;

        $cartRule->reduction_percent = null !== $percentageDiscount ? $percentageDiscount->getPercentage() : null;
        $cartRule->reduction_exclude_special = null !== $percentageDiscount ?
            !$percentageDiscount->appliesToDiscountedProducts() :
            null;

        $discountApplicationType = $command->getDiscountApplicationType();

        if (null !== $discountApplicationType) {
            $this->fillDiscountApplicationType(
                $cartRule,
                $command,
                $cartRuleAction,
                $discountApplicationType
            );
        }
    }

    /**
     * @param CartRule $cartRule
     * @param AddCartRuleCommand $command
     * @param CartRuleActionInterface $cartRuleAction
     * @param DiscountApplicationType $discountApplicationType
     *
     * @throws CartRuleConstraintException
     */
    private function fillDiscountApplicationType(
        CartRule $cartRule,
        AddCartRuleCommand $command,
        CartRuleActionInterface $cartRuleAction,
        DiscountApplicationType $discountApplicationType
    ): void {
        $hasAmountDiscount = null !== $cartRuleAction->getAmountDiscount();
        $hasPercentageDiscount = null !== $cartRuleAction->getPercentageDiscount();

        switch ($discountApplicationType->getValue()) {
            case DiscountApplicationType::SELECTED_PRODUCTS:
                if (!$hasPercentageDiscount) {
                    throw new CartRuleConstraintException('Cart rule, which is applied to selected products, must have percent discount type.', CartRuleConstraintException::INCOMPATIBLE_CART_RULE_ACTIONS);
                }

                $cartRule->reduction_product = LegacyDiscountApplicationType::SELECTED_PRODUCTS;

                break;
            case DiscountApplicationType::CHEAPEST_PRODUCT:
                if (!$hasPercentageDiscount) {
                    throw new CartRuleConstraintException('Cart rule, which is applied to cheapest product, must have percent discount type.', CartRuleConstraintException::INCOMPATIBLE_CART_RULE_ACTIONS);
                }

                $cartRule->reduction_product = LegacyDiscountApplicationType::CHEAPEST_PRODUCT;

                break;
            case DiscountApplicationType::SPECIFIC_PRODUCT:
                if (!$hasPercentageDiscount && !$hasAmountDiscount) {
                    throw new CartRuleConstraintException('Cart rule, which is applied to a specific product, ' . 'must have percentage or amount application type.', CartRuleConstraintException::INCOMPATIBLE_CART_RULE_ACTIONS);
                }

                if (null === $command->getDiscountProductId()) {
                    throw new CartRuleConstraintException('Cart rule, which is applied to a specific product, must have a product specified.', CartRuleConstraintException::MISSING_DISCOUNT_APPLICATION_PRODUCT);
                }

                $cartRule->reduction_product = $command->getDiscountProductId()->getValue();

                break;
            case DiscountApplicationType::ORDER_WITHOUT_SHIPPING:
                if (!$hasAmountDiscount && !$hasPercentageDiscount) {
                    throw new CartRuleConstraintException('Cart rule, which is applied to whole order without shipping, ' . 'must have percentage or amount application type.', CartRuleConstraintException::INCOMPATIBLE_CART_RULE_ACTIONS);
                }

                $cartRule->reduction_product = LegacyDiscountApplicationType::ORDER_WITHOUT_SHIPPING;

                break;
        }
    }
}
