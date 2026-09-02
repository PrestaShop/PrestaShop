<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Employee\Access;

/**
 * Interface ProfileAccessCheckerInterface describes a profile access checker.
 */
interface ProfileAccessCheckerInterface
{
    /**
     * Checks if given employee can access given profile.
     *
     * @param int $employeeId
     * @param int $profileId
     *
     * @return bool
     */
    public function canEmployeeAccessProfile($employeeId, $profileId);
}
