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

namespace PrestaShop\PrestaShop\Adapter\Discount\Update;

use CartRule;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class DiscountBuilder
{
    public function __construct(
        private readonly DiscountTypeRepository $discountTypeRepository
    ) {
    }

    public function build(AddDiscountCommand $command): CartRule
    {
        $cartRule = new CartRule();
        $validFrom = $command->getValidFrom() ?: new DateTimeImmutable();
        $validTo = $command->getValidTo() ?: $validFrom->modify('+1 month');

        $cartRule->name = $command->getLocalizedNames();
        $cartRule->description = $command->getDescription();
        $cartRule->code = $command->getCode();
        $cartRule->highlight = $command->isHighlightInCart();
        $cartRule->partial_use = $command->allowPartialUse();
        $cartRule->priority = $command->getPriority();
        $cartRule->active = $command->isActive();
        $cartRule->id_customer = $command->getCustomerId()?->getValue();
        $cartRule->date_from = $validFrom->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
        $cartRule->date_to = $validTo->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
        $cartRule->quantity = $command->getTotalQuantity();
        $cartRule->quantity_per_user = $command->getQuantityPerUser();
        $cartRule->reduction_amount = 0;
        $cartRule->reduction_currency = 0;
        $cartRule->reduction_tax = false;
        $cartRule->reduction_percent = 0;

        $discountType = $command->getDiscountType()->getValue();
        $cartRule->id_cart_rule_type = $this->discountTypeRepository->getTypeIdByString($discountType);
        $cartRule->free_shipping = $discountType === DiscountType::FREE_SHIPPING;

        if (in_array($command->getDiscountType()->getValue(), [
            DiscountType::CART_LEVEL,
            DiscountType::ORDER_LEVEL,
            DiscountType::PRODUCT_LEVEL]
        )) {
            if ($command->getReductionPercent()) {
                $cartRule->reduction_percent = (float) (string) $command->getReductionPercent();
            }
            if ($command->getReductionAmount()) {
                $cartRule->reduction_amount = (float) (string) $command->getReductionAmount()->getAmount();
                $cartRule->reduction_currency = $command->getReductionAmount()->getCurrencyId()->getValue();
                $cartRule->reduction_tax = $command->getReductionAmount()->isTaxIncluded();
            }
        } elseif ($command->getDiscountType()->getValue() === DiscountType::FREE_GIFT) {
            $cartRule->gift_product = $command->getGiftProductId()?->getValue() ?? 0;
            $cartRule->gift_product_attribute = $command->getGiftCombinationId()?->getValue() ?? 0;
        }

        if ($command->getDiscountType()->getValue() === DiscountType::PRODUCT_LEVEL) {
            if ($command->getCheapestProduct()) {
                // If cheapest product is enabled we set the specific value
                $cartRule->reduction_product = DiscountSettings::CHEAPEST_PRODUCT;
            } elseif (null !== $command->getReductionProductId()) {
                $cartRule->reduction_product = $command->getReductionProductId()->getValue();
            }
        }

        if (null !== $command->getMinimumProductQuantity()) {
            $cartRule->minimum_product_quantity = $command->getMinimumProductQuantity();
        }

        if (null !== $command->getMinimumAmount()) {
            $cartRule->minimum_amount = (float) (string) $command->getMinimumAmount()->getAmount();
            $cartRule->minimum_amount_currency = $command->getMinimumAmount()->getCurrencyId()->getValue();
            $cartRule->minimum_amount_tax = $command->getMinimumAmount()->isTaxIncluded();
            $cartRule->minimum_amount_shipping = $command->getMinimumAmount()->isShippingIncluded();
        } else {
            $cartRule->minimum_amount = 0;
            $cartRule->minimum_amount_currency = 0;
            $cartRule->minimum_amount_tax = false;
            $cartRule->minimum_amount_shipping = false;
        }

        return $cartRule;
    }
}
