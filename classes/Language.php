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
*  @version  Release: $Revision: 7040 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class LanguageCore extends ObjectModel
{
	public $id;

	/** @var string Name */
	public $name;

	/** @var string 2-letter iso code */
	public $iso_code;

	/** @var string 5-letter iso code */
	public $language_code;

	/** @var string date format http://http://php.net/manual/en/function.date.php with the date only */
	public $date_format_lite = 'Y-m-d';

	/** @var string date format http://http://php.net/manual/en/function.date.php with hours and minutes */
	public $date_format_full = 'Y-m-d H:i:s';

	/** @var bool true if this language is right to left language */
	public $is_rtl = false;

	/** @var boolean Status */
	public $active = true;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'lang',
		'primary' => 'id_lang',
		'fields' => array(
			'name' => 				array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 32),
			'iso_code' => 			array('type' => self::TYPE_STRING, 'validate' => 'isLanguageIsoCode', 'required' => true, 'size' => 2),
			'language_code' => 		array('type' => self::TYPE_STRING, 'validate' => 'isLanguageCode', 'size' => 5),
			'active' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'is_rtl' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_format_lite' => 	array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'required' => true, 'size' => 32),
			'date_format_full' => 	array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat', 'required' => true, 'size' => 32),
		),
	);


	/** @var array Languages cache */
	protected static $_checkedLangs;
	protected static $_LANGUAGES;
	protected static $countActiveLanguages;

	protected	$webserviceParameters = array(
		'objectNodeName' => 'language',
		'objectsNodeName' => 'languages',
	);

	protected $translationsFilesAndVars = array(
			'fields' => '_FIELDS',
			'errors' => '_ERRORS',
			'admin' => '_LANGADM',
			'pdf' => '_LANGPDF',
			'tabs' => 'tabs',
		);

	public	function __construct($id = null, $id_lang = null)
	{
		parent::__construct($id);
	}

	/**
	 * @see ObjectModel::getFields()
	 * @return array
	 */
	public function getFields()
	{
		if (empty($this->language_code))
			$this->language_code = $this->iso_code;

		return parent::getFields();
	}

	/**
	 * Generate translations files
	 *
	 */
	protected function _generateFiles($newIso = null)
	{
		$iso_code = $newIso ? $newIso : $this->iso_code;

		if (!file_exists(_PS_TRANSLATIONS_DIR_.$iso_code))
			mkdir(_PS_TRANSLATIONS_DIR_.$iso_code);
		foreach ($this->translationsFilesAndVars as $file => $var)
		{
			$path_file = _PS_TRANSLATIONS_DIR_.$iso_code.'/'.$file.'.php';
			if (!file_exists($path_file))
				if ($file != 'tabs')
					file_put_contents($path_file, '<?php
	global $'.$var.';
	$'.$var.' = array();
?>');
				else
					file_put_contents($path_file, '<?php
	$'.$var.' = array();
	return $'.$var.';
?>');

			@chmod($path_file, 0777);
		}
	}

	/**
	 * Move translations files after editiing language iso code
	 */
	public function moveToIso($newIso)
	{
		if ($newIso == $this->iso_code)
			return true;

		if (file_exists(_PS_TRANSLATIONS_DIR_.$this->iso_code))
			rename(_PS_TRANSLATIONS_DIR_.$this->iso_code, _PS_TRANSLATIONS_DIR_.$newIso);

		if (file_exists(_PS_MAIL_DIR_.$this->iso_code))
			rename(_PS_MAIL_DIR_.$this->iso_code, _PS_MAIL_DIR_.$newIso);

		$modulesList = Module::getModulesDirOnDisk();
		foreach ($modulesList as $moduleDir)
		{
			if (file_exists(_PS_MODULE_DIR_.$moduleDir.'/mails/'.$this->iso_code))
				rename(_PS_MODULE_DIR_.$moduleDir.'/mails/'.$this->iso_code, _PS_MODULE_DIR_.$moduleDir.'/mails/'.$newIso);

			if (file_exists(_PS_MODULE_DIR_.$moduleDir.'/'.$this->iso_code.'.php'))
				rename(_PS_MODULE_DIR_.$moduleDir.'/'.$this->iso_code.'.php', _PS_MODULE_DIR_.$moduleDir.'/'.$newIso.'.php');
		}

		foreach (Theme::getThemes() as $theme)
		{
			$theme_dir = $theme->directory;
			if (file_exists(_PS_ALL_THEMES_DIR_.$theme_dir.'/lang/'.$this->iso_code.'.php'))
				rename(_PS_ALL_THEMES_DIR_.$theme_dir.'/lang/'.$this->iso_code.'.php', _PS_ALL_THEMES_DIR_.$theme_dir.'/lang/'.$newIso.'.php');

			if (file_exists(_PS_ALL_THEMES_DIR_.$theme_dir.'/mails/'.$this->iso_code))
				rename(_PS_ALL_THEMES_DIR_.$theme_dir.'/mails/'.$this->iso_code, _PS_ALL_THEMES_DIR_.$theme_dir.'/mails/'.$newIso);

			foreach ($modulesList as $module)
				if (file_exists(_PS_ALL_THEMES_DIR_.$theme_dir.'/modules/'.$module.'/'.$this->iso_code.'.php'))
					rename(_PS_ALL_THEMES_DIR_.$theme_dir.'/modules/'.$module.'/'.$this->iso_code.'.php', _PS_ALL_THEMES_DIR_.$theme_dir.'/modules/'.$module.'/'.$newIso.'.php');
		}
	}

	/**
	  * Return an array of theme 
	  *
	  * @return array([theme dir] => array('name' => [theme name]))
		* @deprecated will be removed in 1.6
	  */
	protected function _getThemesList()
	{
		Tools::displayAsDeprecated();
		static $themes = array();

		if (empty($themes))
		{
			$installed_themes = Theme::getThemes();
			foreach ($installed_themes as $theme)
				$themes[$theme->directory] = array('name' => $theme->name);
		}
		return $themes;
	}

	public function add($autodate = true, $nullValues = false)
	{
		if (!parent::add($autodate))
			return false;

		// create empty files if they not exists
		$this->_generateFiles();

		// @todo Since a lot of modules are not in right format with their primary keys name, just get true ...
		$resUpdateSQL = $this->loadUpdateSQL();
		$resUpdateSQL = true;

		return $resUpdateSQL && Tools::generateHtaccess();
	}

	public function toggleStatus()
	{
		if (!parent::toggleStatus())
			return false;

		return Tools::generateHtaccess();
	}

	public function checkFiles()
	{
		return Language::checkFilesWithIsoCode($this->iso_code);
	}


	/**
	 * This functions checks if every files exists for the language $iso_code.
	 * Concerned files are those located in translations/$iso_code/
	 * and translations/mails/$iso_code .
	 *
	 * @param mixed $iso_code
	 * @returntrue if all files exists
	 */
	public static function checkFilesWithIsoCode($iso_code)
	{
		if (isset(self::$_checkedLangs[$iso_code]) && self::$_checkedLangs[$iso_code])
			return true;

		foreach (array_keys(Language::getFilesList($iso_code, _THEME_NAME_, false, false, false, true)) as $key)
			if (!file_exists($key))
				return false;
		self::$_checkedLangs[$iso_code] = true;
		return true;
	}

	public static function getFilesList($iso_from, $theme_from, $iso_to = false, $theme_to = false, $select = false, $check = false, $modules = false)
	{
		if (empty($iso_from))
			die(Tools::displayError());

		$copy = ($iso_to && $theme_to) ? true : false;

		$lPath_from = _PS_TRANSLATIONS_DIR_.(string)$iso_from.'/';
		$tPath_from = _PS_ROOT_DIR_.'/themes/'.(string)$theme_from.'/';
		$pPath_from = _PS_ROOT_DIR_.'/themes/'.(string)$theme_from.'/pdf/';
		$mPath_from = _PS_MAIL_DIR_.(string)$iso_from.'/';

		if ($copy)
		{
			$lPath_to = _PS_TRANSLATIONS_DIR_.(string)$iso_to.'/';
			$tPath_to = _PS_ROOT_DIR_.'/themes/'.(string)$theme_to.'/';
			$pPath_to = _PS_ROOT_DIR_.'/themes/'.(string)$theme_to.'/pdf/';
			$mPath_to = _PS_MAIL_DIR_.(string)$iso_to.'/';
		}

		$lFiles = array('admin.php', 'errors.php', 'fields.php', 'pdf.php', 'tabs.php', 'index.php');

		// Added natives mails files
		$mFiles = array(
			'account.html', 'account.txt',
			'backoffice_order.html', 'backoffice_order.txt',
			'bankwire.html', 'bankwire.txt',
			'cheque.html', 'cheque.txt',
			'contact.html', 'contact.txt',
			'contact_form.html', 'contact_form.txt',
			'credit_slip.html', 'credit_slip.txt',
			'download_product.html', 'download_product.txt', 'download-product.tpl',
			'employee_password.html', 'employee_password.txt',
			'forward_msg.html', 'forward_msg.txt',
			'guest_to_customer.html', 'guest_to_customer.txt',
			'in_transit.html', 'in_transit.txt',
			'log_alert.html', 'log_alert.txt',
			'newsletter.html', 'newsletter.txt',
			'order_canceled.html', 'order_canceled.txt',
			'order_conf.html', 'order_conf.txt',
			'order_customer_comment.html', 'order_customer_comment.txt',
			'order_merchant_comment.html', 'order_merchant_comment.txt',
			'order_return_state.html', 'order_return_state.txt',
			'outofstock.html', 'outofstock.txt',
			'password.html', 'password.txt',
			'password_query.html', 'password_query.txt',
			'payment.html', 'payment.txt',
			'payment_error.html', 'payment_error.txt',
			'preparation.html', 'preparation.txt',
			'refund.html', 'refund.txt',
			'reply_msg.html', 'reply_msg.txt',
			'shipped.html', 'shipped.txt',
			'test.html', 'test.txt',
			'voucher.html', 'voucher.txt',
			'voucher_new.html', 'voucher_new.txt',
			'order_changed.html', 'order_changed.txt', 'index.php'
		);

		$number = -1;

		$files = array();
		$files_tr = array();
		$files_theme = array();
		$files_mail = array();
		$files_modules = array();

		// When a copy is made from a theme in specific language
		// to an other theme for the same language,
		// it's avoid to copy Translations, Mails files
		// and modules files which are not override by theme.
		if (!$copy || $iso_from != $iso_to)
		{
			// Translations files
			if (!$check || ($check && (string)$iso_from != 'en'))
				foreach ($lFiles as $file)
					$files_tr[$lPath_from.$file] = ($copy ? $lPath_to.$file : ++$number);
			if ($select == 'tr')
				return $files_tr;
			$files = array_merge($files, $files_tr);

			// Mail files
			if (!$check || ($check && (string)$iso_from != 'en'))
				$files_mail[$mPath_from.'lang.php'] = ($copy ? $mPath_to.'lang.php' : ++$number);
			foreach ($mFiles as $file)
				$files_mail[$mPath_from.$file] = ($copy ? $mPath_to.$file : ++$number);
			if ($select == 'mail')
				return $files_mail;
			$files = array_merge($files, $files_mail);

			// Modules
			if ($modules)
			{
				$modList = Module::getModulesDirOnDisk();
				foreach ($modList as $mod)
				{
					$modDir = _PS_MODULE_DIR_.$mod;
					// Lang file
					if (file_exists($modDir.'/translations/'.(string)$iso_from.'.php'))
						$files_modules[$modDir.'/translations/'.(string)$iso_from.'.php'] = ($copy ? $modDir.'/translations/'.(string)$iso_to.'.php' : ++$number);
					else if (file_exists($modDir.'/'.(string)$iso_from.'.php'))
						$files_modules[$modDir.'/'.(string)$iso_from.'.php'] = ($copy ? $modDir.'/'.(string)$iso_to.'.php' : ++$number);
					// Mails files
					$modMailDirFrom = $modDir.'/mails/'.(string)$iso_from;
					$modMailDirTo = $modDir.'/mails/'.(string)$iso_to;
					if (file_exists($modMailDirFrom))
					{
						$dirFiles = scandir($modMailDirFrom);
						foreach ($dirFiles as $file)
							if (file_exists($modMailDirFrom.'/'.$file) && $file != '.' && $file != '..' && $file != '.svn')
								$files_modules[$modMailDirFrom.'/'.$file] = ($copy ? $modMailDirTo.'/'.$file : ++$number);
					}
				}
				if ($select == 'modules')
					return $files_modules;
				$files = array_merge($files, $files_modules);
			}
		}
		else if ($select == 'mail' || $select == 'tr')
			return $files;

		// Theme files
		if (!$check || ($check && (string)$iso_from != 'en'))
		{
			$files_theme[$tPath_from.'lang/'.(string)$iso_from.'.php'] = ($copy ? $tPath_to.'lang/'.(string)$iso_to.'.php' : ++$number);

			// Override for pdf files in the theme
			if (file_exists($pPath_from.'lang/'.(string)$iso_from.'.php'))
				$files_theme[$pPath_from.'lang/'.(string)$iso_from.'.php'] = ($copy ? $pPath_to.'lang/'.(string)$iso_to.'.php' : ++$number);

			$module_theme_files = (file_exists($tPath_from.'modules/') ? scandir($tPath_from.'modules/') : array());
			foreach ($module_theme_files as $module)
				if ($module !== '.' && $module != '..' && $module !== '.svn' && file_exists($tPath_from.'modules/'.$module.'/'.(string)$iso_from.'.php'))
					$files_theme[$tPath_from.'modules/'.$module.'/'.(string)$iso_from.'.php'] = ($copy ? $tPath_to.'modules/'.$module.'/'.(string)$iso_to.'.php' : ++$number);
		}
		if ($select == 'theme')
			return $files_theme;
		$files = array_merge($files, $files_theme);

		// Return
		return $files;
	}

	/**
	 * loadUpdateSQL will create default lang values when you create a new lang, based on default id lang
	 *
	 * @return boolean true if succeed
	 */
	public function loadUpdateSQL()
	{
		$tables = Db::getInstance()->executeS('SHOW TABLES LIKE \''.str_replace('_', '\\_', _DB_PREFIX_).'%\_lang\' ');
		$langTables = array();

		foreach ($tables as $table)
			foreach ($table as $t)
				if ($t != _DB_PREFIX_.'configuration_lang')
				$langTables[] = $t;

		$return = true;

		$shops = Shop::getShopsCollection(false);
		foreach ($shops as $shop)
		{
			$id_lang_default = Configuration::get('PS_LANG_DEFAULT', null, $shop->id_shop_group, $shop->id);

			foreach ($langTables as $name)
			{
				$fields = '';
				// We will check if the table contains a column "id_shop"
				// If yes, we will add "id_shop" as a WHERE condition in queries copying data from default language
				$shop_field_exists = false;
				$columns = Db::getInstance()->executeS('SHOW COLUMNS FROM `'.$name.'`');
				foreach ($columns as $column)
				{
					$fields .= $column['Field'].', ';
					if ($column['Field'] == 'id_shop')
						$shop_field_exists = true;
				}
				$fields = rtrim($fields, ', ');
				preg_match('#^'.preg_quote(_DB_PREFIX_).'(.+)_lang$#i', $name, $m);
				$identifier = 'id_'.$m[1];

				$sql = 'INSERT IGNORE INTO `'.$name.'` ('.$fields.') (SELECT ';

				// For each column, copy data from default language
				foreach ($columns as $column)
				{
					if ($identifier != $column['Field'] && $column['Field'] != 'id_lang')
					{
						$sql .= '(SELECT `'.$column['Field'].'`	FROM `'.$name.'` tl	WHERE tl.`id_lang` = '.(int)$id_lang_default
									.($shop_field_exists ? ' AND tl.`id_shop` = '.(int)$shop->id : '')
									.' AND tl.`'.$identifier.'` = `'.str_replace('_lang', '', $name).'`.`'.$identifier.'`), ';
					}
					else
						$sql .= '`'.$column['Field'].'`, ';
				}
				$sql = rtrim($sql, ', ');
				$sql .= ' FROM `'._DB_PREFIX_.'lang` CROSS JOIN `'.str_replace('_lang', '', $name).'`) ;';
				$return &= Db::getInstance()->execute(pSQL($sql));
			}
		}
		return $return;
	}

	public static function recurseDeleteDir($dir)
	{
		if (!is_dir($dir))
			return false;
		if ($handle = @opendir($dir))
		{
			while (false !== ($file = readdir($handle)))
				if ($file != '.' && $file != '..')
				{
					if (is_dir($dir.'/'.$file))
						Language::recurseDeleteDir($dir.'/'.$file);
					elseif (file_exists($dir.'/'.$file))
						@unlink($dir.'/'.$file);
				}
			closedir($handle);
		}
		rmdir($dir);
	}

	public function delete()
	{
		if (!$this->hasMultishopEntries())
		{
			if (empty($this->iso_code))
				$this->iso_code = Language::getIsoById($this->id);
	
			// Database translations deletion
			$result = Db::getInstance()->executeS('SHOW TABLES FROM `'._DB_NAME_.'`');
			foreach ($result as $row)
				if (isset($row['Tables_in_'._DB_NAME_]) && !empty($row['Tables_in_'._DB_NAME_]) && preg_match('/_lang/', $row['Tables_in_'._DB_NAME_]))
					if (!Db::getInstance()->execute('DELETE FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int)$this->id))
						return false;
	
	
			// Delete tags
			Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'tag WHERE id_lang = '.(int)$this->id);
	
			// Delete search words
			Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'search_word WHERE id_lang = '.(int)$this->id);
	
			// Files deletion
			foreach (Language::getFilesList($this->iso_code, _THEME_NAME_, false, false, false, true, true) as $key => $file)
				if (file_exists($key))
				unlink($key);
			$modList = scandir(_PS_MODULE_DIR_);
			foreach ($modList as $mod)
			{
				Language::recurseDeleteDir(_PS_MODULE_DIR_.$mod.'/mails/'.$this->iso_code);
				$files = @scandir(_PS_MODULE_DIR_.$mod.'/mails/');
				if (count($files) <= 2)
					Language::recurseDeleteDir(_PS_MODULE_DIR_.$mod.'/mails/');
	
				if (file_exists(_PS_MODULE_DIR_.$mod.'/'.$this->iso_code.'.php'))
				{
					unlink(_PS_MODULE_DIR_.$mod.'/'.$this->iso_code.'.php');
					$files = @scandir(_PS_MODULE_DIR_.$mod);
					if (count($files) <= 2)
						Language::recurseDeleteDir(_PS_MODULE_DIR_.$mod);
				}
			}
	
			if (file_exists(_PS_MAIL_DIR_.$this->iso_code))
				Language::recurseDeleteDir(_PS_MAIL_DIR_.$this->iso_code);
			if (file_exists(_PS_TRANSLATIONS_DIR_.$this->iso_code))
				Language::recurseDeleteDir(_PS_TRANSLATIONS_DIR_.$this->iso_code);
		}
		
		if (!parent::delete())
			return false;
		if (!$this->hasMultishopEntries())
		{
			// delete images
			$files_copy = array(
				'/en.jpg',
				'/en-default-thickbox_default.jpg',
				'/en-default-home_default.jpg',
				'/en-default-large_default.jpg',
				'/en-default-medium_default.jpg',
				'/en-default-small_default.jpg'
			);
			$tos = array(_PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_PROD_IMG_DIR_, _PS_SUPP_IMG_DIR_);
			foreach ($tos as $to)
				foreach ($files_copy as $file)
				{
					$name = str_replace('/en', ''.$this->iso_code, $file);
	
					if (file_exists($to.$name))
						unlink($to.$name);
					if (file_exists(dirname(__FILE__).'/../img/l/'.$this->id.'.jpg'))
						unlink(dirname(__FILE__).'/../img/l/'.$this->id.'.jpg');
				}
		}
		return Tools::generateHtaccess();
	}


	public function deleteSelection($selection)
	{
		if (!is_array($selection))
			die(Tools::displayError());

		$result = true;
		foreach ($selection as $id)
		{
			$this->id = (int)($id);
			$result = $result && $this->delete();
		}

		Tools::generateHtaccess();
		return $result;
	}

	/**
	  * Return available languages
	  *
	  * @param boolean $active Select only active languages
	  * @return array Languages
	  */
	public static function getLanguages($active = true, $id_shop = false)
	{
		if (!self::$_LANGUAGES)
			Language::loadLanguages();

		$languages = array();
		foreach (self::$_LANGUAGES as $language)
		{
			if ($active && !$language['active'] || ($id_shop && !isset($language['shops'][(int)$id_shop])))
				continue;
			$languages[] = $language;
		}
		return $languages;
	}

	public static function getLanguage($id_lang)
	{
		if (!array_key_exists((int)($id_lang), self::$_LANGUAGES))
			return false;
		return self::$_LANGUAGES[(int)($id_lang)];
	}

	/**
	  * Return iso code from id
	  *
	  * @param integer $id_lang Language ID
	  * @return string Iso code
	  */
	public static function getIsoById($id_lang)
	{
		if (isset(self::$_LANGUAGES[(int)$id_lang]['iso_code']))
			return self::$_LANGUAGES[(int)$id_lang]['iso_code'];
		return false;
	}

	/**
	  * Return id from iso code
	  *
	  * @param string $iso_code Iso code
	  * @return integer Language ID
	  */
	public static function getIdByIso($iso_code)
	{
	 	if (!Validate::isLanguageIsoCode($iso_code))
	 		die(Tools::displayError('Fatal error: ISO code is not correct').' '.$iso_code);

		return Db::getInstance()->getValue('SELECT `id_lang` FROM `'._DB_PREFIX_.'lang` WHERE `iso_code` = \''.pSQL(strtolower($iso_code)).'\'');
	}

	public static function getLanguageCodeByIso($iso_code)
	{
	 	if (!Validate::isLanguageIsoCode($iso_code))
			die(Tools::displayError('Fatal error: ISO code is not correct').' '.$iso_code);

		return Db::getInstance()->getValue('SELECT `language_code` FROM `'._DB_PREFIX_.'lang` WHERE `iso_code` = \''.pSQL(strtolower($iso_code)).'\'');
	}

	/**
	  * Return array (id_lang, iso_code)
	  *
	  * @param string $iso_code Iso code
	  * @return array  Language (id_lang, iso_code)
	  */
	public static function getIsoIds($active = true)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_lang`, `iso_code` FROM `'._DB_PREFIX_.'lang` '.($active ? 'WHERE active = 1' : ''));
	}

	public static function copyLanguageData($from, $to)
	{
		$result = Db::getInstance()->executeS('SHOW TABLES FROM `'._DB_NAME_.'`');
		foreach ($result as $row)
			if (preg_match('/_lang/', $row['Tables_in_'._DB_NAME_]) && $row['Tables_in_'._DB_NAME_] != _DB_PREFIX_.'lang')
			{
				$result2 = Db::getInstance()->executeS('SELECT * FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int)$from);
				if (!count($result2))
					continue;
				Db::getInstance()->execute('DELETE FROM `'.$row['Tables_in_'._DB_NAME_].'` WHERE `id_lang` = '.(int)$to);
				$query = 'INSERT INTO `'.$row['Tables_in_'._DB_NAME_].'` VALUES ';
				foreach ($result2 as $row2)
				{
					$query .= '(';
					$row2['id_lang'] = $to;
					foreach ($row2 as $field)
						$query .= '\''.pSQL($field, true).'\',';
					$query = rtrim($query, ',').'),';
				}
				$query = rtrim($query, ',');
				Db::getInstance()->execute($query);
			}
		return true;
	}

	/**
	  * Load all languages in memory for caching
	  */
	public static function loadLanguages()
	{
		self::$_LANGUAGES = array();

		$sql = 'SELECT l.*, ls.`id_shop`
				FROM `'._DB_PREFIX_.'lang` l
				LEFT JOIN `'._DB_PREFIX_.'lang_shop` ls ON (l.id_lang = ls.id_lang)';

		$result = Db::getInstance()->executeS($sql);
		foreach ($result as $row)
		{
			if (!isset(self::$_LANGUAGES[(int)$row['id_lang']]))
				self::$_LANGUAGES[(int)$row['id_lang']] = $row;
			self::$_LANGUAGES[(int)$row['id_lang']]['shops'][(int)$row['id_shop']] = true;
		}
	}

	public function update($nullValues = false)
	{
		if (!parent::update($nullValues))
			return false;

		return Tools::generateHtaccess();
	}

	public static function checkAndAddLanguage($iso_code)
	{
		if (Language::getIdByIso($iso_code))
			return true;
		else
		{
			if (@fsockopen('www.prestashop.com', 80))
			{
				$lang = new Language();
				$lang->iso_code = $iso_code;
				$lang->active = true;

				if ($lang_pack = Tools::jsonDecode(Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/get_language_pack.php?version='._PS_VERSION_.'&iso_lang='.$iso_code)))
				{
					if (isset($lang_pack->name)
					&& isset($lang_pack->version)
					&& isset($lang_pack->iso_code))
						$lang->name = $lang_pack->name;
				}
				if (!$lang->name || !$lang->add())
					return false;
				$insert_id = (int)$lang->id;

				if ($lang_pack)
				{
					$flag = Tools::file_get_contents('http://www.prestashop.com/download/lang_packs/flags/jpeg/'.$iso_code.'.jpg');
					if ($flag != null && !preg_match('/<body>/', $flag))
					{
						$file = fopen(dirname(__FILE__).'/../img/l/'.$insert_id.'.jpg', 'w');
						if ($file)
						{
							fwrite($file, $flag);
							fclose($file);
						}
						else
							Language::_copyNoneFlag($insert_id);
					}
					else
						Language::_copyNoneFlag($insert_id);
				}
				else
					Language::_copyNoneFlag($insert_id);

				$files_copy = array(
					'/en.jpg',
					'/en-default-thickbox_default.jpg',
					'/en-default-home_default.jpg',
					'/en-default-large_default.jpg',
					'/en-default-medium_default.jpg',
					'/en-default-small_default.jpg',
					'/en-default-scene_default.jpg'
				);
				foreach (array(_PS_CAT_IMG_DIR_, _PS_MANU_IMG_DIR_, _PS_PROD_IMG_DIR_, _PS_SUPP_IMG_DIR_) as $to)
					foreach ($files_copy as $file)
						@copy(dirname(__FILE__).'/../img/l'.$file, $to.str_replace('/en', '/'.$iso_code, $file));
				return true;
			}
			else
				return false;
		}
	}

	protected static function _copyNoneFlag($id)
	{
		return copy(dirname(__FILE__).'/../img/l/none.jpg', dirname(__FILE__).'/../img/l/'.$id.'.jpg');
	}

	protected static $_cache_language_installation = null;
	public static function isInstalled($iso_code)
	{
		if (self::$_cache_language_installation === null)
		{
			self::$_cache_language_installation = array();
			$result = Db::getInstance()->executeS('SELECT `id_lang`, `iso_code` FROM `'._DB_PREFIX_.'lang`');
			foreach ($result as $row)
				self::$_cache_language_installation[$row['iso_code']] = $row['id_lang'];
		}
		return (isset(self::$_cache_language_installation[$iso_code]) ? self::$_cache_language_installation[$iso_code] : false);
	}

	public static function countActiveLanguages()
	{
		if (!self::$countActiveLanguages)
			self::$countActiveLanguages = Db::getInstance()->getValue('
				SELECT COUNT(DISTINCT l.id_lang) FROM `'._DB_PREFIX_.'lang` l
				'.Shop::addSqlAssociation('lang', 'l').'
				WHERE l.`active` = 1
			');
		return self::$countActiveLanguages;
	}

	/**
	 * Check if more on than one language is activated
	 *
	 * @since 1.5.0
	 * @return bool
	 */
	public static function isMultiLanguageActivated()
	{
		return (Language::countActiveLanguages() > 1);
	}
}
