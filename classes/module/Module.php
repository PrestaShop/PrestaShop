<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class ModuleCore
{
	/** @var integer Module ID */
	public $id = null;

	/** @var float Version */
	public $version;
	public $database_version;

	/**
	 * @since 1.5.0.1
	 * @var string Registered Version in database
	 */
	public $registered_version;

	/** @var array filled with known compliant PS versions */
	public $ps_versions_compliancy = array('min' => '1.4', 'max' => '1.6');

	/** @var array filled with modules needed for install */
	public $dependencies = array();

	/** @var string Unique name */
	public $name;

	/** @var string Human name */
	public $displayName;

	/** @var string A little description of the module */
	public $description;

	/** @var string author of the module */
	public $author;

	/** @var int need_instance */
	public $need_instance = 1;

	/** @var string Admin tab corresponding to the module */
	public $tab = null;

	/** @var boolean Status */
	public $active = false;

	/** @var string Fill it if the module is installed but not yet set up */
	public $warning;

	/** @var array to store the limited country */
	public $limited_countries = array();

	/** @var array used by AdminTab to determine which lang file to use (admin.php or module lang file) */
	public static $classInModule = array();

	/** @var array current language translations */
	protected $_lang = array();

	/** @var string Module web path (eg. '/shop/modules/modulename/')  */
	protected $_path = null;
	/**
	 * @since 1.5.0.1
	 * @var string Module local path (eg. '/home/prestashop/modules/modulename/')
	 */
	protected $local_path = null;

	/** @var protected array filled with module errors */
	protected $_errors = array();

	/** @var protected array filled with module success */
	protected $_confirmations = array();

	/** @var protected string main table used for modules installed */
	protected $table = 'module';

	/** @var protected string identifier of the main table */
	protected $identifier = 'id_module';

	/** @var protected array cache filled with modules informations */
	protected static $modules_cache;

	/** @var protected array cache filled with modules instances */
	protected static $_INSTANCE = array();

	/** @var protected boolean filled with config xml generation mode */
	protected static $_generate_config_xml_mode = false;

	/** @var protected array filled with cache translations */
	protected static $l_cache = array();

	/** @var protected array filled with cache permissions (modules / employee profiles) */
	protected static $cache_permissions = array();

	/** @var Context */
	protected $context;

	/** @var Smarty_Data */
	protected $smarty;
	
	
	const CACHE_FILE_MODULES_LIST = '/config/xml/modules_list.xml';
	
	const CACHE_FILE_TAB_MODULES_LIST = '/config/xml/tab_modules_list.xml';
	
	const CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST = '/config/xml/default_country_modules_list.xml';
	
	const CACHE_FILE_CUSTOMER_MODULES_LIST = '/config/xml/customer_modules_list.xml';
	
	const CACHE_FILE_MUST_HAVE_MODULES_LIST = '/config/xml/must_have_modules_list.xml';
	
	/**
	 * Constructor
	 *
	 * @param string $name Module unique name
	 * @param Context $context
	 */
	public function __construct($name = null, Context $context = null)
	{
		// Load context and smarty
		$this->context = $context ? $context : Context::getContext();				
		if (is_object($this->context->smarty))				
			$this->smarty = $this->context->smarty->createData($this->context->smarty);

		// If the module has no name we gave him its id as name
		if ($this->name == null)
			$this->name = $this->id;

		// If the module has the name we load the corresponding data from the cache
		if ($this->name != null)
		{
			// If cache is not generated, we generate it
			if (self::$modules_cache == null && !is_array(self::$modules_cache))
			{
				// Join clause is done to check if the module is activated in current shop context												
				$sql_limit_shop = 'SELECT COUNT(*) FROM `'._DB_PREFIX_.'module_shop` ms WHERE m.`id_module` = ms.`id_module` AND ms.`id_shop` = '.((is_object(Context::getContext()->shop) && $id = (int)Context::getContext()->shop->id) ? $id : 1);
									
				$sql = 'SELECT m.`id_module`, m.`name`, ('.$sql_limit_shop.') as total FROM `'._DB_PREFIX_.'module` m';

				// Result is cached
				self::$modules_cache = array();
				$result = Db::getInstance()->executeS($sql);
				foreach ($result as $row)
				{
					self::$modules_cache[$row['name']] = $row;
					self::$modules_cache[$row['name']]['active'] = ($row['total'] > 0) ? 1 : 0;
				}
			}

			// We load configuration from the cache
			if (isset(self::$modules_cache[$this->name]))
			{
				if (isset(self::$modules_cache[$this->name]['id_module']))
					$this->id = self::$modules_cache[$this->name]['id_module'];
				foreach (self::$modules_cache[$this->name] as $key => $value)
					if (key_exists($key, $this))
						$this->{$key} = $value;
				$this->_path = __PS_BASE_URI__.'modules/'.$this->name.'/';
			}
			$this->local_path = _PS_MODULE_DIR_.$this->name.'/';
		}
	}

	/**
	 * Insert module into datable
	 */
	public function install()
	{
		Hook::exec('actionModuleInstallBefore', array('object' => $this));
		// Check module name validation
		if (!Validate::isModuleName($this->name))
		{
			$this->_errors[] = $this->l('Unable to install the module (Module name is not valid).');
			return false;
		}

		// Check PS version compliancy
		if (version_compare(_PS_VERSION_, $this->ps_versions_compliancy['min']) < 0 || version_compare(_PS_VERSION_, $this->ps_versions_compliancy['max']) >= 0)
		{
			$this->_errors[] = $this->l('The version of your module is not compliant with your PrestaShop version.');
			return false;
		}

		// Check module dependencies
		if (count($this->dependencies) > 0)
			foreach ($this->dependencies as $dependency)
				if (!Db::getInstance()->getRow('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \''.pSQL($dependency).'\''))
				{
					$error = $this->l('Before installing this module, you have to installed these/this module(s) first :').'<br />';
					foreach ($this->dependencies as $d)
						$error .= '- '.$d.'<br />';
					$this->_errors[] = $error;
					return false;
				}

		// Check if module is installed
		$result = Module::isInstalled($this->name);
		if ($result)
		{
			$this->_errors[] = $this->l('This module has already been installed.');
			return false;
		}

		// Install overrides
		try {
			$this->installOverrides();
		} catch (Exception $e) {
			$this->_errors[] = sprintf(Tools::displayError('Unable to install override: %s'), $e->getMessage());
			//$this->uninstallOverrides(); remove this line because if module a install an override, then module b install same override, this line will remove override of module a (if you find a bug related to this line please don't forget what i say before)
			return false;
		}

		// Install module and retrieve the installation id
		$result = Db::getInstance()->insert($this->table, array('name' => $this->name, 'active' => 1, 'version' => $this->version));
		if (!$result)
		{
			$this->_errors[] = $this->l('Technical error : PrestaShop could not installed this module.');
			return false;
		}
		$this->id = Db::getInstance()->Insert_ID();

		Cache::clean('Module::isInstalled'.$this->name);
		
		// Enable the module for current shops in context
		$this->enable();

		// Permissions management
		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_access` (`id_profile`, `id_module`, `view`, `configure`) (
				SELECT id_profile, '.(int)$this->id.', 1, 1
				FROM '._DB_PREFIX_.'access a
				WHERE id_tab = (
					SELECT `id_tab` FROM '._DB_PREFIX_.'tab
					WHERE class_name = \'AdminModules\' LIMIT 1)
				AND a.`view` = 1)');

		Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'module_access` (`id_profile`, `id_module`, `view`, `configure`) (
				SELECT id_profile, '.(int)$this->id.', 1, 0
				FROM '._DB_PREFIX_.'access a
				WHERE id_tab = (
					SELECT `id_tab` FROM '._DB_PREFIX_.'tab
					WHERE class_name = \'AdminModules\' LIMIT 1)
				AND a.`view` = 0)');

		// Adding Restrictions for client groups
		Group::addRestrictionsForModule($this->id, Shop::getShops(true, null, true));
		Hook::exec('actionModuleInstallAfter', array('object' => $this));
		return true;
	}

	/**
	 * Set errors, warning or success message of a module upgrade
	 *
	 * @param $upgrade_detail
	 */
	protected function setUpgradeMessage($upgrade_detail)
	{
		// Store information if a module has been upgraded (memory optimization)
		if ($upgrade_detail['available_upgrade'])
		{
			if ($upgrade_detail['success'])
			{
				$this->_confirmations[] = $this->l('Current version: ').$this->version;
				$this->_confirmations[] = $upgrade_detail['number_upgraded'].' '.$this->l('file upgrade applied');
			}
			else
			{
				if (!$upgrade_detail['number_upgraded'])
					$this->_errors[] = $this->l('None upgrades have been applied');
				else
				{
					$this->_errors[] = $this->l('Upgraded from: ').$upgrade_detail['upgraded_from'].$this->l(' to ').
						$upgrade_detail['upgraded_to'];
					$this->_errors[] = $upgrade_detail['number_upgrade_left'].' '.$this->l('upgrade left');
				}

				$this->_errors[] = $this->l('To prevent any problem, this module has been turned off');
			}
		}
	}

	/**
	 * Init the upgrade module
	 *
	 * @static
	 * @param $module_name
	 * @param $module_version
	 * @return bool
	 */
	public static function initUpgradeModule($module)
	{
		if (((int)$module->installed == 1) & (empty($module->database_version) === true))
		{
			Module::upgradeModuleVersion($module->name, $module->version);
			$module->database_version = $module->version;
		}
		
		// Init cache upgrade details
		self::$modules_cache[$module->name]['upgrade'] = array(
			'success' => false, // bool to know if upgrade succeed or not
			'available_upgrade' => 0, // Number of available module before any upgrade
			'number_upgraded' => 0, // Number of upgrade done
			'number_upgrade_left' => 0,
			'upgrade_file_left' => array(), // List of the upgrade file left
			'version_fail' => 0, // Version of the upgrade failure
			'upgraded_from' => 0, // Version number before upgrading anything
			'upgraded_to' => 0, // Last upgrade applied
		);
		
		// Need Upgrade will check and load upgrade file to the moduleCache upgrade case detail
		$ret = $module->installed && Module::needUpgrade($module);
		return $ret;
	}

	/**
	 * Run the upgrade for a given module name and version
	 *
	 * @return array
	 */
	public function runUpgradeModule()
	{
		$upgrade = &self::$modules_cache[$this->name]['upgrade'];
		foreach ($upgrade['upgrade_file_left'] as $num => $file_detail)
		{
			// Default variable required in the included upgrade file need to be set by default there:
			// upgrade_version, success_upgrade
			$upgrade_result = false;
			include($file_detail['file']);

			// Call the upgrade function if defined
			if (function_exists($file_detail['upgrade_function']))
				$upgrade_result = $file_detail['upgrade_function']($this);

			$upgrade['success'] = $upgrade_result;

			// Set detail when an upgrade succeed or failed
			if ($upgrade_result)
			{
				$upgrade['number_upgraded'] += 1;
				$upgrade['upgraded_to'] = $file_detail['version'];

				unset($upgrade['upgrade_file_left'][$num]);
			}
			else
			{
				$upgrade['version_fail'] = $file_detail['version'];

				// If any errors, the module is disabled
				$this->disable();
				break;
			}
		}

		$upgrade['number_upgrade_left'] = count($upgrade['upgrade_file_left']);
		// Update module version in DB with the last succeed upgrade
		if ($upgrade['upgraded_to'])
			Module::upgradeModuleVersion($this->name, $upgrade['upgraded_to']);
		$this->setUpgradeMessage($upgrade);
		return $upgrade;
	}

	/**
	 * Upgrade the registered version to a new one
	 *
	 * @static
	 * @param $name
	 * @param $version
	 * @return bool
	 */
	public static function upgradeModuleVersion($name, $version)
	{
		return Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'module` m
				SET m.version = \''.bqSQL($version).'\'
				WHERE m.name = \''.bqSQL($name).'\'');
	}

	/**
	 * Check if a module need to be upgraded.
	 * This method modify the module_cache adding an upgrade list file
	 *
	 * @static
	 * @param $module_name
	 * @param $module_version
	 * @return bool
	 */
	public static function needUpgrade($module)
	{
		self::$modules_cache[$module->name]['upgrade']['upgraded_from'] = $module->database_version;
		// Check the version of the module with the registered one and look if any upgrade file exist
		if (Tools::version_compare($module->version, $module->database_version, '>'))
		{
			$old_version = $module->database_version;
			$module = Module::getInstanceByName($module->name);
			if ($module instanceof Module)
				return $module->loadUpgradeVersionList($module->name, $module->version, $old_version);
		}
		return null;
	}

	/**
	 * Load the available list of upgrade of a specified module
	 * with an associated version
	 *
	 * @static
	 * @param $module_name
	 * @param $module_version
	 * @param $registered_version
	 * @return bool to know directly if any files have been found
	 */
	protected static function loadUpgradeVersionList($module_name, $module_version, $registered_version)
	{
		$list = array();

		$upgrade_path = _PS_MODULE_DIR_.$module_name.'/upgrade/';

		// Check if folder exist and it could be read
		if (file_exists($upgrade_path) && ($files = scandir($upgrade_path)))
		{
			// Read each file name
			foreach ($files as $file)
				if (!in_array($file, array('.', '..', '.svn', 'index.php')))
				{
					$tab = explode('-', $file);
					$file_version = basename($tab[1], '.php');
					// Compare version, if minor than actual, we need to upgrade the module
					if (count($tab) == 2 &&
						 (Tools::version_compare($file_version, $module_version, '<=') &&
							Tools::version_compare($file_version, $registered_version, '>')))
					{
						$list[] = array(
							'file' => $upgrade_path.$file,
							'version' => $file_version,
							'upgrade_function' => 'upgrade_module_'.str_replace('.', '_', $file_version));
					}
				}
		}

		// No files upgrade, then upgrade succeed
		if (count($list) == 0)
		{
			self::$modules_cache[$module_name]['upgrade']['success'] = true;
			Module::upgradeModuleVersion($module_name, $module_version);
		}
		
		usort($list, 'ps_module_version_sort');

		// Set the list to module cache
		self::$modules_cache[$module_name]['upgrade']['upgrade_file_left'] = $list;
		self::$modules_cache[$module_name]['upgrade']['available_upgrade'] = count($list);
		return (bool)count($list);
	}

	/**
	 * Return the status of the upgraded module
	 *
	 * @static
	 * @param $module_name
	 * @return bool
	 */
	public static function getUpgradeStatus($module_name)
	{
		return (isset(self::$modules_cache[$module_name]) &&
			self::$modules_cache[$module_name]['upgrade']['success']);
	}

	/**
	 * Delete module from datable
	 *
	 * @return boolean result
	 */
	public function uninstall()
	{
		// Check module installation id validation
		if (!Validate::isUnsignedId($this->id))
		{
			$this->_errors[] = $this->l('The module is not installed.');
			return false;
		}

		// Uninstall overrides
		if (!$this->uninstallOverrides())
			return false;

		// Retrieve hooks used by the module
		$sql = 'SELECT `id_hook` FROM `'._DB_PREFIX_.'hook_module` WHERE `id_module` = '.(int)$this->id;
		$result = Db::getInstance()->executeS($sql);
		foreach	($result as $row)
		{
			$this->unregisterHook((int)$row['id_hook']);
			$this->unregisterExceptions((int)$row['id_hook']);
		}

		// Disable the module for all shops
		$this->disable(true);

		// Delete permissions module access
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_access` WHERE `id_module` = '.(int)$this->id);

		// Remove restrictions for client groups
		Group::truncateRestrictionsByModule($this->id);

		// Uninstall the module
		if (Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module` WHERE `id_module` = '.(int)$this->id))
		{
			Cache::clean('Module::isInstalled'.$this->name);
			return true;
		}
		
		return false;
	}
	
	/**
	 * This function enable module $name. If an $name is an array,
	 * this will enable all of them
	 *
	 * @param array|string $name
	 * @return true if succeed
	 * @since 1.4.1
	 */
	public static function enableByName($name)
	{
		// If $name is not an array, we set it as an array
		if (!is_array($name))
			$name = array($name);
		$res = true;
		// Enable each module
		foreach ($name as $n)
			if (Validate::isModuleName($n))
				$res &= Module::getInstanceByName($n)->enable();
		return $res;
	}

	/**
	 * Activate current module.
	 *
	 * @param bool $forceAll If true, enable module for all shop
	 */
	public function enable($forceAll = false)
	{
		// Retrieve all shops where the module is enabled
		$list = Shop::getContextListShopID();
		$sql = 'SELECT `id_shop` FROM `'._DB_PREFIX_.'module_shop`
				WHERE `id_module` = '.$this->id.
				((!$forceAll) ? ' AND `id_shop` IN('.implode(', ', $list).')' : '');

		// Store the results in an array
		$items = array();
		if ($results = Db::getInstance($sql)->executeS($sql))
			foreach ($results as $row)
				$items[] = $row['id_shop'];

		// Enable module in the shop where it is not enabled yet
		foreach ($list as $id)
			if (!in_array($id, $items))
				Db::getInstance()->insert('module_shop', array(
					'id_module' =>	$this->id,
					'id_shop' =>	$id,
				));

		return true;
	}

	/**
	 * This function disable module $name. If an $name is an array,
	 * this will disable all of them
	 *
	 * @param array|string $name
	 * @return true if succeed
	 * @since 1.4.1
	 */
	public static function disableByName($name)
	{
		// If $name is not an array, we set it as an array
		if (!is_array($name))
			$name = array($name);
		$res = true;
		// Disable each module
		foreach ($name as $n)
			if (Validate::isModuleName($n))
				$res &= Module::getInstanceByName($n)->disable();
		return $res;
	}

	/**
	 * Desactivate current module.
	 *
	 * @param bool $forceAll If true, disable module for all shop
	 */
	public function disable($forceAll = false)
	{
		// Disable module for all shops
		$sql = 'DELETE FROM `'._DB_PREFIX_.'module_shop` WHERE `id_module` = '.(int)$this->id.' '.((!$forceAll) ? ' AND `id_shop` IN('.implode(', ', Shop::getContextListShopID()).')' : '');
		Db::getInstance()->execute($sql);
	}

	/**
	  * Display flags in forms for translations
	  *
	  * @param array $languages All languages available
	  * @param integer $default_language Default language id
	  * @param string $ids Multilingual div ids in form
	  * @param string $id Current div id]
	  * @param boolean $return define the return way : false for a display, true for a return
	  * @param boolean $use_vars_instead_of_ids use an js vars instead of ids seperate by "¤"
	  */
	public function displayFlags($languages, $default_language, $ids, $id, $return = false, $use_vars_instead_of_ids = false)
	{
		if (count($languages) == 1)
			return false;

		$output = '
		<div class="displayed_flag">
			<img src="../img/l/'.$default_language.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="toggleLanguageFlags(this);" alt="" />
		</div>
		<div id="languages_'.$id.'" class="language_flags">
			'.$this->l('Choose language:').'<br /><br />';
		foreach ($languages as $language)
			if ($use_vars_instead_of_ids)
				$output .= '<img src="../img/l/'.(int)$language['id_lang'].'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', '.$ids.', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
			else
			$output .= '<img src="../img/l/'.(int)$language['id_lang'].'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
		$output .= '</div>';

		if ($return)
			return $output;
		echo $output;
	}

	/**
	 * Connect module to a hook
	 *
	 * @param string $hook_name Hook name
	 * @param array $shop_list List of shop linked to the hook (if null, link hook to all shops)
	 * @return boolean result
	 */
	public function registerHook($hook_name, $shop_list = null)
	{
		// Check hook name validation and if module is installed
		if (!Validate::isHookName($hook_name))
			throw new PrestaShopException('Invalid hook name');
		if (!isset($this->id) || !is_numeric($this->id))
			return false;

		// Retrocompatibility
		if ($alias = Hook::getRetroHookName($hook_name))
			$hook_name = $alias;

		Hook::exec('actionModuleRegisterHookBefore', array('object' => $this, 'hook_name' => $hook_name));
		// Get hook id
		$id_hook = Hook::getIdByName($hook_name);

		// If hook does not exist, we create it
		if (!$id_hook)
		{
			$new_hook = new Hook();
			$new_hook->name = pSQL($hook_name);
			$new_hook->title = pSQL($hook_name);
			$new_hook->add();
			$id_hook = $new_hook->id;
			if (!$id_hook)
				return false;
		}

		// If shop lists is null, we fill it with all shops
		if (is_null($shop_list))
			$shop_list = Shop::getShops(true, null, true);

		$return = true;
		foreach ($shop_list as $shop_id)
		{
			// Check if already register
			$sql = 'SELECT hm.`id_module`
				FROM `'._DB_PREFIX_.'hook_module` hm, `'._DB_PREFIX_.'hook` h
				WHERE hm.`id_module` = '.(int)($this->id).' AND h.`id_hook` = '.$id_hook.'
				AND h.`id_hook` = hm.`id_hook` AND `id_shop` = '.(int)$shop_id;
			if (Db::getInstance()->getRow($sql))
				continue;

			// Get module position in hook
			$sql = 'SELECT MAX(`position`) AS position
				FROM `'._DB_PREFIX_.'hook_module`
				WHERE `id_hook` = '.(int)$id_hook.' AND `id_shop` = '.(int)$shop_id;
			if (!$position = Db::getInstance()->getValue($sql))
				$position = 0;

			// Register module in hook
			$return &= Db::getInstance()->insert('hook_module', array(
				'id_module' => (int)$this->id,
				'id_hook' => (int)$id_hook,
				'id_shop' => (int)$shop_id,
				'position' => (int)($position + 1),
			));
		}

		Hook::exec('actionModuleRegisterHookAfter', array('object' => $this, 'hook_name' => $hook_name));
		return $return;
	}

	/**
	  * Unregister module from hook
	  *
	  * @param mixed $id_hook Hook id (can be a hook name since 1.5.0)
	  * @param array $shop_list List of shop
	  * @return boolean result
	  */
	public function unregisterHook($hook_id, $shop_list = null)
	{
		// Get hook id if a name is given as argument
		if (!is_numeric($hook_id))
		{
			$hook_name = (int)$hook_id;
			// Retrocompatibility
			$hook_id = Hook::getIdByName($hook_id);
			if (!$hook_id)
				return false;
		}
		else
			$hook_name = Hook::getNameById((int)$hook_id);

		Hook::exec('actionModuleUnRegisterHookBefore', array('object' => $this, 'hook_name' => $hook_name));

		// Unregister module on hook by id
		$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_module` = '.(int)$this->id.' AND `id_hook` = '.(int)$hook_id
			.(($shop_list) ? ' AND `id_shop` IN('.implode(', ', $shop_list).')' : '');
		$result = Db::getInstance()->execute($sql);

		// Clean modules position
		$this->cleanPositions($hook_id, $shop_list);

		Hook::exec('actionModuleUnRegisterHookAfter', array('object' => $this, 'hook_name' => $hook_name));

		return $result;
	}

	/**
	  * Unregister exceptions linked to module
	  *
	  * @param int $id_hook Hook id
	  * @param array $shop_list List of shop
	  * @return boolean result
	  */
	public function unregisterExceptions($hook_id, $shop_list = null)
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module_exceptions`
			WHERE `id_module` = '.(int)$this->id.' AND `id_hook` = '.(int)$hook_id
			.(($shop_list) ? ' AND `id_shop` IN('.implode(', ', $shop_list).')' : '');
		return Db::getInstance()->execute($sql);
	}

	/**
	  * Add exceptions for module->Hook
	  *
	  * @param int $id_hook Hook id
	  * @param array $excepts List of file name
	  * @param array $shop_list List of shop
	  * @return boolean result
	  */
	public function registerExceptions($id_hook, $excepts, $shop_list = null)
	{
		// If shop lists is null, we fill it with all shops
		if (is_null($shop_list))
			$shop_list = Shop::getContextListShopID();

		// Save modules exception for each shop
		foreach ($shop_list as $shop_id)
		{
			foreach ($excepts as $except)
			{
				if (!$except)
					continue;
				$insertException = array(
					'id_module' => (int)$this->id,
					'id_hook' => (int)$id_hook,
					'id_shop' => (int)$shop_id,
					'file_name' => pSQL($except),
				);
				$result = Db::getInstance()->insert('hook_module_exceptions', $insertException);
				if (!$result)
					return false;
			}
		}
		return true;
	}

	/**
	  * Edit exceptions for module->Hook
	  *
	  * @param int $hookID Hook id
	  * @param array $excepts List of shopID and file name
	  * @return boolean result
	  */
	public function editExceptions($id_hook, $excepts)
	{
		$result = true;
		foreach ($excepts as $shop_id => $except)
		{
			$shop_list = ($shop_id == 0) ? Shop::getContextListShopID() : array($shop_id);
			$this->unregisterExceptions($id_hook, $shop_list);
			$result &= $this->registerExceptions($id_hook, $except, $shop_list);
		}

		return $result;
	}


	/**
	 * This function is used to determine the module name
	 * of an AdminTab which belongs to a module, in order to keep translation
	 * related to a module in its directory (instead of $_LANGADM)
	 *
	 * @param mixed $currentClass the
	 * @return boolean|string if the class belongs to a module, will return the module name. Otherwise, return false.
	 */
	public static function getModuleNameFromClass($currentClass)
	{
		// Module can now define AdminTab keeping the module translations method,
		// i.e. in modules/[module name]/[iso_code].php
		if (!isset(self::$classInModule[$currentClass]) && class_exists($currentClass))
		{
			global $_MODULES;
			$_MODULE = array();
			$reflectionClass = new ReflectionClass($currentClass);
			$filePath = realpath($reflectionClass->getFileName());
			$realpathModuleDir = realpath(_PS_MODULE_DIR_);
			if (substr(realpath($filePath), 0, strlen($realpathModuleDir)) == $realpathModuleDir)
			{
				// For controllers in module/controllers path
				if (basename(dirname(dirname($filePath))) == 'controllers')
					self::$classInModule[$currentClass] = basename(dirname(dirname(dirname($filePath))));
				// For old AdminTab controllers
				else
					self::$classInModule[$currentClass] = substr(dirname($filePath), strlen($realpathModuleDir) + 1);

				$file = _PS_MODULE_DIR_.self::$classInModule[$currentClass].'/'.Context::getContext()->language->iso_code.'.php';
				if (Tools::file_exists_cache($file) && include_once($file))
					$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;
			}
			else
				self::$classInModule[$currentClass] = false;
		}

		// return name of the module, or false
		return self::$classInModule[$currentClass];
	}

	/**
	  * Return an instance of the specified module
	  *
	  * @param string $module_name Module name
	  * @return Module
	  */
	public static function getInstanceByName($module_name)
	{
		if (!Validate::isModuleName($module_name))
		{
			if (_PS_MODE_DEV_)
				die(Tools::displayError($module_name.' is not a valid module name.'));
			return false;
		}

		if (!isset(self::$_INSTANCE[$module_name]))
		{
			if (Tools::file_exists_cache(_PS_MODULE_DIR_.$module_name.'/'.$module_name.'.php'))
			{
				include_once(_PS_MODULE_DIR_.$module_name.'/'.$module_name.'.php');

				if (class_exists($module_name, false))
					return self::$_INSTANCE[$module_name] = new $module_name;
			}
			return false;
		}
		return self::$_INSTANCE[$module_name];
	}

	/**
	  * Return an instance of the specified module
	  *
	  * @param integer $id_module Module ID
	  * @return Module instance
	  */
	public static function getInstanceById($id_module)
	{
		static $id2name = null;

		if (is_null($id2name))
		{
			$id2name = array();
			$sql = 'SELECT `id_module`, `name` FROM `'._DB_PREFIX_.'module`';
			if ($results = Db::getInstance()->executeS($sql))
				foreach ($results as $row)
					$id2name[$row['id_module']] = $row['name'];
		}

		if (isset($id2name[$id_module]))
			return Module::getInstanceByName($id2name[$id_module]);

		return false;
	}

	public static function configXmlStringFormat($string)
	{
		return str_replace('\'', '\\\'', Tools::htmlentitiesDecodeUTF8($string));
	}


	public static function getModuleName($module)
	{
		// Config file
		$configFile = _PS_MODULE_DIR_.$module.'/config.xml';
		if (!file_exists($configFile))
			return 'Module '.ucfirst($module);

		// Load config.xml
		libxml_use_internal_errors(true);
		$xml_module = simplexml_load_file($configFile);
		foreach (libxml_get_errors() as $error)
		{
			libxml_clear_errors();
			return 'Module '.ucfirst($module);
		}
		libxml_clear_errors();

		// Find translations
		global $_MODULES;
		$file = _PS_MODULE_DIR_.$module.'/'.Context::getContext()->language->iso_code.'.php';
		if (Tools::file_exists_cache($file) && include_once($file))
			if (isset($_MODULE) && is_array($_MODULE))
				$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

		// Return Name
		return Translate::getModuleTranslation((string)$xml_module->name, Module::configXmlStringFormat($xml_module->displayName), (string)$xml_module->name);
	}


	/**
	 * Return available modules
	 *
	 * @param boolean $useConfig in order to use config.xml file in module dir
	 * @return array Modules
	 */
	public static function getModulesOnDisk($useConfig = false, $loggedOnAddons = false, $id_employee = false)
	{
		global $_MODULES;

		// Init var
		$module_list = array();
		$module_name_list = array();
		$modulesNameToCursor = array();
		$errors = array();

		// Get modules directory list and memory limit
		$modules_dir = Module::getModulesDirOnDisk();
		$memory_limit = Tools::getMemoryLimit();
		
		$modules_installed = array();
		$result = Db::getInstance()->executeS('
		SELECT name, version, interest
		FROM `'._DB_PREFIX_.'module`
		LEFT JOIN `'._DB_PREFIX_.'module_preference` ON (`module` = `name` AND `id_employee` = '.(int)$id_employee.')');
		foreach ($result as $row)
			$modules_installed[$row['name']] = $row;

		foreach ($modules_dir as $module)
		{
			// Memory usage checking
			if (function_exists('memory_get_usage') && $memory_limit != '-1')
			{
				$current_memory = memory_get_usage(true);
				// memory_threshold in MB
				$memory_threshold = (Tools::isX86_64arch() ? 3 : 1.5);
				if (($memory_limit - $current_memory) <= ($memory_threshold * 1024 * 1024))
				{
					$errors[] = Tools::displayError('All modules cannot be loaded due to memory limit restrictions, please increase your memory_limit value on your server configuration');
					break;
				}
			}

			// Check if config.xml module file exists and if it's not outdated
			$configFile = _PS_MODULE_DIR_.$module.'/config.xml';
			$xml_exist = file_exists($configFile);
			if ($xml_exist)
				$needNewConfigFile = (filemtime($configFile) < filemtime(_PS_MODULE_DIR_.$module.'/'.$module.'.php'));
			else
				$needNewConfigFile = true;

			// If config.xml exists and that the use config flag is at true
			if ($useConfig && $xml_exist)
			{
				// Load config.xml
				libxml_use_internal_errors(true);
				$xml_module = simplexml_load_file($configFile);
				foreach (libxml_get_errors() as $error)
					$errors[] = '['.$module.'] '.Tools::displayError('Error found in config file:').' '.htmlentities($error->message);
				libxml_clear_errors();

				// If no errors in Xml, no need instand and no need new config.xml file, we load only translations
				if (!count($errors) && (int)$xml_module->need_instance == 0 && !$needNewConfigFile)
				{
					$file = _PS_MODULE_DIR_.$module.'/'.Context::getContext()->language->iso_code.'.php';
					if (Tools::file_exists_cache($file) && include_once($file))
						if (isset($_MODULE) && is_array($_MODULE))
							$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

					$item = new stdClass();
					$item->id = 0;
					$item->warning = '';
					foreach ($xml_module as $k => $v)
						$item->$k = (string)$v;
					$item->displayName = stripslashes(Translate::getModuleTranslation((string)$xml_module->name, Module::configXmlStringFormat($xml_module->displayName), (string)$xml_module->name));
					$item->description = stripslashes(Translate::getModuleTranslation((string)$xml_module->name, Module::configXmlStringFormat($xml_module->description), (string)$xml_module->name));
					$item->author = stripslashes(Translate::getModuleTranslation((string)$xml_module->name, Module::configXmlStringFormat($xml_module->author), (string)$xml_module->name));

					if (isset($xml_module->confirmUninstall))
						$item->confirmUninstall = Translate::getModuleTranslation((string)$xml_module->name, Module::configXmlStringFormat($xml_module->confirmUninstall), (string)$xml_module->name);

					$item->active = 0;
					$item->onclick_option = false;
					
					$module_list[] = $item;
					$module_name_list[] = '\''.pSQL($item->name).'\'';
					$modulesNameToCursor[strval($item->name)] = $item;
				}
			}

			// If use config flag is at false or config.xml does not exist OR need instance OR need a new config.xml file
			if (!$useConfig || !$xml_exist || (isset($xml_module->need_instance) && (int)$xml_module->need_instance == 1) || $needNewConfigFile)
			{
				// If class does not exists, we include the file
				if (!class_exists($module, false))
				{
					// Get content from php file
					$filepath = _PS_MODULE_DIR_.$module.'/'.$module.'.php';
					$file = trim(file_get_contents(_PS_MODULE_DIR_.$module.'/'.$module.'.php'));
					if (substr($file, 0, 5) == '<?php')
						$file = substr($file, 5);
					if (substr($file, -2) == '?>')
						$file = substr($file, 0, -2);

					// If (false) is a trick to not load the class with "eval".
					// This way require_once will works correctly
					if (eval('if (false){	'.$file.' }') !== false)
						require_once( _PS_MODULE_DIR_.$module.'/'.$module.'.php' );
					else
						$errors[] = sprintf(Tools::displayError('%1$s (parse error in %2$s)'), $module, substr($filepath, strlen(_PS_ROOT_DIR_)));
				}

				// If class exists, we just instanciate it
				if (class_exists($module, false))
				{
					$tmp_module = new $module;

					$item = new stdClass();
					$item->id = $tmp_module->id;
					$item->warning = $tmp_module->warning;
					$item->name = $tmp_module->name;
					$item->version = $tmp_module->version;
					$item->tab = $tmp_module->tab;
					$item->displayName = $tmp_module->displayName;
					$item->description = stripslashes($tmp_module->description);
					$item->author = $tmp_module->author;
					$item->limited_countries = $tmp_module->limited_countries;
					$item->parent_class = get_parent_class($module);
					$item->is_configurable = $tmp_module->is_configurable = method_exists($tmp_module, 'getContent') ? 1 : 0;
					$item->need_instance = isset($tmp_module->need_instance) ? $tmp_module->need_instance : 0;
					$item->active = $tmp_module->active;
					$item->currencies = isset($tmp_module->currencies) ? $tmp_module->currencies : null;
					$item->currencies_mode = isset($tmp_module->currencies_mode) ? $tmp_module->currencies_mode : null;
					$item->confirmUninstall = isset($tmp_module->confirmUninstall) ? $tmp_module->confirmUninstall : null;
					
					$item->onclick_option  = method_exists($module, 'onclickOption') ? true : false;
					if ($item->onclick_option)
					{
						$href = Context::getContext()->link->getAdminLink('Module', true).'&module_name='.$tmp_module->name.'&tab_module='.$tmp_module->tab;
						$item->onclick_option_content = array();
						$option_tab = array('desactive', 'reset', 'configure', 'delete');
						foreach ($option_tab as $opt)
							$item->onclick_option_content[$opt] = $tmp_module->onclickOption($opt, $href);					
					}
					
					
					$module_list[] = $item;
					if (!$xml_exist || $needNewConfigFile)
					{
						self::$_generate_config_xml_mode = true;
						$tmp_module->_generateConfigXml();
						self::$_generate_config_xml_mode = false;
					}
					unset($tmp_module);
				}
				else
					$errors[] = sprintf(Tools::displayError('%1$s (class missing in %2$s)'), $module, substr($filepath, strlen(_PS_ROOT_DIR_)));
			}
		}

		// Get modules information from database
		if (!empty($module_name_list))
		{
			$list = Shop::getContextListShopID();

			$sql = 'SELECT m.id_module, m.name, (
						SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE m.id_module = ms.id_module AND ms.id_shop IN ('.implode(',', $list).')
					) as total
					FROM '._DB_PREFIX_.'module m
					WHERE m.name IN ('.implode(',', $module_name_list).')';
			$results = Db::getInstance()->executeS($sql);
			foreach ($results as $result)
			{
				$moduleCursor = $modulesNameToCursor[$result['name']];
				$moduleCursor->id = $result['id_module'];
				$moduleCursor->active = ($result['total'] == count($list)) ? 1 : 0;
			}
		}

		// Get Default Country Modules and customer module
		$files_list = array(
			array('type' => 'addonsNative', 'file' => _PS_ROOT_DIR_.self::CACHE_FILE_DEFAULT_COUNTRY_MODULES_LIST, 'loggedOnAddons' => 0),
			array('type' => 'addonsBought', 'file' => _PS_ROOT_DIR_.self::CACHE_FILE_CUSTOMER_MODULES_LIST, 'loggedOnAddons' => 1),
			array('type' => 'addonsMustHave', 'file' => _PS_ROOT_DIR_.self::CACHE_FILE_MUST_HAVE_MODULES_LIST, 'loggedOnAddons' => 0),
		);
		foreach ($files_list as $f)
			if (file_exists($f['file']) && ($f['loggedOnAddons'] == 0 || $loggedOnAddons))
			{
				$file = $f['file'];
				$content = Tools::file_get_contents($file);
				$xml = @simplexml_load_string($content, null, LIBXML_NOCDATA);
				if ($xml && isset($xml->module))
					foreach ($xml->module as $modaddons)
					{
						$flag_found = 0;
						foreach ($module_list as $k => $m)
							if ($m->name == $modaddons->name && !isset($m->available_on_addons))
							{
								$flag_found = 1;
								if ($m->version != $modaddons->version && version_compare($m->version, $modaddons->version) === -1)
									$module_list[$k]->version_addons = $modaddons->version;
							}
						if ($flag_found == 0)
						{
							$item = new stdClass();
							$item->id = 0;
							$item->warning = '';
							$item->type = strip_tags((string)$f['type']);
							$item->name = strip_tags((string)$modaddons->name);
							$item->version = strip_tags((string)$modaddons->version);
							$item->tab = strip_tags((string)$modaddons->tab);
							$item->displayName = strip_tags((string)$modaddons->displayName).' (Addons)';
							$item->description = stripslashes(strip_tags((string)$modaddons->description));
							$item->author = strip_tags((string)$modaddons->author);
							$item->limited_countries = array();
							$item->parent_class = '';
							$item->onclick_option = false;
							$item->is_configurable = 0;
							$item->need_instance = 0;
							$item->not_on_disk = 1;
							$item->available_on_addons = 1;
							$item->active = 0;
							if (isset($modaddons->img))
							{
								if (!file_exists(_PS_TMP_IMG_DIR_.md5($modaddons->name).'.jpg'))
									if (!file_put_contents(_PS_TMP_IMG_DIR_.md5($modaddons->name).'.jpg', Tools::file_get_contents($modaddons->img)))
										copy(_PS_IMG_DIR_.'404.gif', _PS_TMP_IMG_DIR_.md5($modaddons->name).'.jpg');
								if (file_exists(_PS_TMP_IMG_DIR_.md5($modaddons->name).'.jpg'))
									$item->image = '../img/tmp/'.md5($modaddons->name).'.jpg';
							}
							if ($item->type == 'addonsMustHave')
							{
								$item->addons_buy_url = strip_tags((string)$modaddons->url);
								$prices = (array)$modaddons->price;
								$id_default_currency = Configuration::get('PS_CURRENCY_DEFAULT');
								foreach ($prices as $currency => $price)
									if ($id_currency = Currency::getIdByIsoCode($currency))
									{
										$item->price = (float)$price;
										$item->id_currency = (int)$id_currency;
										if ($id_default_currency == $id_currency)
											break;
									}
							}
							$module_list[] = $item;
						}
					}
			}
			
		foreach ($module_list as &$module)
			if (isset($modules_installed[$module->name]))
			{
				$module->installed = true;
				$module->database_version = $modules_installed[$module->name]['version'];
				$module->interest = $modules_installed[$module->name]['interest'];
			}
			else
			{
				$module->installed = false;
				$module->database_version = 0;
				$module->interest = 0;
			}

		usort($module_list, create_function('$a,$b', '
			if ($a->displayName == $b->displayName)
				return 0;
			return ($a->displayName < $b->displayName) ? -1 : 1;
		'));

		if ($errors)
		{
			echo '<div class="alert error"><h3>'.Tools::displayError('The following module(s) could not be loaded').':</h3><ol>';
			foreach ($errors as $error)
				echo '<li>'.$error.'</li>';
			echo '</ol></div>';
		}

		return $module_list;
	}

	/**
	 * Return modules directory list
	 *
	 * @return array Modules Directory List
	 */
	public static function getModulesDirOnDisk()
	{
		$module_list = array();
		$modules = scandir(_PS_MODULE_DIR_);
		foreach ($modules as $name)
		{
			if (is_dir(_PS_MODULE_DIR_.$name) && Tools::file_exists_cache(_PS_MODULE_DIR_.$name.'/'.$name.'.php'))
			{
				if (!Validate::isModuleName($name))
					throw new PrestaShopException(sprintf('Module %s is not a valid module name', $name));
				$module_list[] = $name;
			}
		}
		return $module_list;
	}


	/**
	 * Return non native module
	 *
	 * @param int $position Take only positionnables modules
	 * @return array Modules
	 */
	public static function getNonNativeModuleList()
	{
		$db = Db::getInstance();

		$module_list_xml = _PS_ROOT_DIR_.self::CACHE_FILE_MODULES_LIST;
		$native_modules = simplexml_load_file($module_list_xml);
		$native_modules = $native_modules->modules;
		foreach ($native_modules as $native_modules_type)
			if (in_array($native_modules_type['type'], array('native', 'partner')))
			{
				$arr_native_modules[] = '""';
				foreach ($native_modules_type->module as $module)
					$arr_native_modules[] = '"'.pSQL($module['name']).'"';
			}

		return $db->executeS('SELECT * FROM `'._DB_PREFIX_.'module` m WHERE `name` NOT IN ('.implode(',', $arr_native_modules).') ');
	}

	/**
	 * Return installed modules
	 *
	 * @param int $position Take only positionnables modules
	 * @return array Modules
	 */
	public static function getModulesInstalled($position = 0)
	{
		$sql = 'SELECT m.* FROM `'._DB_PREFIX_.'module` m ';
		if ($position)
			$sql .= 'LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON m.`id_module` = hm.`id_module`
				 LEFT JOIN `'._DB_PREFIX_.'hook` k ON hm.`id_hook` = k.`id_hook`
				 WHERE k.`position` = 1
				 GROUP BY m.id_module';
		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Execute modules for specified hook
	 *
	 * @param string $hook_name Hook Name
	 * @param array $hookArgs Parameters for the functions
	 * @return string modules output
	 */
	public static function hookExec($hook_name, $hookArgs = array(), $id_module = null)
	{
		Tools::displayAsDeprecated();
		return Hook::exec($hook_name, $hookArgs, $id_module);
	}

	public static function hookExecPayment()
	{
		Tools::displayAsDeprecated();
		return Hook::exec('displayPayment');
	}

	public static function preCall($module_name)
	{
		return true;
	}
	
	public static function getPaypalIgnore()
	{
		$iso_code = Country::getIsoById((int)Configuration::get('PS_COUNTRY_DEFAULT'));
		$paypal_countries = array('ES', 'FR', 'PL', 'IT');
		if (Context::getContext()->getMobileDevice() && Context::getContext()->shop->getTheme() == 'default' && in_array($iso_code, $paypal_countries))
			return 'm.`name` = \'paypal\'';
	}

	/**
	 * Returns the list of the payment module associated to the current customer
	 * @see PaymentModule::getInstalledPaymentModules() if you don't care about the context
	 *
	 * @return array module informations
	 */
	public static function getPaymentModules()
	{
		$context = Context::getContext();
		if (isset($context->cart))
			$billing = new Address((int)$context->cart->id_address_invoice);

		$frontend = true;
		$groups = array();
		if (isset($context->employee))
			$frontend = false;
		elseif (isset($context->customer))
		{
			$groups = $context->customer->getGroups();
			if (empty($groups))
				$groups = array(Configuration::get('PS_UNIDENTIFIED_GROUP'));
		}

		$hookPayment = 'Payment';
		if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\''))
			$hookPayment = 'displayPayment';

		$list = Shop::getContextListShopID();
		if ($paypal_condition = Module::getPaypalIgnore())
			$paypal_condition = ' AND '.$paypal_condition;
			
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
		FROM `'._DB_PREFIX_.'module` m
		'.($frontend ? 'LEFT JOIN `'._DB_PREFIX_.'module_country` mc ON (m.`id_module` = mc.`id_module` AND mc.id_shop = '.(int)$context->shop->id.')' : '').'
		'.($frontend ? 'INNER JOIN `'._DB_PREFIX_.'module_group` mg ON (m.`id_module` = mg.`id_module` AND mg.id_shop = '.(int)$context->shop->id.')' : '').'
		'.($frontend && isset($context->customer) ? 'INNER JOIN `'._DB_PREFIX_.'customer_group` cg on (cg.`id_group` = mg.`id_group`AND cg.`id_customer` = '.(int)$context->customer->id.')' : '').'
		LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
		LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
		WHERE h.`name` = \''.pSQL($hookPayment).'\'
		'.(isset($billing) && $frontend ? 'AND mc.id_country = '.(int)$billing->id_country : '').'
		AND (SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', $list).')) = '.count($list).'
		AND hm.id_shop IN('.implode(', ', $list).')
		'.(count($groups) && $frontend ? 'AND (mg.`id_group` IN('.implode(', ', $groups).'))' : '').$paypal_condition.'
		GROUP BY hm.id_hook, hm.id_module
		ORDER BY hm.`position`, m.`name` DESC');
	}

	/**
	 * @deprecated 1.5.0 Use Translate::getModuleTranslation()
	 */
	public static function findTranslation($name, $string, $source)
	{
		return Translate::getModuleTranslation($name, $string, $source);
	}

	/**
	 * Get translation for a given module text
	 *
	 * Note: $specific parameter is mandatory for library files.
	 * Otherwise, translation key will not match for Module library
	 * when module is loaded with eval() Module::getModulesOnDisk()
	 *
	 * @param string $string String to translate
	 * @param boolean|string $specific filename to use in translation key
	 * @return string Translation
	 */
	public function l($string, $specific = false)
	{
		if (self::$_generate_config_xml_mode)
			return $string;

		return Translate::getModuleTranslation($this, $string, ($specific) ? $specific : $this->name);
	}

	/*
	 * Reposition module
	 *
	 * @param boolean $id_hook Hook ID
	 * @param boolean $way Up (1) or Down (0)
	 * @param int $position
	 */
	public function updatePosition($id_hook, $way, $position = null)
	{
		foreach (Shop::getContextListShopID() as $shop_id)
		{
			$sql = 'SELECT hm.`id_module`, hm.`position`, hm.`id_hook`
					FROM `'._DB_PREFIX_.'hook_module` hm
					WHERE hm.`id_hook` = '.(int)$id_hook.' AND hm.`id_shop` = '.$shop_id.'
					ORDER BY hm.`position` '.($way ? 'ASC' : 'DESC');
			if (!$res = Db::getInstance()->executeS($sql))
				continue;

			foreach ($res as $key => $values)
				if ((int)$values[$this->identifier] == (int)$this->id)
				{
					$k = $key;
					break;
				}
			if (!isset($k) || !isset($res[$k]) || !isset($res[$k + 1]))
				return false;

			$from = $res[$k];
			$to = $res[$k + 1];

			if (isset($position) && !empty($position))
				$to['position'] = (int)$position;

			$sql = 'UPDATE `'._DB_PREFIX_.'hook_module`
				SET `position`= position '.($way ? '-1' : '+1').'
				WHERE position between '.(int)(min(array($from['position'], $to['position']))).' AND '.max(array($from['position'], $to['position'])).'
				AND `id_hook` = '.(int)$from['id_hook'].' AND `id_shop` = '.$shop_id;
			if (!Db::getInstance()->execute($sql))
				return false;

			$sql = 'UPDATE `'._DB_PREFIX_.'hook_module`
				SET `position`='.(int)$to['position'].'
				WHERE `'.pSQL($this->identifier).'` = '.(int)$from[$this->identifier].'
				AND `id_hook` = '.(int)$to['id_hook'].' AND `id_shop` = '.$shop_id;
			if (!Db::getInstance()->execute($sql))
				return false;
		}
		return true;
	}

	/*
	 * Reorder modules position
	 *
	 * @param boolean $id_hook Hook ID
	 * @param array $shop_list List of shop
	 */
	public function cleanPositions($id_hook, $shop_list = null)
	{
		$sql = 'SELECT `id_module`, `id_shop`
			FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_hook` = '.(int)$id_hook.'
			'.((!is_null($shop_list) && $shop_list) ? ' AND `id_shop` IN('.implode(', ', $shop_list).')' : '').'
			ORDER BY `position`';
		$results = Db::getInstance()->executeS($sql);
		$position = array();
		foreach ($results as $row)
		{
			if (!isset($position[$row['id_shop']]))
				$position[$row['id_shop']] = 1;

			$sql = 'UPDATE `'._DB_PREFIX_.'hook_module`
				SET `position` = '.$position[$row['id_shop']].'
				WHERE `id_hook` = '.(int)$id_hook.'
				AND `id_module` = '.$row['id_module'].' AND `id_shop` = '.$row['id_shop'];
			Db::getInstance()->execute($sql);
			$position[$row['id_shop']]++;
		}

		return true;
	}

	public function displayError($error)
	{
	 	$output = '
		<div class="module_error alert error">
			'.$error.'
		</div>';
		$this->error = true;
		return $output;
	}

	public function displayConfirmation($string)
	{
	 	$output = '
		<div class="module_confirmation conf confirm">
			'.$string.'
		</div>';
		return $output;
	}

	/*
	 * Return exceptions for module in hook
	 *
	 * @param int $id_hook Hook ID
	 * @return array Exceptions
	 */
	protected static $exceptionsCache = null;
	public function getExceptions($hookID, $dispatch = false)
	{
		if (self::$exceptionsCache === null)
		{
			self::$exceptionsCache = array();
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'hook_module_exceptions`
				WHERE `id_shop` IN ('.implode(', ', Shop::getContextListShopID()).')';
			$result = Db::getInstance()->executeS($sql);
			foreach ($result as $row)
			{
				if (!$row['file_name'])
					continue;
				$key = $row['id_hook'].'-'.$row['id_module'];
				if (!isset(self::$exceptionsCache[$key]))
					self::$exceptionsCache[$key] = array();
				if (!isset(self::$exceptionsCache[$key][$row['id_shop']]))
					self::$exceptionsCache[$key][$row['id_shop']] = array();
				self::$exceptionsCache[$key][$row['id_shop']][] = $row['file_name'];
			}
		}

		$key = $hookID.'-'.$this->id;
		if (!$dispatch)
		{
			$files = array();
			foreach (Shop::getContextListShopID() as $shop_id)
				if (isset(self::$exceptionsCache[$key], self::$exceptionsCache[$key][$shop_id]))
					foreach (self::$exceptionsCache[$key][$shop_id] as $file)
						if (!in_array($file, $files))
							$files[] = $file;
			return $files;
		}
		else
		{
			$list = array();
			foreach (Shop::getContextListShopID() as $shop_id)
				if (isset(self::$exceptionsCache[$key], self::$exceptionsCache[$key][$shop_id]))
					$list[$shop_id] = self::$exceptionsCache[$key][$shop_id];
			return $list;
		}
	}

	public static function isInstalled($module_name)
	{
		if (!Cache::isStored('Module::isInstalled'.$module_name))
		{
			$id_module = Module::getModuleIdByName($module_name);
			Cache::store('Module::isInstalled'.$module_name, (bool)$id_module);
		}
		return Cache::retrieve('Module::isInstalled'.$module_name);
	}

	public static function isEnabled($module_name)
	{
		if (!Cache::isStored('Module::isEnabled'.$module_name))
		{
			$active = false;
			$id_module = Module::getModuleIdByName($module_name);
			if (Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module_shop` WHERE `id_module` = '.(int)$id_module.' AND `id_shop` = '.(int)Context::getContext()->shop->id))
				$active = true;
			Cache::store('Module::isEnabled'.$module_name, (bool)$active);
		}
		return Cache::retrieve('Module::isEnabled'.$module_name);
	}

	public function isRegisteredInHook($hook)
	{
		if (!$this->id)
			return false;

		$sql = 'SELECT COUNT(*)
			FROM `'._DB_PREFIX_.'hook_module` hm
			LEFT JOIN `'._DB_PREFIX_.'hook` h ON (h.`id_hook` = hm.`id_hook`)
			WHERE h.`name` = \''.pSQL($hook).'\' AND hm.`id_module` = '.(int)$this->id;
		return Db::getInstance()->getValue($sql);
	}

	/*
	** Template management (display, overload, cache)
	*/
	protected static function _isTemplateOverloadedStatic($module_name, $template)
	{
		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$module_name.'/'.$template))
			return true;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.$module_name.'/views/templates/hook/'.$template))
			return false;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.$module_name.'/'.$template))
			return false;
		return null;
	}

	protected function _isTemplateOverloaded($template)
	{
		return Module::_isTemplateOverloadedStatic($this->name, $template);
	}
	
	protected function getCacheId($name = null)
	{
		if ($name === null)
			$name = $this->name;
		return $name.'|'.(int)Tools::usingSecureMode().'|'.(int)$this->context->shop->id.'|'.(int)Group::getCurrent()->id.'|'.(int)$this->context->language->id.'|'.(int)$this->context->currency->id;
	}

	public function display($file, $template, $cacheId = null, $compileId = null)
	{
		if (($overloaded = Module::_isTemplateOverloadedStatic(basename($file, '.php'), $template)) === null)
			return Tools::displayError('No template found for module').' '.basename($file, '.php');
		else
		{
			$this->smarty->assign(array(
				'module_dir' =>				__PS_BASE_URI__.'modules/'.basename($file, '.php').'/',
				'module_template_dir' =>	($overloaded ? _THEME_DIR_ : __PS_BASE_URI__).'modules/'.basename($file, '.php').'/'
			));

			if ($cacheId !== null)
				Tools::enableCache();

			$smarty_subtemplate = $this->context->smarty->createTemplate(
				$this->getTemplatePath($template),
				$cacheId,
				$compileId,
				$this->smarty
			);
			$result = $smarty_subtemplate->fetch();

			if ($cacheId !== null)
				Tools::restoreCacheSettings();

			return $result;
		}
	}

	/**
	 * Get realpath of a template of current module (check if template is overriden too)
	 *
	 * @since 1.5.0
	 * @param string $template
	 * @return string
	 */
	public function getTemplatePath($template)
	{
		$overloaded = $this->_isTemplateOverloaded($template);
		if ($overloaded === null)
			return null;
		if ($overloaded)
			return _PS_THEME_DIR_.'modules/'.$this->name.'/'.$template;
		else if (file_exists(_PS_MODULE_DIR_.$this->name.'/views/templates/hook/'.$template))
			return _PS_MODULE_DIR_.$this->name.'/views/templates/hook/'.$template;
		else
			return _PS_MODULE_DIR_.$this->name.'/'.$template;
	}

	protected function _getApplicableTemplateDir($template)
	{
		return $this->_isTemplateOverloaded($template) ? _PS_THEME_DIR_ : _PS_MODULE_DIR_.$this->name.'/';
	}

	public function isCached($template, $cacheId = null, $compileId = null)
	{
		$context = Context::getContext();

		Tools::enableCache();
		$is_cached =  $context->smarty->isCached($this->getTemplatePath($template), $cacheId, $compileId);
		Tools::restoreCacheSettings();

		return $is_cached;
	}

	protected function _clearCache($template, $cache_id = null, $compile_id = null)
	{
		Tools::enableCache();
		Tools::clearCache(Context::getContext()->smarty, $this->getTemplatePath($template), $cache_id, $compile_id);
		Tools::restoreCacheSettings();
	}

	protected function _generateConfigXml()
	{
		$xml = '<?xml version="1.0" encoding="UTF-8" ?>
        <module>
            <name>'.$this->name.'</name>
            <displayName><![CDATA['.Tools::htmlentitiesUTF8($this->displayName).']]></displayName>
            <version><![CDATA['.$this->version.']]></version>
            <description><![CDATA['.Tools::htmlentitiesUTF8($this->description).']]></description>
            <author><![CDATA['.Tools::htmlentitiesUTF8($this->author).']]></author>
            <tab><![CDATA['.Tools::htmlentitiesUTF8($this->tab).']]></tab>'.(isset($this->confirmUninstall) ? "\n\t".'<confirmUninstall>'.$this->confirmUninstall.'</confirmUninstall>' : '').'
            <is_configurable>'.(isset($this->is_configurable) ? (int)$this->is_configurable : 0).'</is_configurable>
            <need_instance>'.(int)$this->need_instance.'</need_instance>'.(isset($this->limited_countries) ? "\n\t".'<limited_countries>'.(count($this->limited_countries) == 1 ? $this->limited_countries[0] : '').'</limited_countries>' : '').'
        </module>';
		if (is_writable(_PS_MODULE_DIR_.$this->name.'/'))
			file_put_contents(_PS_MODULE_DIR_.$this->name.'/config.xml', $xml);
	}

	/**
	 * Check if the module is transplantable on the hook in parameter
	 * @param string $hook_name
	 * @return bool if module can be transplanted on hook
	 */
	public function isHookableOn($hook_name)
	{
		$retro_hook_name = Hook::getRetroHookName($hook_name);
		return (is_callable(array($this, 'hook'.ucfirst($hook_name))) || is_callable(array($this, 'hook'.ucfirst($retro_hook_name))));
	}

	/**
	 * Check employee permission for module
	 * @param array $variable (action)
	 * @param object $employee
	 * @return bool if module can be transplanted on hook
	 */
	public function getPermission($variable, $employee = null)
	{
		return Module::getPermissionStatic($this->id, $variable, $employee);
	}

	/**
	 * Check employee permission for module (static method)
	 * @param integer $id_module
	 * @param array $variable (action)
	 * @param object $employee
	 * @return bool if module can be transplanted on hook
	 */
	public static function getPermissionStatic($id_module, $variable, $employee = null)
	{
		if (!in_array($variable, array('view', 'configure')))
			return false;
		if (!$employee)
			$employee = Context::getContext()->employee;

		if ($employee->id_profile == _PS_ADMIN_PROFILE_)
			return true;

		if (!isset(self::$cache_permissions[$employee->id_profile]))
		{
			self::$cache_permissions[$employee->id_profile] = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `id_module`, `view`, `configure` FROM `'._DB_PREFIX_.'module_access` WHERE `id_profile` = '.(int)$employee->id_profile);
			foreach ($result as $row)
			{
				self::$cache_permissions[$employee->id_profile][$row['id_module']]['view'] = $row['view'];
				self::$cache_permissions[$employee->id_profile][$row['id_module']]['configure'] = $row['configure'];
			}
		}

		if (!isset(self::$cache_permissions[$employee->id_profile][$id_module]))
			throw new PrestaShopException('No access reference in table module_access for id_module '.$id_module.'.');

		return (bool)self::$cache_permissions[$employee->id_profile][$id_module][$variable];
	}

	/**
	 * Get Unauthorized modules for a client group
	 * @param integer group_id
	 */
	public static function getAuthorizedModules($group_id)
	{
		return Db::getInstance()->executeS('
		SELECT m.`id_module`, m.`name` FROM `'._DB_PREFIX_.'module_group` mg
		LEFT JOIN `'._DB_PREFIX_.'module` m ON (m.`id_module` = mg.`id_module`)
		WHERE mg.`id_group` = '.(int)$group_id);
	}

	/**
	 * Get id module by name
	 * @param string name
	 * @return integer id
	 */
	public static function getModuleIdByName($name)
	{
		return Db::getInstance()->getValue('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = "'.pSQL($name).'"');
	}

	/**
	 * Get module errors
	 *
	 * @since 1.5.0
	 * @return array errors
	 */
	public function getErrors()
	{
		return $this->_errors;
	}

	/**
	 * Get module messages confirmation
	 *
	 * @since 1.5.0
	 * @return array conf
	 */
	public function getConfirmations()
	{
		return $this->_confirmations;
	}

	/**
	 * Get local path for module
	 *
	 * @since 1.5.0
	 * @return string
	 */
	public function getLocalPath()
	{
		return $this->local_path;
	}

	/**
	 * Get uri path for module
	 *
	 * @since 1.5.0
	 * @return string
	 */
	public function getPathUri()
	{
		return $this->_path;
	}

	/*
	 * Return module position for a given hook
	 *
	 * @param boolean $id_hook Hook ID
	 * @return integer position
	 */
	public function getPosition($id_hook)
	{
		if (isset(Hook::$preloadModulesFromHooks))
			if (isset(Hook::$preloadModulesFromHooks[$id_hook]))
				if (isset(Hook::$preloadModulesFromHooks[$id_hook]['module_position'][$this->id]))
					return Hook::$preloadModulesFromHooks[$id_hook]['module_position'][$this->id];
				else
					return 0;
		$result = Db::getInstance()->getRow('
			SELECT `position`
			FROM `'._DB_PREFIX_.'hook_module`
			WHERE `id_hook` = '.(int)$id_hook.'
			AND `id_module` = '.(int)$this->id.'
			AND `id_shop` = '.(int)Context::getContext()->shop->id);

		return $result['position'];
	}

	/**
	 * add a warning message to display at the top of the admin page
	 *
	 * @param string $msg
	 */
	public function adminDisplayWarning($msg)
	{
		if (!($this->context->controller instanceof AdminController))
			return false;
		$this->context->controller->warnings[] = $msg;
	}

	/**
	 * add a info message to display at the top of the admin page
	 *
	 * @param string $msg
	 */
	protected function adminDisplayInformation($msg)
	{
		if (!($this->context->controller instanceof AdminController))
			return false;
		$this->context->controller->informations[] = $msg;
	}

	/**
	 * Install overrides files for the module
	 *
	 * @return bool
	 */
	public function installOverrides()
	{
		if (!is_dir($this->getLocalPath().'override'))
			return true;

		$result = true;
		foreach (Tools::scandir($this->getLocalPath().'override', 'php', '', true) as $file)
		{
			$class = basename($file, '.php');
			if (Autoload::getInstance()->getClassPath($class.'Core'))
				$result &= $this->addOverride($class);
		}

		return $result;
	}

	/**
	 * Uninstall overrides files for the module
	 *
	 * @return bool
	 */
	public function uninstallOverrides()
	{
		if (!is_dir($this->getLocalPath().'override'))
			return true;

		$result = true;
		foreach (Tools::scandir($this->getLocalPath().'override', 'php', '', true) as $file)
		{
			$class = basename($file, '.php');
			if (Autoload::getInstance()->getClassPath($class.'Core'))
				$result &= $this->removeOverride($class);
		}
		return $result;
	}

	/**
	 * Add all methods in a module override to the override class
	 *
	 * @param string $classname
	 * @return bool
	 */
	public function addOverride($classname)
	{
		$path = Autoload::getInstance()->getClassPath($classname.'Core');

		// Check if there is already an override file, if not, we just need to copy the file
		if (!($classpath = Autoload::getInstance()->getClassPath($classname)))
		{
			$override_src = $this->getLocalPath().'override'.DIRECTORY_SEPARATOR.$path;
			$override_dest = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'override'.DIRECTORY_SEPARATOR.$path;
			if (!is_writable(dirname($override_dest)))
				throw new Exception(sprintf(Tools::displayError('directory (%s) not writable'), dirname($override_dest)));
			copy($override_src, $override_dest);
			// Re-generate the class index
			Autoload::getInstance()->generateIndex();
			return true;
		}
		
		// Check if override file is writable
		$override_path = _PS_ROOT_DIR_.'/'.Autoload::getInstance()->getClassPath($classname);
		if ((!file_exists($override_path) && !is_writable(dirname($override_path))) || (file_exists($override_path) && !is_writable($override_path)))
			throw new Exception(sprintf(Tools::displayError('file (%s) not writable'), $override_path));
			
		// Make a reflection of the override class and the module override class
		$override_file = file($override_path);
		eval(preg_replace(array('#^\s*<\?php#', '#class\s+'.$classname.'\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i'), array('', 'class '.$classname.'OverrideOriginal'), implode('', $override_file)));
		$override_class = new ReflectionClass($classname.'OverrideOriginal');

		$module_file = file($this->getLocalPath().'override'.DIRECTORY_SEPARATOR.$path);
		eval(preg_replace(array('#^\s*<\?php#', '#class\s+'.$classname.'(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'), array('', 'class '.$classname.'Override'), implode('', $module_file)));
		$module_class = new ReflectionClass($classname.'Override');

		// Check if none of the methods already exists in the override class
		foreach ($module_class->getMethods() as $method)
			if ($override_class->hasMethod($method->getName()))
				throw new Exception(sprintf(Tools::displayError('The method %1$s in the class %2$s is already overriden.'), $method->getName(), $classname));

		// Check if none of the properties already exists in the override class
		foreach ($module_class->getProperties() as $property)
			if ($override_class->hasProperty($property->getName()))
				throw new Exception(sprintf(Tools::displayError('The property %1$s in the class %2$s is already defined.'), $property->getName(), $classname));

		// Insert the methods from module override in override
		$copy_from = array_slice($module_file, $module_class->getStartLine() + 1, $module_class->getEndLine() - $module_class->getStartLine() - 2);
		array_splice($override_file, $override_class->getEndLine() - 1, 0, $copy_from);
		$code = implode('', $override_file);
		file_put_contents($override_path, $code);

		return true;
	}

	/**
	 * Remove all methods in a module override from the override class
	 *
	 * @param string $classname
	 * @return bool
	 */
	public function removeOverride($classname)
	{
		$path = Autoload::getInstance()->getClassPath($classname.'Core');

		if (!Autoload::getInstance()->getClassPath($classname))
			return true;

		// Check if override file is writable
		$override_path = _PS_ROOT_DIR_.'/'.Autoload::getInstance()->getClassPath($classname);
		if (!is_writable($override_path))
			return false;

		// Make a reflection of the override class and the module override class
		$override_file = file($override_path);
		eval(preg_replace(array('#^\s*<\?php#', '#class\s+'.$classname.'\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?#i'), array('', 'class '.$classname.'OverrideOriginal_remove'), implode('', $override_file)));
		$override_class = new ReflectionClass($classname.'OverrideOriginal_remove');

		$module_file = file($this->getLocalPath().'override/'.$path);
		eval(preg_replace(array('#^\s*<\?php#', '#class\s+'.$classname.'(\s+extends\s+([a-z0-9_]+)(\s+implements\s+([a-z0-9_]+))?)?#i'), array('', 'class '.$classname.'Override_remove'), implode('', $module_file)));
		$module_class = new ReflectionClass($classname.'Override_remove');

		// Remove methods from override file
		$override_file = file($override_path);
		foreach ($module_class->getMethods() as $method)
		{
			if (!$override_class->hasMethod($method->getName()))
				continue;

			$method = $override_class->getMethod($method->getName());
			$length = $method->getEndLine() - $method->getStartLine() + 1;
			array_splice($override_file, $method->getStartLine() - 1, $length, array_pad(array(), $length, '#--remove--#'));
		}

		// Remove properties from override file
		foreach ($module_class->getProperties() as $property)
		{
			if (!$override_class->hasProperty($property->getName()))
				continue;

			// Remplacer la ligne de declaration par "remove"
			foreach ($override_file as $line_number => &$line_content)
				if (preg_match('/(public|private|protected)\s+(static\s+)?\$'.$property->getName().'/i', $line_content))
				{
					$line_content = '#--remove--#';
					break;
				}
		}

		// Rewrite nice code
		$code = '';
		foreach ($override_file as $line)
		{
			if ($line == '#--remove--#')
				continue;

			$code .= $line;
		}
		file_put_contents($override_path, $code);

		return true;
	}
}

function ps_module_version_sort($a, $b)
{
	return version_compare($a['version'], $b['version']);
}
