<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Query;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Gets order return state information for editing.
 */
class GetOrderReturnStateForEditing
{
    /**
     * @var OrderReturnStateId
     */
    private $orderReturnStateId;

    /**
     * @param int $orderReturnStateId
     */
    public function __construct($orderReturnStateId)
    {
        $this->orderReturnStateId = new OrderReturnStateId($orderReturnStateId);
    }

    /**
     * @return OrderReturnStateId
     */
    public function getOrderReturnStateId()
    {
        return $this->orderReturnStateId;
    }
}
