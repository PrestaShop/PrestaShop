<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\Query;

use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;

/**
 * Gets order state information for editing.
 */
class GetOrderStateForEditing
{
    /**
     * @var OrderStateId
     */
    private $orderStateId;

    /**
     * @param int $orderStateId
     */
    public function __construct($orderStateId)
    {
        $this->orderStateId = new OrderStateId($orderStateId);
    }

    /**
     * @return OrderStateId
     */
    public function getOrderStateId()
    {
        return $this->orderStateId;
    }
}
