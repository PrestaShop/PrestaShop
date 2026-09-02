<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

use Exception;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * Throw when failed changing order status
 */
class ChangeOrderStatusException extends OrderException
{
    /**
     * @var OrderId[]
     */
    private $ordersWithFailedToUpdateStatus;

    /**
     * @var OrderId[]
     */
    private $ordersWithFailedToSendEmail;

    /**
     * @var OrderId[]
     */
    private $ordersWithAssignedStatus;

    /**
     * @param OrderId[] $ordersWithFailedToUpdateStatus
     * @param OrderId[] $ordersWithFailedToSendEmail
     * @param OrderId[] $ordersWithAssignedStatus
     * @param string $message
     * @param int $code
     * @param Exception|null $previous
     */
    public function __construct(
        array $ordersWithFailedToUpdateStatus,
        array $ordersWithFailedToSendEmail,
        array $ordersWithAssignedStatus,
        $message = '',
        $code = 0,
        $previous = null
    ) {
        $this->ordersWithFailedToUpdateStatus = $ordersWithFailedToUpdateStatus;
        $this->ordersWithFailedToSendEmail = $ordersWithFailedToSendEmail;
        $this->ordersWithAssignedStatus = $ordersWithAssignedStatus;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return OrderId[]
     */
    public function getOrdersWithFailedToUpdateStatus()
    {
        return $this->ordersWithFailedToUpdateStatus;
    }

    /**
     * @return OrderId[]
     */
    public function getOrdersWithFailedToSendEmail()
    {
        return $this->ordersWithFailedToSendEmail;
    }

    /**
     * @return OrderId[]
     */
    public function getOrdersWithAssignedStatus()
    {
        return $this->ordersWithAssignedStatus;
    }
}
