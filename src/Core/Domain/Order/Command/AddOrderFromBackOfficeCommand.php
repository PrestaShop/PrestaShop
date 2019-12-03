<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\InvalidEmployeeIdException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;

/**
 * Adds new order from given cart.
 */
class AddOrderFromBackOfficeCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var string
     */
    private $orderMessage;

    /**
     * @var string
     */
    private $paymentModuleName;

    /**
     * @var int
     */
    private $orderStateId;

    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * @param int $cartId
     * @param int $employeeId
     * @param string $orderMessage
     * @param string $paymentModuleName
     * @param int $orderStateId
     *
     * @throws OrderException
     * @throws CartConstraintException
     * @throws InvalidEmployeeIdException
     */
    public function __construct($cartId, $employeeId, $orderMessage, $paymentModuleName, $orderStateId)
    {
        $this->assertIsModuleName($paymentModuleName);
        $this->assertOrderStateIsPositiveInt($orderStateId);

        $this->cartId = new CartId($cartId);
        $this->employeeId = new EmployeeId($employeeId);
        $this->orderMessage = $orderMessage;
        $this->paymentModuleName = $paymentModuleName;
        $this->orderStateId = $orderStateId;
    }

    /**
     * @return CartId
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return string
     */
    public function getOrderMessage()
    {
        return $this->orderMessage;
    }

    /**
     * @return string
     */
    public function getPaymentModuleName()
    {
        return $this->paymentModuleName;
    }

    /**
     * @return int
     */
    public function getOrderStateId()
    {
        return $this->orderStateId;
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param string $moduleName
     *
     * @throws OrderException
     */
    private function assertIsModuleName($moduleName)
    {
        if (!is_string($moduleName) || !preg_match('/^[a-zA-Z0-9_-]+$/', $moduleName)) {
            throw new OrderException('Payment module name is invalid');
        }
    }

    /**
     * @param int $orderStateId
     */
    private function assertOrderStateIsPositiveInt($orderStateId)
    {
        if (!is_int($orderStateId) || 0 >= $orderStateId) {
            throw new OrderException('Invalid order state id');
        }
    }
}
