<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Exception;

/**
 * Class FailedToDeleteProfileException is thrown when Profile deletion fails.
 */
class FailedToDeleteProfileException extends ProfileException
{
    /**
     * Code is used when cannot delete profile because it is assigned to employee.
     */
    public const PROFILE_IS_ASSIGNED_TO_EMPLOYEE = 1;

    /**
     * Code is used when unexpected error (e.g. lost db connection) occures while deleting profile.
     */
    public const UNEXPECTED_ERROR = 2;

    /**
     * Code is used when logged in employee attempts to delete its own profile.
     */
    public const PROFILE_IS_ASSIGNED_TO_CONTEXT_EMPLOYEE = 3;
}
