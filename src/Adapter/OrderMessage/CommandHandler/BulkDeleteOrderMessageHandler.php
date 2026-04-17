<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\OrderMessage\CommandHandler;

use PrestaShop\PrestaShop\Adapter\OrderMessage\AbstractOrderMessageHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\BulkDeleteOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler\BulkDeleteOrderMessageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageException;
use PrestaShopException;

/**
 * Deletes Order messages in bulk action using object model
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkDeleteOrderMessageHandler extends AbstractOrderMessageHandler implements BulkDeleteOrderMessageHandlerInterface
{
    /**
     * @param BulkDeleteOrderMessageCommand $command
     */
    public function handle(BulkDeleteOrderMessageCommand $command): void
    {
        foreach ($command->getOrderMessageIds() as $orderMessageId) {
            $orderMessage = $this->getOrderMessage($orderMessageId);

            try {
                if (false === $orderMessage->delete()) {
                    throw new OrderMessageException(sprintf('Failed to delete Order message with id "%d" during bulk delete', $orderMessage->id), OrderMessageException::FAILED_BULK_DELETE);
                }
            } catch (PrestaShopException) {
                throw new OrderMessageException(sprintf('Failed to delete Order message with id "%s"', $orderMessage->id));
            }
        }
    }
}
