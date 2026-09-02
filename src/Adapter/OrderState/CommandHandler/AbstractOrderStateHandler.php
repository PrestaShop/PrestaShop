<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderState\CommandHandler;

use OrderState;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\MissingOrderStateRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use PrestaShopException;

/**
 * Provides reusable methods for order state command handlers.
 *
 * @internal
 */
abstract class AbstractOrderStateHandler
{
    /**
     * @throws OrderStateNotFoundException
     */
    protected function assertOrderStateWasFound(OrderStateId $orderStateId, OrderState $orderState)
    {
        if ($orderState->id !== $orderStateId->getValue()) {
            throw new OrderStateNotFoundException($orderStateId, sprintf('OrderState with id "%s" was not found.', $orderStateId->getValue()));
        }
    }

    /**
     * @throws MissingOrderStateRequiredFieldsException
     */
    protected function assertRequiredFieldsAreNotMissing(OrderState $orderState)
    {
        $errors = $orderState->validateFieldsRequiredDatabase();

        if (!empty($errors)) {
            $missingFields = array_keys($errors);

            throw new MissingOrderStateRequiredFieldsException($missingFields, sprintf('One or more required fields for order state are missing. Missing fields are: %s', implode(',', $missingFields)));
        }
    }

    /**
     * @param OrderStateId $orderStateId
     *
     * @return OrderState
     *
     * @throws OrderStateException
     * @throws OrderStateNotFoundException
     */
    protected function getOrderState(OrderStateId $orderStateId): OrderState
    {
        try {
            $orderState = new OrderState($orderStateId->getValue());
        } catch (PrestaShopException $e) {
            throw new OrderStateException('Failed to create new order state', 0, $e);
        }

        if ($orderState->id !== $orderStateId->getValue()) {
            throw new OrderStateNotFoundException($orderStateId);
        }

        return $orderState;
    }

    /**
     * Deletes legacy Address
     *
     * @param OrderState $orderState
     *
     * @return bool
     *
     * @throws OrderStateException
     */
    protected function deleteOrderState(OrderState $orderState): bool
    {
        try {
            $orderState->deleted = true;

            return (bool) $orderState->update();
        } catch (PrestaShopException) {
            throw new OrderStateException(sprintf(
                'An error occurred when deleting OrderState object with id "%s".',
                $orderState->id
            ));
        }
    }
}
