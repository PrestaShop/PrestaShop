<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Changes invoice address for given order.
 */
class ChangeOrderInvoiceAddressCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @var AddressId
     */
    private $newInvoiceAddressId;

    /**
     * @param int $orderId
     * @param int $newInvoiceAddressId
     */
    public function __construct($orderId, $newInvoiceAddressId)
    {
        $this->orderId = new OrderId($orderId);
        $this->newInvoiceAddressId = new AddressId($newInvoiceAddressId);
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
    public function getNewInvoiceAddressId()
    {
        return $this->newInvoiceAddressId;
    }
}
