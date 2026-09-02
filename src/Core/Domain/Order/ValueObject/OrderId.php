<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;

/**
 * Order identity
 */
class OrderId
{
    /**
     * @var int
     */
    private $orderId;

    /**
     * @param int $orderId
     *
     * @throws OrderException
     */
    public function __construct($orderId)
    {
        $this->assertIntegerIsGreaterThanZero($orderId);

        $this->orderId = $orderId;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->orderId;
    }

    /**
     * @param int $orderId
     */
    private function assertIntegerIsGreaterThanZero($orderId)
    {
        if (!is_int($orderId) || 0 > $orderId) {
            throw new OrderException(sprintf('Order id %s is invalid. Order id must be number that is greater than zero.', var_export($orderId, true)));
        }
    }
}
