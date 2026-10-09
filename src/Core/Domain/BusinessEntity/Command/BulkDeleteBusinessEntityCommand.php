<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Command;

use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\CommandHandler\BulkDeleteBusinessEntityHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\Exception\BusinessEntityConstraintException;
use PrestaShop\PrestaShop\Core\Domain\BusinessEntity\ValueObject\BusinessEntityId;

/**
 * Class BulkDeleteBusinessEntityCommand is used to soft delete a list of business entities at once.
 *
 * @see BulkDeleteBusinessEntityHandlerInterface
 */
class BulkDeleteBusinessEntityCommand
{
    /**
     * @var BusinessEntityId[]
     */
    private readonly array $businessEntityIds;

    /**
     * @param int[] $businessEntityIds
     *
     * @throws BusinessEntityConstraintException
     */
    public function __construct(array $businessEntityIds)
    {
        $ids = [];
        foreach ($businessEntityIds as $businessEntityId) {
            $ids[] = new BusinessEntityId((int) $businessEntityId);
        }

        $this->businessEntityIds = $ids;
    }

    /**
     * @return BusinessEntityId[]
     */
    public function getBusinessEntityIds(): array
    {
        return $this->businessEntityIds;
    }
}
