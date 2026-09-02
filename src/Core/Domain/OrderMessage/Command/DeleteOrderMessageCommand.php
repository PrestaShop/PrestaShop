<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Delete given order message
 */
class DeleteOrderMessageCommand
{
    /**
     * @var OrderMessageId
     */
    private $orderMessageId;

    /**
     * @param int $orderMessageId
     */
    public function __construct(int $orderMessageId)
    {
        $this->orderMessageId = new OrderMessageId($orderMessageId);
    }

    /**
     * @return OrderMessageId
     */
    public function getOrderMessageId(): OrderMessageId
    {
        return $this->orderMessageId;
    }
}
