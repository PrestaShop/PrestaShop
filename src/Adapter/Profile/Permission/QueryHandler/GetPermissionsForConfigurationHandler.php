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

namespace PrestaShop\PrestaShop\Adapter\Profile\Permission\QueryHandler;

use Module;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query\GetPermissionsForConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryHandler\GetPermissionsForConfigurationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult\ConfigurablePermissions;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ControllerPermission;
use PrestaShopBundle\Security\Voter\PageVoter;
use Profile;
use RuntimeException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Tab;

/**
 * Get configurable permissions
 *
 * @internal
 */
class GetPermissionsForConfigurationHandler implements GetPermissionsForConfigurationHandlerInterface
{
    /**
     * @internal Max nesting level for building tabs tree
     */
    public const MAX_NESTING_LEVEL = 12;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var array
     */
    private $whitelist = [];

    /**
     * @var int
     */
    private $languageId;

    /**
     * @var array
     */
    protected $nonConfigurableTabs;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        int $languageId,
        array $nonConfigurableTabs
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->languageId = $languageId;
        $this->nonConfigurableTabs = $nonConfigurableTabs;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetPermissionsForConfiguration $query): ConfigurablePermissions
    {
        $profiles = $this->getProfilesForPermissionsConfiguration();
        $tabs = $this->getTabsForPermissionsConfiguration();

        $tabPermissionsForProfiles = $this->getTabPermissionsForProfiles($profiles);
        $modulePermissionsForProfiles = $this->getModulePermissionsForProfiles($profiles);

        $employeeProfileId = $query->getEmployeeProfileId()->getValue();
        $canEmployeeEditPermissions = $this->authorizationChecker->isGranted(PageVoter::UPDATE, 'AdminAccess');

        $bulkConfigurationPermissions = $this->getBulkConfigurationForProfiles(
            $employeeProfileId,
            $canEmployeeEditPermissions,
            $tabPermissionsForProfiles,
            $profiles,
            $tabs,
            ControllerPermission::SUPPORTED_PERMISSIONS
        );

        return new ConfigurablePermissions(
            $tabPermissionsForProfiles,
            $modulePermissionsForProfiles,
            $profiles,
            $tabs,
            $bulkConfigurationPermissions,
            ControllerPermission::SUPPORTED_PERMISSIONS,
            $employeeProfileId,
            $canEmployeeEditPermissions
        );
    }

    /**
     * @return array<int> IDs of non configurable tabs
     */
    private function getNonConfigurableTabs(): array
    {
        $result = [];
        foreach ($this->nonConfigurableTabs as $tabName) {
            $result[] = Tab::getIdFromClassName($tabName);
        }

        return $result;
    }

    /**
     * @return array<array<string, int|string|bool>>
     */
    private function getProfilesForPermissionsConfiguration(): array
    {
        $legacyProfiles = Profile::getProfiles($this->languageId);
        $profiles = [];

        foreach ($legacyProfiles as $profile) {
            $profiles[] = [
                'id' => $profile['id_profile'],
                'name' => $profile['name'],
                'is_administrator' => (int) $profile['id_profile'] === _PS_ADMIN_PROFILE_,
            ];
        }

        return $profiles;
    }

    /**
     * @return array
     */
    private function getTabsForPermissionsConfiguration(): array
    {
        $nonConfigurableTabs = $this->getNonConfigurableTabs();
        $legacyTabs = Tab::getTabs($this->languageId);
        $tabs = [];

        foreach ($legacyTabs as $tab) {
            // Don't allow permissions for unnamed tabs (ie. AdminLogin)
            if (empty($tab['name'])) {
                continue;
            }

            // Don't allow permissions for undefined parents
            if ((int) $tab['id_parent'] === -1) {
                continue;
            }

            if (in_array((int) $tab['id_tab'], $nonConfigurableTabs)) {
                continue;
            }

            $this->whitelist[] = $tab['id_tab'];
            $tabs[] = [
                'id' => (int) $tab['id_tab'],
                'id_parent' => (int) $tab['id_parent'],
                'name' => $tab['name'],
            ];
        }

        return $this->buildTabsTree($tabs);
    }

    /**
     * @param array $tabs
     * @param int $parentId
     * @param int $nestingLevel
     *
     * @return array
     */
    private function buildTabsTree(array &$tabs, int $parentId = 0, int $nestingLevel = 0): array
    {
        if (self::MAX_NESTING_LEVEL < $nestingLevel) {
            throw new RuntimeException(sprintf(
                'Maximum nesting level of "%d" reached in "%s"', self::MAX_NESTING_LEVEL,
                __METHOD__
            ));
        }

        $children = [];

        foreach ($tabs as &$tab) {
            if ((int) $tab['id_parent'] !== (int) $parentId) {
                continue;
            }

            $id = $tab['id'];
            $children[$id] = $tab;
            $children[$id]['children'] = $this->buildTabsTree($tabs, (int) $id, $nestingLevel + 1);
        }

        return $children;
    }

    /**
     * @param array $profiles
     *
     * @return array
     */
    private function getTabPermissionsForProfiles(array $profiles): array
    {
        $permissions = [];

        foreach ($profiles as $profile) {
            // Allow only whitelisted elements
            $permissions[$profile['id']] = array_filter(
                Profile::getProfileAccesses($profile['id']),
                function ($item) {
                    return in_array($item['id_tab'], $this->whitelist);
                }
            );
        }

        return $permissions;
    }

    /**
     * @param int $employeeProfileId
     * @param bool $hasEmployeeEditPermission
     * @param array $profileTabPermissions
     * @param array $profiles
     * @param array $tabs
     * @param array $permissions
     *
     * @return array
     */
    private function getBulkConfigurationForProfiles(
        int $employeeProfileId,
        bool $hasEmployeeEditPermission,
        array $profileTabPermissions,
        array $profiles,
        array $tabs,
        array $permissions
    ): array {
        $bulkConfiguration = [];

        foreach ($profiles as $profile) {
            $bulkConfiguration[$profile['id']] = [
                ControllerPermission::VIEW => true,
                ControllerPermission::ADD => true,
                ControllerPermission::EDIT => true,
                ControllerPermission::DELETE => true,
                ControllerPermission::ALL => true,
            ];

            // if employee does not have "edit" permission
            // then configuration is disabled
            if (!$hasEmployeeEditPermission) {
                $bulkConfiguration[$profile['id']] = [
                    ControllerPermission::VIEW => false,
                    ControllerPermission::ADD => false,
                    ControllerPermission::EDIT => false,
                    ControllerPermission::DELETE => false,
                    ControllerPermission::ALL => false,
                ];

                continue;
            }

            foreach ($tabs as $tab) {
                foreach ($permissions as $permission) {
                    if (!$profileTabPermissions[$employeeProfileId][$tab['id']][$permission]) {
                        $bulkConfiguration[$profile['id']][ControllerPermission::VIEW] = false;
                        $bulkConfiguration[$profile['id']][ControllerPermission::ALL] = false;

                        break;
                    }
                }

                foreach ($tab['children'] as $childTab) {
                    foreach ($permissions as $permission) {
                        if (!$profileTabPermissions[$employeeProfileId][$childTab['id']][$permission]) {
                            $bulkConfiguration[$profile['id']][ControllerPermission::ADD] = false;
                            $bulkConfiguration[$profile['id']][ControllerPermission::ALL] = false;

                            break;
                        }
                    }

                    foreach ($childTab['children'] as $subChild) {
                        foreach ($permissions as $permission) {
                            if (!$profileTabPermissions[$employeeProfileId][$subChild['id']][$permission]) {
                                $bulkConfiguration[$profile['id']][ControllerPermission::EDIT] = false;
                                $bulkConfiguration[$profile['id']][ControllerPermission::ALL] = false;

                                break;
                            }
                        }

                        foreach ($subChild['children'] as $subSubChild) {
                            foreach ($permissions as $permission) {
                                if (!$profileTabPermissions[$employeeProfileId][$subSubChild['id']][$permission]) {
                                    $bulkConfiguration[$profile['id']][ControllerPermission::DELETE] = false;
                                    $bulkConfiguration[$profile['id']][ControllerPermission::ALL] = false;

                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $bulkConfiguration;
    }

    /**
     * @param array $profiles
     *
     * @return array
     */
    private function getModulePermissionsForProfiles(array $profiles): array
    {
        $profilePermissionsForModules = [];

        foreach ($profiles as $profile) {
            $profilePermissionsForModules[$profile['id']] = Module::getModulesAccessesByIdProfile($profile['id']);

            uasort($profilePermissionsForModules[$profile['id']], function ($a, $b) {
                // For the reference https://github.com/PrestaShop/PrestaShop/pull/12428/files#r267703322
                $a['name'] = $a['name'] ?? '';
                $b['name'] = $b['name'] ?? '';

                return strnatcmp($a['name'], $b['name']);
            });
        }

        return $profilePermissionsForModules;
    }
}
