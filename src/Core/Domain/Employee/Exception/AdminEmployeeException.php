<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Exception;

/**
 * Class AdminEmployeeException is thrown when last admin of the shop is being disabled or deleted.
 */
class AdminEmployeeException extends EmployeeException
{
    /**
     * Code is used when the only admin of the shop is being disabled or deleted.
     */
    public const CANNOT_CHANGE_LAST_ADMIN = 1;

    /**
     * Code is used when an employee who is not a super admin (or no employee: Admin API, CLI) changes a super admin.
     */
    public const CANNOT_MANAGE_SUPER_ADMIN = 2;
}
