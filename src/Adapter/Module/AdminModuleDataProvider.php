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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Module;

use Context;
use Employee;
use Module as LegacyModule;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShopBundle\Service\DataProvider\Admin\CategoriesProvider;
use PrestaShopBundle\Service\DataProvider\Admin\ModuleInterface;
use Symfony\Component\Routing\Router;
use Tools;

/**
 * Data provider for new Architecture, about Module object model.
 *
 * This class will provide data from DB / ORM about Modules for the Admin interface.
 * This is an Adapter that works with the Legacy code and persistence behaviors.
 */
class AdminModuleDataProvider implements ModuleInterface
{
    public const _DAY_IN_SECONDS_ = 86400; /* Cache for One Day */

    /**
     * @const array giving a translation domain key for each module action
     */
    public const _ACTIONS_TRANSLATION_DOMAINS_ = [
        Module::ACTION_INSTALL => 'Admin.Actions',
        Module::ACTION_UNINSTALL => 'Admin.Actions',
        Module::ACTION_ENABLE => 'Admin.Actions',
        Module::ACTION_DISABLE => 'Admin.Actions',
        Module::ACTION_ENABLE_MOBILE => 'Admin.Modules.Feature',
        Module::ACTION_DISABLE_MOBILE => 'Admin.Modules.Feature',
        Module::ACTION_RESET => 'Admin.Actions',
        Module::ACTION_UPGRADE => 'Admin.Actions',
        Module::ACTION_CONFIGURE => 'Admin.Actions',
        Module::ACTION_DELETE => 'Admin.Actions',
    ];

    /**
     * @const array giving a translation label for each module action
     */
    public const ACTIONS_TRANSLATION_LABELS = [
        Module::ACTION_INSTALL => 'Install',
        Module::ACTION_UNINSTALL => 'Uninstall',
        Module::ACTION_ENABLE => 'Enable',
        Module::ACTION_DISABLE => 'Disable',
        Module::ACTION_ENABLE_MOBILE => 'Enable mobile',
        Module::ACTION_DISABLE_MOBILE => 'Disable mobile',
        Module::ACTION_RESET => 'Reset',
        Module::ACTION_UPGRADE => 'Upgrade',
        Module::ACTION_CONFIGURE => 'Configure',
        Module::ACTION_DELETE => 'Delete',
    ];

    /**
     * @var array<string> of defined and callable module actions
     */
    protected $moduleActions = [
        Module::ACTION_INSTALL,
        Module::ACTION_CONFIGURE,
        Module::ACTION_ENABLE,
        Module::ACTION_DISABLE,
        Module::ACTION_ENABLE_MOBILE,
        Module::ACTION_DISABLE_MOBILE,
        Module::ACTION_RESET,
        Module::ACTION_UPGRADE,
        Module::ACTION_UNINSTALL,
        Module::ACTION_DELETE,
    ];

    /**
     * @var Router|null
     */
    private $router = null;

    /**
     * @var CategoriesProvider
     */
    private $categoriesProvider;

    /**
     * @var ModuleDataProvider
     */
    private $moduleProvider;

    /**
     * @var Employee|null
     */
    private $employee;

    /**
     * @var array
     */
    protected $catalog_modules = [];

    /**
     * @var array
     */
    protected $catalog_modules_names;

    /**
     * @var bool
     */
    public $failed = false;

    public function __construct(
        CategoriesProvider $categoriesProvider,
        ModuleDataProvider $modulesProvider,
        Employee $employee = null
    ) {
        $this->categoriesProvider = $categoriesProvider;
        $this->moduleProvider = $modulesProvider;
        $this->employee = $employee;
    }

    /**
     * @param Router $router
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @deprecated since version 1.7.3.0
     *
     * @return array
     */
    public function getAllModules()
    {
        return LegacyModule::getModulesOnDisk(
            true,
            (int) Context::getContext()->employee->id
        );
    }

    /**
     * Check the permissions of the current context (CLI or employee) for a module.
     *
     * @param array $actions Actions to check
     * @param string $name The module name
     *
     * @return array of allowed actions
     */
    protected function filterAllowedActions(array $actions, $name = '')
    {
        $allowedActions = [];
        foreach (array_keys($actions) as $actionName) {
            if ($this->isAllowedAccess($actionName, $name)) {
                $allowedActions[$actionName] = $actions[$actionName];
            }
        }

        return $allowedActions;
    }

    /**
     * Check the permissions of the current context (CLI or employee) for a specified action.
     *
     * @param string $action The action called in the module
     * @param string $name (Optionnal for 'install') The module name to check
     *
     * @return bool
     */
    public function isAllowedAccess($action, $name = '')
    {
        if (Tools::isPHPCLI()) {
            return true;
        }

        if (in_array($action, ['install', 'upgrade'])) {
            return $this->employee->can('add', 'AdminModulessf');
        }

        if ('delete' === $action) {
            return $this->employee->can('delete', 'AdminModulessf');
        }

        if ('uninstall' === $action) {
            return $this->employee->can('delete', 'AdminModulessf') && $this->moduleProvider->can('uninstall', $name);
        }

        return $this->employee->can('edit', 'AdminModulessf') && $this->moduleProvider->can('configure', $name);
    }

    /**
     * Generates a list with actions and their respective URLs, depending on if the module is installed or not,
     * enabled, upgradable and other variables.
     *
     * @param ModuleCollection $modules
     * @param string|null $specific_action
     *
     * @return ModuleCollection
     */
    public function setActionUrls(ModuleCollection $modules, ?string $specific_action = null): ModuleCollection
    {
        foreach ($modules as $module) {
            $urls = [];
            $moduleAttributes = $module->getAttributes();
            $moduleDatabaseAttributes = $module->getDatabaseAttributes();

            // Generate target URL for each action we offer
            foreach ($this->moduleActions as $action) {
                if ($action === 'configure') {
                    $urls[$action] = $this->router->generate('admin_module_configure_action', [
                        'module_name' => $moduleAttributes->get('name'),
                    ]);
                    continue;
                }
                $parameters = [
                    'action' => $action,
                    'module_name' => $moduleAttributes->get('name'),
                ];
                if ($action === 'upgrade' && $moduleAttributes->get('download_url') !== null) {
                    $parameters['source'] = $moduleAttributes->get('download_url');
                }
                $urls[$action] = $this->router->generate('admin_module_manage_action', $parameters);
            }

            // Let's filter the actions depending on conditions the module is in
            if ($module->isInstalled()) {
                unset($urls['install']);
                unset($urls['delete']);
                if (!$module->isActive()) {
                    unset(
                        $urls['disable'],
                        $urls['enableMobile'],
                        $urls['disableMobile']
                    );
                    if ($moduleDatabaseAttributes->get('active') === null) {
                        unset($urls['enable']);
                    }
                } else {
                    unset(
                        $urls['enable'],
                        $urls[$module->isActiveOnMobile() ? 'enableMobile' : 'disableMobile']
                    );
                }

                if (!$module->canBeUpgraded()) {
                    unset($urls['upgrade']);
                }

                if (!$module->isConfigurable()) {
                    unset($urls['configure']);
                }
            } elseif ($module->isUninstalled()) {
                $urls = [
                    'install' => $urls['install'],
                    'delete' => $urls['delete'],
                ];
            } else {
                $urls = ['install' => $urls['install']];
            }

            // Go through the actions and remove all actions that the current environment
            // doesn't have rights for.
            $filteredUrls = $this->filterAllowedActions($urls, $moduleAttributes->get('name'));

            if ($specific_action && array_key_exists($specific_action, $filteredUrls)) {
                $urlActive = $specific_action;
            } else {
                $urlActive = key($filteredUrls);
            }

            $moduleAttributes->set('urls', $filteredUrls);
            $moduleAttributes->set('url_active', $urlActive);
            $moduleAttributes->set('actionTranslationDomains', self::_ACTIONS_TRANSLATION_DOMAINS_);
            $moduleAttributes->set('actionTranslationLabels', self::ACTIONS_TRANSLATION_LABELS);
            $moduleAttributes->set(
                'categoryParent',
                $this->categoriesProvider->getParentCategory($moduleAttributes->get('categoryName'))
            );
        }

        return $modules;
    }

    /**
     * @param array $modules
     * @param array $filters
     *
     * @return array
     */
    protected function applyModuleFilters(array $modules, array $filters)
    {
        if (!count($filters)) {
            return $modules;
        }

        // We get our module IDs to keep
        foreach ($filters as $filter_name => $value) {
            $search_result = [];

            switch ($filter_name) {
                case 'search':
                    // We build our results array.
                    // We could remove directly the non-matching modules, but we will give that for the final loop of this function

                    foreach (explode(' ', $value) as $keyword) {
                        if (empty($keyword)) {
                            continue;
                        }

                        // Instead of looping on the whole module list, we use $module_ids which can already be reduced
                        // thanks to the previous array_intersect(...)
                        foreach ($modules as $key => $module) {
                            if (strpos($module->displayName, $keyword) !== false
                                || strpos($module->name, $keyword) !== false
                                || strpos($module->description, $keyword) !== false) {
                                $search_result[] = $key;
                            }
                        }
                    }

                    break;
                case 'name':
                    // exact given name (should return 0 or 1 result)
                    $search_result[] = $value;

                    break;
                default:
                    // "the switch statement is considered a looping structure for the purposes of continue."
                    continue 2;
            }

            $modules = array_intersect_key($modules, array_flip($search_result));
        }

        return $modules;
    }
}
