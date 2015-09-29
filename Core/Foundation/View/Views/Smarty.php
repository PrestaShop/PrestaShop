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

namespace PrestaShop\PrestaShop\Core\Foundation\View\Views;

use PrestaShop\PrestaShop\Core\Foundation\View\View;
use PrestaShop\PrestaShop\Foundation\View\SmartyPlugins\FunctionsTrait;
use PrestaShop\PrestaShop\Foundation\View\SmartyPlugins\SmartyLazyRegister;

/**
 * Smarty view
 *
 * The Smarty view is a custom View class that renders templates using Smarty
 */
class Smarty extends View
{
    use FunctionsTrait;

    public $parserDirectory = null;
    public $parserCompileDirectory = null;
    public $parserCacheDirectory = null;
    public $parserExtensions = array();
    private $parserInstance = null;

    /**
     * Render Template
     *
     * @param string $template
     * @param null $data
     *
     * @return string
     */
    public function render($template = null, $data = null)
    {
        $parser = $this->getInstance();
        $parser->assign($this->all());

        return $parser->fetch($template, $data);
    }

    /**
     * Create new instance
     *
     * @throws \Exception
     *
     * @return \Smarty Instance
     */
    public function getInstance()
    {
        if (!($this->parserInstance instanceof \Smarty)) {
            if (!class_exists('\Smarty')) {
                if (!is_dir($this->parserDirectory)) {
                    // FIXME: utiliser les Exceptions PS
                    throw new \Exception('Cannot set the Smarty lib directory : ' . $this->parserDirectory . '. Directory does not exist.');
                }
                require_once $this->parserDirectory . '/Smarty.class.php';
            }

            if (\Configuration::get('PS_SMARTY_LOCAL')) {
                $this->parserInstance = new \SmartyCustom();
            } else {
                $this->parserInstance = new \Smarty();
            }

            $this->parserInstance->template_dir = $this->getTemplatesDirectory();
            if ($this->parserExtensions) {
                $this->parserInstance->addPluginsDir($this->parserExtensions);
            }
            if ($this->parserCompileDirectory) {
                $this->parserInstance->compile_dir = $this->parserCompileDirectory;
            }
            if ($this->parserCacheDirectory) {
                $this->parserInstance->cache_dir = $this->parserCacheDirectory;
            }

            $this->setDefaultOptions();
        }

        return $this->parserInstance;
    }

    /**
     * Set the default options
     */
    final private function setDefaultOptions()
    {
        $this->parserInstance->caching = false;
        $this->parserInstance->force_compile = (\Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_FORCE_COMPILE_) ? true : false;
        $this->parserInstance->compile_check = (\Configuration::get('PS_SMARTY_FORCE_COMPILE') >= _PS_SMARTY_CHECK_COMPILE_) ? true : false;
        $this->parserInstance->debug_tpl = _PS_ALL_THEMES_DIR_.'debug.tpl';

        if (!\Tools::getSafeModeStatus()) {
            $this->parserInstance->use_sub_dirs = true;
        }

        if (\Configuration::get('PS_SMARTY_CACHING_TYPE') == 'mysql') {
            include(_PS_CLASS_DIR_.'/SmartyCacheResourceMysql.php');
            $this->parserInstance->caching_type = 'mysql';
        }

        if (defined('_PS_ADMIN_DIR_')) {
            $this->setDefaultAdminOptions();
            $this->smartyRegisterFunction('function', 'l', [$this, 'smartyTranslateAdmin'], false);
        } else {
            $this->setDefaultFrontOptions();
            $this->smartyRegisterFunction('function', 'l', [$this, 'smartyTranslateFront'], false);
        }

        //register plugins
        $this->smartyRegisterFunction('modifier', 'truncate', [$this, 'smarty_modifier_truncate']);
        $this->smartyRegisterFunction('modifier', 'secureReferrer', array('Tools', 'secureReferrer'));
        $this->smartyRegisterFunction('function', 't', [$this, 'smartyTruncate']); // unused
        $this->smartyRegisterFunction('function', 'm', [$this, 'smartyMaxWords']); // unused
        $this->smartyRegisterFunction('function', 'p', [$this, 'smartyShowObject']); // Debug only
        $this->smartyRegisterFunction('function', 'd', [$this, 'smartyDieObject']); // Debug only
        $this->smartyRegisterFunction('function', 'hook', [$this, 'smartyHook']);
        $this->smartyRegisterFunction('function', 'toolsConvertPrice', [$this, 'toolsConvertPrice']);
        $this->smartyRegisterFunction('modifier', 'json_encode', array('Tools', 'jsonEncode'));
        $this->smartyRegisterFunction('modifier', 'json_decode', array('Tools', 'jsonDecode'));
        $this->smartyRegisterFunction('function', 'dateFormat', array('Tools', 'dateFormat'));
        $this->smartyRegisterFunction('function', 'convertPrice', array('Product', 'convertPrice'));
        $this->smartyRegisterFunction('function', 'convertPriceWithCurrency', array('Product', 'convertPriceWithCurrency'));
        $this->smartyRegisterFunction('function', 'displayWtPrice', array('Product', 'displayWtPrice'));
        $this->smartyRegisterFunction('function', 'displayWtPriceWithCurrency', array('Product', 'displayWtPriceWithCurrency'));
        $this->smartyRegisterFunction('function', 'displayPrice', array('Tools', 'displayPriceSmarty'));
        $this->smartyRegisterFunction('modifier', 'convertAndFormatPrice', array('Product', 'convertAndFormatPrice')); // used twice
        $this->smartyRegisterFunction('function', 'getAdminToken', array('Tools', 'getAdminTokenLiteSmarty'));
        $this->smartyRegisterFunction('function', 'displayAddressDetail', array('AddressFormat', 'generateAddressSmarty'));
        $this->smartyRegisterFunction('function', 'getWidthSize', array('Image', 'getWidth'));
        $this->smartyRegisterFunction('function', 'getHeightSize', array('Image', 'getHeight'));
        $this->smartyRegisterFunction('function', 'addJsDef', array('Media', 'addJsDef'));
        $this->smartyRegisterFunction('block', 'addJsDefL', array('Media', 'addJsDefL'));
        $this->smartyRegisterFunction('modifier', 'boolval', array('Tools', 'boolval'));
        $this->smartyRegisterFunction('modifier', 'cleanHtml', [$this, 'smartyCleanHtml']);
    }

    /**
     * Register a smarty function
     *
     * @param string $type
     * @param string $function The function name
     * @param array $params the callback method
     * @param bool $lazy
     *
     * @return bool false if wrong $type
     */
    private function smartyRegisterFunction($type, $function, $params, $lazy = true)
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
            $this->parserInstance->registerPlugin($type, $function, array($lazy_register, $params));
        } else {
            $this->parserInstance->registerPlugin($type, $function, $params);
        }
    }

    /**
     * Set the default options for an admin instance
     */
    final private function setDefaultAdminOptions()
    {
        $this->parserInstance->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'default/template');

        $this->parserInstance->debugging = false;
        $this->parserInstance->debugging_ctrl = 'NONE';

        // Let user choose to force compilation
        $this->parserInstance->force_compile = (\Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_FORCE_COMPILE_) ? true : false;

        // But force compile_check since the performance impact is small and it is better for debugging
        $this->parserInstance->compile_check = true;
    }

    /**
     * Set the default options for a front instance
     */
    final private function setDefaultFrontOptions()
    {
        $this->parserInstance->setTemplateDir(_PS_THEME_DIR_);

        if (\Configuration::get('PS_HTML_THEME_COMPRESSION')) {
            $this->parserInstance->registerFilter('output', 'smartyMinifyHTML');
        }

        if (\Configuration::get('PS_JS_HTML_THEME_COMPRESSION')) {
            $this->parserInstance->registerFilter('output', 'smartyPackJSinHTML');
        }
    }
}
