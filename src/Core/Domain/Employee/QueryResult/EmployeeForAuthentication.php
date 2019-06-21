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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Stores data of an employee for authentication.
 */
class EmployeeForAuthentication
{
    /**
     * @var string
     */
    private $defaultPageUrl;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var string
     */
    private $hashedPassword;

    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * @var int
     */
    private $profileId;

    /**
     * @var array
     */
    private $roles;

    /**
     * @var bool
     */
    private $stayLoggedIn;

    /**
     * @param EmployeeId $employeeId
     * @param Email $email
     * @param string $hashedPassword
     * @param string $defaultPageUrl
     * @param int $profileId
     * @param array $roles
     * @param bool $stayLoggedIn
     */
    public function __construct(
        EmployeeId $employeeId,
        Email $email,
        $hashedPassword,
        $defaultPageUrl,
        $profileId,
        array $roles,
        $stayLoggedIn = true
    ) {
        $this->employeeId = $employeeId;
        $this->defaultPageUrl = $defaultPageUrl;
        $this->email = $email;
        $this->hashedPassword = $hashedPassword;
        $this->profileId = $profileId;
        $this->roles = $roles;
        $this->stayLoggedIn = $stayLoggedIn;
    }

    /**
     * @return string
     */
    public function getDefaultPageUrl()
    {
        return $this->defaultPageUrl;
    }

    /**
     * @return Email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getHashedPassword()
    {
        return $this->hashedPassword;
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @return int
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @return bool
     */
    public function getStayLoggedIn()
    {
        return $this->stayLoggedIn;
    }
}
