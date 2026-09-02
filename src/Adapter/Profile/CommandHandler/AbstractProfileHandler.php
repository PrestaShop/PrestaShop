<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\CommandHandler;

use Employee;
use PrestaShop\PrestaShop\Core\Domain\Profile\Exception\FailedToDeleteProfileException;
use Profile;

/**
 * @internal
 */
abstract class AbstractProfileHandler
{
    /**
     * Checks if given profile is not assigned to any employee.
     *
     * @param Profile $profile
     *
     * @throws FailedToDeleteProfileException
     */
    protected function assertProfileIsNotAssignedToEmployee(Profile $profile)
    {
        $profileEmployees = Employee::getEmployeesByProfile($profile->id);

        if (!empty($profileEmployees)) {
            throw new FailedToDeleteProfileException(sprintf('Failed to delete profile with id "%d", because it is assigned to employee.', $profile->id), FailedToDeleteProfileException::PROFILE_IS_ASSIGNED_TO_EMPLOYEE);
        }
    }
}
