<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\CommandHandler;

use Configuration;
use Language;
use OrderReturnState;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\DuplicateOrderReturnStateNameException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\MissingOrderReturnStateRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\Name;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;
use PrestaShopException;

/**
 * Provides reusable methods for order return state command handlers.
 *
 * @internal
 */
abstract class AbstractOrderReturnStateHandler
{
    /**
     * @throws OrderReturnStateNotFoundException
     */
    protected function assertOrderReturnStateWasFound(OrderReturnStateId $orderReturnStateId, OrderReturnState $orderReturnState)
    {
        if ($orderReturnState->id !== $orderReturnStateId->getValue()) {
            throw new OrderReturnStateNotFoundException($orderReturnStateId, sprintf('OrderReturnState with id "%s" was not found.', $orderReturnStateId->getValue()));
        }
    }

    /**
     * @throws MissingOrderReturnStateRequiredFieldsException
     */
    protected function assertRequiredFieldsAreNotMissing(OrderReturnState $orderReturnState)
    {
        $errors = $orderReturnState->validateFieldsRequiredDatabase();

        if (!empty($errors)) {
            $missingFields = array_keys($errors);

            throw new MissingOrderReturnStateRequiredFieldsException($missingFields, sprintf('One or more required fields for order return state are missing. Missing fields are: %s', implode(',', $missingFields)));
        }
    }

    /**
     * @param OrderReturnStateId $orderReturnStateId
     *
     * @return OrderReturnState
     *
     * @throws OrderReturnStateException
     * @throws OrderReturnStateNotFoundException
     */
    /**
     * Asserts that no other order return state already uses one of the given localized names.
     *
     * WHY: see AbstractOrderStateHandler::assertNameIsNotDuplicate() - every language is checked and
     * not only the default one, because ObjectModel::formatFields() falls back to the default language
     * value when a translation is left empty on a required field.
     *
     * @param int|null $excludeOrderReturnStateId order return state being edited, excluded from the search
     *
     * @throws DuplicateOrderReturnStateNameException
     */
    protected function assertNameIsNotDuplicate(OrderReturnState $orderReturnState, ?int $excludeOrderReturnStateId = null): void
    {
        $localizedNames = is_array($orderReturnState->name) ? $orderReturnState->name : [];
        $defaultLangId = (int) Configuration::get('PS_LANG_DEFAULT');

        foreach (Language::getIDs(false) as $langId) {
            $langId = (int) $langId;
            $name = (string) ($localizedNames[$langId] ?? '');
            if ('' === $name) {
                $name = (string) ($localizedNames[$defaultLangId] ?? '');
            }

            if ('' === $name) {
                continue;
            }

            if (OrderReturnState::existsLocalizedNameInDatabase($name, $langId, $excludeOrderReturnStateId)) {
                throw new DuplicateOrderReturnStateNameException(new Name($name), sprintf('An order return state named "%s" already exists.', $name));
            }
        }
    }

    protected function getOrderReturnState(OrderReturnStateId $orderReturnStateId): OrderReturnState
    {
        try {
            $OrderReturnState = new OrderReturnState($orderReturnStateId->getValue());
        } catch (PrestaShopException $e) {
            throw new OrderReturnStateException('Failed to create new order return state', 0, $e);
        }

        if ($OrderReturnState->id !== $orderReturnStateId->getValue()) {
            throw new OrderReturnStateNotFoundException($orderReturnStateId);
        }

        return $OrderReturnState;
    }

    /**
     * Deletes legacy Address
     *
     * @param OrderReturnState $orderReturnState
     *
     * @return bool
     *
     * @throws OrderReturnStateException
     */
    protected function deleteOrderReturnState(OrderReturnState $orderReturnState): bool
    {
        try {
            return (bool) $orderReturnState->delete();
        } catch (PrestaShopException) {
            throw new OrderReturnStateException(sprintf(
                'An error occurred when deleting OrderReturnState object with id "%s".',
                $orderReturnState->id
            ));
        }
    }
}
