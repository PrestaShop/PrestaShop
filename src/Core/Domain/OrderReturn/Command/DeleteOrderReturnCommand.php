<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;

/**
 * Removes an entire merchandise return (and its detail rows) from the grid row action.
 */
class DeleteOrderReturnCommand
{
    private OrderReturnId $orderReturnId;

    /**
     * @throws OrderReturnConstraintException
     */
    public function __construct(int $orderReturnId)
    {
        $this->orderReturnId = new OrderReturnId($orderReturnId);
    }

    public function getOrderReturnId(): OrderReturnId
    {
        return $this->orderReturnId;
    }
}
