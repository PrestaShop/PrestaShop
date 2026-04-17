<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\CommandHandler;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command\AddOrderReturnStateCommand;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\CommandHandler\AddOrderReturnStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;

/**
 * Handles command that adds new order return state
 *
 * @internal
 */
#[AsCommandHandler]
final class AddOrderReturnStateHandler extends AbstractOrderReturnStateHandler implements AddOrderReturnStateHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddOrderReturnStateCommand $command)
    {
        $orderReturnState = new OrderReturnState();

        $this->fillOrderReturnStateWithCommandData($orderReturnState, $command);
        $this->assertRequiredFieldsAreNotMissing($orderReturnState);

        if (false === $orderReturnState->validateFields(false)) {
            throw new OrderReturnStateException('Order status contains invalid field values');
        }

        $orderReturnState->add();

        return new OrderReturnStateId((int) $orderReturnState->id);
    }

    private function fillOrderReturnStateWithCommandData(OrderReturnState $orderReturnState, AddOrderReturnStateCommand $command)
    {
        $orderReturnState->name = $command->getLocalizedNames();
        $orderReturnState->color = $command->getColor();
    }
}
