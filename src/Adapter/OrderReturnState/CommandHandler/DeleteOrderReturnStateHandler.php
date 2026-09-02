<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\DeleteOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler\DeleteOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\DeleteOrderReturnStateException;

/**
 * Handles command which deletes order states
 */
#[AsCommandHandler]
class DeleteOrderReturnStateHandler extends AbstractOrderReturnStateHandler implements DeleteOrderReturnStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(DeleteOrderReturnStateCommand $command): void
    {
        $orderReturnStateId = $command->getOrderReturnStateId();
        $orderReturnState = $this->getOrderReturnState($orderReturnStateId);

        if (!$this->deleteOrderReturnState($orderReturnState)) {
            throw new DeleteOrderReturnStateException(
                sprintf('Cannot delete OrderReturnState object with id "%d".', $orderReturnStateId->getValue()),
                DeleteOrderReturnStateException::FAILED_DELETE
            );
        }
    }
}
