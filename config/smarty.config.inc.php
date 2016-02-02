<?php
/**
 * 2007-2015 PrestaShop
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

define('_PS_SMARTY_DIR_', _PS_VENDOR_DIR_.'prestashop/smarty/');

global $smarty;
if (Configuration::get('PS_SMARTY_LOCAL')) {
    $smarty = new SmartyCustom();
} else {
    $smarty = new Smarty();
}

$smarty->setCompileDir(_PS_CACHE_DIR_.'smarty/compile');
$smarty->setCacheDir(_PS_CACHE_DIR_.'smarty/cache');
if (!Tools::getSafeModeStatus()) {
    $smarty->use_sub_dirs = true;
}
$smarty->setConfigDir(_PS_SMARTY_DIR_.'configs');
$smarty->caching = false;
if (Configuration::get('PS_SMARTY_CACHING_TYPE') == 'mysql') {
    include(_PS_CLASS_DIR_.'/SmartyCacheResourceMysql.php');
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
    require_once(dirname(__FILE__).'/smartyadmin.config.inc.php');
} else {
    require_once(dirname(__FILE__).'/smartyfront.config.inc.php');
}

smartyRegisterFunction($smarty, 'modifier', 'truncate', 'smarty_modifier_truncate');
smartyRegisterFunction($smarty, 'modifier', 'secureReferrer', array('Tools', 'secureReferrer'));

smartyRegisterFunction($smarty, 'function', 't', 'smartyTruncate'); // unused
smartyRegisterFunction($smarty, 'function', 'm', 'smartyMaxWords'); // unused
smartyRegisterFunction($smarty, 'function', 'p', 'smartyShowObject'); // Debug only
smartyRegisterFunction($smarty, 'function', 'd', 'smartyDieObject'); // Debug only
smartyRegisterFunction($smarty, 'function', 'l', 'smartyTranslate', false);
smartyRegisterFunction($smarty, 'function', 'hook', 'smartyHook');
smartyRegisterFunction($smarty, 'function', 'toolsConvertPrice', 'toolsConvertPrice');
smartyRegisterFunction($smarty, 'modifier', 'json_encode', array('Tools', 'jsonEncode'));
smartyRegisterFunction($smarty, 'modifier', 'json_decode', array('Tools', 'jsonDecode'));
smartyRegisterFunction($smarty, 'function', 'dateFormat', array('Tools', 'dateFormat'));
smartyRegisterFunction($smarty, 'function', 'convertPrice', array('Product', 'convertPrice'));
smartyRegisterFunction($smarty, 'function', 'convertPriceWithCurrency', array('Product', 'convertPriceWithCurrency'));
smartyRegisterFunction($smarty, 'function', 'displayWtPrice', array('Product', 'displayWtPrice'));
smartyRegisterFunction($smarty, 'function', 'displayWtPriceWithCurrency', array('Product', 'displayWtPriceWithCurrency'));
smartyRegisterFunction($smarty, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
smartyRegisterFunction($smarty, 'modifier', 'convertAndFormatPrice', array('Product', 'convertAndFormatPrice')); // used twice
smartyRegisterFunction($smarty, 'function', 'getAdminToken', array('Tools', 'getAdminTokenLiteSmarty'));
smartyRegisterFunction($smarty, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
smartyRegisterFunction($smarty, 'function', 'getWidthSize', array('Image', 'getWidth'));
smartyRegisterFunction($smarty, 'function', 'getHeightSize', array('Image', 'getHeight'));
smartyRegisterFunction($smarty, 'function', 'addJsDef', array('Media', 'addJsDef'));
smartyRegisterFunction($smarty, 'block', 'addJsDefL', array('Media', 'addJsDefL'));
smartyRegisterFunction($smarty, 'modifier', 'boolval', array('Tools', 'boolval'));
smartyRegisterFunction($smarty, 'modifier', 'cleanHtml', 'smartyCleanHtml');
smartyRegisterFunction($smarty, 'function', 'widget', 'smartyWidget');
smartyRegisterFunction($smarty, 'block', 'widget_block', 'smartyWidgetBlock');
smartyRegisterFunction($smarty, 'modifier', 'classname', 'smartyClassname');
smartyRegisterFunction($smarty, 'modifier', 'classnames', 'smartyClassnames');
smartyRegisterFunction($smarty, 'function', 'url', array('Link', 'getUrlSmarty'));

function smartyDieObject($params, &$smarty)
{
    return Tools::d($params['var']);
}

function smartyShowObject($params, &$smarty)
{
    return Tools::p($params['var']);
}

function smartyMaxWords($params, &$smarty)
{
    Tools::displayAsDeprecated();
    $params['s'] = str_replace('...', ' ...', html_entity_decode($params['s'], ENT_QUOTES, 'UTF-8'));
    $words = explode(' ', $params['s']);

    foreach ($words as &$word) {
        if (Tools::strlen($word) > $params['n']) {
            $word = Tools::substr(trim(chunk_split($word, $params['n']-1, '- ')), 0, -1);
        }
    }

    return implode(' ', Tools::htmlentitiesUTF8($words));
}

function smartyTruncate($params, &$smarty)
{
    Tools::displayAsDeprecated();
    $text = isset($params['strip']) ? strip_tags($params['text']) : $params['text'];
    $length = $params['length'];
    $sep = isset($params['sep']) ? $params['sep'] : '...';

    if (Tools::strlen($text) > $length + Tools::strlen($sep)) {
        $text = Tools::substr($text, 0, $length).$sep;
    }

    return (isset($params['encode']) ? Tools::htmlentitiesUTF8($text, ENT_NOQUOTES) : $text);
}

function smarty_modifier_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false, $charset = 'UTF-8')
{
    if (!$length) {
        return '';
    }

    $string = trim($string);

    if (Tools::strlen($string) > $length) {
        $length -= min($length, Tools::strlen($etc));
        if (!$break_words && !$middle) {
            $string = preg_replace('/\s+?(\S+)?$/u', '', Tools::substr($string, 0, $length+1, $charset));
        }
        return !$middle ? Tools::substr($string, 0, $length, $charset).$etc : Tools::substr($string, 0, $length/2, $charset).$etc.Tools::substr($string, -$length/2, $length, $charset);
    } else {
        return $string;
    }
}

function smarty_modifier_htmlentitiesUTF8($string)
{
    return Tools::htmlentitiesUTF8($string);
}
function smartyMinifyHTML($tpl_output, &$smarty)
{
    $context = Context::getContext();
    if (isset($context->controller) && in_array($context->controller->php_self, array('pdf-invoice', 'pdf-order-return', 'pdf-order-slip'))) {
        return $tpl_output;
    }
    $tpl_output = Media::minifyHTML($tpl_output);
    return $tpl_output;
}

function smartyPackJSinHTML($tpl_output, &$smarty)
{
    $context = Context::getContext();
    if (isset($context->controller) && in_array($context->controller->php_self, array('pdf-invoice', 'pdf-order-return', 'pdf-order-slip'))) {
        return $tpl_output;
    }
    $tpl_output = Media::packJSinHTML($tpl_output);
    return $tpl_output;
}

function smartyRegisterFunction($smarty, $type, $function, $params, $lazy = true)
{
    if (!in_array($type, array('function', 'modifier', 'block'))) {
        return false;
    }

    // lazy is better if the function is not called on every page
    if ($lazy) {
        $lazy_register = SmartyLazyRegister::getInstance();
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

        unset($hook_params['h']);
        unset($hook_params['excl']);
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

function toolsConvertPrice($params, &$smarty)
{
    return Tools::convertPrice($params['price'], Context::getContext()->currency);
}

function withWidget($params, callable $cb)
{
    if (!isset($params['name'])) {
        throw new Exception('Smarty helper `render_widget` expects at least the `name` parameter.');
    }

    $moduleName = $params['name'];
    unset($params['name']);

    $moduleInstance = Module::getInstanceByName($moduleName);

    if (!$moduleInstance instanceof PrestaShop\PrestaShop\Core\Business\Module\WidgetInterface) {
        throw new Exception(sprintf(
            'Module `%1$s` is not a WidgetInterface.',
            $moduleName
        ));
    }

    return $cb($moduleInstance, $params);
}

function smartyWidget($params, &$smarty)
{
    return withWidget($params, function ($widget, $params) {
        return $widget->renderWidget(null, $params);
    });
}

function smartyWidgetBlock($params, $content, &$smarty)
{
    static $backedUpVariablesStack = [];

    if (null === $content) {
        // Function is called twice: at the opening of the block
        // and when it is closed.
        // This is the first call.
        withWidget($params, function ($widget, $params) use (&$smarty, &$backedUpVariablesStack) {
            // Assign widget variables and backup all the variables they override
            $currentVariables = $smarty->getTemplateVars();
            $scopedVariables = $widget->getWidgetVariables();
            $backedUpVariables = [];
            foreach ($scopedVariables as $key => $value) {
                if (array_key_exists($key, $currentVariables)) {
                    $backedUpVariables[$key] = $currentVariables[$key];
                }
                $smarty->assign($key, $value);
            }
            $backedUpVariablesStack[] = $backedUpVariables;
        });
        // We don't display anything since the template is not rendered yet.
        return '';
    } else {
        // Function gets called for the closing tag of the block.
        // We restore the backed up variables in order not to override
        // template variables.
        if (!empty($backedUpVariablesStack)) {
            $backedUpVariables = array_pop($backedUpVariablesStack);
            foreach ($backedUpVariables as $key => $value) {
                $smarty->assign($key, $value);
            }
        }
        // This time content is filled with rendered template, so return it.
        return $content;
    }
}

function smartyClassname($classname)
{
    $classname = str_replace('_', '-', $classname);
    return $classname;
}

function smartyClassnames(array $classnames)
{
    $enabled_classes = [];
    foreach ($classnames as $classname => $enabled) {
        if ($enabled) {
            $enabled_classes[] = smartyClassname($classname);
        }
    }
    return implode(' ', $enabled_classes);
}

/**
 * Used to delay loading of external classes with smarty->register_plugin
 */
class SmartyLazyRegister
{
    protected $registry = array();
    protected static $instance;

    /**
     * Register a function or method to be dynamically called later
     * @param string|array $params function name or array(object name, method name)
     */
    public function register($params)
    {
        if (is_array($params)) {
            $this->registry[$params[1]] = $params;
        } else {
            $this->registry[$params] = $params;
        }
    }

    /**
     * Dynamically call static function or method
     *
     * @param string $name function name
     * @param mixed $arguments function argument
     * @return mixed function return
     */
    public function __call($name, $arguments)
    {
        $item = $this->registry[$name];

        // case 1: call to static method - case 2 : call to static function
        if (is_array($item[1])) {
            return call_user_func_array($item[1].'::'.$item[0], array($arguments[0], &$arguments[1]));
        } else {
            $args = array();

            foreach ($arguments as $a => $argument) {
                if ($a == 0) {
                    $args[] = $arguments[0];
                } else {
                    $args[] = &$arguments[$a];
                }
            }

            return call_user_func_array($item, $args);
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new SmartyLazyRegister();
        }
        return self::$instance;
    }
}
