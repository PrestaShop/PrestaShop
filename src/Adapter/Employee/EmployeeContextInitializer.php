<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Employee;

use Context;
use Employee;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Loads an existing super-administrator into the legacy context.
 *
 * Used by CLI entry points that need to satisfy the ProfileAccessChecker
 * before dispatching CQRS employee commands (the checker requires the
 * context employee to be a super-admin in order to act on super-admin
 * profiles).
 */
class EmployeeContextInitializer
{
    public function __construct(
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    /**
     * @return int|null The impersonated employee ID, or null if no super-admin exists
     */
    public function initializeWithFirstSuperAdmin(): ?int
    {
        $superAdminProfileId = (int) $this->configuration->get('_PS_ADMIN_PROFILE_');
        $superAdmins = Employee::getEmployeesByProfile($superAdminProfileId, true);

        if (empty($superAdmins)) {
            return null;
        }

        $employeeId = (int) $superAdmins[0]['id_employee'];
        Context::getContext()->employee = new Employee($employeeId);

        return $employeeId;
    }
}
