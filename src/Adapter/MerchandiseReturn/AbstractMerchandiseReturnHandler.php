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

namespace PrestaShop\PrestaShop\Adapter\MerchandiseReturn;

use OrderReturn;
use OrderReturnState;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\DeleteMerchandiseReturnProductException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnOrderStateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MissingMerchandiseReturnRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnDetailId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnStateId;
use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\ValueObject\OrderReturnStateId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShopException;

/**
 * Provides reusable methods for merchandise return command/query handlers
 */
abstract class AbstractMerchandiseReturnHandler extends AbstractObjectModelHandler
{
    /**
     * Gets legacy OrderReturn
     *
     * @param MerchandiseReturnId $merchandiseReturnId
     *
     * @return OrderReturn
     *
     * @throws MerchandiseReturnException
     */
    protected function getOrderReturn(MerchandiseReturnId $merchandiseReturnId): OrderReturn
    {
        try {
            $orderReturn = new OrderReturn($merchandiseReturnId->getValue());
        } catch (PrestaShopException $e) {
            throw new MerchandiseReturnException('Failed to create new order return', 0, $e);
        }

        if ($orderReturn->id !== $merchandiseReturnId->getValue()) {
            throw new MerchandiseReturnNotFoundException($merchandiseReturnId, sprintf('Merchandise return with id "%s" was not found.', $merchandiseReturnId->getValue()));
        }

        return $orderReturn;
    }

    /**
     * Gets legacy OrderReturn
     *
     * @param OrderReturnStateId $orderReturnStateId
     * @return OrderReturn
     *
     * @throws MerchandiseReturnException
     * @throws MerchandiseReturnOrderStateConstraintException
     */
    protected function getOrderReturnState(MerchandiseReturnStateId $orderReturnStateId): OrderReturnState
    {
        try {
            $orderReturnState = new OrderReturnState($orderReturnStateId->getValue());
        } catch (PrestaShopException $e) {
            throw new MerchandiseReturnException('Failed to create new order return state', 0, $e);
        }

        if ($orderReturnState->id !== $orderReturnStateId->getValue()) {
            throw new MerchandiseReturnOrderStateConstraintException(
                $orderReturnStateId,
                sprintf('Merchandise return state with id "%s" was not found.', $orderReturnStateId->getValue())
            );
        }

        return $orderReturnState;
    }

    /**
     * @param MerchandiseReturnId $merchandiseReturnId
     * @param MerchandiseReturnDetailId $merchandiseReturnDetailId
     * @param CustomizationId|null $customizationId
     *
     * @throws DeleteMerchandiseReturnProductException
     * @throws MerchandiseReturnNotFoundException
     * @throws PrestaShopException
     * @throws \PrestaShopDatabaseException
     */
    protected function deleteMerchandiseReturnProduct(
        MerchandiseReturnId $merchandiseReturnId,
        MerchandiseReturnDetailId $merchandiseReturnDetailId,
        ?CustomizationId $customizationId
    ): void {
        $orderReturn = $this->getOrderReturn($merchandiseReturnId);

        if ((int) ($orderReturn->countProduct()) <= 1) {
            throw new DeleteMerchandiseReturnProductException('Can\'t delete last product from merchandise return');
        }

        if ($customizationId !== null) {
            $this->deleteOrderReturnDetailWithCustomization(
                $merchandiseReturnId,
                $merchandiseReturnDetailId,
                $customizationId
            );
        } else {
            $this->deleteOrderReturnDetail(
                $merchandiseReturnId,
                $merchandiseReturnDetailId
            );
        }
    }

    /**
     * @param MerchandiseReturnId $merchandiseReturnId
     * @param MerchandiseReturnDetailId $merchandiseReturnDetailId
     * @param CustomizationId $customizationId
     *
     * @throws DeleteMerchandiseReturnProductException
     */
    private function deleteOrderReturnDetailWithCustomization(
        MerchandiseReturnId $merchandiseReturnId,
        MerchandiseReturnDetailId $merchandiseReturnDetailId,
        CustomizationId $customizationId
    ): void {
        if (!OrderReturn::deleteOrderReturnDetail(
            $merchandiseReturnId->getValue(),
            $merchandiseReturnDetailId->getValue(),
            $customizationId->getValue()
        )) {
            throw new DeleteMerchandiseReturnProductException('Failed to delete merchandise return detail');
        }
    }

    /**
     * @param MerchandiseReturnId $merchandiseReturnId
     * @param MerchandiseReturnDetailId $merchandiseReturnDetailId
     *
     * @throws DeleteMerchandiseReturnProductException
     */
    private function deleteOrderReturnDetail(
        MerchandiseReturnId $merchandiseReturnId,
        MerchandiseReturnDetailId $merchandiseReturnDetailId
    ): void {
        if (!OrderReturn::deleteOrderReturnDetail(
            $merchandiseReturnId->getValue(),
            $merchandiseReturnDetailId->getValue()
        )) {
            throw new DeleteMerchandiseReturnProductException('Failed to delete merchandise return detail');
        }
    }

    /**
     * @param OrderReturn $orderReturn
     *
     * @throws MissingMerchandiseReturnRequiredFieldsException
     * @throws PrestaShopException
     */
    protected function assertRequiredFieldsAreNotMissing(OrderReturn $orderReturn): void
    {
        $errors = $orderReturn->validateFieldsRequiredDatabase();

        if (!empty($errors)) {
            $missingFields = array_keys($errors);

            throw new MissingMerchandiseReturnRequiredFieldsException($missingFields, sprintf('One or more required fields for order return are missing. Missing fields are: %s', implode(',', $missingFields)));
        }
    }
}
