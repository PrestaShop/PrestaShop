<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderState\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\DeleteOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler\DeleteOrderStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\DeleteOrderStateException;

/**
 * Handles command which deletes order states
 */
#[AsCommandHandler]
class DeleteOrderStateHandler extends AbstractOrderStateHandler implements DeleteOrderStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteOrderStateCommand $command): void
    {
        $orderStateId = $command->getOrderStateId();
        $orderState = $this->getOrderState($orderStateId);

        if (!$orderState->isRemovable()) {
            throw new DeleteOrderStateException(
                sprintf('Cannot delete unremovable OrderState object with id "%d".', $orderStateId->getValue()),
                DeleteOrderStateException::FAILED_DELETE
            );
        }

        if (!$this->deleteOrderState($orderState)) {
            throw new DeleteOrderStateException(
                sprintf('Cannot delete OrderState object with id "%d".', $orderStateId->getValue()),
                DeleteOrderStateException::FAILED_DELETE
            );
        }
    }
}
