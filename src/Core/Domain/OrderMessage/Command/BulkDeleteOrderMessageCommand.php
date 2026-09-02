<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Delete given order messages
 */
class BulkDeleteOrderMessageCommand
{
    /**
     * @var OrderMessageId[]
     */
    private $orderMessageIds;

    /**
     * @param int[] $orderMessageIds
     */
    public function __construct(array $orderMessageIds)
    {
        foreach ($orderMessageIds as $orderMessageId) {
            $this->orderMessageIds[] = new OrderMessageId($orderMessageId);
        }
    }

    /**
     * @return OrderMessageId[]
     */
    public function getOrderMessageIds(): array
    {
        return $this->orderMessageIds;
    }
}
