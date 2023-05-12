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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Exception;

/**
 * Thrown when validating cart rule's data
 */
class CartRuleConstraintException extends CartRuleException
{
    /**
     * Used when discount is applied to specific product, but that product is not set.
     */
    public const MISSING_DISCOUNT_APPLICATION_PRODUCT = 1;
    public const INVALID_DISCOUNT_APPLICATION_TYPE = 2;
    public const INVALID_GIFT_PRODUCT_ATTRIBUTE = 3;
    public const INVALID_PRIORITY = 4;
    public const DATE_FROM_GREATER_THAN_DATE_TO = 5;
    public const INVALID_QUANTITY = 6;
    public const INVALID_QUANTITY_PER_USER = 7;
    public const INVALID_GIFT_PRODUCT = 8;
    public const MISSING_ACTION = 9;
    public const INVALID_ID = 10;
    public const INVALID_NAME = 11;
    public const INVALID_STATUS = 12;
    public const INVALID_CUSTOMER_ID = 13;
    public const INVALID_DATE_FROM = 14;
    public const INVALID_DATE_TO = 15;
    public const INVALID_DESCRIPTION = 16;
    public const INVALID_PARTIAL_USE = 17;
    public const INVALID_CODE = 18;
    public const INVALID_MINIMUM_AMOUNT = 19;
    public const INVALID_MINIMUM_AMOUNT_TAX = 20;
    public const INVALID_MINIMUM_AMOUNT_CURRENCY = 21;
    public const INVALID_MINIMUM_AMOUNT_SHIPPING = 22;
    public const INVALID_COUNTRY_RESTRICTION = 23;
    public const INVALID_CARRIER_RESTRICTION = 24;
    public const INVALID_GROUP_RESTRICTION = 25;
    public const INVALID_CART_RULE_RESTRICTION = 26;
    public const INVALID_PRODUCT_RESTRICTION = 27;
    public const INVALID_SHOP_RESTRICTION = 28;
    public const INVALID_FREE_SHIPPING = 29;
    public const INVALID_REDUCTION_PERCENT = 30;
    public const INVALID_REDUCTION_AMOUNT = 31;
    public const INVALID_REDUCTION_TAX = 32;
    public const INVALID_REDUCTION_CURRENCY = 33;
    public const INVALID_REDUCTION_PRODUCT = 34;
    public const INVALID_REDUCTION_EXCLUDE_SPECIAL = 35;
    public const INVALID_HIGHLIGHT = 36;
    public const INVALID_ACTIVE = 37;
    public const NON_UNIQUE_CODE = 38;
    public const INVALID_PRICE_DISCOUNT = 39;
    public const INVALID_RESTRICTION_RULE = 40;
}
