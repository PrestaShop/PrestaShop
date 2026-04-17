<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

use Throwable;

/**
 * Throw new when a partial refund's is asked without a specified quantity
 */
class InvalidRefundException extends OrderException
{
    /**
     * @var int
     */
    private $refundableQuantity;

    /**
     * Used when the quantity refunded is not strictly positive
     */
    public const INVALID_QUANTITY = 1;

    /**
     * Used when the quantity refunded is higher than the remaining quantity
     */
    public const QUANTITY_TOO_HIGH = 2;

    /**
     * Used when the amount refunded is not strictly positive
     */
    public const INVALID_AMOUNT = 3;

    /**
     * Used when no refund details have been supplied (nor products nor shipping refund)
     */
    public const NO_REFUNDS = 4;

    /**
     * Used when no generation is set (no credit slip and no voucher generation)
     */
    public const NO_GENERATION = 5;

    /**
     * @param int $code
     * @param int $refundableQuantity
     * @param string $message
     * @param Throwable|null $previous
     */
    public function __construct($code = 0, int $refundableQuantity = 0, $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->refundableQuantity = $refundableQuantity;
    }

    /**
     * @return int
     */
    public function getRefundableQuantity(): int
    {
        return $this->refundableQuantity;
    }
}
