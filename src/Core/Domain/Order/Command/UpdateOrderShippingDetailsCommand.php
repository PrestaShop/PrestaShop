<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Updates shipping details for given order.
 */
class UpdateOrderShippingDetailsCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var int
     */
    private $newCarrierId;

    /**
     * @var string|null
     */
    private $trackingNumber;

    /**
     * @var int
     */
    private $currentOrderCarrierId;

    /**
     * @param int $orderId
     * @param int $currentOrderCarrierId
     * @param int $newCarrierId
     * @param string $trackingNumber
     */
    public function __construct(int $orderId, int $currentOrderCarrierId, int $newCarrierId, ?string $trackingNumber = '')
    {
        $this->orderId = new OrderId($orderId);
        $this->newCarrierId = $newCarrierId;
        $this->trackingNumber = $trackingNumber;
        $this->currentOrderCarrierId = $currentOrderCarrierId;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return int
     */
    public function getNewCarrierId(): int
    {
        return $this->newCarrierId;
    }

    /**
     * @return string|null
     */
    public function getShippingTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    /**
     * @return int
     */
    public function getCurrentOrderCarrierId(): int
    {
        return $this->currentOrderCarrierId;
    }
}
