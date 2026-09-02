<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Exception;

/**
 * Is thrown when order state cannot be deleted
 */
class DeleteOrderStateException extends OrderStateException
{
    /**
     * When fails to delete single order state
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete order states in bulk action
     */
    public const FAILED_BULK_DELETE = 20;
}
