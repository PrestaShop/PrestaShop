<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\OrderMessage;

use OrderMessage;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\ValueObject\OrderMessageId;

/**
 * Provides common methods for OrderMessage command/query handlers that uses object model
 *
 * @internal
 */
abstract class AbstractOrderMessageHandler
{
    /**
     * @param OrderMessageId $orderMessageId
     *
     * @return OrderMessage
     */
    protected function getOrderMessage(OrderMessageId $orderMessageId): OrderMessage
    {
        $orderMessage = new OrderMessage($orderMessageId->getValue());

        if ($orderMessage->id !== $orderMessageId->getValue()) {
            throw new OrderMessageNotFoundException($orderMessageId, sprintf('Order message with id "%s" was not found', $orderMessageId->getValue()));
        }

        return $orderMessage;
    }
}
