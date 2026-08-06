<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;

/**
 * Removes several merchandise returns at once from the grid bulk action.
 */
class BulkDeleteOrderReturnsCommand
{
    /**
     * @var OrderReturnId[]
     */
    private array $orderReturnIds;

    /**
     * @param int[] $orderReturnIds
     *
     * @throws OrderReturnConstraintException
     */
    public function __construct(array $orderReturnIds)
    {
        $this->orderReturnIds = [];
        foreach ($orderReturnIds as $orderReturnId) {
            $this->orderReturnIds[] = new OrderReturnId((int) $orderReturnId);
        }
    }

    /**
     * @return OrderReturnId[]
     */
    public function getOrderReturnIds(): array
    {
        return $this->orderReturnIds;
    }
}
