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

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Command;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\ResetPasswordInformationMissingException;
use PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject\EmployeeId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

/**
 * Reset employee's password
 */
class ResetPasswordCommand
{
    /**
     * @var EmployeeId
     */
    private $employeeId;

    /**
     * @var string
     */
    private $resetToken;

    /**
     * @var Email
     */
    private $email;

    /**
     * @var string
     */
    private $newPlainPassword;

    /**
     * @param int $employeeId
     * @param string $email
     * @param string $resetToken
     * @param string $newPlainPassword
     */
    public function __construct($employeeId, $email, $resetToken, $newPlainPassword)
    {
        if (empty($resetToken)) {
            throw new ResetPasswordInformationMissingException('Reset token cannot be empty.');
        }

        if (empty(trim($email))) {
            throw new ResetPasswordInformationMissingException('Employee email cannot be empty.');
        }

        $this->employeeId = new EmployeeId($employeeId);
        $this->resetToken = $resetToken;
        $this->email = new Email($email);
        $this->newPlainPassword = $newPlainPassword;
    }

    /**
     * @return EmployeeId
     */
    public function getEmployeeId()
    {
        return $this->employeeId;
    }

    /**
     * @return string
     */
    public function getResetToken()
    {
        return $this->resetToken;
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
    public function getNewPlainPassword()
    {
        return $this->newPlainPassword;
    }
}
