<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\OrderMessage\CommandHandler;

use PrestaShop\PrestaShop\Adapter\OrderMessage\AbstractOrderMessageHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\DeleteOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler\DeleteOrderMessageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageException;
use PrestaShopException;

/**
 * Handles deleting order message using object model
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteOrderMessageHandler extends AbstractOrderMessageHandler implements DeleteOrderMessageHandlerInterface
{
    /**
     * @param DeleteOrderMessageCommand $command
     */
    public function handle(DeleteOrderMessageCommand $command): void
    {
        $orderMessage = $this->getOrderMessage($command->getOrderMessageId());

        try {
            if (false === $orderMessage->delete()) {
                throw new OrderMessageException(sprintf('Failed to delete Order message with id "%d"', $orderMessage->id), OrderMessageException::FAILED_DELETE);
            }
        } catch (PrestaShopException) {
            throw new OrderMessageException(sprintf('Failed to delete Order message with id "%s"', $orderMessage->id));
        }
    }
}
