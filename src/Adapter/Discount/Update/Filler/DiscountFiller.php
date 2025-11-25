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

namespace PrestaShop\PrestaShop\Adapter\Discount\Update\Filler;

use CartRule;
use PrestaShop\PrestaShop\Adapter\Domain\LocalizedObjectModelTrait;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\UpdateDiscountCommand;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;

class DiscountFiller
{
    use LocalizedObjectModelTrait;

    public function fillUpdatableProperties(CartRule $cartRule, UpdateDiscountCommand $command): array
    {
        $updatableProperties = [];
        if ($command->isDirty('validFrom')) {
            $cartRule->date_from = $command->getValidFrom()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_from';
        }
        if ($command->isDirty('validTo')) {
            $cartRule->date_to = $command->getValidTo()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT);
            $updatableProperties[] = 'date_to';
        }
        if ($command->isDirty('localizedNames')) {
            $cartRule->name = $command->getLocalizedNames();
            $this->fillLocalizedValues($cartRule, 'name', $command->getLocalizedNames(), $updatableProperties);
        }
        if ($command->isDirty('description')) {
            $cartRule->description = $command->getDescription();
            $updatableProperties[] = 'description';
        }
        if ($command->isDirty('code')) {
            $cartRule->code = $command->getCode();
            $updatableProperties[] = 'code';
        }
        if ($command->isDirty('highlightInCart')) {
            $cartRule->highlight = $command->isHighlightInCart();
            $updatableProperties[] = 'highlight';
        }
        if ($command->isDirty('allowPartialUse')) {
            $cartRule->partial_use = $command->allowPartialUse();
            $updatableProperties[] = 'partial_use';
        }
        if ($command->isDirty('active')) {
            $cartRule->active = $command->isActive();
            $updatableProperties[] = 'active';
        }
        if ($command->isDirty('customerId')) {
            $cartRule->id_customer = $command->getCustomerId()->getValue();
            $updatableProperties[] = 'id_customer';
        }
        if ($command->isDirty('totalQuantity')) {
            $cartRule->quantity = $command->getTotalQuantity();
            $updatableProperties[] = 'quantity';
        }
        if ($command->isDirty('quantityPerUser')) {
            $cartRule->quantity_per_user = $command->getQuantityPerUser();
            $updatableProperties[] = 'quantity_per_user';
        }
        if ($command->isDirty('priority')) {
            $cartRule->priority = $command->getPriority();
            $updatableProperties[] = 'priority';
        }
        if ($command->isDirty('reductionProduct')) {
            $cartRule->reduction_product = $command->getReductionProduct();
            $updatableProperties[] = 'reduction_product';
        }
        if ($command->isDirty('amountDiscount')) {
            $cartRule->reduction_amount = (float) (string) $command->getAmountDiscount()->getAmount();
            $cartRule->reduction_currency = $command->getAmountDiscount()->getCurrencyId()->getValue();
            $cartRule->reduction_tax = $command->getAmountDiscount()->isTaxIncluded();
            $cartRule->reduction_percent = null;
            $updatableProperties[] = 'reduction_amount';
            $updatableProperties[] = 'reduction_currency';
            $updatableProperties[] = 'reduction_tax';
            $updatableProperties[] = 'reduction_percent';
        }
        if ($command->isDirty('percentDiscount')) {
            $cartRule->reduction_percent = (float) (string) $command->getPercentDiscount();
            $cartRule->reduction_amount = null;
            $cartRule->reduction_currency = 0;
            $cartRule->reduction_tax = false;
            $updatableProperties[] = 'reduction_percent';
            $updatableProperties[] = 'reduction_amount';
            $updatableProperties[] = 'reduction_currency';
            $updatableProperties[] = 'reduction_tax';
        }
        if ($command->isDirty('productId')) {
            $cartRule->gift_product = $command->getProductId()->getValue();
            $updatableProperties[] = 'gift_product';
        }
        if ($command->isDirty('combinationId')) {
            $cartRule->gift_product_attribute = $command->getCombinationId()->getValue();
            $updatableProperties[] = 'gift_product_attribute';
        }

        return $updatableProperties;
    }
}
