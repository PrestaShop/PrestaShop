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

namespace PrestaShop\PrestaShop\Adapter\CartRule\CommandHandler;

use CartRule;
use PrestaShop\PrestaShop\Adapter\CartRule\LegacyDiscountApplicationType;
use PrestaShop\PrestaShop\Adapter\CartRule\Repository\CartRuleRepository;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Command\EditCartRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\CartRule\CommandHandler\EditCartRuleHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleAction\CartRuleActionInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\DiscountApplicationType;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class EditCartRuleHandler implements EditCartRuleHandlerInterface
{
    /**
     * @var CartRuleRepository
     */
    private $cartRuleRepository;

    public function __construct(
        CartRuleRepository $cartRuleRepository
    ) {
        $this->cartRuleRepository = $cartRuleRepository;
    }

    public function handle(EditCartRuleCommand $command): void
    {
        $cartRule = $this->cartRuleRepository->get($command->getCartRuleId());
        $updatableProperties = $this->fillUpdatableProperties($cartRule, $command);

        if (empty($updatableProperties)) {
            return;
        }

        $this->cartRuleRepository->partialUpdate($cartRule, $updatableProperties);
    }

    /**
     * @param CartRule $cartRule
     * @param EditCartRuleCommand $command
     *
     * @return array<int|string, string|int[]> updatable properties
     */
    private function fillUpdatableProperties(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $propertiesToUpdate = [];
        if (null !== $command->getLocalizedNames()) {
            $cartRule->name = $command->getLocalizedNames();
            $propertiesToUpdate['name'] = array_keys($command->getLocalizedNames());
        }
        if (null !== $command->getDescription()) {
            $cartRule->description = $command->getDescription();
            $propertiesToUpdate[] = 'description';
        }
        if (null !== $command->getCode()) {
            $cartRule->code = $command->getCode();
            $propertiesToUpdate[] = 'code';
        }
        if (null !== $command->highlightInCart()) {
            $cartRule->highlight = $command->highlightInCart();
            $propertiesToUpdate[] = 'highlight';
        }
        if (null !== $command->allowPartialUse()) {
            $cartRule->partial_use = $command->allowPartialUse();
            $propertiesToUpdate[] = 'partial_use';
        }
        if (null !== $command->getPriority()) {
            $cartRule->priority = $command->getPriority();
            $propertiesToUpdate[] = 'priority';
        }
        if (null !== $command->isActive()) {
            $cartRule->active = $command->isActive();
            $propertiesToUpdate[] = 'active';
        }

        $conditionsToUpdate = $this->fillConditions($cartRule, $command);
        $actionsToUpdate = $this->fillActions($cartRule, $command);

        return array_merge($propertiesToUpdate, $conditionsToUpdate, $actionsToUpdate);
    }

    /**
     * Fills cart rule with conditions data from command.
     *
     * @param CartRule $cartRule
     * @param EditCartRuleCommand $command
     *
     * @return array<int|string, string|int[]> updatable properties
     */
    private function fillConditions(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $updatableProperties = [];

        if (null !== $command->getCustomerId()) {
            $cartRule->id_customer = $command->getCustomerId()->getValue();
            $updatableProperties[] = 'id_customer';
        }
        if (null !== $command->getValidFrom()) {
            $cartRule->date_from = $command->getValidFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_from';
        }
        if (null !== $command->getValidTo()) {
            $cartRule->date_to = $command->getValidTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_to';
        }
        if (null !== $command->getMinimumAmount()) {
            $minimumAmount = $command->getMinimumAmount();
            $cartRule->minimum_amount = (float) (string) $minimumAmount->getAmount();
            $cartRule->minimum_amount_currency = $minimumAmount->getCurrencyId()->getValue();
            $cartRule->minimum_amount_tax = $minimumAmount->isTaxIncluded();
            $cartRule->minimum_amount_shipping = $command->isMinimumAmountShippingIncluded();
            $updatableProperties = array_merge($updatableProperties, [
                'minimum_amount',
                'minimum_amount_currency',
                'minimum_amount_tax',
                'minimum_amount_shipping',
            ]);
        }

        if (null !== $command->getTotalQuantity()) {
            $cartRule->quantity = $command->getTotalQuantity();
            $updatableProperties[] = 'quantity';
        }
        if (null !== $command->getQuantityPerUser()) {
            $cartRule->quantity_per_user = $command->getQuantityPerUser();
            $updatableProperties[] = 'quantity_per_user';
        }

        return $updatableProperties;
    }

    /**
     * Fills cart rule with actions data from command.
     *
     * @param CartRule $cartRule
     * @param EditCartRuleCommand $command
     *
     * @return array<int|string, string|int[]> updatable properties
     */
    private function fillActions(CartRule $cartRule, EditCartRuleCommand $command): array
    {
        $cartRuleAction = $command->getCartRuleAction();

        if (null === $cartRuleAction) {
            return [];
        }

        $updatableProperties = [];
        $amountDiscount = $cartRuleAction->getAmountDiscount();
        if (null !== $amountDiscount) {
            $cartRule->reduction_amount = (float) (string) $amountDiscount->getAmount();
            $cartRule->reduction_currency = $amountDiscount->getCurrencyId()->getValue();
            $cartRule->reduction_tax = $amountDiscount->isTaxIncluded();
            $cartRule->reduction_percent = 0;
            $cartRule->reduction_exclude_special = false;
            $cartRule->reduction_product = DiscountApplicationType::ORDER_WITHOUT_SHIPPING;
            $updatableProperties[] = 'reduction_amount';
            $updatableProperties[] = 'reduction_currency';
            $updatableProperties[] = 'reduction_percent';
            $updatableProperties[] = 'reduction_exclude_special';
            $updatableProperties[] = 'reduction_product';
        }

        $percentageDiscount = $cartRuleAction->getPercentageDiscount();
        if (null !== $percentageDiscount) {
            $cartRule->reduction_percent = (string) $percentageDiscount->getPercentage();
            $cartRule->reduction_exclude_special = !$percentageDiscount->applyToDiscountedProducts();
            $cartRule->reduction_amount = 0;
            $cartRule->reduction_currency = 0;
            $cartRule->reduction_tax = false;
            $updatableProperties[] = 'reduction_amount';
            $updatableProperties[] = 'reduction_currency';
            $updatableProperties[] = 'reduction_tax';
            $updatableProperties[] = 'reduction_percent';
            $updatableProperties[] = 'reduction_exclude_special';
        }

        $giftProduct = $cartRuleAction->getGiftProduct();
        if (null !== $giftProduct) {
            $cartRule->gift_product = $giftProduct->getProductId()->getValue();
            $cartRule->gift_product_attribute = $giftProduct->getCombinationId() ? $giftProduct->getCombinationId()->getValue() : null;
            $updatableProperties[] = 'gift_product';
            $updatableProperties[] = 'gift_product_attribute';
        }

        $cartRule->free_shipping = $cartRuleAction->isFreeShipping();
        $updatableProperties[] = 'free_shipping';

        $discountApplicationType = $command->getDiscountApplicationType();
        if (null !== $discountApplicationType) {
            $this->fillDiscountApplicationType(
                $cartRule,
                $command,
                $cartRuleAction,
                $discountApplicationType
            );
            $updatableProperties[] = 'reduction_product';
        }

        return $updatableProperties;
    }

    /**
     * @param CartRule $cartRule
     * @param EditCartRuleCommand $command
     * @param CartRuleActionInterface $cartRuleAction
     * @param DiscountApplicationType $discountApplicationType
     */
    private function fillDiscountApplicationType(
        CartRule $cartRule,
        EditCartRuleCommand $command,
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
