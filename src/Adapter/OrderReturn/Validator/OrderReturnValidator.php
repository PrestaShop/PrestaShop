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

namespace PrestaShop\PrestaShop\Adapter\OrderReturn\Validator;

use OrderReturn;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\OrderReturn\Exception\OrderReturnConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

class OrderReturnValidator extends AbstractObjectModelValidator
{
    /**
     * @param OrderReturn $orderReturn
     *
     * @throws OrderReturnConstraintException
     */
    public function validate(OrderReturn $orderReturn): void
    {
        $this->validateOrderReturnProperty($orderReturn, 'id_customer', OrderReturnConstraintException::INVALID_ID_CUSTOMER);
        $this->validateOrderReturnProperty($orderReturn, 'id_order', OrderReturnConstraintException::INVALID_ID_ORDER);
        $this->validateOrderReturnProperty($orderReturn, 'state', OrderReturnConstraintException::INVALID_STATE);
        $this->validateOrderReturnProperty($orderReturn, 'question', OrderReturnConstraintException::INVALID_QUESTION);
        $this->validateOrderReturnProperty($orderReturn, 'date_add', OrderReturnConstraintException::INVALID_DATE_ADD);
        $this->validateOrderReturnProperty($orderReturn, 'date_upd', OrderReturnConstraintException::INVALID_DATE_UPD);
    }

    /**
     * @param OrderReturn $orderReturn
     * @param string $propertyName
     * @param int $errorCode
     *
     * @throws CoreException
     */
    private function validateOrderReturnProperty(OrderReturn $orderReturn, string $propertyName, int $errorCode = 0): void
    {
        $this->validateObjectModelProperty(
            $orderReturn,
            $propertyName,
            ProductConstraintException::class,
            $errorCode
        );
    }
}
