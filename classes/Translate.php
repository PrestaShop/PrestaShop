<?php
/*
* 2007-2014 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * Small parasite class initialised by Translate
 * It's only purpose is to call Translate::saveTranslationToL2Cache
 * at the script end. As Translate is not instatiated, we cannot do
 * it directly there
 */
class TranslateSaveGuard
{
	/**
	 * Calls Translate::saveTranslationToL2Cache
	 */
	public function __destruct(){
		Translate::saveTranslationToL2Cache();
	}
}

/**
 *
 * @since 1.5.0
 */
class TranslateCore
{

	/**
	 * @var TranslateSaveGuard
	 */
	protected static $l2_save_guard = null;

	/**
	 * Md5-hashed context bound identifier
	 * @var string
	 */
	protected static $l2_context_identifier = null;

	/**
	 * Current cache
	 * @var array
	 */
	protected static $l2_cache = array();

	/**
	 * List of cache miss
	 * @var array
	*/
	protected static $l2_miss = array();

	/**
	 * Flag for marking tainted execution - when we do not want to recreate files (after removal generally)
	 * @var boolean
	 */
	protected static $l2_do_not_save = false;

	/**
	 * Flag which marks if 3 main translation files for admin were included
	 * @var boolean
	 */
	protected static $l2_admin_translations_loaded = false;

	/**
	 * Get a translation for an admin controller
	 *
	 * @param $string
	 * @param string $class
	 * @param bool $addslashes
	 * @param bool $htmlentities
	 * @return string
	 */
	public static function getAdminTranslation($string, $class = 'AdminTab', $addslashes = false, $htmlentities = true, $sprintf = null)
	{
		if (!self::$l2_save_guard)
			self::initL2Cache();
		$l2_cache_key = md5(sprintf('at|%s|%s|%s|%s|%d', $string, $class, (int)$addslashes, (int)$htmlentities, $sprintf ? json_encode($sprintf) : ''));
		if (!isset(self::$l2_cache[$l2_cache_key]) && !isset(self::$l2_miss[$l2_cache_key])){
			self::$l2_miss[$l2_cache_key] = true;

			static $modules_tabs = null;

			// @todo remove global keyword in translations files and use static
			global $_LANGADM;

			if ($modules_tabs === null)
				$modules_tabs = Tab::getModuleTabList();

			if ($_LANGADM == null)
			{
				$iso = Context::getContext()->language->iso_code;
				if (empty($iso))
					$iso = Language::getIsoById((int)(Configuration::get('PS_LANG_DEFAULT')));
				if (file_exists(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php'))
					include_once(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');
			}

			if (isset($modules_tabs[strtolower($class)]))
			{
				$class_name_controller = $class.'controller';
				// if the class is extended by a module, use modules/[module_name]/xx.php lang file
				if (class_exists($class_name_controller) && Module::getModuleNameFromClass($class_name_controller)){
					self::$l2_cache[$l2_cache_key] = Translate::getModuleTranslation(Module::$classInModule[$class_name_controller], $string, $class_name_controller, $sprintf, $addslashes);
					return self::$l2_cache[$l2_cache_key];
				}
			}

			$string = preg_replace("/\\*'/", "\'", $string);
			$key = md5($string);
			if (isset($_LANGADM[$class.$key]))
				$str = $_LANGADM[$class.$key];
			else
				$str = Translate::getGenericAdminTranslation($string, $key, $_LANGADM);

			if ($htmlentities)
				$str = htmlspecialchars($str, ENT_QUOTES, 'utf-8');
			$str = str_replace('"', '&quot;', $str);

			if ($sprintf !== null)
				$str = Translate::checkAndReplaceArgs($str, $sprintf);

			self::$l2_cache[$l2_cache_key] = ($addslashes ? addslashes($str) : stripslashes($str));
		}

		return self::$l2_cache[$l2_cache_key];
	}

	/**
	 * Return the translation for a string if it exists for the base AdminController or for helpers
	 *
	 * @static
	 * @param $string string to translate
	 * @param null $key md5 key if already calculated (optional)
	 * @param $lang_array global array of admin translations
	 * @return string translation
	 */
	public static function getGenericAdminTranslation($string, $key = null, &$lang_array)
	{
		$string = preg_replace("/\\*'/", "\'", $string);
		if (is_null($key))
			$key = md5($string);

		if (isset($lang_array['AdminController'.$key]))
			$str = $lang_array['AdminController'.$key];
		elseif (isset($lang_array['Helper'.$key]))
			$str = $lang_array['Helper'.$key];
		elseif (isset($lang_array['AdminTab'.$key]))
			$str = $lang_array['AdminTab'.$key];
		else
			// note in 1.5, some translations has moved from AdminXX to helper/*.tpl
			$str = $string;

		return $str;
	}

	/**
	 * Get a translation for a module
	 *
	 * @param string|Module $module
	 * @param string $string
	 * @param string $source
	 * @return string
	 */
	public static function getModuleTranslation($module, $string, $source, $sprintf = null, $js = false)
	{
		global $_MODULES, $_MODULE, $_LANGADM;

		if (!self::$l2_save_guard)
			self::initL2Cache();

		$l2_cache_key = md5(sprintf('mt|%s|%s|%s|%s|%d', $module instanceof Module ? $module->name : (string)$module, $string, $source, $sprintf ? json_encode($sprintf) : '', (int)$js));

		if (!isset(self::$l2_cache[$l2_cache_key]) && !isset(self::$l2_miss[$l2_cache_key]))
		{
			self::$l2_miss[$l2_cache_key] = true;

			static $lang_cache = array();
			// $_MODULES is a cache of translations for all module.
			// $translations_merged is a cache of wether a specific module's translations have already been added to $_MODULES
			static $translations_merged = array();

			$name = $module instanceof Module ? $module->name : $module;
			$language = Context::getContext()->language;

			if (!isset($translations_merged[$name]) && isset(Context::getContext()->language))
			{
				$files_by_priority = array(
					// Translations in theme
					_PS_THEME_DIR_.'modules/'.$name.'/translations/'.$language->iso_code.'.php',
					_PS_THEME_DIR_.'modules/'.$name.'/'.$language->iso_code.'.php',
					// PrestaShop 1.5 translations
					_PS_MODULE_DIR_.$name.'/translations/'.$language->iso_code.'.php',
					// PrestaShop 1.4 translations
					_PS_MODULE_DIR_.$name.'/'.$language->iso_code.'.php'
				);
				foreach ($files_by_priority as $file)
					if (file_exists($file))
					{
						include_once($file);
						$_MODULES = !empty($_MODULES) ? $_MODULES + $_MODULE : $_MODULE; //we use "+" instead of array_merge() because array merge erase existing values.
						$translations_merged[$name] = true;
					}
			}
			$string = preg_replace("/\\*'/", "\'", $string);
			$key = md5($string);

			$cache_key = $name.'|'.$string.'|'.$source.'|'.(int)$js;

			if (!isset($lang_cache[$cache_key]))
			{
				if ($_MODULES == null)
				{
					if ($sprintf !== null)
						$string = Translate::checkAndReplaceArgs($string, $sprintf);
					self::$l2_cache[$l2_cache_key] = str_replace('"', '&quot;', $string);
				} else {
					$current_key = strtolower('<{'.$name.'}'._THEME_NAME_.'>'.$source).'_'.$key;
					$default_key = strtolower('<{'.$name.'}prestashop>'.$source).'_'.$key;
					if ('controller' == ($file = substr($source, 0, - 10)))
					{
						$current_key_file = strtolower('<{'.$name.'}'._THEME_NAME_.'>'.$file).'_'.$key;
						$default_key_file = strtolower('<{'.$name.'}prestashop>'.$file).'_'.$key;
					}

					if (isset($current_key_file) && isset($_MODULES[$current_key_file]))
						$ret = stripslashes($_MODULES[$current_key_file]);
					elseif (isset($default_key_file) && isset($_MODULES[$default_key_file]))
						$ret = stripslashes($_MODULES[$default_key_file]);
					elseif (isset($_MODULES[$current_key]))
						$ret = stripslashes($_MODULES[$current_key]);
					elseif (isset($_MODULES[$default_key]))
						$ret = stripslashes($_MODULES[$default_key]);
					// if translation was not found in module, look for it in AdminController or Helpers
					elseif (self::loadInitTranslations() && !empty($_LANGADM))
						$ret = Translate::getGenericAdminTranslation($string, $key, $_LANGADM);
					else
						$ret = stripslashes($string);

					if ($sprintf !== null)
						$ret = Translate::checkAndReplaceArgs($ret, $sprintf);

					if ($js)
						$ret = addslashes($ret);
					else
						$ret = htmlspecialchars($ret, ENT_COMPAT, 'UTF-8');

					if ($sprintf === null)
						$lang_cache[$cache_key] = $ret;
					else {
						self::$l2_cache[$l2_cache_key] = $ret;
						return self::$l2_cache[$l2_cache_key];
					}
				}
			}
			self::$l2_cache[$l2_cache_key] = $lang_cache[$cache_key];
		}

		return self::$l2_cache[$l2_cache_key];

	}

	/**
	 * Get a translation for a PDF
	 *
	 * @param string $string
	 * @return string
	 */
	public static function getPdfTranslation($string)
	{
		global $_LANGPDF;

		if (!self::$l2_save_guard)
			self::initL2Cache();

		$l2_cache_key = md5(sprintf('pdf|%s', $string));

		if (!isset(self::$l2_cache[$l2_cache_key]) && !isset(self::$l2_miss[$l2_cache_key]))
		{
			self::$l2_miss[$l2_cache_key] = true;

			$iso = Context::getContext()->language->iso_code;

			if (!Validate::isLangIsoCode($iso))
				Tools::displayError(sprintf('Invalid iso lang (%s)', Tools::safeOutput($iso)));

			$override_i18n_file = _PS_THEME_DIR_.'pdf/lang/'.$iso.'.php';
			$i18n_file = _PS_TRANSLATIONS_DIR_.$iso.'/pdf.php';
			if (file_exists($override_i18n_file))
       	    	$i18n_file = $override_i18n_file;

			if (!include($i18n_file))
				Tools::displayError(sprintf('Cannot include PDF translation language file : %s', $i18n_file));

			if (!isset($_LANGPDF) || !is_array($_LANGPDF))
				self::$l2_cache[$l2_cache_key] =  str_replace('"', '&quot;', $string);
			else {

				$string = preg_replace("/\\*'/", "\'", $string);
				$key = md5($string);

				self::$l2_cache[$l2_cache_key] = (array_key_exists('PDF'.$key, $_LANGPDF) ? $_LANGPDF['PDF'.$key] : $string);
			}
		}
		return self::$l2_cache[$l2_cache_key];
	}

	/**
	 * Check if string use a specif syntax for sprintf and replace arguments if use it
	 *
	 * @param $string
	 * @param $args
	 * @return string
	 */
	public static function checkAndReplaceArgs($string, $args)
	{
		if (preg_match_all('#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#', $string, $matches) && !is_null($args))
		{
			if (!is_array($args))
				$args = array($args);

			return vsprintf($string, $args);
		}
		return $string;
	}

	/**
	* Perform operations on translations after everything is escaped and before displaying it
	*/
	public static function postProcessTranslation($string, $params)
	{
		// If tags were explicitely provided, we want to use them *after* the translation string is escaped.
		if (!empty($params['tags']))
		{
			foreach ($params['tags'] as $index => $tag)
			{
				// Make positions start at 1 so that it behaves similar to the %1$d etc. sprintf positional params
				$position = $index + 1;
				// extract tag name
				$match = array();
				if (preg_match('/^\s*<\s*(\w+)/', $tag, $match))
				{
					$opener = $tag;
					$closer = '</'.$match[1].'>';

					$string = str_replace('['.$position.']', $opener, $string);
					$string = str_replace('[/'.$position.']', $closer, $string);
					$string = str_replace('['.$position.'/]', $opener.$closer, $string);
				}
			}
		}

		return $string;
	}

	/**
	 * Compatibility method that just calls postProcessTranslation.
	 * @deprecated renamed this to postProcessTranslation, since it is not only used in relation to smarty.
	 */
	public static function smartyPostProcessTranslation($string, $params)
	{
		return Translate::postProcessTranslation($string, $params);
	}

	/**
	 * Helper function to make calls to postProcessTranslation more readable.
	 */
	public static function ppTags($string, $tags)
	{
		return Translate::postProcessTranslation($string, array('tags' => $tags));
	}

	public static function setL2CacheDoNotSaveFlag()
	{
		self::$l2_do_not_save = true;
	}


	/**
	 * Deletes all L2 cache files
	 * @return array
	 */
	public static function clearL2Cache($prefix = null){
		return array_map('unlink', glob(self::getTranslationL2CacheDir().($prefix !==null && trim($prefix, 'a..zA..Z0..9') =='' ? (string)$prefix : ''). '*'));
	}

	/**
	 * Saves translation to cache file if necessary.
	 * @return boolean True if file created succesfully or false otherwise
	 */
	public static function saveTranslationToL2Cache(){
		if (!self::$l2_do_not_save && !empty(self::$l2_cache) && (!empty(self::$l2_miss) || !file_exists(self::getTranslationL2CacheFileName())))
		{
			if (!file_exists(self::getTranslationL2CacheDir()))
			{
				mkdir(self::getTranslationL2CacheDir(), 0777, true);
			}
			$tmpfile  = tempnam(self::getTranslationL2CacheDir(), '_tm');
			if ($tmpfile && file_put_contents($tmpfile, json_encode(self::$l2_cache)))
			{
				return (rename($tmpfile, self::getTranslationL2CacheFileName()) || (unlink($tmpfile) && false));
			}
		}
		return false;
	}

	/**
	 * Inits save guard object and loads cache if disponible
	 */
	protected static function initL2Cache(){
		if (self::$l2_save_guard === null)
		{
			self::$l2_save_guard = new TranslateSaveGuard();
			self::$l2_cache = self::loadTranslationFromL2Cache();
		}
	}

	/**
	 * Returns translation cache from file defined by context
	 * @return array
	 */
	protected static function loadTranslationFromL2Cache()
	{
		$result = array();
		$filename = self::getTranslationL2CacheFileName();
		if (file_exists($filename)) {
			$result = json_decode((string)file_get_contents($filename), true);
		}
		return is_array($result) ? $result : array();
	}

	/**
	 * Returns actual cache file name, based on context
	 * @return string Full file path
	 */
	protected static function getTranslationL2CacheFileName()
	{
		return self::getTranslationL2CacheDir().self::getContextIdentifier(). '.json';
	}

	/**
	 * Returns L2 cache directory (which not necessary exists), which is a subdir of cache dir
	 * @return string Directory path with directory separator at end
	 */
	protected static function getTranslationL2CacheDir()
	{
		return _PS_CACHE_DIR_.DIRECTORY_SEPARATOR.'tl2c'.DIRECTORY_SEPARATOR;
	}

	/**
	 * Returns unique context identifier. It's calculated at first call, to avoid problems with some
	 * context elements changing during the script execution.
	 * Currently elements of context identifier are : language id,shop id, controller name, theme name, bo flag
	 * @return string Context identifier in format <id_lang>-<md5 of rest of params>
	 */
	protected static function getContextIdentifier()
	{
		if (self::$l2_context_identifier === null)
		{
			$result = array();
			$context = Context::getContext();

			if (isset($context) && isset($context->language) && isset($context->language->id))
				$lang = $context->language->id;
			else
				$lang = (int)Configuration::get('PS_LANG_DEFAULT');

			if (isset($context) && isset($context->shop) && isset($context->shop->id))
				$result[] = $context->shop->id;

			if (isset($context) && isset($context->controller) && isset($context->controller->php_self))
				$result[] = $context->controller->php_self;

			if (isset($context) && isset($context->theme) && isset($context->theme->name))
				$result[] = $context->theme->name;

			if (isset($context) && isset($context->employee) && isset($context->employee->id))
				$result[] = 'bo';

			self::$l2_context_identifier = (string)$lang.'-'.md5(implode('-', array_map('strval', $result)));
		}
		return self::$l2_context_identifier;
	}

	/**
	 * Loads 3 main admin translation files
	 * @return boolean
	 */
	public static function loadInitTranslations(){
		
		if (!self::$l2_admin_translations_loaded){
			self::$l2_admin_translations_loaded = true;
			$context = Context::getContext();
			$iso = $context->language->iso_code;
			if (file_exists(_PS_TRANSLATIONS_DIR_.$iso.'/errors.php'))
				include_once(_PS_TRANSLATIONS_DIR_.$iso.'/errors.php');
			if (file_exists(_PS_TRANSLATIONS_DIR_.$iso.'/fields.php'))
				include_once(_PS_TRANSLATIONS_DIR_.$iso.'/fields.php');
			if (file_exists(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php'))
				include_once(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');
		}
		
		return self::$l2_admin_translations_loaded;
	}

	/**
	 * for AdminTab::l
	 * @param unknown_type $string
	 * @param unknown_type $currentClass
	 * @param unknown_type $class
	 * @param unknown_type $addslashes
	 * @param unknown_type $htmlentities
	 * @return multitype:
	 */
	public function getAdminTabTranslation($string,  $currentClass = 'AdminTab', $class = 'AdminTab', $addslashes = false, $htmlentities = true)
	{
		if (!self::$l2_save_guard)
			self::initL2Cache();

		$l2_cache_key = md5(sprintf('atl|%s|%s|%s|%d|%d', $string, $currentClass, $class, (int)$addslashes, (int)$htmlentities));

		if (!isset(self::$l2_cache[$l2_cache_key]) && !isset(self::$l2_miss[$l2_cache_key]))
		{
			self::$l2_miss[$l2_cache_key] = true;
			self::loadInitTranslations();
			global $_LANGADM;
			$key = md5(str_replace('\'', '\\\'', $string));
			$str = (array_key_exists($currentClass.$key, $_LANGADM)) ? $_LANGADM[$currentClass.$key] : ((array_key_exists($class.$key, $_LANGADM)) ? $_LANGADM[$class.$key] : $string);
			$str = $htmlentities ? htmlentities($str, ENT_QUOTES, 'utf-8') : $str;
			self::$l2_cache[$l2_cache_key] = str_replace('"', '&quot;', ($addslashes ? addslashes($str) : stripslashes($str)));
		}
		
		return self::$l2_cache[$l2_cache_key];
	}

	/**
	 * For translate() from functions.php
	 * @param string $string
	 * @return multitype:
	 */
	public function getTranslateFuncTranslation($string)
	{
		if (!self::$l2_save_guard)
			self::initL2Cache();

		$l2_cache_key = md5(sprintf('tft|%s', $string));
		
		if (!isset(self::$l2_cache[$l2_cache_key]) && !isset(self::$l2_miss[$l2_cache_key]))
		{
			self::$l2_miss[$l2_cache_key] = true;
			self::loadInitTranslations();
			global $_LANGADM;
			if (!is_array($_LANGADM))
				self::$l2_cache[$l2_cache_key] = str_replace('"', '&quot;', $string);
			else {
				$key = md5(str_replace('\'', '\\\'', $string));
				$str = (array_key_exists('index'.$key, $_LANGADM)) ? $_LANGADM['index'.$key] : ((array_key_exists('index'.$key, $_LANGADM)) ? $_LANGADM['index'.$key] : $string);
				self::$l2_cache[$l2_cache_key] = str_replace('"', '&quot;', stripslashes($str));
			}
		}

		return self::$l2_cache[$l2_cache_key];

	}

	/**
	 * For Tools::displayError
	 * @param unknown_type $string
	 * @param unknown_type $htmlentities
	 * @param Context $context
	 * @return multitype:
	 */
	public static function getTranslateDisplayError($string, $htmlentities, Context $context = null)
	{
		if (!self::$l2_save_guard)
			self::initL2Cache();

		if ($context === null)
			$context = Context::getContext();

		$l2_cache_key = md5(sprintf('tde|%s|%d|%s', $string, (int)$htmlentities, $context->language->iso_code));

		if (!isset(self::$l2_cache[$l2_cache_key]) && !isset(self::$l2_miss[$l2_cache_key]))
		{
			self::$l2_miss[$l2_cache_key] = true;
			global $_ERRORS;
			@include_once(_PS_TRANSLATIONS_DIR_.$context->language->iso_code.'/errors.php');

			if (!is_array($_ERRORS))
				self::$l2_cache[$l2_cache_key] = $htmlentities ? Tools::htmlentitiesUTF8($string) : $string;
			else {
				$key = md5(str_replace('\'', '\\\'', $string));
				$str = (isset($_ERRORS) && is_array($_ERRORS) && array_key_exists($key, $_ERRORS)) ? $_ERRORS[$key] : $string;
				self::$l2_cache[$l2_cache_key] =  $htmlentities ? Tools::htmlentitiesUTF8(stripslashes($str)) : $str;
			}
		}
		
		return self::$l2_cache[$l2_cache_key];
	}

	/**
	 * For ObjectModel::displayFieldName
	 * @param unknown_type $field
	 * @param unknown_type $class
	 * @param unknown_type $htmlentities
	 * @param Context $context
	 * @return Ambigous <unknown, string>
	 */
	public static function getTranslateDisplayFieldName($field, $class, $htmlentities = true, Context $context = null)
	{
		if (!self::$l2_save_guard)
			self::initL2Cache();

		if ($context === null)
			$context = Context::getContext();

		$l2_cache_key = md5(sprintf('om|%s|%s|%d|%s', $field, $class, (int)$htmlentities, $context->language->iso_code));

		if (!isset(self::$l2_cache[$l2_cache_key]) && !isset(self::$l2_miss[$l2_cache_key]))
		{
			self::$l2_miss[$l2_cache_key] = true;
			global $_FIELDS;

			if ($_FIELDS === null && file_exists(_PS_TRANSLATIONS_DIR_.$context->language->iso_code.'/fields.php'))
				include_once(_PS_TRANSLATIONS_DIR_.$context->language->iso_code.'/fields.php');

			$key = $class.'_'.md5($field);
			self::$l2_cache[$l2_cache_key] = ((is_array($_FIELDS) && array_key_exists($key, $_FIELDS)) ? ($htmlentities ? htmlentities($_FIELDS[$key], ENT_QUOTES, 'utf-8') : $_FIELDS[$key]) : $field);
		}
		
		return self::$l2_cache[$l2_cache_key];
	}

}
