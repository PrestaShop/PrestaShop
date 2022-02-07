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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        } catch (PrestaShopException $e) {
            throw new OrderStateException(sprintf(
                'An error occurred when deleting OrderState object with id "%s".',
                $orderState->id
            ));
        }
    }
}
