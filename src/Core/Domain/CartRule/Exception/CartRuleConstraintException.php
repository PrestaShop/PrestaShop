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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
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
     * Used when cart rule is using an invalid discount application type
     */
    public const INVALID_DISCOUNT_APPLICATION_TYPE = 1;

    /**
     * Used when cart rule name is empty
     */
    public const EMPTY_NAME = 2;

    /**
     * Used when cart rule's priority is invalid
     */
    public const INVALID_PRIORITY = 3;

    /**
     * Used when cart rule's date from is greater than date to
     */
    public const DATE_FROM_GREATER_THAN_DATE_TO = 4;

    /**
     * Used when cart rule's quantity is invalid
     */
    public const INVALID_QUANTITY = 5;

    /**
     * Used when quantity per user is invalid
     */
    public const INVALID_QUANTITY_PER_USER = 6;

    /**
     * Used when percentage discount is invalid
     */
    public const INVALID_PERCENTAGE = 7;

    /**
     * Used when cart rule has invalid gift product assigned
     */
    public const INVALID_GIFT_PRODUCT = 8;

    /**
     * Used when cart rule has invalid gift product attribute
     */
    public const INVALID_GIFT_PRODUCT_ATTRIBUTE = 9;

    /**
     * Used when cart rule has incompatible actions,
     * e.g. both amount and percentage discounts at the same time.
     */
    public const INCOMPATIBLE_CART_RULE_ACTIONS = 10;

    /**
     * Used when cart rule is missing an action.
     */
    public const MISSING_ACTION = 11;

    /**
     * Used when discount is applied to specific product, but that product is not set.
     */
    public const MISSING_DISCOUNT_APPLICATION_PRODUCT = 12;

    /**
     * Used when cart rule id constraints are violated
     */
    public const INVALID_ID = 13;

    /**
     * When cart rule status is not valid
     */
    public const INVALID_STATUS = 20;
}
