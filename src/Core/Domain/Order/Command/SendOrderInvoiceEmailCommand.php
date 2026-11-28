<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Sends the invoices of an order to its customer, without changing the order status.
 */
class SendOrderInvoiceEmailCommand
{
    /**
     * @var OrderId
     */
    private $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = new OrderId($orderId);
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }
}
