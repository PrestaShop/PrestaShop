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

namespace PrestaShop\PrestaShop\Adapter\Module;

use Context;
use Employee;
use Module as LegacyModule;
use PrestaShop\PrestaShop\Core\Addon\AddonsCollection;
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
        'install' => 'Admin.Actions',
        'uninstall' => 'Admin.Actions',
        'enable' => 'Admin.Actions',
        'disable' => 'Admin.Actions',
        'enable_mobile' => 'Admin.Modules.Feature',
        'disable_mobile' => 'Admin.Modules.Feature',
        'reset' => 'Admin.Actions',
        'upgrade' => 'Admin.Actions',
        'configure' => 'Admin.Actions',
    ];

    /**
     * @var array of defined and callable module actions
     */
    protected $moduleActions = ['install', 'uninstall', 'enable', 'disable', 'enable_mobile', 'disable_mobile', 'reset', 'upgrade'];

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

        if ('uninstall' === $action) {
            return $this->employee->can('delete', 'AdminModulessf') && $this->moduleProvider->can('uninstall', $name);
        }

        return $this->employee->can('edit', 'AdminModulessf') && $this->moduleProvider->can('configure', $name);
    }

    /**
     * @param AddonsCollection $addons
     * @param string|null $specific_action
     *
     * @return AddonsCollection
     */
    public function generateAddonsUrls(AddonsCollection $addons, $specific_action = null)
    {
        foreach ($addons as $addon) {
            $urls = [];
            foreach ($this->moduleActions as $action) {
                $urls[$action] = $this->router->generate('admin_module_manage_action', [
                    'action' => $action,
                    'module_name' => $addon->attributes->get('name'),
                ]);
            }
            $urls['configure'] = $this->router->generate('admin_module_configure_action', [
                'module_name' => $addon->attributes->get('name'),
            ]);

            if ($addon->database->has('installed') && $addon->database->getBoolean('installed')) {
                if (!$addon->database->getBoolean('active')) {
                    $url_active = 'enable';
                    unset(
                        $urls['install'],
                        $urls['disable']
                    );
                } elseif ($addon->attributes->getBoolean('is_configurable')) {
                    $url_active = 'configure';
                    unset(
                        $urls['enable'],
                        $urls['install']
                    );
                } else {
                    $url_active = 'disable';
                    unset(
                        $urls['install'],
                        $urls['enable'],
                        $urls['configure']
                    );
                }

                if (!$addon->attributes->getBoolean('is_configurable')) {
                    unset($urls['configure']);
                }

                if (!$addon->database->getBoolean('active_on_mobile')) {
                    unset($urls['disable_mobile']);
                } else {
                    unset($urls['enable_mobile']);
                }
                if (!$addon->canBeUpgraded()) {
                    unset(
                        $urls['upgrade']
                    );
                }
            } elseif (
                !$addon->attributes->has('origin') ||
                $addon->disk->getBoolean('is_present') ||
                in_array($addon->attributes->get('origin'), ['native', 'native_all', 'partner', 'customer'], true)
            ) {
                $url_active = 'install';
                unset(
                    $urls['uninstall'],
                    $urls['enable'],
                    $urls['disable'],
                    $urls['enable_mobile'],
                    $urls['disable_mobile'],
                    $urls['reset'],
                    $urls['upgrade'],
                    $urls['configure']
                );
            } else {
                $url_active = 'buy';
            }

            $urls = $this->filterAllowedActions($urls, $addon->attributes->get('name'));
            $addon->attributes->set('urls', $urls);
            $addon->attributes->set('actionTranslationDomains', self::_ACTIONS_TRANSLATION_DOMAINS_);
            if ($specific_action && array_key_exists($specific_action, $urls)) {
                $addon->attributes->set('url_active', $specific_action);
            } elseif ($url_active === 'buy' || array_key_exists($url_active, $urls)) {
                $addon->attributes->set('url_active', $url_active);
            } else {
                $addon->attributes->set('url_active', key($urls));
            }

            $categoryParent = $this->categoriesProvider->getParentCategory($addon->attributes->get('categoryName'));
            $addon->attributes->set('categoryParent', $categoryParent);
        }

        return $addons;
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
