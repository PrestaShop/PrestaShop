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

global $smarty;
if (Configuration::get('PS_SMARTY_LOCAL')) {
    $smarty = new SmartyCustom();
} elseif (_PS_MODE_DEV_ && !defined('_PS_ADMIN_DIR_')) {
    $smarty = new SmartyDev();
} else {
    $smarty = new Smarty();
}

$smarty->setConfigDir([]);
$smarty->setCompileDir(_PS_CACHE_DIR_.'smarty/compile');
$smarty->setCacheDir(_PS_CACHE_DIR_.'smarty/cache');
$smarty->use_sub_dirs = true;
$smarty->caching = false;

if (Configuration::get('PS_SMARTY_CACHING_TYPE') == 'mysql') {
    include _PS_CLASS_DIR_.'Smarty/SmartyCacheResourceMysql.php';
    $smarty->caching_type = 'mysql';
}
$smarty->force_compile = (Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_FORCE_COMPILE_) ? true : false;
$smarty->compile_check = (Configuration::get('PS_SMARTY_FORCE_COMPILE') >= _PS_SMARTY_CHECK_COMPILE_) ? true : false;
$smarty->debug_tpl = _PS_ALL_THEMES_DIR_.'debug.tpl';

/* Use this constant if you want to load smarty without all PrestaShop functions */
if (defined('_PS_SMARTY_FAST_LOAD_') && _PS_SMARTY_FAST_LOAD_) {
    return;
}

if (defined('_PS_ADMIN_DIR_')) {
    require_once dirname(__FILE__).'/smartyadmin.config.inc.php';
} else {
    require_once dirname(__FILE__).'/smartyfront.config.inc.php';
}

require_once SMARTY_PLUGINS_DIR.'modifier.truncate.php';

// This escape modifier is required for invoice PDF generation
function smartyEscape($string, $esc_type = 'html', $char_set = null, $double_encode = true)
{
    $escapeModifierFile = implode(
        DIRECTORY_SEPARATOR,
        array(
            SMARTY_PLUGINS_DIR,
            'modifier.escape.php',
        )
    );
    require_once $escapeModifierFile;

    global $smarty;
    if (($esc_type === 'html' || $esc_type === 'htmlall') && $smarty->escape_html) {
        return $string;
    } else {
        return smarty_modifier_escape($string, $esc_type, $char_set, $double_encode);
    }
}

smartyRegisterFunction($smarty, 'modifier', 'escape', 'smartyEscape');
smartyRegisterFunction($smarty, 'modifier', 'truncate', 'smarty_modifier_truncate');
smartyRegisterFunction($smarty, 'function', 'l', 'smartyTranslate', false);
smartyRegisterFunction($smarty, 'function', 'hook', 'smartyHook');
smartyRegisterFunction($smarty, 'modifier', 'json_encode', array('Tools', 'jsonEncode'));
smartyRegisterFunction($smarty, 'modifier', 'json_decode', array('Tools', 'jsonDecode'));
smartyRegisterFunction($smarty, 'function', 'dateFormat', array('Tools', 'dateFormat'));
smartyRegisterFunction($smarty, 'modifier', 'boolval', array('Tools', 'boolval'));
smartyRegisterFunction($smarty, 'modifier', 'cleanHtml', 'smartyCleanHtml');
smartyRegisterFunction($smarty, 'modifier', 'classname', 'smartyClassname');
smartyRegisterFunction($smarty, 'modifier', 'classnames', 'smartyClassnames');
smartyRegisterFunction($smarty, 'function', 'url', array('Link', 'getUrlSmarty'));

function smarty_modifier_htmlentitiesUTF8($string)
{
    return Tools::htmlentitiesUTF8($string);
}

function smartyRegisterFunction($smarty, $type, $function, $params, $lazy = true, $initial_lazy_register = null)
{
    if (!in_array($type, array('function', 'modifier', 'block'))) {
        return false;
    }

    // lazy is better if the function is not called on every page
    if ($lazy) {
        if (null !== $initial_lazy_register && $initial_lazy_register->isRegistered($params)) {
            return;
        }

        $lazy_register = SmartyLazyRegister::getInstance($smarty);
        if ($lazy_register->isRegistered($params)) {
            return;
        }
        $lazy_register->register($params);

        if (is_array($params)) {
            $params = $params[1];
        }

        // SmartyLazyRegister allows to only load external class when they are needed
        $smarty->registerPlugin($type, $function, array($lazy_register, $params));
    } else {
        $smarty->registerPlugin($type, $function, $params);
    }
}

function smartyHook($params, &$smarty)
{
    $id_module = null;
    $hook_params = $params;
    $hook_params['smarty'] = $smarty;
    if (!empty($params['mod'])) {
        $module = Module::getInstanceByName($params['mod']);
        unset($hook_params['mod']);
        if ($module && $module->id) {
            $id_module = $module->id;
        } else {
            unset($hook_params['h']);

            return '';
        }
    }
    if (!empty($params['excl'])) {
        $result = '';
        $modules = Hook::getHookModuleExecList($hook_params['h']);

        $moduleexcl = explode(',', $params['excl']);
        foreach ($modules as $module) {
            if (!in_array($module['module'], $moduleexcl)) {
                $result .= Hook::exec($params['h'], $hook_params, $module['id_module']);
            }
        }

        unset(
            $hook_params['h'],
            $hook_params['excl']
        );

        return $result;
    }
    unset($hook_params['h']);

    return Hook::exec($params['h'], $hook_params, $id_module);
}

function smartyCleanHtml($data)
{
    // Prevent xss injection.
    if (Validate::isCleanHtml($data)) {
        return $data;
    }
}

function smartyClassname($classname)
{
    $classname = Tools::replaceAccentedChars(strtolower($classname));
    $classname = preg_replace('/[^A-Za-z0-9]/', '-', $classname);
    $classname = preg_replace('/[-]+/', '-', $classname);

    return $classname;
}

function smartyClassnames(array $classnames)
{
    $enabled_classes = array();
    foreach ($classnames as $classname => $enabled) {
        if ($enabled) {
            $enabled_classes[] = smartyClassname($classname);
        }
    }

    return implode(' ', $enabled_classes);
}
