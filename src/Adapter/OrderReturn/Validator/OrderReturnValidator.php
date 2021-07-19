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

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\Validator;

use OrderReturn;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

class OrderReturnValidator extends AbstractObjectModelValidator
{
    /**
     * @param OrderReturn $orderReturn
     *
     * @throws OrderReturnConstraintException
     */
    public function validateUpdate(OrderReturn $orderReturn): void
    {
        $this->validateOrderReturnProperty($orderReturn, 'id_customer', OrderReturnConstraintException::INVALID_ID_CUSTOMER);
        $this->validateOrderReturnProperty($orderReturn, 'id_order', OrderReturnConstraintException::INVALID_ID_ORDER);
        $this->validateOrderReturnProperty($orderReturn, 'state', OrderReturnConstraintException::INVALID_STATE);
        $this->validateOrderReturnProperty($orderReturn, 'question', OrderReturnConstraintException::INVALID_QUESTION);
        $this->validateOrderReturnProperty($orderReturn, 'date_add', OrderReturnConstraintException::INVALID_DATE_ADD);
        $this->validateOrderReturnProperty($orderReturn, 'date_upd', OrderReturnConstraintException::INVALID_DATE_UPD);
    }

    /**
     * @param OrderReturn $product
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws OrderReturnConstraintException
     */
    private function validateOrderReturnProperty(OrderReturn $product, string $propertyName, int $errorCode = 0): void
    {
        $this->validateObjectModelProperty(
            $product,
            $propertyName,
            ProductConstraintException::class,
            $errorCode
        );
    }
}
