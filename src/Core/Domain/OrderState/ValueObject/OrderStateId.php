<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;

/**
 * Defines OrderState ID with it's constraints
 */
class OrderStateId
{
    /**
     * @var int
     */
    private $orderStateId;

    /**
     * @param int $orderStateId
     */
    public function __construct($orderStateId)
    {
        $this->assertIntegerIsGreaterThanZero($orderStateId);

        $this->orderStateId = $orderStateId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->orderStateId;
    }

    /**
     * @param int $orderStateId
     */
    private function assertIntegerIsGreaterThanZero($orderStateId)
    {
        if (!is_int($orderStateId) || 0 > $orderStateId) {
            throw new OrderStateException(sprintf('OrderState id %s is invalid. OrderState id must be number that is greater than zero.', var_export($orderStateId, true)));
        }
    }
}
