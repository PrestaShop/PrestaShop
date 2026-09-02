<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Security;

use Access as LegacyAccess;
use PrestaShop\PrestaShop\Core\Security\AccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\EmployeePermissionProviderInterface;
use PrestaShop\PrestaShop\Core\Security\Permission;

class Access implements AccessCheckerInterface, EmployeePermissionProviderInterface
{
    public function isEmployeeGranted(string $action, int $employeeProfileId): bool
    {
        return LegacyAccess::isGranted(Permission::PREFIX_TAB . strtoupper($action), $employeeProfileId);
    }

    public function getRoles(int $employeeProfileId): array
    {
        return LegacyAccess::getRoles($employeeProfileId);
    }
}
