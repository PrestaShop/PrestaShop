<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderState\CommandHandler;

use Configuration;
use Language;
use OrderState;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\DuplicateOrderStateNameException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\MissingOrderStateRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\Exception\OrderStateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\Name;
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
     * Asserts that no other order state already uses one of the given localized names.
     *
     * WHY: a name is checked for every language and not only the default one, because
     * ObjectModel::formatFields() falls back to the default language value when a translation is left
     * empty on a required field, so the row that ends up in the database is not always the one the
     * merchant typed. Checking the value that will actually be stored is what makes the rule hold.
     *
     * @param int|null $excludeOrderStateId order state being edited, excluded from the search
     *
     * @throws DuplicateOrderStateNameException
     */
    protected function assertNameIsNotDuplicate(OrderState $orderState, ?int $excludeOrderStateId = null): void
    {
        $localizedNames = is_array($orderState->name) ? $orderState->name : [];
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

            if (OrderState::existsLocalizedNameInDatabase($name, $langId, $excludeOrderStateId)) {
                throw new DuplicateOrderStateNameException(new Name($name), sprintf('An order state named "%s" already exists.', $name));
            }
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
