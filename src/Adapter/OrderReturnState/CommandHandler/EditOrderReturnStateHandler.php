<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\CommandHandler;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\EditOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler\EditOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;

/**
 * Handles commands which edits given order return state with provided data.
 *
 * @internal
 */
#[AsCommandHandler]
final class EditOrderReturnStateHandler extends AbstractOrderReturnStateHandler implements EditOrderReturnStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(EditOrderReturnStateCommand $command)
    {
        $orderReturnStateId = $command->getOrderReturnStateId();
        $orderReturnState = new OrderReturnState($orderReturnStateId->getValue());

        $this->assertOrderReturnStateWasFound($orderReturnStateId, $orderReturnState);

        $this->updateOrderReturnStateWithCommandData($orderReturnState, $command);

        $this->assertRequiredFieldsAreNotMissing($orderReturnState);

        if (false === $orderReturnState->validateFields(false)) {
            throw new OrderReturnStateException('OrderReturnState contains invalid field values');
        }

        if (false === $orderReturnState->update()) {
            throw new OrderReturnStateException('Failed to update order return state');
        }
    }

    private function updateOrderReturnStateWithCommandData(OrderReturnState $orderReturnState, EditOrderReturnStateCommand $command)
    {
        if (null !== $command->getName()) {
            $orderReturnState->name = $command->getName();
        }

        if (null !== $command->getColor()) {
            $orderReturnState->color = $command->getColor();
        }
    }
}
