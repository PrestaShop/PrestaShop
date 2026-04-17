<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Invoice\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Generates invoice for given order.
 */
class GenerateInvoiceCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    /**
     * @param int $orderId
     */
    public function __construct($orderId)
    {
        $this->orderId = new OrderId($orderId);
    }

    /**
     * @return OrderId
     */
    public function getOrderId()
    {
        return $this->orderId;
    }
}
