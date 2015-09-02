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

namespace PrestaShop\PrestaShop\Views;

class Smarty extends \PrestaShop\PrestaShop\View
{
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
     * @return string
     */
    public function render($template = null, $data = null)
    {
        $parser = $this->getInstance();
        $parser->assign($this->all());

        return $parser->fetch($template, $data);
    }

    /**
     * Creates new Smarty object instance
     *
     * @throws \Exception
     * @return \Smarty Instance
     */
    public function getInstance()
    {
        if (!($this->parserInstance instanceof \Smarty)) {
            if (!class_exists('\Smarty')) {
                if (!is_dir($this->parserDirectory)) {
                    throw new \Exception('Cannot set the Smarty lib directory : ' . $this->parserDirectory . '. Directory does not exist.');
                }
                require_once $this->parserDirectory . '/Smarty.class.php';
            }

            $this->parserInstance = new \Smarty();
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
     * Set options to smarty
     */
    private function setDefaultOptions()
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
            require_once(dirname(__FILE__).'/SmartyPlugins/SmartyAdminFunctions.php');
        } else {
            $this->setDefaultFrontOptions();
            require_once(dirname(__FILE__).'/SmartyPlugins/SmartyFrontFunctions.php');
        }

        require_once(dirname(__FILE__).'/SmartyPlugins/SmartyFunctions.php');
    }

    private function setDefaultAdminOptions()
    {
        $this->parserInstance->setTemplateDir(_PS_BO_ALL_THEMES_DIR_ . 'default/template');

        $this->parserInstance->debugging = false;
        $this->parserInstance->debugging_ctrl = 'NONE';

        // Let user choose to force compilation
        $this->parserInstance->force_compile = (\Configuration::get('PS_SMARTY_FORCE_COMPILE') == _PS_SMARTY_FORCE_COMPILE_) ? true : false;
        // But force compile_check since the performance impact is small and it is better for debugging
        $this->parserInstance->compile_check = true;
    }

    private function setDefaultFrontOptions()
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
