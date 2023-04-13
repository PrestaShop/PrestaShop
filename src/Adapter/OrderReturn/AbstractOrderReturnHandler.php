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

namespace PrestaShop\PrestaShop\Adapter\OrderReturn;

use OrderReturn;
use OrderReturnState;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\DeleteOrderReturnProductException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\MissingOrderReturnRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnOrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnDetailId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\ValueObject\OrderReturnId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;
use PrestaShopException;

/**
 * Provides reusable methods for order return command/query handlers
 */
abstract class AbstractOrderReturnHandler extends AbstractObjectModelHandler
{
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
                sprintf('Merchandise return state with id "%s" was not found.', $orderReturnStateId->getValue())
            );
        }

        return $orderReturnState;
    }

    /**
     * @param OrderReturnId $orderReturnId
     * @param OrderReturnDetailId $orderReturnDetailId
     *
     * @throws DeleteOrderReturnProductException
     * @throws OrderReturnException
     */
    protected function deleteOrderReturnProduct(
        OrderReturnId $orderReturnId,
        OrderReturnDetailId $orderReturnDetailId
    ): void {
        $orderReturn = $this->getOrderReturn($orderReturnId);

        if ((int) ($orderReturn->countProduct()) <= 1) {
            throw new DeleteOrderReturnProductException(
                'Can\'t delete last product from merchandise return',
                DeleteOrderReturnProductException::LAST_ORDER_RETURN_PRODUCT
            );
        }

        $this->deleteOrderReturnDetail(
            $orderReturnDetailId
        );
    }

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
            throw new OrderReturnNotFoundException($orderReturnId, sprintf('Merchandise return with id "%s" was not found.', $orderReturnId->getValue()));
        }

        return $orderReturn;
    }

    /**
     * @param OrderReturnDetailId $orderReturnDetailId
     *
     * @throws DeleteOrderReturnProductException
     */
    private function deleteOrderReturnDetail(
        OrderReturnDetailId $orderReturnDetailId
    ): void {
        $orderReturnDetail = OrderReturn::getOrderReturnDetailByOrderDetailId($orderReturnDetailId->getValue());
        if (!$orderReturnDetail) {
            throw new DeleteOrderReturnProductException(
                'Couldn\'t find merchandise return detail',
                DeleteOrderReturnProductException::ORDER_RETURN_PRODUCT_NOT_FOUND
            );
        }

        $orderReturnId = $orderReturnDetail['id_order_return'];
        $customizationId = $orderReturnDetail['id_customization'];

        if (!OrderReturn::deleteOrderReturnDetail(
            $orderReturnId,
            $orderReturnDetailId->getValue(),
            $customizationId
        )) {
            throw new DeleteOrderReturnProductException(
                'Failed to delete merchandise return detail',
                DeleteOrderReturnProductException::UNEXPECTED_ERROR
            );
        }
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
