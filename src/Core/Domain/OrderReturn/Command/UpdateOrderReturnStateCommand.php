<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Updates order returns state.
 */
class UpdateOrderReturnStateCommand
{
    /**
     * @var OrderReturnId
     */
    private $orderReturnId;

    /**
     * @var OrderReturnStateId
     */
    private $orderReturnStateId;

    /**
     * @param int $orderReturnId
     * @param int $orderReturnStateId
     *
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderReturnId, int $orderReturnStateId)
    {
        $this->orderReturnId = new OrderReturnId($orderReturnId);
        $this->orderReturnStateId = new OrderReturnStateId($orderReturnStateId);
    }

    /**
     * @return OrderReturnId
     */
    public function getOrderReturnId(): OrderReturnId
    {
        return $this->orderReturnId;
    }

    /**
     * @return OrderReturnStateId
     */
    public function getOrderReturnStateId(): OrderReturnStateId
    {
        return $this->orderReturnStateId;
    }
}
