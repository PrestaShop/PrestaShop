<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
     * Adds current Hook as a new Object to the database
     *
     * @param bool $autoDate   Automatically set `date_upd` and `date_add` columns
     * @param bool $nullValues Whether we want to use NULL values instead of empty quotes values
     *
     * @return bool Indicates whether the Hook has been successfully added
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
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

    public function add($autoDate = true, $nullValues = false)
    {
        Cache::clean('hook_idsbyname');

        return parent::add($autoDate, $nullValues);
    }

    /**
     * Normalize hook name
     *
     * @param string $hookName
     *
     * @return string
     */
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

    /**
     * Is diplay hook name
     *
     * @param string $hookName
     *
     * @return bool
     */
    public static function isDisplayHookName($hookName)
    {
        $hookName = strtolower(self::normalizeHookName($hookName));

        if ($hookName === 'header' || $hookName === 'displayheader') {
            // this hook is to add resources to the <head> section of the page
            // so it doesn't display anything by itself
            return false;
        }

        return strpos($hookName, 'display') === 0;
    }

    /**
     * Return Hooks List
     *
     * @param bool $position
     *
     * @return array Hooks List
     */
    public static function getHooks($position = false, $onlyDisplayHooks = false)
    {
        $hooks = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT * FROM `'._DB_PREFIX_.'hook` h
			'.($position ? 'WHERE h.`position` = 1' : '').'
			ORDER BY `name`'
        );

        if ($onlyDisplayHooks) {
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
     * @param string $hookName Hook name
     *
     * @return int Hook ID
     */
    public static function getIdByName($hookName)
    {
        $hookName = strtolower($hookName);
        if (!Validate::isHookName($hookName)) {
            return false;
        }

        $cacheId = 'hook_idsbyname';
        if (!Cache::isStored($cacheId)) {
            // Get all hook ID by name and alias
            $hookIds = array();
            $db = Db::getInstance();
            $result = $db->ExecuteS('
			SELECT `id_hook`, `name`
			FROM `'._DB_PREFIX_.'hook`
			UNION
			SELECT `id_hook`, ha.`alias` as name
			FROM `'._DB_PREFIX_.'hook_alias` ha
			INNER JOIN `'._DB_PREFIX_.'hook` h ON ha.name = h.name', false);
            while ($row = $db->nextRow($result)) {
                $hookIds[strtolower($row['name'])] = $row['id_hook'];
            }
            Cache::store($cacheId, $hookIds);
        } else {
            $hookIds = Cache::retrieve($cacheId);
        }

        return (isset($hookIds[$hookName]) ? $hookIds[$hookName] : false);
    }

    /**
     * Return hook ID from name
     */
    public static function getNameById($hookId)
    {
        $cacheId = 'hook_namebyid_'.$hookId;
        if (!Cache::isStored($cacheId)) {
            $result = Db::getInstance()->getValue('
							SELECT `name`
							FROM `'._DB_PREFIX_.'hook`
							WHERE `id_hook` = '.(int) $hookId);
            Cache::store($cacheId, $result);

            return $result;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Get list of hook alias
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getHookAliasList()
    {
        $cacheId = 'hook_alias';
        if (!Cache::isStored($cacheId)) {
            $hookAliasList = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'hook_alias`');
            $hookAlias = array();
            if ($hookAliasList) {
                foreach ($hookAliasList as $ha) {
                    $hookAlias[strtolower($ha['alias'])] = $ha['name'];
                }
            }
            Cache::store($cacheId, $hookAlias);

            return $hookAlias;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Return backward compatibility hook name
     *
     * @param string $hookName Hook name
     *
     * @return int Hook ID
     *
     * @since 1.5.0
     */
    public static function getRetroHookName($hookName)
    {
        $aliasList = Hook::getHookAliasList();
        if (isset($aliasList[strtolower($hookName)])) {
            return $aliasList[strtolower($hookName)];
        }

        $retroHookName = array_search($hookName, $aliasList);
        if ($retroHookName === false) {
            return '';
        }

        return $retroHookName;
    }

    /**
     * Get list of all registered hooks with modules
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getHookModuleList()
    {
        $cacheId = 'hook_module_list';
        if (!Cache::isStored($cacheId)) {
            $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT h.id_hook, h.name as h_name, title, description, h.position, hm.position as hm_position, m.id_module, m.name, active
			FROM `'._DB_PREFIX_.'hook_module` hm
			STRAIGHT_JOIN `'._DB_PREFIX_.'hook` h ON (h.id_hook = hm.id_hook AND hm.id_shop = '.(int) Context::getContext()->shop->id.')
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
            Cache::store($cacheId, $list);

            return $list;
        }

        return Cache::retrieve($cacheId);
    }

    /**
     * Return Hooks List
     *
     * @param int $idHook
     * @param int $idModule
     *
     * @return array Modules List
     *
     * @since 1.5.0
     */
    public static function getModulesFromHook($idHook, $idModule = null)
    {
        $hm_list = Hook::getHookModuleList();
        $module_list = (isset($hm_list[$idHook])) ? $hm_list[$idHook] : array();

        if ($idModule) {
            return (isset($module_list[$idModule])) ? array($module_list[$idModule]) : array();
        }
        return $module_list;
    }

    /**
     * Is the Module registered on the given Hook?
     *
     * @param Module $moduleInstance Module instance
     * @param string $hookName       Hook name
     * @param int    $idShop         Shop ID
     *
     * @return bool Indicates whether the Module is registered on the given Hook
     */
    public static function isModuleRegisteredOnHook($moduleInstance, $hookName, $idShop)
    {
        $prefix = _DB_PREFIX_;
        $id_hook = (int) Hook::getIdByName($hookName);
        $idShop = (int) $idShop;
        $id_module = (int) $moduleInstance->id;

        $sql = "SELECT * FROM {$prefix}hook_module
                  WHERE `id_hook` = {$id_hook}
                  AND `id_module` = {$id_module}
                  AND `id_shop` = {$idShop}";

        $rows = Db::getInstance()->executeS($sql);

        return !empty($rows);
    }

    /**
     * Register Hook
     *
     * @param Module     $moduleInstance
     * @param string     $hookName
     * @param array|null $shopList
     *
     * @return bool
     * @throws PrestaShopException
     */
    public static function registerHook($moduleInstance, $hookName, $shopList = null)
    {
        $return = true;
        if (is_array($hookName)) {
            $hookNames = $hookName;
        } else {
            $hookNames = array($hookName);
        }

        foreach ($hookNames as $hookName) {
            // Check hook name validation and if module is installed
            if (!Validate::isHookName($hookName)) {
                throw new PrestaShopException('Invalid hook name');
            }
            if (!isset($moduleInstance->id) || !is_numeric($moduleInstance->id)) {
                return false;
            }

            // Retrocompatibility
            if ($alias = Hook::getRetroHookName($hookName)) {
                $hookName = $alias;
            }

            Hook::exec('actionModuleRegisterHookBefore', array('object' => $moduleInstance, 'hook_name' => $hookName));
            // Get hook id
            $idHook = Hook::getIdByName($hookName);

            // If hook does not exist, we create it
            if (!$idHook) {
                $newHook = new Hook();
                $newHook->name = pSQL($hookName);
                $newHook->title = pSQL($hookName);
                $newHook->add();
                $idHook = $newHook->id;
                if (!$idHook) {
                    return false;
                }
            }

            // If shop lists is null, we fill it with all shops
            if (is_null($shopList)) {
                $shopList = Shop::getCompleteListOfShopsID();
            }

            $shopListEmployee = Shop::getShops(true, null, true);

            foreach ($shopList as $shopId) {
                // Check if already register
                $sql = 'SELECT hm.`id_module`
                    FROM `'._DB_PREFIX_.'hook_module` hm, `'._DB_PREFIX_.'hook` h
                    WHERE hm.`id_module` = '.(int) $moduleInstance->id.' AND h.`id_hook` = '.$idHook.'
                    AND h.`id_hook` = hm.`id_hook` AND `id_shop` = '.(int) $shopId;
                if (Db::getInstance()->getRow($sql)) {
                    continue;
                }

                // Get module position in hook
                $sql = 'SELECT MAX(`position`) AS position
                    FROM `'._DB_PREFIX_.'hook_module`
                    WHERE `id_hook` = '.(int) $idHook.' AND `id_shop` = '.(int) $shopId;
                if (!$position = Db::getInstance()->getValue($sql)) {
                    $position = 0;
                }

                // Register module in hook
                $return &= Db::getInstance()->insert('hook_module', array(
                    'id_module' => (int) $moduleInstance->id,
                    'id_hook' => (int) $idHook,
                    'id_shop' => (int) $shopId,
                    'position' => (int) ($position + 1),
                ));

                if (!in_array($shopId, $shopListEmployee)) {
                    $where = '`id_module` = '.(int) $moduleInstance->id.' AND `id_shop` = '.(int) $shopId;
                    $return &= Db::getInstance()->delete('module_shop', $where);
                }
            }

            Hook::exec('actionModuleRegisterHookAfter', array('object' => $moduleInstance, 'hook_name' => $hookName));
        }

        return $return;
    }

    /**
     * Unregister Hook
     *
     * @param Module     $moduleInstance
     * @param string     $hookName
     * @param array|null $shopList
     *
     * @return bool
     */
    public static function unregisterHook($moduleInstance, $hookName, $shopList = null)
    {
        if (is_numeric($hookName)) {
            // $hook_name passed it the id_hook
            $hookId = $hookName;
            $hookName = Hook::getNameById((int) $hookId);
        } else {
            $hookId = Hook::getIdByName($hookName);
        }

        if (!$hookId) {
            return false;
        }

        Hook::exec('actionModuleUnRegisterHookBefore', array('object' => $moduleInstance, 'hook_name' => $hookName));

        // Unregister module on hook by id
        $sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
            WHERE `id_module` = '.(int)$moduleInstance->id.' AND `id_hook` = '.(int) $hookId
            .(($shopList) ? ' AND `id_shop` IN('.implode(', ', array_map('intval', $shopList)).')' : '');
        $result = Db::getInstance()->execute($sql);

        // Clean modules position
        $moduleInstance->cleanPositions($hookId, $shopList);

        Hook::exec('actionModuleUnRegisterHookAfter', array('object' => $moduleInstance, 'hook_name' => $hookName));

        return $result;
    }

    /**
     * Get list of modules we can execute per hook
     *
     * @param string $hookName Get list of modules for this hook if given
     *
     * @return array
     *
     * @since 1.5.0
     */
    public static function getHookModuleExecList($hookName = null)
    {
        $context = Context::getContext();
        $cacheId = self::MODULE_LIST_BY_HOOK_KEY.(isset($context->shop->id) ? '_'.$context->shop->id : '').((isset($context->customer)) ? '_'.$context->customer->id : '');
        if (!Cache::isStored($cacheId) || $hookName == 'displayPayment' || $hookName == 'displayPaymentEU' || $hookName == 'paymentOptions' || $hookName == 'displayBackOfficeHeader') {
            $frontend = true;
            $groups = array();
            $useGroups = Group::isFeatureActive();
            if (isset($context->employee)) {
                $frontend = false;
            } else {
                // Get groups list
                if ($useGroups) {
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
            if ($hookName != 'displayBackOfficeHeader') {
                $sql->join(Shop::addSqlAssociation('module', 'm', true, 'module_shop.enable_device & '.(int) Context::getContext()->getDevice()));
                $sql->innerJoin('module_shop', 'ms', 'ms.`id_module` = m.`id_module`');
            }
            $sql->innerJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
            $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
            if ($hookName != 'paymentOptions') {
                $sql->where('h.`name` != "paymentOptions"');
            } elseif ($frontend) {
                // For payment modules, we check that they are available in the contextual country
                if (Validate::isLoadedObject($context->country)) {
                    $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions")AND (SELECT `id_country` FROM `'._DB_PREFIX_.'module_country` mc WHERE mc.`id_module` = m.`id_module` AND `id_country` = '.(int) $context->country->id.' AND `id_shop` = '.(int) $context->shop->id.' LIMIT 1) = '.(int) $context->country->id.')');
                }
                if (Validate::isLoadedObject($context->currency)) {
                    $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions") AND (SELECT `id_currency` FROM `'._DB_PREFIX_.'module_currency` mcr WHERE mcr.`id_module` = m.`id_module` AND `id_currency` IN ('.(int) $context->currency->id.', -1, -2) LIMIT 1) IN ('.(int) $context->currency->id.', -1, -2))');
                }
                if (Validate::isLoadedObject($context->cart)) {
                    $carrier = new Carrier($context->cart->id_carrier);
                    if (Validate::isLoadedObject($carrier)) {
                        $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions") AND (SELECT `id_reference` FROM `'._DB_PREFIX_.'module_carrier` mcar WHERE mcar.`id_module` = m.`id_module` AND `id_reference` = '.(int) $carrier->id_reference.' AND `id_shop` = '.(int) $context->shop->id.' LIMIT 1) = '.(int) $carrier->id_reference.')');
                    }
                }
            }
            if (Validate::isLoadedObject($context->shop)) {
                $sql->where('hm.`id_shop` = '.(int) $context->shop->id);
            }

            if ($frontend) {
                if ($useGroups) {
                    $sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
                    if (Validate::isLoadedObject($context->shop)) {
                        $sql->where('mg.id_shop = '.((int) $context->shop->id).(count($groups) ? ' AND  mg.`id_group` IN ('.implode(', ', $groups).')' : ''));
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
            if ($hookName != 'displayPayment' && $hookName != 'displayPaymentEU' && $hookName != 'paymentOptions' && $hookName != 'displayBackOfficeHeader') {
                Cache::store($cacheId, $list);
            }
        } else {
            $list = Cache::retrieve($cacheId);
        }

        // If hook_name is given, just get list of modules for this hook
        if ($hookName) {
            $retroHookName = strtolower(Hook::getRetroHookName($hookName));
            $hookName = strtolower($hookName);

            $return = array();
            $insertedModules = array();
            if (isset($list[$hookName])) {
                $return = $list[$hookName];
            }
            foreach ($return as $module) {
                $insertedModules[] = $module['id_module'];
            }
            if (isset($list[$retroHookName])) {
                foreach ($list[$retroHookName] as $retroModuleCall) {
                    if (!in_array($retroModuleCall['id_module'], $insertedModules)) {
                        $return[] = $retroModuleCall;
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
     * @param string $hookName        Hook Name
     * @param array  $hookArgs        Parameters for the functions
     * @param int    $idModule        Execute hook for this module only
     * @param bool   $arrayReturn     If specified, module output will be set by name in an array
     * @param bool   $checkExceptions Check permission exceptions
     * @param bool   $usePush         Force change to be refreshed on Dashboard widgets
     * @param int    $idShop          If specified, hook will be execute the shop with this ID
     *
     * @throws PrestaShopException
     *
     * @return string|array modules output
     */
    public static function exec($hookName, $hookArgs = array(), $idModule = null, $arrayReturn = false, $checkExceptions = true, $usePush = false, $idShop = null)
    {
        if (defined('PS_INSTALLATION_IN_PROGRESS')) {
            return;
        }

        static $disableNonNativeModules = null;
        if ($disableNonNativeModules === null) {
            $disableNonNativeModules = (bool) Configuration::get('PS_DISABLE_NON_NATIVE_MODULE');
        }

        // Check arguments validity
        if (($idModule && !is_numeric($idModule)) || !Validate::isHookName($hookName)) {
            throw new PrestaShopException('Invalid id_module or hook_name');
        }

        // If no modules associated to hook_name or recompatible hook name, we stop the function

        if (!$moduleList = Hook::getHookModuleExecList($hookName)) {
            return '';
        }

        // Check if hook exists
        if (!$idHook = Hook::getIdByName($hookName)) {
            return false;
        }

        if (array_key_exists($hookName, self::$deprecated_hooks)) {
            $deprecVersion = isset(self::$deprecated_hooks[$hookName]['from'])?
                    self::$deprecated_hooks[$hookName]['from']:
                    _PS_VERSION_;
            Tools::displayAsDeprecated('The hook '. $hookName .' is deprecated in PrestaShop v.'. $deprecVersion);
        }

        // Store list of executed hooks on this page
        Hook::$executed_hooks[$idHook] = $hookName;

        $context = Context::getContext();
        if (!isset($hookArgs['cookie']) || !$hookArgs['cookie']) {
            $hookArgs['cookie'] = $context->cookie;
        }
        if (!isset($hookArgs['cart']) || !$hookArgs['cart']) {
            $hookArgs['cart'] = $context->cart;
        }

        $retroHookName = Hook::getRetroHookName($hookName);

        // Look on modules list
        $altern = 0;
        if ($arrayReturn) {
            $output = array();
        } else {
            $output = '';
        }

        if ($disableNonNativeModules && !isset(Hook::$native_module)) {
            Hook::$native_module = Module::getNativeModuleList();
        }

        $differentShop = false;
        if ($idShop !== null && Validate::isUnsignedId($idShop) && $idShop != $context->shop->getContextShopID()) {
            $oldContext = $context->shop->getContext();
            $oldShop = clone $context->shop;
            $shop = new Shop((int) $idShop);
            if (Validate::isLoadedObject($shop)) {
                $context->shop = $shop;
                $context->shop->setContext(Shop::CONTEXT_SHOP, $shop->id);
                $differentShop = true;
            }
        }

        foreach ($moduleList as $array) {
            // Check errors
            if ($idModule && $idModule != $array['id_module']) {
                continue;
            }

            if ((bool) $disableNonNativeModules && Hook::$native_module && count(Hook::$native_module) && !in_array($array['module'], Hook::$native_module)) {
                continue;
            }

            // Check permissions
            if ($checkExceptions) {
                $exceptions = Module::getExceptionsStatic($array['id_module'], $array['id_hook']);

                $controller = Dispatcher::getInstance()->getController();
                $controllerObj = Context::getContext()->controller;

                //check if current controller is a module controller
                if (isset($controllerObj->module) && Validate::isLoadedObject($controllerObj->module)) {
                    $controller = 'module-'.$controllerObj->module->name.'-'.$controller;
                }

                if (in_array($controller, $exceptions)) {
                    continue;
                }

                //Backward compatibility of controller names
                $matchingName = array(
                    'authentication' => 'auth',
                );
                if (isset($matchingName[$controller]) && in_array($matchingName[$controller], $exceptions)) {
                    continue;
                }
                if (Validate::isLoadedObject($context->employee) && !Module::getPermissionStatic($array['id_module'], 'view', $context->employee)) {
                    continue;
                }
            }

            if (!($moduleInstance = Module::getInstanceByName($array['module']))) {
                continue;
            }

            if ($usePush && !$moduleInstance->allow_push) {
                continue;
            }
            // Check which / if method is callable
            $hookCallable = is_callable(array($moduleInstance, 'hook'.$hookName));
            $hookRetroCallable = is_callable(array($moduleInstance, 'hook'.$retroHookName));

            if ($hookCallable || $hookRetroCallable) {
                $hookArgs['altern'] = ++$altern;

                if ($usePush && isset($moduleInstance->push_filename) && file_exists($moduleInstance->push_filename)) {
                    Tools::waitUntilFileIsModified($moduleInstance->push_filename, $moduleInstance->push_time_limit);
                }

                // Call hook method
                if ($hookCallable) {
                    $display = Hook::coreCallHook($moduleInstance, 'hook'.$hookName, $hookArgs);
                } elseif ($hookRetroCallable) {
                    $display = Hook::coreCallHook($moduleInstance, 'hook'.$retroHookName, $hookArgs);
                } else {
                    continue;
                }

                if ($arrayReturn) {
                    $output[$moduleInstance->name] = $display;
                } else {
                    $output .= $display;
                }
            } elseif (Hook::isDisplayHookName($hookName)) {
                if ($moduleInstance instanceof WidgetInterface) {
                    $display = Hook::coreRenderWidget($moduleInstance, $hookName, $hookArgs);

                    if ($arrayReturn) {
                        $output[$moduleInstance->name] = $display;
                    } else {
                        $output .= $display;
                    }
                }
            }
        }

        if ($differentShop && isset($oldShop) && isset($oldContext) && isset($shop)) {
            $context->shop = $oldShop;
            $context->shop->setContext($oldContext, $shop->id);
        }

        return $output;
    }

    /**
     * Core call hook
     *
     * @param $module
     * @param $method
     * @param $params
     *
     * @return mixed
     */
    public static function coreCallHook($module, $method, $params)
    {
        return $module->{$method}($params);
    }

    /**
     * Core render widget
     *
     * @param $module
     * @param $hookName
     * @param $params
     *
     * @return mixed
     */
    public static function coreRenderWidget($module, $hookName, $params)
    {
        return $module->renderWidget($hookName, $params);
    }
}
