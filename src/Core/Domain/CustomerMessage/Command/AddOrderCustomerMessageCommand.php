<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Command;

use PrestaShop\PrestaShop\Core\Domain\CustomerMessage\Exception\CustomerMessageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;

/**
 * This command adds/sends message to the customer related with the order.
 */
class AddOrderCustomerMessageCommand
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var OrderId
     */
    private $orderId;
    /**
     * @var bool
     */
    private $isPrivate;

    /**
     * @param int $orderId
     * @param string $message
     * @param bool $isPrivate
     *
     * @throws OrderException
     * @throws CustomerMessageConstraintException
     */
    public function __construct(int $orderId, string $message, bool $isPrivate)
    {
        $this->orderId = new OrderId($orderId);
        $this->setMessage($message);
        $this->isPrivate = $isPrivate;
    }

    /**
     * @return OrderId
     */
    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->isPrivate;
    }

    /**
     * @param string $message
     *
     * @throws CustomerMessageConstraintException
     */
    private function setMessage(string $message): void
    {
        if (!$message) {
            throw new CustomerMessageConstraintException('Missing required message', CustomerMessageConstraintException::MISSING_MESSAGE);
        }

        $this->message = $message;
    }
}
