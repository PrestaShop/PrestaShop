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
*  @version  Release: $Revision: 7436 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

abstract class ModuleCore
{
	/** @var integer Module ID */
	public $id = NULL;

	/** @var float Version */
	public $version;

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

	/** @var string Admin tab correponding to the module */
	public $tab = NULL;

	/** @var boolean Status */
	public $active = false;

	/** @var array current language translations */
	protected $_lang = array();

	/** @var string Module web path (eg. '/shop/modules/modulename/')  */
	protected $_path = NULL;

	/** @var string Fill it if the module is installed but not yet set up */
	public $warning;

	/** @var string Message display before uninstall a module */
	public $beforeUninstall = NULL;

	protected $_errors = false;

	protected $table = 'module';

	protected $identifier = 'id_module';

	public static $_db;

	/** @var array to store the limited country */
	public $limited_countries = array();

	protected static $modulesCache;

	protected static $_INSTANCE = array();

	protected static $_generateConfigXmlMode = false;

	protected static $l_cache = array();

	protected static $cache_permissions = array();

	/**
	 * @var array used by AdminTab to determine which lang file to use (admin.php or module lang file)
	 */
	public static $classInModule	= array();

	/** @var Context */
	protected $context;

	/**
	 * Constructor
	 *
	 * @param string $name Module unique name
	 * @param Context $context
	 */
	public function __construct($name = null, Context $context = null)
	{
		$this->context = $context ? $context : Context::getContext();

		if ($this->name == NULL)
			$this->name = $this->id;
		if ($this->name != NULL)
		{
			if (self::$modulesCache == NULL AND !is_array(self::$modulesCache))
			{
				$list = $this->context->shop->getListOfID();

				// Join clause is done to check if the module is activated in current shop context
				$sql = 'SELECT m.id_module, m.name, (
							SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE m.id_module = ms.id_module AND ms.id_shop IN ('.implode(',', $list).')
						) as total
						FROM '._DB_PREFIX_.'module m';
				self::$modulesCache = array();
				$result = Db::getInstance()->executeS($sql);
				foreach ($result as $row)
				{
					self::$modulesCache[$row['name']] = $row;
					self::$modulesCache[$row['name']]['active'] = ($row['total'] == count($list)) ? true : false;
				}
			}

			if (isset(self::$modulesCache[$this->name]))
			{
				$this->active = self::$modulesCache[$this->name]['active'];
				$this->id = self::$modulesCache[$this->name]['id_module'];
				foreach (self::$modulesCache[$this->name] AS $key => $value)
					if (key_exists($key, $this))
						$this->{$key} = $value;
				$this->_path = __PS_BASE_URI__.'modules/'.$this->name.'/';
			}
		}
	}

	protected function sqlShopRestriction($share = false, $alias = null)
	{
		return $this->context->shop->addSqlRestriction($share, $alias, 'shop');
	}

	/**
	 * Insert module into datable
	 */
	public function install()
	{
		if (!Validate::isModuleName($this->name))
			die(Tools::displayError());
		$result = Db::getInstance()->getRow('
		SELECT `id_module`
		FROM `'._DB_PREFIX_.'module`
		WHERE `name` = \''.pSQL($this->name).'\'');
		if ($result)
			return false;

		$result = Db::getInstance()->AutoExecute(_DB_PREFIX_.$this->table, array('name' => $this->name, 'active' => 1), 'INSERT');
		if (!$result)
			return false;
		$this->id = Db::getInstance()->Insert_ID();

		$this->enable(true);

		// Permissions management
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'module_access` (`id_profile`, `id_module`, `view`, `configure`) (
			SELECT id_profile, '.(int)$this->id.', 1, 1
			FROM '._DB_PREFIX_.'access a
			WHERE id_tab = (SELECT `id_tab` FROM '._DB_PREFIX_.'tab WHERE class_name = \'AdminModules\' LIMIT 1)
			AND a.`view` = 1
		)');
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'module_access` (`id_profile`, `id_module`, `view`, `configure`) (
			SELECT id_profile, '.(int)$this->id.', 1, 0
			FROM '._DB_PREFIX_.'access a
			WHERE id_tab = (SELECT `id_tab` FROM '._DB_PREFIX_.'tab WHERE class_name = \'AdminModules\' LIMIT 1)
			AND a.`view` = 0
		)');
		// Adding Restrictions for client groups
		Group::addRestrictionsForModule($this->id);

		return true;
	}

	/**
	 * Delete module from datable
	 *
	 * @return boolean result
	 */
	public function uninstall()
	{
		if (!Validate::isUnsignedId($this->id))
			return false;

		$sql = 'SELECT id_hook
				FROM '._DB_PREFIX_.'hook_module hm
				WHERE id_module = '.(int)$this->id;
		$result = Db::getInstance()->executeS($sql);
		foreach	($result AS $row)
		{
			$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
					WHERE `id_module` = '.(int)$this->id.'
						AND `id_hook` = '.(int)$row['id_hook'];
			Db::getInstance()->execute($sql);
			$this->cleanPositions($row['id_hook']);
		}
		$this->disable(true);

		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'module_access` WHERE `id_module` = '.(int)$this->id);

		// Remove restrictions for client groups
		Group::truncateRestrictionsByModule($this->id);

		return Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'module`
			WHERE `id_module` = '.(int)$this->id);
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
		if (!is_array($name))
			$name = array($name);

		foreach ($name as $k => $v)
			Module::getInstanceByName($name)->enable();
	}

	/**
	 * Activate current module.
	 *
	 * @param bool $forceAll If true, enable module for all shop
	 */
	public function enable($forceAll = false)
	{
		$list = $this->context->shop->getListOfID();
		$sql = 'SELECT id_shop
				FROM '._DB_PREFIX_.'module_shop
				WHERE id_module = '.$this->id.'
					'.((!$forceAll) ? 'AND id_shop IN('.implode(', ', $list).')' : '');
		$items = array();
		if ($results = Db::getInstance($sql)->executeS($sql))
			foreach ($results as $row)
				$items[] = $row['id_shop'];

		foreach ($list as $id)
			if (!in_array($id, $items))
				Db::getInstance()->autoExecute(_DB_PREFIX_.'module_shop', array(
					'id_module' =>	$this->id,
					'id_shop' =>	$id,
				), 'INSERT');

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
		if (!is_array($name))
			$name = array($name);

		foreach ($name as $k => $v)
			Module::getInstanceByName($name)->disable();

		return true;
	}

	/**
	 * Desactivate current module.
	 *
	 * @param bool $forceAll If true, disable module for all shop
	 */
	public function disable($forceAll = false)
	{
		$sql = 'DELETE FROM '._DB_PREFIX_.'module_shop
				WHERE id_module = '.$this->id.'
					'.((!$forceAll) ? ' AND id_shop IN('.implode(', ', $this->context->shop->getListOfID()).')' : '');
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
		if (sizeof($languages) == 1)
			return false;
		$output = '
		<div class="displayed_flag">
			<img src="../img/l/'.$default_language.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="toggleLanguageFlags(this);" alt="" />
		</div>
		<div id="languages_'.$id.'" class="language_flags">
			'.$this->l('Choose language:').'<br /><br />';
		foreach ($languages as $language)
			if($use_vars_instead_of_ids)
				$output .= '<img src="../img/l/'.(int)($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', '.$ids.', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
			else
			$output .= '<img src="../img/l/'.(int)($language['id_lang']).'.jpg" class="pointer" alt="'.$language['name'].'" title="'.$language['name'].'" onclick="changeLanguage(\''.$id.'\', \''.$ids.'\', '.$language['id_lang'].', \''.$language['iso_code'].'\');" /> ';
		$output .= '</div>';

		if ($return)
			return $output;
		echo $output;
	}

	/**
	 * Connect module to a hook
	 *
	 * @param string $hook_name Hook name
	 * @param array $shopList List of shop linked to the hook (if null, link hook to all shops)
	 * @return boolean result
	 */
	public function registerHook($hook_name, $shopList = null)
	{
		if (!Validate::isHookName($hook_name))
			die(Tools::displayError());
		if (!isset($this->id) OR !is_numeric($this->id))
			return false;

		// Retrocompatibility
		Hook::preloadHookAlias();
		if (isset(Hook::$preloadHookAlias[$hook_name]))
			$hook_name = Hook::$preloadHookAlias[$hook_name];

		// Get hook id
		$sql = 'SELECT `id_hook`
				FROM `'._DB_PREFIX_.'hook`
				WHERE `name` = \''.pSQL($hook_name).'\'';
		$hookID = Db::getInstance()->getValue($sql);
		if (!$hookID)
		{
			$newHook = new Hook();
			$newHook->name = pSQL($hook_name);
			$newHook->title = pSQL($hook_name);
			$newHook->add();
			$hookID = $newHook->id;
			if (!$hookID)
				return false;
		}

		if (is_null($shopList))
			$shopList = Shop::getShops(true, null, true);

		$return = true;
		foreach ($shopList as $shopID)
		{
			// Check if already register
			$sql = 'SELECT hm.`id_module`
					FROM `'._DB_PREFIX_.'hook_module` hm, `'._DB_PREFIX_.'hook` h
					WHERE hm.`id_module` = '.(int)($this->id).'
						AND h.id_hook = '.$hookID.'
						AND h.`id_hook` = hm.`id_hook`
						AND id_shop = '.$shopID;
			if (Db::getInstance()->getRow($sql))
				continue;

			// Get module position in hook
			$sql = 'SELECT MAX(`position`) AS position
					FROM `'._DB_PREFIX_.'hook_module`
					WHERE `id_hook` = '.$hookID
						.' AND id_shop = '.$shopID;
			if (!$position = Db::getInstance()->getValue($sql))
				$position = 0;

			// Register module in hook
			$result = Db::getInstance()->autoExecute(_DB_PREFIX_.'hook_module', array(
				'id_module' =>	$this->id,
				'id_hook' =>	$hookID,
				'id_shop' =>	$shopID,
				'position' =>	$position + 1,
			), 'INSERT');
			if (!$result)
				$return &= false;
		}

		$this->cleanPositions($hookID, $shopList);
		return $return;
	}

	/**
	  * Unregister module from hook
	  *
	  * @param int $id_hook Hook id (can be a hook name since 1.5.0)
	  * @param array $shopList List of shop
	  * @return boolean result
	  */
	public function unregisterHook($hook_id, $shopList = null)
	{
		// Get hook id if a name is given as argument
		if (!is_numeric($hook_id))
		{
			// Retrocompatibility
			Hook::preloadHookAlias();
			if (isset(Hook::$preloadHookAlias[$hook_id]))
				$hook_id = Hook::$preloadHookAlias[$hook_id];

			$sql = 'SELECT `id_hook`
					FROM `'._DB_PREFIX_.'hook`
					WHERE `name` = \''.pSQL($hook_id).'\'';
			$hook_id = Db::getInstance()->getValue($sql);
			if (!$hook_id)
				return false;
		}

		$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
				WHERE `id_module` = '.(int)$this->id.'
					AND `id_hook` = '.(int)$hook_id
					.(($shopList) ? ' AND id_shop IN('.implode(', ', $shopList).')' : '');
		$result = Db::getInstance()->execute($sql);
		$this->cleanPositions($hook_id, $shopList);
		return $result;
	}

	/**
	  * Unregister exceptions linked to module
	  *
	  * @param int $id_hook Hook id
	  * @param array $shopList List of shop
	  * @return boolean result
	  */
	public function unregisterExceptions($hook_id, $shopList = null)
	{
		$sql = 'DELETE
				FROM `'._DB_PREFIX_.'hook_module_exceptions`
				WHERE `id_module` = '.(int)$this->id.'
					AND `id_hook` = '.(int)$hook_id
					.(($shopList) ? ' AND id_shop IN('.implode(', ', $shopList).')' : '');
		return Db::getInstance()->execute($sql);
	}

	/**
	  * Add exceptions for module->Hook
	  *
	  * @param int $id_hook Hook id
	  * @param array $excepts List of file name
	  * @param array $shopList List of shop
	  * @return boolean result
	  */
	public function registerExceptions($id_hook, $excepts, $shopList = null)
	{
		if (is_null($shopList))
			Context::getContext()->shop->getListOfID();

		foreach ($shopList as $shopID)
		{
			foreach ($excepts AS $except)
			{
				if (!$except)
					continue;

				$result = Db::getInstance()->autoExecute(_DB_PREFIX_.'hook_module_exceptions', array(
					'id_module' =>	$this->id,
					'id_hook' =>	(int)$id_hook,
					'id_shop' =>	$shopID,
					'file_name' =>	pSQL($except),
				), 'INSERT');
				if (!$result)
					return false;
			}
		}
		return true;
	}

	public function editExceptions($hookID, $excepts)
	{
		$result = true;
		foreach ($excepts as $shopID => $except)
		{
			$shopList = ($shopID == 0) ? Context::getContext()->shop->getListOfID() : array($shopID);
			$this->unregisterExceptions($hookID, $shopList);
			$result &= $this->registerExceptions($hookID, $except, $shopList);
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
		if (!isset(self::$classInModule[$currentClass]))
		{
			global $_MODULES;
			$_MODULE = array();
			$reflectionClass = new ReflectionClass($currentClass);
			$filePath = realpath($reflectionClass->getFileName());
			$realpathModuleDir = realpath(_PS_MODULE_DIR_);
			if (substr(realpath($filePath), 0, strlen($realpathModuleDir)) == $realpathModuleDir)
			{
				self::$classInModule[$currentClass] = substr(dirname($filePath), strlen($realpathModuleDir)+1);
				$file = _PS_MODULE_DIR_.self::$classInModule[$currentClass].'/'.Context::getContext()->language->iso_code.'.php';
				if (Tools::file_exists_cache($file) AND include_once($file))
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
	  * @param string $moduleName Module name
	  * @return Module
	  */
	public static function getInstanceByName($moduleName)
	{
		if (!Tools::file_exists_cache(_PS_MODULE_DIR_.$moduleName.'/'.$moduleName.'.php'))
			return false;
		include_once(_PS_MODULE_DIR_.$moduleName.'/'.$moduleName.'.php');
		if (!class_exists($moduleName, false))
			return false;

		if (!isset(self::$_INSTANCE[$moduleName]))
			self::$_INSTANCE[$moduleName] = new $moduleName;
		return self::$_INSTANCE[$moduleName];
	}

	/**
	  * Return an instance of the specified module
	  *
	  * @param integer $id_module Module ID
	  * @return Module instance
	  */
	static public function getInstanceById($moduleID)
	{
		static $id2name = null;

		if (is_null($id2name))
		{
			$id2name = array();
			$sql = 'SELECT id_module, name
					FROM '._DB_PREFIX_.'module';
			if ($results = Db::getInstance()->executeS($sql))
				foreach ($results as $row)
					$id2name[$row['id_module']] = $row['name'];
		}

		if (isset($id2name[$moduleID]))
			return Module::getInstanceByName($id2name[$moduleID]);
		return false;
	}

	public static function configXmlStringFormat($string)
	{
		return str_replace('\'', '\\\'', Tools::htmlentitiesDecodeUTF8($string));
	}

	/**
	  * Return available modules
	  *
	  * @param boolean $useConfig in order to use config.xml file in module dir
	  * @return array Modules
	  */
	public static function getModulesOnDisk($useConfig = false, $loggedOnAddons = false)
	{
		global $_MODULES;

		$moduleList = array();
		$moduleNameList = array();
		$modulesNameToCursor = array();
		$errors = array();
		$modules_dir = self::getModulesDirOnDisk();

		$memory_limit = Tools::getMemoryLimit();

		foreach ($modules_dir as $module)
		{
			// Memory usage checking
			if (function_exists('memory_get_usage') && $memory_limit !== -1)
			{
				$current_memory = memory_get_usage(true);
				// memory_threshold in MB
				$memory_threshold = (Tools::isX86_64arch() ? 3 : 1.5);
				if (($memory_limit - $current_memory) <= ($memory_threshold * 1024 * 1024))
				{
					$errors[] = Tools::displayError('All modules cannot be loaded due to memory limit restriction reason, please increase your memory_limit value on your server configuration');
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
					if (Tools::file_exists_cache($file) AND include_once($file))
						if(isset($_MODULE) AND is_array($_MODULE))
							$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

					$item = new stdClass();
					$item->id = 0;
					$item->warning = '';
					foreach ($xml_module as $k => $v)
						$item->$k = (string) $v;
					$item->displayName = Module::findTranslation($xml_module->name, self::configXmlStringFormat($xml_module->displayName), (string)$xml_module->name);
					$item->description = Module::findTranslation($xml_module->name, self::configXmlStringFormat($xml_module->description), (string)$xml_module->name);
					$item->author = Module::findTranslation($xml_module->name, self::configXmlStringFormat($xml_module->author), (string)$xml_module->name);

					if (isset($xml_module->confirmUninstall))
						$item->confirmUninstall = Module::findTranslation($xml_module->name, self::configXmlStringFormat($xml_module->confirmUninstall), (string)$xml_module->name);

					$item->active = 0;
					$moduleList[] = $item;
					$moduleNameList[] = '\''.pSQL($item->name).'\'';
					$modulesNameToCursor[strval($item->name)] = $item;
				}
			}

			// If use config flag is at false or config.xml does not exist OR need instance OR need a new config.xml file
			if (!$useConfig OR !$xml_exist OR (isset($xml_module->need_instance) AND (int)$xml_module->need_instance == 1) OR $needNewConfigFile)
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
				if (class_exists($module,false))
				{
					$tmpModule = new $module;

					$item = new stdClass();
					$item->id = $tmpModule->id;
					$item->warning = $tmpModule->warning;
					$item->name = $tmpModule->name;
					$item->version = $tmpModule->version;
					$item->tab = $tmpModule->tab;
					$item->displayName = $tmpModule->displayName;
					$item->description = $tmpModule->description;
					$item->author = $tmpModule->author;
					$item->limited_countries = $tmpModule->limited_countries;
					$item->is_configurable = isset($tmpModule->is_configurable) ? $tmpModule->is_configurable : 1;
					$item->need_instance = isset($tmpModule->need_instance) ? $tmpModule->need_instance : 0;
					$item->active = $tmpModule->active;
					$item->currencies = isset($tmpModule->currencies) ? $tmpModule->currencies : null;
					$item->currencies_mode = isset($tmpModule->currencies_mode) ? $tmpModule->currencies_mode : null;
					unset($tmpModule);

					$moduleList[] = $item;
					if (!$xml_exist OR $needNewConfigFile)
					{
						self::$_generateConfigXmlMode = true;
						$tmpModule = new $module;
						$tmpModule->_generateConfigXml();
						self::$_generateConfigXmlMode = false;
					}
				}
				else
					$errors[] = sprintf(Tools::displayError('%1$s (class missing in %2$s)'), $module, substr($filepath, strlen(_PS_ROOT_DIR_)));
			}
		}

		// Get modules information from database
		if (!empty($moduleNameList))
		{
			$list = Context::getContext()->shop->getListOfID();

			$sql = 'SELECT m.id_module, m.name, (
						SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE m.id_module = ms.id_module AND ms.id_shop IN ('.implode(',', $list).')
					) as total
					FROM '._DB_PREFIX_.'module m
					WHERE m.name IN ('.implode(',', $moduleNameList).')';
			$results = Db::getInstance()->executeS($sql);
			foreach ($results as $result)
			{
				$moduleCursor = $modulesNameToCursor[$result['name']];
				$moduleCursor->id = $result['id_module'];
				$moduleCursor->active = ($result['total'] == count($list)) ? 1 : 0;
			}
		}


		// Get Default Country Modules and customer module
		if ($loggedOnAddons)
		{
			$filesList = array(_PS_ROOT_DIR_.'/config/default_country_modules_list.xml', _PS_ROOT_DIR_.'/config/customer_modules_list.xml');
			foreach ($filesList as $file)
				if (file_exists($file))
				{
					$content = Tools::file_get_contents($file);
					$xml = @simplexml_load_string($content, NULL, LIBXML_NOCDATA);
					foreach ($xml->module as $modaddons)
					{
						$flagFound = 0;
						foreach ($moduleList as $k => $m)
							if ($m->name == $modaddons->name && !isset($m->available_on_addons))
							{
								$flagFound = 1;
								if ($m->version != $modaddons->version && version_compare($m->version, $modaddons->version) === -1)
									$moduleList[$k]->version_addons = $modaddons->version;
							}
						if ($flagFound == 0)
						{
							$item = new stdClass();
							$item->id = 0;
							$item->warning = '';
							$item->name = strip_tags((string)$modaddons->name);
							$item->version = strip_tags((string)$modaddons->version);
							$item->tab = strip_tags((string)$modaddons->tab);
							$item->displayName = strip_tags((string)$modaddons->displayName).' (Addons)';
							$item->description = strip_tags((string)$modaddons->description);
							$item->author = strip_tags((string)$modaddons->author);
							$item->limited_countries = array();
							$item->is_configurable = 0;
							$item->need_instance = 0;
							$item->available_on_addons = 1;
							$item->active = 0;
							if (isset($modaddons->img))
							{
								if (!file_exists('../img/tmp/'.md5($modaddons->name).'.jpg'))
									copy($modaddons->img, '../img/tmp/'.md5($modaddons->name).'.jpg');
								if (file_exists('../img/tmp/'.md5($modaddons->name).'.jpg'))
									$item->image = '../img/tmp/'.md5($modaddons->name).'.jpg';
							}
							$moduleList[] = $item;
						}
					}
				}
		}

		//echo round($current_memory / 1024 / 1024, 2).'Mo<br />';

		if ($errors)
		{
			echo '<div class="alert error"><h3>'.Tools::displayError('The following module(s) couldn\'t be loaded').':</h3><ol>';
			foreach ($errors AS $error)
				echo '<li>'.$error.'</li>';
			echo '</ol></div>';
		}

		return $moduleList;
	}

	public static function getModulesDirOnDisk()
	{
		$moduleList = array();
		$modules = scandir(_PS_MODULE_DIR_);
		foreach ($modules AS $name)
		{
			if (is_dir(_PS_MODULE_DIR_.$name) && Tools::file_exists_cache(_PS_MODULE_DIR_.$name.'/'.$name.'.php'))
			{
				if (!Validate::isModuleName($name))
					throw new PrestashopException(sprintf('Module %s is not a valid module name', $name));
				$moduleList[] = $name;
			}
		}
		return $moduleList;
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

		$module_list_xml = _PS_ROOT_DIR_.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'modules_list.xml';
		$nativeModules = simplexml_load_file($module_list_xml);
		$nativeModules = $nativeModules->modules;
		foreach ($nativeModules as $nativeModulesType)
			if (in_array($nativeModulesType['type'],array('native','partner')))
			{
				$arrNativeModules[] = '""';
				foreach ($nativeModulesType->module as $module)
					$arrNativeModules[] = '"'.pSQL($module['name']).'"';
			}

		return $db->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'module` m
			WHERE name NOT IN ('.implode(',',$arrNativeModules).') ');
	}

	/**
	 * Return installed modules
	 *
	 * @param int $position Take only positionnables modules
	 * @return array Modules
	 */
	public static function getModulesInstalled($position = 0)
	{
		$sql = 'SELECT m.*
				FROM `'._DB_PREFIX_.'module` m';
		if ($position)
			$sql .= ' LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON m.`id_module` = hm.`id_module`
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
	public static function hookExec($hook_name, $hookArgs = array(), $id_module = NULL)
	{
		Tools::displayAsDeprecated();
		return Hook::exec($hook_name, $hookArgs, $id_module);
	}

	public static function hookExecPayment()
	{
		Tools::displayAsDeprecated();
		return Hook::exec('payment');
	}


	public static function preCall($moduleName)
	{
		return true;
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
		$id_customer = $context->customer->id;
		$billing = new Address((int)$context->cart->id_address_invoice);
		if (isset($context->customer))
			$groups = $context->customer->getGroups();

		$hookPayment = 'Payment';
		if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'._DB_PREFIX_.'hook` WHERE `name` = \'displayPayment\''))
			$hookPayment = 'displayPayment';

		$list = Context::getContext()->shop->getListOfID();
		$sql = 'SELECT DISTINCT h.`id_hook`, m.`name`, hm.`position`
				FROM `'._DB_PREFIX_.'module_country` mc
				LEFT JOIN `'._DB_PREFIX_.'module` m ON m.`id_module` = mc.`id_module`
				INNER JOIN `'._DB_PREFIX_.'module_group` mg ON (m.`id_module` = mg.`id_module`)
				INNER JOIN `'._DB_PREFIX_.'customer_group` cg on (cg.`id_group` = mg.`id_group` AND cg.`id_customer` = '.(int)$context->customer->id.')
				LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
				LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`';
		if (isset($context->customer))
			$sql .= '
				LEFT JOIN `'._DB_PREFIX_.'group_module_restriction` gmr ON gmr.`id_module` = m.`id_module`';
		$sql .= '
				WHERE h.`name` = \''.pSQL($hookPayment).'\'
					AND mc.id_country = '.(int)($billing->id_country).'
					AND mc.id_shop = '.(int)$context->shop->getID(true).'
					AND mg.id_shop = '.(int)$context->shop->getID(true).'
					AND (SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', $list).')) = '.count($list).'
					AND hm.id_shop IN('.implode(', ', $list).')';
		if (isset($context->customer))
			$sql .= '
					AND (gmr.`authorized` = 1 AND gmr.`id_group` IN('.implode(', ', $groups).'))';
		$sql .= '
				GROUP BY hm.id_hook, hm.id_module
				ORDER BY hm.`position`, m.`name` DESC';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

		return $result;
	}

	/**
	 * find translation from $_MODULES and put it in self::$l_cache if not already exist
	 * and return it.
	 *
	 * @param string $name name of the module
	 * @param string $string term to find
	 * @param string $source additional param for building translation key
	 * @return string
	 */
	public static function findTranslation($name, $string, $source)
	{
		global $_MODULES;

		$cache_key = $name . '|' . $string . '|' . $source;

		if (!isset(self::$l_cache[$cache_key]))
		{
			if (!is_array($_MODULES))
				return str_replace('"', '&quot;', $string);
			// set array key to lowercase for 1.3 compatibility
			$_MODULES = array_change_key_case($_MODULES);
			$currentKey = '<{'.strtolower($name).'}'.strtolower(_THEME_NAME_).'>'.strtolower($source).'_'.md5($string);
			$defaultKey = '<{'.strtolower($name).'}prestashop>'.strtolower($source).'_'.md5($string);

			if (isset($_MODULES[$currentKey]))
				$ret = stripslashes($_MODULES[$currentKey]);
			elseif (isset($_MODULES[Tools::strtolower($currentKey)]))
				$ret = stripslashes($_MODULES[Tools::strtolower($currentKey)]);
			elseif (isset($_MODULES[$defaultKey]))
				$ret = stripslashes($_MODULES[$defaultKey]);
			elseif (isset($_MODULES[Tools::strtolower($defaultKey)]))
				$ret = stripslashes($_MODULES[Tools::strtolower($defaultKey)]);
			else
				$ret = stripslashes($string);

			self::$l_cache[$cache_key] = str_replace('"', '&quot;', $ret);
		}
		return self::$l_cache[$cache_key];
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
	public function l($string, $specific = false, $id_lang = null)
	{
		if (self::$_generateConfigXmlMode)
			return $string;

		global $_MODULES, $_MODULE;

		if ($id_lang == null)
			$id_lang = Context::getContext()->language->id;
		$file = _PS_MODULE_DIR_.$this->name.'/'.Context::getContext()->language->iso_code.'.php';

		if (Tools::file_exists_cache($file) AND include_once($file))
			$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

		$source = $specific ? $specific : $this->name;
		$string = str_replace('\'', '\\\'', $string);
		$ret = $this->findTranslation($this->name, $string, $source);
		return $ret;
	}

	/*
	 * Reposition module
	 *
	 * @param boolean $id_hook Hook ID
	 * @param boolean $way Up (1) or Down (0)
	 * @param int $position
	 */
	public function updatePosition($id_hook, $way, $position = NULL)
	{
		foreach ($this->context->shop->getListOfID() as $shopID)
		{
			$sql = 'SELECT hm.`id_module`, hm.`position`, hm.`id_hook`
					FROM `'._DB_PREFIX_.'hook_module` hm
					WHERE hm.`id_hook` = '.(int)$id_hook.'
						AND hm.id_shop = '.$shopID.'
					ORDER BY hm.`position` '.($way ? 'ASC' : 'DESC');
			if (!$res = Db::getInstance()->executeS($sql))
				continue;

			foreach ($res AS $key => $values)
				if ((int)($values[$this->identifier]) == (int)($this->id))
				{
					$k = $key ;
					break ;
				}
			if (!isset($k) OR !isset($res[$k]) OR !isset($res[$k + 1]))
				return false;
			$from = $res[$k];
			$to = $res[$k + 1];

			if (isset($position) and !empty($position))
				$to['position'] = (int)($position);

			$sql = 'UPDATE `'._DB_PREFIX_.'hook_module`
					SET `position`= position '.($way ? '-1' : '+1').'
					WHERE position between '.(int)(min(array($from['position'], $to['position']))) .' AND '.(int)(max(array($from['position'], $to['position']))).'
						AND `id_hook`='.(int)$from['id_hook'].'
						AND id_shop = '.$shopID;
			if (!Db::getInstance()->execute($sql))
				return false;

			$sql = 'UPDATE `'._DB_PREFIX_.'hook_module`
					SET `position`='.(int)($to['position']).'
					WHERE `'.pSQL($this->identifier).'` = '.(int)($from[$this->identifier]).'
						AND `id_hook` = '.(int)($to['id_hook']).'
						AND id_shop = '.$shopID;
			if (!Db::getInstance()->execute($sql))
				return false;
		}
	}

	/*
	 * Reorder modules position
	 *
	 * @param boolean $id_hook Hook ID
	 * @param array $shopList List of shop
	 */
	public function cleanPositions($id_hook, $shopList = null)
	{
		$sql = 'SELECT id_module, id_shop
				FROM '._DB_PREFIX_.'hook_module
				WHERE id_hook = '.(int)$id_hook.'
				'.((!is_null($shopList) && $shopList) ? ' AND id_shop IN('.implode(', ', $shopList).')' : '').'
				ORDER BY position';
		$results = Db::getInstance()->executeS($sql);
		$position = array();
		foreach ($results as $row)
		{
			if (!isset($position[$row['id_shop']]))
				$position[$row['id_shop']] = 1;

			$sql = 'UPDATE '._DB_PREFIX_.'hook_module
					SET position = '.$position[$row['id_shop']].'
					WHERE id_hook = '.(int)$id_hook.'
						AND id_module = '.$row['id_module'].'
						AND id_shop = '.$row['id_shop'];
			Db::getInstance()->execute($sql);
			$position[$row['id_shop']]++;
		}

		return true;
	}

	public function displayError($error)
	{
	 	$output = '
		<div class="module_error alert error">
			<img src="'._PS_IMG_.'admin/warning.gif" alt="" title="" /> '.$error.'
		</div>';
		$this->error = true;
		return $output;
	}

	public function displayConfirmation($string)
	{
	 	$output = '
		<div class="module_confirmation conf confirm">
			<img src="'._PS_IMG_.'admin/ok.gif" alt="" title="" /> '.$string.'
		</div>';
		return $output;
	}

	/*
	 * Return exceptions for module in hook
	 *
	 * @param int $id_hook Hook ID
	 * @return array Exceptions
	 */
	protected static $exceptionsCache = NULL;
	public function getExceptions($hookID, $dispatch = false)
	{
		if (is_null(self::$exceptionsCache))
		{
			self::$exceptionsCache = array();
			$sql = 'SELECT *
					FROM `'._DB_PREFIX_.'hook_module_exceptions`
					WHERE id_shop IN ('.implode(', ', Context::getContext()->shop->getListOfID()).')';
			$result = Db::getInstance()->executeS($sql);
			foreach ($result as $row)
			{
				if (!$row['file_name'])
					continue;
				$key = $row['id_hook'] . '-' . $row['id_module'];
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
			foreach (Context::getContext()->shop->getListOfID() as $shopID)
				if (isset(self::$exceptionsCache[$key], self::$exceptionsCache[$key][$shopID]))
					foreach (self::$exceptionsCache[$key][$shopID] as $file)
						if (!in_array($file, $files))
							$files[] = $file;
			return $files;
		}
		else
		{
			$list = array();
			foreach (Context::getContext()->shop->getListOfID() as $shopID)
				if (isset(self::$exceptionsCache[$key], self::$exceptionsCache[$key][$shopID]))
					$list[$shopID] = self::$exceptionsCache[$key][$shopID];
			return $list;
		}
	}

	public static function isInstalled($moduleName)
	{
		Db::getInstance()->executeS('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \''.pSQL($moduleName).'\'');
		return (bool)Db::getInstance()->NumRows();
	}

	public function isRegisteredInHook($hook)
	{
		if (!$this->id)
			return false;

		$sql = 'SELECT COUNT(*)
				FROM `'._DB_PREFIX_.'hook_module` hm
				LEFT JOIN `'._DB_PREFIX_.'hook` h ON (h.`id_hook` = hm.`id_hook`)
				WHERE h.`name` = \''.pSQL($hook).'\'
					AND hm.`id_module` = '.(int)($this->id);
		return Db::getInstance()->getValue($sql);
	}

	/*
	** Template management (display, overload, cache)
	*/
	protected static function _isTemplateOverloadedStatic($moduleName, $template)
	{
		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$moduleName.'/'.$template))
			return true;
		elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.$moduleName.'/'.$template))
			return false;
		return NULL;
	}

	protected function _isTemplateOverloaded($template)
	{
		return self::_isTemplateOverloadedStatic($this->name, $template);
	}

	public static function display($file, $template, $cacheId = NULL, $compileId = NULL)
	{
		$context = Context::getContext();

		$context->smarty->assign('module_dir', __PS_BASE_URI__.'modules/'.basename($file, '.php').'/');
		if (($overloaded = self::_isTemplateOverloadedStatic(basename($file, '.php'), $template)) === NULL)
			$result = Tools::displayError('No template found for module').' '.basename($file,'.php');
		else
		{
			$context->smarty->assign('module_template_dir', ($overloaded ? _THEME_DIR_ : __PS_BASE_URI__).'modules/'.basename($file, '.php').'/');
			$result = $context->smarty->fetch(($overloaded ? _PS_THEME_DIR_.'modules/'.basename($file, '.php') : _PS_MODULE_DIR_.basename($file, '.php')).'/'.$template, $cacheId, $compileId);
		}
		return $result;
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
		if (is_null($overloaded))
			return null;
		return ($overloaded ? _PS_THEME_DIR_.'modules/'.$this->name : _PS_MODULE_DIR_.$this->name).'/'.$template;
	}

	/**
	 * Assign a smarty vars (same syntax as smarty->assign) but prefix all keys with module name
	 *
	 * @since 1.5.0
	 * @param string $key Variable key (can be an array)
	 * @param mixed $value Variable value
	 */
	public function templateAssign($key, $value = null)
	{
		if (is_array($key))
		{
			foreach ($key as $k => $v)
				$this->context->smarty->assign($this->name.'_'.$k, $v);
		}
		else
			$this->context->smarty->assign($this->name.'_'.$key, $value);
	}

	protected function _getApplicableTemplateDir($template)
	{
		return $this->_isTemplateOverloaded($template) ? _PS_THEME_DIR_ : _PS_MODULE_DIR_.$this->name.'/';
	}

	public function isCached($template, $cacheId = NULL, $compileId = NULL)
	{
		$context = Context::getContext();

		return $context->smarty->isCached($this->_getApplicableTemplateDir($template).$template, $cacheId, $compileId);
	}

	protected function _clearCache($template, $cacheId = NULL, $compileId = NULL)
	{
		Tools::clearCache(Context::getContext()->smarty);
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
	<is_configurable>'.(int)method_exists($this, 'getContent').'</is_configurable>
	<need_instance>'.(int)$this->need_instance.'</need_instance>'.(isset($this->limited_countries) ? "\n\t".'<limited_countries>'.(sizeof($this->limited_countries) == 1 ? $this->limited_countries[0] : '').'</limited_countries>' : '').'
</module>';
		if (is_writable(_PS_MODULE_DIR_.$this->name.'/'))
			file_put_contents(_PS_MODULE_DIR_.$this->name.'/config.xml', $xml);
	}

	/**
	 * @param string $hook_name
	 * @return bool if module can be transplanted on hook
	 */
	public function isHookableOn($hook_name)
	{
		return is_callable(array($this, 'hook'.ucfirst($hook_name)));
	}

	public function getPermission($variable, $employee = null)
	{
		return self::getPermissionStatic($this->id, $variable, $employee);
	}

	public static function getPermissionStatic($id_module, $variable, $employee = null)
	{
		if (!in_array($variable, array('view', 'configure')))
			return false;
		if (!$employee)
			$employee = Context::getContext()->employee;
		if (!isset($cache_permissions[$employee->id_profile]))
		{
			$cache_permissions[$employee->id_profile] = array();
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_module, `view`, `configure` FROM '._DB_PREFIX_.'module_access WHERE id_profile = '.(int)$employee->id_profile);
			foreach ($result as $row)
			{
				$cache_permissions[$employee->id_profile][$row['id_module']]['view'] = $row['view'];
				$cache_permissions[$employee->id_profile][$row['id_module']]['configure'] = $row['configure'];
			}
		}
		return (bool)$cache_permissions[$employee->id_profile][$id_module][$variable];
	}

	/**
	 * get Unauthorized modules for a client group
	 * @param integer group_id
	 */
	public static function getAuthorizedModules($group_id)
	{
		return Db::getInstance()->executeS('
			SELECT m.id_module, m.name FROM `'._DB_PREFIX_.'group_module_restriction` gmr
			LEFT JOIN `'._DB_PREFIX_.'module` m ON (m.`id_module` = gmr.`id_module`)
			WHERE gmr.`id_group` = '.(int) $group_id.'
			AND gmr.`authorized` = 1
		');
	}

	/**
	 * get id module by name
	 * @param string name
	 * @return integer id
	 */
	public static function getModuleIdByName($name)
	{
		return Db::getInstance()->getValue('SELECT id_module FROM `'._DB_PREFIX_.'module` WHERE name = "'.pSQL($name).'"');
	}
}

