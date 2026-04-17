<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

use Throwable;

/**
 * Thrown when the order state is incompatible with an action (ex: standard
 * refund on an order not paid yet).
 */
class InvalidOrderStateException extends OrderException
{
    /**
     * Used when the order is not paid (and it should be)
     */
    public const NOT_PAID = 1;

    /**
     * Used when the order is already paid (and it should not be)
     */
    public const ALREADY_PAID = 2;

    /**
     * Used when the order has not been delivered (and it should have)
     */
    public const DELIVERY_NOT_FOUND = 3;

    /**
     * Used when the order has been delivered (and it shouldn't have)
     */
    public const UNEXPECTED_DELIVERY = 4;
    /**
     * Used when the order state is not found
     */
    public const INVALID_ID = 5;

    /**
     * @param int $code
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
