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

use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class HookCore extends ObjectModel
{
    /**
     * @var string Hook name identifier
     */
    public $name;

    /**
     * @var string Hook title (displayed in BO)
     */
    public $title;

    /**
     * @var string Hook description
     */
    public $description;

    /**
     * @var bool
     */
    public $position = false;

    /**
     * @var bool
     */
    public $active = true;

    /**
     * @var array List of executed hooks on this page
     */
    public static $executed_hooks = [];

    public static $native_module;

    protected static $disabledHookModules = [];

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'hook',
        'primary' => 'id_hook',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isHookName', 'required' => true, 'size' => 191],
            'title' => ['type' => self::TYPE_STRING, 'validate' => 'isGenericName'],
            'description' => ['type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'],
            'position' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    /**
     * List of all deprecated hooks.
     *
     * @var array
     */
    protected static $deprecated_hooks = [
        // Back office
        'backOfficeFooter' => ['from' => '1.7.0.0'],
        'displayBackOfficeFooter' => ['from' => '1.7.0.0'],

        // Shipping step
        'displayCarrierList' => ['from' => '1.7.0.0'],
        'extraCarrier' => ['from' => '1.7.0.0'],

        // Payment step
        'hookBackBeforePayment' => ['from' => '1.7.0.0'],
        'hookDisplayBeforePayment' => ['from' => '1.7.0.0'],
        'hookOverrideTOSDisplay' => ['from' => '1.7.0.0'],

        // Product page
        'displayProductTabContent' => ['from' => '1.7.0.0'],
        'displayProductTab' => ['from' => '1.7.0.0'],

        // Order page
        'displayAdminOrderRight' => ['from' => '1.7.7.0'],
        'displayAdminOrderLeft' => ['from' => '1.7.7.0'],
        'displayAdminOrderTabOrder' => ['from' => '1.7.7.0'],
        'displayAdminOrderTabShip' => ['from' => '1.7.7.0'],
        'displayAdminOrderContentOrder' => ['from' => '1.7.7.0'],
        'displayAdminOrderContentShip' => ['from' => '1.7.7.0'],

        // Controller
        'actionAjaxDieBefore' => ['from' => '1.6.1.1'],
        'actionGetProductPropertiesAfter' => ['from' => '1.7.8.0'],
    ];

    public const MODULE_LIST_BY_HOOK_KEY = 'hook_module_exec_list_';

    public function add($autodate = true, $null_values = false)
    {
        Cache::clean('hook_idsbyname');
        Cache::clean('hook_idsbyname_withalias');
        Cache::clean('active_hooks');

        return parent::add($autodate, $null_values);
    }

    public function clearCache($all = false)
    {
        Cache::clean('hook_idsbyname');
        Cache::clean('hook_idsbyname_withalias');
        Cache::clean('active_hooks');
        parent::clearCache($all);
    }

    /**
     * Returns the canonical name for a given hook.
     *
     * @param string $hookName
     *
     * @return string
     */
    public static function normalizeHookName($hookName)
    {
        $loweredName = strtolower($hookName);

        if ($loweredName == 'displayheader') {
            return 'displayHeader';
        }

        $hookNamesByAlias = Hook::getCanonicalHookNames();

        return $hookNamesByAlias[$loweredName] ?? $hookName;
    }

    /**
     * Return true if the hook name starts with "display"
     *
     * @param string $hook_name The name of the hook to check
     *
     * @return bool
     */
    public static function isDisplayHookName($hook_name)
    {
        $hook_name = strtolower(static::normalizeHookName($hook_name));

        if ($hook_name === 'header' || $hook_name === 'displayheader') {
            // this hook is to add resources to the <head> section of the page
            // so it doesn't display anything by itself
            return false;
        }

        return strpos($hook_name, 'display') === 0;
    }

    /**
     * Return Hooks List.
     *
     * @param bool $position
     *
     * @return array Hooks List
     */
    public static function getHooks($position = false, $only_display_hooks = false)
    {
        $hooks = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            '
			SELECT * FROM `' . _DB_PREFIX_ . 'hook` h
			' . ($position ? 'WHERE h.`position` = 1' : '') . '
			ORDER BY `name`'
        );

        if ($only_display_hooks) {
            return array_filter($hooks, function ($hook) {
                return static::isDisplayHookName($hook['name']);
            });
        } else {
            return $hooks;
        }
    }

    /**
     * Returns the hook ID from a given hook name.
     *
     * By default, if the provided hook name is an alias, this method will return the id of their canonical hook.
     * Otherwise, it will treat the alias as a normal hook and will return false if it's not registered in the hooks table.
     *
     * @param string $hookName Hook name
     * @param bool $withAliases [default=true] Set to FALSE to ignore hook aliases
     * @param bool $refreshCache [default=false] Set to TRUE to force cache refresh
     *
     * @return int|false Hook ID, or false if it doesn't exist
     *
     * @throws PrestaShopDatabaseException
     */
    public static function getIdByName($hookName, bool $withAliases = true, bool $refreshCache = false)
    {
        $hookName = strtolower($hookName);
        if (!Validate::isHookName($hookName)) {
            return false;
        }

        $hook_ids = self::getAllHookIds($withAliases, $refreshCache);

        return $hook_ids[$hookName] ?? false;
    }

    /**
     * Return hook ID from name.
     *
     * @return string Hook name
     *
     * @throws PrestaShopObjectNotFoundException
     */
    public static function getNameById($hook_id)
    {
        $cache_id = 'hook_namebyid_' . $hook_id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getValue('
							SELECT `name`
							FROM `' . _DB_PREFIX_ . 'hook`
							WHERE `id_hook` = ' . (int) $hook_id);

            if (false === $result) {
                throw new PrestaShopObjectNotFoundException(
                    sprintf('The hook id #%d does not exist in database', $hook_id)
                );
            }

            Cache::store($cache_id, $result);

            return $result;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Indicates whether the provided hook is an alias of another one
     *
     * @param string $hookName Hook name to test
     *
     * @return bool TRUE if the hook is an alias, false otherwise
     *
     * @throws PrestaShopDatabaseException
     */
    public static function isAlias(string $hookName): bool
    {
        $aliases = self::getCanonicalHookNames();

        return isset($aliases[strtolower($hookName)]);
    }

    /**
     * Get the list of hook aliases, indexed by hook name
     *
     * @since 1.7.1.0
     *
     * @return array<string, array<string>> Array of hookName => hookAliases[]
     */
    private static function getAllHookAliases(): array
    {
        $cacheId = 'hook_aliases';
        if (!Cache::isStored($cacheId)) {
            $hookAliasList = Db::getInstance()->executeS('SELECT `name`, `alias` FROM `' . _DB_PREFIX_ . 'hook_alias`');
            $hookAliases = [];
            if ($hookAliasList) {
                foreach ($hookAliasList as $ha) {
                    $hookAliases[strtolower($ha['name'])][] = $ha['alias'];
                }
            }
            Cache::store($cacheId, $hookAliases);

            return $hookAliases;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Returns all backward compatibility hook names for a given canonical hook name.
     *
     * @param string $canonicalHookName Canonical hook name
     *
     * @return string[] List of aliases
     *
     * @since 1.7.1.0
     */
    private static function getHookAliasesFor(string $canonicalHookName): array
    {
        $cacheId = 'hook_aliases_' . $canonicalHookName;
        if (Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $allAliases = Hook::getAllHookAliases();

        $aliases = $allAliases[strtolower($canonicalHookName)] ?? [];

        Cache::store($cacheId, $aliases);

        return $aliases;
    }

    /**
     * Returns a list of canonical hook names, indexed by lower case alias.
     *
     * @return array Array of hook names, indexed by lower case alias
     *
     * @throws PrestaShopDatabaseException
     */
    private static function getCanonicalHookNames(): array
    {
        $cacheId = 'hook_canonical_names';

        if (!Cache::isStored($cacheId)) {
            $databaseResults = Db::getInstance()->executeS('SELECT name, alias FROM `' . _DB_PREFIX_ . 'hook_alias`');
            $hooksByAlias = [];
            if ($databaseResults) {
                foreach ($databaseResults as $record) {
                    $hooksByAlias[strtolower($record['alias'])] = $record['name'];
                }
            }
            Cache::store($cacheId, $hooksByAlias);

            return $hooksByAlias;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Returns a list containing the canonical name for the provided hook name followed by all its aliases.
     *
     * @param string $hookName
     *
     * @return array
     */
    public static function getAllKnownNames(string $hookName): array
    {
        $canonical = static::normalizeHookName($hookName);

        return array_unique(
            array_merge(
                [$canonical],
                self::getHookAliasesFor($canonical)
            )
        );
    }

    /**
     * Check if a hook is callable on a module.
     *
     * @param Module $module Module instance
     * @param string $hookName Hook name
     * @param bool $strict [default=false] Set to TRUE to avoid checking if aliases are callable as well
     *
     * @return bool
     *
     * @since 1.7.1.0
     */
    public static function isHookCallableOn(Module $module, string $hookName, $strict = false): bool
    {
        $hooksToCheck = (!$strict) ? static::getAllKnownNames($hookName) : [$hookName];

        foreach ($hooksToCheck as $currentHookName) {
            if (is_callable([$module, self::getMethodName($currentHookName)])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Call a hook (or one of its alternative names) on a module.
     *
     * @since 1.7.1.0
     *
     * @param Module $module
     * @param string $hookName
     * @param array $hookArgs
     *
     * @return mixed
     */
    private static function callHookOn(Module $module, string $hookName, array $hookArgs)
    {
        try {
            // Note: we need to make sure to call the exact hook name first.
            // This especially important when the module uses __call() to process the right hook.
            // Since is_callable() will always return true when __call() is available,
            // if the module was expecting an aliased hook name to be invoked, but we send
            // the canonical hook name instead, the hook will never be acknowledged by the module.
            $methodName = self::getMethodName($hookName);
            if (is_callable([$module, $methodName])) {
                return static::coreCallHook($module, $methodName, $hookArgs);
            }

            // fall back to all other names
            foreach (static::getAllKnownNames($hookName) as $hook) {
                $methodName = self::getMethodName($hook);
                if (is_callable([$module, $methodName])) {
                    return static::coreCallHook($module, $methodName, $hookArgs);
                }
            }
        } catch (Exception $e) {
            $environment = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Environment');
            if ($environment->isDebug()) {
                throw new CoreException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return '';
    }

    /**
     * Get list of all registered hooks with modules, indexed by hook id and module id
     *
     * @since 1.5.0
     *
     * @return array<int, array<int, array{id_hook:string|int,title:string,description:string,'hm.position':string|int,'m.position':string|int,id_module:string,name:string,active:string|int}>>
     */
    public static function getHookModuleList()
    {
        $cache_id = 'hook_module_list';
        if (Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }

        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT h.id_hook, h.name as h_name, title, description, h.position, hm.position as hm_position, m.id_module, m.name, m.active
            FROM `' . _DB_PREFIX_ . 'hook_module` hm
            STRAIGHT_JOIN `' . _DB_PREFIX_ . 'hook` h ON (h.id_hook = hm.id_hook AND hm.id_shop = ' . (int) Context::getContext()->shop->id . ')
            STRAIGHT_JOIN `' . _DB_PREFIX_ . 'module` as m ON (m.id_module = hm.id_module)
            ORDER BY hm.position'
        );
        $list = [];
        foreach ($results as $result) {
            if (!isset($list[$result['id_hook']])) {
                $list[$result['id_hook']] = [];
            }

            $list[$result['id_hook']][$result['id_module']] = [
                'id_hook' => $result['id_hook'],
                'title' => $result['title'],
                'description' => $result['description'],
                'hm.position' => $result['position'],
                'm.position' => $result['hm_position'],
                'id_module' => $result['id_module'],
                'name' => $result['name'],
                'active' => $result['active'],
            ];
        }
        Cache::store($cache_id, $list);

        return $list;
    }

    /**
     * Return Hooks List.
     *
     * @since 1.5.0
     *
     * @param int $id_hook
     * @param int|null $id_module
     *
     * @return array Modules List
     */
    public static function getModulesFromHook($id_hook, $id_module = null)
    {
        $hm_list = Hook::getHookModuleList();
        $module_list = (isset($hm_list[$id_hook])) ? $hm_list[$id_hook] : [];

        if ($id_module) {
            return (isset($module_list[$id_module])) ? [$module_list[$id_module]] : [];
        }

        return $module_list;
    }

    public static function isModuleRegisteredOnHook($module_instance, $hook_name, $id_shop)
    {
        $prefix = _DB_PREFIX_;
        $id_hook = (int) Hook::getIdByName($hook_name, true);
        $id_shop = (int) $id_shop;
        $id_module = (int) $module_instance->id;

        $sql = "SELECT * FROM {$prefix}hook_module
                  WHERE `id_hook` = {$id_hook}
                  AND `id_module` = {$id_module}
                  AND `id_shop` = {$id_shop}";

        $rows = Db::getInstance()->executeS($sql);

        return !empty($rows);
    }

    /**
     * Registers a module to a given hook
     *
     * @param ModuleCore $module_instance The affected module
     * @param string|string[] $hook_name Hook name(s) to register this module to
     * @param int[]|null $shop_list List of shop ids
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public static function registerHook($module_instance, $hook_name, $shop_list = null)
    {
        $return = true;
        $hook_names = (is_array($hook_name)) ? $hook_name : [$hook_name];

        foreach ($hook_names as $hook_name) {
            // Check hook name validation and if module is installed
            if (!Validate::isHookName($hook_name)) {
                throw new PrestaShopException('Invalid hook name');
            }
            if (!($module_instance instanceof Module)
                || !isset($module_instance->id)
                || !is_numeric($module_instance->id)
            ) {
                return false;
            }

            // Check that hook listener is implemented by the module
            if (!static::isHookCallableOn($module_instance, $hook_name) && !($module_instance instanceof WidgetInterface)) {
                $message = sprintf(
                    'Hook with the name %s has been registered by %s, but the corresponding method %s has not been defined in the Module class.',
                    $hook_name,
                    get_class($module_instance),
                    self::getMethodName($hook_name)
                );
                if (_PS_MODE_DEV_) {
                    throw new PrestaShopModuleException($message);
                }
                $logger = new LegacyLogger();
                $logger->warning($message);
            }

            Hook::exec(
                'actionModuleRegisterHookBefore',
                [
                    'object' => $module_instance,
                    'hook_name' => $hook_name,
                ]
            );

            // Get hook id
            $id_hook = Hook::getIdByName($hook_name, false);

            // If hook does not exist, we create it
            if (!$id_hook) {
                $new_hook = new Hook();
                $new_hook->name = pSQL($hook_name);
                $new_hook->title = pSQL($hook_name);
                $new_hook->position = true;
                $new_hook->add();
                $id_hook = $new_hook->id;
                if (!$id_hook) {
                    return false;
                }
            }

            // If shop lists is null, we fill it with all shops
            if (null === $shop_list) {
                $shop_list = Shop::getCompleteListOfShopsID();
            }

            $shop_list_employee = Shop::getShops(true, null, true);

            foreach ($shop_list as $shop_id) {
                $isModuleAlreadyRegisteredOnHook = static::isModuleRegisteredOnHook(
                    $module_instance,
                    $hook_name,
                    (int) $shop_id
                );

                if ($isModuleAlreadyRegisteredOnHook) {
                    continue;
                }

                // Get module position in hook
                $sql = 'SELECT MAX(`position`) AS position
                    FROM `' . _DB_PREFIX_ . 'hook_module`
                    WHERE `id_hook` = ' . (int) $id_hook . ' AND `id_shop` = ' . (int) $shop_id;
                if (!$position = Db::getInstance()->getValue($sql)) {
                    $position = 0;
                }

                // Register module in hook
                $return = $return && Db::getInstance()->insert('hook_module', [
                    'id_module' => (int) $module_instance->id,
                    'id_hook' => (int) $id_hook,
                    'id_shop' => (int) $shop_id,
                    'position' => (int) ($position + 1),
                ]);

                if (!in_array($shop_id, $shop_list_employee)) {
                    $where = '`id_module` = ' . (int) $module_instance->id . ' AND `id_shop` = ' . (int) $shop_id;
                    $return = $return && Db::getInstance()->delete('module_shop', $where);
                }
            }

            Hook::exec(
                'actionModuleRegisterHookAfter',
                [
                    'object' => $module_instance,
                    'hook_name' => $hook_name,
                ]
            );
        }

        return $return;
    }

    /**
     * Unhooks a module from given hook
     *
     * @param ModuleCore $module_instance The module to unhook
     * @param int|string $hook_identifier Hook ID or hook name to unhook the module from
     * @param int[]|null $shop_list List of shop ids
     *
     * @return bool
     */
    public static function unregisterHook($module_instance, $hook_identifier, $shop_list = null)
    {
        if (is_numeric($hook_identifier)) {
            // If we received hook ID as an integer directly, we try to find it's name
            $hook_id = $hook_identifier;

            /*
             * Try to load hook name. The hook could be deleted, but we don't care, we can still do the job.
             * Try/catch block is here because getNameById throws an exception when the hook is not found.
             * It would be better if getNameById returned false in future versions.
             */
            try {
                $hook_name = Hook::getNameById((int) $hook_identifier);
            } catch (PrestaShopObjectNotFoundException $e) {
            }

            // If getting the name failed or we got some malformed hook name
            if (empty($hook_name)) {
                $hook_name = '';
            }
        } else {
            // If we received hook name as a string, we try to find it's ID
            $hook_id = Hook::getIdByName($hook_identifier, false);
            $hook_name = $hook_identifier;
        }

        // Hook id is critical, we can't unhook anything if we don't know the ID
        if (empty($hook_id)) {
            return false;
        }

        if (!empty($hook_name)) {
            Hook::exec(
                'actionModuleUnRegisterHookBefore',
                [
                    'object' => $module_instance,
                    'hook_name' => $hook_name,
                ]
            );
        }

        // Unregister module on hook by id
        $sql = 'DELETE FROM `' . _DB_PREFIX_ . 'hook_module`
            WHERE `id_module` = ' . (int) $module_instance->id . ' AND `id_hook` = ' . (int) $hook_id
            . (($shop_list) ? ' AND `id_shop` IN(' . implode(', ', array_map('intval', $shop_list)) . ')' : '');
        $result = Db::getInstance()->execute($sql);

        // Clean modules position
        $module_instance->cleanPositions($hook_id, $shop_list);

        if (!empty($hook_name)) {
            Hook::exec(
                'actionModuleUnRegisterHookAfter',
                [
                    'object' => $module_instance,
                    'hook_name' => $hook_name,
                ]
            );
        }

        return $result;
    }

    /**
     * Returns a list of modules that are registered for a given hook, each following this schema:
     *
     * ```
     *     [
     *         'id_hook' => $hookId,
     *         'module' => $moduleName,
     *         'id_module' => $moduleId
     *     ]
     * ```
     *
     * If no hook name is given, it returns all the hook registrations, indexed by lower cased hook name.
     *
     * @param string|null $hookName Hook name (null to return all hooks)
     *
     * @return array[]|false returns an array of hook registrations, or false if the provided hook name is not registered
     *
     * @throws PrestaShopDatabaseException
     *
     * @since 1.5.0
     */
    public static function getHookModuleExecList($hookName = null)
    {
        $allHookRegistrations = self::getAllHookRegistrations(Context::getContext(), $hookName);

        // If no hook_name is given, return all registered hooks
        if (null === $hookName) {
            return $allHookRegistrations;
        }

        $normalizedHookName = strtolower($hookName);
        $modulesToInvoke = (isset($allHookRegistrations[$normalizedHookName])) ? $allHookRegistrations[$normalizedHookName] : [];

        // add modules that are registered to aliases of this hook
        $aliases = Hook::getHookAliasesFor($hookName);

        if (!empty($aliases)) {
            $alreadyIncludedModuleIds = array_column($modulesToInvoke, 'id_module');

            foreach ($aliases as $alias) {
                $hookAlias = strtolower($alias);

                if (isset($allHookRegistrations[$hookAlias])) {
                    foreach ($allHookRegistrations[$hookAlias] as $registeredAlias) {
                        if (!in_array($registeredAlias['id_module'], $alreadyIncludedModuleIds)) {
                            $modulesToInvoke[] = $registeredAlias;
                        }
                    }
                }
            }
        }

        return !empty($modulesToInvoke) ? $modulesToInvoke : false;
    }

    /**
     * Add a module ID to the list of modules that should not execute hooks
     */
    public static function disableHooksForModule(int $moduleId): void
    {
        if (in_array($moduleId, self::$disabledHookModules)) {
            return;
        }

        self::$disabledHookModules[] = $moduleId;
        Cache::clean(self::MODULE_LIST_BY_HOOK_KEY . '*');
    }

    /**
     * Execute modules for specified hook.
     *
     * @param string $hook_name Hook Name
     * @param array $hook_args Parameters for the functions
     * @param string|int|null $id_module Execute hook for this module only
     * @param bool $array_return If specified, module output will be set by name in an array
     * @param bool $check_exceptions Check permission exceptions
     * @param bool $use_push Force change to be refreshed on Dashboard widgets
     * @param int|null $id_shop If specified, hook will be execute the shop with this ID
     * @param bool $chain If specified, hook will chain the return of hook module
     *
     * @throws PrestaShopException
     *
     * @return mixed|null Module's output
     */
    public static function exec(
        $hook_name,
        $hook_args = [],
        $id_module = null,
        $array_return = false,
        $check_exceptions = true,
        $use_push = false,
        $id_shop = null,
        $chain = false
    ) {
        if ($use_push) {
            Tools::displayParameterAsDeprecated('use_push');
        }

        if (defined('PS_INSTALLATION_IN_PROGRESS') || !self::getHookStatusByName($hook_name)) {
            return $array_return ? [] : null;
        }

        $hookRegistry = self::getHookRegistry();
        $isRegistryEnabled = null !== $hookRegistry;

        if ($isRegistryEnabled) {
            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
            $hookRegistry->selectHook($hook_name, $hook_args, $backtrace[0]['file'], $backtrace[0]['line']);
        }

        // $chain & $array_return are incompatible so if chained is set to true, we disable the array_return option
        if (true === $chain) {
            $array_return = false;
        }

        static $disable_non_native_modules = null;
        if ($disable_non_native_modules === null) {
            $disable_non_native_modules = (bool) Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
        }

        // Check arguments validity
        if (($id_module && !is_numeric($id_module)) || !Validate::isHookName($hook_name)) {
            throw new PrestaShopException('Invalid id_module or hook_name');
        }

        // If no modules associated to hook_name or recompatible hook name, we stop the function

        if (!$module_list = Hook::getHookModuleExecList($hook_name)) {
            if ($isRegistryEnabled) {
                $hookRegistry->collect();
            }

            return ($array_return) ? [] : '';
        }

        // Check if hook exists
        if (!$id_hook = Hook::getIdByName($hook_name, false)) {
            if ($isRegistryEnabled) {
                $hookRegistry->collect();
            }

            return ($array_return) ? [] : false;
        }

        // Store list of executed hooks on this page
        Hook::$executed_hooks[$id_hook] = $hook_name;

        $context = Context::getContext();
        if (!isset($hook_args['cookie']) || !$hook_args['cookie']) {
            $hook_args['cookie'] = $context->cookie;
        }
        if (!isset($hook_args['cart']) || !$hook_args['cart']) {
            $hook_args['cart'] = $context->cart;
        }

        // Look on modules list
        $altern = 0;
        $output = ($array_return) ? [] : '';

        if ($disable_non_native_modules && !isset(Hook::$native_module)) {
            Hook::$native_module = Module::getNativeModuleList();
        }

        $different_shop = false;
        if ($id_shop !== null && Validate::isUnsignedId($id_shop) && $id_shop != $context->shop->getContextShopID()) {
            $old_context = $context->shop->getContext();
            $old_shop = clone $context->shop;
            $shop = new Shop((int) $id_shop);
            if (Validate::isLoadedObject($shop)) {
                $context->shop = $shop;
                $context->shop->setContext(Shop::CONTEXT_SHOP, $shop->id);
                $different_shop = true;
            }
        }

        foreach ($module_list as $key => $hookRegistration) {
            // Check errors
            if ($id_module && $id_module != $hookRegistration['id_module']) {
                continue;
            }

            if ((bool) $disable_non_native_modules && Hook::$native_module && count(Hook::$native_module) && !in_array($hookRegistration['module'], Hook::$native_module)) {
                continue;
            }

            $registeredHookId = $hookRegistration['id_hook'];
            if ($registeredHookId === $id_hook) {
                // the module is registered to the canonical hook name
                $registeredHookName = $hook_name;
            } else {
                // the module is registered to an alias
                $registeredHookName = static::getNameById($hookRegistration['id_hook']);
                trigger_error(
                    sprintf(
                        'The hook "%s" is deprecated, please use "%s" instead in module "%s".',
                        $registeredHookName,
                        $hook_name,
                        $hookRegistration['module']
                    ),
                    E_USER_DEPRECATED
                );
            }

            // Check permissions
            if ($check_exceptions) {
                $exceptions = Module::getExceptionsStatic($hookRegistration['id_module'], $hookRegistration['id_hook']);

                $controller_obj = Context::getContext()->controller;
                if ($controller_obj === null) {
                    $controller = null;
                } else {
                    $controller = isset($controller_obj->controller_name) ?
                        $controller_obj->controller_name
                        : $controller_obj->php_self;
                }

                //check if current controller is a module controller
                if (isset($controller_obj->module) && Validate::isLoadedObject($controller_obj->module)) {
                    $controller = 'module-' . $controller_obj->module->name . '-' . $controller;
                }

                if (in_array($controller, $exceptions)) {
                    continue;
                }

                //Backward compatibility of controller names
                $matching_name = [
                    'authentication' => 'auth',
                ];
                if (isset($matching_name[$controller]) && in_array($matching_name[$controller], $exceptions)) {
                    continue;
                }
                if (Validate::isLoadedObject($context->employee) && !Module::getPermissionStatic($hookRegistration['id_module'], 'view', $context->employee)) {
                    continue;
                }
            }

            if (!($moduleInstance = Module::getInstanceByName($hookRegistration['module']))) {
                continue;
            }

            if ($isRegistryEnabled) {
                $hookRegistry->hookedByModule($moduleInstance);
            }

            if (Hook::isHookCallableOn($moduleInstance, $registeredHookName)) {
                $hook_args['altern'] = ++$altern;

                if (0 !== $key && true === $chain) {
                    $hook_args = $output;
                }

                $display = Hook::callHookOn($moduleInstance, $registeredHookName, $hook_args);

                if ($array_return) {
                    $output[$moduleInstance->name] = $display;
                } else {
                    if (true === $chain) {
                        $output = $display;
                    } else {
                        $output .= $display;
                    }
                }
                if ($isRegistryEnabled) {
                    $hookRegistry->hookedByCallback($moduleInstance, $hook_args);
                }
            } elseif (Hook::isDisplayHookName($registeredHookName)) {
                if ($moduleInstance instanceof WidgetInterface) {
                    if (0 !== $key && true === $chain) {
                        $hook_args = $output;
                    }

                    $display = Hook::coreRenderWidget($moduleInstance, $registeredHookName, $hook_args);

                    if ($array_return) {
                        $output[$moduleInstance->name] = $display;
                    } else {
                        if (true === $chain) {
                            $output = $display;
                        } else {
                            $output .= $display;
                        }
                    }
                }

                if ($isRegistryEnabled) {
                    $hookRegistry->hookedByWidget($moduleInstance, $hook_args);
                }
            }
        }

        if ($different_shop
            && isset($old_shop, $old_context, $shop->id)
             ) {
            $context->shop = $old_shop;
            $context->shop->setContext($old_context, $shop->id);
        }

        if (true === $chain) {
            if (isset($output['cookie'])) {
                unset($output['cookie']);
            }
            if (isset($output['cart'])) {
                unset($output['cart']);
            }
        }

        if ($isRegistryEnabled) {
            $hookRegistry->hookWasCalled();
            $hookRegistry->collect();
        }

        return $output;
    }

    public static function coreCallHook($module, $method, $params)
    {
        return $module->{$method}($params);
    }

    public static function coreRenderWidget($module, $hook_name, $params)
    {
        $context = Context::getContext();
        if (!Module::isEnabled($module->name) || $context->isMobile() && !Module::isEnabledForMobileDevices($module->name)) {
            return null;
        }

        try {
            return $module->renderWidget($hook_name, $params);
        } catch (Exception $e) {
            $environment = ServiceLocator::get('\\PrestaShop\\PrestaShop\\Adapter\\Environment');
            if ($environment->isDebug()) {
                throw new CoreException($e->getMessage(), $e->getCode(), $e);
            }
        }

        return '';
    }

    /**
     * @return \PrestaShopBundle\DataCollector\HookRegistry|null
     */
    private static function getHookRegistry()
    {
        $sfContainer = SymfonyContainer::getInstance();
        if (null !== $sfContainer && 'dev' === $sfContainer->getParameter('kernel.environment')) {
            return $sfContainer->get('prestashop.hooks_registry');
        }

        return null;
    }

    /**
     * Retrieves all modules registered to any hook, indexed by hok name.
     *
     * Each registration looks like this:
     *
     * ```
     *     [
     *         'id_hook' => $hookId,
     *         'module' => $moduleName,
     *         'id_module' => $moduleId
     *     ]
     * ```
     *
     * @param Context $context
     * @param string|null $hookName Hook name (to be used when the hook registration is dynamic and context sensitive)
     *
     * @return array[][]
     *
     * @throws PrestaShopDatabaseException
     */
    private static function getAllHookRegistrations(Context $context, ?string $hookName): array
    {
        $shop = $context->shop;
        $customer = $context->customer;

        $cache_id = self::MODULE_LIST_BY_HOOK_KEY
            . (isset($shop->id) ? '_' . $shop->id : '')
            . (isset($customer->id) ? '_' . $customer->id : '');

        $useCache = (
            !in_array(
                $hookName,
                [
                    'displayPayment',
                    'displayPaymentEU',
                    'paymentOptions',
                    'displayBackOfficeHeader',
                    'displayAdminLogin',
                ]
            )
        );

        if ($useCache && Cache::isStored($cache_id)) {
            return Cache::retrieve($cache_id);
        }

        $groups = [];
        $use_groups = Group::isFeatureActive();
        $frontend = !$context->employee instanceof Employee;
        if ($frontend) {
            // Get groups list
            if ($use_groups) {
                if ($customer instanceof Customer && $customer->isLogged()) {
                    $groups = $customer->getGroups();
                } elseif ($customer instanceof Customer && $customer->isGuest()) {
                    $groups = [(int) Configuration::get('PS_GUEST_GROUP')];
                } else {
                    $groups = [(int) Configuration::get('PS_UNIDENTIFIED_GROUP')];
                }
            }
        }

        // SQL Request
        $sql = new DbQuery();
        $sql->select('h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module');
        $sql->from('module', 'm');
        if (!in_array($hookName, ['displayBackOfficeHeader', 'displayAdminLogin'])) {
            $sql->join(
                Shop::addSqlAssociation(
                    'module',
                    'm',
                    true,
                    'module_shop.enable_device & ' . (int) Context::getContext()->getDevice()
                )
            );
        } else {
            $sql->innerJoin('module_shop', 'module_shop', 'module_shop.`id_module` = m.`id_module`');
        }
        $sql->innerJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
        $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
        if ($hookName !== 'paymentOptions') {
            $sql->where('h.`name` != "paymentOptions"');
        } elseif ($frontend) {
            // For payment modules, we check that they are available in the contextual country
            if (Validate::isLoadedObject($context->country)) {
                $sql->where(
                    '(
                        h.`name` IN ("displayPayment", "displayPaymentEU", "paymentOptions")
                        AND (
                            SELECT `id_country`
                            FROM `' . _DB_PREFIX_ . 'module_country` mc
                            WHERE mc.`id_module` = m.`id_module`
                            AND `id_country` = ' . (int) $context->country->id . '
                            AND `id_shop` = ' . (int) $shop->id . '
                            LIMIT 1
                        ) = ' . (int) $context->country->id . ')'
                );
            }
            if (Validate::isLoadedObject($context->currency)) {
                $sql->where(
                    '(
                        h.`name` IN ("displayPayment", "displayPaymentEU", "paymentOptions")
                        AND (
                            SELECT `id_currency`
                            FROM `' . _DB_PREFIX_ . 'module_currency` mcr
                            WHERE mcr.`id_module` = m.`id_module`
                            AND `id_currency` IN (' . (int) $context->currency->id . ', -1, -2)
                            LIMIT 1
                        ) IN (' . (int) $context->currency->id . ', -1, -2))'
                );
            }
            if (Validate::isLoadedObject($context->cart)) {
                $carrier = new Carrier($context->cart->id_carrier);
                if (Validate::isLoadedObject($carrier)) {
                    $sql->where(
                        '(
                            h.`name` IN ("displayPayment", "displayPaymentEU", "paymentOptions")
                            AND (
                                SELECT `id_reference`
                                FROM `' . _DB_PREFIX_ . 'module_carrier` mcar
                                WHERE mcar.`id_module` = m.`id_module`
                                AND `id_reference` = ' . (int) $carrier->id_reference . '
                                AND `id_shop` = ' . (int) $shop->id . '
                                LIMIT 1
                            ) = ' . (int) $carrier->id_reference . ')'
                    );
                }
            }
        }
        if (Validate::isLoadedObject($shop) && $hookName !== 'displayAdminLogin') {
            $sql->where('hm.`id_shop` = ' . (int) $shop->id);
        }

        if ($frontend) {
            if ($use_groups) {
                $sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
                if (Validate::isLoadedObject($shop)) {
                    $sql->where(
                        'mg.id_shop = ' . ((int) $shop->id)
                        . (count($groups) ? ' AND  mg.`id_group` IN (' . implode(', ', $groups) . ')' : '')
                    );
                } elseif (count($groups)) {
                    $sql->where('mg.`id_group` IN (' . implode(', ', $groups) . ')');
                }
            }
        }

        if (!empty(self::$disabledHookModules)) {
            $sql->where('m.id_module NOT IN (' . implode(', ', self::$disabledHookModules) . ')');
        }

        $sql->groupBy('hm.id_hook, hm.id_module');
        $sql->orderBy('hm.`position`');

        $allHookRegistrations = [];
        if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
            /** @var array{hook: string, id_module: int, id_hook: int, module: string} $row */
            foreach ($result as $row) {
                $row['hook'] = strtolower($row['hook']);
                if (!isset($allHookRegistrations[$row['hook']])) {
                    $allHookRegistrations[$row['hook']] = [];
                }

                $allHookRegistrations[$row['hook']][] = [
                    'id_hook' => $row['id_hook'],
                    'module' => $row['module'],
                    'id_module' => $row['id_module'],
                ];
            }
        }

        if ($useCache) {
            Cache::store($cache_id, $allHookRegistrations);
        }

        return $allHookRegistrations;
    }

    /**
     * Returns all hook IDs, indexed by hook name.
     *
     * @param bool $withAliases [default=false] If true, includes hook aliases along their canonical hook id
     * @param bool $refreshCache [default=false] Force cache refresh
     *
     * @return int[]
     *
     * @throws PrestaShopDatabaseException
     */
    private static function getAllHookIds(bool $withAliases = false, bool $refreshCache = false): array
    {
        $cacheId = 'hook_idsbyname';
        if ($withAliases) {
            $cacheId .= 'hook_idsbyname_withalias';
        }

        if (!$refreshCache && Cache::isStored($cacheId)) {
            return Cache::retrieve($cacheId);
        }

        $db = Db::getInstance();
        // Get all hook IDs by name and alias
        $hookIds = [];

        if ($withAliases) {
            $sql = 'SELECT `id_hook`, `name`
                FROM `' . _DB_PREFIX_ . 'hook`
                UNION
                SELECT `id_hook`, ha.`alias` as name
                FROM `' . _DB_PREFIX_ . 'hook_alias` ha
                INNER JOIN `' . _DB_PREFIX_ . 'hook` h ON ha.name = h.name';
        } else {
            $sql = 'SELECT `id_hook`, `name` FROM `' . _DB_PREFIX_ . 'hook`';
        }

        $result = $db->executeS($sql, false);

        while ($row = $db->nextRow($result)) {
            $hookIds[strtolower($row['name'])] = $row['id_hook'];
        }

        Cache::store($cacheId, $hookIds);

        return $hookIds;
    }

    /**
     * Returns the name of the method to invoke in a module for a given hook name
     *
     * @param string $hookName Hook name
     *
     * @return string Method name
     */
    private static function getMethodName(string $hookName): string
    {
        return 'hook' . ucfirst($hookName);
    }

    /**
     * Return status from a given hook name.
     *
     * @param string $hook_name Hook name
     *
     * @return bool
     */
    public static function getHookStatusByName($hook_name): bool
    {
        $hook_names = [];
        if (Cache::isStored('active_hooks')) {
            $hook_names = Cache::retrieve('active_hooks');
        } else {
            $sql = new DbQuery();
            $sql->select('lower(name) as name');
            $sql->from('hook', 'h');
            $sql->where('h.active = 1');
            $active_hooks = Db::getInstance()->executeS($sql);
            if (!empty($active_hooks)) {
                $hook_names = array_column($active_hooks, 'name');
                if (is_array($hook_names)) {
                    Cache::store('active_hooks', $hook_names);
                }
            }
        }

        return in_array(strtolower($hook_name), $hook_names);
    }
}
