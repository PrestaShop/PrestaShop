<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include_once(PS_ADMIN_DIR.'/../classes/AdminTab.php');
include_once(PS_ADMIN_DIR.'/../tools/tar/Archive_Tar.php');
include_once(PS_ADMIN_DIR.'/../tools/pear/PEAR.php');
define ('TEXTAREA_SIZED', 70);

class AdminTranslations extends AdminTab
{
	protected $total_expression = 0;
	protected $all_iso_lang = array();
	protected $modules_translations = array();
	const DEFAULT_THEME_NAME = 'default';
	protected static $tpl_regexp = '';
	protected static $php_regexp = '';

	/**
	 * Is true if number of var exceed the suhosin request or post limit 
	 * 
	 * @var boolean
	 */
	protected $suhosin_limit_exceed = false;
	
	public function __construct()
	{
		parent::__construct();
		self::$tpl_regexp = '/\{l s=\''._PS_TRANS_PATTERN_.'\'( mod=\'.+\')?( js=1)?\}/U';
		self::$php_regexp = '/->l\(\''._PS_TRANS_PATTERN_.'\'(, \'(.+)\')?(, (.+))?\)/U';
	}
	
	/**
	 * This method merge each arrays of modules translation in 
	 * the array of modules translations
	 * 
	 * @param boolean $is_default if true a prefix is set before each keys in global $_MODULES array
	 */
	protected function getModuleTranslations($is_default = false)
	{
		global $_MODULES, $_MODULE;

		if (!isset($_MODULE) AND !isset($_MODULES))
			$_MODULES = array();
		elseif (isset($_MODULE))
		{
			if(is_array($_MODULE) AND $is_default === true)
			{
				$_NEW_MODULE = array();
				foreach($_MODULE as $key=>$value)
				{
					$_NEW_MODULE[self::DEFAULT_THEME_NAME.$key] = $value;
				}
				$_MODULE = $_NEW_MODULE;
			}
			$_MODULES = (is_array($_MODULES) AND is_array($_MODULE)) ? array_merge($_MODULES, $_MODULE) : $_MODULE;
		}
	}
	
	/**
	 * This method is only used by AdminTranslations::submitCopyLang().
	 * 
	 * It try to create folder in new theme.
	 * 
	 * When a translation file is copied for a module, its translation key is wrong.
	 * We have to change the translation key and rewrite the file.
	 * 
	 * @param string $dest file name
	 * @return bool
	 */
	protected function checkDirAndCreate($dest)
	{
		$bool = true;
		// To get only folder path
		$path = dirname($dest);
		// If folder wasn't already added
		if (!file_exists($path))
		{
			if(!mkdir($path, 0777, true))
			{
				$bool &= false;
				$this->_errors[] = $this->l('Cannot create the folder').' "'.$path.'". '.$this->l('Check directory writing permisions.');
			}
		}
		return $bool;
	}

	protected function writeTranslationFile($type, $path, $mark = false, $fullmark = false)
	{
		global $currentIndex;

		if ($fd = fopen($path, 'w'))
		{
			unset($_POST['submitTranslations'.$type], $_POST['lang']);
			unset($_POST['token']);
			$toInsert = array();
			foreach($_POST AS $key => $value)
				if (!empty($value))
					$toInsert[$key] = $value;

			$tab = ($fullmark ? Tools::strtoupper($fullmark) : 'LANG').($mark ? Tools::strtoupper($mark) : '');
			fwrite($fd, "<?php\n\nglobal \$_".$tab.";\n\$_".$tab." = array();\n");
			foreach($toInsert AS $key => $value)
				fwrite($fd, '$_'.$tab.'[\''.pSQL($key, true).'\'] = \''.pSQL($value, true).'\';'."\n");
			fwrite($fd, "\n?>");
			fclose($fd);
			Tools::redirectAdmin($currentIndex.'&conf=4&token='.$this->token);
		}
		else
			die('Cannot write language file');
	}

	public function submitCopyLang()
	{
		global $currentIndex;

		if (!($fromLang = strval(Tools::getValue('fromLang'))) OR !($toLang = strval(Tools::getValue('toLang'))))
			$this->_errors[] = $this->l('you must select 2 languages in order to copy data from one to another');
		elseif (!($fromTheme = strval(Tools::getValue('fromTheme'))) OR !($toTheme = strval(Tools::getValue('toTheme'))))
			$this->_errors[] = $this->l('you must select 2 themes in order to copy data from one to another');
		elseif (!Language::copyLanguageData(Language::getIdByIso($fromLang), Language::getIdByIso($toLang)))
			$this->_errors[] = $this->l('an error occurred while copying data');
		elseif ($fromLang == $toLang AND $fromTheme == $toTheme)
			$this->_errors[] = $this->l('nothing to copy! (same language and theme)');
		if (sizeof($this->_errors))
			return ;

		$bool = true;
		$items = Language::getFilesList($fromLang, $fromTheme, $toLang, $toTheme, false, false, true);
		foreach ($items AS $source => $dest)
		{
			$bool &= $this->checkDirAndCreate($dest);
			$bool &= @copy($source, $dest);
			
			if (strpos($dest, 'modules') AND basename($source) === $fromLang.'.php' AND $bool !== false)
			{
				$bool &= $this->changeModulesKeyTranslation($dest, $fromTheme, $toTheme);
			}
		}
		if ($bool)
			Tools::redirectLink($currentIndex.'&conf=14&token='.$this->token);
		$this->_errors[] = $this->l('a part of the data has been copied but some language files could not be found or copied');
	}
	
	/**
	 * Change the key translation to according it to theme name.
	 * 
	 * @param string $path
	 * @param string $theme_from
	 * @param string $theme_to
	 * @return boolean
	 */
	public function changeModulesKeyTranslation ($path, $theme_from, $theme_to)
	{
		$content = file_get_contents($path);
		$arr_replace = array();
		$bool_flag = true;
		if(preg_match_all('#\$_MODULE\[\'([^\']+)\'\]#Ui', $content, $matches))
		{
			foreach ($matches[1] as $key=>$value)
			{
				$arr_replace[$value] = str_replace($theme_from, $theme_to, $value);
			}
			$content = str_replace(array_keys($arr_replace), array_values($arr_replace), $content);
			$bool_flag = (file_put_contents($path, $content) === false) ? false : true;
		}
		return $bool_flag;
	}
	public function submitExportLang()
	{
		global $currentIndex;

		$lang = strtolower(Tools::getValue('iso_code'));
		$theme = strval(Tools::getValue('theme'));
		if ($lang AND $theme)
		{
			$items = array_flip(Language::getFilesList($lang, $theme, false, false, false, false, true));
			$gz = new Archive_Tar(_PS_TRANSLATIONS_DIR_.'/export/'.$lang.'.gzip', true);
			if ($gz->createModify($items, NULL, _PS_ROOT_DIR_));
				Tools::redirect('translations/export/'.$lang.'.gzip');
			$this->_errors[] = Tools::displayError('An error occurred while creating archive.');
		}
		$this->_errors[] = Tools::displayError('Please choose a language and theme.');
	}
	
	public function checkAndAddMailsFiles ($iso_code, $files_list)
	{
		$mails = scandir(_PS_MAIL_DIR_.'en/');
		$mails_new_lang = array();
		foreach ($files_list as $file)
		{
			if (preg_match('#^mails\/([a-z0-9]+)\/#Ui', $file['filename'], $matches))
			{
				$slash_pos = strrpos($file['filename'], '/');
				$mails_new_lang[] = substr($file['filename'], -(strlen($file['filename'])-$slash_pos-1));
			}
		}
		$arr_mails_needed = array_diff($mails, $mails_new_lang);
		foreach ($arr_mails_needed as $mail_to_add)
		{
			if ($mail_to_add !== '.' && $mail_to_add !== '..' && $mail_to_add !== '.svn')
			{
				@copy(_PS_MAIL_DIR_.'en/'.$mail_to_add, _PS_MAIL_DIR_.$iso_code.'/'.$mail_to_add);
			}
		}
	}
	public function submitImportLang()
	{
		global $currentIndex;

		if (!isset($_FILES['file']['tmp_name']) OR !$_FILES['file']['tmp_name'])
			$this->_errors[] = Tools::displayError('No file selected');
		else
		{
			$gz = new Archive_Tar($_FILES['file']['tmp_name'], true);
			$iso_code = str_replace('.gzip', '', $_FILES['file']['name']);
			$files_list = $gz->listContent();
			if ($gz->extract(_PS_TRANSLATIONS_DIR_.'../', false))
			{
				$this->checkAndAddMailsFiles($iso_code, $files_list);
				if (Validate::isLanguageFileName($_FILES['file']['name']))
				{
					if (!Language::checkAndAddLanguage($iso_code))
						$conf = 20;
				}
				Tools::redirectAdmin($currentIndex.'&conf='.(isset($conf) ? $conf : '15').'&token='.$this->token);
			}
			$this->_errors[] = Tools::displayError('Archive cannot be extracted.');
		}
	}
	
	public function submitAddLang()
	{
		global $currentIndex;
		
		// $arr_import_lang[0] = iso lang
		// $arr_import_lang[1] = prestashop version
		$arr_import_lang = explode('|',Tools::getValue('params_import_language'));
		
		if (Validate::isLangIsoCode($arr_import_lang[0]))
		{
			if (@fsockopen('www.prestashop.com', 80))
			{
				if ($content = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/gzip/'.$arr_import_lang[1].'/'.$arr_import_lang[0].'.gzip'))
				{
					$file = _PS_TRANSLATIONS_DIR_.$arr_import_lang[0].'.gzip';
					if (file_put_contents($file, $content))
					{
						$gz = new Archive_Tar($file, true);
						$files_list = $gz->listContent();
						if ($gz->extract(_PS_TRANSLATIONS_DIR_.'../', false))
						{
							$this->checkAndAddMailsFiles($arr_import_lang[0], $files_list);
							if (!Language::checkAndAddLanguage($arr_import_lang[0]))
								$conf = 20;
							if (!unlink($file))
								$this->_errors[] = Tools::displayError('Cannot delete archive');
							Tools::redirectAdmin($currentIndex.'&conf='.(isset($conf) ? $conf : '15').'&token='.$this->token);
						}
						$this->_errors[] = Tools::displayError('Archive cannot be extracted.');
						if (!unlink($file))
							$this->_errors[] = Tools::displayError('Cannot delete archive');
					}
					else
						$this->_errors[] = Tools::displayError('Server does not have permissions for writing.');
				}
				else
					$this->_errors[] = Tools::displayError('Language not found');
			}
			else
				$this->_errors[] = Tools::displayError('archive cannot be downloaded from prestashop.com.');
		}
		else
			$this->_errors[] = Tools::displayError('Invalid parameter');
	}
	
	/**
	 * This method check each file (tpl or php file), get its sentences to translate,
	 * compare with posted values and write in iso code translation file.
	 * 
	 * @param string $file_name
	 * @param array $files
	 * @param string $theme_name
	 * @param string $module_name
	 * @param string|boolean $dir
	 * @return void
	 */
	protected function findAndWriteTranslationsIntoFile($file_name, $files, $theme_name, $module_name, $dir = false)
	{
		// These static vars allow to use file to write just one time.
		static $_cache_file = array();
		static $str_write = '';
		static $array_check_duplicate = array();
		
		// Default translations and Prestashop overriding themes are distinguish
		$is_default = $theme_name === self::DEFAULT_THEME_NAME ? true : false;
		
		// Set file_name in static var, this allow to open and wright the file just one time
		if (!isset($_cache_file[($is_default ? self::DEFAULT_THEME_NAME : $theme_name).'-'.$file_name]) )
		{
			$str_write = '';
			$_cache_file[($is_default ? self::DEFAULT_THEME_NAME : $theme_name).'-'.$file_name] = true;
			if(!file_exists($file_name))
				file_put_contents($file_name, '');
			if (!is_writable($file_name))
				die ($this->l('Cannot write the theme\'s language file ').'('.$file_name.')'.$this->l('. Please check write permissions.'));
				
			// this string is initialized one time for a file
			$str_write .= "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
			$array_check_duplicate = array();
		}
			
		if (!$dir)
			$dir = ($theme_name == self::DEFAULT_THEME_NAME ? _PS_MODULE_DIR_.$module_name.'/' : _PS_ALL_THEMES_DIR_.$theme_name.'/modules/'.$module_name.'/');
		
		foreach ($files AS $template_file)
		{
			if ((preg_match('/^(.*).tpl$/', $template_file) OR ($is_default AND preg_match('/^(.*).php$/', $template_file))) AND file_exists($tpl = $dir.$template_file))
			{
				// Get translations key
				$content = file_get_contents($tpl);
				preg_match_all(substr($template_file, -4) == '.tpl' ? self::$tpl_regexp : self::$php_regexp, $content, $matches);
				
				// Write each translation on its module file
				$template_name = substr(basename($template_file), 0, -4);
				
				foreach ($matches[1] AS $key)
				{
					$post_key = md5(strtolower($module_name).'_'.($is_default ? self::DEFAULT_THEME_NAME : strtolower($theme_name)).'_'.strtolower($template_name).'_'.md5($key));
					$pattern = '\'<{'.strtolower($module_name).'}'.($is_default ? 'prestashop' : strtolower($theme_name)).'>'.strtolower($template_name).'_'.md5($key).'\'';
					if (array_key_exists($post_key, $_POST) AND !empty($_POST[$post_key]) AND !in_array($pattern, $array_check_duplicate))
					{
						$array_check_duplicate[] = $pattern;
						$str_write .= '$_MODULE['.$pattern.'] = \''.pSQL($_POST[$post_key]).'\';'."\n";
						$this->total_expression++;
					}
				}
			}
		}
		if (isset($_cache_file[($is_default ? self::DEFAULT_THEME_NAME : $theme_name).'-'.$file_name]) AND $str_write != "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n")
			file_put_contents($file_name, $str_write);
	}
	public function clearModuleFiles ($files, $type_clear = 'file', $path = '')
	{
		$arr_exclude = array('img', 'js', 'mails');
		$arr_good_ext = array('.tpl', '.php');
		foreach ($files as $key=>$file)
		{
			if ($file{0} === '.' OR in_array(substr($file, 0, strrpos($file,'.')), $this->all_iso_lang))
				unset($files[$key]);
			else if ($type_clear === 'file' AND !in_array(substr($file, strrpos($file,'.')),$arr_good_ext))
				unset($files[$key]);
			else if ($type_clear === 'directory' AND (!is_dir($path.$file) OR in_array($file, $arr_exclude)))
				unset($files[$key]);
				
		}
		return $files;
	}
	/**
	 * This method get translation for each files of a module,
	 * compare with global $_MODULES array and fill AdminTranslations::modules_translations array
	 * With key as English sentences and values as their iso code translations. 
	 *  
	 * @param array $files
	 * @param string $theme_name
	 * @param string $module_name
	 * @param string|boolean $dir
	 * @param string $iso_code
	 * @return void
	 */
	protected function findAndFillTranslations($files, $theme_name, $module_name, $dir = false, $iso_code = '')
	{
		global $_MODULES;
		// added for compatibility
		$_MODULES = array_change_key_case($_MODULES);
		
		// Default translations and Prestashop overriding themes are distinguish
		$is_default = $theme_name === self::DEFAULT_THEME_NAME ? true : false;
		
		if (!$dir)
			$dir = ($theme_name === self::DEFAULT_THEME_NAME ? _PS_MODULE_DIR_.$module_name.'/' : _PS_ALL_THEMES_DIR_.$theme_name.'/modules/'.$module_name.'/');
		
		// Thank to this var similar keys are not duplicate 
		// in AndminTranslation::modules_translations array
		// see below
		$array_check_duplicate = array();
		foreach ($files AS $template_file)
		{
			if ((preg_match('/^(.*).tpl$/', $template_file) OR ($is_default AND preg_match('/^(.*).php$/', $template_file))) AND file_exists($tpl = $dir.$template_file))
			{
				// Get translations key
				$content = file_get_contents($tpl);
				preg_match_all(substr($template_file, -4) == '.tpl' ? self::$tpl_regexp : self::$php_regexp, $content, $matches);
				
				// Write each translation on its module file
				$template_name = substr(basename($template_file), 0, -4);
				
				foreach ($matches[1] AS $key)
				{
					$module_key = ($is_default ? self::DEFAULT_THEME_NAME : '').'<{'.Tools::strtolower($module_name).'}'.strtolower($is_default ? 'prestashop' : $theme_name).'>'.Tools::strtolower($template_name).'_'.md5($key);
					// to avoid duplicate entry
					if (!in_array($module_key, $array_check_duplicate))
					{
						$array_check_duplicate[] = $module_key;
						$this->modules_translations[strtolower($is_default ? self::DEFAULT_THEME_NAME : $theme_name)][$module_name][$template_name][$key]
							 = key_exists($module_key, $_MODULES) ? html_entity_decode($_MODULES[$module_key], ENT_COMPAT, 'UTF-8') : '';
						$this->total_expression++;
					}
				}
			}
		}
	}

	public function postProcess()
	{
		global $currentIndex;

		if (Tools::isSubmit('submitCopyLang'))
		{
		 	if ($this->tabAccess['add'] === '1')
				$this->submitCopyLang();
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (Tools::isSubmit('submitExport'))
		{
			if ($this->tabAccess['add'] === '1')
				$this->submitExportLang();
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (Tools::isSubmit('submitImport'))
		{
		 	if ($this->tabAccess['add'] === '1')
				$this->submitImportLang();
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (Tools::isSubmit('submitAddLanguage'))
		{
			if ($this->tabAccess['add'] === '1')
				$this->submitAddLang();
			else
				$this->_errors[] = Tools::displayError('You do not have permission to add here.');
		}
		elseif (Tools::isSubmit('submitTranslationsFront'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('Front', _PS_THEME_DIR_.'lang/'.Tools::strtolower(Tools::getValue('lang')).'.php');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::isSubmit('submitTranslationsPDF'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('PDF', _PS_TRANSLATIONS_DIR_.Tools::strtolower(Tools::getValue('lang')).'/pdf.php', 'PDF');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::isSubmit('submitTranslationsBack'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('Back', _PS_TRANSLATIONS_DIR_.Tools::strtolower(Tools::getValue('lang')).'/admin.php', 'ADM');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::isSubmit('submitTranslationsErrors'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('Errors', _PS_TRANSLATIONS_DIR_.Tools::strtolower(Tools::getValue('lang')).'/errors.php', false, 'ERRORS');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::isSubmit('submitTranslationsFields'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('Fields', _PS_TRANSLATIONS_DIR_.Tools::strtolower(Tools::getValue('lang')).'/fields.php', false, 'FIELDS');
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');

		}
		elseif (Tools::isSubmit('submitTranslationsMails') || Tools::isSubmit('submitTranslationsMailsAndStay'))
		{
		 	if ($this->tabAccess['edit'] === '1' && ($id_lang = Language::getIdByIso(Tools::getValue('lang'))) > 0)
		 	{
		 		$this->submitTranslationsMails($id_lang);
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		elseif (Tools::isSubmit('submitTranslationsModules'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				$array_lang_src = Language::getLanguages(false);
				foreach ($array_lang_src as $language)
					$this->all_iso_lang[] = $language['iso_code'];
				
				$lang = Tools::strtolower($_POST['lang']);
				if (!Validate::isLanguageIsoCode($lang))
					die(Tools::displayError());
				if (!$modules = scandir(_PS_MODULE_DIR_))
					$this->displayWarning(Tools::displayError('There are no modules in your copy of PrestaShop. Use the Modules tab to activate them or go to our Website to download additional Modules.'));
				else
				{
					$arr_find_and_write = array();
					$arr_files = $this->getAllModuleFiles($modules, _PS_MODULE_DIR_, $lang, true);
					$arr_find_and_write = array_merge($arr_find_and_write, $arr_files);
					
					if(file_exists(_PS_THEME_DIR_.'/modules/'))
					{
						$modules = scandir(_PS_THEME_DIR_.'/modules/');
						$arr_files = $this->getAllModuleFiles($modules, _PS_THEME_DIR_.'modules/', $lang);
						$arr_find_and_write = array_merge($arr_find_and_write, $arr_files);
					}
					
					foreach ($arr_find_and_write as $key=>$value)
						$this->findAndWriteTranslationsIntoFile($value['file_name'], $value['files'], $value['theme'], $value['module'], $value['dir']);
					Tools::redirectAdmin($currentIndex.'&conf=4&token='.$this->token);
				}
			}
			else
				$this->_errors[] = Tools::displayError('You do not have permission to edit here.');
		}
	}
	protected function getMailPattern()
	{
		// Let the indentation like it.
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>#title</title>
</head>
<body>
	#content
</body>
</html>';
	}
	/**
	 * This method is used to wright translation for mails.
	 * This wrights subject translation files 
	 * (in root/mails/lang_choosen/lang.php or root/_PS_THEMES_DIR_/mails/lang_choosen/lang.php)
	 * and mails files. 
	 *  
	 * @param int $id_lang
	 */
	protected function submitTranslationsMails ($id_lang)
	{
		global $currentIndex;
		$obj_lang = new Language($id_lang);
		$params_redirect = (Tools::isSubmit('submitTranslationsMailsAndStay') ? '&lang='.Tools::getValue('lang').'&type='.Tools::getValue('type') : '');
		
		$arr_mail_content = array();
		$arr_mail_path = array();
		if (Tools::getValue('core_mail')) {
			$arr_mail_content['core_mail'] = Tools::getValue('core_mail');
			$arr_mail_path['core_mail'] = _PS_MAIL_DIR_.$obj_lang->iso_code.'/';
		}
		if (Tools::getValue('module_mail')) {
			$arr_mail_content['module_mail'] = Tools::getValue('module_mail');
			$arr_mail_path['module_mail'] = _PS_MODULE_DIR_.'{module}'.'/mails/'.$obj_lang->iso_code.'/';
		}
		if (Tools::getValue('theme_mail')) {
			$arr_mail_content['theme_mail'] = Tools::getValue('theme_mail');
			$arr_mail_path['theme_mail'] = _PS_THEME_DIR_.'mails/'.$obj_lang->iso_code.'/';
		}
		if (Tools::getValue('theme_module_mail')) {
			$arr_mail_content['theme_module_mail'] = Tools::getValue('theme_module_mail');
			$arr_mail_path['theme_module_mail'] = _PS_THEME_DIR_.'modules/{module}'.'/mails/'.$obj_lang->iso_code.'/';
		}
		
		// Save each mail content
		foreach ($arr_mail_content as $group_name=>$all_content)
		{
			foreach ($all_content as $type_content=>$mails)
			{
				foreach ($mails as $mail_name=>$content)
				{
					
					$module_name = false;
					$module_name_pipe_pos = stripos($mail_name, '|');
					if ($module_name_pipe_pos)
					{
						$module_name = substr($mail_name, 0, $module_name_pipe_pos);
						$mail_name = substr($mail_name, $module_name_pipe_pos+1);
					}
					
					if ($type_content == 'html')
					{
						$content = Tools::htmlentitiesUTF8($content);
						$content = htmlspecialchars_decode($content);
						
						$title = '';
						if (Tools::getValue('title_'.$group_name.'_'.$mail_name))
						{
							$title = Tools::getValue('title_'.$group_name.'_'.$mail_name);
						}
						$string_mail = $this->getMailPattern();
						$content = str_replace(array('#title', '#content'), array($title, $content), $string_mail);

						// Magic Quotes shall... not.. PASS!
						if (_PS_MAGIC_QUOTES_GPC_)
							$content = stripslashes($content);
					}
					if (Validate::isCleanHTML($content))
					{
						$path = $arr_mail_path[$group_name];
						if ($module_name)
							$path = str_replace('{module}', $module_name, $path);
						file_put_contents($path.$mail_name.'.'.$type_content, $content);
						chmod($path.$mail_name.'.'.$type_content, 0777);
					}
					else
					{
						$this->_errors[] = Tools::displayError('HTML mails templates cannot contain JavaScript code.');
					}
				}
			}
		}
		
		// Update subjects
		$array_subjects = array();
		if ($subjects = Tools::getValue('subject') AND is_array($subjects))
		{
			$array_subjects['core_and_modules'] = array('translations'=>array(), 'path'=>$arr_mail_path['core_mail'].'lang.php');
			if (isset($arr_mail_path['theme_mail']))
				$array_subjects['themes_and_modules'] = array('translations'=>array(), 'path'=>$arr_mail_path['theme_mail'].'lang.php');
			
			foreach ($subjects AS $group => $subject_translation)
			{
				if ($group == 'core_mail' || $group == 'module_mail') {
					$array_subjects['core_and_modules']['translations'] = array_merge($array_subjects['core_and_modules']['translations'], $subject_translation);
				}
				elseif ( isset($array_subjects['themes_and_modules']) && ($group == 'theme_mail' || $group == 'theme_module_mail')) {
					$array_subjects['themes_and_modules']['translations'] = array_merge($array_subjects['themes_and_modules']['translations'], $subject_translation);
				}
			}
		}
		if (!empty($array_subjects)) {
			foreach ($array_subjects as $type=>$infos) {
				$this->writeSubjectTranslationFile($infos['translations'], $infos['path']);
			}
		}
		if (count($this->_errors) == 0)
			Tools::redirectAdmin($currentIndex.'&conf=4&token='.$this->token.$params_redirect);
	}
	public function display()
	{
		global $currentIndex, $cookie;

		$translations = array(
			'front' => $this->l('Front Office translations'),
			'back' => $this->l('Back Office translations'),
			'errors' => $this->l('Error message translations'),
			'fields' => $this->l('Field name translations'),
			'modules' => $this->l('Module translations'),
			'pdf' => $this->l('PDF translations'),
			'mails' => $this->l('E-mail template translations'),
		);

		if ($type = Tools::getValue('type'))
			$this->{'displayForm'.ucfirst($type)}(Tools::strtolower(Tools::getValue('lang')));
		else
		{
			$languages = Language::getLanguages(false);
			echo '<fieldset><legend><img src="../img/admin/translation.gif" />'.$this->l('Modify translations').'</legend>'.
			$this->l('Here you can modify translations for all text input into PrestaShop.').'<br />'.
			$this->l('First, select a section (such as Back Office or Modules), then click the flag representing the language you want to edit.').'<br /><br />
			<form method="get" action="index.php" id="typeTranslationForm">
				<input type="hidden" name="tab" value="AdminTranslations" />
				<input type="hidden" name="lang" id="translation_lang" value="0" />
				<select name="type" style="float:left; margin-right:10px;">';
			foreach ($translations AS $key => $translation)
				echo '<option value="'.$key.'">'.$translation.'&nbsp;</option>';
			echo '</select>';
			foreach ($languages AS $language)
				echo '<a href="javascript:chooseTypeTranslation(\''.$language['iso_code'].'\')">
						<img src="'._THEME_LANG_DIR_.$language['id_lang'].'.jpg" alt="'.$language['iso_code'].'" title="'.$language['iso_code'].'" />
					</a>';
			echo '<input type="hidden" name="token" value="'.$this->token.'" /></form></fieldset>
			<br /><br /><h2>'.$this->l('Translation exchange').'</h2>';
			echo '<form action="'.$currentIndex.'&token='.$this->token.'" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend onclick="$(\'#submitAddLangContent\').slideDown(\'slow\'); $(\'#submitImportContent\').slideUp(\'slow\');" style="cursor:pointer;">
					<img src="../img/admin/import.gif" />'.$this->l('Add a language').'
				</legend>
				<div id="submitAddLangContent" style="float:left;"><p>'.$this->l('You can add a language directly from prestashop.com here').'</p>
					<div style="font-weight:bold; float:left;">'.$this->l('Language you want to add:').' ';
			
			// create a context to test www.prestashop.com connection 
			$opts = array(
				'http'=>array(
					'protocol_version'=>'1.1',
					'method'=>'GET',
				)
			);
			$context = stream_context_create($opts);
			if (@file_get_contents('http://www.prestashop.com', false, $context))
			{
				// Get all iso code available
				if(fsockopen('www.prestashop.com', 80))
				{
					$lang_packs = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_each_language_pack.php?version='._PS_VERSION_);
					if ($lang_packs!== false && $lang_packs != '' && $lang_packs = Tools::jsonDecode($lang_packs))
					{
						echo 	'<select id="params_import_language" name="params_import_language">';
						echo '<optgroup label="'.$this->l('Add a language').'">';
						$alreadyInstalled = '<optgroup label="'.$this->l('Update a language').'">';
						foreach($lang_packs AS $lang_pack)
							if (!Language::isInstalled($lang_pack->iso_code))
								echo '<option value="'.$lang_pack->iso_code.'|'.$lang_pack->version.'">'.$lang_pack->name.'</option>';
							else 
								$alreadyInstalled.='<option value="'.$lang_pack->iso_code.'|'.$lang_pack->version.'">'.$lang_pack->name.'</option>';
						echo '</optgroup>'.$alreadyInstalled.'</optgroup>';
						echo 	'</select>';
					}
					echo '	</div>
					<div style="float:left">
						<input type="submit" value="'.$this->l('Add the language').'" name="submitAddLanguage" class="button" style="margin:0px 0px 0px 25px;" />
					</div>';
				}
			}
			else
				echo '<br /><br /><p class="error">'.$this->l('Cannot connect to prestashop.com to get languages list.').'</p></div>';
			echo '	</div>
			</fieldset>
			</form><br />';
			echo '<form action="'.$currentIndex.'&token='.$this->token.'" method="post" enctype="multipart/form-data">
				<fieldset>
					<legend onclick="$(\'#submitImportContent\').slideDown(\'slow\'); $(\'#submitAddLangContent\').slideUp(\'slow\');" style="cursor:pointer;">
						<img src="../img/admin/import.gif" />'.$this->l('Import a language pack manually').'
					</legend>
					<div id="submitImportContent" style="display:none;">
						<p>'.$this->l('Import data from file (language pack).').'<br />'.
						$this->l('If the name format is: isocode.gzip (e.g. fr.gzip) and the language corresponding to this package does not exist, it will automatically be created.').'<br />'.
						$this->l('Be careful, as it will replace all existing data for the destination language!').'<br />'.
						$this->l('Browse your computer for the language file to be imported:').'</p>
						<div style="float:left;">
							<p>
								<div style="width:75px; font-weight:bold; float:left;">'.$this->l('From:').'</div>
								<input type="file" name="file" />
							</p>
						</div>
						<div style="float:left;">
							<input type="submit" value="'.$this->l('Import').'" name="submitImport" class="button" style="margin:10px 0px 0px 25px;" />
						</div>
					</div>
				</fieldset>
			</form>
			<br /><br />
			<form action="'.$currentIndex.'&token='.$this->token.'" method="post" enctype="multipart/form-data">
				<fieldset><legend><img src="../img/admin/export.gif" />'.$this->l('Export a language').'</legend>
					<p>'.$this->l('Export data from one language to a file (language pack).').'<br />'.
					$this->l('Choose the theme from which you want to export translations.').'<br />
					<select name="iso_code" style="margin-top:10px;">';
				foreach ($languages AS $language)
					echo '<option value="'.$language['iso_code'].'">'.$language['name'].'</option>';
				echo '
					</select>
					&nbsp;&nbsp;&nbsp;
					<select name="theme" style="margin-top:10px;">';
				$themes = self::getThemesList();
				foreach ($themes AS $theme)
					echo '<option value="'.$theme['name'].'">'.$theme['name'].'</option>';
				echo '
					</select>&nbsp;&nbsp;
					<input type="submit" class="button" name="submitExport" value="'.$this->l('Export').'" />
				</fieldset>
			</form>
			<br /><br />';
			$allLanguages = Language::getLanguages(false);
			echo '
			<form action="'.$currentIndex.'&token='.$this->token.'" method="post">
				<fieldset><legend><img src="../img/admin/copy_files.gif" />'.$this->l('Copy').'</legend>
					<p>'.$this->l('Copies data from one language to another.').'<br />'.
					$this->l('Be careful, as it will replace all existing data for the destination language!').'<br />'.
					$this->l('If necessary').', <b><a href="index.php?tab=AdminLanguages&addlang&token='.Tools::getAdminToken('AdminLanguages'.(int)(Tab::getIdFromClassName('AdminLanguages')).(int)($cookie->id_employee)).'">'.$this->l('first create a new language').'</a></b>.</p>
					<div style="float:left;">
						<p>
							<div style="width:75px; font-weight:bold; float:left;">'.$this->l('From:').'</div>
							<select name="fromLang">';
					foreach	($languages AS $language)
						echo '<option value="'.$language['iso_code'].'">'.$language['name'].'</option>';
					echo '
							</select>
							&nbsp;&nbsp;&nbsp;
							<select name="fromTheme">';
						$themes = self::getThemesList();
						foreach ($themes AS $theme)
							echo '<option value="'.$theme['name'].'">'.$theme['name'].'</option>';
						echo '
							</select> <span style="font-style: bold; color: red;">*</span>
						</p>
						<p>
							<div style="width:75px; font-weight:bold; float:left;">'.$this->l('To:').'</div>
							<select name="toLang">';
					foreach	($allLanguages AS $language)
						echo '<option value="'.$language['iso_code'].'">'.$language['name'].'</option>';
					echo '
							</select>
							&nbsp;&nbsp;&nbsp;
							<select name="toTheme">';
						$themes = self::getThemesList();
						foreach ($themes AS $theme)
							echo '<option value="'.$theme['name'].'">'.$theme['name'].'</option>';
						echo '
							</select>
						</p>
					</div>
					<div style="float:left;">
						<input type="submit" value="'.$this->l('   Copy   ').'" name="submitCopyLang" class="button" style="margin:25px 0px 0px 25px;" />
					</div>
					<p style="clear: left; padding: 16px 0px 0px 0px;"><span style="font-style: bold; color: red;">*</span> '.$this->l('Language files (as indicated at Tools >> Languages >> Edition) must be complete to allow copying of translations').'</p>
				</fieldset>
			</form>';
		}
	}

	public function fileExists($dir, $file, $var)
	{
		${$var} = array();
		if (!file_exists($dir))
			if (!mkdir($dir, 0700))
				die('Please create the directory '.$dir);
		if (!file_exists($dir.'/'.$file))
			if (!file_put_contents($dir.'/'.$file, "<?php\n\nglobal \$".$var.";\n\$".$var." = array();\n\n?>"))
				die('Please create a "'.$file.'" file in '.$dir);
		if (!is_writable($dir.'/'.$file))
			$this->displayWarning(Tools::displayError('This file must be writable:').' '.$dir.'/'.$file);
		include($dir.'/'.$file);
		return ${$var};
	}

	public function displayToggleButton($closed = false)
	{
		$str_output = '
		<script type="text/javascript">';
		if (Tools::getValue('type') == 'mails')
			$str_output .= '$(document).ready(function(){
				openCloseAllDiv(\''.$_GET['type'].'_div\', this.value == openAll); toggleElemValue(this.id, openAll, closeAll);
				});';
		$str_output .= '
			var openAll = \''.html_entity_decode($this->l('Expand all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
			var closeAll = \''.html_entity_decode($this->l('Close all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
		</script>
		<input type="button" class="button" id="buttonall" onclick="openCloseAllDiv(\''.$_GET['type'].'_div\', this.value == openAll); toggleElemValue(this.id, openAll, closeAll);" />
		<script type="text/javascript">toggleElemValue(\'buttonall\', '.($closed ? 'openAll' : 'closeAll').', '.($closed ? 'closeAll' : 'openAll').');</script>';
		return $str_output;
	}
	
	protected function displaySubmitButtons($name)
	{
		return '
			<input type="submit" name="submitTranslations'.ucfirst($name).'" value="'.$this->l('Update translations').'" class="button" />
			<input type="submit" name="submitTranslations'.ucfirst($name).'AndStay" value="'.$this->l('Update and stay').'" class="button" />';
	}
	
	public function displayAutoTranslate()
	{
		$languageCode = Tools::htmlentitiesUTF8(Language::getLanguageCodeByIso(Tools::getValue('lang')));
		return '
		<input type="button" class="button" onclick="translateAll();" value="'.$this->l('Translate with Google (experimental)').'" />
		<script type="text/javascript" src="http://www.google.com/jsapi"></script>
		<script type="text/javascript">
			var gg_translate = {
				language_code : \''.$languageCode.'\',
				not_available : \''.addslashes(html_entity_decode($this->l('this language is not available on Google Translate API'), ENT_QUOTES, 'utf-8')).'\',
				tooltip_title : \''.addslashes(html_entity_decode($this->l('Google translate suggests :'), ENT_QUOTES, 'utf-8')).'\'
			};
		</script>
		<script type="text/javascript" src="../js/gg-translate.js"></script>
		<script type="text/javascript">
			var displayOnce = 0;
			google.load("language", "1");
			function translateAll() {
				if (!ggIsTranslatable(gg_translate[\'language_code\']))
					alert(\'"\'+gg_translate[\'language_code\']+\'" : \'+gg_translate[\'not_available\']);
				else
				{
					$.each($(\'input[type="text"]\'), function() {
						var tdinput = $(this);
						if (tdinput.attr("value") == "" && tdinput.parent("td").prev().html()) {
							google.language.translate(tdinput.parent("td").prev().html(), "en", gg_translate[\'language_code\'], function(result) {
								if (!result.error)
									tdinput.val(result.translation);
								else if (displayOnce == 0)
								{
									displayOnce = 1;
									alert(result.error.message);
								}
							});
						}
					});
					$.each($("textarea"), function() {
						var tdtextarea = $(this);
						if (tdtextarea.html() == "" && tdtextarea.parent("td").prev().html()) {
							google.language.translate(tdtextarea.parent("td").prev().html(), "en", gg_translate[\'language_code\'], function(result) {
								if (!result.error)
									tdtextarea.html(result.translation);
								else if (displayOnce == 0)
								{
									displayOnce = 1;
									alert(result.error.message);
								}
							});
						}
					});
				}
			}
		</script>';
	}
	
	public function displayLimitPostWarning($count)
	{
		$str_output = '';
		if ((ini_get('suhosin.post.max_vars') AND ini_get('suhosin.post.max_vars') < $count)
		 OR (ini_get('suhosin.request.max_vars') AND ini_get('suhosin.request.max_vars') < $count))
		{
			if (ini_get('suhosin.post.max_vars') < $count OR ini_get('suhosin.request.max_vars') < $count)
			{
				$this->suhosin_limit_exceed = true;
				$str_output .= '<div class="warning">'.$this->l('Warning, your hosting provider is using the suhosin patch for PHP, which limit the maximum number of fields to post in a form :').'<br/>'
				.'<b>'.ini_get('suhosin.post.max_vars').'</b> '.$this->l('for suhosin.post.max_vars.').'<br/>'
				.'<b>'.ini_get('suhosin.request.max_vars').'</b> '.$this->l('for suhosin.request.max_vars.').'<br/>'
				.$this->l('Please ask your hosting provider to increase the suhosin post and request limit to')
				.' <u><b>'.((int)$count + 100).'</b></u> '.$this->l('at least.').' '.$this->l('or edit the translation file manually.').'</div>'; 
			}
		}
		return $str_output;
	}

	public function displayFormFront($lang)
	{
		global $currentIndex;
		$_LANG = $this->fileExists(_PS_THEME_DIR_.'lang', Tools::strtolower($lang).'.php', '_LANG');
		$str_output = '';
		
		/* List templates to parse */
		$templates = array_merge(scandir(_PS_THEME_DIR_), scandir(_PS_ALL_THEMES_DIR_));
		$count = 0;
		$files = array();
		foreach ($templates AS $template)
			if (preg_match('/^(.*).tpl$/', $template) AND (file_exists($tpl = _PS_THEME_DIR_.$template) OR file_exists($tpl = _PS_ALL_THEMES_DIR_.$template)))
			{
				$template2 = substr(basename($template), 0, -4);
				$newLang = array();
				$fd = fopen($tpl, 'r');
				$content = fread($fd, filesize($tpl));

				/* Search language tags (eg {l s='to translate'}) */
				$regex = '/\{l s=\''._PS_TRANS_PATTERN_.'\'( js=1)?\}/U';
				preg_match_all($regex, $content, $matches);

				/* Get string translation */
				foreach($matches[1] AS $key)
				{
					if(empty($key))
					{
						$this->_errors[] = $this->l('Empty string found, please edit:').' <br />'._PS_THEME_DIR_.''.$template;
						$newLang[$key] = '';
					}
					else
					{
						$key2 = $template2.'_'.md5($key);
						$newLang[$key] = (key_exists($key2, $_LANG)) ? html_entity_decode($_LANG[$key2], ENT_COMPAT, 'UTF-8') : '';
					}
				}
				$files[$template2] = $newLang;
				$count += sizeof($newLang);
			}

		$str_output .= '
		<h2>'.$this->l('Language').' : '.Tools::strtoupper($lang).' - '.$this->l('Front-Office translations').'</h2>
		'.$this->l('Total expressions').' : <b>'.$count.'</b>. '.$this->l('Click the fieldset title to expand or close the fieldset.').'.<br /><br />';
		$str_output .= $this->displayLimitPostWarning($count);
		if (!$this->suhosin_limit_exceed)
		{
			$str_output .= '
			<form method="post" action="'.$currentIndex.'&submitTranslationsFront=1&token='.$this->token.'" class="form">';
			$str_output .= $this->displayToggleButton(sizeof($_LANG) >= $count);
			$str_output .= $this->displayAutoTranslate();
			$str_output .= '<input type="hidden" name="lang" value="'.$lang.'" /><input type="submit" name="submitTranslationsFront" value="'.$this->l('Update translations').'" class="button" /><br /><br />';
			foreach ($files AS $k => $newLang)
				if (sizeof($newLang))
				{
					$countValues = array_count_values($newLang);
					$empty = isset($countValues['']) ? $countValues[''] : 0;
					$str_output .= '
					<fieldset><legend style="cursor : pointer" onclick="$(\'#'.$k.'-tpl\').slideToggle();">'.$k.' - <font color="blue">'.sizeof($newLang).'</font> '.$this->l('expressions').' (<font color="red">'.$empty.'</font>)</legend>
						<div name="front_div" id="'.$k.'-tpl" style="display: '.($empty ? 'block' : 'none').';">
							<table cellpadding="2">';
					foreach ($newLang AS $key => $value)
					{
						$str_output .= '<tr><td style="width: 40%">'.stripslashes($key).'</td><td>';
						if (strlen($key) != 0 && strlen($key) < TEXTAREA_SIZED)
							$str_output .= '= <input type="text" style="width: 450px" name="'.$k.'_'.md5($key).'" value="'.stripslashes(preg_replace('/"/', '\&quot;', stripslashes($value))).'" />';
						elseif(strlen($key))
							$str_output .= '= <textarea rows="'.(int)(strlen($key) / TEXTAREA_SIZED).'" style="width: 450px" name="'.$k.'_'.md5($key).'">'.stripslashes(preg_replace('/"/', '\&quot;', stripslashes($value))).'</textarea>';
						else
							$str_output .= '<span class="error-inline">'.implode(', ', $this->_errors).'</span>';
						$str_output .= '</td></tr>';
					}
					$str_output .= '
							</table>
						</div>
					</fieldset><br />';
				}
			$str_output .= '<br /><input type="submit" name="submitTranslationsFront" value="'.$this->l('Update translations').'" class="button" /></form>';
		}
		if (!empty($this->_errors))
			$this->displayErrors();
		echo $str_output;
	}

	public function displayFormBack($lang)
	{
		global $currentIndex;
		$_LANGADM = $this->fileExists(_PS_TRANSLATIONS_DIR_.$lang, 'admin.php', '_LANGADM');
		$str_output = '';
		/* List templates to parse */
		$count = 0;
		$tabs = scandir(PS_ADMIN_DIR.'/tabs');
		$tabs[] = '../../classes/AdminTab.php';
		$files = array();
		foreach ($tabs AS $tab)
			if (preg_match('/^(.*)\.php$/', $tab) AND file_exists($tpl = PS_ADMIN_DIR.'/tabs/'.$tab))
			{
				$tab = basename(substr($tab, 0, -4));
				$fd = fopen($tpl, 'r');
				$content = fread($fd, filesize($tpl));
				fclose($fd);
				$regex = '/this->l\(\''._PS_TRANS_PATTERN_.'\'[\)|\,]/U';
				preg_match_all($regex, $content, $matches);
				foreach ($matches[1] AS $key)
					$tabsArray[$tab][$key] = stripslashes(key_exists($tab.md5($key), $_LANGADM) ? html_entity_decode($_LANGADM[$tab.md5($key)], ENT_COMPAT, 'UTF-8') : '');
				$count += isset($tabsArray[$tab]) ? sizeof($tabsArray[$tab]) : 0;
			}
		foreach (array('header.inc', 'footer.inc', 'index', 'login', 'password', 'functions') AS $tab)
		{
			$tab = PS_ADMIN_DIR.'/'.$tab.'.php';
			$fd = fopen($tab, 'r');
			$content = fread($fd, filesize($tab));
			fclose($fd);
			$regex = '/translate\(\''._PS_TRANS_PATTERN_.'\'\)/U';
			preg_match_all($regex, $content, $matches);
			foreach ($matches[1] AS $key)
				$tabsArray['index'][$key] = stripslashes(key_exists('index'.md5($key), $_LANGADM) ? html_entity_decode($_LANGADM['index'.md5($key)], ENT_COMPAT, 'UTF-8') : '');
			$count += isset($tabsArray['index']) ? sizeof($tabsArray['index']) : 0;
		}

		$str_output .= '
		<h2>'.$this->l('Language').' : '.Tools::strtoupper($lang).' - '.$this->l('Back-Office translations').'</h2>
		'.$this->l('Expressions to translate').' : <b>'.$count.'</b>. '.$this->l('Click on the titles to open fieldsets').'.<br /><br />';
		$str_output .= $this->displayLimitPostWarning($count);
		if (!$this->suhosin_limit_exceed)
		{
			$str_output .= '
			<form method="post" action="'.$currentIndex.'&submitTranslationsBack=1&token='.$this->token.'" class="form">';
			$str_output .= $this->displayToggleButton();
			$str_output .= $this->displayAutoTranslate();
			$str_output .= '<input type="hidden" name="lang" value="'.$lang.'" /><input type="submit" name="submitTranslationsBack" value="'.$this->l('Update translations').'" class="button" /><br /><br />';
			foreach ($tabsArray AS $k => $newLang)
				if (sizeof($newLang))
				{
					$countValues = array_count_values($newLang);
					$empty = isset($countValues['']) ? $countValues[''] : 0;
					$str_output .= '
					<fieldset><legend style="cursor : pointer" onclick="$(\'#'.$k.'-tpl\').slideToggle();">'.$k.' - <font color="blue">'.sizeof($newLang).'</font> '.$this->l('expressions').' (<font color="red">'.$empty.'</font>)</legend>
						<div name="back_div" id="'.$k.'-tpl" style="display: '.($empty ? 'block' : 'none').';">
							<table cellpadding="2">';
					foreach ($newLang AS $key => $value)
					{
						$str_output .= '<tr><td style="width: 40%">'.stripslashes($key).'</td><td>= ';
						if (strlen($key) < TEXTAREA_SIZED)
							$str_output .= '<input type="text" style="width: 450px" name="'.$k.md5($key).'" value="'.stripslashes(preg_replace('/"/', '\&quot;', $value)).'" /></td></tr>';
						else
							$str_output .= '<textarea rows="'.(int)(strlen($key) / TEXTAREA_SIZED).'" style="width: 450px" name="'.$k.md5($key).'">'.stripslashes(preg_replace('/"/', '\&quot;', $value)).'</textarea></td></tr>';
					}
					$str_output .= '
							</table>
						</div>
					</fieldset><br />';
				}
			$str_output .= '<br /><input type="submit" name="submitTranslationsBack" value="'.$this->l('Update translations').'" class="button" /></form>';
		}
		echo $str_output;
	}

	public function displayFormErrors($lang)
	{
		global $currentIndex;
		$_ERRORS = $this->fileExists(_PS_TRANSLATIONS_DIR_.$lang, 'errors.php', '_ERRORS');
		
		$str_output = '';
		
		/* List files to parse */
		$stringToTranslate = array();
		$dirToParse = array(PS_ADMIN_DIR.'/../',
							PS_ADMIN_DIR.'/../classes/',
							PS_ADMIN_DIR.'/../controllers/',
							PS_ADMIN_DIR.'/../override/classes/',
							PS_ADMIN_DIR.'/../override/controllers/',
							PS_ADMIN_DIR.'/',
							PS_ADMIN_DIR.'/tabs/');
		if (!file_exists(_PS_MODULE_DIR_))
				die($this->displayWarning(Tools::displayError('Fatal error: Module directory is not here anymore ').'('._PS_MODULE_DIR_.')'));
			if (!is_writable(_PS_MODULE_DIR_))
				$this->displayWarning(Tools::displayError('The module directory must be writable'));
			if (!$modules = scandir(_PS_MODULE_DIR_))
				$this->displayWarning(Tools::displayError('There are no modules in your copy of PrestaShop. Use the Modules tab to activate them or go to our Website to download additional Modules.'));
			else
			{
				$count = 0;

				foreach ($modules AS $module)
					if (is_dir(_PS_MODULE_DIR_.$module) && $module != '.' && $module != '..' && $module != '.svn' )
						$dirToParse[] = _PS_MODULE_DIR_.$module.'/';
			}
		foreach ($dirToParse AS $dir)
			foreach (scandir($dir) AS $file)
				if (preg_match('/.php$/', $file) AND file_exists($fn = $dir.$file) AND $file != 'index.php')
				{
					if (!filesize($fn))
						continue;
					preg_match_all('/Tools::displayError\(\''._PS_TRANS_PATTERN_.'\'(, (true|false))?\)/U', fread(fopen($fn, 'r'), filesize($fn)), $matches);
					foreach($matches[1] AS $key)
						$stringToTranslate[$key] = (key_exists(md5($key), $_ERRORS)) ? html_entity_decode($_ERRORS[md5($key)], ENT_COMPAT, 'UTF-8') : '';
				}
		$irow = 0;
		$str_output .= $this->displayAutoTranslate();
		$str_output .= '<h2>'.$this->l('Language').' : '.Tools::strtoupper($lang).' - '.$this->l('Errors translations').'</h2>'
		.$this->l('Errors to translate').' : <b>'.sizeof($stringToTranslate).'</b><br /><br />';
		$str_output .= $this->displayLimitPostWarning(sizeof($stringToTranslate));
		if (!$this->suhosin_limit_exceed)
		{
			$str_output .= '
			<form method="post" action="'.$currentIndex.'&submitTranslationsErrors=1&lang='.$lang.'&token='.$this->token.'" class="form">
			<input type="submit" name="submitTranslationsErrors" value="'.$this->l('Update translations').'" class="button" /><br /><br />
			<table cellpadding="0" cellspacing="0" class="table">';
			ksort($stringToTranslate);
			foreach ($stringToTranslate AS $key => $value)
				$str_output .= '<tr '.(empty($value) ? 'style="background-color:#FBB"' : (++$irow % 2 ? 'class="alt_row"' : '')).'><td>'.stripslashes($key).'</td><td style="width: 430px">= <input type="text" name="'.md5($key).'" value="'.preg_replace('/"/', '&quot;', stripslashes($value)).'" style="width: 380px"></td></tr>';
			$str_output .= '</table><br /><input type="submit" name="submitTranslationsErrors" value="'.$this->l('Update translations').'" class="button" /></form>';
		}
		echo $str_output;
	}

	public function displayFormFields($lang)
	{
		global $currentIndex;
		$_FIELDS = $this->fileExists(_PS_TRANSLATIONS_DIR_.$lang, 'fields.php', '_FIELDS');

		$str_output = '';
		$classArray = array();
		$count = 0;
		foreach (scandir(_PS_CLASS_DIR_) AS $classFile)
		{
			if (!preg_match('/\.php$/', $classFile) OR $classFile == 'index.php')
				continue;
			include_once(_PS_CLASS_DIR_.$classFile);
			$className = substr($classFile, 0, -4);
			if (!class_exists($className))
				continue;
			if (!is_subclass_of($className, 'ObjectModel'))
				continue;
			$classArray[$className] = call_user_func(array($className, 'getValidationRules'), $className);
			if (isset($classArray[$className]['validate']))
				$count += sizeof($classArray[$className]['validate']);
			if (isset($classArray[$className]['validateLang']))
				$count += sizeof($classArray[$className]['validateLang']);
		}

		$str_output .= $this->displayAutoTranslate();
		$str_output .= '
		<h2>'.$this->l('Language').' : '.Tools::strtoupper($lang).' - '.$this->l('Fields translations').'</h2>';
		$str_output .= $this->displayLimitPostWarning($count);
		if (!$this->suhosin_limit_exceed)
		{
			$str_output .= $this->l('Fields to translate').' : <b>'.$count.'</b>. '.$this->l('Click on the titles to open fieldsets').'.<br /><br />
			<form method="post" action="'.$currentIndex.'&submitTranslationsFields=1&token='.$this->token.'" class="form">';
			$str_output .= $this->displayToggleButton();
			$str_output .= '<input type="hidden" name="lang" value="'.$lang.'" /><input type="submit" name="submitTranslationsFields" value="'.$this->l('Update translations').'" class="button" /><br /><br />';
			foreach ($classArray AS $className => $rules)
			{
				$translated = 0;
				$toTranslate = 0;
				if (isset($rules['validate']))
					foreach ($rules['validate'] AS $key => $value)
						(array_key_exists($className.'_'.md5($key), $_FIELDS)) ? ++$translated : ++$toTranslate;
				if (isset($rules['validateLang']))
					foreach ($rules['validateLang'] AS $key => $value)
						(array_key_exists($className.'_'.md5($key), $_FIELDS)) ? ++$translated : ++$toTranslate;
				$str_output .= '
				<fieldset><legend style="cursor : pointer" onclick="$(\'#'.$className.'-tpl\').slideToggle();">'.$className.' - <font color="blue">'.($toTranslate + $translated).'</font> '.$this->l('fields').' (<font color="red">'.$toTranslate.'</font>)</legend>
				<div name="fields_div" id="'.$className.'-tpl" style="display: '.($toTranslate ? 'block' : 'none').';">
					<table cellpadding="2">';
				if (isset($rules['validate']))
					foreach ($rules['validate'] AS $key => $value)
						$str_output .= '<tr><td style="text-align:right;width:200px;">'.stripslashes($key).'</td><td style="width: 680px">= <input type="text" name="'.$className.'_'.md5(addslashes($key)).'" value="'.(array_key_exists($className.'_'.md5(addslashes($key)), $_FIELDS) ? html_entity_decode($_FIELDS[$className.'_'.md5(addslashes($key))], ENT_NOQUOTES, 'UTF-8') : '').'" style="width: 620px"></td></tr>';
				if (isset($rules['validateLang']))
					foreach ($rules['validateLang'] AS $key => $value)
						$str_output .= '<tr><td style="text-align:right;width:200px;">'.stripslashes($key).'</td><td style="width: 680px">= <input type="text" name="'.$className.'_'.md5(addslashes($key)).'" value="'.(array_key_exists($className.'_'.md5(addslashes($key)), $_FIELDS) ? html_entity_decode($_FIELDS[$className.'_'.md5(addslashes($key))], ENT_COMPAT, 'UTF-8') : '').'" style="width: 620px"></td></tr>';
				$str_output .= '
					</table>
				</div>
				</fieldset><br />';
			}
			$str_output .= '<br /><input type="submit" name="submitTranslationsFields" value="'.$this->l('Update translations').'" class="button" /></form>';
		}
		echo $str_output;
	}
	
	/**
	 * Get each informations for each mails founded in the folder $dir.
	 * 
	 * @since 1.4.0.14
	 * @param string $dir
	 * @param string $lang
	 * @param $string $group_name
	 */
	public function getMailFiles($dir, $lang, $group_name = 'mail')
	{
		$arr_return = array();
		
		// Very usefull to name input and textarea fields
		$arr_return['group_name'] = $group_name;
		$arr_return['empty_values'] = 0;
		$arr_return['total_filled'] = 0;
		$arr_return['directory'] = $dir;
//		$arr_return['subject'] = $this->getSubjectMailContent($dir.$lang);
		if(file_exists($dir.'en'))
		{
			// Get all english files to compare with the language to translate
			foreach (scandir($dir.'en') AS $email_file)
			{
				if (strripos($email_file, '.html') > 0 || strripos($email_file, '.txt') > 0)
				{
					$email_name = substr($email_file, 0, strripos($email_file, '.'));
					$type = substr($email_file, strripos($email_file, '.')+1);
					if (!isset($arr_return['files'][$email_name])) {
						$arr_return['files'][$email_name] = array();
					}
					$arr_return['files'][$email_name][$type]['en'] = $this->getMailContent($dir, $email_file, 'en');
					
					// check if the file exists in the language to translate
					if (file_exists($dir.$lang.'/'.$email_file)) {
						$arr_return['files'][$email_name][$type][$lang] = $this->getMailContent($dir, $email_file, $lang);
					} else {
						$arr_return['files'][$email_name][$type][$lang] = '';
					}
					if ($arr_return['files'][$email_name][$type][$lang] == '') {
						$arr_return['empty_values']++;
					} else {
						$arr_return['total_filled']++;
					}
				}
		}
		}
		return $arr_return;
	}
	
	/**
	 * Get content of the mail file.
	 * 
	 * @since 1.4.0.14
	 * @param string $dir
	 * @param string $file
	 * @param string $lang iso code of a language
	 */
	protected function getMailContent($dir, $file, $lang)
	{
		$arr_return = array();
		$content = file_get_contents($dir.$lang.'/'.$file);
		
		if (Tools::strlen($content) === 0) {
			$content = '';
		}
		return $content;
	}
	
	/**
	 * Display mails in html format.
	 * This was create for factorize the html displaying
	 * 
	 * @since 1.4.0.14
	 * @param array $mails
	 * @param array $all_subject_mail
	 * @param Language $obj_lang
	 * @param string $id_html use for set html id attribute for the block
	 * @param string $title Set the title for the block
	 * @param string|boolean $name_for_module is not false define add a name for disntiguish mails module
	 */
	protected function displayMailContent($mails, $all_subject_mail, $obj_lang, $id_html, $title, $name_for_module = false)
	{
		$str_return = '';
		$group_name = 'mail';
		if (key_exists('group_name', $mails))
		{
			$group_name = $mails['group_name'];
		}
		$str_return .= '
		<div class="mails_field" >
			<h3 style="cursor : pointer" onclick="$(\'#'.$id_html.'\').slideToggle();">'.$title.' - <font color="red">'.$mails['empty_values'].'</font> '
			.sprintf($this->l('missing translation(s) on %s template(s) for %s'), '<font color="blue">'.((int)$mails['empty_values']+(int)$mails['total_filled']).'</font>', $obj_lang->name)
			.':</h3>
			<div name="mails_div" id="'.$id_html.'">';
		if (!empty($mails['files']))
		{
			foreach ($mails['files'] AS $mail_name => $mail_files)
			{
				if ((key_exists('html', $mail_files) OR key_exists('txt', $mail_files)))
				{
					if (key_exists($mail_name, $all_subject_mail))
					{
						$subject_mail =  $all_subject_mail[$mail_name];
						$str_return .= '
						<div class="label-subject" style="text-align:center;">
							<label style="text-align:right">'.sprintf($this->l('Subject for %s:'), '<em>'.$mail_name.'</em>').'</label>
							<div class="mail-form" style="text-align:left">
								<b>'.$subject_mail.'</b><br />
								<input type="text" name="subject['.$group_name.']['.$subject_mail.']" value="'.(isset($mails['subject'][$subject_mail]) ? $mails['subject'][$subject_mail] : '').'" />
							</div>
						</div>';
					} else {
						$str_return .= '
						<div class="label-subject">
							<b>'.sprintf($this->l('No Subject was found for %s.'), '<em>'.$mail_name.'</em>').'</b>'
						.'</div>';
					}
					if (key_exists('html', $mail_files))
					{
						$base_uri = str_replace(_PS_ROOT_DIR_, __PS_BASE_URI__, $mails['directory']);
						$base_uri = str_replace('//', '/', $base_uri);
						$url_mail = $base_uri.$obj_lang->iso_code.'/'.$mail_name.'.html';
						$str_return .= $this->displayMailBlockHtml($mail_files['html'], $obj_lang->iso_code, $url_mail, $mail_name, $group_name, $name_for_module);
					}
					if (key_exists('txt', $mail_files)) {
						$str_return .= $this->displayMailBlockTxt($mail_files['txt'], $obj_lang->iso_code, $mail_name, $group_name, $name_for_module);
					}
				}
			}
		}
		else
		{
			$str_return .= '
				<p class="error">'.$this->l('There is a problem to get the Mail files.').'<br />'
				.sprintf($this->l('Please ensure that english files exists in %s folder'), '<em>'.$mails['directory'].'en</em>')
				.'</p>';
		}
		$str_return .= '
			</div><!-- #'.$id_html.' -->
			<div class="clear"></div>
		</div>';
		return $str_return;
	}
	/**
	 * Just build the html structure for display txt mails
	 * 
	 * @since 1.4.0.14
	 * @param array $content with english and language needed contents
	 * @param string $lang iso code of the needed language
	 * @param string $mail_name name of the file to translate (same for txt and html files)
	 * @param string $group_name group name allow to distinguish each block of mail.
	 * @param string|boolean $name_for_module is not false define add a name for disntiguish mails module
	 */
	protected function displayMailBlockTxt($content, $lang, $mail_name, $group_name, $name_for_module = false)
	{
		return '
				<div class="block-mail" >
					<label>'.$mail_name.'.txt</label>
					<div class="mail-form">
						<div><textarea class="rte mailrte noEditor" cols="80" rows="30" name="'.$group_name.'[txt]['.($name_for_module ? $name_for_module.'|' : '' ).$mail_name.']" style="width:560px;margin=0;">'.Tools::htmlentitiesUTF8(stripslashes(strip_tags($content[$lang]))).'</textarea></div>
					</div><!-- .mail-form -->
				</div><!-- .block-mail -->';
	}
	/**
	 * Just build the html structure for display html mails. 
	 * 
	 * @since 1.4.0.14
	 * @param array $content with english and language needed contents
	 * @param string $lang iso code of the needed language
	 * @param string $url for the html page and displaying an outline
	 * @param string $mail_name name of the file to translate (same for txt and html files)
	 * @param string $group_name group name allow to distinguish each block of mail. 
	 * @param string|boolean $name_for_module is not false define add a name for disntiguish mails module  
	 */
	protected function displayMailBlockHtml($content, $lang, $url, $mail_name, $group_name, $name_for_module = false)
	{
		$title = array();
		
		// Because TinyMCE don't work correctly with <DOCTYPE>, <html> and <body> tags
		if (stripos($content[$lang], '<body'))
		{
			$array_lang = $lang != 'en' ? array('en', $lang) : array($lang);
			
			foreach ($array_lang as $language)
			{
				$title[$language] = substr($content[$language], 0, stripos($content[$language], '<body'));
				preg_match('#<title>([^<]+)</title>#Ui', $title[$language], $matches);
				$title[$language] = empty($matches[1])?'':$matches[1];
				
				// The 2 lines below allow to exlude <body> tag from the content.
				// This allow to exclude body tag even if attributs are setted.
				$content[$language] = substr($content[$language], stripos($content[$language], '<body')+5);
				$content[$language] = substr($content[$language], stripos($content[$language], '>')+1);
				$content[$language] = substr($content[$language], 0, stripos($content[$language], '</body>'));
			}
		}
		
		$str_return = '';
		$str_return .= '
		<div class="block-mail" >
			<label>'.$mail_name.'.html</label>
			<div class="mail-form">
				<div>';
		$str_return .= '
				<div class="label-subject">
					<b>'.$this->l('"title" tag:').'</b>&nbsp;'.(isset($title['en']) ? $title['en'] : '').'<br />
					<input type="text" name="title_'.$group_name.'_'.$mail_name.'" value="'.(isset($title[$lang]) ? $title[$lang] : '').'" />
				</div><!-- .label-subject -->';
		$str_return .= '
				<iframe style="background:white;border:1px solid #DFD5C3;" border="0" src ="'.$url.'?'.(rand(0,1000000000000)).'" width="565" height="497"></iframe>
					<a style="display:block;margin-top:5px;width:130px;" href="#" onclick="$(this).parent().hide(); displayTiny($(this).parent().next()); return false;" class="button">Edit this mail template</a>
				</div>
				<textarea style="display:none;" class="rte mailrte" cols="80" rows="30" name="'.$group_name.'[html]['.($name_for_module ? $name_for_module.'|' : '' ).$mail_name.']">'.(isset($content[$lang]) ? Tools::htmlentitiesUTF8(stripslashes($content[$lang])) : '').'</textarea>
			</div><!-- .mail-form -->
		</div><!-- .block-mail -->';
		return $str_return;
	}
	
	/**
	 * Check in each module if contains mails folder.
	 * 
	 * @return array of module which has mails
	 */
	public function getModulesHasMails()
	{
		$arr_modules = array();
		foreach (scandir(_PS_MODULE_DIR_) AS $module_dir)
		{
			if ($module_dir[0] != '.' AND file_exists(_PS_MODULE_DIR_.$module_dir.'/mails'))
			{
				$arr_modules[$module_dir] = _PS_MODULE_DIR_.$module_dir;
			}
			
		}
		return $arr_modules;
	}
	protected function getTinyMCEForMails($iso_lang)
	{
		// TinyMCE
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso_lang.'.js') ? $iso_lang : 'en');
		$ad = dirname($_SERVER["PHP_SELF"]);
		return '
			<script type="text/javascript">	
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
			<script type="text/javascript">
			function displayTiny(obj) {
					tinyMCE.get(obj.attr(\'name\')).show();
				}
			</script>';
	}
	public function displayFormMails($lang, $noDisplay = false)
	{
		global $cookie, $currentIndex;
		
		$core_mails = array();
		$module_mails = array();
		$theme_mails = array();
		$str_output = '';
		
		// get all mail subjects, this method parse each files in Prestashop !!
		$subject_mail = array();
		$modules_has_mails = $this->getModulesHasMails();
		$arr_files_to_parse = array(
			_PS_ROOT_DIR_.'/controllers',
			_PS_ROOT_DIR_.'/classes',
			PS_ADMIN_DIR.'/tabs',
		);
		$arr_files_to_parse = array_merge($arr_files_to_parse, $modules_has_mails);
		foreach ($arr_files_to_parse as $path) {
			$subject_mail = self::getSubjectMail($path, $subject_mail);
		}
		
		$core_mails = $this->getMailFiles(_PS_MAIL_DIR_, $lang, 'core_mail');
		$core_mails['subject'] = $this->getSubjectMailContent(_PS_MAIL_DIR_.$lang);
		foreach ($modules_has_mails AS $module_name=>$module_path)
		{
			$module_mails[$module_name] = $this->getMailFiles($module_path.'/mails/', $lang, 'module_mail');
			$module_mails[$module_name]['subject'] = $core_mails['subject'];
		}
		
		// Before 1.4.0.14 each theme folder was parsed,
		// This page was really to low to load.
		// Now just use the current theme.
		if(_THEME_NAME_ !== AdminTranslations::DEFAULT_THEME_NAME)
		{
			if(file_exists(_PS_THEME_DIR_.'mails'))
			{
				$theme_mails['theme_mail'] = $this->getMailFiles(_PS_THEME_DIR_.'mails/', $lang, 'theme_mail');
				$theme_mails['theme_mail']['subject'] = $this->getSubjectMailContent(_PS_THEME_DIR_.'mails/'.$lang);
			}
			if (file_exists(_PS_THEME_DIR_.'/modules'))
			{
				foreach (scandir(_PS_THEME_DIR_.'/modules') AS $module_dir)
				{
					if ($module_dir[0] != '.' AND file_exists(_PS_THEME_DIR_.'modules/'.$module_dir.'/mails'))
					{
						$theme_mails[$module_dir] = $this->getMailFiles(_PS_THEME_DIR_.'modules/'.$module_dir.'/mails/', $lang, 'theme_module_mail');
						$theme_mails[$module_dir]['subject'] = $theme_mails['theme_mail']['subject'];
					}
				}
			}
		}
		
		if ($noDisplay)
		{
			$empty = 0;
			$total = 0;
			$total += (int)$core_mails['total_filled'];
			$empty += (int)$core_mails['empty_values'];
			foreach ($module_mails as $mod_infos)
			{
				$total += (int)$mod_infos['total_filled'];
				$empty += (int)$mod_infos['empty_values'];
			}
			foreach ($theme_mails as $themes_infos)
			{
				$total += (int)$themes_infos['total_filled'];
				$empty += (int)$themes_infos['empty_values'];
			}
			return array('total' => $total, 'empty' => $empty);
		}

		$obj_lang = new Language(Language::getIdByIso($lang));
		
		// TinyMCE
		$str_output .= $this->getTinyMCEForMails($obj_lang->iso_code);
		
		$str_output .= '<!--'.$this->l('Language').'-->';
		$str_output .= '
		<h2>'.$this->l('Language').' : '.Tools::strtoupper($lang).' - '.$this->l('E-mail template translations').'</h2>'
		.$this->l('Click on the titles to open fieldsets').'.<br /><br />';

		// display form
		$str_output .= '
		<form method="post" action="'.$currentIndex.'&token='.$this->token.'&type=mails&lang='.$obj_lang->iso_code.'" class="form">';
		$str_output .= $this->displayToggleButton();
		$str_output .= $this->displaySubmitButtons(Tools::getValue('type'));
		$str_output .= '<br/><br/>';

		// core emails
		$str_output .= $this->l('Core e-mails:');
		$str_output .= $this->displayMailContent($core_mails, $subject_mail, $obj_lang, 'core', $this->l('Core e-mails'));
		// module mails
		$str_output .= $this->l('Modules e-mails:');
		foreach ($module_mails as $module_name => $mails)
		{
			$str_output .= $this->displayMailContent($mails, $subject_mail, $obj_lang, Tools::strtolower($module_name), sprintf($this->l('E-mails for %s module'), '<em>'.$module_name.'</em>'), $module_name);
		}
		// mail theme and module theme
		if (!empty($theme_mails))
		{
			$str_output .= $this->l('Themes e-mails:');
			$bool_title = false;
			foreach ($theme_mails as $theme_or_module_name => $mails)
			{
				$title = $theme_or_module_name != 'theme_mail' ? ucfirst(_THEME_NAME_).' '.sprintf($this->l('E-mails for %s module'), '<em>'.$theme_or_module_name.'</em>') : ucfirst(_THEME_NAME_).' '.$this->l('e-mails');
				if ($theme_or_module_name != 'theme_mail' && !$bool_title) {
					$bool_title = true;
					$str_output .= $this->l('E-mails modules in theme:');
				}
				$str_output .= $this->displayMailContent($mails, $subject_mail, $obj_lang, 'theme_'.Tools::strtolower($theme_or_module_name), $title, ($theme_or_module_name != 'theme_mail' ? $theme_or_module_name : false));
			}
		}
		$str_output .= '
				<input type="hidden" name="lang" value="'.$lang.'" />
				<input type="hidden" name="type" value="'.Tools::getValue('type').'" />';
		$str_output .= $this->displaySubmitButtons(Tools::getValue('type'));
		$str_output .= '<br /><br />';
		$str_output .= '</form>';
		echo $str_output;
	}

	protected function getSubjectMail($directory, $subject_mail)
	{
		foreach (scandir($directory) AS $filename)
		{
			if (strripos($filename, '.php') > 0 AND $filename != 'AdminTranslations.php')
			{
				$content = file_get_contents($directory.'/'.$filename);
				$content = str_replace("\n", " ", $content);
				if (preg_match_all('/Mail::Send([^;]*);/si', $content, $tab))
				{
					for ($i = 0 ; isset($tab[1][$i]) ; $i++)
					{
						$tab2 = explode(',', $tab[1][$i]);
						if (is_array($tab2))
						{
							if ($tab2 && isset($tab2[1]))
							{
								$tab2[1] = trim(str_replace('\'', '', $tab2[1]));
								if (preg_match('/Mail::l\(\''._PS_TRANS_PATTERN_.'\'\)/s', $tab2[2], $tab3))
									$tab2[2] = $tab3[1];
								$subject_mail[$tab2[1]] = $tab2[2];
							}
						}
					}
				}
			}
			if ($filename != '.svn' AND $filename != '.' AND $filename != '..' AND is_dir(($directory.'/'.$filename)))
				 $subject_mail = self::getSubjectMail($directory.'/'.$filename, $subject_mail);
		}
		return $subject_mail;
	}

	protected function getSubjectMailContent($directory)
	{
		$subject_mail_content =  array();
		
		if (Tools::file_exists_cache($directory.'/lang.php'))
		{
			// we need to include this even if already included
			include($directory.'/lang.php');
			foreach($_LANGMAIL as $key => $subject)
			{
				$subject = str_replace("\n", " ", $subject);
				$subject = str_replace("\\'", "\'", $subject);

				$subject_mail_content[$key] = htmlentities($subject,ENT_QUOTES,'UTF-8');
			}
		}
		else
			$this->_errors[] = $this->l('Subject mail translation file not found in').' '.$directory;
		return $subject_mail_content;
	}

	protected function writeSubjectTranslationFile($sub, $path, $mark = false, $fullmark = false)
	{
		global $currentIndex;

		if ($fd = @fopen($path, 'w'))
		{
			//$tab = ($fullmark ? Tools::strtoupper($fullmark) : 'LANG').($mark ? Tools::strtoupper($mark) : '');
			$tab = 'LANGMAIL';
			fwrite($fd, "<?php\n\nglobal \$_".$tab.";\n\$_".$tab." = array();\n");

			foreach($sub AS $key => $value)
			{
				// Magic Quotes shall... not.. PASS!
				if (_PS_MAGIC_QUOTES_GPC_)
					$value = stripslashes($value);
				fwrite($fd, '$_'.$tab.'[\''.pSQL($key).'\'] = \''.pSQL($value).'\';'."\n");
			}

			fwrite($fd, "\n?>");
			fclose($fd);

		}
		else
			die($this->l('Cannot write language file for mails subjects, path is:').$path);
	}
	
	/**
	 * This get files to translate in module directory.
	 * Recursive method allow to get each files for a module no matter his depth.
	 * 
	 * @param string $path directory path to scan
	 * @param array $array_files by reference - array which saved files to parse.
	 * @param string $module_name module name
	 * @param string $lang_file full path of translation file
	 * @param boolean $is_default 
	 */
	protected function recursiveGetModuleFiles($path, &$array_files, $module_name, $lang_file, $is_default = false)
	{
		$files_module = array();
		$files_module = scandir($path);
		$files_for_module = $this->clearModuleFiles($files_module, 'file');
		if (!empty($files_for_module))
			$array_files[] = array(
				'file_name'		=> $lang_file,
				'dir'			=> $path,
				'files'			=> $files_for_module,
				'module'		=> $module_name,
				'is_default'	=> $is_default,
				'theme'			=> ($is_default ? self::DEFAULT_THEME_NAME : _THEME_NAME_ ),
			);
		$dir_module = $this->clearModuleFiles($files_module, 'directory', $path);
		if(!empty($dir_module))
		{
			foreach ($dir_module AS $folder)
			{
				$this->recursiveGetModuleFiles($path.$folder.'/', $array_files, $module_name, $lang_file, $is_default);
			}
		}
	}
	
	/**
	 * This method get translation in each translations file.
	 * The file depend on $lang param.
	 * 
	 * @param array $modules list of modules
	 * @param string $root_dir path where it get each modules
	 * @param string $lang iso code of choosen language to translate
	 * @param boolean $is_default set it if modules are located in root/prestashop/modules folder
	 * 				  This allow to distinguish overrided prestashop theme and original module 
	 */
	protected function getAllModuleFiles(array $modules, $root_dir, $lang, $is_default = false)
	{
		$array_files = array();
		foreach ($modules AS $module)
		{
			if ($module{0} != '.' AND is_dir($root_dir.$module))
			{
				@include_once($root_dir.$module.'/'.$lang.'.php');
				self::getModuleTranslations($is_default);
				$this->recursiveGetModuleFiles($root_dir.$module.'/', $array_files, $module, $root_dir.$module.'/'.$lang.'.php', $is_default);
			}
		}
		return $array_files;
	}
	public function displayFormModules($lang)
	{
		global $currentIndex, $_MODULES;
		
		$array_lang_src = Language::getLanguages(false);
		$str_output = '';
		
		foreach ($array_lang_src as $language)
		{
			$this->all_iso_lang[] = $language['iso_code'];
		}

		if (!file_exists(_PS_MODULE_DIR_))
			die($this->displayWarning(Tools::displayError('Fatal error: Module directory is not here anymore ').'('._PS_MODULE_DIR_.')'));
		if (!is_writable(_PS_MODULE_DIR_))
			$this->displayWarning(Tools::displayError('The module directory must be writable'));
		if (!$modules = scandir(_PS_MODULE_DIR_))
			$this->displayWarning(Tools::displayError('There are no modules in your copy of PrestaShop. Use the Modules tab to activate them or go to our Website to download additional Modules.'));
		else
		{
			$arr_find_and_fill = array();
			
			$arr_files = $this->getAllModuleFiles($modules, _PS_MODULE_DIR_, $lang, true);
			$arr_find_and_fill = array_merge($arr_find_and_fill, $arr_files);
			
			if(file_exists(_PS_THEME_DIR_.'/modules/'))
			{
				$modules = scandir(_PS_THEME_DIR_.'/modules/');
				$arr_files = $this->getAllModuleFiles($modules, _PS_THEME_DIR_.'modules/', $lang);
				$arr_find_and_fill = array_merge($arr_find_and_fill, $arr_files);
			}
			foreach ($arr_find_and_fill as $value)
				$this->findAndFillTranslations($value['files'], $value['theme'], $value['module'], $value['dir'], $lang);
			
			$str_output .= '
			<h2>'.$this->l('Language').' : '.Tools::strtoupper($lang).' - '.$this->l('Modules translations').'</h2>
			'.$this->l('Total expressions').' : <b>'.$this->total_expression.'</b>. '.$this->l('Click the fieldset title to expand or close the fieldset.').'.<br /><br />';
			$str_output .= $this->displayLimitPostWarning($this->total_expression);
			if (!$this->suhosin_limit_exceed)
			{
				$str_output .= '
				<form method="post" action="'.$currentIndex.'&submitTranslationsModules=1&token='.$this->token.'" class="form">';
				$str_output .= $this->displayToggleButton();
				$str_output .= $this->displayAutoTranslate();
				$str_output .= '<input type="hidden" name="lang" value="'.$lang.'" /><input type="submit" name="submitTranslationsModules" value="'.$this->l('Update translations').'" class="button" /><br /><br />';
				
				if (count($this->modules_translations) > 1) 
				{
					$str_output .= '<h3 style="padding:0;margin:0;">'.$this->l('List of Themes - Click to access theme translation:').'</h3>';
					$str_output .= '<ul style="list-style-type:none;padding:0;margin:0 0 10px 0;">';
					foreach (array_keys($this->modules_translations) as $theme)
						$str_output .= '<li><a href="#'.$theme.'" class="link">- '.($theme === 'default' ? $this->l('default') : $theme ).'</a></li>';
					$str_output .= '</ul>';
				}

				foreach ($this->modules_translations AS $theme_name => $theme)
				{
					$str_output .= '<h2>&gt;'.$this->l('Theme:').' <a name="'.$theme_name.'">'.($theme_name === self::DEFAULT_THEME_NAME ? $this->l('default') : $theme_name ).'</h2>';
					foreach ($theme AS $module_name => $module)
					{
						$str_output .= ''.$this->l('Module:').' <a name="'.$module_name.'" style="font-style:italic">'.$module_name.'</a>';
						foreach ($module AS $template_name => $newLang)
							if (sizeof($newLang))
							{
								$countValues = array_count_values($newLang);
								$empty = isset($countValues['']) ? $countValues[''] : 0;
								$str_output .= '
								<fieldset style="margin-top:5px"><legend style="cursor : pointer" onclick="$(\'#'.$theme_name.'_'.$module_name.'_'.$template_name.'\').slideToggle();">'.($theme_name === 'default' ? $this->l('default') : $theme_name ).' - '.$template_name.' - <font color="blue">'.sizeof($newLang).'</font> '.$this->l('expressions').' (<font color="red">'.$empty.'</font>)</legend>
									<div name="modules_div" id="'.$theme_name.'_'.$module_name.'_'.$template_name.'" style="display: '.($empty ? 'block' : 'none').';">
										<table cellpadding="2">';
								foreach ($newLang AS $key => $value)
								{
									$str_output .= '<tr><td style="width: 40%">'.stripslashes($key).'</td><td>= ';
									if (strlen($key) < TEXTAREA_SIZED)
										$str_output .= '<input type="text" style="width: 450px" name="'.md5(strtolower($module_name).'_'.strtolower($theme_name).'_'.strtolower($template_name).'_'.md5($key)).'" value="'.stripslashes(preg_replace('/"/', '\&quot;', stripslashes($value))).'" /></td></tr>';
									else
										$str_output .= '<textarea rows="'.(int)(strlen($key) / TEXTAREA_SIZED).'" style="width: 450px" name="'.md5(strtolower($module_name).'_'.strtolower($theme_name).'_'.strtolower($template_name).'_'.md5($key)).'">'.stripslashes(preg_replace('/"/', '\&quot;', stripslashes($value))).'</textarea></td></tr>';
								}
								$str_output .= '
										</table>
									</div>
								</fieldset><br />';
							}
					}
				}
				$str_output .= '<br /><input type="submit" name="submitTranslationsModules" value="'.$this->l('Update translations').'" class="button" /></form>';
			}
		}
		echo $str_output;
	}

	public function displayFormPDF()
	{
		global $currentIndex;

		$lang = Tools::strtolower(Tools::getValue('lang'));
		$_LANG = array();
		$str_output = '';
		
		if (!file_exists(_PS_TRANSLATIONS_DIR_.$lang))
			if (!mkdir(_PS_TRANSLATIONS_DIR_.$lang, 0700))
				die('Please create a "'.$iso.'" directory in '._PS_TRANSLATIONS_DIR_);
		if (!file_exists(_PS_TRANSLATIONS_DIR_.$lang.'/pdf.php'))
			if (!file_put_contents(_PS_TRANSLATIONS_DIR_.$lang.'/pdf.php', "<?php\n\nglobal \$_LANGPDF;\n\$_LANGPDF = array();\n\n?>"))
				die('Please create a "'.Tools::strtolower($lang).'.php" file in '.realpath(PS_ADMIN_DIR.'/'));
		unset($_LANGPDF);
		@include(_PS_TRANSLATIONS_DIR_.$lang.'/pdf.php');
		$files = array();
		$count = 0;
		$tab = 'PDF_invoice';
		$pdf = _PS_CLASS_DIR_.'PDF.php';
		$newLang = array();
		$fd = fopen($pdf, 'r');
		$content = fread($fd, filesize($pdf));
		fclose($fd);
		$regex = '/self::l\(\''._PS_TRANS_PATTERN_.'\'[\)|\,]/U';
		preg_match_all($regex, $content, $matches);
		foreach($matches[1] AS $key)
			$tabsArray[$tab][$key] = stripslashes(key_exists($tab.md5(addslashes($key)), $_LANGPDF) ? html_entity_decode($_LANGPDF[$tab.md5(addslashes($key))], ENT_COMPAT, 'UTF-8') : '');
		$count += isset($tabsArray[$tab]) ? sizeof($tabsArray[$tab]) : 0;
		$closed = sizeof($_LANGPDF) >= $count;
		
		$str_output .= $this->displayAutoTranslate();
		$str_output .= '<h2>'.$this->l('Language').' : '.Tools::strtoupper($lang).'</h2>'
			.$this->l('Expressions to translate').' : <b>'.$count.'</b>. '.$this->l('Click on the titles to open fieldsets').'.<br /><br />';
		$str_output .= $this->displayLimitPostWarning($count);
		if (!$this->suhosin_limit_exceed)
		{
			$str_output .= '
			<form method="post" action="'.$currentIndex.'&submitTranslationsPDF=1&token='.$this->token.'" class="form">
					<script type="text/javascript">
						var openAll = \''.html_entity_decode($this->l('Expand all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
						var closeAll = \''.html_entity_decode($this->l('Close all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
					</script>
					<input type="hidden" name="lang" value="'.$lang.'" />
					<input type="button" class="button" id="buttonall" onclick="openCloseAllDiv(\'pdf_div\', this.value == openAll); toggleElemValue(this.id, openAll, closeAll);" />
					<script type="text/javascript">
						toggleElemValue(\'buttonall\', '.($closed ? 'openAll' : 'closeAll').', '.($closed ? 'closeAll' : 'openAll').');
					</script>';
			$str_output .= '<input type="submit" name="submitTranslationsPDF" value="'.$this->l('Update translations').'" class="button" /><br /><br />';
			foreach ($tabsArray AS $k => $newLang)
				if (sizeof($newLang))
				{
					$countValues = array_count_values($newLang);
					$empty = isset($countValues['']) ? $countValues[''] : 0;
					$str_output .= '<fieldset><legend style="cursor : pointer" onclick="$(\'#'.$k.'-tpl\').slideToggle();">'.$k.' - <font color="blue">'.sizeof($newLang).'</font> '.$this->l('expressions').' (<font color="red">'.$empty.'</font>)</legend>
						<div name="pdf_div" id="'.$k.'-tpl" style="display: '.($empty ? 'block' : 'none').';">
							<table cellpadding="2">';
					foreach ($newLang AS $key => $value)
					{
						$str_output .= '<tr>
							<td>'.stripslashes($key).'</td>
							<td style="width: 580px">
								= <input type="text" name="'.$k.md5($key).'" value="'.stripslashes(preg_replace('/"/', '\&quot;', $value)).'" style="width: 515px">
							</td>
						</tr>';
					}
					$str_output .= '</table>
						</div>
					</fieldset><br />';
				}
			$str_output .= '<br /><input type="submit" name="submitTranslationsPDF" value="'.$this->l('Update translations').'" class="button" /></form>';
		}
		echo $str_output;
	}

	/**
	  * Return an array with themes and thumbnails
	  *
	  * @return array
	  */
	static public function getThemesList()
	{
		$dir = opendir(_PS_ALL_THEMES_DIR_);
		while ($folder = readdir($dir))
			if ($folder != '.' AND $folder != '..' AND is_dir($folder) AND file_exists(_PS_ALL_THEMES_DIR_.'/'.$folder.'/preview.jpg'))
				$themes[$folder]['name'] = $folder;
		closedir($dir);
		return isset($themes) ? $themes : array();
	}
}
