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
