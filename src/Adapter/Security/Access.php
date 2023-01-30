<?php

namespace PrestaShop\PrestaShop\Adapter\Security;

use Access as LegacyAccess;
use PrestaShop\PrestaShop\Core\Security\AccessCheckerInterface;
use PrestaShop\PrestaShop\Core\Security\EmployeePermissionProviderInterface;

class Access implements AccessCheckerInterface, EmployeePermissionProviderInterface
{
    public function isEmployeeGranted(string $action, int $employeeProfileId): bool
    {
        return LegacyAccess::isGranted('ROLE_MOD_TAB_' . strtoupper($action), $employeeProfileId);
    }

    public function getRoles(int $employeeProfileId): array
    {
        return LegacyAccess::getRoles($employeeProfileId);
    }
}
