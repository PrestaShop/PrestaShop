<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;

/**
 * Defines OrderReturnState ID with it's constraints
 */
class OrderReturnStateId
{
    /**
     * @var int
     */
    private $orderReturnStateId;

    /**
     * @param int $orderReturnStateId
     */
    public function __construct($orderReturnStateId)
    {
        $this->assertIntegerIsGreaterThanZero($orderReturnStateId);

        $this->orderReturnStateId = $orderReturnStateId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->orderReturnStateId;
    }

    /**
     * @param int $orderReturnStateId
     */
    private function assertIntegerIsGreaterThanZero($orderReturnStateId)
    {
        if (!is_int($orderReturnStateId) || 0 > $orderReturnStateId) {
            throw new OrderReturnStateException(sprintf('OrderReturnState id %s is invalid. OrderReturnState id must be number that is greater than zero.', var_export($orderReturnStateId, true)));
        }
    }
}
