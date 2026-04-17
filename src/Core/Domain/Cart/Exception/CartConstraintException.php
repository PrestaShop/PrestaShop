<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Exception;

/**
 * Thrown when cart constraints are violated
 */
class CartConstraintException extends CartException
{
    /**
     * When cart product quantity is invalid
     */
    public const INVALID_QUANTITY = 1;

    /**
     * When cart product quantity is already correct
     */
    public const UNCHANGED_QUANTITY = 2;

    /**
     * When carrier is not found or inactive
     */
    public const INVALID_CARRIER = 3;
}
