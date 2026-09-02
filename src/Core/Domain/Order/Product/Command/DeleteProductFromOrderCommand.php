<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Product\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Deletes product from given order.
 */
class DeleteProductFromOrderCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @param int $orderId
     * @param int $orderDetailId
     */
    public function __construct($orderId, $orderDetailId)
    {
        $this->orderId = new OrderId($orderId);
        $this->orderDetailId = $orderDetailId;
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
    public function getOrderDetailId()
    {
        return $this->orderDetailId;
    }
}
