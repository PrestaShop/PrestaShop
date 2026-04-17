<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\OrderMessage\CommandHandler;

use OrderMessage;
use PrestaShop\PrestaShop\Adapter\OrderMessage\AbstractOrderMessageHandler;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Command\EditOrderMessageCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\CommandHandler\EditOrderMessageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageException;
use PrestaShop\PrestaShop\Core\Domain\OrderMessage\Exception\OrderMessageNameAlreadyUsedException;
use PrestaShopException;

/**
 * Handles editing order message using legacy object model
 *
 * @internal
 */
#[AsCommandHandler]
final class EditOrderMessageHandler extends AbstractOrderMessageHandler implements EditOrderMessageHandlerInterface
{
    /**
     * @param EditOrderMessageCommand $command
     */
    public function handle(EditOrderMessageCommand $command): void
    {
        $this->assertNameIsNotAlreadyUsed($command);

        $orderMessage = $this->getOrderMessage($command->getOrderMessageId());

        if (null !== $command->getLocalizedName()) {
            $orderMessage->name = $command->getLocalizedName();
        }

        if (null !== $command->getLocalizedMessage()) {
            $orderMessage->message = $command->getLocalizedMessage();
        }

        try {
            $orderMessage->validateFields();
            $orderMessage->validateFieldsLang();
        } catch (PrestaShopException $e) {
            throw new OrderMessageException('Order message contains invalid fields', 0, $e);
        }

        try {
            if (false === $orderMessage->update()) {
                throw new OrderMessageException(sprintf('Failed to update order message with id "%s"', $command->getOrderMessageId()->getValue()));
            }
        } catch (PrestaShopException $e) {
            throw new OrderMessageException(sprintf('Failed to update order message with id "%s"', $command->getOrderMessageId()->getValue()), 0, $e);
        }
    }

    private function assertNameIsNotAlreadyUsed(EditOrderMessageCommand $command): void
    {
        foreach ($command->getLocalizedName() as $langId => $langName) {
            $orderMessages = OrderMessage::getOrderMessages($langId);
            if (!is_array($orderMessages)) {
                continue;
            }
            foreach ($orderMessages as $orderMessage) {
                if ((int) $orderMessage['id_order_message'] === $command->getOrderMessageId()->getValue()) {
                    continue;
                }
                if ($orderMessage['name'] === $langName) {
                    throw new OrderMessageNameAlreadyUsedException(
                        $langName,
                        $langId,
                        'An order message already exists for this name'
                    );
                }
            }
        }
    }
}
