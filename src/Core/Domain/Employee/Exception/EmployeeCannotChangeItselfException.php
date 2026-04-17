<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\Exception;

/**
 * Class EmployeeCannotChangeItselfException is thrown when employee is trying to change status or delete itself.
 */
class EmployeeCannotChangeItselfException extends EmployeeException
{
    /**
     * Code is used when employee which is logged in tries to change its status.
     */
    public const CANNOT_CHANGE_STATUS = 1;
}
