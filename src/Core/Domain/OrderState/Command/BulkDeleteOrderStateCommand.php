<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Deletes addresses in bulk action
 */
class BulkDeleteOrderStateCommand
{
    /**
     * @var OrderStateId[]
     */
    private $orderStateIds = [];

    /**
     * @param int[] $orderStateIds
     *
     * @throws OrderStateException
     */
    public function __construct(array $orderStateIds)
    {
        $this->setOrderStateIds($orderStateIds);
    }

    /**
     * @return OrderStateId[]
     */
    public function getOrderStateIds(): array
    {
        return $this->orderStateIds;
    }

    /**
     * @param int[] $orderStateIds
     *
     * @throws OrderStateException
     */
    private function setOrderStateIds(array $orderStateIds)
    {
        foreach ($orderStateIds as $orderStateId) {
            $this->orderStateIds[] = new OrderStateId($orderStateId);
        }
    }
}
