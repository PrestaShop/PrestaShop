<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

class CancelOrderProductCommand
{
    /**
     * @var array
     *
     * key: orderDetailId, value: quantity
     */
    private $cancelledProducts;

    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * CancelOrderProductCommand constructor.
     *
     * @param array $cancelledProducts
     * @param int $orderId
     */
    public function __construct(array $cancelledProducts, int $orderId)
    {
        $this->cancelledProducts = $cancelledProducts;
        $this->orderId = new OrderId($orderId);
    }

    /**
     * @return array
     */
    public function getCancelledProducts()
    {
        return $this->cancelledProducts;
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
}
