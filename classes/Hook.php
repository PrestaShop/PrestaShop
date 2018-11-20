<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

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
     * @var array List of executed hooks on this page
     */
    public static $executed_hooks = array();

    public static $native_module;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'hook',
        'primary' => 'id_hook',
        'fields' => array(
            'name' =>            array('type' => self::TYPE_STRING, 'validate' => 'isHookName', 'required' => true, 'size' => 64),
            'title' =>            array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'description' =>    array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
            'position' =>        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
        ),
    );

    /**
     * @deprecated 1.5.0
     */
    protected static $_hook_modules_cache = null;

    /**
     * @deprecated 1.5.0
     */
    protected static $_hook_modules_cache_exec = null;

    /**
     * List of all deprecated hooks
     * @var array
     */
    protected static $deprecated_hooks = array(
        // Back office
        'backOfficeFooter' => array('from' => '1.7.0.0'),
        'displayBackOfficeFooter' => array('from' => '1.7.0.0'),

        // Shipping step
        'displayCarrierList' => array('from' => '1.7.0.0'),
        'extraCarrier' => array('from' => '1.7.0.0'),

        // Payment step
        'hookBackBeforePayment' => array('from' => '1.7.0.0'),
        'hookDisplayBeforePayment' => array('from' => '1.7.0.0'),
        'hookOverrideTOSDisplay' => array('from' => '1.7.0.0'),

        // Product page
        'displayProductTabContent' => array('from' => '1.7.0.0'),
        'displayProductTab' => array('from' => '1.7.0.0'),
    );

    const MODULE_LIST_BY_HOOK_KEY = 'hook_module_exec_list_';

    public function add($autodate = true, $null_values = false)
    {
        Cache::clean('hook_idsbyname');
        return parent::add($autodate, $null_values);
    }

    public static function normalizeHookName($hookName)
    {
        if (strtolower($hookName) == 'displayheader') {
            return 'displayHeader';
        }
        $hookAliasList = Hook::getHookAliasList();
        if (isset($hookAliasList[strtolower($hookName)])) {
            return $hookAliasList[strtolower($hookName)];
        }
        return $hookName;
    }

    public static function isDisplayHookName($hook_name)
    {
        $hook_name = strtolower(self::normalizeHookName($hook_name));

        if ($hook_name === 'header' || $hook_name === 'displayheader') {
            // this hook is to add resources to the <head> section of the page
            // so it doesn't display anything by itself
            return false;
        }

        return strpos($hook_name, 'display') === 0;
    }

    /**
     * Return Hooks List
     *
     * @param bool $position
     * @return array Hooks List
     */
    public static function getHooks($position = false, $only_display_hooks = false)
    {
        $hooks = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `'._DB_PREFIX_.'hook` h
			'.($position ? 'WHERE h.`position` = 1' : '').'
			ORDER BY `name`'
        );

        if ($only_display_hooks) {
            return array_filter($hooks, function ($hook) {
                return self::isDisplayHookName($hook['name']);
            });
        } else {
            return $hooks;
        }
    }

    /**
     * Return hook ID from name
     *
     * @param string $hook_name Hook name
     * @return int Hook ID
     */
    public static function getIdByName($hook_name)
    {
        $hook_name = strtolower($hook_name);
        if (!Validate::isHookName($hook_name)) {
            return false;
        }

        $cache_id = 'hook_idsbyname';
        if (!Cache::isStored($cache_id)) {
            // Get all hook ID by name and alias
            $hook_ids = array();
            $db = Db::getInstance();
            $result = $db->ExecuteS('
			SELECT `id_hook`, `name`
			FROM `'._DB_PREFIX_.'hook`
			UNION
			SELECT `id_hook`, ha.`alias` as name
			FROM `'._DB_PREFIX_.'hook_alias` ha
			INNER JOIN `'._DB_PREFIX_.'hook` h ON ha.name = h.name', false);
            while ($row = $db->nextRow($result)) {
                $hook_ids[strtolower($row['name'])] = $row['id_hook'];
            }
            Cache::store($cache_id, $hook_ids);
        } else {
            $hook_ids = Cache::retrieve($cache_id);
        }

        return (isset($hook_ids[$hook_name]) ? $hook_ids[$hook_name] : false);
    }

    /**
     * Return hook ID from name
     */
    public static function getNameById($hook_id)
    {
        $cache_id = 'hook_namebyid_'.$hook_id;
        if (!Cache::isStored($cache_id)) {
            $result = Db::getInstance()->getValue('
							SELECT `name`
							FROM `'._DB_PREFIX_.'hook`
							WHERE `id_hook` = '.(int)$hook_id);
            Cache::store($cache_id, $result);
            return $result;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get list of hook alias
     *
     * @since 1.5.0
     * @return array
     * @deprecated 1.7.1.0
     */
    public static function getHookAliasList()
    {
        $cache_id = 'hook_alias';
        if (!Cache::isStored($cache_id)) {
            $hook_alias_list = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'hook_alias`');
            $hook_alias = array();
            if ($hook_alias_list) {
                foreach ($hook_alias_list as $ha) {
                    $hook_alias[strtolower($ha['alias'])] = $ha['name'];
                }
            }
            Cache::store($cache_id, $hook_alias);
            return $hook_alias;
        }
        return Cache::retrieve($cache_id);
    }

    /**
     * Get the list of hook aliases
     *
     * @since 1.7.1.0
     * @return array
     */
    private static function getHookAliasesList()
    {
        $cacheId = 'hook_aliases';
        if (!Cache::isStored($cacheId)) {
            $hookAliasList = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'hook_alias`');
            $hookAliases = array();
            if ($hookAliasList) {
                foreach ($hookAliasList as $ha) {
                    if (!isset($hookAliases[$ha['name']])) {
                        $hookAliases[$ha['name']] = array();
                    }
                    $hookAliases[$ha['name']][] = $ha['alias'];
                }
            }
            Cache::store($cacheId, $hookAliases);
            return $hookAliases;
        }
        return Cache::retrieve($cacheId);
    }

    /**
     * Return backward compatibility hook names
     *
     * @since 1.7.1.0
     * @param $hookName
     * @return array
     */
    private static function getHookAliasesFor($hookName)
    {
        $cacheId = 'hook_aliases_' . $hookName;
        if (!Cache::isStored($cacheId)) {
            $aliasesList = Hook::getHookAliasesList();

            if (isset($aliasesList[$hookName])) {
                Cache::store($cacheId, $aliasesList[$hookName]);
                return $aliasesList[$hookName];
            }

            $retroName = array_keys(array_filter($aliasesList, function ($elem) use ($hookName) {
               return in_array($hookName, $elem) ;
            }));

            if (empty($retroName)) {
                Cache::store($cacheId, array());
                return array();
            }

            Cache::store($cacheId, $retroName);
            return $retroName;
        }
        return Cache::retrieve($cacheId);
    }

    /**
     * Check if a hook or one of its old names is callable on a module
     *
     * @since 1.7.1.0
     * @param $module
     * @param $hookName
     * @return bool
     */
    private static function isHookCallableOn($module, $hookName)
    {
        $aliases = Hook::getHookAliasesFor($hookName);
        $aliases[] = $hookName;

        return array_reduce($aliases, function ($prev, $curr) use ($module) {
            return $prev || is_callable(array($module, 'hook' . $curr));
        }, false);
    }

    /**
     * Call a hook (or one of its old name) on a module
     *
     * @since 1.7.1.0
     * @param $module
     * @param $hookName
     * @param $hookArgs
     * @return string
     */
    private static function callHookOn($module, $hookName, $hookArgs)
    {
        if (is_callable(array($module, 'hook' . $hookName))) {
            return Hook::coreCallHook($module, 'hook' . $hookName, $hookArgs);
        }
        foreach (Hook::getHookAliasesFor($hookName) as $hook) {
            if (is_callable(array($module, 'hook' . $hook))) {
                return Hook::coreCallHook($module, 'hook' . $hook, $hookArgs);
            }
        }
        return '';
    }

    /**
     * Return backward compatibility hook name
     *
     * @since 1.5.0
     * @param string $hook_name Hook name
     * @return int Hook ID
     * @deprecated 1.7.1.0
     */
    public static function getRetroHookName($hook_name)
    {
        $alias_list = Hook::getHookAliasList();
        if (isset($alias_list[strtolower($hook_name)])) {
            return $alias_list[strtolower($hook_name)];
        }

        $retro_hook_name = array_search($hook_name, $alias_list);
        if ($retro_hook_name === false) {
            return '';
        }
        return $retro_hook_name;
    }

    /**
     * Get list of all registered hooks with modules
     *
     * @since 1.5.0
     * @return array
     */
    public static function getHookModuleList()
    {
        $cache_id = 'hook_module_list';
        if (!Cache::isStored($cache_id)) {
            $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT h.id_hook, h.name as h_name, title, description, h.position, hm.position as hm_position, m.id_module, m.name, active
			FROM `'._DB_PREFIX_.'hook_module` hm
			STRAIGHT_JOIN `'._DB_PREFIX_.'hook` h ON (h.id_hook = hm.id_hook AND hm.id_shop = '.(int)Context::getContext()->shop->id.')
			STRAIGHT_JOIN `'._DB_PREFIX_.'module` as m ON (m.id_module = hm.id_module)
			ORDER BY hm.position');
            $list = array();
            foreach ($results as $result) {
                if (!isset($list[$result['id_hook']])) {
                    $list[$result['id_hook']] = array();
                }

                $list[$result['id_hook']][$result['id_module']] = array(
                    'id_hook' => $result['id_hook'],
                    'title' => $result['title'],
                    'description' => $result['description'],
                    'hm.position' => $result['position'],
                    'm.position' => $result['hm_position'],
                    'id_module' => $result['id_module'],
                    'name' => $result['name'],
                    'active' => $result['active'],
                );
            }
            Cache::store($cache_id, $list);

            // @todo remove this in 1.6, we keep it in 1.5 for retrocompatibility
            Hook::$_hook_modules_cache = $list;
            return $list;
        }

        return Cache::retrieve($cache_id);
    }

    /**
     * Return Hooks List
     *
     * @since 1.5.0
     * @param int $id_hook
     * @param int $id_module
     * @return array Modules List
     */
    public static function getModulesFromHook($id_hook, $id_module = null)
    {
        $hm_list = Hook::getHookModuleList();
        $module_list = (isset($hm_list[$id_hook])) ? $hm_list[$id_hook] : array();

        if ($id_module) {
            return (isset($module_list[$id_module])) ? array($module_list[$id_module]) : array();
        }
        return $module_list;
    }

    public static function isModuleRegisteredOnHook($module_instance, $hook_name, $id_shop)
    {
        $prefix = _DB_PREFIX_;
        $id_hook = (int) Hook::getIdByName($hook_name);
        $id_shop = (int) $id_shop;
        $id_module = (int) $module_instance->id;

        $sql = "SELECT * FROM {$prefix}hook_module
                  WHERE `id_hook` = {$id_hook}
                  AND `id_module` = {$id_module}
                  AND `id_shop` = {$id_shop}";

        $rows = Db::getInstance()->executeS($sql);
        return !empty($rows);
    }

    public static function registerHook($module_instance, $hook_name, $shop_list = null)
    {
        $return = true;
        if (is_array($hook_name)) {
            $hook_names = $hook_name;
        } else {
            $hook_names = array($hook_name);
        }

        foreach ($hook_names as $hook_name) {
            // Check hook name validation and if module is installed
            if (!Validate::isHookName($hook_name)) {
                throw new PrestaShopException('Invalid hook name');
            }
            if (!isset($module_instance->id) || !is_numeric($module_instance->id)) {
                return false;
            }

            // Retrocompatibility
            $hook_name_bak = $hook_name;
            if ($alias = Hook::getRetroHookName($hook_name)) {
                $hook_name = $alias;
            }

            Hook::exec('actionModuleRegisterHookBefore', array('object' => $module_instance, 'hook_name' => $hook_name));
            // Get hook id
            $id_hook = Hook::getIdByName($hook_name);

            // If hook does not exist, we create it
            if (!$id_hook) {
                $new_hook = new Hook();
                $new_hook->name = pSQL($hook_name);
                $new_hook->title = pSQL($hook_name);
                $new_hook->position = 1;
                $new_hook->add();
                $id_hook = $new_hook->id;
                if (!$id_hook) {
                    return false;
                }
            }

            // If shop lists is null, we fill it with all shops
            if (is_null($shop_list)) {
                $shop_list = Shop::getCompleteListOfShopsID();
            }

            $shop_list_employee = Shop::getShops(true, null, true);

            foreach ($shop_list as $shop_id) {
                // Check if already register
                $sql = 'SELECT hm.`id_module`
                    FROM `'._DB_PREFIX_.'hook_module` hm, `'._DB_PREFIX_.'hook` h
                    WHERE hm.`id_module` = '.(int)$module_instance->id.' AND h.`id_hook` = '.$id_hook.'
                    AND h.`id_hook` = hm.`id_hook` AND `id_shop` = '.(int)$shop_id;
                if (Db::getInstance()->getRow($sql)) {
                    continue;
                }

                // Get module position in hook
                $sql = 'SELECT MAX(`position`) AS position
                    FROM `'._DB_PREFIX_.'hook_module`
                    WHERE `id_hook` = '.(int)$id_hook.' AND `id_shop` = '.(int)$shop_id;
                if (!$position = Db::getInstance()->getValue($sql)) {
                    $position = 0;
                }

                // Register module in hook
                $return &= Db::getInstance()->insert('hook_module', array(
                    'id_module' => (int)$module_instance->id,
                    'id_hook' => (int)$id_hook,
                    'id_shop' => (int)$shop_id,
                    'position' => (int)($position + 1),
                ));

                if (!in_array($shop_id, $shop_list_employee)) {
                    $where = '`id_module` = '.(int)$module_instance->id.' AND `id_shop` = '.(int)$shop_id;
                    $return &= Db::getInstance()->delete('module_shop', $where);
                }
            }

            Hook::exec('actionModuleRegisterHookAfter', array('object' => $module_instance, 'hook_name' => $hook_name));
        }
        return $return;
    }

    public static function unregisterHook($module_instance, $hook_name, $shop_list = null)
    {
        if (is_numeric($hook_name)) {
            // $hook_name passed it the id_hook
            $hook_id = $hook_name;
            $hook_name = Hook::getNameById((int)$hook_id);
        } else {
            $hook_id = Hook::getIdByName($hook_name);
        }

        if (!$hook_id) {
            return false;
        }

        Hook::exec('actionModuleUnRegisterHookBefore', array('object' => $module_instance, 'hook_name' => $hook_name));

        // Unregister module on hook by id
        $sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
            WHERE `id_module` = '.(int)$module_instance->id.' AND `id_hook` = '.(int)$hook_id
            .(($shop_list) ? ' AND `id_shop` IN('.implode(', ', array_map('intval', $shop_list)).')' : '');
        $result = Db::getInstance()->execute($sql);

        // Clean modules position
        $module_instance->cleanPositions($hook_id, $shop_list);

        Hook::exec('actionModuleUnRegisterHookAfter', array('object' => $module_instance, 'hook_name' => $hook_name));

        return $result;
    }

    /**
     * Get list of modules we can execute per hook
     *
     * @since 1.5.0
     * @param string $hook_name Get list of modules for this hook if given
     * @return array
     */
    public static function getHookModuleExecList($hook_name = null)
    {
        $context = Context::getContext();
        $cache_id = self::MODULE_LIST_BY_HOOK_KEY.(isset($context->shop->id) ? '_'.$context->shop->id : '').((isset($context->customer)) ? '_'.$context->customer->id : '');
        if (!Cache::isStored($cache_id) || $hook_name == 'displayPayment' || $hook_name == 'displayPaymentEU' || $hook_name == 'paymentOptions' || $hook_name == 'displayBackOfficeHeader') {
            $frontend = true;
            $groups = array();
            $use_groups = Group::isFeatureActive();
            if (isset($context->employee)) {
                $frontend = false;
            } else {
                // Get groups list
                if ($use_groups) {
                    if (isset($context->customer) && $context->customer->isLogged()) {
                        $groups = $context->customer->getGroups();
                    } elseif (isset($context->customer) && $context->customer->isLogged(true)) {
                        $groups = array((int)Configuration::get('PS_GUEST_GROUP'));
                    } else {
                        $groups = array((int)Configuration::get('PS_UNIDENTIFIED_GROUP'));
                    }
                }
            }

            // SQL Request
            $sql = new DbQuery();
            $sql->select('h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module');
            $sql->from('module', 'm');
            if ($hook_name != 'displayBackOfficeHeader') {
                $sql->join(Shop::addSqlAssociation('module', 'm', true, 'module_shop.enable_device & '.(int)Context::getContext()->getDevice()));
                $sql->innerJoin('module_shop', 'ms', 'ms.`id_module` = m.`id_module`');
            }
            $sql->innerJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
            $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
            if ($hook_name != 'paymentOptions') {
                $sql->where('h.`name` != "paymentOptions"');
            }
            // For payment modules, we check that they are available in the contextual country
            elseif ($frontend) {
                if (Validate::isLoadedObject($context->country)) {
                    $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions")AND (SELECT `id_country` FROM `'._DB_PREFIX_.'module_country` mc WHERE mc.`id_module` = m.`id_module` AND `id_country` = '.(int)$context->country->id.' AND `id_shop` = '.(int)$context->shop->id.' LIMIT 1) = '.(int)$context->country->id.')');
                }
                if (Validate::isLoadedObject($context->currency)) {
                    $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions") AND (SELECT `id_currency` FROM `'._DB_PREFIX_.'module_currency` mcr WHERE mcr.`id_module` = m.`id_module` AND `id_currency` IN ('.(int)$context->currency->id.', -1, -2) LIMIT 1) IN ('.(int)$context->currency->id.', -1, -2))');
                }
                if (Validate::isLoadedObject($context->cart)) {
                    $carrier = new Carrier($context->cart->id_carrier);
                    if (Validate::isLoadedObject($carrier)) {
                        $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions") AND (SELECT `id_reference` FROM `'._DB_PREFIX_.'module_carrier` mcar WHERE mcar.`id_module` = m.`id_module` AND `id_reference` = '.(int)$carrier->id_reference.' AND `id_shop` = '.(int)$context->shop->id.' LIMIT 1) = '.(int)$carrier->id_reference.')');
                    }
                }
            }
            if (Validate::isLoadedObject($context->shop)) {
                $sql->where('hm.`id_shop` = '.(int)$context->shop->id);
            }

            if ($frontend) {
                if ($use_groups) {
                    $sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
                    if (Validate::isLoadedObject($context->shop)) {
                        $sql->where('mg.id_shop = '.((int)$context->shop->id).(count($groups) ? ' AND  mg.`id_group` IN ('.implode(', ', $groups).')' : ''));
                    } elseif (count($groups)) {
                        $sql->where('mg.`id_group` IN ('.implode(', ', $groups).')');
                    }
                }
            }

            $sql->groupBy('hm.id_hook, hm.id_module');
            $sql->orderBy('hm.`position`');

            $list = array();
            if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($result as $row) {
                    $row['hook'] = strtolower($row['hook']);
                    if (!isset($list[$row['hook']])) {
                        $list[$row['hook']] = array();
                    }

                    $list[$row['hook']][] = array(
                        'id_hook' => $row['id_hook'],
                        'module' => $row['module'],
                        'id_module' => $row['id_module'],
                    );
                }
            }
            if ($hook_name != 'displayPayment' && $hook_name != 'displayPaymentEU' && $hook_name != 'paymentOptions' && $hook_name != 'displayBackOfficeHeader') {
                Cache::store($cache_id, $list);
                // @todo remove this in 1.6, we keep it in 1.5 for backward compatibility
                self::$_hook_modules_cache_exec = $list;
            }
        } else {
            $list = Cache::retrieve($cache_id);
        }

        // If hook_name is given, just get list of modules for this hook
        if ($hook_name) {
            $retro_hook_name = strtolower(Hook::getRetroHookName($hook_name));
            $hook_name = strtolower($hook_name);

            $return = array();
            $inserted_modules = array();
            if (isset($list[$hook_name])) {
                $return = $list[$hook_name];
            }
            foreach ($return as $module) {
                $inserted_modules[] = $module['id_module'];
            }
            if (isset($list[$retro_hook_name])) {
                foreach ($list[$retro_hook_name] as $retro_module_call) {
                    if (!in_array($retro_module_call['id_module'], $inserted_modules)) {
                        $return[] = $retro_module_call;
                    }
                }
            }

            return (count($return) > 0 ? $return : false);
        } else {
            return $list;
        }
    }

    /**
     * Execute modules for specified hook
     *
     * @param string $hook_name Hook Name
     * @param array $hook_args Parameters for the functions
     * @param int $id_module Execute hook for this module only
     * @param bool $array_return If specified, module output will be set by name in an array
     * @param bool $check_exceptions Check permission exceptions
     * @param bool $use_push Force change to be refreshed on Dashboard widgets
     * @param int $id_shop If specified, hook will be execute the shop with this ID
     * @param bool $chain If specified, hook will chain the return of hook module
     *
     * @throws PrestaShopException
     *
     * @return string/array modules output
     */
    public static function exec(
        $hook_name,
        $hook_args = array(),
        $id_module = null,
        $array_return = false,
        $check_exceptions = true,
        $use_push = false,
        $id_shop = null,
        $chain = false
    )
    {
        if (defined('PS_INSTALLATION_IN_PROGRESS')) {
            return;
        }

        // $chain & $array_return are incompatible so if chained is set to true, we disable the array_return option
        if (true === $chain) {
            $array_return = false;
        }

        static $disable_non_native_modules = null;
        if ($disable_non_native_modules === null) {
            $disable_non_native_modules = (bool)Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
        }

        // Check arguments validity
        if (($id_module && !is_numeric($id_module)) || !Validate::isHookName($hook_name)) {
            throw new PrestaShopException('Invalid id_module or hook_name');
        }

        // If no modules associated to hook_name or recompatible hook name, we stop the function

        if (!$module_list = Hook::getHookModuleExecList($hook_name)) {
            if ($array_return) {
                return array();
            } else {
                return '';
            }
        }

        // Check if hook exists
        if (!$id_hook = Hook::getIdByName($hook_name)) {
            if ($array_return) {
                return array();
            } else {
                return false;
            }
        }

        if (array_key_exists($hook_name, self::$deprecated_hooks)) {
            $deprecVersion = isset(self::$deprecated_hooks[$hook_name]['from'])?
                    self::$deprecated_hooks[$hook_name]['from']:
                    _PS_VERSION_;
            Tools::displayAsDeprecated('The hook '. $hook_name .' is deprecated in PrestaShop v.'. $deprecVersion);
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
        if ($array_return) {
            $output = array();
        } else {
            $output = '';
        }

        if ($disable_non_native_modules && !isset(Hook::$native_module)) {
            Hook::$native_module = Module::getNativeModuleList();
        }

        $different_shop = false;
        if ($id_shop !== null && Validate::isUnsignedId($id_shop) && $id_shop != $context->shop->getContextShopID()) {
            $old_context = $context->shop->getContext();
            $old_shop = clone $context->shop;
            $shop = new Shop((int)$id_shop);
            if (Validate::isLoadedObject($shop)) {
                $context->shop = $shop;
                $context->shop->setContext(Shop::CONTEXT_SHOP, $shop->id);
                $different_shop = true;
            }
        }

        foreach ($module_list as $key => $array) {
            // Check errors
            if ($id_module && $id_module != $array['id_module']) {
                continue;
            }

            if ((bool)$disable_non_native_modules && Hook::$native_module && count(Hook::$native_module) && !in_array($array['module'], Hook::$native_module)) {
                continue;
            }

            // Check permissions
            if ($check_exceptions) {
                $exceptions = Module::getExceptionsStatic($array['id_module'], $array['id_hook']);

                $controller = Dispatcher::getInstance()->getController();
                $controller_obj = Context::getContext()->controller;

                //check if current controller is a module controller
                if (isset($controller_obj->module) && Validate::isLoadedObject($controller_obj->module)) {
                    $controller = 'module-'.$controller_obj->module->name.'-'.$controller;
                }

                if (in_array($controller, $exceptions)) {
                    continue;
                }

                //Backward compatibility of controller names
                $matching_name = array(
                    'authentication' => 'auth'
                );
                if (isset($matching_name[$controller]) && in_array($matching_name[$controller], $exceptions)) {
                    continue;
                }
                if (Validate::isLoadedObject($context->employee) && !Module::getPermissionStatic($array['id_module'], 'view', $context->employee)) {
                    continue;
                }
            }

            if (!($moduleInstance = Module::getInstanceByName($array['module']))) {
                continue;
            }

            if ($use_push && !$moduleInstance->allow_push) {
                continue;
            }

            if (Hook::isHookCallableOn($moduleInstance, $hook_name)) {
                $hook_args['altern'] = ++$altern;

                if ($use_push && isset($moduleInstance->push_filename) && file_exists($moduleInstance->push_filename)) {
                    Tools::waitUntilFileIsModified($moduleInstance->push_filename, $moduleInstance->push_time_limit);
                }

                if (0 !== $key && true === $chain) {
                    $hook_args = $output;
                }

                $display = Hook::callHookOn($moduleInstance, $hook_name, $hook_args);

                if ($array_return) {
                    $output[$moduleInstance->name] = $display;
                } else {
                    if (true === $chain) {
                        $output = $display;
                    } else {
                        $output .= $display;
                    }
                }
            } elseif (Hook::isDisplayHookName($hook_name)) {
                if ($moduleInstance instanceof WidgetInterface) {

                    if (0 !== $key && true === $chain) {
                        $hook_args = $output;
                    }

                    $display = Hook::coreRenderWidget($moduleInstance, $hook_name, $hook_args);

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
            }
        }

        if ($different_shop) {
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

        return $output;
    }

    public static function coreCallHook($module, $method, $params)
    {
        return $module->{$method}($params);
    }

    public static function coreRenderWidget($module, $hook_name, $params)
    {
        return $module->renderWidget($hook_name, $params);
    }
}
