<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
