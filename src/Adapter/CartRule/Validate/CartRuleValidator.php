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

namespace PrestaShop\PrestaShop\Adapter\CartRule\Validate;

use CartRule;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;

class CartRuleValidator extends AbstractObjectModelValidator
{
    public function validate(CartRule $cartRule): void
    {
        $this->validateCartRuleProperty($cartRule, 'id_customer', CartRuleConstraintException::INVALID_CUSTOMER_ID);
        $this->validateCartRuleProperty($cartRule, 'date_from', CartRuleConstraintException::INVALID_DATE_FROM);
        $this->validateCartRuleProperty($cartRule, 'date_to', CartRuleConstraintException::INVALID_DATE_TO);
        $this->validateCartRuleProperty($cartRule, 'description', CartRuleConstraintException::INVALID_DESCRIPTION);
        $this->validateCartRuleProperty($cartRule, 'quantity', CartRuleConstraintException::INVALID_QUANTITY);
        $this->validateCartRuleProperty($cartRule, 'quantity_per_user', CartRuleConstraintException::INVALID_QUANTITY_PER_USER);
        $this->validateCartRuleProperty($cartRule, 'priority', CartRuleConstraintException::INVALID_PRIORITY);
        $this->validateCartRuleProperty($cartRule, 'partial_use', CartRuleConstraintException::INVALID_PARTIAL_USE);
        $this->validateCartRuleProperty($cartRule, 'code', CartRuleConstraintException::INVALID_CODE);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount', CartRuleConstraintException::INVALID_MINIMUM_AMOUNT);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_tax', CartRuleConstraintException::INVALID_MINIMUM_AMOUNT_TAX);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_currency', CartRuleConstraintException::INVALID_MINIMUM_AMOUNT_CURRENCY);
        $this->validateCartRuleProperty($cartRule, 'minimum_amount_shipping', CartRuleConstraintException::INVALID_MINIMUM_AMOUNT_SHIPPING);
        $this->validateCartRuleProperty($cartRule, 'country_restriction', CartRuleConstraintException::INVALID_COUNTRY_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'carrier_restriction', CartRuleConstraintException::INVALID_CARRIER_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'group_restriction', CartRuleConstraintException::INVALID_GROUP_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'cart_rule_restriction', CartRuleConstraintException::INVALID_CART_RULE_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'product_restriction', CartRuleConstraintException::INVALID_PRODUCT_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'shop_restriction', CartRuleConstraintException::INVALID_SHOP_RESTRICTION);
        $this->validateCartRuleProperty($cartRule, 'free_shipping', CartRuleConstraintException::INVALID_FREE_SHIPPING);
        $this->validateCartRuleProperty($cartRule, 'reduction_percent', CartRuleConstraintException::INVALID_REDUCTION_PERCENT);
        $this->validateCartRuleProperty($cartRule, 'reduction_amount', CartRuleConstraintException::INVALID_REDUCTION_AMOUNT);
        $this->validateCartRuleProperty($cartRule, 'reduction_tax', CartRuleConstraintException::INVALID_REDUCTION_TAX);
        $this->validateCartRuleProperty($cartRule, 'reduction_currency', CartRuleConstraintException::INVALID_REDUCTION_CURRENCY);
        $this->validateCartRuleProperty($cartRule, 'reduction_product', CartRuleConstraintException::INVALID_REDUCTION_PRODUCT);
        $this->validateCartRuleProperty($cartRule, 'reduction_exclude_special', CartRuleConstraintException::INVALID_REDUCTION_EXCLUDE_SPECIAL);
        $this->validateCartRuleProperty($cartRule, 'gift_product', CartRuleConstraintException::INVALID_GIFT_PRODUCT);
        $this->validateCartRuleProperty($cartRule, 'gift_product_attribute', CartRuleConstraintException::INVALID_GIFT_PRODUCT_ATTRIBUTE);
        $this->validateCartRuleProperty($cartRule, 'highlight', CartRuleConstraintException::INVALID_HIGHLIGHT);
        $this->validateCartRuleProperty($cartRule, 'active', CartRuleConstraintException::INVALID_ACTIVE);

        $this->validateObjectModelLocalizedProperty(
            $cartRule,
            'name',
            CartRuleConstraintException::class,
            CartRuleConstraintException::INVALID_NAME
        );

        $this->assertCodeIsUnique($cartRule);
    }

    private function validateCartRuleProperty(CartRule $cartRule, string $propertyName, int $code): void
    {
        $this->validateObjectModelProperty(
            $cartRule,
            $propertyName,
            CartRuleConstraintException::class,
            $code
        );
    }

    private function assertCodeIsUnique(CartRule $cartRule): void
    {
        $code = $cartRule->code;

        if (empty($code)) {
            return;
        }

        try {
            $duplicateCodeCartRuleId = (int) CartRule::getIdByCode($code);
        } catch (PrestaShopException $e) {
            throw new CoreException('Error occurred when trying to check if cart rule code is unique', 0, $e);
        }

        if ($duplicateCodeCartRuleId && $duplicateCodeCartRuleId !== (int) $cartRule->id) {
            throw new CartRuleConstraintException(
                sprintf('Cart rule with code "%s" already exists', $code),
                CartRuleConstraintException::NON_UNIQUE_CODE
            );
        }
    }
}
