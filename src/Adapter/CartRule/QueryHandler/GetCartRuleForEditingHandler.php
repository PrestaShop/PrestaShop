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

namespace PrestaShop\PrestaShop\Adapter\CartRule\QueryHandler;

use CartRule;
use DateTime;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\CartRule\AbstractCartRuleHandler;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryHandler\GetCartRuleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRule;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRuleActions;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRuleConditions;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRuleInformation;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRuleMinimum;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRuleReduction;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRuleRestrictions;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\NoCustomerId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtils;

/**
 * Handles command which gets catalog price rule for editing using legacy object model
 */
final class GetCartRuleForEditingHandler extends AbstractCartRuleHandler implements GetCartRuleForEditingHandlerInterface
{
    /**
     * @param GetCartRuleForEditing $query
     *
     * @return EditableCartRule
     *
     * @throws CartRuleException
     * @throws CartRuleNotFoundException
     */
    public function handle(GetCartRuleForEditing $query): EditableCartRule
    {
        $cartRuleId = $query->getCartRuleId();
        $cartRule = $this->getCartRule($cartRuleId);

        $cartRuleInformation = $this->getCartRuleInformation($cartRule);
        $cartRuleConditions = $this->getCartRuleConditions($cartRule);
        $cartRuleActions = $this->getCartRuleActions($cartRule);
        $dateAdd = $cartRule->date_add;
        $dateUpd = $cartRule->date_upd;

        return new EditableCartRule(
            $cartRuleId,
            $cartRuleInformation,
            $cartRuleConditions,
            $cartRuleActions,
            !DateTimeUtils::isNull($dateAdd) ? new DateTime($dateAdd) : null,
            !DateTimeUtils::isNull($dateUpd) ? new DateTime($dateUpd) : null
        );
    }

    private function getCartRuleInformation(CartRule $cartRule): EditableCartRuleInformation
    {
        return new EditableCartRuleInformation(
            $cartRule->name,
            $cartRule->description,
            $cartRule->code,
            (bool) $cartRule->highlight,
            (bool) $cartRule->partial_use,
            (int) $cartRule->priority,
            (bool) $cartRule->active
        );
    }

    private function getCartRuleConditions(CartRule $cartRule): EditableCartRuleConditions
    {
        $customerId = (int) $cartRule->id_customer !== NoCustomerId::NO_CUSTOMER_ID_VALUE ? new CustomerId((int) $cartRule->id_customer) : new NoCustomerId();
        $dateFrom = $cartRule->date_from;
        $dateTo = $cartRule->date_to;
        $minimumAmountCurrencyId = $cartRule->minimum_amount_currency ? new CurrencyId((int) $cartRule->minimum_amount_currency) : null;

        $cartRuleMinimum = new EditableCartRuleMinimum(
            new Number($cartRule->minimum_amount),
            (bool) $cartRule->minimum_amount_tax,
            $minimumAmountCurrencyId,
            (bool) $cartRule->minimum_amount_shipping
        );

        $cartRuleRestrictions = new EditableCartRuleRestrictions(
            (bool) $cartRule->country_restriction,
            (bool) $cartRule->carrier_restriction,
            (bool) $cartRule->group_restriction,
            (bool) $cartRule->cart_rule_restriction,
            (bool) $cartRule->product_restriction,
            (bool) $cartRule->shop_restriction
        );

        return new EditableCartRuleConditions(
            $customerId,
            !DateTimeUtils::isNull($dateFrom) ? new DateTime($dateFrom) : null,
            !DateTimeUtils::isNull($dateTo) ? new DateTime($dateTo) : null,
            (int) $cartRule->quantity,
            (int) $cartRule->quantity_per_user,
            $cartRuleMinimum,
            $cartRuleRestrictions
        );
    }

    private function getCartRuleActions(CartRule $cartRule): EditableCartRuleActions
    {
        $reductionCurrencyId = $cartRule->reduction_currency ? new CurrencyId((int) $cartRule->reduction_currency) : null;
        $reductionProductId = $cartRule->reduction_product ? new ProductId((int) $cartRule->reduction_product) : null;
        $giftProductProductId = $cartRule->reduction_product ? new ProductId((int) $cartRule->gift_product) : null;
        $giftProductProductAttributeId = $cartRule->gift_product_attribute ? new CombinationId((int) $cartRule->gift_product_attribute) : null;

        $reduction = new EditableCartRuleReduction(
            new Number($cartRule->reduction_percent),
            new Number($cartRule->reduction_amount),
            (bool) $cartRule->reduction_tax,
            $reductionCurrencyId,
            $reductionProductId,
            (bool) $cartRule->reduction_exclude_special
        );

        return new EditableCartRuleActions(
            (bool) $cartRule->free_shipping,
            $reduction,
            $giftProductProductId,
            $giftProductProductAttributeId
        );
    }
}
