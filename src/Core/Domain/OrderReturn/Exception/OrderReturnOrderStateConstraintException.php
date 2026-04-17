<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception;

/**
 * Is thrown when order return constraint is violated
 */
class OrderReturnOrderStateConstraintException extends OrderReturnException
{
    /**
     * When order return order state id is not valid
     */
    public const INVALID_ID = 10;
}
