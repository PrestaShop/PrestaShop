<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ControllerPermission;

class ConfigurablePermissions
{
    /**
     * @var array
     */
    private $profilePermissionsForTabs;

    /**
     * @var array
     */
    private $profiles;

    /**
     * @var array
     */
    private $tabs;

    /**
     * @var array
     */
    private $bulkConfiguration;

    /**
     * @var array
     */
    private $profilePermissionsForModules;

    /**
     * @var array
     */
    private $permissions;

    /**
     * @var int
     */
    private $employeeProfileId;

    /**
     * @var bool
     */
    private $hasEmployeeEditPermission;

    /**
     * @param array $profilePermissionsForTabs
     * @param array $profilePermissionsForModules
     * @param array $profiles
     * @param array $tabs
     * @param array $bulkConfiguration
     * @param string[] $permissions
     * @param int $employeeProfileId
     * @param bool $hasEmployeeEditPermission
     */
    public function __construct(
        array $profilePermissionsForTabs,
        array $profilePermissionsForModules,
        array $profiles,
        array $tabs,
        array $bulkConfiguration,
        array $permissions,
        int $employeeProfileId,
        bool $hasEmployeeEditPermission
    ) {
        $this->profilePermissionsForTabs = $profilePermissionsForTabs;
        $this->profiles = $profiles;
        $this->tabs = $tabs;
        $this->bulkConfiguration = $bulkConfiguration;
        $this->profilePermissionsForModules = $profilePermissionsForModules;
        $this->permissions = $permissions;
        $this->employeeProfileId = $employeeProfileId;
        $this->hasEmployeeEditPermission = $hasEmployeeEditPermission;
    }

    /**
     * @return array
     */
    public function getProfilePermissionsForTabs(): array
    {
        return $this->profilePermissionsForTabs;
    }

    /**
     * @return array
     */
    public function getProfiles(): array
    {
        return $this->profiles;
    }

    /**
     * @return array
     */
    public function getTabs(): array
    {
        return $this->tabs;
    }

    /**
     * @param int $profileId
     *
     * @return bool
     */
    public function isBulkViewConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::VIEW];
    }

    /**
     * @param int $profileId
     *
     * @return bool
     */
    public function isBulkAddConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::ADD];
    }

    /**
     * @param int $profileId
     *
     * @return bool
     */
    public function isBulkEditConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::EDIT];
    }

    /**
     * @param int $profileId
     *
     * @return bool
     */
    public function isBulkDeleteConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::DELETE];
    }

    /**
     * @param int $profileId
     *
     * @return bool
     */
    public function isBulkAllConfigurationEnabled(int $profileId): bool
    {
        return $this->bulkConfiguration[$profileId][ControllerPermission::ALL];
    }

    /**
     * @return array
     */
    public function getProfilePermissionsForModules(): array
    {
        return $this->profilePermissionsForModules;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        return $this->permissions;
    }

    /**
     * @return int
     */
    public function getEmployeeProfileId(): int
    {
        return $this->employeeProfileId;
    }

    /**
     * @return bool
     */
    public function hasEmployeeEditPermission(): bool
    {
        return $this->hasEmployeeEditPermission;
    }
}
