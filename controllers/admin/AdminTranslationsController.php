<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @version  Release: $Revision: 7310 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define ('TEXTAREA_SIZED', 70);

class AdminTranslationsControllerCore extends AdminController
{
	protected $link_lang_pack = 'http://www.prestashop.com/download/lang_packs/get_each_language_pack.php';
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
	protected $post_limit_exceed = false;

	public function __construct()
	{
		$this->multishop_context = Shop::CONTEXT_ALL;

		parent::__construct();

	 	$this->table = 'translations';
		include_once(_PS_ADMIN_DIR_.'/../tools/tar/Archive_Tar.php');
		include_once(_PS_ADMIN_DIR_.'/../tools/pear/PEAR.php');

		self::$tpl_regexp = '/\{l s=\''._PS_TRANS_PATTERN_.'\'( sprintf=.*)?( mod=\'.+\')?( js=1)?\}/U';
		// added ? after spaces because some peoples forget them. see PSCFI-2501
		self::$php_regexp = '/->l\(\''._PS_TRANS_PATTERN_.'\'(, ?\'(.+)\')?(, ?(.+))?\)/U';
	}

	public function initContent()
	{
		if ($type = Tools::getValue('type'))
		{
			$method_name = 'initForm'.$type;
			if (method_exists($this, $method_name))
				$this->content .= $this->{$method_name}(Tools::strtolower(Tools::getValue('lang')));
			else
			{
				$this->errors[] = sprintf(Tools::displayError('"%s" does not exist. Maybe you typed the URL manually.'), $type);
				$this->content .= $this->initMain();
			}
		}
		else
			$this->content .= $this->initMain();

		$this->context->smarty->assign(array('content' => $this->content));
	}

	public function initToolbar()
	{
		$this->toolbar_btn['save-and-stay'] = array(
			'short' => 'SaveAndStay',
			'href' => '#',
			'desc' => $this->l('Save and stay'),
		);
		$this->toolbar_btn['save'] = array(
			'href' => '#',
			'desc' => $this->l('Update translations')
		);
		$this->toolbar_btn['cancel'] = array(
			'href' => self::$currentIndex.'&token='.$this->token,
			'desc' => $this->l('Cancel')
		);
	}

	public function initMain()
	{
		// Block modify
		$translations = array(
			'front' => $this->l('Front Office translations'),
			'back' => $this->l('Back Office translations'),
			'errors' => $this->l('Error message translations'),
			'fields' => $this->l('Field name translations'),
			'modules' => $this->l('Installed module translations'),
			'pdf' => $this->l('PDF translations'),
			'mails' => $this->l('E-mail template translations'),
		);

		// Block add/update
		$packs_to_install = array();
		$packs_to_update = array();
		if ($lang_packs = Tools::file_get_contents($this->link_lang_pack.'?version='._PS_VERSION_, false, @stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 5)))))
			// Notice : for php < 5.2 compatibility, Tools::jsonDecode. The second parameter to true will set us
			if ($lang_packs != '' && $lang_packs = Tools::jsonDecode($lang_packs, true))
				foreach ($lang_packs as $key => $lang_pack)
				{
					if (!Language::isInstalled($lang_pack['iso_code']))
						$packs_to_install[$key] = $lang_pack;
					else
						$packs_to_update[$key] = $lang_pack;
				}

		$this->tpl_view_vars = array(
			'theme_lang_dir' =>_THEME_LANG_DIR_,
			'token' => $this->token,
			'languages' => Language::getLanguages(false),
			'translations' => $translations,
			'packs_to_install' => $packs_to_install,
			'packs_to_update' => $packs_to_update,
			'url_submit' => self::$currentIndex.'&token='.$this->token,
			'themes' => $themes = AdminTranslationsController::getThemesList(),
			'url_create_language' => 'index.php?tab=AdminLanguages&addlang&token='.Tools::getAdminToken('AdminLanguages'.(int)(Tab::getIdFromClassName('AdminLanguages')).(int)$this->context->employee->id),
		);

		$this->toolbar_scroll = false;
		$this->base_tpl_view = 'main.tpl';
		return parent::renderView();
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

		if (!isset($_MODULE) && !isset($_MODULES))
			$_MODULES = array();
		else if (isset($_MODULE))
		{
			if (is_array($_MODULE) && $is_default === true)
			{
				$_NEW_MODULE = array();
				foreach ($_MODULE as $key => $value)
					$_NEW_MODULE[self::DEFAULT_THEME_NAME.$key] = $value;
				$_MODULE = $_NEW_MODULE;
			}
			$_MODULES = (is_array($_MODULES) && is_array($_MODULE)) ? array_merge($_MODULES, $_MODULE) : $_MODULE;
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
			if (!mkdir($path, 0777, true))
			{
				$bool &= false;
				$this->errors[] = $this->l('Cannot create the folder').' "'.$path.'". '.$this->l('Check directory writing permisions.');
			}
		}
		return $bool;
	}

	protected function writeTranslationFile($type, $path, $mark = false, $fullmark = false)
	{
		if ($fd = fopen($path, 'w'))
		{
			// Get value of button save and stay
			$save_and_stay = Tools::getValue('submitTranslations'.Tools::toCamelCase($type, true).'AndStay');

			// Get language
			$lang = strtolower(Tools::getValue('lang'));

			// Unset all POST which are not translations
			unset(
				$_POST['submitTranslations'.Tools::toCamelCase($type, true)],
				$_POST['submitTranslations'.Tools::toCamelCase($type, true).'AndStay'],
				$_POST['lang'],
				$_POST['token']
			);

			$to_insert = array();
			foreach ($_POST as $key => $value)
				if (!empty($value))
					$to_insert[$key] = $value;

			// translations array is ordered by key (easy merge)
			ksort($to_insert);
			$tab = ($fullmark ? Tools::strtoupper($fullmark) : 'LANG').($mark ? Tools::strtoupper($mark) : '');
			fwrite($fd, "<?php\n\nglobal \$_".$tab.";\n\$_".$tab." = array();\n");
			foreach ($to_insert as $key => $value)
				fwrite($fd, '$_'.$tab.'[\''.pSQL($key, true).'\'] = \''.pSQL($value, true).'\';'."\n");
			fwrite($fd, "\n?>");
			fclose($fd);

			if ($save_and_stay)
				Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.'&lang='.$lang.'&type='.$type);
			else
				Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
		}
		else
			die('Cannot write language file');
	}

	public function submitCopyLang()
	{
		if (!($from_lang = strval(Tools::getValue('fromLang'))) || !($to_lang = strval(Tools::getValue('toLang'))))
			$this->errors[] = $this->l('You must select 2 languages in order to copy data from one to another');
		else if (!($from_theme = strval(Tools::getValue('fromTheme'))) || !($to_theme = strval(Tools::getValue('toTheme'))))
			$this->errors[] = $this->l('You must select 2 themes in order to copy data from one to another');
		else if (!Language::copyLanguageData(Language::getIdByIso($from_lang), Language::getIdByIso($to_lang)))
			$this->errors[] = $this->l('An error occurred while copying data');
		else if ($from_lang == $to_lang && $from_theme == $to_theme)
			$this->errors[] = $this->l('Nothing to copy! (same language and theme)');
		if (count($this->errors))
			return;

		$bool = true;
		$items = Language::getFilesList($from_lang, $from_theme, $to_lang, $to_theme, false, false, true);
		foreach ($items as $source => $dest)
		{
			$bool &= $this->checkDirAndCreate($dest);
			$bool &= @copy($source, $dest);

			if (strpos($dest, 'modules') && basename($source) === $from_lang.'.php' && $bool !== false)
				$bool &= $this->changeModulesKeyTranslation($dest, $from_theme, $to_theme);
		}
		if ($bool)
			Tools::redirectAdmin(self::$currentIndex.'&conf=14&token='.$this->token);
		$this->errors[] = $this->l('A part of the data has been copied but some language files could not be found or copied');
	}

	/**
	 * Change the key translation to according it to theme name.
	 *
	 * @param string $path
	 * @param string $theme_from
	 * @param string $theme_to
	 * @return boolean
	 */
	public function changeModulesKeyTranslation($path, $theme_from, $theme_to)
	{
		$content = file_get_contents($path);
		$arr_replace = array();
		$bool_flag = true;
		if (preg_match_all('#\$_MODULE\[\'([^\']+)\'\]#Ui', $content, $matches))
		{
			foreach ($matches[1] as $key => $value)
				$arr_replace[$value] = str_replace($theme_from, $theme_to, $value);
			$content = str_replace(array_keys($arr_replace), array_values($arr_replace), $content);
			$bool_flag = (file_put_contents($path, $content) === false) ? false : true;
		}
		return $bool_flag;
	}

	public function submitExportLang()
	{
		$lang = strtolower(Tools::getValue('iso_code'));
		$theme = strval(Tools::getValue('theme'));
		if ($lang && $theme)
		{
			$items = array_flip(Language::getFilesList($lang, $theme, false, false, false, false, true));
			$gz = new Archive_Tar(_PS_TRANSLATIONS_DIR_.'/export/'.$lang.'.gzip', true);
			if ($gz->createModify($items, null, _PS_ROOT_DIR_));
				Tools::redirectLink(Tools::getCurrentUrlProtocolPrefix().Tools::getShopDomain().__PS_BASE_URI__.'translations/export/'.$lang.'.gzip');
			$this->errors[] = Tools::displayError('An error occurred while creating archive.');
		}
		$this->errors[] = Tools::displayError('Please choose a language and a theme.');
	}

	public function checkAndAddMailsFiles($iso_code, $files_list)
	{
		// 1 - Scan mails files
		$mails = scandir(_PS_MAIL_DIR_.'en/');

		$mails_new_lang = array();

		// Get all email files
		foreach ($files_list as $file)
		{
			if (preg_match('#^mails\/([a-z0-9]+)\/#Ui', $file['filename'], $matches))
			{
				$slash_pos = strrpos($file['filename'], '/');
				$mails_new_lang[] = substr($file['filename'], -(strlen($file['filename']) - $slash_pos - 1));
			}
		}

		// Get the difference
		$arr_mails_needed = array_diff($mails, $mails_new_lang);

		// Add mails files
		foreach ($arr_mails_needed as $mail_to_add)
			if ($mail_to_add !== '.' && $mail_to_add !== '..' && $mail_to_add !== '.svn')
				@copy(_PS_MAIL_DIR_.'en/'.$mail_to_add, _PS_MAIL_DIR_.$iso_code.'/'.$mail_to_add);


		// 2 - Scan modules files
		$modules = scandir(_PS_MODULE_DIR_);

		$module_mail_en = array();
		$module_mail_iso_code = array();

		foreach ($modules as $module)
		{
			if (!in_array($module, array('.', '..', '.svn', '.htaccess')) && file_exists(_PS_MODULE_DIR_.$module.'/mails/en/'))
			{
				$arr_files = scandir(_PS_MODULE_DIR_.$module.'/mails/en/');

				foreach ($arr_files as $file)
				{
					if (!in_array($file, array('.', '..', '.svn', '.htaccess')))
					{
						if (file_exists(_PS_MODULE_DIR_.$module.'/mails/en/'.$file))
							$module_mail_en[] = _PS_MODULE_DIR_.$module.'/mails/ISO_CODE/'.$file;

						if (file_exists(_PS_MODULE_DIR_.$module.'/mails/'.$iso_code.'/'.$file))
							$module_mail_iso_code[] = _PS_MODULE_DIR_.$module.'/mails/ISO_CODE/'.$file;
					}
				}
			}
		}

		// Get the difference in this modules
		$arr_modules_mails_needed = array_diff($module_mail_en, $module_mail_iso_code);

		// Add mails files for this modules
		foreach ($arr_modules_mails_needed as $file)
		{
			$file_en = str_replace('ISO_CODE', 'en', $file);
			$file_iso_code = str_replace('ISO_CODE', $iso_code, $file);
			$dir_iso_code = substr($file_iso_code, 0, -(strlen($file_iso_code) - strrpos($file_iso_code, '/') - 1));

			if (!file_exists($dir_iso_code))
				mkdir($dir_iso_code);

			if (file_exists($file_en))
				copy($file_en, $file_iso_code);
		}
	}
	public function submitImportLang()
	{
		if (!isset($_FILES['file']['tmp_name']) || !$_FILES['file']['tmp_name'])
			$this->errors[] = Tools::displayError('No file selected');
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
				Tools::redirectAdmin(self::$currentIndex.'&conf='.(isset($conf) ? $conf : '15').'&token='.$this->token);
			}
			$this->errors[] = Tools::displayError('Archive cannot be extracted.');
		}
	}

	public function submitAddLang()
	{
		$arr_import_lang = explode('|', Tools::getValue('params_import_language')); /* 0 = Language ISO code, 1 = PS version */
		if (Validate::isLangIsoCode($arr_import_lang[0]))
		{
			if ($content = Tools::file_get_contents(
				'http://www.prestashop.com/download/lang_packs/gzip/'.$arr_import_lang[1].'/'.$arr_import_lang[0].'.gzip', false,
				@stream_context_create(array('http' => array('method' => 'GET', 'timeout' => 5)))))
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
							$this->errors[] = Tools::displayError('Cannot delete archive');
						Tools::redirectAdmin(self::$currentIndex.'&conf='.(isset($conf) ? $conf : '15').'&token='.$this->token);
					}
					$this->errors[] = Tools::displayError('Archive cannot be extracted.');
					if (!unlink($file))
						$this->errors[] = Tools::displayError('Cannot delete archive');
				}
				else
					$this->errors[] = Tools::displayError('Server does not have permissions for writing.');
			}
			else
				$this->errors[] = Tools::displayError('Language not found');
		}
		else
			$this->errors[] = Tools::displayError('Invalid parameter');
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
		if (!isset($_cache_file[($is_default ? self::DEFAULT_THEME_NAME : $theme_name).'-'.$file_name]))
		{
			$str_write = '';
			$_cache_file[($is_default ? self::DEFAULT_THEME_NAME : $theme_name).'-'.$file_name] = true;
			if (!file_exists($file_name))
				file_put_contents($file_name, '');
			if (!is_writable($file_name))
				die ($this->l('Cannot write to the theme\'s language file ').'('.$file_name.')'.$this->l('Please check write permissions.'));

			// this string is initialized one time for a file
			$str_write .= "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
			$array_check_duplicate = array();
		}

		if (!$dir)
			$dir = ($theme_name == self::DEFAULT_THEME_NAME ? _PS_MODULE_DIR_.$module_name.'/' : _PS_ALL_THEMES_DIR_.$theme_name.'/modules/'.$module_name.'/');

		foreach ($files as $template_file)
		{
			if ((preg_match('/^(.*).tpl$/', $template_file) || ($is_default && preg_match('/^(.*).php$/', $template_file))) && file_exists($tpl = $dir.$template_file))
			{
				// Get translations key
				$content = file_get_contents($tpl);
				preg_match_all(substr($template_file, -4) == '.tpl' ? self::$tpl_regexp : self::$php_regexp, $content, $matches);

				// Write each translation on its module file
				$template_name = substr(basename($template_file), 0, -4);

				foreach ($matches[1] as $key)
				{
					$post_key = md5(strtolower($module_name).'_'.($is_default ? self::DEFAULT_THEME_NAME : strtolower($theme_name)).'_'.strtolower($template_name).'_'.md5($key));
					$pattern = '\'<{'.strtolower($module_name).'}'.($is_default ? 'prestashop' : strtolower($theme_name)).'>'.strtolower($template_name).'_'.md5($key).'\'';
					if (array_key_exists($post_key, $_POST) && !empty($_POST[$post_key]) && !in_array($pattern, $array_check_duplicate))
					{
						$array_check_duplicate[] = $pattern;
						$str_write .= '$_MODULE['.$pattern.'] = \''.pSQL(str_replace(array("\r\n", "\r", "\n"), ' ', $_POST[$post_key])).'\';'."\n";
						$this->total_expression++;
					}
				}
			}
		}
		if (isset($_cache_file[($is_default ? self::DEFAULT_THEME_NAME : $theme_name).'-'.$file_name]) && $str_write != "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n")
			file_put_contents($file_name, $str_write);
	}
	public function clearModuleFiles($files, $type_clear = 'file', $path = '')
	{
		$arr_exclude = array('img', 'js', 'mails');
		$arr_good_ext = array('.tpl', '.php');
		foreach ($files as $key => $file)
		{
			if ($file{0} === '.' || in_array(substr($file, 0, strrpos($file, '.')), $this->all_iso_lang))
				unset($files[$key]);
			else if ($type_clear === 'file' && !in_array(substr($file, strrpos($file, '.')), $arr_good_ext))
				unset($files[$key]);
			else if ($type_clear === 'directory' && (!is_dir($path.$file) || in_array($file, $arr_exclude)))
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
			$dir = ($is_default ? _PS_MODULE_DIR_.$module_name.'/' : _PS_ALL_THEMES_DIR_.$theme_name.'/modules/'.$module_name.'/');

		// Thank to this var similar keys are not duplicate
		// in AndminTranslation::modules_translations array
		// see below
		$array_check_duplicate = array();
		foreach ($files as $template_file)
		{
			if ((preg_match('/^(.*).tpl$/', $template_file) || ($is_default && preg_match('/^(.*).php$/', $template_file))) && file_exists($tpl = $dir.$template_file))
			{
				$content = file_get_contents($tpl);
				// module files can now be ignored by adding this string in a file
				if (strpos($content, 'IGNORE_THIS_FILE_FOR_TRANSLATION') !== false)
					continue;
				// Get translations key
				preg_match_all(substr($template_file, -4) == '.tpl' ? self::$tpl_regexp : self::$php_regexp, $content, $matches);

				// Write each translation on its module file
				$template_name = substr(basename($template_file), 0, -4);

				foreach ($matches[1] as $key)
				{
					$module_key = ($is_default ? self::DEFAULT_THEME_NAME : '').'<{'.Tools::strtolower($module_name).'}'.
						strtolower($is_default ? 'prestashop' : $theme_name).'>'.Tools::strtolower($template_name).'_'.md5($key);
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
		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
			return;
		}
		/* PrestaShop demo mode*/

		if (Tools::isSubmit('submitCopyLang'))
		{
		 	if ($this->tabAccess['add'] === '1')
				$this->submitCopyLang();
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		else if (Tools::isSubmit('submitExport'))
		{
			if ($this->tabAccess['add'] === '1')
				$this->submitExportLang();
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		else if (Tools::isSubmit('submitImport'))
		{
		 	if ($this->tabAccess['add'] === '1')
				$this->submitImportLang();
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		else if (Tools::isSubmit('submitAddLanguage'))
		{
			if ($this->tabAccess['add'] === '1')
				$this->submitAddLang();
			else
				$this->errors[] = Tools::displayError('You do not have permission to add here.');
		}
		else if (Tools::isSubmit('submitTranslationsFront'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('Front', _PS_THEME_DIR_.'lang/'.Tools::strtolower(Tools::getValue('lang')).'.php');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsPdf'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					throw new PrestaShopException('Invalid iso code ['.Tools::getValue('lang').']');

				// Only the PrestaShop team should write the translations into the _PS_TRANSLATIONS_DIR_
				if ((_THEME_NAME_ == self::DEFAULT_THEME_NAME) && _PS_MODE_DEV_)
					$this->writeTranslationFile('PDF', _PS_TRANSLATIONS_DIR_.Tools::strtolower(Tools::getValue('lang')).'/pdf.php', 'PDF');
				else
					$this->writeTranslationFile('PDF', _PS_THEME_DIR_.'pdf/lang/'.Tools::strtolower(Tools::getValue('lang')).'.php', 'PDF');

			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsBack'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('Back', _PS_TRANSLATIONS_DIR_.Tools::strtolower(Tools::getValue('lang')).'/admin.php', 'ADM');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsErrors'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('Errors', _PS_TRANSLATIONS_DIR_.Tools::strtolower(Tools::getValue('lang')).'/errors.php', false, 'ERRORS');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsFields'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 	{
				if (!Validate::isLanguageIsoCode(Tools::strtolower(Tools::getValue('lang'))))
					die(Tools::displayError());
				$this->writeTranslationFile('Fields', _PS_TRANSLATIONS_DIR_.Tools::strtolower(Tools::getValue('lang')).'/fields.php', false, 'FIELDS');
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');

		}
		else if (Tools::isSubmit('submitTranslationsMails') || Tools::isSubmit('submitTranslationsMailsAndStay'))
		{
		 	if ($this->tabAccess['edit'] === '1' && ($id_lang = Language::getIdByIso(Tools::getValue('lang'))) > 0)
		 		$this->submitTranslationsMails($id_lang);
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsModules'))
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

					if (file_exists(_PS_THEME_DIR_.'/modules/'))
					{
						$modules = scandir(_PS_THEME_DIR_.'/modules/');
						$arr_files = $this->getAllModuleFiles($modules, _PS_THEME_DIR_.'modules/', $lang);
						$arr_find_and_write = array_merge($arr_find_and_write, $arr_files);
					}

					foreach ($arr_find_and_write as $key => $value)
						$this->findAndWriteTranslationsIntoFile($value['file_name'], $value['files'], $value['theme'], $value['module'], $value['dir']);

					if (Tools::getValue('submitTranslationsModulesAndStay'))
						Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.'&lang='.$lang.'&type=modules');
					else
						Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
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
	protected function submitTranslationsMails($id_lang)
	{
		$obj_lang = new Language($id_lang);
		$params_redirect = (Tools::isSubmit('submitTranslationsMailsAndStay') ? '&lang='.Tools::strtolower($_POST['lang']).'&type='.Tools::getValue('type') : '');

		$arr_mail_content = array();
		$arr_mail_path = array();
		if (Tools::getValue('core_mail'))
		{
			$arr_mail_content['core_mail'] = Tools::getValue('core_mail');
			$arr_mail_path['core_mail'] = _PS_MAIL_DIR_.$obj_lang->iso_code.'/';
		}
		if (Tools::getValue('module_mail'))
		{
			$arr_mail_content['module_mail'] = Tools::getValue('module_mail');
			$arr_mail_path['module_mail'] = _PS_MODULE_DIR_.'{module}'.'/mails/'.$obj_lang->iso_code.'/';
		}
		if (Tools::getValue('theme_mail'))
		{
			$arr_mail_content['theme_mail'] = Tools::getValue('theme_mail');
			$arr_mail_path['theme_mail'] = _PS_THEME_DIR_.'mails/'.$obj_lang->iso_code.'/';
		}
		if (Tools::getValue('theme_module_mail'))
		{
			$arr_mail_content['theme_module_mail'] = Tools::getValue('theme_module_mail');
			$arr_mail_path['theme_module_mail'] = _PS_THEME_DIR_.'modules/{module}'.'/mails/'.$obj_lang->iso_code.'/';
		}

		// Save each mail content
		foreach ($arr_mail_content as $group_name => $all_content)
		{
			foreach ($all_content as $type_content => $mails)
			{
				foreach ($mails as $mail_name => $content)
				{

					$module_name = false;
					$module_name_pipe_pos = stripos($mail_name, '|');
					if ($module_name_pipe_pos)
					{
						$module_name = substr($mail_name, 0, $module_name_pipe_pos);
						$mail_name = substr($mail_name, $module_name_pipe_pos + 1);
					}

					if ($type_content == 'html')
					{
						$content = Tools::htmlentitiesUTF8($content);
						$content = htmlspecialchars_decode($content);
						// replace correct end of line
						$content = str_replace("\r\n", PHP_EOL, $content);

						$title = '';
						if (Tools::getValue('title_'.$group_name.'_'.$mail_name))
							$title = Tools::getValue('title_'.$group_name.'_'.$mail_name);
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
						$this->errors[] = Tools::displayError('HTML e-mail templates cannot contain JavaScript code.');
				}
			}
		}

		// Update subjects
		$array_subjects = array();
		if (($subjects = Tools::getValue('subject')) && is_array($subjects))
		{
			$array_subjects['core_and_modules'] = array('translations'=>array(), 'path'=>$arr_mail_path['core_mail'].'lang.php');
			if (isset($arr_mail_path['theme_mail']))
				$array_subjects['themes_and_modules'] = array('translations'=>array(), 'path'=>$arr_mail_path['theme_mail'].'lang.php');

			foreach ($subjects as $group => $subject_translation)
			{
				if ($group == 'core_mail' || $group == 'module_mail')
					$array_subjects['core_and_modules']['translations'] = array_merge($array_subjects['core_and_modules']['translations'], $subject_translation);
				elseif (isset($array_subjects['themes_and_modules']) && ($group == 'theme_mail' || $group == 'theme_module_mail'))
					$array_subjects['themes_and_modules']['translations'] = array_merge($array_subjects['themes_and_modules']['translations'], $subject_translation);
			}
		}
		if (!empty($array_subjects))
			foreach ($array_subjects as $infos)
				$this->writeSubjectTranslationFile($infos['translations'], $infos['path']);

		if (count($this->errors) == 0)
			Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token.$params_redirect);
	}

	/** include file $dir/$file and return the var $var declared in it.
	 * This create the file if not exists
	 * @param string $dir
	 * @param string $file
	 * @param string $var var to return (_LANGADM, _LANG, _FIELDS, _ERRORS)
	 * return array
	 */
	public function fileExists($dir, $file, $var)
	{
		${$var} = array();
		if (!file_exists($dir))
			if (!mkdir($dir, 0700))
				throw new PrestaShopException('Directory '.$dir.' cannot be created.');
		if (!file_exists($dir.DIRECTORY_SEPARATOR.$file))
			if (!file_put_contents($dir.'/'.$file, "<?php\n\nglobal \$".$var.";\n\$".$var." = array();\n\n?>"))
				throw new PrestaShopException('File "'.$file.'" doesn\'t exists and cannot be created in '.$dir);
		if (!is_writable($dir.DIRECTORY_SEPARATOR.$file))
			$this->displayWarning(Tools::displayError('This file must be writable:').' '.$dir.'/'.$file);
		include($dir.DIRECTORY_SEPARATOR.$file);
		return ${$var};
	}

	public function displayToggleButton($closed = false)
	{
		$str_output = '
		<script type="text/javascript">';
		if (Tools::getValue('type') == 'mails')
			$str_output .= '$(document).ready(function(){
				openCloseAllDiv(\''.Tools::safeOutput($_GET['type']).'_div\', this.value == openAll); toggleElemValue(this.id, openAll, closeAll);
				});';
		$str_output .= '
			var openAll = \''.html_entity_decode($this->l('Expand all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
			var closeAll = \''.html_entity_decode($this->l('Close all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
		</script>
		<input type="button" class="button" id="buttonall" onclick="openCloseAllDiv(\''.Tools::safeOutput($_GET['type']).'_div\', this.value == openAll); toggleElemValue(this.id, openAll, closeAll);" />
		<script type="text/javascript">toggleElemValue(\'buttonall\', '.($closed ? 'openAll' : 'closeAll').', '.($closed ? 'closeAll' : 'openAll').');</script>';
		return $str_output;
	}

	protected function displaySubmitButtons($name)
	{
		return '
			<input type="submit" name="submitTranslations'.ucfirst($name).'" value="'.$this->l('Update translations').'" class="button" />
			<input type="submit" name="submitTranslations'.ucfirst($name).'AndStay" value="'.$this->l('Update and stay').'" class="button" />';
	}

	/**
	 * Init js variables for translation with google
	 *
	 * @return array of variables to assign to the smarty template
	 */
	public function initAutoTranslate()
	{
		$this->addJS('http://www.google.com/jsapi');
		$this->addJS(_PS_JS_DIR_.'gg-translate.js');
		$this->addJS(_PS_JS_DIR_.'admin-translations.js');

		$language_code = Tools::htmlentitiesUTF8(Language::getLanguageCodeByIso(Tools::getValue('lang')));
		return array('language_code' => $language_code,
					 'not_available' => addslashes(html_entity_decode($this->l('this language is not available in Google Translate\'s API'), ENT_QUOTES, 'utf-8')),
					 'tooltip_title' => addslashes(html_entity_decode($this->l('Google Translate suggests :'), ENT_QUOTES, 'utf-8'))
					);
	}

	public function displayLimitPostWarning($count)
	{
		$return = array();
		if ((ini_get('suhosin.post.max_vars') && ini_get('suhosin.post.max_vars') < $count)
		  || (ini_get('suhosin.request.max_vars') && ini_get('suhosin.request.max_vars') < $count)
			)
		{
			$this->post_limit_exceed = true;
			$return['error_type'] = 'suhosin';
			$return['post.max_vars'] = ini_get('suhosin.post.max_vars');
			$return['request.max_vars'] = ini_get('suhosin.request.max_vars');
			$return['needed_limit'] = $count + 100;
		}
		elseif (ini_get('max_input_vars') && ini_get('max_input_vars') < $count)
		{
			$this->post_limit_exceed = true;
			$return['error_type'] = 'conf';
			$return['max_input_vars'] = ini_get('max_input_vars');
			$return['needed_limit'] = $count + 100;
		}
		return $return;
	}

	public function initFormFront($lang)
	{
		$missing_translations_front = array();
		$_LANG = $this->fileExists(_PS_THEME_DIR_.'lang', Tools::strtolower($lang).'.php', '_LANG');

		/* List templates to parse */
		$templates_per_directory = array(
			_PS_THEME_DIR_ => scandir(_PS_THEME_DIR_),
			_PS_THEME_OVERRIDE_DIR_ => scandir(_PS_THEME_OVERRIDE_DIR_),
			_PS_ALL_THEMES_DIR_ => scandir(_PS_ALL_THEMES_DIR_)
		);
		$count = 0;
		$tabs_array = array();
		foreach ($templates_per_directory as $template_dir => $templates)
		{
			$prefix = '';
			if ($template_dir == _PS_THEME_OVERRIDE_DIR_)
				$prefix = 'override_';

			foreach ($templates as $template)
			{
				if (preg_match('/^(.*).tpl$/', $template) && (file_exists($tpl = $template_dir.$template)))
				{
					$prefix_key = $prefix.substr(basename($template), 0, -4);
					$new_lang = array();
					$fd = fopen($tpl, 'r');
					$content = fread($fd, filesize($tpl));

					/* Search language tags (eg {l s='to translate'}) */
					$regex = '/\{l s=\''._PS_TRANS_PATTERN_.'\'( js=1)?\}/U';
					preg_match_all($regex, $content, $matches);

					/* Get string translation */
					foreach ($matches[1] as $key)
					{
						if (empty($key))
						{
							$this->errors[] = $this->l('Empty string found, please edit:').' <br />'.$template_dir.''.$template;
							$new_lang[$key] = '';
						}
						else
						{
							// Caution ! front has underscore between prefix key and md5, back has not
							if (isset($_LANG[$prefix_key.'_'.md5($key)]))
								// @todo check if stripslashes is needed, it wasn't present in 1.4
								$new_lang[$key] = stripslashes(html_entity_decode($_LANG[$prefix_key.'_'.md5($key)], ENT_COMPAT, 'UTF-8'));
							else
							{
								if (!isset($new_lang[$key]))
								{
									$new_lang[$key] = '';
									if (!isset($missing_translations_front[$prefix_key]))
										$missing_translations_front[$prefix_key] = 1;
									else
										$missing_translations_front[$prefix_key]++;
								}
							}
						}
					}

					$tabs_array[$prefix_key] = $new_lang;
					$count += count($new_lang);
				}
			}
		}

		$this->tpl_view_vars = array(
			'lang' => Tools::strtoupper($lang),
			'translation_type' => $this->l('Front Office translations'),
			'missing_translations' => $missing_translations_front,
			'count' => $count,
			'limit_warning' => $this->displayLimitPostWarning($count),
			'post_limit_exceeded' => $this->post_limit_exceed,
			'url_submit' => self::$currentIndex.'&submitTranslationsFront=1&token='.$this->token,
			'toggle_button' => $this->displayToggleButton(),
			'tabsArray' => $tabs_array,
			'textarea_sized' => TEXTAREA_SIZED,
			'type' => 'front'
		);

		// Add js variables needed for autotranslate
		//$this->tpl_view_vars = array_merge($this->tpl_view_vars, $this->initAutoTranslate());

		$this->initToolbar();
		$this->base_tpl_view = 'translation_form.tpl';
		return parent::renderView();
	}

	public function initFormBack($lang)
	{
		$_LANGADM = $this->fileExists(_PS_TRANSLATIONS_DIR_.$lang, 'admin.php', '_LANGADM');
		// count will contain the number of expressions of the page
		$count = 0;
		$missing_translations_back = array();

		// Parse BO php files for translations
		// Add Controllers
		$tabs = scandir(_PS_ADMIN_CONTROLLER_DIR_);

		// Add override controller admin
		$tabs = array_merge($tabs, Tools::scandir(_PS_ADMIN_CONTROLLER_DIR_, 'php', '../../override/controllers/admin'));

		// Add Helpers
		$tabs = array_merge($tabs, Tools::scandir(_PS_ADMIN_CONTROLLER_DIR_, 'php', '../../classes/helper'));
		// Add parent AdminController
		$tabs[] = '../../classes/controller/AdminController.php';

		foreach ($tabs as $tab)
			if (preg_match('/^(.*)\.php$/', $tab) && file_exists($tpl = _PS_ADMIN_CONTROLLER_DIR_.$tab))
			{
				$prefix_key = basename($tab);
				// -4 becomes -14 to remove the ending "Controller.php" from the filename
				if (strpos($tab, 'Controller.php') !== false)
					$prefix_key = basename(substr($tab, 0, -14));
				elseif (strpos($tab, 'Helper') !== false)
					$prefix_key = 'Helper';

				// @todo this is retrocompatible, but we should not leave this
				if ($prefix_key == 'Admin')
					$prefix_key = 'AdminController';

				$fd = fopen($tpl, 'r');
				$content = fread($fd, filesize($tpl));
				fclose($fd);
				$regex = '/this->l\(\''._PS_TRANS_PATTERN_.'\'[\)|\,]/U';
				preg_match_all($regex, $content, $matches);
				foreach ($matches[1] as $key)
				{
					// Caution ! front has underscore between prefix key and md5, back has not
					if (isset($_LANGADM[$prefix_key.md5($key)]))
					{
						$tabs_array[$prefix_key][$key] = stripslashes(html_entity_decode($_LANGADM[$prefix_key.md5($key)], ENT_COMPAT, 'UTF-8'));
						$count++;
					}
					else
					{
						if (!isset($tabs_array[$prefix_key][$key]))
						{
							$tabs_array[$prefix_key][$key] = '';
							if (!isset($missing_translations_back[$prefix_key]))
								$missing_translations_back[$prefix_key] = 1;
							else
								$missing_translations_back[$prefix_key]++;
							$count++;
						}
					}
				}
			}

		foreach (array('header.inc', 'footer.inc', 'index', 'login', 'password', 'functions') as $tab)
		{
			$prefix_key = 'index';
			$tab = _PS_ADMIN_DIR_.'/'.$tab.'.php';
			$fd = fopen($tab, 'r');
			$content = fread($fd, filesize($tab));
			fclose($fd);
			$regex = '/translate\(\''._PS_TRANS_PATTERN_.'\'\)/U';
			preg_match_all($regex, $content, $matches);
			foreach ($matches[1] as $key)
			{
				// Caution ! front has underscore between prefix key and md5, back has not
				if (isset($_LANGADM[$prefix_key.md5($key)]))
				{
					$tabs_array[$prefix_key][$key] = stripslashes(html_entity_decode($_LANGADM[$prefix_key.md5($key)], ENT_COMPAT, 'UTF-8'));
					$count++;
				}
				else
				{
					if (!isset($tabs_array[$prefix_key][$key]))
					{
						$tabs_array[$prefix_key][$key] = '';
						if (!isset($missing_translations_back[$prefix_key]))
							$missing_translations_back[$prefix_key] = 1;
						else
							$missing_translations_back[$prefix_key]++;
						$count++;
					}
				}
			}
		}

		/* List templates to parse */
		$templates = $this->listFiles(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'themes');
		foreach ($templates as $template)
			if (preg_match('/^(.*).tpl$/', $template))
			{
				$tpl = $template;

				// get controller name instead of file name
				$prefix_key = Tools::toCamelCase(str_replace(_PS_ADMIN_DIR_.DIRECTORY_SEPARATOR.'themes', '', $tpl), true);
				$pos = strrpos($prefix_key, DIRECTORY_SEPARATOR);
				$tmp = substr($prefix_key, 0, $pos);

				if (preg_match('#controllers#', $tmp))
				{
					$parentClass = explode(DIRECTORY_SEPARATOR, $tmp);
					$key = array_search('controllers', $parentClass);
					$prefix_key = 'Admin'.ucfirst($parentClass[$key + 1]);
				}
				else
					$prefix_key = 'Admin'.ucfirst(substr($tmp, strrpos($tmp, DIRECTORY_SEPARATOR) + 1, $pos));

				// Adding list, form, option in Helper Translations
				if ($prefix_key == 'AdminHelpers' || $prefix_key == 'AdminList' || $prefix_key == 'AdminOptions' || $prefix_key == 'AdminForm' || $prefix_key == 'AdminHelpAccess')
					$prefix_key = 'Helper';

				// Adding the folder backup/download/ in AdminBackup Translations
				if ($prefix_key == 'AdminDownload')
					$prefix_key = 'AdminBackup';

				// @todo retrompatibility : we assume here than files directly in template/
				// use the prefix "AdminController" (like old php files 'header', 'footer.inc', 'index', 'login', 'password', 'functions'
				if ($prefix_key == 'Admin' || $prefix_key == 'AdminTemplate')
					$prefix_key = 'AdminController';

				$new_lang = array();
				$fd = fopen($tpl, 'r');
				$content = fread($fd, filesize($tpl));

				/* Search language tags (eg {l s='to translate'}) */
				$regex = '/\{l s=\''._PS_TRANS_PATTERN_.'\'( sprintf=.*)?( js=1)?( slashes=1)?\}/U';
				preg_match_all($regex, $content, $matches);

				/* Get string translation for each tpl file */
				foreach ($matches[1] as $english_string)
				{
					if (empty($english_string))
					{
						$this->errors[] = $this->l('Error in template - Empty string found, please edit:').' <br />'.$tpl;
						$new_lang[$english_string] = '';
					}
					else
					{
						$trans_key = $prefix_key.md5($english_string);

						if (isset($_LANGADM[$trans_key]))
						{
							$new_lang[$english_string] = html_entity_decode($_LANGADM[$trans_key], ENT_COMPAT, 'UTF-8');
							$count++;
						}
						else
						{
							if (!isset($new_lang[$english_string]))
							{
								$new_lang[$english_string] = '';
								if (!isset($missing_translations_back[$prefix_key]))
									$missing_translations_back[$prefix_key] = 1;
								else
									$missing_translations_back[$prefix_key]++;
								$count++;
							}
						}
					}
				}
				if (isset($tabs_array[$prefix_key]))
					$tabs_array[$prefix_key] = array_merge($tabs_array[$prefix_key], $new_lang);
				else
					$tabs_array[$prefix_key] = $new_lang;
			}

		// with php then tpl files, order can be a mess
		asort($tabs_array);

		$this->tpl_view_vars = array(
			'lang' => Tools::strtoupper($lang),
			'translation_type' => $this->l('Back Office translations'),
			'count' => $count,
			'limit_warning' => $this->displayLimitPostWarning($count),
			'post_limit_exceeded' => $this->post_limit_exceed,
			'url_submit' => self::$currentIndex.'&submitTranslationsBack=1&token='.$this->token,
			'toggle_button' => $this->displayToggleButton(),
			'tabsArray' => $tabs_array,
			'missing_translations' => $missing_translations_back,
			'textarea_sized' => TEXTAREA_SIZED,
			'type' => 'back'
		);

		// Add js variables needed for autotranslate
		//$this->tpl_view_vars = array_merge($this->tpl_view_vars, $this->initAutoTranslate());

		$this->initToolbar();
		$this->base_tpl_view = 'translation_form.tpl';
		return parent::renderView();
	}

	public function initFormErrors($lang)
	{
		$_ERRORS = $this->fileExists(_PS_TRANSLATIONS_DIR_.$lang, 'errors.php', '_ERRORS');
		$count_empty = 0;

		/* List files to parse */
		$stringToTranslate = array();
		$dirToParse = array(_PS_ADMIN_DIR_.'/../',
							_PS_ADMIN_DIR_.'/../classes/',
							_PS_ADMIN_DIR_.'/../controllers/front/',
							_PS_ADMIN_DIR_.'/../controllers/admin/',
							_PS_ADMIN_DIR_.'/../override/classes/',
							_PS_ADMIN_DIR_.'/../override/controllers/front/',
							_PS_ADMIN_DIR_.'/../override/controllers/admin/',
							_PS_ADMIN_DIR_.'/');
		if (!file_exists(_PS_MODULE_DIR_))
				die($this->displayWarning(Tools::displayError('Fatal error: Module directory does not exist').'('._PS_MODULE_DIR_.')'));
			if (!is_writable(_PS_MODULE_DIR_))
				$this->displayWarning(Tools::displayError('The module directory must be writable'));
			if (!$modules = scandir(_PS_MODULE_DIR_))
				$this->displayWarning(Tools::displayError('There are no modules in your copy of PrestaShop. Use the Modules tab to activate them or go to our Website to download additional Modules.'));
			else
			{
				$count = 0;

				foreach ($modules as $module)
					if (is_dir(_PS_MODULE_DIR_.$module) && $module != '.' && $module != '..' && $module != '.svn')
						$dirToParse[] = _PS_MODULE_DIR_.$module.'/';
			}
		foreach ($dirToParse as $dir)
			foreach (scandir($dir) as $file)
				if (preg_match('/\.php$/', $file) && file_exists($fn = $dir.$file) && $file != 'index.php')
				{
					if (!filesize($fn))
						continue;
					preg_match_all('/Tools::displayError\(\''._PS_TRANS_PATTERN_.'\'(, (true|false))?\)/U', fread(fopen($fn, 'r'), filesize($fn)), $matches);
					foreach ($matches[1] as $key)
					{
						$stringToTranslate[$key] = (key_exists(md5($key), $_ERRORS)) ? html_entity_decode($_ERRORS[md5($key)], ENT_COMPAT, 'UTF-8') : '';
						$this->total_expression++;

						if (empty($stringToTranslate[$key]))
							$count_empty++;
					}
				}

		$this->tpl_view_vars = array(
			'lang' => Tools::strtoupper($lang),
			'translation_type' => $this->l('Error translations'),
			'count' => $this->total_expression,
			'limit_warning' => $this->displayLimitPostWarning($this->total_expression),
			'post_limit_exceeded' => $this->post_limit_exceed,
			'url_submit' => self::$currentIndex.'&submitTranslationsErrors=1&token='.$this->token,
			'auto_translate' => '',
			'type' => 'errors',
			'errorsArray' => $stringToTranslate,
			'count_empty' => $count_empty
		);

		$this->initToolbar();
		$this->base_tpl_view = 'translation_errors.tpl';
		return parent::renderView();
	}

	public function initFormFields($lang)
	{
		$_FIELDS = $this->fileExists(_PS_TRANSLATIONS_DIR_.$lang, 'fields.php', '_FIELDS');
		$missing_translations_fields = array();
		$str_output = '';
		$classArray = array();
		$tabs_array = array();
		$count = 0;

		foreach (scandir(_PS_CLASS_DIR_) as $classFile)
		{
			if (!preg_match('/\.php$/', $classFile) || $classFile == 'index.php')
				continue;
			include_once(_PS_CLASS_DIR_.$classFile);
			$prefix_key = substr($classFile, 0, -4);
			if (!class_exists($prefix_key))
				continue;
			if (!is_subclass_of($prefix_key, 'ObjectModel'))
				continue;
			$classArray[$prefix_key] = call_user_func(array($prefix_key, 'getValidationRules'), $prefix_key);
		}

		foreach ($classArray as $prefix_key => $rules)
		{
			if (isset($rules['validate']))
				foreach ($rules['validate'] as $key => $value)
					if (isset($_FIELDS[$prefix_key.'_'.md5($key)]))
					{
						// @todo check key : md5($key) was initially md5(addslashes($key))
						$tabs_array[$prefix_key][$key] = html_entity_decode($_FIELDS[$prefix_key.'_'.md5($key)], ENT_COMPAT, 'UTF-8');
						$count++;
					}
					else
					{
						if (!isset($tabs_array[$prefix_key][$key]))
						{
							$tabs_array[$prefix_key][$key] = '';
							if (!isset($missing_translations_fields[$prefix_key]))
								$missing_translations_fields[$prefix_key] = 1;
							else
								$missing_translations_fields[$prefix_key]++;
							$count++;
						}
					}
			if (isset($rules['validateLang']))
				foreach ($rules['validateLang'] as $key => $value)
					if (isset($_FIELDS[$prefix_key.'_'.md5($key)]))
					{
						$tabs_array[$prefix_key][$key] = array_key_exists($prefix_key.'_'.md5(addslashes($key)), $_FIELDS) ? html_entity_decode($_FIELDS[$prefix_key.'_'.md5(addslashes($key))], ENT_COMPAT, 'UTF-8') : '';
						$count++;
					}
					else
					{
						if (!isset($tabs_array[$prefix_key][$key]))
						{
							$tabs_array[$prefix_key][$key] = '';
							if (!isset($missing_translations_fields[$prefix_key]))
								$missing_translations_fields[$prefix_key] = 1;
							else
								$missing_translations_fields[$prefix_key]++;
							$count++;
						}
					}
		}

		$this->tpl_view_vars = array(
			'lang' => Tools::strtoupper($lang),
			'translation_type' => $this->l('Field name translations'),
			'count' => $count,
			'limit_warning' => $this->displayLimitPostWarning($count),
			'post_limit_exceeded' => $this->post_limit_exceed,
			'url_submit' => self::$currentIndex.'&submitTranslationsFields=1&token='.$this->token,
			'toggle_button' => $this->displayToggleButton(),
			'auto_translate' => '',
			'tabsArray' => $tabs_array,
			'missing_translations' => $missing_translations_fields,
			'textarea_sized' => TEXTAREA_SIZED,
			'type' => 'fields'
		);

		$this->initToolbar();
		$this->base_tpl_view = 'translation_form.tpl';
		return parent::renderView();
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
		if (file_exists($dir.'en'))
		{
			// Get all english files to compare with the language to translate
			foreach (scandir($dir.'en') as $email_file)
			{
				if (strripos($email_file, '.html') > 0 || strripos($email_file, '.txt') > 0)
				{
					$email_name = substr($email_file, 0, strripos($email_file, '.'));
					$type = substr($email_file, strripos($email_file, '.') + 1);
					if (!isset($arr_return['files'][$email_name]))
						$arr_return['files'][$email_name] = array();
					// $email_file is from scandir ($dir), so we already know that file exists
					$arr_return['files'][$email_name][$type]['en'] = $this->getMailContent($dir, $email_file, 'en');

					// check if the file exists in the language to translate
					if (file_exists($dir.$lang.'/'.$email_file))
					{
						$arr_return['files'][$email_name][$type][$lang] = $this->getMailContent($dir, $email_file, $lang);
						$this->total_expression++;
					}
					else
						$arr_return['files'][$email_name][$type][$lang] = '';

					if ($arr_return['files'][$email_name][$type][$lang] == '')
						$arr_return['empty_values']++;
					else
						$arr_return['total_filled']++;
				}
			}
		}
		else
			// @todo : allow to translate when english is missing
			$this->warnings[] = sprintf(Tools::displayError('mail directory exists for %1$s but not for english in %2$s'),
				$lang, str_replace(_PS_ROOT_DIR_, '', $dir));
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
		$content = file_get_contents($dir.$lang.'/'.$file);

		if (Tools::strlen($content) === 0)
			$content = '';
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
			$group_name = $mails['group_name'];

		$str_return .= '
		<div class="mails_field" >
			<h3 style="cursor : pointer" onclick="$(\'#'.$id_html.'\').slideToggle();">'.$title.' - <font color="red">'.$mails['empty_values'].'</font> '
			.sprintf($this->l('missing translation(s) on %1$s template(s) for %2$s'),
				'<font color="blue">'.((int)$mails['empty_values'] + (int)$mails['total_filled']).'</font>',
			 	$obj_lang->name)
			.':</h3>
			<div name="mails_div" id="'.$id_html.'">';
		if (!empty($mails['files']))
		{
			foreach ($mails['files'] as $mail_name => $mail_files)
			{
				if (key_exists('html', $mail_files) || key_exists('txt', $mail_files))
				{
					if (key_exists($mail_name, $all_subject_mail))
					{
						$subject_mail = $all_subject_mail[$mail_name];
						$str_return .= '
						<div class="label-subject" style="text-align:center;">
							<label style="text-align:right">'.sprintf($this->l('Subject for %s:'), '<em>'.$mail_name.'</em>').'</label>
							<div class="mail-form" style="text-align:left">
								<b>'.$subject_mail.'</b><br />
								<input type="text" name="subject['.$group_name.']['.$subject_mail.']" value="'.(isset($mails['subject'][$subject_mail]) ? $mails['subject'][$subject_mail] : '').'" />
							</div>
						</div>';
					}
					else
					{
						$str_return .= '
						<div class="label-subject">
							<b>'.sprintf($this->l('No Subject was found for %s, or subject is generated in database.'), '<em>'.$mail_name.'</em>').'</b>'
						.'</div>';
					}
					if (key_exists('html', $mail_files))
					{
						$base_uri = str_replace(_PS_ROOT_DIR_, __PS_BASE_URI__, $mails['directory']);
						$base_uri = str_replace('//', '/', $base_uri);
						$url_mail = $base_uri.$obj_lang->iso_code.'/'.$mail_name.'.html';
						$str_return .= $this->displayMailBlockHtml($mail_files['html'], $obj_lang->iso_code, $url_mail, $mail_name, $group_name, $name_for_module);
					}
					if (key_exists('txt', $mail_files))
						$str_return .= $this->displayMailBlockTxt($mail_files['txt'], $obj_lang->iso_code, $mail_name, $group_name, $name_for_module);
				}
			}
		}
		else
		{
			$str_return .= '
				<p class="error">'.$this->l('There is a problem getting the Mail files.').'<br />'
				.sprintf($this->l('Please ensure that English files exist in %s folder'), '<em>'.$mails['directory'].'en</em>')
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
				$content[$language] = substr($content[$language], stripos($content[$language], '<body') + 5);
				$content[$language] = substr($content[$language], stripos($content[$language], '>') + 1);
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
				<iframe style="background:white;border:1px solid #DFD5C3;" border="0" src ="'.$url.'?'.(rand(0, 1000000000000)).'" width="565" height="497"></iframe>
					<a style="display:block;margin-top:5px;width:130px;" href="#" onclick="$(this).parent().hide(); displayTiny($(this).parent().next()); return false;" class="button">'.$this->l('Edit this e-mail template').'</a>
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
		foreach (scandir(_PS_MODULE_DIR_) as $module_dir)
			if ($module_dir[0] != '.' && file_exists(_PS_MODULE_DIR_.$module_dir.'/mails'))
				$arr_modules[$module_dir] = _PS_MODULE_DIR_.$module_dir;
		return $arr_modules;
	}
	protected function getTinyMCEForMails($iso_lang)
	{
		// TinyMCE
		$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso_lang.'.js') ? $iso_lang : 'en');
		$ad = dirname($_SERVER['PHP_SELF']);
		return '
			<script type="text/javascript">
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce.inc.js"></script>
			<script type="text/javascript">
			$(document).ready(function () {
				tinySetup();
			});
			function displayTiny(obj) {
				tinyMCE.get(obj.attr(\'name\')).show();
			}
			</script>';
	}
	public function initFormMails($lang, $noDisplay = false)
	{
		$core_mails = array();
		$module_mails = array();
		$theme_mails = array();
		$obj_lang = new Language(Language::getIdByIso($lang));

		// get all mail subjects, this method parse each files in Prestashop !!
		$subject_mail = array();
		$modules_has_mails = $this->getModulesHasMails();
		$arr_files_to_parse = array(
			_PS_ROOT_DIR_.'/controllers',
			_PS_ROOT_DIR_.'/override',
			_PS_ROOT_DIR_.'/classes',
			_PS_ADMIN_DIR_.'/tabs',
			_PS_ADMIN_DIR_,
		);
		$arr_files_to_parse = array_merge($arr_files_to_parse, $modules_has_mails);
		foreach ($arr_files_to_parse as $path)
			$subject_mail = AdminTranslationsController::getSubjectMail($path, $subject_mail);

		$core_mails = $this->getMailFiles(_PS_MAIL_DIR_, $lang, 'core_mail');
		$core_mails['subject'] = $this->getSubjectMailContent(_PS_MAIL_DIR_.$lang);
		foreach ($modules_has_mails as $module_name => $module_path)
		{
			$module_mails[$module_name] = $this->getMailFiles($module_path.'/mails/', $lang, 'module_mail');
			// @todo : all subjects (for core and modules name) are currently saved in mails/$lang/lang.php instead of module directory
			$module_mails[$module_name]['subject'] = $core_mails['subject'];
			$module_mails[$module_name]['display'] = $this->displayMailContent($module_mails[$module_name], $subject_mail, $obj_lang, Tools::strtolower($module_name), sprintf($this->l('E-mails for %s module'), '<em>'.$module_name.'</em>'), $module_name);
		}

		// Before 1.4.0.14 each theme folder was parsed,
		// This page was really to low to load.
		// Now just use the current theme.
		if (_THEME_NAME_ !== self::DEFAULT_THEME_NAME)
		{
			if (file_exists(_PS_THEME_DIR_.'mails'))
			{
				$theme_mails['theme_mail'] = $this->getMailFiles(_PS_THEME_DIR_.'mails/', $lang, 'theme_mail');
				$theme_mails['theme_mail']['subject'] = $this->getSubjectMailContent(_PS_THEME_DIR_.'mails/'.$lang);
				$theme_mails['theme_mail']['display'] = $this->displayMailContent($theme_mails['theme_mail'], $subject_mail, $obj_lang, 'theme_theme_mail', ucfirst(_THEME_NAME_));
			}
			if (file_exists(_PS_THEME_DIR_.'/modules'))
			{
				foreach (scandir(_PS_THEME_DIR_.'/modules') as $module_dir)
				{
					if ($module_dir[0] != '.' && file_exists(_PS_THEME_DIR_.'modules/'.$module_dir.'/mails'))
					{
						$theme_mails[$module_dir] = $this->getMailFiles(_PS_THEME_DIR_.'modules/'.$module_dir.'/mails/', $lang, 'theme_module_mail');
						$theme_mails[$module_dir]['subject'] = $theme_mails['theme_mail']['subject'];
						$title = $module_dir != 'theme_mail' ? ucfirst(_THEME_NAME_).' '.sprintf($this->l('E-mails for %s module'), '<em>'.$module_dir.'</em>') : ucfirst(_THEME_NAME_).' '.$this->l('E-mails');
						$theme_mails[$module_dir]['display'] = $this->displayMailContent($theme_mails[$module_dir], $subject_mail, $obj_lang, 'theme_'.Tools::strtolower($module_dir), $title, ($module_dir != 'theme_mail' ? $module_dir : false));
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

		$this->tpl_view_vars = array(
			'lang' => Tools::strtoupper($lang),
			'translation_type' => $this->l('E-mail template translations'),
			'limit_warning' => $this->displayLimitPostWarning($this->total_expression),
			'post_limit_exceeded' => $this->post_limit_exceed,
			'url_submit' => self::$currentIndex.'&submitTranslationsMails=1&token='.$this->token,
			'toggle_button' => $this->displayToggleButton(),
			'auto_translate' => '',
			'type' => 'mails',
			'tinyMCE' => $this->getTinyMCEForMails($obj_lang->iso_code),
			'mail_content' => $this->displayMailContent($core_mails, $subject_mail, $obj_lang, 'core', $this->l('Core e-mails')),
			'module_mails' => $module_mails,
			'theme_mails' => $theme_mails,
			'theme_name' => _THEME_NAME_
		);

		$this->initToolbar();
		$this->base_tpl_view = 'translation_mails.tpl';
		return parent::renderView();
	}

	protected static function getSubjectMail($directory, $subject_mail)
	{
		foreach (scandir($directory) as $filename)
		{
			if (strripos($filename, '.php') > 0 && $filename != 'AdminTranslations.php')
			{
				$content = file_get_contents($directory.'/'.$filename);
				$content = str_replace("\n", ' ', $content);
				if (preg_match_all('/Mail::Send([^;]*);/si', $content, $tab))
				{
					for ($i = 0; isset($tab[1][$i]); $i++)
					{
						$tab2 = explode(',', $tab[1][$i]);
						if (is_array($tab2))
						{
							if ($tab2 && isset($tab2[1]))
							{
								$tab2[1] = trim(str_replace('\'', '', $tab2[1]));
								if (preg_match('/Mail::l\(\''._PS_TRANS_PATTERN_.'\'/s', $tab2[2], $tab3))
									$tab2[2] = $tab3[1];
								$subject_mail[$tab2[1]] = $tab2[2];
							}
						}
					}
				}
			}
			if ($filename != '.svn' && $filename != '.' && $filename != '..' && is_dir(($directory.'/'.$filename)))
				 $subject_mail = AdminTranslationsController::getSubjectMail($directory.'/'.$filename, $subject_mail);
		}
		return $subject_mail;
	}

	protected function getSubjectMailContent($directory)
	{
		$subject_mail_content = array();

		if (Tools::file_exists_cache($directory.'/lang.php'))
		{
			// we need to include this even if already included (no include once)
			include($directory.'/lang.php');
			foreach ($_LANGMAIL as $key => $subject)
			{
				$this->total_expression++;
				$subject = str_replace('\n', ' ', $subject);
				$subject = str_replace("\\'", "\'", $subject);

				$subject_mail_content[$key] = htmlentities($subject, ENT_QUOTES, 'UTF-8');
			}
		}
		else
			$this->errors[] = $this->l('Subject mail translation file not found in').' '.$directory;
		return $subject_mail_content;
	}

	protected function writeSubjectTranslationFile($sub, $path, $mark = false, $fullmark = false)
	{
		if ($fd = @fopen($path, 'w'))
		{
			//$tab = ($fullmark ? Tools::strtoupper($fullmark) : 'LANG').($mark ? Tools::strtoupper($mark) : '');
			$tab = 'LANGMAIL';
			fwrite($fd, "<?php\n\nglobal \$_".$tab.";\n\$_".$tab." = array();\n");

			foreach ($sub as $key => $value)
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
			die($this->l('Cannot write language file for e-mail subjects, path is:').$path);
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
		if (!empty($dir_module))
		{
			foreach ($dir_module as $folder)
				$this->recursiveGetModuleFiles($path.$folder.'/', $array_files, $module_name, $lang_file, $is_default);
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
	protected function getAllModuleFiles($modules, $root_dir, $lang, $is_default = false)
	{
		$array_files = array();
		foreach ($modules as $module)
		{
			if ($module{0} != '.' && is_dir($root_dir.$module))
			{
				if (file_exists($root_dir.$module.'/translations/'.$lang.'.php'))
					$lang_file = $root_dir.$module.'/translations/'.$lang.'.php';
				else
					$lang_file = $root_dir.$module.'/'.$lang.'.php';
				@include($lang_file);
				AdminTranslationsController::getModuleTranslations($is_default);
				$this->recursiveGetModuleFiles($root_dir.$module.'/', $array_files, $module, $lang_file, $is_default);
			}
		}
		return $array_files;
	}

	public function initFormModules($lang)
	{
		global $_MODULES;

		$array_lang_src = Language::getLanguages(false);

		foreach ($array_lang_src as $language)
			$this->all_iso_lang[] = $language['iso_code'];

		if (!file_exists(_PS_MODULE_DIR_))
			die($this->displayWarning(Tools::displayError('Fatal error: Module directory does not exist').'('._PS_MODULE_DIR_.')'));
		if (!is_writable(_PS_MODULE_DIR_))
			$this->displayWarning(Tools::displayError('The module directory must be writable'));
		if (!$modules = scandir(_PS_MODULE_DIR_))
			$this->displayWarning(Tools::displayError('There are no modules in your copy of PrestaShop. Use the Modules tab to activate them or go to our Website to download additional Modules.'));
		else
		{
			if (!_PS_MODE_DEV_)
			{
				// Get all module which are installed for to have a minimum of POST
				$modules = Module::getModulesInstalled();

				foreach ($modules as &$module)
					$module = $module['name'];
			}

			$arr_find_and_fill = array();

			$arr_files = $this->getAllModuleFiles($modules, _PS_MODULE_DIR_, $lang, true);
			$arr_find_and_fill = array_merge($arr_find_and_fill, $arr_files);

			if (file_exists(_PS_THEME_DIR_.'/modules/'))
			{
				$modules = scandir(_PS_THEME_DIR_.'/modules/');
				$arr_files = $this->getAllModuleFiles($modules, _PS_THEME_DIR_.'modules/', $lang);
				$arr_find_and_fill = array_merge($arr_find_and_fill, $arr_files);
			}
			foreach ($arr_find_and_fill as $value)
				$this->findAndFillTranslations($value['files'], $value['theme'], $value['module'], $value['dir'], $lang);

			$this->tpl_view_vars = array(
				'default_theme_name' => self::DEFAULT_THEME_NAME,
				'lang' => Tools::strtoupper($lang),
				'translation_type' => $this->l('Installed module translations'),
				'count' => $this->total_expression,
				'limit_warning' => $this->displayLimitPostWarning($this->total_expression),
				'post_limit_exceeded' => $this->post_limit_exceed,
				'url_submit' => self::$currentIndex.'&submitTranslationsModules=1&token='.$this->token,
				'toggle_button' => $this->displayToggleButton(),
				'textarea_sized' => TEXTAREA_SIZED,
				'type' => 'modules',
				'modules_translations' => isset($this->modules_translations) ? $this->modules_translations : array()
			);

			$this->initToolbar();
			$this->base_tpl_view = 'translation_modules.tpl';
			return parent::renderView();
		}
	}

	/** parse $filepath to find expression which match $regex, and return an
	 *
	 * @param string $filepath file to parse
	 * @param string $regex regexp to use
	 * @param array $langArray contains expression in the chosen language
	 * @param string $tab name to use with the md5 key
	 * @param array $tabs_array
	 * @return array containing all datas needed for building the translation form
	 * @since 1.4.5.0
	 */
	protected function _parsePdfClass($filepath, $regex, $langArray, $tab, $tabs_array)
	{
		$content = file_get_contents($filepath);
		preg_match_all($regex, $content, $matches);
		foreach ($matches[1] as $key)
			$tabs_array[$tab][$key] = stripslashes(key_exists($tab.md5(addslashes($key)), $langArray) ? html_entity_decode($langArray[$tab.md5(addslashes($key))], ENT_COMPAT, 'UTF-8') : '');
		return $tabs_array;
	}

	public function initFormPDF()
	{
		$lang = Tools::strtolower(Tools::getValue('lang'));
        $_LANGPDF = array();
		$missing_translations_pdf = array();

		if (!Validate::isLangIsoCode($lang))
			die('Invalid iso lang ('.Tools::safeOutput($lang).')');

		$i18n_dir  = _PS_TRANSLATIONS_DIR_.$lang.'/';
		$default_i18n_file = $i18n_dir.'pdf.php';

		if ((_THEME_NAME_ != self::DEFAULT_THEME_NAME) || !_PS_MODE_DEV_)
		{
			$i18n_dir = _PS_THEME_DIR_.'pdf/lang/';
			$i18n_file = $i18n_dir.$lang.'.php';
		}
		else
			$i18n_file = $default_i18n_file;

        $this->checkDirAndCreate($i18n_file);
		if (!file_exists($i18n_file))
			die('Please create a "'.$lang.'.php" file in '.$i18n_dir);

		if (!is_writable($i18n_file))
			die('Cannot write into the "'.$i18n_file);

		unset($_LANGPDF);
		@include($i18n_file);

		// if the override's translation file is empty load the default file
		if (!isset($_LANGPDF) || count($_LANGPDF) == 0)
			@include($default_i18n_file);

		$count = 0;
		$prefix_key = 'PDF';
		$tabs_array = array($prefix_key=>array());
		$regex = '/HTMLTemplate.*::l\(\''._PS_TRANS_PATTERN_.'\'[\)|\,]/U';

		// need to parse PDF.php in order to find $regex and add this to $tabs_array
		// this has to be done for the core class, and eventually for the override
		foreach (glob(_PS_CLASS_DIR_.'pdf/*.php') as $filename)
		{
			$tabs_array = $this->_parsePdfClass($filename, $regex, $_LANGPDF, $prefix_key, $tabs_array);
			if (file_exists(_PS_ROOT_DIR_.'/override/classes/pdf/'.basename($filename)))
				$tabs_array = $this->_parsePdfClass(_PS_ROOT_DIR_.'/override/classes/pdf/'.basename($filename), $regex, $_LANGPDF, $prefix_key, $tabs_array);
		}

		// parse pdf template
		/* Search language tags (eg {l s='to translate'}) */
		$regex = '/\{l s=\''._PS_TRANS_PATTERN_.'\'( sprintf=.*)?( js=1)?( pdf=\'true\')?\}/U';
      $default_template_files = glob(_PS_PDF_DIR_.'*.tpl');
      $override_template_files = glob(_PS_THEME_DIR_.'pdf/*.tpl');

		foreach (array_merge($default_template_files, $override_template_files) as $filename)
		{
			preg_match_all($regex, file_get_contents($filename), $matches);
			foreach ($matches[1] as $key)
			{
				if (isset($_LANGPDF[$prefix_key.md5($key)]))
				{
					// @todo check key : md5($key) was initially md5(addslashes($key))
					$tabs_array[$prefix_key][$key] = (html_entity_decode($_LANGPDF[$prefix_key.md5($key)], ENT_COMPAT, 'UTF-8'));
					$count++;
				}
				else
				{
					if (!isset($tabs_array[$prefix_key][$key]))
					{
						$tabs_array[$prefix_key][$key] = '';
						if (!isset($missing_translations_pdf[$prefix_key]))
							$missing_translations_pdf[$prefix_key] = 1;
						else
							$missing_translations_pdf[$prefix_key]++;
						$count++;
					}
				}
			}
		}

		$this->tpl_view_vars = array(
			'lang' => Tools::strtoupper($lang),
			'translation_type' => $this->l('PDF translations'),
			'count' => $count,
			'limit_warning' => $this->displayLimitPostWarning($this->total_expression),
			'post_limit_exceeded' => $this->post_limit_exceed,
			'url_submit' => self::$currentIndex.'&submitTranslationsPdf=1&token='.$this->token,
			'toggle_button' => $this->displayToggleButton(),
			'auto_translate' => '',
			'textarea_sized' => TEXTAREA_SIZED,
			'type' => 'pdf',
			'tabsArray' => $tabs_array,
			'missing_translations' => $missing_translations_pdf
		);

		$this->initToolbar();
		$this->base_tpl_view = 'translation_form.tpl';
		return parent::renderView();
	}

	/**
	  * Return an array with themes and thumbnails
	  *
	  * @return array
	  */
	public static function getThemesList()
	{
		$dir = opendir(_PS_ALL_THEMES_DIR_);
		while ($folder = readdir($dir))
			if ($folder != '.' && $folder != '..' && is_dir(_PS_ALL_THEMES_DIR_.DIRECTORY_SEPARATOR.$folder) && file_exists(_PS_ALL_THEMES_DIR_.'/'.$folder.'/preview.jpg'))
				$themes[$folder]['name'] = $folder;
		closedir($dir);
		return isset($themes) ? $themes : array();
	}
	/** recursively list files in directory $dir
	 *
	 */
	public function listFiles($dir, $list = array())
	{
		$fileext = 'tpl';
		$res = true;
		$dir = rtrim($dir, '/').DIRECTORY_SEPARATOR;

		$to_parse = scandir($dir);
		// copied (and kind of) adapted from AdminImages.php
		foreach ($to_parse as $file)
		{
			if ($file != '.' && $file != '..' && $file != '.svn')
			{
				if (preg_match('#'.preg_quote($fileext, '#').'$#i', $file))
					$list[] = $dir.$file;
				else if (is_dir($dir.$file))
					$list = $this->listFiles($dir.$file, $list);
			}
		}
		return $list;
	}

}

