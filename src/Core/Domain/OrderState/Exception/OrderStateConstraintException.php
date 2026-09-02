<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Exception;

/**
 * Is thrown when order state constraint is violated
 */
class OrderStateConstraintException extends OrderStateException
{
    /**
     * @var int Code is used when invalid name is provided for order state
     */
    public const INVALID_NAME = 1;
    /**
     * @var int Code is used when empty name is provided for order state
     */
    public const EMPTY_NAME = 2;
    /**
     * @var int
     */
    public const INVALID_FILE_SIZE = 3;
}
