<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Exception;

/**
 * Thrown when validating cart rule's data
 */
class CartRuleConstraintException extends CartRuleException
{
    /**
     * Used when cart rule is using an invalid discount application type
     */
    const INVALID_DISCOUNT_APPLICATION_TYPE = 1;

    /**
     * Used when cart rule name is empty
     */
    const EMPTY_NAME = 2;

    /**
     * Used when cart rule's priority is invalid
     */
    const INVALID_PRIORITY = 3;

    /**
     * Used when cart rule's date from is greater than date to
     */
    const DATE_FROM_GREATER_THAN_DATE_TO = 4;

    /**
     * Used when cart rule's quantity is invalid
     */
    const INVALID_QUANTITY = 5;

    /**
     * Used when quantity per user is invalid
     */
    const INVALID_QUANTITY_PER_USER = 6;

    /**
     * Used when percentage discount is invalid
     */
    const INVALID_PERCENTAGE = 7;

    /**
     * Used when cart rule has invalid gift product assigned
     */
    const INVALID_GIFT_PRODUCT = 8;

    /**
     * Used when cart rule has invalid gift product attribute
     */
    const INVALID_GIFT_PRODUCT_ATTRIBUTE = 9;

    /**
     * Used when cart rule has incompatible actions,
     * e.g. both amount and percentage discounts at the same time.
     */
    const INCOMPATIBLE_CART_RULE_ACTIONS = 10;

    /**
     * Used when cart rule is missing an action.
     */
    const MISSING_ACTION = 11;

    /**
     * Used when discount is applied to specific product, but that product is not set.
     */
    const MISSING_DISCOUNT_APPLICATION_PRODUCT = 12;

    /**
     * Used when cart rule id constraints are violated
     */
    const INVALID_ID = 13;

    /**
     * When cart rule cannot be used, because its disabled
     */
    const DISABLED = 14;

    /**
     * When cart rule cannot be used, because it has no quantity left
     */
    const NO_QUANTITY = 15;

    /**
     * When cart rule cannot be used, because its not yet valid (cart rule takes effect depending on date_from value)
     */
    const NOT_VALID_YET = 16;

    /**
     * When cart rule cannot be used, because it is already expired (cart rule expires depending on date_to value)
     */
    const EXPIRED = 17;

    /**
     * When cart rule cannot be used, because it already reached its usage limit
     */
    const USAGE_LIMIT_REACHED = 18;

    /**
     * When cart rule cannot be used, because it is not allowed for certain user/user-group
     */
    const NOT_ALLOWED = 19;

    /**
     * When cart rule cannot be used, because it is not available for cart delivery address
     */
    const UNAVAILABLE_FOR_DELIVERY_ADDRESS = 20;

    /**
     * When cart rule cannot be used, because it is not available for cart country of delivery
     */
    const UNAVAILABLE_FOR_COUNTRY = 21;

    /**
     * When cart rule requires a carrier before it can be applied
     */
    const REQUIRES_CARRIER = 22;

    /**
     * When cart rule cannot be used, because it is not available for cart carrier
     */
    const UNAVAILABLE_FOR_CARRIER = 23;

    /**
     * When cart rule cannot be used, because it is not applicable for on sale products
     */
    const UNAVAILABLE_FOR_SALE_PRODUCTS = 24;

    /**
     * When cart rule cannot be used, because it requires minimum amount to be reached
     */
    const REQUIRES_AMOUNT = 25;

    /**
     * When cart rule cannot be applied, because it is already in cart
     */
    const ALREADY_IN_CART = 26;

    /**
     * When cart rule cannot be combined with already applied cart rules
     */
    const CANNOT_BE_COMBINED = 27;
}
