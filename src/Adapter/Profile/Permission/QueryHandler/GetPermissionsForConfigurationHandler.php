<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Profile\Permission\QueryHandler;

use Context;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query\GetPermissionsForConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryHandler\GetPermissionsForConfigurationHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult\ConfigurablePermissions;
use Profile;
use Tab;

/**
 * Get configuratble permissions
 *
 * @internal
 */
final class GetPermissionsForConfigurationHandler implements GetPermissionsForConfigurationHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(GetPermissionsForConfiguration $query)
    {
        $profiles = $this->getProfilesForPermissionsConfiguration();
        $tabs = $this->getTabsForPermissionsConfiguration();
        $permissions = ['view', 'add', 'edit', 'delete'];

        $tabPermissions = $this->getTabPermissionsForProfiles($profiles);

        $bulkConfiguration = $this->getBulkConfigurationForProfiles(
            $query->getEmployeeProfileId()->getValue(),
            true, //@todo: fix
            $tabPermissions,
            $profiles,
            $tabs,
            $permissions
        );

        return new ConfigurablePermissions(
            $tabPermissions,
            $profiles,
            $tabs,
            $bulkConfiguration
        );
    }

    /**
     * @return int[] IDs of non configurable tabs
     */
    private function getNonConfigurableTabs()
    {
        return [
            Tab::getIdFromClassName('AdminLogin'),
        ];
    }

    /**
     * @return array
     */
    private function getProfilesForPermissionsConfiguration()
    {
        $legacyProfiles = Profile::getProfiles(Context::getContext()->language->id);
        $profiles = [];

        foreach ($legacyProfiles as $profile) {
            $isAdministrator = (int) $profile['id_profile'] === _PS_ADMIN_PROFILE_;

            $profiles[] = [
                'id' => $profile['id_profile'],
                'name' => $profile['name'],
                'is_administrator' => $isAdministrator,
            ];
        }

        return $profiles;
    }

    /**
     * @return array
     */
    private function getTabsForPermissionsConfiguration()
    {
        $nonConfigurableTabs = $this->getNonConfigurableTabs();
        $legacyTabs = Tab::getTabs(Context::getContext()->language->id);
        $tabs = [];

        foreach ($legacyTabs as $tab) {
            // Don't allow permissions for unnamed tabs (ie. AdminLogin)
            if (empty($tab['name'])) {
                continue;
            }

            if (in_array($tab['id_tab'], $nonConfigurableTabs)) {
                continue;
            }

            $tabs[] = [
                'id' => $tab['id_tab'],
                'id_parent' => $tab['id_parent'],
                'name' => $tab['name'],
            ];
        }

        return $this->buildTabsTree($tabs);
    }

    /**
     * @param array $tabs
     * @param int $parentId
     *
     * @return array
     */
    private function buildTabsTree(array &$tabs, $parentId = 0)
    {
        $children = [];

        foreach ($tabs as &$tab) {
            $id = $tab['id'];

            if ((int) $tab['id_parent'] === (int) $parentId) {
                $children[$id] = $tab;
                $children[$id]['children'] = $this->buildTabsTree($tabs, $id);
            }
        }

        return $children;
    }

    /**
     * @param array $profiles
     *
     * @return array
     */
    private function getTabPermissionsForProfiles(array $profiles)
    {
        $permissions = [];

        foreach ($profiles as $profile) {
            $permissions[$profile['id']] = Profile::getProfileAccesses($profile['id']);
        }

        dump($permissions);

        return $permissions;
    }

    /**
     * @todo: check if this can be used or removed
     *
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
        $employeeProfileId,
        $hasEmployeeEditPermission,
        array $profileTabPermissions,
        array $profiles,
        array $tabs,
        array $permissions
    ) {
        $bulkConfiguration = [];

        foreach ($profiles as $profile) {
            $bulkConfiguration[$profile['id']] = [
                'view' => true,
                'add' => true,
                'edit' => true,
                'delete' => true,
                'all' => true,
            ];

            // if employee does not have "edit" permission
            // then configuration is disabled
            if (!$hasEmployeeEditPermission) {
                $bulkConfiguration[$profile['id']] = [
                    'view' => false,
                    'add' => false,
                    'edit' => false,
                    'delete' => false,
                    'all' => false,
                ];

                continue;
            }

            foreach ($tabs as $tab) {
                foreach ($permissions as $permission) {
                    if (!$profileTabPermissions[$employeeProfileId][$tab['id']][$permission]) {
                        $bulkConfiguration[$profile['id']]['view'] = false;
                        $bulkConfiguration[$profile['id']]['all'] = false;

                        break;
                    }
                }

                foreach ($tab['children'] as $childTab) {
                    foreach ($permissions as $permission) {
                        if (!$profileTabPermissions[$employeeProfileId][$childTab['id']][$permission]) {
                            $bulkConfiguration[$profile['id']]['add'] = false;
                            $bulkConfiguration[$profile['id']]['all'] = false;

                            break;
                        }
                    }

                    foreach ($childTab['children'] as $subChild) {
                        foreach ($permissions as $permission) {
                            if (!$profileTabPermissions[$employeeProfileId][$subChild['id']][$permission]) {
                                $bulkConfiguration[$profile['id']]['edit'] = false;
                                $bulkConfiguration[$profile['id']]['all'] = false;

                                break;
                            }
                        }

                        foreach ($subChild['children'] as $subSubChild) {
                            foreach ($permissions as $permission) {
                                if (!$profileTabPermissions[$employeeProfileId][$subSubChild['id']][$permission]) {
                                    $bulkConfiguration[$profile['id']]['delete'] = false;
                                    $bulkConfiguration[$profile['id']]['all'] = false;

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
}
