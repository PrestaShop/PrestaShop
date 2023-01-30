<?php

namespace PrestaShop\PrestaShop\Core\Security;

interface EmployeePermissionProviderInterface
{
    public function getRoles(int $employeeProfileId): array;
}
