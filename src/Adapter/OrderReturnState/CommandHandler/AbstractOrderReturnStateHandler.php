<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturnState\CommandHandler;

use OrderReturnState;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\MissingOrderReturnStateRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateNotFoundException;
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
        } catch (PrestaShopException $e) {
            throw new OrderReturnStateException(sprintf(
                'An error occurred when deleting OrderReturnState object with id "%s".',
                $orderReturnState->id
            ));
        }
    }
}
