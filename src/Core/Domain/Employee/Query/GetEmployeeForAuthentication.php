<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Query;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;

/**
 * Gets employee for authentication.
 */
class GetEmployeeForAuthentication
{
    /**
     * @var EmployeeId|null
     */
    private $employeeId;

    /**
     * @var string|null
     */
    private $email;

    /**
     * @var bool
     */
    private $stayLoggedIn;

    /**
     * This query cannot be constructed directly, because it can be built in two ways:
     * from employee ID or from email.
     * There are factory methods available for building this query instance.
     *
     * @see GetEmployeeForAuthentication::fromEmployeeId()
     * @see GetEmployeeForAuthentication::fromEmail()
     *
     * @param int|null $employeeId
     * @param string|null $email
     */
    private function __construct($employeeId = null, $email = null)
    {
        $this->employeeId = null !== $employeeId ? new EmployeeId($employeeId) : null;
        $this->email = $email;
    }

    /**
     * Build query instance from email.
     *
     * @param string $email
     *
     * @return GetEmployeeForAuthentication
     */
    public static function fromEmail($email)
    {
        return new self(null, $email);
    }

    /**
     * Built query instance from employee ID.
     *
     * @param int $employeeId
     *
     * @return GetEmployeeForAuthentication
     */
    public static function fromEmployeeId($employeeId)
    {
        return new self($employeeId);
    }

    /**
     * @param bool $stayLoggedIn
     *
     * @return $this
     */
    public function setStayLoggedIn($stayLoggedIn)
    {
        $this->stayLoggedIn = $stayLoggedIn;

        return $this;
    }

    /**
     * @return bool
     */
    public function getStayLoggedIn()
    {
        return $this->stayLoggedIn;
    }

    /**
     * @return EmployeeId|null
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }
}
