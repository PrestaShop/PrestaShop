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
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2015 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

smartyRegisterFunction($this->parserInstance, 'modifier', 'truncate', 'smarty_modifier_truncate');
smartyRegisterFunction($this->parserInstance, 'modifier', 'secureReferrer', array('Tools', 'secureReferrer'));
smartyRegisterFunction($this->parserInstance, 'function', 't', 'smartyTruncate'); // unused
smartyRegisterFunction($this->parserInstance, 'function', 'm', 'smartyMaxWords'); // unused
smartyRegisterFunction($this->parserInstance, 'function', 'p', 'smartyShowObject'); // Debug only
smartyRegisterFunction($this->parserInstance, 'function', 'd', 'smartyDieObject'); // Debug only
smartyRegisterFunction($this->parserInstance, 'function', 'l', 'smartyTranslate', false);
smartyRegisterFunction($this->parserInstance, 'function', 'hook', 'smartyHook');
smartyRegisterFunction($this->parserInstance, 'function', 'toolsConvertPrice', 'toolsConvertPrice');
smartyRegisterFunction($this->parserInstance, 'modifier', 'json_encode', array('Tools', 'jsonEncode'));
smartyRegisterFunction($this->parserInstance, 'modifier', 'json_decode', array('Tools', 'jsonDecode'));
smartyRegisterFunction($this->parserInstance, 'function', 'dateFormat', array('Tools', 'dateFormat'));
smartyRegisterFunction($this->parserInstance, 'function', 'convertPrice', array('Product', 'convertPrice'));
smartyRegisterFunction($this->parserInstance, 'function', 'convertPriceWithCurrency', array('Product', 'convertPriceWithCurrency'));
smartyRegisterFunction($this->parserInstance, 'function', 'displayWtPrice', array('Product', 'displayWtPrice'));
smartyRegisterFunction($this->parserInstance, 'function', 'displayWtPriceWithCurrency', array('Product', 'displayWtPriceWithCurrency'));
smartyRegisterFunction($this->parserInstance, 'function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
smartyRegisterFunction($this->parserInstance, 'modifier', 'convertAndFormatPrice', array('Product', 'convertAndFormatPrice')); // used twice
smartyRegisterFunction($this->parserInstance, 'function', 'getAdminToken', array('Tools', 'getAdminTokenLiteSmarty'));
smartyRegisterFunction($this->parserInstance, 'function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
smartyRegisterFunction($this->parserInstance, 'function', 'getWidthSize', array('Image', 'getWidth'));
smartyRegisterFunction($this->parserInstance, 'function', 'getHeightSize', array('Image', 'getHeight'));
smartyRegisterFunction($this->parserInstance, 'function', 'addJsDef', array('Media', 'addJsDef'));
smartyRegisterFunction($this->parserInstance, 'block', 'addJsDefL', array('Media', 'addJsDefL'));
smartyRegisterFunction($this->parserInstance, 'modifier', 'boolval', array('Tools', 'boolval'));
smartyRegisterFunction($this->parserInstance, 'modifier', 'cleanHtml', 'smartyCleanHtml');

function smartyDieObject($params)
{
    return \Tools::d($params['var']);
}

function smartyShowObject($params)
{
    return \Tools::p($params['var']);
}

function smartyMaxWords($params)
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

function smartyTruncate($params)
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
function smartyMinifyHTML($tpl_output)
{
    $context = Context::getContext();
    if (isset($context->controller) && in_array($context->controller->php_self, array('pdf-invoice', 'pdf-order-return', 'pdf-order-slip'))) {
        return $tpl_output;
    }
    $tpl_output = Media::minifyHTML($tpl_output);
    return $tpl_output;
}

function smartyPackJSinHTML($tpl_output)
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
    if (!empty($params['h'])) {
        $id_module = null;
        $hook_params = $params;
        $hook_params['smarty'] = $smarty;
        if (!empty($params['mod'])) {
            $module = Module::getInstanceByName($params['mod']);
            if ($module && $module->id) {
                $id_module = $module->id;
            }
            unset($hook_params['mod']);
        }
        unset($hook_params['h']);
        return Hook::exec($params['h'], $hook_params, $id_module);
    }
}

function smartyCleanHtml($data)
{
    // Prevent xss injection.
    if (Validate::isCleanHtml($data)) {
        return $data;
    }
}

function toolsConvertPrice($params)
{
    return Tools::convertPrice($params['price'], Context::getContext()->currency);
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
