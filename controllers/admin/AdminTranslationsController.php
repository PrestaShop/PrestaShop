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
	/** Name of theme by default */
	const DEFAULT_THEME_NAME = 'default';

	/** @var string : Link which list all pack of language */
	protected $link_lang_pack = 'http://www.prestashop.com/download/lang_packs/get_each_language_pack.php';

	/** @var int : number of sentence which can be translated */
	protected $total_expression = 0;

	/** @var int : number of sentence which aren't translated */
	protected $missing_translations = 0;

	/** @var array : List of ISO code for all languages */
	protected $all_iso_lang = array();

	/** @var array */
	protected $modules_translations = array();

	/** @var array : List of folder which must be ignored */
	protected static $ignore_folder = array('.', '..', '.svn', '.htaccess', 'index.php');

	/** @var array : List of theme by translation type : FRONT, BACK, ERRORS... */
	protected $translations_informations = array();

	/** @var array : List of theme by translation type : FRONT, BACK, ERRORS... */
	protected $translations_type_for_theme = array('front', 'modules', 'pdf', 'mails');

	/** @var array : List of all languages */
	protected $languages;

	/** @var array : List of all themes */
	protected $themes;

	/** @var string : Directory of selected theme */
	protected $theme_selected;

	/** @var string : Name of translations type */
	protected $type_selected;

	/** @var object : Language for the selected language */
	protected $lang_selected;

	/** @var boolean : Is true if number of var exceed the suhosin request or post limit */
	protected $post_limit_exceed = false;

	public function __construct()
	{
		$this->multishop_context = Shop::CONTEXT_ALL;

		parent::__construct();

	 	$this->table = 'translations';

		// Include all file for create or read an archive
		include_once(_PS_ADMIN_DIR_.'/../tools/tar/Archive_Tar.php');
		include_once(_PS_ADMIN_DIR_.'/../tools/pear/PEAR.php');
	}

	/*
	 * Set the type which is selected
	 */
	public function setTypeSelected($type_selected)
	{
		$this->type_selected = $type_selected;
	}

	/**
	 * AdminController::initContent() override
	 * @see AdminController::initContent()
	 */
	public function initContent()
	{
		if (!is_null($this->type_selected))
		{
			$method_name = 'initForm'.$this->type_selected;
			if (method_exists($this, $method_name))
				$this->content = $this->initForm($method_name);
			else
			{
				$this->errors[] = sprintf(Tools::displayError('"%s" does not exist. Maybe you typed the URL manually.'), $this->type_selected);
				$this->content = $this->initMain();
			}
		}
		else
			$this->content = $this->initMain();

		$this->context->smarty->assign(array('content' => $this->content));
	}

	/**
	 * This function create vars by default and call the good method for generate form
	 *
	 * @param $method_name
	 * @return call the method $this->method_name()
	 */
	public function initForm($method_name)
	{
		// Create a title for each translation page
		$title = sprintf(
			$this->l('%1$s (Language: %2$s, Theme: %3$s)'),
			$this->translations_informations[$this->type_selected]['name'],
			$this->lang_selected->name,
			$this->theme_selected
		);

		// Set vars for all forms
		$this->tpl_view_vars = array(
			'lang' => $this->lang_selected->iso_code,
			'title' => $title,
			'type' => $this->type_selected,
			'theme' => $this->theme_selected,
			'post_limit_exceeded' => $this->post_limit_exceed,
			'url_submit' => self::$currentIndex.'&submitTranslations'.ucfirst($this->type_selected).'=1&token='.$this->token,
			'toggle_button' => $this->displayToggleButton(),
			'textarea_sized' => TEXTAREA_SIZED,
			'auto_translate' => ''
		);

		// Call method initForm for a type
		return $this->{$method_name}();
	}

	/**
	 * AdminController::initToolbar() override
	 * @see AdminController::initToolbar()
	 */
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

	/**
	 * Generate the Main page
	 */
	public function initMain()
	{
		// Block add/update a language
		$packs_to_install = array();
		$packs_to_update = array();
		$token = Tools::getAdminToken('AdminLanguages'.(int)Tab::getIdFromClassName('AdminLanguages').(int)$this->context->employee->id);
		$file_name = $this->link_lang_pack.'?version='._PS_VERSION_;
		$array_stream_context = array('http' => array('method' => 'GET', 'timeout' => 5));
		if ($lang_packs = Tools::file_get_contents($file_name, false, @stream_context_create($array_stream_context)))
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
			'theme_default' => self::DEFAULT_THEME_NAME,
			'theme_lang_dir' =>_THEME_LANG_DIR_,
			'token' => $this->token,
			'languages' => $this->languages,
			'translations_type' => $this->translations_informations,
			'translations_type_for_theme' => $this->translations_type_for_theme,
			'packs_to_install' => $packs_to_install,
			'packs_to_update' => $packs_to_update,
			'url_submit' => self::$currentIndex.'&token='.$this->token,
			'themes' => $this->themes,
			'id_theme_current' => $this->context->shop->id_theme,
			'url_create_language' => 'index.php?controller=AdminLanguages&addlang&token='.$token,
		);

		$this->toolbar_scroll = false;
		$this->base_tpl_view = 'main.tpl';
		return parent::renderView();
	}

	/**
	 * This method merge each arrays of modules translation in the array of modules translations
	 */
	protected function getModuleTranslations()
	{
		global $_MODULE;
		$name_var = $this->translations_informations[$this->type_selected]['var'];

		if (!isset($_MODULE) && !isset($GLOBALS[$name_var]))
			$GLOBALS[$name_var] = array();
		else if (isset($_MODULE))
			if (is_array($GLOBALS[$name_var]) && is_array($_MODULE))
				$GLOBALS[$name_var] = array_merge($GLOBALS[$name_var], $_MODULE);
			else
				$GLOBALS[$name_var] = $_MODULE;
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
		if (!Tools::file_exists_cache($path))
			if (!mkdir($path, 0777, true))
			{
				$bool &= false;
				$this->errors[] = sprintf($this->l('Cannot create the folder "%s". Check directory writing permisions.'), $path);
			}

		return $bool;
	}

	/**
	 * Read the Post var and write the translation file.
	 * This method overwrites the old translation file.
	 *
	 * @param bool $override_file : set true if this file is a override
	 */
	protected function writeTranslationFile($override_file = false)
	{
		$type = Tools::toCamelCase($this->type_selected, true);
		$translation_informations = $this->translations_informations[$this->type_selected];

		if ($override_file)
			$file_path = $translation_informations['override']['dir'].$translation_informations['override']['file'];
		else
			$file_path = $translation_informations['dir'].$translation_informations['file'];

		if (!file_exists($file_path))
			throw new PrestaShopException(sprintf(Tools::displayError('This file doesn\'t exists: "%s". Please create this file.'), $file_path));

		if ($fd = fopen($file_path, 'w'))
		{
			// Get value of button save and stay
			$save_and_stay = Tools::getValue('submitTranslations'.$type.'AndStay');

			// Get language
			$lang = strtolower(Tools::getValue('lang'));

			// Unset all POST which are not translations
			unset(
				$_POST['submitTranslations'.$type],
				$_POST['submitTranslations'.$type.'AndStay'],
				$_POST['lang'],
				$_POST['token'],
				$_POST['theme'],
				$_POST['type']
			);

			// Get all POST which aren't empty
			$to_insert = array();
			foreach ($_POST as $key => $value)
				if (!empty($value))
					$to_insert[$key] = $value;

			// translations array is ordered by key (easy merge)
			ksort($to_insert);
			$tab = $translation_informations['var'];
			fwrite($fd, "<?php\n\nglobal \$".$tab.";\n\$".$tab." = array();\n");
			foreach ($to_insert as $key => $value)
				fwrite($fd, '$'.$tab.'[\''.pSQL($key, true).'\'] = \''.pSQL($value, true).'\';'."\n");
			fwrite($fd, "\n?>");
			fclose($fd);

			// Redirect
			if ($save_and_stay)
				$this->redirect(true);
			else
				$this->redirect();
		}
		else
			throw new PrestaShopException(sprintf(Tools::displayError('Cannot write this file: "%s"'), $file_path));
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
			$this->redirect(false, 14);
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

	public function exportTabs()
	{
		// Get name tabs by iso code
		$tabs = Tab::getTabs($this->lang_selected->id);

		// Get name of the default tabs
		$tabs_default_lang = Tab::getTabs(1);

		$tabs_default = array();
		foreach ($tabs_default_lang as $tab)
			$tabs_default[$tab['class_name']] = pSQL($tab['name']);

		// Create content
		$content = "<?php\n\n\$tabs = array();";
		if (!empty($tabs))
			foreach ($tabs as $tab)
				if ($tabs_default[$tab['class_name']] != pSQL($tab['name']))
				$content .= "\n\$tabs['".$tab['class_name']."'] = '".pSQL($tab['name'])."';";
		$content .= "\n\nreturn \$tabs;";

		$dir = _PS_TRANSLATIONS_DIR_.$this->lang_selected->iso_code.DIRECTORY_SEPARATOR;
		$path = $dir.'tabs.php';

		// Check if tabs.php exists for the selected Iso Code
		if (!Tools::file_exists_cache($dir))
			if (!mkdir($dir, 0777, true))
				throw new PrestaShopException('The file '.$dir.' cannot be created.');
		if (!file_put_contents($path, $content))
				throw new PrestaShopException('File "'.$path.'" doesn\'t exists and cannot be created in '.$dir);
		if (!is_writable($path))
			$this->displayWarning(sprintf(Tools::displayError('This file must be writable: %s'), $path));
	}

	public function submitExportLang()
	{
		if ($this->lang_selected->iso_code && $this->theme_selected)
		{
			$this->exportTabs();
			$items = array_flip(Language::getFilesList($this->lang_selected->iso_code, $this->theme_selected, false, false, false, false, true));
			$gz = new Archive_Tar(_PS_TRANSLATIONS_DIR_.'/export/'.$this->lang_selected->iso_code.'.gzip', true);
			$file_name = Tools::getCurrentUrlProtocolPrefix().Tools::getShopDomain().__PS_BASE_URI__.'translations/export/'.$this->lang_selected->iso_code.'.gzip';
			if ($gz->createModify($items, null, _PS_ROOT_DIR_));
			{
				ob_start();
				header('Pragma: public');
				header('Expires: 0');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Cache-Control: public');
				header('Content-Description: File Transfer');
				header('Content-type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'.$this->lang_selected->iso_code.'.gzip'.'"');
				header('Content-Transfer-Encoding: binary');
				ob_end_flush();
				@readfile($file_name);
			}
			$this->errors[] = Tools::displayError('An error occurred while creating archive.');
		}
		$this->errors[] = Tools::displayError('Please choose a language and a theme.');
	}

	public static function checkAndAddMailsFiles($iso_code, $files_list)
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
			if (!in_array($mail_to_add, self::$ignore_folder))
				@copy(_PS_MAIL_DIR_.'en/'.$mail_to_add, _PS_MAIL_DIR_.$iso_code.'/'.$mail_to_add);

		// 2 - Scan modules files
		$modules = scandir(_PS_MODULE_DIR_);

		$module_mail_en = array();
		$module_mail_iso_code = array();

		foreach ($modules as $module)
		{
			if (!in_array($module, self::$ignore_folder) && Tools::file_exists_cache(_PS_MODULE_DIR_.$module.'/mails/en/'))
			{
				$arr_files = scandir(_PS_MODULE_DIR_.$module.'/mails/en/');

				foreach ($arr_files as $file)
				{
					if (!in_array($file, self::$ignore_folder))
					{
						if (Tools::file_exists_cache(_PS_MODULE_DIR_.$module.'/mails/en/'.$file))
							$module_mail_en[] = _PS_MODULE_DIR_.$module.'/mails/ISO_CODE/'.$file;

						if (Tools::file_exists_cache(_PS_MODULE_DIR_.$module.'/mails/'.$iso_code.'/'.$file))
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

			if (Tools::file_exists_cache($file_en))
				copy($file_en, $file_iso_code);
		}
	}

	/**
	 * Move theme translations in selected themes
	 *
	 * @param array $files
	 * @param array $themes_selected
	 */
	public function checkAndAddThemesFiles($files, $themes_selected)
	{
		foreach ($files as $file)
		{
			// Check if file is a file theme
			if (preg_match('#^themes\/([a-z0-9]+)\/lang\/#Ui', $file['filename'], $matches))
			{
				$slash_pos = strrpos($file['filename'], '/');
				$name_file = substr($file['filename'], -(strlen($file['filename']) - $slash_pos - 1));
				$name_default_theme = $matches[1];
				$deleted_old_theme = false;

				// Get the old file theme
				if (file_exists(_PS_THEME_DIR_.'lang/'.$name_file))
					$theme_file_old = _PS_THEME_DIR_.'lang/'.$name_file;
				else
				{
					$deleted_old_theme = true;
					$theme_file_old = str_replace(self::DEFAULT_THEME_NAME, $name_default_theme, _PS_THEME_DIR_.'lang/'.$name_file);
				}

				// Move the old file theme in the new folder
				foreach ($themes_selected as $theme_name)
					if (file_exists($theme_file_old))
						copy($theme_file_old, str_replace($name_default_theme, $theme_name, $theme_file_old));

				if ($deleted_old_theme)
					@unlink($theme_file_old);
			}
		}
	}

	/**
	 * Add new translations tabs by code ISO
	 *
	 * @param array $iso_code
	 * @param array $files
	 */
	public static function addNewTabs($iso_code, $files)
	{
		foreach ($files as $file)
		{
			// Check if file is a file theme
			if (preg_match('#^translations\/'.$iso_code.'\/tabs.php#Ui', $file['filename'], $matches) && Validate::isLanguageIsoCode($iso_code))
			{
				// Include array width new translations tabs
				$tabs = include _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.$file['filename'];

				foreach ($tabs as $class_name => $translations)
				{
					// Get instance of this tab by class name
					$tab = Tab::getInstanceFromClassName($class_name);

					//Check if class name exists
					if (isset($tab->class_name) && !empty($tab->class_name))
					{
						$id_lang = Language::getIdByIso($iso_code);
						$tab->name[(int)$id_lang] = pSQL($translations);

						// Update this tab
						$tab->update();
					}
				}
			}
		}
	}

	public function submitImportLang()
	{
		if (!isset($_FILES['file']['tmp_name']) || !$_FILES['file']['tmp_name'])
			$this->errors[] = Tools::displayError('No file selected');
		else
		{
			$gz = new Archive_Tar($_FILES['file']['tmp_name'], true);
			$filename = $_FILES['file']['name'];
			$iso_code = str_replace(array('.tar.gz', '.gzip'), '', $filename);
			if (Validate::isLangIsoCode($iso_code))
			{
				$themes_selected = Tools::getValue('theme', array(self::DEFAULT_THEME_NAME));
				$files_list = $gz->listContent();
				if ($gz->extract(_PS_TRANSLATIONS_DIR_.'../', false))
				{
					AdminTranslationsController::checkAndAddMailsFiles($iso_code, $files_list);
					$this->checkAndAddThemesFiles($files_list, $themes_selected);
					AdminTranslationsController::addNewTabs($iso_code, $files_list);
					if (Validate::isLanguageFileName($filename))
					{
						if (!Language::checkAndAddLanguage($iso_code))
							$conf = 20;
					}
					$this->redirect(false, (isset($conf) ? $conf : '15'));
				}
				$this->errors[] = Tools::displayError('Archive cannot be extracted.');
			}
			else
				$this->errors[] = sprintf(Tools::displayError('ISO CODE invalid "%1$s" for the following file: "%2$s"'), $iso_code, $filename);
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
						AdminTranslationsController::checkAndAddMailsFiles($arr_import_lang[0], $files_list);
						AdminTranslationsController::addNewTabs($arr_import_lang[0], $files_list);
						if (!Language::checkAndAddLanguage($arr_import_lang[0]))
							$conf = 20;
						if (!unlink($file))
							$this->errors[] = Tools::displayError('Cannot delete archive');

						$this->redirect(false, (isset($conf) ? $conf : '15'));
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
		static $cache_file = array();
		static $str_write = '';
		static $array_check_duplicate = array();

		// Default translations and Prestashop overriding themes are distinguish
		$is_default = $theme_name === self::DEFAULT_THEME_NAME ? true : false;

		// Set file_name in static var, this allow to open and wright the file just one time
		if (!isset($cache_file[$theme_name.'-'.$file_name]))
		{
			$str_write = '';
			$cache_file[$theme_name.'-'.$file_name] = true;
			if (!Tools::file_exists_cache($file_name))
				file_put_contents($file_name, '');
			if (!is_writable($file_name))
				throw new PrestaShopException(sprintf(
					Tools::displayError('Cannot write to the theme\'s language file (%s). Please check write permissions.'),
					$file_name
				));

			// this string is initialized one time for a file
			$str_write .= "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n";
			$array_check_duplicate = array();
		}

		foreach ($files as $file)
		{
			if (preg_match('/^(.*).(tpl|php)$/', $file) && Tools::file_exists_cache($file_path = $dir.$file) && !in_array($file, self::$ignore_folder))
			{
				// Get content for this file
				$content = file_get_contents($file_path);

				// Get file type
				$type_file = substr($file, -4) == '.tpl' ? 'tpl' : 'php';

				// Parse this content
				$matches = $this->userParseFile($content, $this->type_selected, $type_file);

				// Write each translation on its module file
				$template_name = substr(basename($file), 0, -4);

				foreach ($matches as $key)
				{
					if ($is_default)
					{
						$post_key = md5(strtolower($module_name).'_'.self::DEFAULT_THEME_NAME.'_'.strtolower($template_name).'_'.md5($key));
						$pattern = '\'<{'.strtolower($module_name).'}prestashop>'.strtolower($template_name).'_'.md5($key).'\'';
					}
					else
					{
						$post_key = md5(strtolower($module_name).'_'.strtolower($theme_name).'_'.strtolower($template_name).'_'.md5($key));
						$pattern = '\'<{'.strtolower($module_name).'}'.strtolower($theme_name).'>'.strtolower($template_name).'_'.md5($key).'\'';
					}

					if (array_key_exists($post_key, $_POST) && !empty($_POST[$post_key]) && !in_array($pattern, $array_check_duplicate))
					{
						$array_check_duplicate[] = $pattern;
						$str_write .= '$_MODULE['.$pattern.'] = \''.pSQL(str_replace(array("\r\n", "\r", "\n"), ' ', $_POST[$post_key])).'\';'."\n";
						$this->total_expression++;
					}
				}
			}
		}

		if (isset($cache_file[$theme_name.'-'.$file_name]) && $str_write != "<?php\n\nglobal \$_MODULE;\n\$_MODULE = array();\n")
			file_put_contents($file_name, $str_write);
	}

	/**
	 * Clear the list of module file by type (file or directory)
	 *
	 * @param $files : list of files
	 * @param string $type_clear (file|directory)
	 * @param string $path
	 * @return array : list of a good files
	 */
	public function clearModuleFiles($files, $type_clear = 'file', $path = '')
	{
		// List of directory which not must be parsed
		$arr_exclude = array('img', 'js', 'mails');

		// List of good extention files
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
	protected function findAndFillTranslations($files, $theme_name, $module_name, $dir = false)
	{
		$name_var = $this->translations_informations[$this->type_selected]['var'];

		// added for compatibility
		$GLOBALS[$name_var] = array_change_key_case($GLOBALS[$name_var]);

		// Default translations and Prestashop overriding themes are distinguish
		$is_default = $theme_name === self::DEFAULT_THEME_NAME ? true : false;

		// Thank to this var similar keys are not duplicate
		// in AndminTranslation::modules_translations array
		// see below
		$array_check_duplicate = array();
		foreach ($files as $file)
		{
			if ((preg_match('/^(.*).tpl$/', $file) || preg_match('/^(.*).php$/', $file)) && Tools::file_exists_cache($file_path = $dir.$file))
			{
				// Get content for this file
				$content = file_get_contents($file_path);

				// Module files can now be ignored by adding this string in a file
				if (strpos($content, 'IGNORE_THIS_FILE_FOR_TRANSLATION') !== false)
					continue;

				// Get file type
				$type_file = substr($file, -4) == '.tpl' ? 'tpl' : 'php';

				// Parse this content
				$matches = $this->userParseFile($content, $this->type_selected, $type_file);

				// Write each translation on its module file
				$template_name = substr(basename($file), 0, -4);

				foreach ($matches as $key)
				{
					$module_key = '<{'.Tools::strtolower($module_name).'}'.
						strtolower($is_default ? 'prestashop' : $theme_name).'>'.Tools::strtolower($template_name).'_'.md5($key);
					// to avoid duplicate entry
					if (!in_array($module_key, $array_check_duplicate))
					{
						$array_check_duplicate[] = $module_key;
						if (!isset($this->modules_translations[$theme_name][$module_name][$template_name][$key]['trad']))
							$this->total_expression++;

						if (array_key_exists($module_key, $GLOBALS[$name_var]))
							$this->modules_translations[$theme_name][$module_name][$template_name][$key]['trad'] = html_entity_decode($GLOBALS[$name_var][$module_key], ENT_COMPAT, 'UTF-8');
						else
						{
							$this->modules_translations[$theme_name][$module_name][$template_name][$key]['trad'] = '';
							$this->missing_translations++;
						}
						$this->modules_translations[$theme_name][$module_name][$template_name][$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
					}
				}
			}
		}
	}

	/**
	 * Get list of files which must be parsed by directory and by type of translations
	 *
	 * @return array : list of files by directory
	 */
	public function getFileToParseByTypeTranslation()
	{
		$directories = array();

		switch ($this->type_selected)
		{
			case 'front':
				$directories['tpl'] = array(_PS_ALL_THEMES_DIR_.'/' => scandir(_PS_ALL_THEMES_DIR_));
				self::$ignore_folder[] = 'modules';
				$directories['tpl'] = array_merge($directories['tpl'], $this->listFiles(_PS_THEME_SELECTED_DIR_));

				if (Tools::file_exists_cache(_PS_THEME_OVERRIDE_DIR_))
					$directories['tpl'] = array_merge($directories['tpl'], $this->listFiles(_PS_THEME_OVERRIDE_DIR_));

				break;

			case 'back':
				$directories = array(
					'php' => array(
						_PS_ADMIN_CONTROLLER_DIR_.'/' => scandir(_PS_ADMIN_CONTROLLER_DIR_),
						_PS_OVERRIDE_DIR_.'controllers/admin/' => scandir(_PS_OVERRIDE_DIR_.'controllers/admin/'),
						_PS_CLASS_DIR_.'helper/' => scandir(_PS_CLASS_DIR_.'helper/'),
						_PS_CLASS_DIR_.'controller/' => array('AdminController.php'),
						_PS_CLASS_DIR_ => array('PaymentModule.php')
					),
					'tpl' => $this->listFiles(_PS_ADMIN_DIR_.'/themes/'),
					'specific' => array(
						_PS_ADMIN_DIR_.'/' => array(
							'header.inc.php',
							'footer.inc.php',
							'index.php',
							'login.php',
							'password.php',
							'functions.php'
						)
					)
				);

				// For translate the template which are overridden
				if (file_exists(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'templates'))
					$directories['tpl'] = array_merge($directories['tpl'], $this->listFiles(_PS_OVERRIDE_DIR_.'controllers'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'templates'));

				break;

			case 'errors':
				$directories['php'] = array(
					_PS_ROOT_DIR_.'/' => scandir(_PS_ROOT_DIR_),
					_PS_ADMIN_DIR_.'/' => scandir(_PS_ADMIN_DIR_),
					_PS_FRONT_CONTROLLER_DIR_ => scandir(_PS_FRONT_CONTROLLER_DIR_),
					_PS_ADMIN_CONTROLLER_DIR_ => scandir(_PS_ADMIN_CONTROLLER_DIR_),
					_PS_OVERRIDE_DIR_.'controllers/front/' => scandir(_PS_OVERRIDE_DIR_.'controllers/front/'),
					_PS_OVERRIDE_DIR_.'controllers/admin/' => scandir(_PS_OVERRIDE_DIR_.'controllers/admin/')
				);

				// Get all files for folders classes/ and override/classes/ recursively
				$directories['php'] = array_merge($directories['php'], $this->listFiles(_PS_CLASS_DIR_, array(), 'php'));
				$directories['php'] = array_merge($directories['php'], $this->listFiles(_PS_OVERRIDE_DIR_.'classes/', array(), 'php'));
				break;

			case 'fields':
				$directories['php'] = $this->listFiles(_PS_CLASS_DIR_, array(), 'php');
				break;

			case 'pdf':
				$tpl_theme = Tools::file_exists_cache(_PS_THEME_SELECTED_DIR_.'pdf/') ? scandir(_PS_THEME_SELECTED_DIR_.'pdf/') : array();
				$directories = array(
					'php' => array(
						_PS_CLASS_DIR_.'pdf/' => scandir(_PS_CLASS_DIR_.'pdf/'),
						_PS_OVERRIDE_DIR_.'classes/pdf/' => scandir(_PS_OVERRIDE_DIR_.'classes/pdf/')
					),
					'tpl' => array(
						_PS_PDF_DIR_ => scandir(_PS_PDF_DIR_),
						_PS_THEME_SELECTED_DIR_.'pdf/' => $tpl_theme
					)
				);
				break;

			case 'mails':
				$directories['php'] = array(
					_PS_FRONT_CONTROLLER_DIR_ => scandir(_PS_FRONT_CONTROLLER_DIR_),
					_PS_ADMIN_CONTROLLER_DIR_ => scandir(_PS_ADMIN_CONTROLLER_DIR_),
					_PS_OVERRIDE_DIR_.'controllers/front/' => scandir(_PS_OVERRIDE_DIR_.'controllers/front/'),
					_PS_OVERRIDE_DIR_.'controllers/admin/' => scandir(_PS_OVERRIDE_DIR_.'controllers/admin/'),
					_PS_ADMIN_DIR_.'/' => scandir(_PS_ADMIN_DIR_),
					_PS_ADMIN_DIR_.'/tabs/' => scandir(_PS_ADMIN_DIR_.'/tabs')
				);

				// Get all files for folders classes/ and override/classes/ recursively
				$directories['php'] = array_merge($directories['php'], $this->listFiles(_PS_CLASS_DIR_, array(), 'php'));
				$directories['php'] = array_merge($directories['php'], $this->listFiles(_PS_OVERRIDE_DIR_.'classes/', array(), 'php'));

				$directories['php'] = array_merge($directories['php'], $this->getModulesHasMails());
				break;

		}

		return $directories;
	}

	/**
	 * This method parse a file by type of translation and type file
	 *
	 * @param $content
	 * @param $type_translation : front, back, errors, modules...
	 * @param string|bool $type_file : (tpl|php)
	 * @return return $matches
	 */
	protected function userParseFile($content, $type_translation, $type_file = false)
	{
		switch ($type_translation)
		{
			case 'front':
					// Parsing file in Front office
					$regex = '/\{l\s*s=\''._PS_TRANS_PATTERN_.'\'(\s*sprintf=.*)?(\s*js=1)?\s*\}/U';
				break;

			case 'back':
					// Parsing file in Back office
					if ($type_file == 'php')
						$regex = '/this->l\(\''._PS_TRANS_PATTERN_.'\'[\)|\,]/U';
					else if ($type_file == 'specific')
						$regex = '/translate\(\''._PS_TRANS_PATTERN_.'\'\)/U';
					else
						$regex = '/\{l\s*s\s*=\''._PS_TRANS_PATTERN_.'\'(\s*sprintf=.*)?(\s*js=1)?(\s*slashes=1)?\s*\}/U';
				break;

			case 'errors':
					// Parsing file for all errors syntax
					$regex = '/Tools::displayError\(\''._PS_TRANS_PATTERN_.'\'(,\s*(true|false))?\)/U';
				break;

			case 'modules':
					// Parsing modules file
					if ($type_file == 'php')
						$regex = '/->l\(\''._PS_TRANS_PATTERN_.'\'(, ?\'(.+)\')?(, ?(.+))?\)/U';
					else
						$regex = '/\{l\s*s=\''._PS_TRANS_PATTERN_.'\'(\s*sprintf=.*)?(\s*mod=\'.+\')?(\s*js=1)?\s*\}/U';
				break;

			case 'pdf':
					// Parsing PDF file
					if ($type_file == 'php')
						$regex = '/HTMLTemplate.*::l\(\''._PS_TRANS_PATTERN_.'\'[\)|\,]/U';
					else
						$regex = '/\{l\s*s=\''._PS_TRANS_PATTERN_.'\'(\s*sprintf=.*)?(\s*js=1)?(\s*pdf=\'true\')?\s*\}/U';
				break;
		}

		preg_match_all($regex, $content, $matches);

		return $matches[1];
	}

	/**
	 * Get all translations informations for all type of translations
	 *
	 * array(
	 * 	'type' => array(
	 * 		'name' => string : title for the translation type,
	 * 		'var' => string : name of var for the translation file,
	 * 		'dir' => string : dir of translation file
	 * 		'file' => string : file name of translation file
	 * 	)
	 * )
	 */
	public function getTranslationsInformations()
	{
		$this->translations_informations = array(
			'front' => array(
				'name' => $this->l('Front Office translations'),
				'var' => '_LANG',
				'dir' => _PS_THEME_SELECTED_DIR_.'lang/',
				'file' => $this->lang_selected->iso_code.'.php'
			),
			'back' => array(
				'name' => $this->l('Back Office translations'),
				'var' => '_LANGADM',
				'dir' => _PS_TRANSLATIONS_DIR_.$this->lang_selected->iso_code.'/',
				'file' => 'admin.php'
			),
			'errors' => array(
				'name' => $this->l('Error message translations'),
				'var' => '_ERRORS',
				'dir' => _PS_TRANSLATIONS_DIR_.$this->lang_selected->iso_code.'/',
				'file' => 'errors.php'
			),
			'fields' => array(
				'name' => $this->l('Field name translations'),
				'var' => '_FIELDS',
				'dir' => _PS_TRANSLATIONS_DIR_.$this->lang_selected->iso_code.'/',
				'file' => 'fields.php'
			),
			'modules' => array(
				'name' => $this->l('Installed module translations'),
				'var' => '_MODULES',
				'dir' => _PS_MODULE_DIR_,
				'file' => '',
				'override' => array(
					'dir' => _PS_THEME_SELECTED_DIR_.'modules/',
					'file' => ''
				)
			),
			'pdf' => array(
				'name' => $this->l('PDF translations'),
				'var' => '_LANGPDF',
				'dir' => _PS_TRANSLATIONS_DIR_.$this->lang_selected->iso_code.'/',
				'file' => 'pdf.php',
				'override' => array(
					'dir' => _PS_THEME_SELECTED_DIR_.'pdf/lang/',
					'file' => $this->lang_selected->iso_code.'.php'
				)
			),
			'mails' => array(
				'name' => $this->l('E-mail template translations'),
				'var' => '_LANGMAIL',
				'dir' => _PS_MAIL_DIR_.$this->lang_selected->iso_code.'/',
				'file' => 'lang.php',
				'override' => array(
					'dir' => _PS_THEME_SELECTED_DIR_.'mails/'.$this->lang_selected->iso_code.'/',
					'file' => 'lang.php'
				)
			)
		);
	}

	/**
	 * Get all informations on : languages, theme and the translation type.
	 */
	public function getInformations()
	{
		// Get all Languages
		$this->languages = Language::getLanguages(false);

		// Get all iso_code of languages
		foreach ($this->languages as $language)
			$this->all_iso_lang[] = $language['iso_code'];

		// Get all themes
		$this->themes = Theme::getThemes();

		// Get folder name of theme
		if (($theme = Tools::getValue('theme')) && !is_array($theme))
			$this->theme_selected = Tools::safeOutput($theme);
		else
			$this->theme_selected = self::DEFAULT_THEME_NAME;

		// Set the path of selected theme
		define('_PS_THEME_SELECTED_DIR_', _PS_ROOT_DIR_.'/themes/'.$this->theme_selected.'/');

		// Get type of translation
		if (($type = Tools::getValue('type')) && !is_array($type))
			$this->type_selected = strtolower(Tools::safeOutput($type));

		// Get selected language
		if (Tools::getValue('lang') || Tools::getValue('iso_code'))
		{
			$iso_code = Tools::getValue('lang') ? Tools::getValue('lang') : Tools::getValue('iso_code');

			if (!Validate::isLangIsoCode($iso_code) || !in_array($iso_code, $this->all_iso_lang))
				throw new PrestaShopException(sprintf(Tools::displayError('Invalid iso code "%s"'), $iso_code));

			$this->lang_selected = new Language((int)Language::getIdByIso($iso_code));
		}
		else
			$this->lang_selected = new Language((int)Language::getIdByIso('en'));

		// Get all information for translations
		$this->getTranslationsInformations();
	}


	/**
	 * AdminController::postProcess() override
	 * @see AdminController::postProcess()
	 */
	public function postProcess()
	{
		$this->getInformations();

		/* PrestaShop demo mode */
		if (_PS_MODE_DEMO_)
		{
			$this->errors[] = Tools::displayError('This functionality has been disabled.');
			return;
		}
		/* PrestaShop demo mode */

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
				$this->writeTranslationFile();
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsPdf'))
		{
		 	if ($this->tabAccess['edit'] === '1')
				// Only the PrestaShop team should write the translations into the _PS_TRANSLATIONS_DIR_
				if (($this->theme_selected == self::DEFAULT_THEME_NAME) && _PS_MODE_DEV_)
					$this->writeTranslationFile();
				else
					$this->writeTranslationFile(true);
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsBack'))
		{
		 	if ($this->tabAccess['edit'] === '1')
				$this->writeTranslationFile();
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsErrors'))
		{
		 	if ($this->tabAccess['edit'] === '1')
				$this->writeTranslationFile();
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsFields'))
		{
		 	if ($this->tabAccess['edit'] === '1')
				$this->writeTranslationFile();
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');

		}
		else if (Tools::isSubmit('submitTranslationsMails') || Tools::isSubmit('submitTranslationsMailsAndStay'))
		{
		 	if ($this->tabAccess['edit'] === '1')
		 		$this->submitTranslationsMails();
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
		else if (Tools::isSubmit('submitTranslationsModules'))
		{
			if ($this->tabAccess['edit'] === '1')
			{
				// Get a good path for module directory
				if ($this->theme_selected == self::DEFAULT_THEME_NAME)
					$i18n_dir = $this->translations_informations[$this->type_selected]['dir'];
				else
					$i18n_dir = $this->translations_informations[$this->type_selected]['override']['dir'];

				// Get list of modules
				if ($modules = $this->getListModules())
				{
					// Get files of all modules
					$arr_files = $this->getAllModuleFiles($modules, $i18n_dir, $this->lang_selected->iso_code, true);

					// Find and write all translation modules files
					foreach ($arr_files as $value)
						$this->findAndWriteTranslationsIntoFile($value['file_name'], $value['files'], $value['theme'], $value['module'], $value['dir']);

					// Redirect
					if (Tools::getValue('submitTranslationsModulesAndStay'))
						$this->redirect(true);
					else
						$this->redirect();
				}
			}
			else
				$this->errors[] = Tools::displayError('You do not have permission to edit here.');
		}
	}

	/**
	 * This method redirect in the translation main page or in the translation page
	 *
	 * @param bool $save_and_stay : true if the user has clicked on the button "save and stay"
	 * @param bool $conf : id of confirmation message
	 */
	protected function redirect($save_and_stay = false, $conf = false)
	{
		$conf = !$conf ? 4 : $conf;
		$url_base = self::$currentIndex.'&token='.$this->token.'&conf='.$conf;
		if ($save_and_stay)
			Tools::redirectAdmin($url_base.'&lang='.$this->lang_selected->iso_code.'&type='.$this->type_selected.'&theme='.$this->theme_selected);
		else
			Tools::redirectAdmin($url_base);
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
	 */
	protected function submitTranslationsMails()
	{
		$arr_mail_content = array();
		$arr_mail_path = array();

		if (Tools::getValue('core_mail'))
		{
			$arr_mail_content['core_mail'] = Tools::getValue('core_mail');

			// Get path of directory for find a good path of translation file
			if ($this->theme_selected != self::DEFAULT_THEME_NAME)
				$arr_mail_path['core_mail'] = $this->translations_informations[$this->type_selected]['override']['dir'];
			else
				$arr_mail_path['core_mail'] = $this->translations_informations[$this->type_selected]['dir'];
		}

		if (Tools::getValue('module_mail'))
		{
			$arr_mail_content['module_mail'] = Tools::getValue('module_mail');

			// Get path of directory for find a good path of translation file
			if ($this->theme_selected != self::DEFAULT_THEME_NAME)
				$arr_mail_path['module_mail'] = $this->translations_informations['modules']['override']['dir'].'{module}/mails/'.$this->lang_selected->iso_code.'/';
			else
				$arr_mail_path['module_mail'] = $this->translations_informations['modules']['dir'].'{module}/mails/'.$this->lang_selected->iso_code.'/';
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
						if (!Validate::isModuleName($module_name))
							throw new PrestaShopException(sprinf(Tools::displayError('Invalid module name "%s"'), $module_name));
						$mail_name = substr($mail_name, $module_name_pipe_pos + 1);
						if (!Validate::isTplName($mail_name))
							throw new PrestaShopException(sprintf(Tools::displayError('Invalid mail name "%s"'), $mail_name));
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
					}
					else
						throw new PrestaShopException(Tools::displayError('HTML e-mail templates cannot contain JavaScript code.'));
				}
			}
		}

		// Update subjects
		$array_subjects = array();
		if (($subjects = Tools::getValue('subject')) && is_array($subjects))
		{
			$array_subjects['core_and_modules'] = array('translations'=>array(), 'path'=>$arr_mail_path['core_mail'].'lang.php');

			foreach ($subjects as $subject_translation)
				$array_subjects['core_and_modules']['translations'] = array_merge($array_subjects['core_and_modules']['translations'], $subject_translation);
		}

		if (!empty($array_subjects))
			foreach ($array_subjects as $infos)
				$this->writeSubjectTranslationFile($infos['translations'], $infos['path']);

		if (Tools::isSubmit('submitTranslationsMailsAndStay'))
			$this->redirect(true);
		else
			$this->redirect();
	}

	/**
	 * Include file $dir/$file and return the var $var declared in it.
	 * This create the file if not exists
	 *
	 * return array : translations
	 */
	public function fileExists()
	{
		$var = $this->translations_informations[$this->type_selected]['var'];
		$dir = $this->translations_informations[$this->type_selected]['dir'];
		$file = $this->translations_informations[$this->type_selected]['file'];

		$$var = array();
		if (!Tools::file_exists_cache($dir))
			if (!mkdir($dir, 0700))
				throw new PrestaShopException('Directory '.$dir.' cannot be created.');
		if (!Tools::file_exists_cache($dir.DIRECTORY_SEPARATOR.$file))
			if (!file_put_contents($dir.'/'.$file, "<?php\n\nglobal \$".$var.";\n\$".$var." = array();\n\n?>"))
				throw new PrestaShopException('File "'.$file.'" doesn\'t exists and cannot be created in '.$dir);
		if (!is_writable($dir.DIRECTORY_SEPARATOR.$file))
			$this->displayWarning(Tools::displayError('This file must be writable:').' '.$dir.'/'.$file);
		include($dir.DIRECTORY_SEPARATOR.$file);
		return $$var;
	}

	public function displayToggleButton($closed = false)
	{
		$str_output = '
		<script type="text/javascript">';
		if (Tools::getValue('type') == 'mails')
			$str_output .= '$(document).ready(function(){
				openCloseAllDiv(\''.$this->type_selected.'_div\', this.value == openAll); toggleElemValue(this.id, openAll, closeAll);
				});';
		$str_output .= '
			var openAll = \''.html_entity_decode($this->l('Expand all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
			var closeAll = \''.html_entity_decode($this->l('Close all fieldsets'), ENT_NOQUOTES, 'UTF-8').'\';
		</script>
		<input type="button" class="button" id="buttonall" onclick="openCloseAllDiv(\''.$this->type_selected.'_div\', this.value == openAll); toggleElemValue(this.id, openAll, closeAll);" />
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
					 'tooltip_title' => addslashes(html_entity_decode($this->l('Google Translate suggests:'), ENT_QUOTES, 'utf-8'))
					);
	}

	public function displayLimitPostWarning($count)
	{
		$return = array();
		if ((ini_get('suhosin.post.max_vars') && ini_get('suhosin.post.max_vars') < $count) || (ini_get('suhosin.request.max_vars') && ini_get('suhosin.request.max_vars') < $count))
		{
			$return['error_type'] = 'suhosin';
			$return['post.max_vars'] = ini_get('suhosin.post.max_vars');
			$return['request.max_vars'] = ini_get('suhosin.request.max_vars');
			$return['needed_limit'] = $count + 100;
		}
		elseif (ini_get('max_input_vars') && ini_get('max_input_vars') < $count)
		{
			$return['error_type'] = 'conf';
			$return['max_input_vars'] = ini_get('max_input_vars');
			$return['needed_limit'] = $count + 100;
		}
		return $return;
	}

	/**
	 * Find sentence which use %d, %s, %%, %1$d, %1$s...
	 *
	 * @param $key : english sentence
	 * @return array|bool return list of matches
	 */
	public function checkIfKeyUseSprintf($key)
	{
		if (preg_match_all('#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#', $key, $matches))
			return implode(', ', $matches[0]);
		return false;
	}

	/**
	 * This method generate the form for front translations
	 */
	public function initFormFront()
	{
		$missing_translations_front = array();
		$name_var = $this->translations_informations[$this->type_selected]['var'];
		$GLOBALS[$name_var] = $this->fileExists();

		/* List templates to parse */
		$files_by_directory = $this->getFileToParseByTypeTranslation();
		$count = 0;
		$tabs_array = array();
		foreach ($files_by_directory['tpl'] as $dir => $files)
		{
			$prefix = '';
			if ($dir == _PS_THEME_OVERRIDE_DIR_)
				$prefix = 'override_';

			foreach ($files as $file)
			{
				if (preg_match('/^(.*).tpl$/', $file) && (Tools::file_exists_cache($file_path = $dir.$file)))
				{
					$prefix_key = $prefix.substr(basename($file), 0, -4);
					$new_lang = array();

					// Get content for this file
					$content = file_get_contents($file_path);

					// Parse this content
					$matches = $this->userParseFile($content, $this->type_selected);

					/* Get string translation */
					foreach ($matches as $key)
					{
						if (empty($key))
						{
							$this->errors[] = sprintf($this->l('Empty string found, please edit: "%s"'), $file_path);
							$new_lang[$key] = '';
						}
						else
						{
							// Caution ! front has underscore between prefix key and md5, back has not
							if (isset($GLOBALS[$name_var][$prefix_key.'_'.md5($key)]))
								$new_lang[$key]['trad'] = stripslashes(html_entity_decode($GLOBALS[$name_var][$prefix_key.'_'.md5($key)], ENT_COMPAT, 'UTF-8'));
							else
							{
								if (!isset($new_lang[$key]['trad']))
								{
									$new_lang[$key]['trad'] = '';
									if (!isset($missing_translations_front[$prefix_key]))
										$missing_translations_front[$prefix_key] = 1;
									else
										$missing_translations_front[$prefix_key]++;
								}
							}
							$new_lang[$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
						}
					}

					if (isset($tabs_array[$prefix_key]))
						$tabs_array[$prefix_key] = array_merge($tabs_array[$prefix_key], $new_lang);
					else
						$tabs_array[$prefix_key] = $new_lang;

					$count += count($new_lang);
				}
			}
		}

		$this->tpl_view_vars = array_merge($this->tpl_view_vars, array(
			'missing_translations' => $missing_translations_front,
			'count' => $count,
			'limit_warning' => $this->displayLimitPostWarning($count),
			'tabsArray' => $tabs_array,
		));

		// Add js variables needed for autotranslate
		//$this->tpl_view_vars = array_merge($this->tpl_view_vars, $this->initAutoTranslate());

		$this->initToolbar();
		$this->base_tpl_view = 'translation_form.tpl';
		return parent::renderView();
	}

	/**
	 * This method generate the form for back translations
	 */
	public function initFormBack()
	{
		$name_var = $this->translations_informations[$this->type_selected]['var'];
		$GLOBALS[$name_var] = $this->fileExists();
		$missing_translations_back = array();

		// Get all types of file (PHP, TPL...) and a list of files to parse by folder
		$files_per_directory = $this->getFileToParseByTypeTranslation();

		foreach ($files_per_directory['php'] as $dir => $files)
			foreach ($files as $file)
				// Check if is a PHP file and if the override file exists
				if (preg_match('/^(.*)\.php$/', $file) && Tools::file_exists_cache($file_path = $dir.$file) && !in_array($file, self::$ignore_folder))
				{
					$prefix_key = basename($file);
					// -4 becomes -14 to remove the ending "Controller.php" from the filename
					if (strpos($file, 'Controller.php') !== false)
						$prefix_key = basename(substr($file, 0, -14));
					else if (strpos($file, 'Helper') !== false)
						$prefix_key = 'Helper';

					if ($prefix_key == 'Admin')
						$prefix_key = 'AdminController';

					if ($prefix_key == 'PaymentModule.php')
						$prefix_key = 'PaymentModule';

					// Get content for this file
					$content = file_get_contents($file_path);

					// Parse this content
					$matches = $this->userParseFile($content, $this->type_selected, 'php');

					foreach ($matches as $key)
					{
						// Caution ! front has underscore between prefix key and md5, back has not
						if (isset($GLOBALS[$name_var][$prefix_key.md5($key)]))
							$tabs_array[$prefix_key][$key]['trad'] = stripslashes(html_entity_decode($GLOBALS[$name_var][$prefix_key.md5($key)], ENT_COMPAT, 'UTF-8'));
						else
						{
							if (!isset($tabs_array[$prefix_key][$key]['trad']))
							{
								$tabs_array[$prefix_key][$key]['trad'] = '';
								if (!isset($missing_translations_back[$prefix_key]))
									$missing_translations_back[$prefix_key] = 1;
								else
									$missing_translations_back[$prefix_key]++;
							}
						}
						$tabs_array[$prefix_key][$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
					}
				}

		foreach ($files_per_directory['specific'] as $dir => $files)
			foreach ($files as $file)
				if (Tools::file_exists_cache($file_path = $dir.$file) && !in_array($file, self::$ignore_folder))
				{
					$prefix_key = 'index';

					// Get content for this file
					$content = file_get_contents($file_path);

					// Parse this content
					$matches = $this->userParseFile($content, $this->type_selected, 'specific');

					foreach ($matches as $key)
					{
						// Caution ! front has underscore between prefix key and md5, back has not
						if (isset($GLOBALS[$name_var][$prefix_key.md5($key)]))
							$tabs_array[$prefix_key][$key]['trad'] = stripslashes(html_entity_decode($GLOBALS[$name_var][$prefix_key.md5($key)], ENT_COMPAT, 'UTF-8'));
						else
						{
							if (!isset($tabs_array[$prefix_key][$key]['trad']))
							{
								$tabs_array[$prefix_key][$key]['trad'] = '';
								if (!isset($missing_translations_back[$prefix_key]))
									$missing_translations_back[$prefix_key] = 1;
								else
									$missing_translations_back[$prefix_key]++;
							}
						}
						$tabs_array[$prefix_key][$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
					}
				}

		foreach ($files_per_directory['tpl'] as $dir => $files)
			foreach ($files as $file)
				if (preg_match('/^(.*).tpl$/', $file) && Tools::file_exists_cache($file_path = $dir.$file))
				{
					// get controller name instead of file name
					$prefix_key = Tools::toCamelCase(str_replace(_PS_ADMIN_DIR_.'/themes', '', $file_path), true);
					$pos = strrpos($prefix_key, DIRECTORY_SEPARATOR);
					$tmp = substr($prefix_key, 0, $pos);

					if (preg_match('#controllers#', $tmp))
					{
						$parent_class = explode(DIRECTORY_SEPARATOR, $tmp);
						$key = array_search('controllers', $parent_class);
						$prefix_key = 'Admin'.ucfirst($parent_class[$key + 1]);
					}
					else
						$prefix_key = 'Admin'.ucfirst(substr($tmp, strrpos($tmp, DIRECTORY_SEPARATOR) + 1, $pos));

					// Adding list, form, option in Helper Translations
					$list_prefix_key = array('AdminHelpers', 'AdminList', 'AdminView', 'AdminOptions', 'AdminForm', 'AdminHelpAccess');
					if (in_array($prefix_key, $list_prefix_key))
						$prefix_key = 'Helper';

					// Adding the folder backup/download/ in AdminBackup Translations
					if ($prefix_key == 'AdminDownload')
						$prefix_key = 'AdminBackup';

					// use the prefix "AdminController" (like old php files 'header', 'footer.inc', 'index', 'login', 'password', 'functions'
					if ($prefix_key == 'Admin' || $prefix_key == 'AdminTemplate')
						$prefix_key = 'AdminController';

					$new_lang = array();

					// Get content for this file
					$content = file_get_contents($file_path);

					// Parse this content
					$matches = $this->userParseFile($content, $this->type_selected, 'tpl');

					/* Get string translation for each tpl file */
					foreach ($matches as $english_string)
					{
						if (empty($english_string))
						{
							$this->errors[] = sprintf($this->l('Error in template - Empty string found, please edit: "%s"'), $file_path);
							$new_lang[$english_string] = '';
						}
						else
						{
							$trans_key = $prefix_key.md5($english_string);

							if (isset($GLOBALS[$name_var][$trans_key]))
								$new_lang[$english_string]['trad'] = html_entity_decode($GLOBALS[$name_var][$trans_key], ENT_COMPAT, 'UTF-8');
							else
							{
								if (!isset($new_lang[$english_string]['trad']))
								{
									$new_lang[$english_string]['trad'] = '';
									if (!isset($missing_translations_back[$prefix_key]))
										$missing_translations_back[$prefix_key] = 1;
									else
										$missing_translations_back[$prefix_key]++;
								}
							}
							$new_lang[$english_string]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
						}
					}
					if (isset($tabs_array[$prefix_key]))
						$tabs_array[$prefix_key] = array_merge($tabs_array[$prefix_key], $new_lang);
					else
						$tabs_array[$prefix_key] = $new_lang;
				}


		// count will contain the number of expressions of the page
		$count = 0;
		foreach ($tabs_array as $array)
			$count += count($array);

		$this->tpl_view_vars = array_merge($this->tpl_view_vars, array(
			'count' => $count,
			'limit_warning' => $this->displayLimitPostWarning($count),
			'tabsArray' => $tabs_array,
			'missing_translations' => $missing_translations_back
		));

		// Add js variables needed for autotranslate
		//$this->tpl_view_vars = array_merge($this->tpl_view_vars, $this->initAutoTranslate());

		$this->initToolbar();
		$this->base_tpl_view = 'translation_form.tpl';
		return parent::renderView();
	}

	/**
	 * Check if directory and file exist and return an list of modules
	 *
	 * @return array : list of modules
	 */
	public function getListModules()
	{
		if (!Tools::file_exists_cache($this->translations_informations['modules']['dir']))
			throw new PrestaShopException(Tools::displayError('Fatal error: Module directory does not exist').'('.$this->translations_informations['modules']['dir'].')');
		if (!is_writable($this->translations_informations['modules']['dir']))
			throw new PrestaShopException(Tools::displayError('The module directory must be writable'));

		$modules = array();
		if (!_PS_MODE_DEV_ && $this->theme_selected == self::DEFAULT_THEME_NAME)
		{
			// Get all module which are installed for to have a minimum of POST
			$modules = Module::getModulesInstalled();

			foreach ($modules as &$module)
				$module = $module['name'];
		}
		else if ($this->theme_selected == self::DEFAULT_THEME_NAME)
			if (Tools::file_exists_cache($this->translations_informations['modules']['dir']))
				$modules = scandir($this->translations_informations['modules']['dir']);
			else
				$this->displayWarning(Tools::displayError('There are no modules in your copy of PrestaShop. Use the Modules page to activate them or go to our Website to download additional Modules.'));
		else
			if (Tools::file_exists_cache($this->translations_informations['modules']['override']['dir']))
				$modules = scandir($this->translations_informations['modules']['override']['dir']);
			else
				$this->displayWarning(Tools::displayError('There are no modules in your copy of PrestaShop. Use the Modules page to activate them or go to our Website to download additional Modules.'));

		return $modules;
	}

	/**
	 * This method generate the form for errors translations
	 */
	public function initFormErrors()
	{
		$name_var = $this->translations_informations[$this->type_selected]['var'];
		$GLOBALS[$name_var] = $this->fileExists();
		$count_empty = array();

		/* List files to parse */
		$string_to_translate = array();
		$file_by_directory = $this->getFileToParseByTypeTranslation();

		if ($modules = $this->getListModules())
		{
			foreach ($modules as $module)
				if (is_dir(_PS_MODULE_DIR_.$module) && !in_array($module, self::$ignore_folder))
					$file_by_directory['php'] = array_merge($file_by_directory['php'], $this->listFiles(_PS_MODULE_DIR_.$module.'/', array(), 'php'));
		}

		foreach ($file_by_directory['php'] as $dir => $files)
			foreach ($files as $file)
				if (preg_match('/\.php$/', $file) && Tools::file_exists_cache($file_path = $dir.$file) && !in_array($file, self::$ignore_folder))
				{
					if (!filesize($file_path))
						continue;

					// Get content for this file
					$content = file_get_contents($file_path);

					// Parse this content
					$matches = $this->userParseFile($content, $this->type_selected);

					foreach ($matches as $key)
					{
						if (array_key_exists(md5($key), $GLOBALS[$name_var]))
							$string_to_translate[$key]['trad'] = html_entity_decode($GLOBALS[$name_var][md5($key)], ENT_COMPAT, 'UTF-8');
						else
						{
							$string_to_translate[$key]['trad'] = '';
							if (!isset($count_empty[$key]))
								$count_empty[$key] = 1;
						}
						$string_to_translate[$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
					}
				}

		$this->tpl_view_vars = array_merge($this->tpl_view_vars, array(
			'count' => count($string_to_translate),
			'limit_warning' => $this->displayLimitPostWarning(count($string_to_translate)),
			'errorsArray' => $string_to_translate,
			'missing_translations' => $count_empty
		));

		$this->initToolbar();
		$this->base_tpl_view = 'translation_errors.tpl';
		return parent::renderView();
	}

	/**
	 * This method generate the form for fields translations
	 */
	public function initFormFields()
	{
		$name_var = $this->translations_informations[$this->type_selected]['var'];
		$GLOBALS[$name_var] = $this->fileExists();
		$missing_translations_fields = array();
		$class_array = array();
		$tabs_array = array();
		$count = 0;

		$files_by_directory = $this->getFileToParseByTypeTranslation();

		foreach ($files_by_directory['php'] as $dir => $files)
			foreach ($files as $file)
			{
				if (!preg_match('/\.php$/', $file) || $file == 'index.php')
					continue;
				include_once($dir.$file);
				$prefix_key = substr($file, 0, -4);
				if (!class_exists($prefix_key))
					continue;
				if (!is_subclass_of($prefix_key, 'ObjectModel'))
					continue;
				$class_array[$prefix_key] = call_user_func(array($prefix_key, 'getValidationRules'), $prefix_key);
			}

		foreach ($class_array as $prefix_key => $rules)
		{
			if (isset($rules['validate']))
				foreach ($rules['validate'] as $key => $value)
				{
					if (isset($GLOBALS[$name_var][$prefix_key.'_'.md5($key)]))
					{
						$tabs_array[$prefix_key][$key]['trad'] = html_entity_decode($GLOBALS[$name_var][$prefix_key.'_'.md5($key)], ENT_COMPAT, 'UTF-8');
						$count++;
					}
					else
					{
						if (!isset($tabs_array[$prefix_key][$key]['trad']))
						{
							$tabs_array[$prefix_key][$key]['trad'] = '';
							if (!isset($missing_translations_fields[$prefix_key]))
								$missing_translations_fields[$prefix_key] = 1;
							else
								$missing_translations_fields[$prefix_key]++;
							$count++;
						}
					}
				}
			if (isset($rules['validateLang']))
				foreach ($rules['validateLang'] as $key => $value)
				{
					if (isset($GLOBALS[$name_var][$prefix_key.'_'.md5($key)]))
					{
						$tabs_array[$prefix_key][$key]['trad'] = '';
						if (array_key_exists($prefix_key.'_'.md5(addslashes($key)), $GLOBALS[$name_var]))
							$tabs_array[$prefix_key][$key]['trad'] = html_entity_decode($GLOBALS[$name_var][$prefix_key.'_'.md5(addslashes($key))], ENT_COMPAT, 'UTF-8');

						$count++;
					}
					else
					{
						if (!isset($tabs_array[$prefix_key][$key]['trad']))
						{
							$tabs_array[$prefix_key][$key]['trad'] = '';
							if (!isset($missing_translations_fields[$prefix_key]))
								$missing_translations_fields[$prefix_key] = 1;
							else
								$missing_translations_fields[$prefix_key]++;
							$count++;
						}
					}
				}
		}

		$this->tpl_view_vars = array_merge($this->tpl_view_vars, array(
			'count' => $count,
			'limit_warning' => $this->displayLimitPostWarning($count),
			'tabsArray' => $tabs_array,
			'missing_translations' => $missing_translations_fields
		));

		$this->initToolbar();
		$this->base_tpl_view = 'translation_form.tpl';
		return parent::renderView();
	}

	/**
	 * Get each informations for each mails founded in the folder $dir.
	 *
	 * @since 1.4.0.14
	 * @param string $dir
	 * @param string $group_name
	 * @return array : list of mails
	 */
	public function getMailFiles($dir, $group_name = 'mail')
	{
		$arr_return = array();

		// Very usefull to name input and textarea fields
		$arr_return['group_name'] = $group_name;
		$arr_return['empty_values'] = 0;
		$arr_return['total_filled'] = 0;
		$arr_return['directory'] = $dir;

		// Get path for english mail directory
		$dir_en = str_replace('/'.$this->lang_selected->iso_code.'/', '/en/', $dir);

		if (Tools::file_exists_cache($dir_en))
		{
			// Get all english files to compare with the language to translate
			foreach (scandir($dir_en) as $email_file)
			{
				if (strripos($email_file, '.html') > 0 || strripos($email_file, '.txt') > 0)
				{
					$email_name = substr($email_file, 0, strripos($email_file, '.'));
					$type = substr($email_file, strripos($email_file, '.') + 1);
					if (!isset($arr_return['files'][$email_name]))
						$arr_return['files'][$email_name] = array();
					// $email_file is from scandir ($dir), so we already know that file exists
					$arr_return['files'][$email_name][$type]['en'] = $this->getMailContent($dir_en, $email_file);

					// check if the file exists in the language to translate
					if (Tools::file_exists_cache($dir.'/'.$email_file))
					{
						$arr_return['files'][$email_name][$type][$this->lang_selected->iso_code] = $this->getMailContent($dir, $email_file);
						$this->total_expression++;
					}
					else
						$arr_return['files'][$email_name][$type][$this->lang_selected->iso_code] = '';

					if ($arr_return['files'][$email_name][$type][$this->lang_selected->iso_code] == '')
						$arr_return['empty_values']++;
					else
						$arr_return['total_filled']++;
				}
			}
		}
		else
			$this->warnings[] = sprintf(Tools::displayError('mail directory exists for %1$s but not for english in %2$s'),
				$this->lang_selected->iso_code, str_replace(_PS_ROOT_DIR_, '', $dir));
		return $arr_return;
	}

	/**
	 * Get content of the mail file.
	 *
	 * @since 1.4.0.14
	 * @param string $dir
	 * @param string $file
	 * @return array : content of file
	 */
	protected function getMailContent($dir, $file)
	{
		$content = file_get_contents($dir.'/'.$file);

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
		if (array_key_exists('group_name', $mails))
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
				if (array_key_exists('html', $mail_files) || array_key_exists('txt', $mail_files))
				{
					if (array_key_exists($mail_name, $all_subject_mail))
					{
						$subject_mail = $all_subject_mail[$mail_name];
						$value_subject_mail = isset($mails['subject'][$subject_mail]) ? $mails['subject'][$subject_mail] : '';
						$str_return .= '
						<div class="label-subject" style="text-align:center;">
							<label style="text-align:right">'.sprintf($this->l('Subject for %s:'), '<em>'.$mail_name.'</em>').'</label>
							<div class="mail-form" style="text-align:left">
								<b>'.$subject_mail.'</b><br />';
								if (isset($value_subject_mail['trad']) && $value_subject_mail['trad'])
									$str_return .= '<input type="text" name="subject['.$group_name.']['.$subject_mail.']" value="'.$value_subject_mail['trad'].'" />';
								else
									$str_return .= '<input type="text" name="subject['.$group_name.']['.$subject_mail.']" value="" />';

								if (isset($value_subject_mail['use_sprintf']) && $value_subject_mail['use_sprintf'])
								{
									$str_return .= '<a class="useSpecialSyntax" title="'.$this->l('This expression uses a special syntax:').' '.$value_subject_mail['use_sprintf'].'" style="cursor:pointer">
										<img src="'._PS_IMG_.'admin/error.png" alt="'.$value_subject_mail['use_sprintf'].'" />
									</a>';
								}
							$str_return .= '</div>
						</div>';
					}
					else
					{
						$str_return .= '
						<div class="label-subject">
							<b>'.sprintf($this->l('No Subject was found for %s, or subject is generated in database.'), '<em>'.$mail_name.'</em>').'</b>
						</div>';
					}
					if (array_key_exists('html', $mail_files))
					{
						$base_uri = str_replace(_PS_ROOT_DIR_, __PS_BASE_URI__, $mails['directory']);
						$base_uri = str_replace('//', '/', $base_uri);
						$url_mail = $base_uri.$mail_name.'.html';
						$str_return .= $this->displayMailBlockHtml($mail_files['html'], $obj_lang->iso_code, $url_mail, $mail_name, $group_name, $name_for_module);
					}
					if (array_key_exists('txt', $mail_files))
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
		$name_for_module = $name_for_module ? $name_for_module.'|' : '';
		$content[$lang] = (isset($content[$lang]) ? Tools::htmlentitiesUTF8(stripslashes($content[$lang])) : '');
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
				<iframe style="background:white;border:1px solid #DFD5C3;" border="0" src ="'.$url.'?'.(rand(0, 1000000000)).'" width="565" height="497"></iframe>
					<a style="display:block;margin-top:5px;width:130px;" href="#" onclick="$(this).parent().hide(); displayTiny($(this).parent().next()); return false;" class="button">'.$this->l('Edit this e-mail template').'</a>
				</div>
				<textarea style="display:none;" class="rte mailrte" cols="80" rows="30" name="'.$group_name.'[html]['.$name_for_module.$mail_name.']">'.$content[$lang].'</textarea>
			</div><!-- .mail-form -->
		</div><!-- .block-mail -->';
		return $str_return;
	}

	/**
	 * Check in each module if contains mails folder.
	 *
	 * @return array of module which has mails
	 */
	public function getModulesHasMails($with_module_name = false)
	{
		if ($this->theme_selected != self::DEFAULT_THEME_NAME)
			$i18n_dir = $this->translations_informations['modules']['override']['dir'];
		else
			$i18n_dir = $this->translations_informations['modules']['dir'];

		$arr_modules = array();
		foreach (scandir($i18n_dir) as $module_dir)
		{
			$dir = $i18n_dir.$module_dir.'/';
			if (!in_array($module_dir, self::$ignore_folder) && Tools::file_exists_cache($dir.'mails/'))
				if ($with_module_name)
					$arr_modules[$module_dir] = $dir;
				else
					$arr_modules[$dir] = scandir($dir);
		}
		return $arr_modules;
	}

	protected function getTinyMCEForMails($iso_lang)
	{
		// TinyMCE
		$iso_tiny_mce = (Tools::file_exists_cache(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso_lang.'.js') ? $iso_lang : 'en');
		$ad = dirname($_SERVER['PHP_SELF']);
		return '
			<script type="text/javascript">
			var iso = \''.$iso_tiny_mce.'\' ;
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

	/**
	 * This method generate the form for mails translations
	 */
	public function initFormMails($no_display = false)
	{
		$module_mails = array();

		// get all mail subjects, this method parse each files in Prestashop !!
		$subject_mail = array();

		$modules_has_mails = $this->getModulesHasMails(true);

		$files_by_directiories = $this->getFileToParseByTypeTranslation();

		foreach ($files_by_directiories['php'] as $dir => $files)
			foreach ($files as $file)
				if (Tools::file_exists_cache($dir.$file) && is_file($dir.$file) && !in_array($file, self::$ignore_folder) && preg_match('/\.php$/', $file))
					$subject_mail = $this->getSubjectMail($dir, $file, $subject_mail);

		// Get path of directory for find a good path of translation file
		if ($this->theme_selected != self::DEFAULT_THEME_NAME && @filemtime($this->translations_informations[$this->type_selected]['override']['dir']))
			$i18n_dir = $this->translations_informations[$this->type_selected]['override']['dir'];
		else
			$i18n_dir = $this->translations_informations[$this->type_selected]['dir'];

		$core_mails = $this->getMailFiles($i18n_dir, 'core_mail');
		$core_mails['subject'] = $this->getSubjectMailContent($i18n_dir);

		foreach ($modules_has_mails as $module_name => $module_path)
		{
			$module_mails[$module_name] = $this->getMailFiles($module_path.'mails/'.$this->lang_selected->iso_code.'/', 'module_mail');
			$module_mails[$module_name]['subject'] = $core_mails['subject'];
			$module_mails[$module_name]['display'] = $this->displayMailContent($module_mails[$module_name], $subject_mail, $this->lang_selected, Tools::strtolower($module_name), sprintf($this->l('E-mails for %s module'), '<em>'.$module_name.'</em>'), $module_name);
		}

		if ($no_display)
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
			return array('total' => $total, 'empty' => $empty);
		}

		$this->tpl_view_vars = array_merge($this->tpl_view_vars, array(
			'limit_warning' => $this->displayLimitPostWarning($this->total_expression),
			'tinyMCE' => $this->getTinyMCEForMails($this->lang_selected->iso_code),
			'mail_content' => $this->displayMailContent($core_mails, $subject_mail, $this->lang_selected, 'core', $this->l('Core e-mails')),
			'module_mails' => $module_mails,
			'theme_name' => $this->theme_selected
		));

		$this->initToolbar();
		$this->base_tpl_view = 'translation_mails.tpl';
		return parent::renderView();
	}

	/**
	 * Get list of subjects of mails
	 *
	 * @param $dir
	 * @param $file
	 * @param $subject_mail
	 * @return array : list of subjects of mails
	 */
	protected function getSubjectMail($dir, $file, $subject_mail)
	{
		$content = file_get_contents($dir.'/'.$file);
		$content = str_replace("\n", ' ', $content);

		if (preg_match_all('/Mail::Send([^;]*);/si', $content, $tab))
			for ($i = 0; isset($tab[1][$i]); $i++)
			{
				$tab2 = explode(',', $tab[1][$i]);
				if (is_array($tab2))
					if ($tab2 && isset($tab2[1]))
					{
						$tab2[1] = trim(str_replace('\'', '', $tab2[1]));
						if (preg_match('/Mail::l\(\''._PS_TRANS_PATTERN_.'\'/s', $tab2[2], $matches))
							$subject_mail[$tab2[1]] = $matches[1];
					}
			}

		if (!in_array($file, self::$ignore_folder) && is_dir($dir.'/'.$file))
			 $subject_mail = $this->getSubjectMail($dir, $file, $subject_mail);

		return $subject_mail;
	}

	/**
	 * @param $directory : name of directory
	 * @return array
	 */
	protected function getSubjectMailContent($directory)
	{
		$subject_mail_content = array();

		if (Tools::file_exists_cache($directory.'/lang.php'))
		{
			// we need to include this even if already included (no include once)
			include($directory.'/lang.php');
			foreach ($GLOBALS[$this->translations_informations[$this->type_selected]['var']] as $key => $subject)
			{
				$this->total_expression++;
				$subject = str_replace('\n', ' ', $subject);
				$subject = str_replace("\\'", "\'", $subject);

				$subject_mail_content[$key]['trad'] = htmlentities($subject, ENT_QUOTES, 'UTF-8');
				$subject_mail_content[$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
			}
		}
		else
			$this->errors[] = sprintf($this->l('Subject mail translation file not found in "%s"'), $directory);
		return $subject_mail_content;
	}

	protected function writeSubjectTranslationFile($sub, $path)
	{
		if ($fd = @fopen($path, 'w'))
		{
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
			throw new PrestaShopException(sprintf(Tools::displayError('Cannot write language file for e-mail subjects, path is: %s'), $path));
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
		if (Tools::file_exists_cache($path))
			$files_module = scandir($path);
		$files_for_module = $this->clearModuleFiles($files_module, 'file');
		if (!empty($files_for_module))
			$array_files[] = array(
				'file_name'		=> $lang_file,
				'dir'			=> $path,
				'files'			=> $files_for_module,
				'module'		=> $module_name,
				'is_default'	=> $is_default,
				'theme'			=> $this->theme_selected,
			);

		$dir_module = $this->clearModuleFiles($files_module, 'directory', $path);

		if (!empty($dir_module))
			foreach ($dir_module as $folder)
				$this->recursiveGetModuleFiles($path.$folder.'/', $array_files, $module_name, $lang_file, $is_default);
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
				if (Tools::file_exists_cache($root_dir.$module.'/translations/'.$lang.'.php'))
					$lang_file = $root_dir.$module.'/translations/'.$lang.'.php';
				else
					$lang_file = $root_dir.$module.'/'.$lang.'.php';
				@include($lang_file);

				$this->getModuleTranslations();
				$this->recursiveGetModuleFiles($root_dir.$module.'/', $array_files, $module, $lang_file, $is_default);
			}
		}
		return $array_files;
	}

	/**
	 * This method generate the form for modules translations
	 */
	public function initFormModules()
	{
		// Get path of directory for find a good path of translation file
		if ($this->theme_selected != self::DEFAULT_THEME_NAME)
			$i18n_dir = $this->translations_informations[$this->type_selected]['override']['dir'];
		else
			$i18n_dir = $this->translations_informations[$this->type_selected]['dir'];

		// Get list of modules
		$modules = $this->getListModules();

		if (!empty($modules))
		{
			// Get all modules files and include all translation files
			$arr_files = $this->getAllModuleFiles($modules, $i18n_dir, $this->lang_selected->iso_code, true);

			foreach ($arr_files as $value)
				$this->findAndFillTranslations($value['files'], $value['theme'], $value['module'], $value['dir']);

			$this->tpl_view_vars = array_merge($this->tpl_view_vars, array(
				'default_theme_name' => self::DEFAULT_THEME_NAME,
				'count' => $this->total_expression,
				'limit_warning' => $this->displayLimitPostWarning($this->total_expression),
				'textarea_sized' => TEXTAREA_SIZED,
				'modules_translations' => isset($this->modules_translations) ? $this->modules_translations : array(),
				'missing_translations' => $this->missing_translations
			));

			$this->initToolbar();
			$this->base_tpl_view = 'translation_modules.tpl';
			return parent::renderView();
		}
	}

	/** Parse PDF class
	 *
	 * @param string $file_path file to parse
	 * @param string $file_type type of file
	 * @param array $langArray contains expression in the chosen language
	 * @param string $tab name to use with the md5 key
	 * @param array $tabs_array
	 * @return array containing all datas needed for building the translation form
	 * @since 1.4.5.0
	 */
	protected function parsePdfClass($file_path, $file_type, $lang_array, $tab, $tabs_array, &$count_missing)
	{
		// Get content for this file
		$content = file_get_contents($file_path);

		// Parse this content
		$matches = $this->userParseFile($content, $this->type_selected, $file_type);

		foreach ($matches as $key)
		{
			if (stripslashes(array_key_exists($tab.md5(addslashes($key)), $lang_array)))
				$tabs_array[$tab][$key]['trad'] = html_entity_decode($lang_array[$tab.md5(addslashes($key))], ENT_COMPAT, 'UTF-8');
			else
			{
				$tabs_array[$tab][$key]['trad'] = '';
				if (!isset($count_missing[$tab]))
					$count_missing[$tab] = 1;
				else
					$count_missing[$tab]++;
			}
			$tabs_array[$tab][$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
		}

		return $tabs_array;
	}

	/**
	 * This method generate the form for PDF translations
	 */
	public function initFormPDF()
	{
		$name_var = $this->translations_informations[$this->type_selected]['var'];
		$GLOBALS[$name_var] = array();
		$missing_translations_pdf = array();

		$i18n_dir = $this->translations_informations[$this->type_selected]['dir'];
		$default_i18n_file = $i18n_dir.$this->translations_informations[$this->type_selected]['file'];

		if (($this->theme_selected == self::DEFAULT_THEME_NAME) || _PS_MODE_DEV_)
			$i18n_file = $default_i18n_file;
		else
		{
			$i18n_dir = $this->translations_informations[$this->type_selected]['override']['dir'];
			$i18n_file = $i18n_dir.$this->translations_informations[$this->type_selected]['override']['file'];
		}

		$this->checkDirAndCreate($i18n_file);
		if (!file_exists($i18n_file))
			$this->errors[] = sprintf(Tools::displayError('Please create a "%1$s.php" file in "%2$s"'), $this->lang_selected->iso_code, $i18n_dir);

		if (!is_writable($i18n_file))
			$this->errors[] = sprintf(Tools::displayError('Cannot write into the "%s"'), $i18n_file);

		@include($i18n_file);

		// if the override's translation file is empty load the default file
		if (!isset($GLOBALS[$name_var]) || count($GLOBALS[$name_var]) == 0)
			@include($default_i18n_file);

		$prefix_key = 'PDF';
		$tabs_array = array($prefix_key => array());

		$files_by_directory = $this->getFileToParseByTypeTranslation();

		foreach ($files_by_directory as $type => $directories)
			foreach ($directories as $dir => $files)
				foreach ($files as $file)
					if (!in_array($file, self::$ignore_folder) && Tools::file_exists_cache($file_path = $dir.$file))
					{
						if ($type == 'tpl')
						{
							if (Tools::file_exists_cache($file_path) && is_file($file_path))
							{
								// Get content for this file
								$content = file_get_contents($file_path);

								// Parse this content
								$matches = $this->userParseFile($content, $this->type_selected, 'tpl');

								foreach ($matches as $key)
								{
									if (isset($GLOBALS[$name_var][$prefix_key.md5($key)]))
										$tabs_array[$prefix_key][$key]['trad'] = (html_entity_decode($GLOBALS[$name_var][$prefix_key.md5($key)], ENT_COMPAT, 'UTF-8'));
									else
									{
										if (!isset($tabs_array[$prefix_key][$key]['trad']))
										{
											$tabs_array[$prefix_key][$key]['trad'] = '';
											if (!isset($missing_translations_pdf[$prefix_key]))
												$missing_translations_pdf[$prefix_key] = 1;
											else
												$missing_translations_pdf[$prefix_key]++;
										}
									}
									$tabs_array[$prefix_key][$key]['use_sprintf'] = $this->checkIfKeyUseSprintf($key);
								}
							}
						}
						else
							if (Tools::file_exists_cache($file_path))
								$tabs_array = $this->parsePdfClass($file_path, 'php', $GLOBALS[$name_var], $prefix_key, $tabs_array, $missing_translations_pdf);
					}

		$this->tpl_view_vars = array_merge($this->tpl_view_vars, array(
			'count' => count($tabs_array['PDF']),
			'limit_warning' => $this->displayLimitPostWarning(count($tabs_array['PDF'])),
			'tabsArray' => $tabs_array,
			'missing_translations' => $missing_translations_pdf
		));

		$this->initToolbar();
		$this->base_tpl_view = 'translation_form.tpl';
		return parent::renderView();
	}

	/**
	 * recursively list files in directory $dir
	 */
	public function listFiles($dir, $list = array(), $file_ext = 'tpl')
	{
		$dir = rtrim($dir, '/').DIRECTORY_SEPARATOR;

		$to_parse = scandir($dir);
		// copied (and kind of) adapted from AdminImages.php
		foreach ($to_parse as $file)
		{
			if (!in_array($file, self::$ignore_folder))
			{
				if (preg_match('#'.preg_quote($file_ext, '#').'$#i', $file))
					$list[$dir][] = $file;
				else if (is_dir($dir.$file))
					$list = $this->listFiles($dir.$file, $list, $file_ext);
			}
		}
		return $list;
	}

}
