<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Changes status for given orders.
 */
class BulkChangeOrderStatusCommand
{
    /**
     * @var OrderId[]
     */
    private $orderIds;

    /**
     * @var int
     */
    private $newOrderStatusId;

    /**
     * @param int[] $orderIds
     * @param int $newOrderStatusId
     */
    public function __construct(array $orderIds, $newOrderStatusId)
    {
        if (!is_int($newOrderStatusId) || 0 >= $newOrderStatusId) {
            throw new OrderException(sprintf('Order status Id must be integer greater than 0, but %s given.', var_export($newOrderStatusId, true)));
        }

        $this->newOrderStatusId = $newOrderStatusId;
        $this->setOrderIds($orderIds);
    }

    /**
     * @return OrderId[]
     */
    public function getOrderIds()
    {
        return $this->orderIds;
    }

    /**
     * @return int
     */
    public function getNewOrderStatusId()
    {
        return $this->newOrderStatusId;
    }

    /**
     * @param int[] $orderIds
     */
    private function setOrderIds(array $orderIds)
    {
        foreach ($orderIds as $orderId) {
            $this->orderIds[] = new OrderId($orderId);
        }
    }
}
