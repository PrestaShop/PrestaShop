<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Thrown when order is not found
 */
class OrderNotFoundException extends OrderException
{
    public function __construct(
        private readonly ?OrderId $orderId = null,
        string $message = '',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getOrderId(): ?OrderId
    {
        return $this->orderId;
    }
}
