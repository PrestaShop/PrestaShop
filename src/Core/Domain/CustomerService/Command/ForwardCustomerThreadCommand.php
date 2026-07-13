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
        return new self((int) $customerThreadId, (string) $comment, (int) $employeeId);
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
        return new self((int) $customerThreadId, (string) $comment, null, (string) $email);
    }

    /**
     * @param int         $customerThreadId
     * @param string      $comment
     * @param int|null    $employeeId Forward to another employee. Mutually exclusive with $email.
     * @param string|null $email      Forward to someone else by email. Mutually exclusive with $employeeId.
     */
    public function __construct(
        int $customerThreadId = 0,
        string $comment = '',
        ?int $employeeId = null,
        ?string $email = null
    ) {
        if ($customerThreadId > 0) {
            $this->customerThreadId = new CustomerThreadId($customerThreadId);
            $this->comment = $comment;
            if (null !== $employeeId) {
                $this->employeeId = new EmployeeId($employeeId);
            }
            if (null !== $email) {
                $this->email = new Email($email);
            }
        }
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
