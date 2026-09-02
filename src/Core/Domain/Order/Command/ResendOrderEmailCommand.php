<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

class ResendOrderEmailCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var int
     */
    private $orderStatusId;

    /**
     * @var int
     */
    private $orderHistoryId;

    public function __construct(int $orderId, int $orderStatusId, int $orderHistoryId)
    {
        $this->orderId = new OrderId($orderId);
        $this->orderStatusId = $orderStatusId;
        $this->orderHistoryId = $orderHistoryId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getOrderStatusId(): int
    {
        return $this->orderStatusId;
    }

    /**
     * @return int
     */
    public function getOrderHistoryId(): int
    {
        return $this->orderHistoryId;
    }
}
