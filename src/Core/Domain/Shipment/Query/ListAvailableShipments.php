<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shipment\Query;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Shipment\ValueObject\OrderDetailsId;

class ListAvailableShipments
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var OrderDetailsId
     */
    private $orderIdDetails;

    public function __construct(int $orderId, array $orderIdDetails)
    {
        $this->orderId = new OrderId($orderId);
        $this->orderIdDetails = new OrderDetailsId($orderIdDetails);
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getOrderIdDetails(): OrderDetailsId
    {
        return $this->orderIdDetails;
    }
}
