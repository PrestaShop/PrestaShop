<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Deletes order return statuses in bulk action
 */
class BulkDeleteOrderReturnStateCommand
{
    /**
     * @var OrderReturnStateId[]
     */
    private $orderReturnStateIds = [];

    /**
     * @param int[] $orderReturnStateIds
     */
    public function __construct(array $orderReturnStateIds)
    {
        $this->setOrderReturnStateIds($orderReturnStateIds);
    }

    /**
     * @return OrderReturnStateId[]
     */
    public function getOrderReturnStateIds(): array
    {
        return $this->orderReturnStateIds;
    }

    /**
     * @param int[] $orderReturnStateIds
     */
    private function setOrderReturnStateIds(array $orderReturnStateIds): void
    {
        foreach ($orderReturnStateIds as $orderReturnStateId) {
            $this->orderReturnStateIds[] = new OrderReturnStateId($orderReturnStateId);
        }
    }
}
