<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

/**
 * Exception thrown when constraint is violated in Order subdomain.
 */
class OrderConstraintException extends OrderException
{
    /**
     * Used in create order from BO when the customer message is invalid.
     */
    public const INVALID_CUSTOMER_MESSAGE = 1;

    /**
     * @var int Is used when invalid (not string) internal note provided
     */
    public const INVALID_INTERNAL_NOTE = 2;

    /**
     * Used in add payment from BO when the payment method is invalid.
     */
    public const INVALID_PAYMENT_METHOD = 3;

    /**
     * Code is used when the cart rule name given when adding a discount to an order is longer than
     * the column can hold.
     */
    public const INVALID_CART_RULE_NAME = 4;
}
