<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception;

/**
 * Is thrown when order return state constraint is violated
 */
class OrderReturnStateConstraintException extends OrderReturnStateException
{
    /**
     * @var int Code is used when invalid name is provided for order return state
     */
    public const INVALID_NAME = 1;
    /**
     * @var int Code is used when empty name is provided for order return state
     */
    public const EMPTY_NAME = 2;
}
