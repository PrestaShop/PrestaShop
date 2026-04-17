<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\Exception;

use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateIdInterface;
use Throwable;

/**
 * Is raised when state or states cannot be deleted
 */
class DeleteStateException extends StateException
{
    /**
     * When fails to delete single state
     */
    public const FAILED_DELETE = 1;

    /**
     * When fails to delete states in bulk actions
     */
    public const FAILED_BULK_DELETE = 2;

    /**
     * @param StateIdInterface $stateId
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function createDeleteFailure(StateIdInterface $stateId, ?Throwable $previous = null): self
    {
        return new static(
            sprintf('Cannot delete state with id "%d"', $stateId->getValue()),
            static::FAILED_DELETE,
            $previous
        );
    }

    /**
     * @param StateId $stateId
     * @param Throwable|null $previous
     *
     * @return static
     */
    public static function createBulkDeleteFailure(StateId $stateId, ?Throwable $previous = null): self
    {
        return new static(
            sprintf('An error occurred when deleting state with id "%d"', $stateId->getValue()),
            static::FAILED_BULK_DELETE,
            $previous
        );
    }
}
