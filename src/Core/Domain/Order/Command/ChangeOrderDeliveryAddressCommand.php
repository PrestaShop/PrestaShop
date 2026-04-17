<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Changes delivery address for given order.
 */
class ChangeOrderDeliveryAddressCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var AddressId
     */
    private $newDeliveryAddressId;

    /**
     * @param int $orderId
     * @param int $newDeliveryAddressId
     */
    public function __construct($orderId, $newDeliveryAddressId)
    {
        $this->orderId = new OrderId($orderId);
        $this->newDeliveryAddressId = new AddressId($newDeliveryAddressId);
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @return AddressId
     */
    public function getNewDeliveryAddressId()
    {
        return $this->newDeliveryAddressId;
    }
}
