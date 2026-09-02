<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Deletes Order Return States
 */
class DeleteOrderReturnStateCommand
{
    /**
     * @var OrderReturnStateId
     */
    private $orderReturnStateId;

    /**
     * @param int $orderReturnStateId
     */
    public function __construct(int $orderReturnStateId)
    {
        $this->orderReturnStateId = new OrderReturnStateId($orderReturnStateId);
    }

    /**
     * @return OrderReturnStateId
     */
    public function getOrderReturnStateId(): OrderReturnStateId
    {
        return $this->orderReturnStateId;
    }
}
