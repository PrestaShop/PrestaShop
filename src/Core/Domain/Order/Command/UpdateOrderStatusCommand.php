<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Updates order status.
 */
class UpdateOrderStatusCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var int
     */
    private $newOrderStatusId;

    /**
     * @param int $orderId
     * @param int $newOrderStatusId
     */
    public function __construct($orderId, $newOrderStatusId)
    {
        $this->orderId = new OrderId($orderId);
        $this->newOrderStatusId = $newOrderStatusId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getNewOrderStatusId()
    {
        return $this->newOrderStatusId;
    }
}
