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

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

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
$smarty->caching = Smarty::CACHING_OFF;

/* @phpstan-ignore-next-line */
if (_PS_SMARTY_CACHING_TYPE_ == 'mysql') {
    include _PS_CLASS_DIR_.'Smarty/SmartyCacheResourceMysql.php';
    $smarty->caching_type = 'mysql';
}
$smarty->force_compile = Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_FORCE_COMPILE_;
$smarty->compile_check = (Configuration::get('PS_SMARTY_FORCE_COMPILE') >= _PS_SMARTY_CHECK_COMPILE_) ? Smarty::COMPILECHECK_ON : Smarty::COMPILECHECK_OFF;
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
smartyRegisterFunction($smarty, 'function', 'dateFormat', array('Tools', 'dateFormat'));
smartyRegisterFunction($smarty, 'modifier', 'boolval', array('Tools', 'boolval'));
smartyRegisterFunction($smarty, 'modifier', 'cleanHtml', 'smartyCleanHtml');
smartyRegisterFunction($smarty, 'modifier', 'classname', 'smartyClassname');
smartyRegisterFunction($smarty, 'modifier', 'classnames', 'smartyClassnames');
smartyRegisterFunction($smarty, 'function', 'url', array('Link', 'getUrlSmarty'));
smartyRegisterFunction($smarty, 'function', 'render_template', 'renderTemplate');

// Native PHP Functions
smartyRegisterFunction($smarty, 'modifier', 'addcslashes', 'addcslashes');
smartyRegisterFunction($smarty, 'modifier', 'addslashes', 'addslashes');
smartyRegisterFunction($smarty, 'modifier', 'date','date');
smartyRegisterFunction($smarty, 'modifier', 'end', 'smarty_endWithoutReference');
smartyRegisterFunction($smarty, 'modifier', 'floatval', 'floatval');
smartyRegisterFunction($smarty, 'modifier', 'htmlentities', 'htmlentities');
smartyRegisterFunction($smarty, 'modifier', 'intval', 'intval');
smartyRegisterFunction($smarty, 'modifier', 'json_decode', 'json_decode');
smartyRegisterFunction($smarty, 'modifier', 'json_encode', 'json_encode');
smartyRegisterFunction($smarty, 'modifier', 'mt_rand','mt_rand');
smartyRegisterFunction($smarty, 'modifier', 'rand','rand');
smartyRegisterFunction($smarty, 'modifier', 'strtolower','strtolower');
smartyRegisterFunction($smarty, 'modifier', 'str_replace','str_replace');
smartyRegisterFunction($smarty, 'modifier', 'strval','strval');
smartyRegisterFunction($smarty, 'modifier', 'trim', 'trim');
smartyRegisterFunction($smarty, 'modifier', 'ucfirst', 'ucfirst');
smartyRegisterFunction($smarty, 'modifier', 'urlencode','urlencode');
smartyRegisterFunction($smarty, 'modifier', 'htmlspecialchars','htmlspecialchars');
smartyRegisterFunction($smarty, 'modifier', 'implode', 'implode');
smartyRegisterFunction($smarty, 'modifier', 'explode', 'explode');
smartyRegisterFunction($smarty, 'modifier', 'print_r', 'print_r');
smartyRegisterFunction($smarty, 'modifier', 'var_dump', 'var_dump');
smartyRegisterFunction($smarty, 'modifier', 'lcfirst', 'lcfirst');
smartyRegisterFunction($smarty, 'modifier', 'nl2br', 'nl2br');

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

function smartyClassname(string $classname)
{
    $classname = Tools::replaceAccentedChars(strtolower($classname));
    $classname = preg_replace(['/[^A-Za-z0-9-_]/', '/-{3,}/', '/-+$/'], ['-', '-', ''] , $classname);
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

/**
 * We add this intermediate method to prevent a warning because end expects its input to be a reference
 *
 * @param array<mixed> $arrayValue
 *
 * @return false|mixed
 */
function smarty_endWithoutReference($arrayValue)
{
    return end($arrayValue);
}

function renderTemplate(array $params) {
    $twigTemplate = $params['twig_template'];
    unset($params['twig_template']);
    $smartyTemplate = $params['smarty_template'];
    unset($params['smarty_template']);

    if (
        SymfonyContainer::getInstance()
            ->get(FeatureFlagStateCheckerInterface::class)
            ->isEnabled(FeatureFlagSettings::FEATURE_FLAG_SYMFONY_LAYOUT)
    ) {
        /** @var Twig\Environment $twig */
        $twig = SymfonyContainer::getInstance()->get('twig');
        return $twig->render($twigTemplate, $params);
    } else {
        global $smarty;
        $smarty->display($smartyTemplate);
    }

}
