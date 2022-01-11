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

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Module;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateModulePermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Command\UpdateTabPermissionsCommand;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\Query\GetPermissionsForConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\QueryResult\ConfigurablePermissions;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ControllerPermission;
use PrestaShop\PrestaShop\Core\Domain\Profile\Permission\ValueObject\ModulePermission;
use Tab;

class PermissionFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * This is ugly but the employee used has few impact for now anyway, and super admin should have ID 1 in the fixtures
     */
    private const SUPER_ADMIN_ID = 1;

    /**
     * @Then profile :profileReference should have the following permissions for tabs:
     *
     * @param string $profileReference
     * @param TableNode $table
     */
    public function assertTabsPermissionForProfile(string $profileReference, TableNode $table): void
    {
        $configuration = $this->getConfiguration();
        $tabsPermissionsForAllProfiles = $configuration->getProfilePermissionsForTabs();
        $profileId = $this->getSharedStorage()->get($profileReference);

        Assert::assertNotEmpty($tabsPermissionsForAllProfiles[$profileId]);
        $profileTabsPermissions = $tabsPermissionsForAllProfiles[$profileId];

        $expectedTabsPermissions = $table->getRowsHash();
        foreach ($expectedTabsPermissions as $tabName => $serializedTabPermissions) {
            $tabId = Tab::getIdFromClassName($tabName);
            Assert::assertNotFalse($tabId, sprintf('No tab found for %s', $tabName));

            $tabPermissions = $this->getPermissionsFromTabArray($profileTabsPermissions[$tabId]);
            $expectedTabPermissions = $this->unserializeTabPermissions($serializedTabPermissions);
            Assert::assertEquals(
                $expectedTabPermissions,
                $tabPermissions,
                sprintf(
                    'Incorrect permissions for profile %s for tab %s expected %s but got %s instead',
                    $profileReference,
                    $tabName,
                    var_export($expectedTabPermissions, true),
                    var_export($tabPermissions, true)
                )
            );
        }
    }

    /**
     * @When /^I (disable|enable) (view|add|edit|delete|all) permission for tab (.*) for profile (.*)/
     *
     * @param string $action
     * @param string $permission
     * @param string $profileReference
     */
    public function updateTabPermission(string $action, string $permission, string $tabName, string $profileReference): void
    {
        $this->getCommandBus()->handle(new UpdateTabPermissionsCommand(
            $this->getSharedStorage()->get($profileReference),
            Tab::getIdFromClassName($tabName),
            $permission,
            $action === 'enable'
        ));
    }

    /**
     * @Then profile :profileReference should have the following permissions for modules:
     *
     * @param string $profileReference
     * @param TableNode $table
     */
    public function assertModulesPermissionForProfile(string $profileReference, TableNode $table): void
    {
        $configuration = $this->getConfiguration();
        $modulesPermissionsForAllProfiles = $configuration->getProfilePermissionsForModules();
        $profileId = $this->getSharedStorage()->get($profileReference);

        Assert::assertNotEmpty($modulesPermissionsForAllProfiles[$profileId]);
        $profileModulesPermissions = $modulesPermissionsForAllProfiles[$profileId];

        $expectedModulesPermissions = $table->getRowsHash();
        foreach ($expectedModulesPermissions as $moduleName => $serializedModulePermissions) {
            $modulePermissions = $this->getPermissionsFromModuleArray($profileModulesPermissions[strtoupper($moduleName)]);
            $expectedModulePermissions = $this->unserializeModulePermissions($serializedModulePermissions);
            Assert::assertEquals(
                $expectedModulePermissions,
                $modulePermissions,
                sprintf(
                    'Incorrect permissions for profile %s for module %s expected %s but got %s instead',
                    $profileReference,
                    $moduleName,
                    var_export($expectedModulePermissions, true),
                    var_export($modulePermissions, true)
                )
            );
        }
    }

    /**
     * @When /^I (disable|enable) (view|configure|uninstall) permission for module (.*) for profile (.*)/
     *
     * @param string $action
     * @param string $permission
     * @param string $profileReference
     */
    public function updateModulePermission(string $action, string $permission, string $moduleName, string $profileReference): void
    {
        $this->getCommandBus()->handle(new UpdateModulePermissionsCommand(
            $this->getSharedStorage()->get($profileReference),
            Module::getModuleIdByName($moduleName),
            $permission,
            $action === 'enable'
        ));
    }

    /**
     * @param array $tab
     *
     * @return array<string, bool>
     */
    private function getPermissionsFromModuleArray(array $tab): array
    {
        return [
            ModulePermission::VIEW => (bool) $tab[ModulePermission::VIEW],
            ModulePermission::CONFIGURE => (bool) $tab[ModulePermission::CONFIGURE],
            ModulePermission::UNINSTALL => (bool) $tab[ModulePermission::UNINSTALL],
        ];
    }

    /**
     * @param array $tab
     *
     * @return array<string, bool>
     */
    private function getPermissionsFromTabArray(array $tab): array
    {
        return [
            ControllerPermission::VIEW => (bool) $tab[ControllerPermission::VIEW],
            ControllerPermission::ADD => (bool) $tab[ControllerPermission::ADD],
            ControllerPermission::EDIT => (bool) $tab[ControllerPermission::EDIT],
            ControllerPermission::DELETE => (bool) $tab[ControllerPermission::DELETE],
        ];
    }

    /**
     * Transforms serialized string into array of permissions
     *
     *    "view,add" => [view => true, add => true, edit => false, delete => false]
     *    "edit,delete" => [view => false, add => false, edit => true, delete => true]
     *
     * @param string $serializedTabPermissions
     *
     * @return array<string, bool>
     */
    private function unserializeTabPermissions(string $serializedTabPermissions): array
    {
        $permissions = explode(',', $serializedTabPermissions);

        return [
            ControllerPermission::VIEW => in_array(ControllerPermission::VIEW, $permissions),
            ControllerPermission::ADD => in_array(ControllerPermission::ADD, $permissions),
            ControllerPermission::EDIT => in_array(ControllerPermission::EDIT, $permissions),
            ControllerPermission::DELETE => in_array(ControllerPermission::DELETE, $permissions),
        ];
    }

    /**
     * Transforms serialized string into array of permissions
     *
     *    "view,configure" => [view => true, configure => true, uninstall => false]
     *    "view,uninstall" => [view => true, configure => false, uninstall => true]
     *
     * @param string $serializedTabPermissions
     *
     * @return array<string, bool>
     */
    private function unserializeModulePermissions(string $serializedTabPermissions): array
    {
        $permissions = explode(',', $serializedTabPermissions);

        return [
            ModulePermission::VIEW => in_array(ModulePermission::VIEW, $permissions),
            ModulePermission::CONFIGURE => in_array(ModulePermission::CONFIGURE, $permissions),
            ModulePermission::UNINSTALL => in_array(ModulePermission::UNINSTALL, $permissions),
        ];
    }

    /**
     * Return big fat configuration object with all the data, the input employee has few impact since the object contains
     * everything anyway.
     *
     * @return ConfigurablePermissions
     */
    private function getConfiguration(): ConfigurablePermissions
    {
        return $this->getQueryBus()->handle(new GetPermissionsForConfiguration(self::SUPER_ADMIN_ID));
    }
}
