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
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class InstallModelInstall extends InstallAbstractModel
{
	const SETTINGS_FILE = 'config/settings.inc.php';

	/**
	 * @var FileLogger
	 */
	public $logger;

	public function __construct()
	{
		parent::__construct();

		$this->logger = new FileLogger();
		if (is_writable(_PS_ROOT_DIR_.'/log/'))
			$this->logger->setFilename(_PS_ROOT_DIR_.'/log/'.@date('Ymd').'_installation.log');
	}

	public function setError($errors)
	{
		if (!is_array($errors))
			$errors = array($errors);

		parent::setError($errors);

		foreach ($errors as $error)
			$this->logger->logError($error);
	}

	/**
	 * Generate settings file
	 */
	public function generateSettingsFile($database_server, $database_login, $database_password, $database_name, $database_prefix, $database_engine)
	{
		// Check permissions for settings file
		if (file_exists(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE) && !is_writable(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE))
		{
			$this->setError($this->language->l('%s file is not writable (check permissions)', self::SETTINGS_FILE));
			return false;
		}
		elseif (!file_exists(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE) && !is_writable(_PS_ROOT_DIR_.'/'.dirname(self::SETTINGS_FILE)))
		{
			$this->setError($this->language->l('%s folder is not writable (check permissions)', dirname(self::SETTINGS_FILE)));
			return false;
		}

		// Generate settings content and write file
		$settings_constants = array(
			'_DB_SERVER_' => 			$database_server,
			'_DB_NAME_' =>				$database_name,
			'_DB_USER_' => 				$database_login,
			'_DB_PASSWD_' => 			$database_password,
			'_DB_PREFIX_' => 			$database_prefix,
			'_MYSQL_ENGINE_' => 		$database_engine,
			'_PS_CACHING_SYSTEM_' => 	'CacheMemcache',
			'_PS_CACHE_ENABLED_' => 	'0',
			'_MEDIA_SERVER_1_' => 		'',
			'_MEDIA_SERVER_2_' => 		'',
			'_MEDIA_SERVER_3_' => 		'',
			'_COOKIE_KEY_' => 			Tools::passwdGen(56),
			'_COOKIE_IV_' => 			Tools::passwdGen(8),
			'_PS_CREATION_DATE_' => 	date('Y-m-d'),
			'_PS_VERSION_' => 			_PS_INSTALL_VERSION_,
		);

		// If mcrypt is activated, add Rijndael 128 configuration
		if (function_exists('mcrypt_encrypt'))
		{
			$settings_constants['_RIJNDAEL_KEY_'] = Tools::passwdGen(mcrypt_get_key_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB));
			$settings_constants['_RIJNDAEL_IV_'] = base64_encode(mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB), MCRYPT_RAND));
		}

		$settings_content = "<?php\n";
		foreach ($settings_constants as $constant => $value)
			$settings_content .= "define('$constant', '".str_replace('\'', '\\\'', $value)."');\n";
		if (!file_put_contents(_PS_ROOT_DIR_.'/'.self::SETTINGS_FILE, $settings_content))
		{
			$this->setError($this->language->l('Cannot write settings file'));
			return false;
		}
		return true;
	}

	/**
	 * PROCESS : installDatabase
	 * Generate settings file and create database structure
	 */
	public function installDatabase($clear_database = false)
	{
		// Clear database (only tables with same prefix)
		require_once _PS_ROOT_DIR_.'/'.self::SETTINGS_FILE;
		if ($clear_database)
			$this->clearDatabase();

		// Install database structure
		$sql_loader = new InstallSqlLoader();
		$sql_loader->setMetaData(array(
			'PREFIX_' => _DB_PREFIX_,
			'ENGINE_TYPE' => _MYSQL_ENGINE_,
		));

		try
		{
			$sql_loader->parse_file(_PS_INSTALL_DATA_PATH_.'db_structure.sql');
		}
		catch (PrestashopInstallerException $e)
		{
			$this->setError($this->language->l('Database structure file not found'));
			return false;
		}

		if ($errors = $sql_loader->getErrors())
		{
			foreach ($errors as $error)
				$this->setError($this->language->l('SQL error on query <i>%s</i>', $error['error']));
			return false;
		}

		return true;
	}

	/**
	 * Clear database (only tables with same prefix)
	 *
	 * @param bool $truncate If true truncate the table, if false drop the table
	 */
	public function clearDatabase($truncate = false)
	{
		foreach (Db::getInstance()->executeS('SHOW TABLES') as $row)
		{
			$table = current($row);
			if (!_DB_PREFIX_ || preg_match('#^'._DB_PREFIX_.'#i', $table))
				Db::getInstance()->execute((($truncate) ? 'TRUNCATE' : 'DROP TABLE').' `'.$table.'`');
		}
	}

	/**
	 * PROCESS : installDefaultData
	 * Create default shop and languages
	 */
	public function installDefaultData($shop_name, $clear_database = false)
	{
		if ($clear_database)
			$this->clearDatabase(true);

		// Install first shop
		if (!$this->createShop($shop_name))
			return false;

		// Install languages
		try
		{
			$languages = $this->installLanguages();
		}
		catch (PrestashopInstallerException $e)
		{
			$this->setError($e->getMessage());
			return false;
		}

		$flip_languages = array_flip($languages);
		$id_lang =  (!empty($flip_languages[$this->language->getLanguageIso()])) ? $flip_languages[$this->language->getLanguageIso()] : 1;
		Configuration::updateGlobalValue('PS_LANG_DEFAULT', $id_lang);
		return true;
	}

	/**
	 * PROCESS : populateDatabase
	 * Populate database with default data
	 */
	public function populateDatabase($entity = null)
	{
		$languages = array();
		foreach (Language::getLanguages(false) as $lang)
			$languages[$lang['id_lang']] = $lang['iso_code'];

		// Install XML data (data/xml/ folder)
		$xml_loader = new InstallXmlLoader();
		$xml_loader->setLanguages($languages);

		if (isset($this->xml_loader_ids) && $this->xml_loader_ids)
			$xml_loader->setIds($this->xml_loader_ids);

		if ($entity)
			$xml_loader->populateEntity($entity);
		else
			$xml_loader->populateFromXmlFiles();
		if ($errors = $xml_loader->getErrors())
		{
			$this->setError($errors);
			return false;
		}

		// IDS from xmlLoader are stored in order to use them for fixtures
		$this->xml_loader_ids = $xml_loader->getIds();
		unset($xml_loader);

		// Install custom SQL data (db_data.sql file)
		if (file_exists(_PS_INSTALL_DATA_PATH_.'db_data.sql'))
		{
			$sql_loader = new InstallSqlLoader();
			$sql_loader->setMetaData(array(
				'PREFIX_' => _DB_PREFIX_,
				'ENGINE_TYPE' => _MYSQL_ENGINE_,
			));

			$sql_loader->parse_file(_PS_INSTALL_DATA_PATH_.'db_data.sql', false);
			if ($errors = $sql_loader->getErrors())
			{
				$this->setError($errors);
				return false;
			}
		}

		// Copy language default images (we do this action after database in populated because we need image types information)
		foreach ($languages as $iso)
			$this->copyLanguageImages($iso);

		return true;
	}

	public function createShop($shop_name)
	{
		// Create default group shop
		$shop_group = new ShopGroup();
		$shop_group->name = 'Default';
		$shop_group->active = true;
		if (!$shop_group->add())
		{
			$this->setError($this->language->l('Cannot create group shop').' / '.Db::getInstance()->getMsgError());
			return false;
		}

		// Create default shop
		$shop = new Shop();
		$shop->active = true;
		$shop->id_shop_group = $shop_group->id;
		$shop->id_category = 2;
		$shop->id_theme = 1;
		$shop->name = $shop_name;
		if (!$shop->add())
		{
			$this->setError($this->language->l('Cannot create shop').' / '.Db::getInstance()->getMsgError());
			return false;
		}
		Context::getContext()->shop = $shop;

		// Create default shop URL
		$shop_url = new ShopUrl();
		$shop_url->domain = Tools::getHttpHost();
		$shop_url->domain_ssl = Tools::getHttpHost();
		$shop_url->physical_uri = __PS_BASE_URI__;
		$shop_url->id_shop = $shop->id;
		$shop_url->main = true;
		$shop_url->active = true;
		if (!$shop_url->add())
		{
			$this->setError($this->language->l('Cannot create shop URL').' / '.Db::getInstance()->getMsgError());
			return false;
		}

		return true;
	}

	/**
	 * Install languages
	 *
	 * @return array Association between ID and iso array(id_lang => iso, ...)
	 */
	public function installLanguages()
	{
		$languages = array();
		foreach ($this->language->getIsoList() as $iso)
		{
			if (!file_exists(_PS_INSTALL_LANGS_PATH_.$iso.'/language.xml'))
				throw new PrestashopInstallerException($this->language->l('File "language.xml" not found for language iso "%s"', $iso));

			if (!$xml = @simplexml_load_file(_PS_INSTALL_LANGS_PATH_.$iso.'/language.xml'))
				throw new PrestashopInstallerException($this->language->l('File "language.xml" not valid for language iso "%s"', $iso));

			// Add language in database
			$language = new Language();
			$language->iso_code = $iso;
			$language->active = ($iso == $this->language->getLanguageIso()) ? true : false;
			$language->name = ($xml->name) ? (string)$xml->name : 'Unknown (Unknown)';
			$language->language_code = ($xml->language_code) ? (string)$xml->language_code : $iso;
			$language->date_format_lite = ($xml->date_format_lite) ? (string)$xml->date_format_lite : 'm/j/Y';
			$language->date_format_full = ($xml->date_format_full) ? (string)$xml->date_format_full : 'm/j/Y H:i:s';
			$language->is_rtl = ($xml->is_rtl && ($xml->is_rtl == 'true' || $xml->is_rtl == '1')) ? 1 : 0;
			if (!$language->add())
				throw new PrestashopInstallerException($this->language->l('Cannot install language "%s"', ($xml->name) ? $xml->name : $iso));
			$languages[$language->id] = $iso;

			// Copy language flag
			if (is_writable(_PS_IMG_DIR_.'l/'))
				if (!copy(_PS_INSTALL_LANGS_PATH_.$iso.'/flag.jpg', _PS_IMG_DIR_.'l/'.$language->id.'.jpg'))
					throw new PrestashopInstallerException($this->language->l('Cannot copy flag language "%s"', _PS_INSTALL_LANGS_PATH_.$iso.'/flag.jpg => '._PS_IMG_DIR_.'l/'.$language->id.'.jpg'));
		}

		return $languages;
	}

	public function copyLanguageImages($iso)
	{
		$img_path = _PS_INSTALL_LANGS_PATH_.$iso.'/img/';
		if (!is_dir($img_path))
			return;

		$list = array(
			'products' =>		_PS_PROD_IMG_DIR_,
			'categories',		_PS_CAT_IMG_DIR_,
			'manufacturers' =>	_PS_MANU_IMG_DIR_,
			'suppliers' =>		_PS_SUPP_IMG_DIR_,
			'scenes' =>			_PS_SCENE_IMG_DIR_,
			'stores' =>			_PS_STORE_IMG_DIR_,
			null =>				_PS_IMG_DIR_.'l/', // Little trick to copy images in img/l/ path with all types
		);

		foreach ($list as $cat => $dst_path)
		{
			if (!is_writable($dst_path))
				continue;

			copy($img_path.$iso.'.jpg', $dst_path.$iso.'.jpg');

			$types = ImageType::getImagesTypes($cat);
			foreach ($types as $type)
			{
				if (file_exists($img_path.$iso.'-default-'.$type['name'].'.jpg'))
					copy($img_path.$iso.'-default-'.$type['name'].'.jpg', $dst_path.$iso.'-default-'.$type['name'].'.jpg');
				else
					ImageManager::resize($img_path.$iso.'.jpg', $dst_path.$iso.'-default-'.$type['name'].'.jpg', $type['width'], $type['height']);
			}
		}
	}

	/**
	 * PROCESS : configureShop
	 * Set default shop configuration
	 */
	public function configureShop(array $data = array())
	{
		// Clear smarty cache
		$this->clearSmartyCache();
		
		//clear image cache in tmp folder
		Tools::deleteDirectory(_PS_TMP_IMG_DIR_, false);
		
		$default_data = array(
			'shop_name' =>		'My Shop',
			'shop_activity' =>	'',
			'shop_country' =>	'us',
			'shop_timezone' =>	'US/Eastern',
			'use_smtp' =>		false,
			'smtp_server' =>	'',
			'smtp_login' =>		'',
			'smtp_password' =>	'',
			'smtp_encryption' =>'off',
			'smtp_port' =>		25,
		);

		foreach ($default_data as $k => $v)
			if (!isset($data[$k]))
				$data[$k] = $v;

		Context::getContext()->shop = new Shop(1);
		Configuration::loadConfiguration();
		$id_country = Country::getByIso($data['shop_country']);

		// Set default configuration
		Configuration::updateGlobalValue('PS_SHOP_DOMAIN', 				Tools::getHttpHost());
		Configuration::updateGlobalValue('PS_SHOP_DOMAIN_SSL', 			Tools::getHttpHost());
		Configuration::updateGlobalValue('PS_INSTALL_VERSION', 			_PS_INSTALL_VERSION_);
		Configuration::updateGlobalValue('PS_LOCALE_LANGUAGE', 			$this->language->getLanguageIso());
		Configuration::updateGlobalValue('PS_SHOP_NAME', 				$data['shop_name']);
		Configuration::updateGlobalValue('PS_SHOP_ACTIVITY', 			$data['shop_activity']);
		Configuration::updateGlobalValue('PS_COUNTRY_DEFAULT',			$id_country);
		Configuration::updateGlobalValue('PS_LOCALE_COUNTRY', 			$data['shop_country']);
		Configuration::updateGlobalValue('PS_TIMEZONE', 				$data['shop_timezone']);
		Configuration::updateGlobalValue('PS_CONFIGURATION_AGREMENT',	(int)$data['configuration_agrement']);

		// Set mails configuration
		Configuration::updateGlobalValue('PS_MAIL_METHOD', 			($data['use_smtp']) ? 2 : 1);
		Configuration::updateGlobalValue('PS_MAIL_SERVER', 			$data['smtp_server']);
		Configuration::updateGlobalValue('PS_MAIL_USER', 			$data['smtp_login']);
		Configuration::updateGlobalValue('PS_MAIL_PASSWD', 			$data['smtp_password']);
		Configuration::updateGlobalValue('PS_MAIL_SMTP_ENCRYPTION', $data['smtp_encryption']);
		Configuration::updateGlobalValue('PS_MAIL_SMTP_PORT', 		$data['smtp_port']);

		// Activate rijndael 128 encrypt algorihtm if mcrypt is activated
		Configuration::updateGlobalValue('PS_CIPHER_ALGORITHM', function_exists('mcrypt_encrypt') ? 1 : 0);

		// Set logo configuration
		if (file_exists(_PS_IMG_DIR_.'logo.jpg'))
		{
			list($width, $height) = getimagesize(_PS_IMG_DIR_.'logo.jpg');
			Configuration::updateGlobalValue('SHOP_LOGO_WIDTH', round($width));
			Configuration::updateGlobalValue('SHOP_LOGO_HEIGHT', round($height));
		}

		// Active only the country selected by the merchant
		Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'country SET active = 0 WHERE id_country != '.(int)$id_country);

		// Set localization configuration
		$version = str_replace('.', '', _PS_VERSION_);
		$version = substr($version, 0, 2);

		$localization_file_content = @Tools::file_get_contents('http://api.prestashop.com/localization/'.$version.'/'.$data['shop_country'].'.xml');
		if (!@simplexml_load_string($localization_file_content))
			$localization_file_content = false;
		if (!$localization_file_content)
		{
			$localization_file = _PS_ROOT_DIR_.'/localization/default.xml';
			if (file_exists(_PS_ROOT_DIR_.'/localization/'.$data['shop_country'].'.xml'))
				$localization_file = _PS_ROOT_DIR_.'/localization/'.$data['shop_country'].'.xml';

			$localization_file_content = file_get_contents($localization_file);
		}

		$locale = new LocalizationPackCore();
		$locale->loadLocalisationPack($localization_file_content, '', true);

		// Create default employee
		if (isset($data['admin_firstname']) && isset($data['admin_lastname']) && isset($data['admin_password']) && isset($data['admin_email']))
		{
			$employee = new Employee();
			$employee->firstname = Tools::ucfirst($data['admin_firstname']);
			$employee->lastname = Tools::ucfirst($data['admin_lastname']);
			$employee->email = $data['admin_email'];
			$employee->passwd = md5(_COOKIE_KEY_.$data['admin_password']);
			$employee->last_passwd_gen = date('Y-m-d h:i:s', strtotime('-360 minutes'));
			$employee->bo_theme = 'default';
			$employee->active = true;
			$employee->id_profile = 1;
			$employee->id_lang = Configuration::get('PS_LANG_DEFAULT');
			$employee->bo_show_screencast = 1;
			if (!$employee->add())
			{
				$this->setError($this->language->l('Cannot create admin account'));
				return false;
			}
		}
		else
		{
			$this->setError($this->language->l('Cannot create admin account'));
			return false;
		}

		// Update default contact
		if (isset($data['admin_email']))
		{
			Configuration::updateGlobalValue('PS_SHOP_EMAIL', $data['admin_email']);

			$contacts = new Collection('Contact');
			foreach ($contacts as $contact)
			{
				$contact->email = $data['admin_email'];
				$contact->update();
			}
		}

		return true;
	}

	/**
	 * Clear smarty cache folders
	 */
	public function clearSmartyCache()
	{
		foreach (array(_PS_CACHE_DIR_.'smarty/cache', _PS_CACHE_DIR_.'smarty/compile') as $dir)
			if (file_exists($dir))
				foreach (scandir($dir) as $file)
					if ($file[0] != '.' && $file != 'index.php')
						@unlink($dir.$file);
	}

	public function getModulesList()
	{
		// @todo REMOVE DEV MODE
		$modules = array();
		if (false)
		{
			foreach (scandir(_PS_MODULE_DIR_) as $module)
				if ($module[0] != '.' && is_dir(_PS_MODULE_DIR_.$module) && file_exists(_PS_MODULE_DIR_.$module.'/'.$module.'.php'))
					$modules[] = $module;
		}
		else
		{
			// @todo THIS CODE NEED TO BE REMOVED WHEN MODULES API IS COMMITED
			$modules = array(
				'bankwire',
				'blockadvertising',
				'blockbestsellers',
				'blockcart',
				'blockcategories',
				'blockcms',
				'blockcontact',
				'blockcontactinfos',
				'blockcurrencies',
				'blockcustomerprivacy',
				'blocklanguages',
				'blockmanufacturer',
				'blockmyaccount',
				'blockmyaccountfooter',
				'blocknewproducts',
				'blocknewsletter',
				'blockpaymentlogo',
				'blockpermanentlinks',
				'blockreinsurance',
				'blocksearch',
				'blocksharefb',
				'blocksocial',
				'blockspecials',
				'blockstore',
				'blocksupplier',
				'blocktags',
				'blocktopmenu',
				'blockuserinfo',
				'blockviewed',
				'cheque',
				'favoriteproducts',
				'feeder',
				'graphartichow',
				'graphgooglechart',
				'graphvisifire',
				'graphxmlswfcharts',
				'gridhtml',
				'gsitemap',
				'homefeatured',
				'homeslider',
				'moneybookers',
				'pagesnotfound',
				'sekeywords',
				'statsbestcategories',
				'statsbestcustomers',
				'statsbestproducts',
				'statsbestsuppliers',
				'statsbestvouchers',
				'statscarrier',
				'statscatalog',
				'statscheckup',
				'statsdata',
				'statsequipment',
				'statsforecast',
				'statslive',
				'statsnewsletter',
				'statsorigin',
				'statspersonalinfos',
				'statsproduct',
				'statsregistrations',
				'statssales',
				'statssearch',
				'statsstock',
				'statsvisits',
				'themeinstallator',
			);
		}

		return $modules;
	}

	/**
	 * PROCESS : installModules
	 * Install all modules in ~/modules/ directory
	 */
	public function installModules($module = null)
	{
		$modules = $module ? array($module) : $this->getModulesList();

		$errors = array();
		foreach ($modules as $module_name)
		{
			if (!file_exists(_PS_MODULE_DIR_.$module_name.'/'.$module_name.'.php'))
				continue;

			$module = Module::getInstanceByName($module_name);
			if (!$module->install())
				$errors[] = $this->language->l('Cannot install module "%s"', $module_name);
		}

		if ($errors)
		{
			$this->setError($errors);
			return false;
		}
		return true;
	}

	/**
	 * PROCESS : installFixtures
	 * Install fixtures (E.g. demo products)
	 */
	public function installFixtures($entity = null)
	{
		// Load class (use fixture class if one exists, or use InstallXmlLoader)
		if (file_exists(_PS_INSTALL_FIXTURES_PATH_.'apple/install.php'))
		{
			require_once _PS_INSTALL_FIXTURES_PATH_.'apple/install.php';
			$class = 'InstallFixtures'.Tools::toCamelCase('apple');
			if (!class_exists($class, false))
			{
				$this->setError($this->language->l('Fixtures class "%s" not found', $class));
				return false;
			}

			$xml_loader = new $class();
			if (!$xml_loader instanceof InstallXmlLoader)
			{
				$this->setError($this->language->l('"%s" must be an instane of "InstallXmlLoader"', $class));
				return false;
			}
		}
		else
			$xml_loader = new InstallXmlLoader();

		// Install XML data (data/xml/ folder)
		$xml_loader->setFixturesPath();
		if (isset($this->xml_loader_ids) && $this->xml_loader_ids)
			$xml_loader->setIds($this->xml_loader_ids);

		$languages = array();
		foreach (Language::getLanguages(false) as $lang)
			$languages[$lang['id_lang']] = $lang['iso_code'];
		$xml_loader->setLanguages($languages);

		if ($entity)
			$xml_loader->populateEntity($entity);
		else
			$xml_loader->populateFromXmlFiles();

		if ($errors = $xml_loader->getErrors())
		{
			$this->setError($errors);
			return false;
		}

		// IDS from xmlLoader are stored in order to use them for fixtures
		$this->xml_loader_ids = $xml_loader->getIds();
		unset($xml_loader);

		// Index products in search tables
		Search::indexation(true);

		return true;
	}

	/**
	 * PROCESS : installTheme
	 * Install theme
	 */
	public function installTheme()
	{
		// @todo do a real install of the theme
		$sql_loader = new InstallSqlLoader();
		$sql_loader->setMetaData(array(
			'PREFIX_' => _DB_PREFIX_,
			'ENGINE_TYPE' => _MYSQL_ENGINE_,
		));

		$sql_loader->parse_file(_PS_INSTALL_DATA_PATH_.'theme.sql', false);
		if ($errors = $sql_loader->getErrors())
		{
			$this->setError($errors);
			return false;
		}
	}
}
