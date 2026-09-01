<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\BulkDeleteBusinessEntityHandlerInterface;

/**
 * Class BulkDeleteBusinessEntityCommand is used to soft delete a list of business entities at once.
 *
 * @see BulkDeleteBusinessEntityHandlerInterface
 */
class BulkDeleteBusinessEntityCommand
{
    /**
     * @param int[] $businessEntityIds
     */
    public function __construct(
        private readonly array $businessEntityIds,
    ) {
    }

    /**
     * @return int[]
     */
    public function getBusinessEntityIds(): array
    {
        return $this->businessEntityIds;
    }
}
