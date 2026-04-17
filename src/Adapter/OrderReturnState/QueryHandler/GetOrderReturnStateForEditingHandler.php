<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\QueryHandler;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Query\GetOrderReturnStateForEditing;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\QueryHandler\GetOrderReturnStateForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\QueryResult\EditableOrderReturnState;

/**
 * Handles command that gets orderReturnState for editing
 *
 * @internal
 */
#[AsQueryHandler]
final class GetOrderReturnStateForEditingHandler implements GetOrderReturnStateForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderReturnStateForEditing $query)
    {
        $orderReturnStateId = $query->getOrderReturnStateId();
        $orderReturnState = new OrderReturnState($orderReturnStateId->getValue());

        if ($orderReturnState->id !== $orderReturnStateId->getValue()) {
            throw new OrderReturnStateNotFoundException($orderReturnStateId, sprintf('OrderReturnState with id "%s" was not found', $orderReturnStateId->getValue()));
        }

        return new EditableOrderReturnState(
            $orderReturnStateId,
            $orderReturnState->name,
            $orderReturnState->color
        );
    }
}
