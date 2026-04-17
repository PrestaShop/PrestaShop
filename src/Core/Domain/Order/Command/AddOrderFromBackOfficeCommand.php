<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Order\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\NoEmployeeId;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use PrestaShopBundle\Exception\InvalidModuleException;

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
     * @var EmployeeIdInterface
     */
    private $employeeId;

    /**
     * @param int $cartId
     * @param int $employeeId
     * @param string $orderMessage
     * @param string $paymentModuleName
     * @param int $orderStateId
     */
    public function __construct($cartId, $employeeId, $orderMessage, $paymentModuleName, $orderStateId)
    {
        $this->assertIsModuleName($paymentModuleName);
        $this->assertOrderStateIsPositiveInt($orderStateId);

        $this->cartId = new CartId($cartId);
        $this->employeeId = $employeeId === NoEmployeeId::NO_EMPLOYEE_ID_VALUE ? new NoEmployeeId() : new EmployeeId($employeeId);
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
     * @return EmployeeIdInterface
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @param string $moduleName
     *
     * @throws InvalidModuleException
     */
    private function assertIsModuleName($moduleName)
    {
        if (!is_string($moduleName) || !preg_match('/^[a-zA-Z0-9_-]+$/', $moduleName)) {
            throw new InvalidModuleException();
        }
    }

    /**
     * @param int $orderStateId
     *
     * @throws InvalidOrderStateException
     */
    private function assertOrderStateIsPositiveInt($orderStateId)
    {
        if (!is_int($orderStateId) || 0 >= $orderStateId) {
            throw new InvalidOrderStateException(
                InvalidOrderStateException::INVALID_ID,
                'Invalid order state id'
            );
        }
    }
}
