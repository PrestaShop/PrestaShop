<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailId;

/**
 * Thrown when order detail is not found
 */
class OrderDetailNotFoundException extends OrderException
{
    public function __construct(
        private readonly ?OrderDetailId $orderDetailId = null,
        string $message = '',
        int $code = 0,
        ?Exception $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getOrderDetailId(): ?OrderDetailId
    {
        return $this->orderDetailId;
    }
}
