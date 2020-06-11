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
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\DeleteMerchandiseReturnDetailException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MerchandiseReturnNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\Exception\MissingMerchandiseReturnRequiredFieldsException;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\CustomizationId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnDetailId;
use PrestaShop\PrestaShop\Core\Domain\MerchandiseReturn\ValueObject\MerchandiseReturnId;
use PrestaShopException;

/**
 * Provides reusable methods for merchandise return command/query handlers
 */
abstract class AbstractMerchandiseReturnHandler extends AbstractObjectModelHandler
{
    /**
     * @param MerchandiseReturnId $merchandiseReturnId
     * @param OrderReturn $orderReturn
     *
     * @throws MerchandiseReturnNotFoundException
     */
    protected function assertOrderReturnWasFound(MerchandiseReturnId $merchandiseReturnId, OrderReturn $orderReturn): void
    {
        if ($orderReturn->id !== $merchandiseReturnId->getValue()) {
            throw new MerchandiseReturnNotFoundException($merchandiseReturnId, sprintf('Order return with id "%s" was not found.', $merchandiseReturnId->getValue()));
        }
    }

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

    protected function deleteMerchandiseReturnDetail(
        MerchandiseReturnId $merchandiseReturnId,
        MerchandiseReturnDetailId $merchandiseReturnDetailId,
        ?CustomizationId $customizationId
    ) {
        $orderReturn = new OrderReturn($merchandiseReturnId->getValue());
        $this->assertOrderReturnWasFound($merchandiseReturnId, $orderReturn);

        /**
         * @todo I am not sure if this is the right solution, but one alternative was do do an if
         * If I would not validate customizationId inside valueObject and simply let 0 pass from gridDefinitionFactory
         * it would also work. However this way when using this command to delete order detail I would need to pass 0 if there no customization
         * which is not exactly good practice right? I guess I could also set $customizationId as 0 by default, in that case I couldn't use ValueObject.
         */

        /**
         * Sets customizationIdValue as 0 as this is default value passed in deleteOrderReturnDetail for id customization
         * if there is no customization we don't want to pass anything there
         *
         * @var int
         */
        $customizationIdValue = 0;
        if ($customizationId !== null) {
            $customizationIdValue = $customizationId->getValue();
        }
        if ((int) ($orderReturn->countProduct()) <= 1) {
            throw new DeleteMerchandiseReturnDetailException('Can\'t delete last product from merchandise return');
        }

        if (!OrderReturn::deleteOrderReturnDetail(
            $merchandiseReturnId->getValue(),
            $merchandiseReturnDetailId->getValue(),
            $customizationIdValue
        )) {
            throw new DeleteMerchandiseReturnDetailException('Failed to delete merchandise return detail');
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
