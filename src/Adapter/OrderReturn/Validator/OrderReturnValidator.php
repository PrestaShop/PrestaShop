<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
