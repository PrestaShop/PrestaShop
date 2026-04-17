<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Exception;

/**
 * Thrown when a discount is added with an invalid amount/percentage
 */
class InvalidCartRuleDiscountValueException extends InvalidCartRuleValueException
{
    /**
     * Code used when an invalid percent value is under min value
     */
    public const INVALID_MIN_PERCENT = 10;

    /**
     * Code used when an invalid percent value is above max value
     */
    public const INVALID_MAX_PERCENT = 20;

    /**
     * Code used when the specified amount is under min value
     */
    public const INVALID_MIN_AMOUNT = 30;

    /**
     * Code used when the specified amount is above max value
     */
    public const INVALID_MAX_AMOUNT = 40;

    /**
     * Code used when free shipping cannot be applied
     */
    public const INVALID_FREE_SHIPPING = 50;
}
