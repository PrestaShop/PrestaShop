<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\BulkDeleteOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler\BulkDeleteOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\BulkDeleteOrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;

/**
 * Handles command which deletes OrderReturnStates in bulk action
 */
#[AsCommandHandler]
class BulkDeleteOrderReturnStateHandler extends AbstractOrderReturnStateHandler implements BulkDeleteOrderReturnStateHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws BulkDeleteOrderReturnStateException
     */
    public function handle(BulkDeleteOrderReturnStateCommand $command): void
    {
        $errors = [];

        foreach ($command->getOrderReturnStateIds() as $orderReturnStateId) {
            try {
                $orderReturnState = $this->getOrderReturnState($orderReturnStateId);

                if (!$this->deleteOrderReturnState($orderReturnState)) {
                    $errors[] = $orderReturnState->id;
                }
            } catch (OrderReturnStateException) {
                $errors[] = $orderReturnStateId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteOrderReturnStateException(
                $errors,
                'Failed to delete all of selected order return statuses'
            );
        }
    }
}
