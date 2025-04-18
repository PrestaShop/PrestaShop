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

namespace PrestaShop\PrestaShop\Adapter\CartRule;

use CartRule;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddCartLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddFreeGiftDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddFreeShippingDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddOrderLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddProductLevelDiscountCommand;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class CartRuleBuilder
{
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
        $cartRule->type = $command->getDiscountType()->getValue();
        $cartRule->free_shipping = $command instanceof AddFreeShippingDiscountCommand;

        if ($command instanceof AddCartLevelDiscountCommand || $command instanceof AddOrderLevelDiscountCommand) {
            if ($command->getPercentDiscount()) {
                $cartRule->reduction_percent = (float) (string) $command->getPercentDiscount();
                $cartRule->reduction_amount = 0;
                $cartRule->reduction_currency = 0;
                $cartRule->reduction_tax = false;
            } elseif ($command->getAmountDiscount()) {
                $cartRule->reduction_percent = 0;
                $cartRule->reduction_amount = (float) (string) $command->getAmountDiscount()->getAmount();
                $cartRule->reduction_currency = $command->getAmountDiscount()->getCurrencyId()->getValue();
                $cartRule->reduction_tax = $command->getAmountDiscount()->isTaxIncluded();
            }
        } elseif ($command instanceof AddFreeGiftDiscountCommand) {
            $cartRule->gift_product = $command->getProductId()?->getValue() ?? 0;
            $cartRule->gift_product_attribute = $command->getCombinationId()?->getValue() ?? 0;
        }

        if ($command instanceof AddProductLevelDiscountCommand) {
            if ($command->getPercentDiscount()) {
                $cartRule->reduction_percent = (float) (string) $command->getPercentDiscount();
                $cartRule->reduction_amount = 0;
                $cartRule->reduction_currency = 0;
                $cartRule->reduction_tax = false;
            }
            $cartRule->reduction_product = $command->getReductionProduct();
        }

        return $cartRule;
    }
}
