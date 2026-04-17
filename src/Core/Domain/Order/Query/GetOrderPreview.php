<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Query;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

class GetOrderPreview
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @param int $orderId
     */
    public function __construct(int $orderId)
    {
        $this->orderId = new OrderId($orderId);
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }
}
