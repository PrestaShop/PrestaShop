<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CustomerService\Command;

use PrestaShop\PrestaShop\Core\Domain\CustomerService\ValueObject\CustomerThreadId;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Forwards customer thread
 */
class ForwardCustomerThreadCommand
{
    /**
     * @var EmployeeId|null
     */
    private $employeeId;

    /**
     * @var CustomerThreadId
     */
    private $customerThreadId;

    /**
     * @var Email|null
     */
    private $email;

    /**
     * @var string
     */
    private $comment;

    /**
     * Creates command for forwarding customer thread for another employee
     *
     * @param int $customerThreadId
     * @param int $employeeId
     * @param string $comment
     *
     * @return self
     */
    public static function toAnotherEmployee($customerThreadId, $employeeId, $comment)
    {
        $command = new self();
        $command->employeeId = new EmployeeId($employeeId);
        $command->customerThreadId = new CustomerThreadId($customerThreadId);
        $command->comment = $comment;

        return $command;
    }

    /**
     * Creates command for forwarding customer thread for someone else (not employee)
     *
     * @param int $customerThreadId
     * @param string $email
     * @param string $comment
     *
     * @return ForwardCustomerThreadCommand
     */
    public static function toSomeoneElse($customerThreadId, $email, $comment)
    {
        $command = new self();
        $command->email = new Email($email);
        $command->customerThreadId = new CustomerThreadId($customerThreadId);
        $command->comment = $comment;

        return $command;
    }

    /**
     * Command should be created using static factories
     */
    private function __construct()
    {
    }

    /**
     * @return EmployeeId|null
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @return CustomerThreadId
     */
    public function getCustomerThreadId()
    {
        return $this->customerThreadId;
    }

    /**
     * @return Email|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @return bool
     */
    public function forwardToEmployee()
    {
        return null !== $this->employeeId;
    }
}
