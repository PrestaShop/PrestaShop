<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderReturn;

use OrderReturn;
use OrderReturnState;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\MissingOrderReturnRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnOrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;
use PrestaShopException;

/**
 * Provides reusable methods for order return command/query handlers
 */
abstract class AbstractOrderReturnHandler extends AbstractObjectModelHandler
{
    /**
     * Gets legacy OrderReturn
     *
     * @param OrderReturnId $orderReturnId
     *
     * @return OrderReturn
     *
     * @throws OrderReturnException
     */
    protected function getOrderReturn(OrderReturnId $orderReturnId): OrderReturn
    {
        try {
            $orderReturn = new OrderReturn($orderReturnId->getValue());
        } catch (PrestaShopException $e) {
            throw new OrderReturnException('Failed to create new order return', 0, $e);
        }

        if ($orderReturn->id !== $orderReturnId->getValue()) {
            throw new OrderReturnNotFoundException($orderReturnId, sprintf('Merchandise return with id "%d" was not found.', $orderReturnId->getValue()));
        }

        return $orderReturn;
    }

    /**
     * Gets legacy OrderReturnState
     *
     * @param OrderReturnStateId $orderReturnStateId
     *
     * @return OrderReturnState
     *
     * @throws OrderReturnException
     * @throws OrderReturnOrderStateConstraintException
     */
    protected function getOrderReturnState(OrderReturnStateId $orderReturnStateId): OrderReturnState
    {
        try {
            $orderReturnState = new OrderReturnState($orderReturnStateId->getValue());
        } catch (PrestaShopException $e) {
            throw new OrderReturnException('Failed to create new order return state', 0, $e);
        }

        if ($orderReturnState->id !== $orderReturnStateId->getValue()) {
            throw new OrderReturnOrderStateConstraintException(
                $orderReturnStateId,
                sprintf('Merchandise return state with id "%d" was not found.', $orderReturnStateId->getValue())
            );
        }

        return $orderReturnState;
    }

    /**
     * @param OrderReturn $orderReturn
     *
     * @throws MissingOrderReturnRequiredFieldsException
     * @throws PrestaShopException
     */
    protected function assertRequiredFieldsAreNotMissing(OrderReturn $orderReturn): void
    {
        $errors = $orderReturn->validateFieldsRequiredDatabase();

        if (!empty($errors)) {
            $missingFields = array_keys($errors);

            throw new MissingOrderReturnRequiredFieldsException($missingFields, sprintf('One or more required fields for order return are missing. Missing fields are: %s', implode(',', $missingFields)));
        }
    }
}
