<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Deletes Address
 */
class DeleteOrderStateCommand
{
    /**
     * @var OrderStateId
     */
    private $orderStateId;

    /**
     * @param int $orderStateId
     *
     * @throws OrderStateException
     */
    public function __construct(int $orderStateId)
    {
        $this->orderStateId = new OrderStateId($orderStateId);
    }

    /**
     * @return OrderStateId
     */
    public function getOrderStateId(): OrderStateId
    {
        return $this->orderStateId;
    }
}
