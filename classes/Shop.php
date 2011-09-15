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
*  @version  Release: $Revision: 7521 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */
class ShopCore extends ObjectModel
{
	public	$id_group_shop;
	public	$id_theme;
	public	$name;
	public	$active;
	public	$id_category;
	public	$deleted;

	public $theme_name;
	public $physical_uri;
	public $virtual_uri;
	public $domain;
	public $domain_ssl;

	/**
	 * @var GroupShop
	 */
	protected $group;

	protected $fieldsRequired = array('id_theme', 'id_category', 'id_group_shop', 'name');
	protected $fieldsSize = array('name' => 64);
 	protected $fieldsValidate = array(
 		'active' => 'isBool',
		'name' => 'isGenericName',
 	);
	protected $table = 'shop';
	protected $identifier = 'id_shop';

	/** @var array List of shops cached */
	protected static $shops = array();

	private	static $asso_tables = array(
		'carrier' => 				array('type' => 'shop'),
		'carrier_lang' => 			array('type' => 'fk_shop'),
		'category_lang' => 			array('type' => 'fk_shop'),
		'cms' => 					array('type' => 'shop'),
		'contact' => 				array('type' => 'shop'),
		'country' => 				array('type' => 'shop'),
		'currency' => 				array('type' => 'shop'),
		'discount' => 				array('type' => 'shop'),
		'hook_module' =>			array('type' => 'fk_shop'),
		'hook_module_exceptions' =>	array('type' => 'fk_shop', 'primary' => 'id_hook_module_exceptions'),
		'image' => 					array('type' => 'shop'),
		'lang' => 					array('type' => 'shop'),
		'meta_lang' => 				array('type' => 'fk_shop'),
		'module' => 				array('type' => 'shop'),
		'module_currency' => 		array('type' => 'fk_shop'),
		'module_country' => 		array('type' => 'fk_shop'),
		'module_group' => 			array('type' => 'fk_shop'),
		'stock' => 					array('type' => 'fk_shop', 'primary' => 'id_stock'),
		'product' => 				array('type' => 'shop'),
		'product_lang' => 			array('type' => 'fk_shop'),
		'referrer' => 				array('type' => 'shop'),
		'scene' => 					array('type' => 'shop'),
		'store' => 					array('type' => 'shop'),
		'webservice_account' => 	array('type' => 'shop'),
	);

	protected $webserviceParameters = array(
		'fields' => array(
			'id_group_shop' => array('xlink_resource' => 'shop_groups'),
			'id_category' => array(),
			'id_theme' => array(),
		),
	);

	/**
	 * There are 3 kinds of shop context : shop, group shop and general
	 */
	const CONTEXT_SHOP = 1;
	const CONTEXT_GROUP = 2;
	const CONTEXT_ALL = 3;

	/**
	 * Some data can be shared between shops, like customers or orders
	 */
	const SHARE_CUSTOMER = 'share_customer';
	const SHARE_ORDER = 'share_order';
	const SHARE_STOCK = 'share_stock';

	public function getFields()
	{
		$this->validateFields();

		$fields['id_group_shop'] = (int)$this->id_group_shop;
		$fields['id_category'] = (int)$this->id_category;
		$fields['id_theme'] = (int)$this->id_theme;
		$fields['name'] = pSQL($this->name);
		$fields['active'] = (int)$this->active;
		$fields['deleted'] = (int)$this->deleted;
		return $fields;
	}

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id, $id_lang, $id_shop);

		if ($this->id)
		{
			$sql = 'SELECT su.physical_uri, su.virtual_uri, su.domain, su.domain_ssl, t.name
					FROM '._DB_PREFIX_.'shop s
					LEFT JOIN '._DB_PREFIX_.'shop_url su ON (s.id_shop = su.id_shop)
					LEFT JOIN '._DB_PREFIX_.'theme t ON (t.id_theme = s.id_theme)
					WHERE s.id_shop = '.$this->id.'
						AND s.active = 1
						AND s.deleted = 0
						AND su.main = 1';
			if (!$row = Db::getInstance()->getRow($sql))
				return;

			$this->theme_name = $row['name'];
			$this->physical_uri = $row['physical_uri'];
			$this->virtual_uri = $row['virtual_uri'];
			$this->domain = $row['domain'];
			$this->domain_ssl = $row['domain_ssl'];
		}
	}

	public function add($autodate = true, $null_values = false)
	{
		$res = parent::add($autodate, $null_values);
		Shop::cacheShops(true);
		return $res;
	}

	public function delete()
	{
		if (!$res = parent::delete())
			return false;

		foreach (Shop::getAssoTables() AS $table_name => $row)
		{
			// Special case for stock if current shop is in a share stock group
			if ($table_name == 'stock')
			{
				$group = new GroupShop($this->id_group_shop);
				if ($group->share_stock && $group->getTotalShops() > 1)
					continue;
			}

			$id = 'id_'.$row['type'];
			if ($row['type'] == 'fk_shop')
				$id = 'id_shop';
			else
				$table_name .= '_'.$row['type'];
			$res &= Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.$table_name.'` WHERE `'.$id.'`='.(int)$this->id);
		}

		Shop::cacheShops(true);

		return $res;
	}

	/**
	 * Get a new instance of a shop
	 *
	 * @param int $id shop ID
	 * @return Shop
	 */
	public static function getInstance($id)
	{
		return new Shop($id);
	}

	/**
	 * Find the shop from current domain / uri and get an instance of this shop
	 *
	 * @return Shop
	 */
	public static function initialize()
	{
		// Get list of excluded uri
		$dirname = dirname(__FILE__);
		$directories = scandir($dirname.'/../');
		$excluded_uris = array();
		foreach ($directories AS $directory)
			if (is_dir($dirname.'/../'.$directory) AND (!preg_match ('/^\./', $directory)))
				$excluded_uris[] = $directory;

		// Find current shop from URL
		if (!$id_shop = Tools::getValue('id_shop') && !defined('PS_ADMIN_DIR'))
		{
			$sql = 'SELECT s.id_shop, CONCAT(su.physical_uri, su.virtual_uri) AS uri
					FROM '._DB_PREFIX_.'shop_url su
					LEFT JOIN '._DB_PREFIX_.'shop s ON (s.id_shop = su.id_shop)
					WHERE su.domain = \''.pSQL(Tools::getHttpHost()).'\'
						AND s.active = 1
						AND s.deleted = 0
					ORDER BY LENGTH(uri) DESC';

			$id_shop = '';
			if ($results = Db::getInstance()->executeS($sql))
				foreach ($results as $row)
				{
					if (preg_match('#^'.preg_quote($row['uri'], '#').'#', $_SERVER['REQUEST_URI']))
					{
						$id_shop = $row['id_shop'];
						break;
					}
				}
		}

		// Get instance of found shop
		$shop = new Shop($id_shop);
		if (!Validate::isLoadedObject($shop) || !$shop->active)
		{
			// No shop found ... too bad, let's redirect to default shop
			$defaultShop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
			if (!$defaultShop)
				// Hmm there is something really bad in your Prestashop !
				die('Shop not found');
			$url = 'http://'.$defaultShop->domain.$defaultShop->getBaseURI().ltrim($_SERVER['SCRIPT_NAME'], '/').'?'.$_SERVER['QUERY_STRING'];
			header('location: '.$url);
			exit;
		}
		return $shop;
	}

	/**
	 * Get shop theme name
	 *
	 * @return string
	 */
	public function getTheme()
	{
		return $this->theme_name;
	}

	/**
	 * Get shop URI
	 *
	 * @return string
	 */
	public function getBaseURI()
	{
		return $this->physical_uri.$this->virtual_uri;
	}

	public function getPhysicalURI()
	{
		return $this->physical_uri;
	}

	/**
	 * Get shop URL
	 *
	 * @return string
	 */
	public function getBaseURL()
	{
		if (!$this->domain)
			return false;
		return 'http://'.$this->domain.$this->physical_uri.$this->virtual_uri;
	}

	/**
	 * Get group of current shop
	 *
	 * @return GroupShop
	 */
	public function getGroup()
	{
		if (!$this->group)
			$this->group = new GroupShop($this->getGroupID());
		return $this->group;
	}

	/**
	 * Get current shop ID
	 *
	 * @return int
	 */
	public function getID($useDefault = false)
	{
		return (!$this->id && $useDefault) ? (int)Configuration::get('PS_SHOP_DEFAULT') : (int)$this->id;
	}

	/**
	 * Get current shop group ID
	 *
	 * @return int
	 */
	public function getGroupID()
	{
		if (defined('PS_ADMIN_DIR'))
			return Shop::getContextGroupID();
		return (int)$this->id_group_shop;
	}

	/**
	 * Get root category of current shop
	 *
	 * @return int
	 */
	public function getCategory()
	{
		return ($this->id_category) ? $this->id_category : 1;
	}

	/**
	 * Get list of shop's urls
	 *
	 * @return array
	 */
	public function getUrls()
	{
		$sql = 'SELECT *
				FROM '._DB_PREFIX.'shop_url
				WHERE active = 1
					AND id_shop = '.(int)$this->id;
		return Db::getInstance()->ExecuteS($sql);
	}

	/**
	 * Check if current shop ID is the same as default shop in configuration
	 *
	 * @return bool
	 */
	public function isDefaultShop()
	{
		return ($this->id == Configuration::get('PS_SHOP_DEFAULT'));
	}

	/**
	 * Get list of associated tables to shop
	 *
	 * @return array
	 */
	public static function getAssoTables()
	{
		return self::$asso_tables;
	}

	/**
	 * Load list of groups and shops, and cache it
	 *
	 * @param bool $refresh
	 */
	public static function cacheShops($refresh = false)
	{
		if (self::$shops && !$refresh)
			return;

		$sql = 'SELECT gs.*, s.*, gs.name AS group_name, s.name AS shop_name, s.active, su.domain, su.domain_ssl, su.physical_uri, su.virtual_uri
				FROM '._DB_PREFIX_.'group_shop gs
				LEFT JOIN '._DB_PREFIX_.'shop s
					ON s.id_group_shop = gs.id_group_shop
				LEFT JOIN '._DB_PREFIX_.'shop_url su
					ON s.id_shop = su.id_shop AND su.main = 1
				WHERE s.deleted = 0
					AND gs.deleted = 0
				ORDER BY gs.name, s.name';
		if ($results = Db::getInstance()->ExecuteS($sql))
		{
			foreach ($results as $row)
			{
				if (!isset(self::$shops[$row['id_group_shop']]))
					self::$shops[$row['id_group_shop']] = array(
						'id' =>				$row['id_group_shop'],
						'name' => 			$row['group_name'],
						'share_customer' =>	$row['share_customer'],
						'share_order' =>	$row['share_order'],
						'share_stock' =>	$row['share_stock'],
						'shops' => 			array(),
					);

				self::$shops[$row['id_group_shop']]['shops'][$row['id_shop']] = array(
					'id_shop' =>		$row['id_shop'],
					'id_group_shop' =>	$row['id_group_shop'],
					'name' =>			$row['shop_name'],
					'id_theme' =>		$row['id_theme'],
					'id_category' =>	$row['id_category'],
					'domain' =>			$row['domain'],
					'domain_ssl' =>		$row['domain_ssl'],
					'uri' =>			$row['physical_uri'].$row['virtual_uri'],
					'active' =>			$row['active'],
				);
			}
		}
	}

	/**
	 * Get shops list
	 *
	 * @param bool $active
	 * @param int $id_group_shop
	 * @param bool $getAsListID
	 */
	public static function getShops($active = true, $id_group_shop = null, $getAsListID = false)
	{
		Shop::cacheShops();
		$results = array();
		foreach (self::$shops as $groupID => $groupData)
			foreach ($groupData['shops'] as $id => $shopData)
				if ((!$active || $shopData['active']) && (!$id_group_shop || $id_group_shop == $groupID))
				{
					if ($getAsListID)
						$results[] = $id;
					else
						$results[] = $shopData;
				}
		return $results;
	}

	/**
	 * Return some informations cached for one shop
	 *
	 * @param int $shopID
	 * @return array
	 */
	public static function getShop($shopID)
	{
		Shop::cacheShops();
		foreach (self::$shops as $groupID => $groupData)
			if (array_key_exists($shopID, $groupData['shops']))
				return $groupData['shops'][$shopID];
		return false;
	}

	/**
	 * Return a shop ID from shop name
	 *
	 * @param string $name
	 * @return int
	 */
	public static function getIdByName($name)
	{
		Shop::cacheShops();
		foreach (self::$shops as $groupData)
			foreach ($groupData['shops'] as $shopID => $shopData)
				if (Tools::strtolower($shopData['name']) == Tools::strtolower($name))
					return $shopID;
		return false;
	}

	/**
	 * @return int Total of shops
	 */
	public static function getTotalShops($active = true)
	{
		return count(Shop::getShops($active));
	}

	/**
	 * Retrieve group ID of a shop
	 *
	 * @param int $shopID Shop ID
	 * @return int Group ID
	 */
	public static function getGroupFromShop($shopID, $asID = true)
	{
		Shop::cacheShops();
		foreach (self::$shops as $groupID => $groupData)
			if (array_key_exists($shopID, $groupData['shops']))
				return ($asID) ? $groupID : $groupData;
		return false;
	}

	/**
	 * If the shop group has the option $type activated, get all shops ID of this group, else get current shop ID
	 *
	 * @param int $shopID
	 * @param int $type Shop::SHARE_CUSTOMER | Shop::SHARE_ORDER | Shop::SHARE_STOCK
	 * @return array
	 */
	public static function getSharedShops($shopID, $type)
	{
		if (!in_array($type, array(Shop::SHARE_CUSTOMER, Shop::SHARE_ORDER, Shop::SHARE_STOCK)))
			die('Wrong argument ($type) in Shop::getSharedShops() method');

		Shop::cacheShops();
		foreach (self::$shops as $groupData)
			if (array_key_exists($shopID, $groupData['shops']) && $groupData[$type])
				return array_keys($groupData['shops']);
		return array($shopID);
	}

	/**
	 * Get a list of ID concerned by the shop context (E.g. if context is shop group, get list of children shop ID)
	 *
	 * @param string $share If false, dont check share datas from group. Else can take a Shop::SHARE_* constant value
	 * @return array
	 */
	public function getListOfID($share = false)
	{
		$shopID = $this->getID();
		$shopGroupID = $this->getGroupID();

		if ($shopID)
			$list = ($share) ? Shop::getSharedShops($shopID, $share) : array($shopID);
		else if ($shopGroupID)
			$list = Shop::getShops(true, $shopGroupID, true);
		else
			$list = Shop::getShops(true, null, true);

		return $list;
	}

	/**
	 * Retrieve the current shop context in FO or BO
	 *
	 * @param string null|shop|group
	 * @return array(id_shop, id_group_shop)|int
	 */
	public static function getContext($type = null)
	{
		$context = Context::getContext();
		if (!isset($context->shop))
			return ($type == 'shop' || $type == 'group') ? '' : array('', '');

		$shopID = $context->shop->id;
		$shopGroupID = $context->shop->id_group_shop;
		if (defined('PS_ADMIN_DIR'))
		{
			if (!isset($context->cookie) || !$context->cookie->shopContext)
				return ($type == 'shop' || $type == 'group') ? '' : array('', '');

			// Parse shopContext cookie value (E.g. s-2, g-4)
			$split = explode('-', $context->cookie->shopContext);
			if (count($split) == 2 && $split[0] == 'g')
				$shopGroupID = (int)$split[1];
		}

		if ($type == 'shop')
			return $shopID;
		else if ($type == 'group')
			return $shopGroupID;
		return array($shopID, $shopGroupID);

		if (!$executed)
		{
			$context = Context::getContext();
			if (defined('PS_ADMIN_DIR'))
			{
				// While cookie is not instancied in admin, we wait ...
				if (!isset($context->cookie))
					return ($type == 'shop' || $type == 'group') ? '' : array('', '');

				// Parse shopContext cookie value (E.g. s-2, g-4)
				$split = explode('-', $context->cookie->shopContext);
				if (count($split) == 2)
				{
					if ($split[0] == 's')
						$shopID = (int)$split[1];
					else if ($split[0] == 'g')
						$shopGroupID = (int)$split[1];

					if ($shopID && !$shopGroupID)
						$shopGroupID = Shop::getGroupFromShop($shopID);
				}
			}
			else
			{

				$shopID = (int)$context->shop->getID();
				$shopGroupID = (int)$context->shop->id_group_shop;
			}
			$executed = true;
		}

		if ($type == 'shop')
			return $shopID;
		else if ($type == 'group')
			return $shopGroupID;
		return array($shopID, $shopGroupID);
	}

	/**
	 * Get ID shop from context
	 *
	 * @return int
	 */
	public static function getContextID()
	{
		return Shop::getContext('shop');
	}

	/**
	 * Get ID shop from context
	 *
	 * @return int
	 */
	public static function getContextGroupID()
	{
		return Shop::getContext('group');
	}

	/**
	 * Check in which type of shop context we are
	 *
	 * @return int
	 */
	public function getContextType()
	{
		list($shopID, $shopGroupID) = Shop::getContext();
		if ($shopID)
			return Shop::CONTEXT_SHOP;
		else if ($shopGroupID)
			return Shop::CONTEXT_GROUP;
		return Shop::CONTEXT_ALL;
	}

	/**
	 * Add an sql restriction for shops fields
	 *
	 * @param int $share If false, dont check share datas from group. Else can take a Shop::SHARE_* constant value
	 * @param string $alias
	 * @param string $type shop|group_shop
	 */
	public function sqlRestriction($share = false, $alias = null, $type = 'shop')
	{
		if ($type != 'shop' && $type != 'group_shop')
			$type = 'shop';

		if ($alias)
			$alias .= '.';

		$restriction = '';
		$shopID = $this->getID();
		$shopGroupID = $this->getGroupID();

		if ($type == 'group_shop')
		{
			if ($shopID)
				$restriction = ' AND '.$alias.'id_group_shop = '.Shop::getGroupFromShop($shopID).' ';
			else if ($shopGroupID)
				$restriction = ' AND '.$alias.'id_group_shop = '.$shopGroupID.' ';
		}
		else
		{
			if ($shopID || $shopGroupID)
				$restriction = ' AND '.$alias.'id_shop IN ('.implode(', ', $this->getListOfID($share)).') ';
			else if ($share == Shop::SHARE_STOCK)
				$restriction = ' AND '.$alias.'id_shop = '.$this->getID(true);
		}

		return $restriction;
	}

	/**
	 * Add an SQL JOIN in query between a table and its associated table in multishop
	 *
	 * @param string $table Table name (E.g. product, module, etc.)
	 * @param string $alias Alias of table
	 * @param bool $innerJoin Use or not INNER JOIN
	 * @param Context $context
	 * @return string
	 */
	public function sqlAsso($table, $alias, $innerJoin = true)
	{
		$tableAlias = ' asso_shop_'.$table;
		if (strpos($table, '.') !== false)
			list($tableAlias, $table) = explode('.', $table);

		$asso_tables = Shop::getAssoTables();
		if (!isset($asso_tables[$table]) || $asso_tables[$table]['type'] != 'shop')
			return ;

		$sql = (($innerJoin) ? ' INNER' : ' LEFT').' JOIN '._DB_PREFIX_.$table.'_shop '.$tableAlias.'
					ON '.$tableAlias.'.id_'.$table.' = '.$alias.'.id_'.$table.'
					AND '.$tableAlias.'.id_shop IN('.implode(', ', $this->getListOfID()).') ';
		return $sql;
	}

	/**
	 * Add a restriction on id_shop for multishop lang table
	 *
	 * @param string $alias
	 * @param Context $context
	 * @return string
	 */
	public function sqlLang($alias = null)
	{
		return ' AND '.(($alias) ? $alias.'.' : '').'id_shop = '.$this->getID(true). ' ';
	}

	/**
	 * Get all groups and associated shops as subarrays
	 *
	 * @return array
	 */
	public static function getTree()
	{
		Shop::cacheShops();
		return self::$shops;
	}

	/**
	 * @return bool Return true if there is more than one shop
	 */
	public static function isMultiShopActivated()
	{
		static $total = null;

		if (is_null($total))
			$total = Shop::getTotalShops(true);
		return ($total > 1) ? true : false;
	}

	public function copyShopData($old_id, $tables_import = false, $deleted = false)
	{
		foreach (Shop::getAssoTables() AS $table_name => $row)
		{
			if ($tables_import && !isset($tables_import[$table_name]))
				continue;

			// Special case for stock if current shop is in a share stock group
			if ($table_name == 'stock')
			{
				$group = new GroupShop($this->id_group_shop);
				if ($group->share_stock && $group->haveShops())
					continue;
			}

			$id = 'id_'.$row['type'];
			if ($row['type'] == 'fk_shop')
				$id = 'id_shop';
			else
				$table_name .= '_'.$row['type'];

			if (!$deleted)
			{
				$res = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.$table_name.'` WHERE `'.$id.'` = '.(int)$old_id);
				if ($res)
				{
					unset($res[$id]);
					if (isset($row['primary']))
						unset($res[$row['primary']]);

					$keys = implode(', ', array_keys($res));
					$sql = 'INSERT IGNORE INTO `'._DB_PREFIX_.$table_name.'` ('.$keys.', '.$id.')
								(SELECT '.$keys.', '.(int)$this->id.' FROM '._DB_PREFIX_.$table_name.'
								WHERE `'.$id.'` = '.(int)$old_id.')';
					Db::getInstance()->Execute($sql);
				}
			}
			/*else
			{
				Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.$table_name.'` SET  WHERE `'.$id.'`='.(int)$old_id);
			}*/
		}
	}
}