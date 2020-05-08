<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CartRule\QueryHandler;

use DateTime;
use PrestaShop\Decimal\Number;
use PrestaShop\PrestaShop\Adapter\CartRule\AbstractCartRuleHandler;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Query\GetCartRuleForEditing;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryHandler\GetCartRuleForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult\EditableCartRule;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\ValueObject\CustomerId;
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
     * @throws \PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleException
     * @throws \PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException
     * @throws \PrestaShop\PrestaShop\Core\Domain\Currency\Exception\CurrencyException
     * @throws \PrestaShop\PrestaShop\Core\Domain\Product\Combination\Exception\CombinationConstraintException
     */
    public function handle(GetCartRuleForEditing $query): EditableCartRule
    {
        $cartRuleId = $query->getCartRuleId();
        $cartRule = $this->getCartRule($cartRuleId);

        $dateFrom = $cartRule->date_from;
        $dateTo = $cartRule->date_to;
        $dateAdd = $cartRule->date_add;
        $dateUpd = $cartRule->date_upd;

        $customerId = new CustomerId((int) $cartRule->id_customer);
        $minimumAmountCurrencyId = $cartRule->minimum_amount_currency ? new CurrencyId((int) $cartRule->minimum_amount_currency) : null;
        $reductionCurrencyId = $cartRule->reduction_currency ? new CurrencyId((int) $cartRule->reduction_currency) : null;
        $reductionProductId = $cartRule->reduction_product ? new ProductId((int) $cartRule->reduction_product) : null;
        $giftProductProductId = $cartRule->reduction_product ? new ProductId((int) $cartRule->gift_product) : null;
        $giftProductProductAttributeId = $cartRule->gift_product_attribute ? new CombinationId((int) $cartRule->gift_product_attribute) : null;

        return new EditableCartRule(
            $cartRuleId,
            $cartRule->name,
             $customerId,
            $dateFrom !== DateTimeUtils::NULL_VALUE ? new DateTime($dateFrom) : null,
            $dateTo !== DateTimeUtils::NULL_VALUE ? new DateTime($dateTo) : null,
            $cartRule->description,
            (int) $cartRule->quantity,
            (int) $cartRule->quantity_per_user,
            (int) $cartRule->priority,
            (bool) $cartRule->partial_use,
            $cartRule->code,
            new Number($cartRule->minimum_amount),
            (bool) $cartRule->minimum_amount_tax,
            $minimumAmountCurrencyId,
            (bool) $cartRule->minimum_amount_shipping,
            (bool) $cartRule->country_restriction,
            (bool) $cartRule->carrier_restriction,
            (bool) $cartRule->group_restriction,
            (bool) $cartRule->cart_rule_restriction,
            (bool) $cartRule->product_restriction,
            (bool) $cartRule->shop_restriction,
            (bool) $cartRule->free_shipping,
            new Number($cartRule->reduction_percent),
            new Number($cartRule->reduction_amount),
            (bool) $cartRule->reduction_tax,
            $reductionCurrencyId,
            $reductionProductId,
            (bool) $cartRule->reduction_exclude_special,
            $giftProductProductId,
            $giftProductProductAttributeId,
            (bool) $cartRule->highlight,
            (bool) $cartRule->active,
            $dateAdd !== DateTimeUtils::NULL_VALUE ? new DateTime($dateAdd) : null,
            $dateUpd !== DateTimeUtils::NULL_VALUE ? new DateTime($dateUpd) : null
        );
    }
}
