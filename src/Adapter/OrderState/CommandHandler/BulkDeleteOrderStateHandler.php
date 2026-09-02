<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderState\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Command\BulkDeleteOrderStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderState\CommandHandler\BulkDeleteOrderStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\BulkDeleteOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;

/**
 * Handles command which deletes OrderStatees in bulk action
 */
#[AsCommandHandler]
class BulkDeleteOrderStateHandler extends AbstractOrderStateHandler implements BulkDeleteOrderStateHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws BulkDeleteOrderStateException
     */
    public function handle(BulkDeleteOrderStateCommand $command): void
    {
        $errors = [];

        foreach ($command->getOrderStateIds() as $orderStateId) {
            try {
                $orderState = $this->getOrderState($orderStateId);

                if (!$this->deleteOrderState($orderState)) {
                    $errors[] = $orderState->id;
                }
            } catch (OrderStateException) {
                $errors[] = $orderStateId->getValue();
            }
        }

        if (!empty($errors)) {
            throw new BulkDeleteOrderStateException(
                $errors,
                'Failed to delete all of selected order statuses'
            );
        }
    }
}
