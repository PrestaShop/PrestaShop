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

	public $_errors = false;

	protected $table = 'module';

	protected $identifier = 'id_module';

	static public $_db;

	/** @var array to store the limited country */
	public $limited_countries = array();

	protected static $modulesCache;
	protected static $_hookModulesCache;
	
	protected static $_INSTANCE = array();
	
	protected static $_generateConfigXmlMode = false;
	
	protected static $l_cache = array();

	/**
	 * @var array used by AdminTab to determine which lang file to use (admin.php or module lang file)
	 */
	public static $classInModule	= array();
	
	/** @var int */
	protected $shopID;
	
	/** @var int */
	protected $shopGroupID;

	/**
	 * Constructor
	 *
	 * @param string $name Module unique name
	 */
	public function __construct($name = NULL)
	{
		global $cookie;

		// Search the module shop context
		list($shopID, $shopGroupID) = Shop::retrieveContext();
		$this->setShopID($shopID);
		$this->setShopGroupID($shopGroupID);

		if ($this->name == NULL)
			$this->name = $this->id;
		if ($this->name != NULL)
		{
			if (self::$modulesCache == NULL AND !is_array(self::$modulesCache))
			{
				$list = Shop::getListOfID($this->shopID, $this->shopGroupID);

				// Join clause is done to check if the module is activated in current shop context
				$sql = 'SELECT m.id_module, m.name, (
							SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE m.id_module = ms.id_module AND ms.id_shop IN ('.implode(',', $list).')
						) as total
						FROM '._DB_PREFIX_.'module m';
				self::$modulesCache = array();
				$result = Db::getInstance()->ExecuteS($sql);
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
	
	/**
	 * @param int $id Set shopID property
	 */
	public function setShopID($id)
	{
		$this->shopID = (int)$id;
	}
	
	/**
	 * @param int $id Set shopGroupID property
	 */
	public function setShopGroupID($id)
	{
		$this->shopGroupID = (int)$id;
	}
	
	protected function sqlShopRestriction($share = false, $alias = null)
	{
		return Shop::sqlRestriction($share, $alias, $this->shopID, $this->shopGroupID, 'shop');
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
		$result = Db::getInstance()->ExecuteS($sql);
		foreach	($result AS $row)
		{
			$sql = 'DELETE FROM `'._DB_PREFIX_.'hook_module`
					WHERE `id_module` = '.(int)$this->id.'
						AND `id_hook` = '.(int)$row['id_hook'];
			Db::getInstance()->Execute($sql);
			$this->cleanPositions($row['id_hook']);
		}
		$this->disable(true);

		return Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'module`
			WHERE `id_module` = '.(int)($this->id));
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
			Module::getInstanceByName()->enable();
	}

	/**
	 * Activate current module.
	 * 
	 * @param bool $forceAll If true, enable module for all shop
	 */
	public function enable($forceAll = false)
	{
		$list = Shop::getListOfID($this->shopID, $this->shopGroupID);
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
			Module::getInstanceByName()->disable();
			
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
					'.((!$foreAll) ? ' AND id_shop IN('.implode(', ', Shop::getListOfID($this->shopID, $this->shopGroupID)).')' : '');
		Db::getInstance()->execute($sql);
	}

	/**
	  * Display flags in forms for translations
	  *
	  * @param array $languages All languages available
	  * @param integer $defaultLanguage Default language id
	  * @param string $ids Multilingual div ids in form
	  * @param string $id Current div id]
	  * #param boolean $return define the return way : false for a display, true for a return
	  */
	public function displayFlags($languages, $defaultLanguage, $ids, $id, $return = false)
	{
		if (sizeof($languages) == 1)
			return false;
		$output = '
		<div class="displayed_flag">
			<img src="../img/l/'.$defaultLanguage.'.jpg" class="pointer" id="language_current_'.$id.'" onclick="toggleLanguageFlags(this);" alt="" />
		</div>
		<div id="languages_'.$id.'" class="language_flags">
			'.$this->l('Choose language:').'<br /><br />';
		foreach ($languages as $language)
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

		// Get hook id
		$sql = 'SELECT `id_hook`
				FROM `'._DB_PREFIX_.'hook`
				WHERE `name` = \''.pSQL($hook_name).'\'';
		$hookID = Db::getInstance()->getValue($sql);
		if (!$hookID)
			return false;
			
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
		$result = Db::getInstance()->Execute($sql);
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
		return Db::getInstance()->Execute($sql);
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
			$shopList = Shop::getShops(true, null, true);

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

	public function editExceptions($hookID, $excepts, $shopList = null)
	{
		$this->unregisterExceptions($hookID, $shopList);
		return $this->registerExceptions($hookID, $excepts, $shopList);
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

				$id_lang = (!isset($cookie) OR !is_object($cookie)) ? (int)(Configuration::get('PS_LANG_DEFAULT')) : (int)($cookie->id_lang);
				$file = _PS_MODULE_DIR_.self::$classInModule[$currentClass].'/'.Language::getIsoById($id_lang).'.php';
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
	  * @return Module instance
	  */
	static public function getInstanceByName($moduleName)
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

	/**
	  * Return available modules
	  *
	  * @param boolean $useConfig in order to use config.xml file in module dir
	  * @return array Modules
	  */
	public static function getModulesOnDisk($useConfig = false)
	{
		global $cookie, $_MODULES;

		$moduleList = array();
		$moduleListCursor = 0;
		$moduleNameList = array();
		$modulesNameToCursor = array();
		$errors = array();
		$modules_dir = self::getModulesDirOnDisk();
		foreach ($modules_dir AS $module)
		{
			$configFile = _PS_MODULE_DIR_.$module.'/config.xml';
			$xml_exist = file_exists($configFile);
			if ($xml_exist)
				$needNewConfigFile = (filemtime($configFile) < filemtime(_PS_MODULE_DIR_.$module.'/'.$module.'.php'));
			else
				$needNewConfigFile = true;
			if ($useConfig AND $xml_exist)
			{
				libxml_use_internal_errors(true);
				$xml_module = simplexml_load_file($configFile);
				foreach (libxml_get_errors() as $error)
					$errors[] = '['.$module.'] '.Tools::displayError('Error found in config file:').' '.htmlentities($error->message);
				libxml_clear_errors();

				if (!count($errors) AND (int)$xml_module->need_instance == 0 AND !$needNewConfigFile)
				{
					$file = _PS_MODULE_DIR_.$module.'/'.Language::getIsoById($cookie->id_lang).'.php';
					if (Tools::file_exists_cache($file) AND include_once($file))
						if(isset($_MODULE) AND is_array($_MODULE))
							$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;

					$item = new stdClass();
					$item->id = 0;
					$item->warning = '';
					foreach ($xml_module as $k => $v)
						$item->$k = (string) $v;
					$item->displayName = Module::findTranslation($xml_module->name, $xml_module->displayName, (string)$xml_module->name);
					$item->description = Module::findTranslation($xml_module->name, $xml_module->description, (string)$xml_module->name);
					$item->author = Module::findTranslation($xml_module->name, $xml_module->author, (string)$xml_module->name);

					if (isset($xml_module->confirmUninstall))
						$item->confirmUninstall = Module::findTranslation($xml_module->name, $xml_module->confirmUninstall, (string)$xml_module->name);

					$item->active = 0;
					$moduleList[$moduleListCursor] = $item;
					$moduleNameList[$moduleListCursor] = '\''.pSQL($item->name).'\'';
					$modulesNameToCursor[strval($item->name)] = $moduleListCursor;
					$moduleListCursor++;
				}
			}

			if (!$useConfig OR !$xml_exist OR (isset($xml_module->need_instance) AND (int)$xml_module->need_instance == 1) OR $needNewConfigFile)
			{
				$file = trim(file_get_contents(_PS_MODULE_DIR_.$module.'/'.$module.'.php'));
				if (substr($file, 0, 5) == '<?php')
					$file = substr($file, 5);
				if (substr($file, -2) == '?>')
					$file = substr($file, 0, -2);
				if (class_exists($module, false) OR eval($file) !== false)
				{
					$moduleList[$moduleListCursor++] = new $module;
					if (!$xml_exist OR $needNewConfigFile)
					{
						self::$_generateConfigXmlMode = true;
						$tmpModule = new $module;
						$tmpModule->_generateConfigXml();
						self::$_generateConfigXmlMode = false;
					}
				}
				else
					$errors[] = $module;
			}
		}

		// Get modules information from database
		if (!empty($moduleNameList))
		{
			$list = Shop::getListFromContext();		

			$sql = 'SELECT m.id_module, m.name, (
						SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE m.id_module = ms.id_module AND ms.id_shop IN ('.implode(',', $list).')
					) as total
					FROM '._DB_PREFIX_.'module m
					WHERE m.name IN ('.implode(',', $moduleNameList).')';
			$results = Db::getInstance()->executeS($sql);
			foreach ($results as $result)
			{
				$moduleCursor = $modulesNameToCursor[$result['name']];
				$moduleList[$moduleCursor]->id = $result['id_module'];
				$moduleList[$moduleCursor]->active = ($result['total'] == count($list)) ? 1 : 0;
			}
		}

		if ($errors)
		{
			echo '<div class="alert error"><h3>'.Tools::displayError('Parse error(s) in module(s)').'</h3><ol>';
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
			if (Tools::file_exists_cache($moduleFile = _PS_MODULE_DIR_.$name.'/'.$name.'.php'))
			{
				if (!Validate::isModuleName($name))
					die(Tools::displayError().' (Module '.$name.')');
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
		$modulesDirOnDisk = Module::getModulesDirOnDisk();

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

		return $db->ExecuteS('
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
		return Db::getInstance()->ExecuteS($sql);
	}

	/*
	 * Execute modules for specified hook
	 *
	 * @param string $hook_name Hook Name
	 * @param array $hookArgs Parameters for the functions
	 * @return string modules output
	 */
	public static function hookExec($hook_name, $hookArgs = array(), $id_module = NULL)
	{
		global $cart, $cookie;
		if ((!empty($id_module) AND !Validate::isUnsignedId($id_module)) OR !Validate::isHookName($hook_name))
			die(Tools::displayError());

		$live_edit = false;
		if (!isset($hookArgs['cookie']) OR !$hookArgs['cookie'])
			$hookArgs['cookie'] = $cookie;
		if (!isset($hookArgs['cart']) OR !$hookArgs['cart'])
			$hookArgs['cart'] = $cart;
		$hook_name = strtolower($hook_name);

		if (!isset(self::$_hookModulesCache))
		{
			$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
			$list = Shop::getListFromContext();

			$sql = 'SELECT h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module, h.`live_edit`
					FROM `'._DB_PREFIX_.'module` m
					LEFT JOIN `'._DB_PREFIX_.'hook_module` hm
						ON hm.`id_module` = m.`id_module`
					LEFT JOIN `'._DB_PREFIX_.'hook` h
						ON hm.`id_hook` = h.`id_hook`
					WHERE (SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', $list).')) = '.count($list).'
						AND hm.id_shop IN('.implode(', ', $list).')
					GROUP BY hm.id_hook, hm.id_module
					ORDER BY hm.`position`';
			$result = $db->ExecuteS($sql, false);
			self::$_hookModulesCache = array();
	
			if ($result)
				while ($row = $db->nextRow())
				{
					$row['hook'] = strtolower($row['hook']);
					if (!isset(self::$_hookModulesCache[$row['hook']]))
						self::$_hookModulesCache[$row['hook']] = array();
					self::$_hookModulesCache[$row['hook']][] = array('id_hook' => $row['id_hook'], 'module' => $row['module'], 'id_module' => $row['id_module'], 'live_edit' => $row['live_edit']);
				}
		}

		if (!isset(self::$_hookModulesCache[$hook_name]))
			return;

		$altern = 0;
		$output = '';
		foreach (self::$_hookModulesCache[$hook_name] AS $array)
		{
			if ($id_module AND $id_module != $array['id_module'])
				continue;
			if (!($moduleInstance = Module::getInstanceByName($array['module'])))
				continue;

			$exceptions = $moduleInstance->getExceptions($array['id_hook']);
			foreach ($exceptions AS $exception)
				if (strstr(basename($_SERVER['PHP_SELF']).'?'.$_SERVER['QUERY_STRING'], $exception))
					continue 2;

			if (is_callable(array($moduleInstance, 'hook'.$hook_name)))
			{
				$hookArgs['altern'] = ++$altern;

				$display = call_user_func(array($moduleInstance, 'hook'.$hook_name), $hookArgs);
				if ($array['live_edit'] && ((Tools::isSubmit('live_edit') AND $ad = Tools::getValue('ad') AND (Tools::getValue('liveToken') == sha1(Tools::getValue('ad')._COOKIE_KEY_)))))
				{
					$live_edit = true;
					$output .= '<script type="text/javascript"> modules_list.push(\''.$moduleInstance->name.'\');</script>
								<div id="hook_'.$array['id_hook'].'_module_'.$moduleInstance->id.'_moduleName_'.$moduleInstance->name.'" 
								class="dndModule" style="border: 1px dotted red;'.(!strlen($display) ? 'height:50px;' : '').'">
								<span><img src="'.$moduleInstance->_path.'/logo.gif">'
							 	.$moduleInstance->displayName.'<span style="float:right">
							 	<a href="#" id="'.$array['id_hook'].'_'.$moduleInstance->id.'" class="moveModule">
							 		<img src="'._PS_ADMIN_IMG_.'arrow_out.png"></a>
							 	<a href="#" id="'.$array['id_hook'].'_'.$moduleInstance->id.'" class="unregisterHook">
							 		<img src="'._PS_ADMIN_IMG_.'delete.gif"></span></a>
							 	</span>'.$display.'</div>';
				}
				else
					$output .= $display;
			}
		}
		return ($live_edit ? '<script type="text/javascript">hooks_list.push(\''.$hook_name.'\'); </script><!--<div id="add_'.$hook_name.'" class="add_module_live_edit">
				<a class="exclusive" href="#">Add a module</a></div>--><div id="'.$hook_name.'" class="dndHook" style="min-height:50px">' : '').$output.($live_edit ? '</div>' : '');
	}

	public static function hookExecPayment()
	{
		global $cart, $cookie;
		$hookArgs = array('cookie' => $cookie, 'cart' => $cart);
		$id_customer = (int)($cookie->id_customer);
		$billing = new Address((int)($cart->id_address_invoice));
		$output = '';
		$list = Shop::getListFromContext();
		$sql = 'SELECT DISTINCT h.`id_hook`, m.`name`, hm.`position`
				FROM `'._DB_PREFIX_.'module_country` mc
				LEFT JOIN `'._DB_PREFIX_.'module` m ON m.`id_module` = mc.`id_module`
				INNER JOIN `'._DB_PREFIX_.'module_group` mg ON (m.`id_module` = mg.`id_module`)
				INNER JOIN `'._DB_PREFIX_.'customer_group` cg on (cg.`id_group` = mg.`id_group` AND cg.`id_customer` = '.(int)($id_customer).')
				LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
				LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
				WHERE h.`name` = \'payment\'
					AND mc.id_country = '.(int)($billing->id_country).'
					AND mc.id_shop = '.(int)Shop::getCurrentShop().'
					AND mg.id_shop = '.(int)Shop::getCurrentShop().'
					AND (SELECT COUNT(*) FROM '._DB_PREFIX_.'module_shop ms WHERE ms.id_module = m.id_module AND ms.id_shop IN('.implode(', ', $list).')) = '.count($list).'
					AND hm.id_shop IN('.implode(', ', $list).')
				GROUP BY hm.id_hook, hm.id_module
				ORDER BY hm.`position`, m.`name` DESC';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		if ($result)
			foreach ($result AS $k => $module)
				if (($moduleInstance = Module::getInstanceByName($module['name'])) AND is_callable(array($moduleInstance, 'hookpayment')))
					if (!$moduleInstance->currencies OR ($moduleInstance->currencies AND sizeof(Currency::checkPaymentCurrencies($moduleInstance->id, (int)Shop::getCurrentShop()))))
						$output .= call_user_func(array($moduleInstance, 'hookpayment'), $hookArgs);
		return $output;
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
	public function l($string, $specific = false)
	{
		if (self::$_generateConfigXmlMode)
			return $string;
		
		global $_MODULES, $_MODULE, $cookie;

		$id_lang = (!isset($cookie) OR !is_object($cookie)) ? (int)(Configuration::get('PS_LANG_DEFAULT')) : (int)($cookie->id_lang);
		$file = _PS_MODULE_DIR_.$this->name.'/'.Language::getIsoById($id_lang).'.php';
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
		$list = ShopCore::getListOfID($this->shopID, $this->shopGroupID);
		foreach ($list as $shopID)
		{
			$sql = 'SELECT hm.`id_module`, hm.`position`, hm.`id_hook`
					FROM `'._DB_PREFIX_.'hook_module` hm
					WHERE hm.`id_hook` = '.(int)$id_hook.'
						AND hm.id_shop = '.$shopID.'
					ORDER BY hm.`position` '.($way ? 'ASC' : 'DESC');
			if (!$res = Db::getInstance()->ExecuteS($sql))
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
			if (!Db::getInstance()->Execute($sql))
				return false;
				
			$sql = 'UPDATE `'._DB_PREFIX_.'hook_module`
					SET `position`='.(int)($to['position']).'
					WHERE `'.pSQL($this->identifier).'` = '.(int)($from[$this->identifier]).'
						AND `id_hook` = '.(int)($to['id_hook']).'
						AND id_shop = '.$shopID;
			if (!Db::getInstance()->Execute($sql))
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
				'.((!is_null($shopList)) ? ' AND id_shop IN('.implode(', ', $shopList).')' : '').'
				ORDER BY position';
		$results = Db::getInstance()->ExecuteS($sql);
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
			Db::getInstance()->Execute($sql);
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
					WHERE id_shop IN ('.implode(', ', Shop::getListFromContext()).')';
			$result = Db::getInstance()->ExecuteS($sql);
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
			foreach (Shop::getListFromContext() as $shopID)
				if (isset(self::$exceptionsCache[$key], self::$exceptionsCache[$key][$shopID]))
					foreach (self::$exceptionsCache[$key][$shopID] as $file)
						if (!in_array($file, $files))
							$files[] = $file;
			return $files;
		}
		else
		{
			$list = array();
			foreach (Shop::getListFromContext() as $shopID)
				if (isset(self::$exceptionsCache[$key], self::$exceptionsCache[$key][$shopID]))
					$list[$shopID] = self::$exceptionsCache[$key][$shopID];
			return $list;
		}
	}

	public static function isInstalled($moduleName)
	{
		Db::getInstance()->ExecuteS('SELECT `id_module` FROM `'._DB_PREFIX_.'module` WHERE `name` = \''.pSQL($moduleName).'\'');
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
		global $smarty;

		if (Configuration::get('PS_FORCE_SMARTY_2')) /* Keep a backward compatibility for Smarty v2 */
		{
			$previousTemplate = $smarty->currentTemplate;
			$smarty->currentTemplate = substr(basename($template), 0, -4);
		}
		$smarty->assign('module_dir', __PS_BASE_URI__.'modules/'.basename($file, '.php').'/');
		if (($overloaded = self::_isTemplateOverloadedStatic(basename($file, '.php'), $template)) === NULL)
			$result = Tools::displayError('No template found for module').' '.basename($file,'.php');
		else
		{
			$smarty->assign('module_template_dir', ($overloaded ? _THEME_DIR_ : __PS_BASE_URI__).'modules/'.basename($file, '.php').'/');
			$result = $smarty->fetch(($overloaded ? _PS_THEME_DIR_.'modules/'.basename($file, '.php') : _PS_MODULE_DIR_.basename($file, '.php')).'/'.$template, $cacheId, $compileId);
		}
		if (Configuration::get('PS_FORCE_SMARTY_2')) /* Keep a backward compatibility for Smarty v2 */
			$smarty->currentTemplate = $previousTemplate;
		return $result;
	}

	protected function _getApplicableTemplateDir($template)
	{
		return $this->_isTemplateOverloaded($template) ? _PS_THEME_DIR_ : _PS_MODULE_DIR_.$this->name.'/';
	}

	public function isCached($template, $cacheId = NULL, $compileId = NULL)
	{
		global $smarty;

		/* Use Smarty 3 API calls */
		if (!Configuration::get('PS_FORCE_SMARTY_2')) /* PHP version > 5.1.2 */
			return $smarty->isCached($this->_getApplicableTemplateDir($template).$template, $cacheId, $compileId);
		/* or keep a backward compatibility if PHP version < 5.1.2 */
		else
			return $smarty->is_cached($this->_getApplicableTemplateDir($template).$template, $cacheId, $compileId);
	}

	protected function _clearCache($template, $cacheId = NULL, $compileId = NULL)
	{
		global $smarty;

		/* Use Smarty 3 API calls */
		if (!Configuration::get('PS_FORCE_SMARTY_2')) /* PHP version > 5.1.2 */
			return $smarty->clearCache($template ? $this->_getApplicableTemplateDir($template).$template : NULL, $cacheId, $compileId);
		/* or keep a backward compatibility if PHP version < 5.1.2 */
		else
			return $smarty->clear_cache($template ? $this->_getApplicableTemplateDir($template).$template : NULL, $cacheId, $compileId);
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
			file_put_contents(_PS_MODULE_DIR_.$this->name.'/config.xml', utf8_encode($xml));
	}
	
	/**
	 * @param string $hook_name
	 * @return bool if module can be transplanted on hook
	 */
	public function isHookableOn($hook_name)
	{
		return is_callable(array($this, 'hook'.ucfirst($hook_name)));
	}
}

