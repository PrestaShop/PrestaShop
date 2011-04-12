<?php
/*
* 2007-2011 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class AbsTrustedShops
{
	/**
	 * Saved errors messages.
	 * @var array
	 */
	public $errors = array();
	
	/**
	 * Saved warning messages.
	 * @var array
	 */
	public $warnings = array();
	
	/**
	 * Saved confirmations messages.
	 * @var array
	 */
	public $confirmations = array();
	
	/**
	 * @var string
	 */
	protected static $module_name;
	
	public $limited_countries = array();
	public static $smarty;
	public $tab_name;
	public $id_tab;
	
	/**
	 * Set the object which use the translation method for the specific module.
	 * @var Module
	 */
	protected static $translation_object;
	
	abstract public function install();
	abstract public function uninstall();
	abstract public function getContent();
	public static function setTranslationObject(Module $object)
	{
		self::$translation_object = $object;
	}
	protected function _makeFormAction($uri, $id_tab)
	{
		$uri_component = parse_url($uri);
		$arr_query = explode('&', $uri_component['query']);
		$arr_query_new = array();
		foreach($arr_query as $key=>$value)
		{
			$arr = explode('=', $value);
			$arr_query_new[$arr[0]] = $arr[1];
		}
		$arr_query_new['id_tab'] = $id_tab;
		return str_replace($uri_component['query'], '', $uri).http_build_query($arr_query_new);
	}
	/**
	 * Set a static name for the module.
	 * 
	 * @param string $name
	 */
	public function setModuleName($name)
	{
		self::$module_name = $name;
	}
	
	public function setSmarty($smarty)
	{
		self::$smarty = $smarty;
	}
	
	/**
	 * Get translation for a given module text
	 *
	 * @param string $string String to translate
	 * @return string Translation
	 */
	public function l($string, $specific = false)
	{
		if ($specific === false)
		{
			$reflection_class = new ReflectionClass(get_class($this));
			$specific = basename($reflection_class->getFileName(), '.php');
		}
		if (self::$translation_object instanceof Module)
		{
			return self::$translation_object->l($string, $specific);
		}
	}
	public function display($file, $template, $cacheId = NULL, $compileId = NULL)
	{
		global $smarty;

		if (Configuration::get('PS_FORCE_SMARTY_2')) /* Keep a backward compatibility for Smarty v2 */
		{
			$previousTemplate = $smarty->currentTemplate;
			$smarty->currentTemplate = substr(basename($template), 0, -4);
		}
		$smarty->assign('module_dir', __PS_BASE_URI__.'modules/'.basename($file, '.php').'/');
		if (($overloaded = self::_isTemplateOverloadedStatic(basename($file, '.php'), $template)) === NULL)
			$result = Tools::displayError('No template found');
		else
		{
			$smarty->assign('module_template_dir', ($overloaded ? _THEME_DIR_ : __PS_BASE_URI__).'modules/'.basename($file, '.php').'/');
			$result = $smarty->fetch(($overloaded ? _PS_THEME_DIR_.'modules/'.basename($file, '.php') : _PS_MODULE_DIR_.basename($file, '.php')).'/'.$template, $cacheId, $compileId);
		}
		if (Configuration::get('PS_FORCE_SMARTY_2')) /* Keep a backward compatibility for Smarty v2 */
			$smarty->currentTemplate = $previousTemplate;
		return $result;
	}
	
	/**
	 * Template management (display, overload, cache)
	 * @see Module::_isTemplateOverloadedStatic()
	 */
	protected static function _isTemplateOverloadedStatic($moduleName, $template)
	{
		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$moduleName.'/'.$template))
			return true;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.$moduleName.'/'.$template))
			return false;
		return NULL;
	}
}