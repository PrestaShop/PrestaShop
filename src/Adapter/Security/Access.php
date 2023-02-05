<?php

namespace PrestaShop\PrestaShop\Adapter\Security;

use Access as LegacyAccess;
use PrestaShop\PrestaShop\Core\Security\AccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\EmployeePermissionProviderInterface;
use PrestaShop\PrestaShop\Core\Security\Permission;

class Access implements AccessCheckerInterface, EmployeePermissionProviderInterface
{
    public function isEmployeeGranted(string $action, int $employeeProfileId): bool
    {
        return LegacyAccess::isGranted(Permission::PREFIX . strtoupper($action), $employeeProfileId);
    }

    public function getRoles(int $employeeProfileId): array
    {
        return LegacyAccess::getRoles($employeeProfileId);
    }
}
