<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Removes cart rule from given order.
 */
class DeleteCartRuleFromOrderCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var int
     */
    private $orderCartRuleId;

    /**
     * @param int $orderId
     * @param int $orderCartRuleId
     */
    public function __construct($orderId, $orderCartRuleId)
    {
        $this->orderId = new OrderId($orderId);
        $this->orderCartRuleId = $orderCartRuleId;
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
    public function getOrderCartRuleId()
    {
        return $this->orderCartRuleId;
    }
}
