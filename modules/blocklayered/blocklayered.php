<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockLayered extends Module
{
	private $products;
	private $nbr_products;
	
	private $page = 1;

	public function __construct()
	{
		$this->name = 'blocklayered';
		$this->tab = 'front_office_features';
		$this->version = '1.8.9';
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Layered navigation block');
		$this->description = $this->l('Displays a block with layered navigation filters.');
		
		if ((int)Tools::getValue('p'))
			$this->page = (int)Tools::getValue('p');
	}
	
	public function install()
	{
		if (parent::install() && $this->registerHook('leftColumn') && $this->registerHook('header') && $this->registerHook('footer')
		&& $this->registerHook('categoryAddition') && $this->registerHook('categoryUpdate') && $this->registerHook('attributeGroupForm')
		&& $this->registerHook('afterSaveAttributeGroup') && $this->registerHook('afterDeleteAttributeGroup') && $this->registerHook('featureForm')
		&& $this->registerHook('afterDeleteFeature') && $this->registerHook('afterSaveFeature') && $this->registerHook('categoryDeletion')
		&& $this->registerHook('afterSaveProduct') && $this->registerHook('productListAssign') && $this->registerHook('postProcessAttributeGroup')
		&& $this->registerHook('postProcessFeature') && $this->registerHook('featureValueForm') && $this->registerHook('postProcessFeatureValue')
		&& $this->registerHook('afterDeleteFeatureValue') && $this->registerHook('afterSaveFeatureValue') && $this->registerHook('attributeForm')
		&& $this->registerHook('postProcessAttribute') && $this->registerHook('afterDeleteAttribute') && $this->registerHook('afterSaveAttribute'))
		{
			Configuration::updateValue('PS_LAYERED_HIDE_0_VALUES', 1);
			Configuration::updateValue('PS_LAYERED_SHOW_QTIES', 1);
			Configuration::updateValue('PS_LAYERED_FULL_TREE', 1);
			Configuration::updateValue('PS_LAYERED_FILTER_PRICE_USETAX', 1);
			Configuration::updateValue('PS_LAYERED_FILTER_CATEGORY_DEPTH', 1);
			Configuration::updateValue('PS_LAYERED_FILTER_INDEX_QTY', 0);
			Configuration::updateValue('PS_LAYERED_FILTER_INDEX_CDT', 0);
			Configuration::updateValue('PS_LAYERED_FILTER_INDEX_MNF', 0);
			Configuration::updateValue('PS_LAYERED_FILTER_INDEX_CAT', 0);
			
			$this->rebuildLayeredStructure();
			
			$products_count = Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'product`');
			
			if ($products_count < 20000) // Lock template filter creation if too many products
				$this->rebuildLayeredCache();
			self::installPriceIndexTable();
			$this->installFriendlyUrlTable();
			$this->installIndexableAttributeTable();
			$this->installProductAttributeTable();
			
			if ($products_count < 5000) // Lock indexation if too many products
			{
				$this->indexUrl();
				$this->indexAttribute();
				self::fullPricesIndexProcess();
			}
			
			return true;
		}
		else
		{
			// Installation failed (or hook registration) => uninstall the module
			$this->uninstall();
			return false;
		}
	}

	public function uninstall()
	{
		/* Delete all configurations */
		Configuration::deleteByName('PS_LAYERED_HIDE_0_VALUES');
		Configuration::deleteByName('PS_LAYERED_SHOW_QTIES');
		Configuration::deleteByName('PS_LAYERED_FULL_TREE');
		Configuration::deleteByName('PS_LAYERED_INDEXED');
		Configuration::deleteByName('PS_LAYERED_FILTER_PRICE_USETAX');
		Configuration::deleteByName('PS_LAYERED_FILTER_CATEGORY_DEPTH');
		Configuration::deleteByName('PS_LAYERED_FILTER_INDEX_QTY');
		Configuration::deleteByName('PS_LAYERED_FILTER_INDEX_CDT');
		Configuration::deleteByName('PS_LAYERED_FILTER_INDEX_MNF');
		Configuration::deleteByName('PS_LAYERED_FILTER_INDEX_CAT');
		
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_price_index');
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_friendly_url');
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_indexable_attribute_group');
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_indexable_feature');
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value');
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_indexable_feature_lang_value');
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_category');
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_filter');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_filter_shop');
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_product_attribute');
		return parent::uninstall();
	}
	
	private static function installPriceIndexTable()
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_price_index`');
		
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_price_index` (
			`id_product` INT  NOT NULL,
			`id_currency` INT NOT NULL,
			`id_shop` INT NOT NULL,
			`price_min` INT NOT NULL,
			`price_max` INT NOT NULL,
		PRIMARY KEY (`id_product`, `id_currency`, `id_shop`),
		INDEX `id_currency` (`id_currency`),
		INDEX `price_min` (`price_min`), INDEX `price_max` (`price_max`)
		)  ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
	}
	
	private function installFriendlyUrlTable()
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_friendly_url`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_friendly_url` (
		`id_layered_friendly_url` INT NOT NULL AUTO_INCREMENT,
		`url_key` varchar(32) NOT NULL,
		`data` varchar(200) NOT NULL,
		`id_lang` INT NOT NULL,
		PRIMARY KEY (`id_layered_friendly_url`),
		INDEX `id_lang` (`id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

		Db::getInstance()->execute('CREATE INDEX `url_key` ON `'._DB_PREFIX_.'layered_friendly_url`(url_key(5))');
	}
	
	private function installIndexableAttributeTable()
	{
		// Attributes Groups
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_attribute_group`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_attribute_group` (
		`id_attribute_group` INT NOT NULL,
		`indexable` BOOL NOT NULL DEFAULT 0,
		PRIMARY KEY (`id_attribute_group`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'layered_indexable_attribute_group`
		SELECT id_attribute_group, 1 FROM `'._DB_PREFIX_.'attribute_group`');
		
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value` (
		`id_attribute_group` INT NOT NULL,
		`id_lang` INT NOT NULL,
		`url_name` VARCHAR(20),
		`meta_title` VARCHAR(20),
		PRIMARY KEY (`id_attribute_group`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		
		// Attributes
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_attribute_lang_value`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_attribute_lang_value` (
		`id_attribute` INT NOT NULL,
		`id_lang` INT NOT NULL,
		`url_name` VARCHAR(20),
		`meta_title` VARCHAR(20),
		PRIMARY KEY (`id_attribute`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		
		
		// Features
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_feature`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_feature` (
		`id_feature` INT NOT NULL,
		`indexable` BOOL NOT NULL DEFAULT 0,
		PRIMARY KEY (`id_feature`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'layered_indexable_feature`
		SELECT id_feature, 1 FROM `'._DB_PREFIX_.'feature`');
		
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_feature_lang_value`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_feature_lang_value` (
		`id_feature` INT NOT NULL,
		`id_lang` INT NOT NULL,
		`url_name` VARCHAR(20) NOT NULL,
		`meta_title` VARCHAR(20),
		PRIMARY KEY (`id_feature`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		
		// Features values
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_feature_value_lang_value`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_feature_value_lang_value` (
		`id_feature_value` INT NOT NULL,
		`id_lang` INT NOT NULL,
		`url_name` VARCHAR(20),
		`meta_title` VARCHAR(20),
		PRIMARY KEY (`id_feature_value`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
	}
	
	/**
	 * 
	 * create table product attribute
	 */
	public function installProductAttributeTable()
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_product_attribute`');
		Db::getInstance()->execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_product_attribute` (
		`id_attribute` int(10) unsigned NOT NULL,
		`id_product` int(10) unsigned NOT NULL,
		`id_attribute_group` int(10) unsigned NOT NULL DEFAULT "0",
		`id_shop` int(10) unsigned NOT NULL DEFAULT "1",
		KEY `id_attribute` (`id_attribute`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
	}
	
	/**
	 * 
	 * Generate data product attribute
	 */
	public function indexAttribute($id_product = null)
	{
		if (is_null($id_product))
			Db::getInstance()->execute('TRUNCATE '._DB_PREFIX_.'layered_product_attribute');
		else
			Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_product_attribute WHERE id_product = '.(int)$id_product);
		
		Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'layered_product_attribute` (`id_attribute`, `id_product`, `id_attribute_group`, `id_shop`)
		SELECT pac.id_attribute, pa.id_product, ag.id_attribute_group, '.(version_compare(_PS_VERSION_,'1.5','>') ? 'product_attribute_shop.`id_shop`' : '1').'
		FROM '._DB_PREFIX_.'product_attribute pa 
		'.(version_compare(_PS_VERSION_,'1.5','>') ? Shop::addSqlAssociation('product_attribute', 'pa') : '').'
		INNER JOIN '._DB_PREFIX_.'product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute 
		INNER JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute = pac.id_attribute) 
		INNER JOIN '._DB_PREFIX_.'attribute_group ag ON ag.id_attribute_group = a.id_attribute_group
		'.(is_null($id_product) ? '' : 'AND pa.id_product = '.(int)$id_product).'
		GROUP BY a.id_attribute, pa.id_product '.(version_compare(_PS_VERSION_,'1.5','>') ? ', product_attribute_shop.`id_shop`' : ''));
		
		return 1;
	}
	/*
	 * Url indexation
	 */
	public function indexUrl($ajax = false, $truncate = true)
	{
		if ($truncate)
			Db::getInstance()->execute('TRUNCATE '._DB_PREFIX_.'layered_friendly_url');
		
		$attribute_values_by_lang = array();
		$filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT lc.*, id_lang, name, link_rewrite, cl.id_category
		FROM '._DB_PREFIX_.'layered_category lc
		INNER JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = lc.id_category AND lc.id_category <> 1 )
		GROUP BY type, id_value, id_lang');
		if (!$filters)
			return;

		foreach ($filters as $filter)
			switch ($filter['type'])
			{
				case 'id_attribute_group':
					$attributes = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT agl.public_name name, a.id_attribute_group id_name, al.name value, a.id_attribute id_value, al.id_lang,
					liagl.url_name name_url_name, lial.url_name value_url_name
					FROM '._DB_PREFIX_.'attribute_group ag
					INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = ag.id_attribute_group)
					INNER JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute_group = ag.id_attribute_group)
					INNER JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = a.id_attribute)
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_group liag ON (liag.id_attribute_group = a.id_attribute_group)
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value liagl
					ON (liagl.id_attribute_group = ag.id_attribute_group AND liagl.id_lang = '.(int)$filter['id_lang'].')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_lang_value lial
					ON (lial.id_attribute = a.id_attribute  AND lial.id_lang = '.(int)$filter['id_lang'].')
					WHERE a.id_attribute_group = '.(int)$filter['id_value'].' AND agl.id_lang = al.id_lang AND agl.id_lang = '.(int)$filter['id_lang']);
					foreach ($attributes as $attribute)
					{
						if (!isset($attribute_values_by_lang[$attribute['id_lang']]))
							$attribute_values_by_lang[$attribute['id_lang']] = array();
						if (!isset($attribute_values_by_lang[$attribute['id_lang']]['c'.$attribute['id_name']]))
							$attribute_values_by_lang[$attribute['id_lang']]['c'.$attribute['id_name']] = array();
						$attribute_values_by_lang[$attribute['id_lang']]['c'.$attribute['id_name']][] = array(
							'name' => (!empty($attribute['name_url_name']) ? $attribute['name_url_name'] : $attribute['name']),
							'id_name' => 'c'.$attribute['id_name'],
							'value' => (!empty($attribute['value_url_name']) ? $attribute['value_url_name'] : $attribute['value']),
							'id_value' => $attribute['id_name'].'_'.$attribute['id_value'],
							'id_id_value' => $attribute['id_value'],
							'category_name' => $filter['link_rewrite'],
							'type' => $filter['type']);
					}
					break;
				
				case 'id_feature':
					$features = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT fl.name name, fl.id_feature id_name, fvl.id_feature_value id_value, fvl.value value, fl.id_lang, fl.id_lang,
					lifl.url_name name_url_name, lifvl.url_name value_url_name
					FROM '._DB_PREFIX_.'feature_lang fl
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature lif ON (lif.id_feature = fl.id_feature)
					INNER JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature = fl.id_feature)
					INNER JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fv.id_feature_value)
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_lang_value lifl
					ON (lifl.id_feature = fl.id_feature AND lifl.id_lang = '.(int)$filter['id_lang'].')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_value_lang_value lifvl
					ON (lifvl.id_feature_value = fvl.id_feature_value AND lifvl.id_lang = '.(int)$filter['id_lang'].')
					WHERE fl.id_feature = '.(int)$filter['id_value'].' AND fvl.id_lang = fl.id_lang AND fvl.id_lang = '.(int)$filter['id_lang']);
					foreach ($features as $feature)
					{
						if (!isset($attribute_values_by_lang[$feature['id_lang']]))
							$attribute_values_by_lang[$feature['id_lang']] = array();
						if (!isset($attribute_values_by_lang[$feature['id_lang']]['f'.$feature['id_name']]))
							$attribute_values_by_lang[$feature['id_lang']]['f'.$feature['id_name']] = array();
						$attribute_values_by_lang[$feature['id_lang']]['f'.$feature['id_name']][] = array(
							'name' => (!empty($feature['name_url_name']) ? $feature['name_url_name'] : $feature['name']),
							'id_name' => 'f'.$feature['id_name'],
							'value' => (!empty($feature['value_url_name']) ? $feature['value_url_name'] : $feature['value']),
							'id_value' => $feature['id_name'].'_'.$feature['id_value'],
							'id_id_value' => $feature['id_value'],
							'category_name' => $filter['link_rewrite'],
							'type' => $filter['type']);
					}
					break;
				
				case 'category':
					$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT cl.name, cl.id_lang, c.id_category
					FROM '._DB_PREFIX_.'category c
					INNER JOIN '._DB_PREFIX_.'category_lang cl ON (c.id_category = cl.id_category)
					WHERE cl.id_lang = '.(int)$filter['id_lang']);
					foreach ($categories as $category)
					{
						if (!isset($attribute_values_by_lang[$category['id_lang']]))
							$attribute_values_by_lang[$category['id_lang']] = array();
						if (!isset($attribute_values_by_lang[$category['id_lang']]['category']))
							$attribute_values_by_lang[$category['id_lang']]['category'] = array();
						$attribute_values_by_lang[$category['id_lang']]['category'][] = array('name' => $this->translateWord('Categories', $category['id_lang']),
						'id_name' => null, 'value' => $category['name'], 'id_value' => $category['id_category'],
						'category_name' => $filter['link_rewrite'], 'type' => $filter['type']);
					}
					break;
						
				case 'manufacturer':
					$manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
					SELECT m.name as name,l.id_lang as id_lang,  id_manufacturer
					FROM '._DB_PREFIX_.'manufacturer m , '._DB_PREFIX_.'lang l
					WHERE l.id_lang = '.(int)$filter['id_lang'].' ');
				
					foreach ($manufacturers as $manufacturer)
					{
						if (!isset($attribute_values_by_lang[$manufacturer['id_lang']]))
							$attribute_values_by_lang[$manufacturer['id_lang']] = array();
						if (!isset($attribute_values_by_lang[$manufacturer['id_lang']]['manufacturer']))
							$attribute_values_by_lang[$manufacturer['id_lang']]['manufacturer'] = array();
						$attribute_values_by_lang[$manufacturer['id_lang']]['manufacturer'][] = array('name' => $this->translateWord('Manufacturer', $manufacturer['id_lang']),
						'id_name' => null, 'value' => $manufacturer['name'], 'id_value' => $manufacturer['id_manufacturer'],
						'category_name' => $filter['link_rewrite'], 'type' => $filter['type']);
					}
					break;
				
				case 'quantity':
					$avaibility_list = array(
						$this->translateWord('Not available', (int)$filter['id_lang']),
						$this->translateWord('In stock', (int)$filter['id_lang'])
					);
					foreach ($avaibility_list as $key => $quantity)
						$attribute_values_by_lang[$filter['id_lang']]['quantity'][] = array('name' => $this->translateWord('Availability', (int)$filter['id_lang']),
						'id_name' => null, 'value' => $quantity, 'id_value' => $key, 'id_id_value' => 0,
						'category_name' => $filter['link_rewrite'], 'type' => $filter['type']);
					break;
				
				case 'condition':
					$condition_list = array(
						'new' => $this->translateWord('New', (int)$filter['id_lang']),
						'used' => $this->translateWord('Used', (int)$filter['id_lang']),
						'refurbished' => $this->translateWord('Refurbished', (int)$filter['id_lang'])
					);
					foreach ($condition_list as $key => $condition)
						$attribute_values_by_lang[$filter['id_lang']]['condition'][] = array('name' => $this->translateWord('Condition', (int)$filter['id_lang']),
						'id_name' => null, 'value' => $condition, 'id_value' => $key,
						'category_name' => $filter['link_rewrite'], 'type' => $filter['type']);
					break;
			}
		
		// Foreach langs
		foreach ($attribute_values_by_lang as $id_lang => $attribute_values)
		{
			// Foreach attributes generate a couple "/<attribute_name>_<atttribute_value>". For example: color_blue
			foreach ($attribute_values as $attribute)
					foreach ($attribute as $param)
					{
						$selected_filters = array();
						$link = '/'.str_replace('-', '_', Tools::link_rewrite($param['name'])).'-'.str_replace('-', '_', Tools::link_rewrite($param['value']));
						$selected_filters[$param['type']] = array();
						if (!isset($param['id_id_value']))
							$param['id_id_value'] = $param['id_value'];
						$selected_filters[$param['type']][$param['id_id_value']] = $param['id_value'];
						$url_key = md5($link);
						$id_layered_friendly_url = Db::getInstance()->getValue('SELECT id_layered_friendly_url
						FROM `'._DB_PREFIX_.'layered_friendly_url` WHERE `id_lang` = '.$id_lang.' AND `url_key` = \''.$url_key.'\'');
						if ($id_layered_friendly_url == false)
						{
							Db::getInstance()->AutoExecute(_DB_PREFIX_.'layered_friendly_url', array('url_key' => $url_key, 'data' => serialize($selected_filters), 'id_lang' => $id_lang), 'INSERT');
							$id_layered_friendly_url = Db::getInstance()->Insert_ID();
						}
					}
		}
		if ($ajax)
			return '{"result": 1}';
		else
			return 1;
	}
	
	public function translateWord($string, $id_lang ) 
	{
		static $_MODULES = array();
		global $_MODULE;

		$file = _PS_MODULE_DIR_.$this->name.'/'.Language::getIsoById($id_lang).'.php';

		if (!array_key_exists($id_lang, $_MODULES))
		{
			if (!file_exists($file))
				return $string;
			include($file);
			$_MODULES[$id_lang] = $_MODULE;
		}

		$string = str_replace('\'', '\\\'', $string);

		// set array key to lowercase for 1.3 compatibility
		$_MODULES[$id_lang] = array_change_key_case($_MODULES[$id_lang]);
		$current_key = '<{'.strtolower( $this->name).'}'.strtolower(_THEME_NAME_).'>'.strtolower($this->name).'_'.md5($string);
		$default_key = '<{'.strtolower( $this->name).'}prestashop>'.strtolower($this->name).'_'.md5($string);
			
		if (isset($_MODULES[$id_lang][$current_key]))
			$ret = stripslashes($_MODULES[$id_lang][$current_key]);
		else if (isset($_MODULES[$id_lang][Tools::strtolower($current_key)]))
			$ret = stripslashes($_MODULES[$id_lang][Tools::strtolower($current_key)]);
		else if (isset($_MODULES[$id_lang][$default_key]))
			$ret = stripslashes($_MODULES[$id_lang][$default_key]);
		else if (isset($_MODULES[$id_lang][Tools::strtolower($default_key)]))
			$ret = stripslashes($_MODULES[$id_lang][Tools::strtolower($default_key)]);
		else
			$ret = stripslashes($string);

		return str_replace('"', '&quot;', $ret);
	}
	
	public function hookProductListAssign($params)
	{
		global $smarty;
		if (version_compare(_PS_VERSION_,'1.5','<') && !Configuration::get('PS_LAYERED_INDEXED')
			|| version_compare(_PS_VERSION_,'1.5','>') && !Configuration::getGlobalValue('PS_LAYERED_INDEXED'))
			return;
		// Inform the hook was executed
		$params['hookExecuted'] = true;
		// List of product to overrride categoryController
		$params['catProducts'] = array();
		$selected_filters = $this->getSelectedFilters();
		$filter_block = $this->getFilterBlock($selected_filters);
		$title = '';
		if (is_array($filter_block['title_values']))
			foreach ($filter_block['title_values'] as $key => $val)
				$title .= ' – '.$key.' '.implode('/', $val);
		
		$smarty->assign('categoryNameComplement', $title);
		$this->getProducts($selected_filters, $params['catProducts'], $params['nbProducts'], $p, $n, $pages_nb, $start, $stop, $range);
		// Need a nofollow on the pagination links?
		$smarty->assign('no_follow', $filter_block['no_follow']);
	}
	
	public function hookAfterSaveProduct($params)
	{
		if (!$params['id_product'])
			return;
		
		self::indexProductPrices((int)$params['id_product']);
		$this->indexAttribute((int)$params['id_product']);
	}

	public function hookAfterSaveFeature($params)
	{
		if (!$params['id_feature'] || Tools::getValue('layered_indexable') === false)
			return;
		
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature WHERE id_feature = '.(int)$params['id_feature']);
		Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_feature VALUES ('.(int)$params['id_feature'].', '.(int)Tools::getValue('layered_indexable').')');
		
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature_lang_value WHERE id_feature = '.(int)$params['id_feature']); // don't care about the id_lang
		foreach (Language::getLanguages(false) as $language)
		{
			// Data are validated by method "hookPostProcessFeature"
			$id_lang = (int)$language['id_lang'];
			Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_feature_lang_value
			VALUES ('.(int)$params['id_feature'].', '.$id_lang.', \''.pSQL(Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang))).'\',
			\''.pSQL(Tools::getValue('meta_title_'.$id_lang), true).'\')');
		}
	}

	public function hookAfterSaveFeatureValue($params)
	{
		if (!$params['id_feature_value'])
			return;
		
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature_value_lang_value WHERE id_feature_value = '.(int)$params['id_feature_value']); // don't care about the id_lang
		foreach (Language::getLanguages(false) as $language)
		{
			// Data are validated by method "hookPostProcessFeatureValue"
			$id_lang = (int)$language['id_lang'];
			Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_feature_value_lang_value
			VALUES ('.(int)$params['id_feature_value'].', '.$id_lang.', \''.pSQL(Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang))).'\',
			\''.pSQL(Tools::getValue('meta_title_'.$id_lang), true).'\')');
		}
	}
	
	public function hookAfterDeleteFeatureValue($params)
	{
		if (!$params['id_feature_value'])
			return;
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature_value_lang_value WHERE id_feature_value = '.(int)$params['id_feature_value']);
	}
	
	public function hookPostProcessFeatureValue($params)
	{
		$this->hookPostProcessAttributeGroup($params);
	}
	
	public function hookFeatureValueForm($params)
	{
		$languages = Language::getLanguages(false);
		$default_form_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$lang_value = array();
		
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$return = '
				<script type="text/javascript">
					flag_fields = \'\';
				</script>';
		else
			$return = '';
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
		'SELECT url_name, meta_title, id_lang FROM '._DB_PREFIX_.'layered_indexable_feature_value_lang_value
		WHERE id_feature_value = '.(int)$params['id_feature_value']);
		if ($result)
			foreach ($result as $data)
				$lang_value[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);
		$return .= '<div class="clear"></div>
				<label>'.$this->l('URL:').'</label>
				<div class="margin-form">
				<script type="text/javascript">
					flag_fields += \'¤url_name¤meta_title\';
				</script>
				<div class="translatable">';
		foreach ($languages as $language)
			$return .= '
					<div class="lang_'.$language['id_lang'].'" id="url_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="url_name_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$lang_value[$language['id_lang']]['url_name'], true).'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}_<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Specific URL format in block layered generation').'</p>
					</div>';
		if (version_compare(_PS_VERSION_,'1.5','<'))
			$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'url_name', true, true);
		$return .= '
						</div>
						<div class="clear"></div>
					</div>
					<label>'.$this->l('Meta title:').' </label>
					<div class="margin-form">
						<div class="translatable">';
		foreach ($languages as $language)
			$return .= '
						<div class="lang_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
							<input size="33" type="text" name="meta_title_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$lang_value[$language['id_lang']]['meta_title'], true).'" />
							<p style="clear: both">'.$this->l('Specific format for meta title').'</p>
						</div>';
			
		if (version_compare(_PS_VERSION_,'1.5','<'))
			$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'meta_title', true, true);
		$return .= '
						</div>
						<div class="clear"></div>
					</div>';
		return $return;
	}
	
	public function hookAfterSaveAttribute($params)
	{
		if (!$params['id_attribute'])
			return;
		
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_lang_value WHERE id_attribute = '.(int)$params['id_attribute']); // don't care about the id_lang
		foreach (Language::getLanguages(false) as $language)
		{
			// Data are validated by method "hookPostProcessAttribute"
			$id_lang = (int)$language['id_lang'];
			Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_attribute_lang_value
			VALUES ('.(int)$params['id_attribute'].', '.$id_lang.', \''.pSQL(Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang))).'\',
			\''.pSQL(Tools::getValue('meta_title_'.$id_lang), true).'\')');
		}
	}
	
	public function hookAfterDeleteAttribute($params)
	{
		if (!$params['id_attribute'])
			return;
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_lang_value WHERE id_attribute = '.(int)$params['id_attribute']);
	}
	
	public function hookPostProcessAttribute($params)
	{
		$this->hookPostProcessAttributeGroup($params);
	}
	
	public function hookAttributeForm($params)
	{
		$languages = Language::getLanguages(false);
		$default_form_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$lang_value = array();
		
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$return = '
				<script type="text/javascript">
					flag_fields = \'\';
				</script>';
		else
			$return = '';
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
		'SELECT url_name, meta_title, id_lang FROM '._DB_PREFIX_.'layered_indexable_attribute_lang_value
		WHERE id_attribute = '.(int)$params['id_attribute']);
		if ($result)
			foreach ($result as $data)
				$lang_value[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);
		$return .= '<div class="clear"></div>
				<label>'.$this->l('URL:').'</label>
				<div class="margin-form">
				<script type="text/javascript">
					flag_fields += \'¤url_name¤meta_title\';
				</script>
				<div class="translatable">';
		foreach ($languages as $language)
			$return .= '
					<div class="lang_'.$language['id_lang'].'" id="url_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="url_name_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$lang_value[$language['id_lang']]['url_name'], true).'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}_<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Specific URL format in block layered generation').'</p>
					</div>';
		if (version_compare(_PS_VERSION_,'1.5','<'))
			$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'url_name', true, true);
		$return .= '
						</div>
						<div class="clear"></div>
					</div>
					<label>'.$this->l('Meta title:').' </label>
					<div class="margin-form">
						<div class="translatable">';
		foreach ($languages as $language)
			$return .= '
						<div class="lang_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
							<input size="33" type="text" name="meta_title_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$lang_value[$language['id_lang']]['meta_title'], true).'" />
							<p style="clear: both">'.$this->l('Specific format for meta title').'</p>
						</div>';
		if (version_compare(_PS_VERSION_,'1.5','<'))
			$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'meta_title', true, true);
		$return .= '
						</div>
						<div class="clear"></div>
					</div>';
		return $return;
	}
	
	public function hookPostProcessFeature($params)
	{
		$this->hookPostProcessAttributeGroup($params);
	}

	public function hookAfterDeleteFeature($params)
	{
		if (!$params['id_feature'])
			return;
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature WHERE id_feature = '.(int)$params['id_feature']);
	}
	
	public function hookAfterSaveAttributeGroup($params)
	{
		if (!$params['id_attribute_group'] || Tools::getValue('layered_indexable') === false)
			return;
		
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_group WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
		Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_attribute_group VALUES ('.(int)$params['id_attribute_group'].', '.(int)Tools::getValue('layered_indexable').')');

		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value WHERE id_attribute_group = '.(int)$params['id_attribute_group']); // don't care about the id_lang
		foreach (Language::getLanguages(false) as $language)
		{
			// Data are validated by method "hookPostProcessAttributeGroup"
			$id_lang = (int)$language['id_lang'];
			Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value
			VALUES ('.(int)$params['id_attribute_group'].', '.$id_lang.', \''.pSQL(Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang))).'\',
			\''.pSQL(Tools::getValue('meta_title_'.$id_lang), true).'\')');
		}
	}
	
	public function hookPostProcessAttributeGroup($params)
	{
		// Limit to one call
		static $once = false;
		if ($once)
			return;
		$once = true;
		
		$errors = array();
		foreach (Language::getLanguages(false) as $language)
		{
			$id_lang = $language['id_lang'];
			if (Tools::getValue('url_name_'.$id_lang))
				if (Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang)) != strtolower( Tools::getValue('url_name_'.$id_lang)))
				{
					// Here use the reference "errors" to stop saving process
					$params['errors'][] = Tools::displayError(sprintf($this->l('"%s" is not a valid url'), Tools::getValue('url_name_'.$id_lang)));
				}
		}
	}
	
	public function hookAfterDeleteAttributeGroup($params)
	{
		if (!$params['id_attribute_group'])
			return;

		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_group WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
	}
	
	public function hookAttributeGroupForm($params)
	{
		$languages = Language::getLanguages(false);
		$default_form_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$indexable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT indexable FROM '._DB_PREFIX_.'layered_indexable_attribute_group
		WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
		$lang_value = array();
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
		'SELECT url_name, meta_title, id_lang FROM '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value
		WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
		if ($result)
			foreach ($result as $data)
				$lang_value[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);

		if ($indexable === false)
			$on = true;
		else
			$on = (bool)$indexable;

		if (version_compare(_PS_VERSION_,'1.5','>'))
			$return = '
				<script type="text/javascript">
					flag_fields = \'\';
				</script>';
		else
			$return = '';
		
		$return .= '<div class="clear"></div>
				<label>'.$this->l('URL:').'</label>
				<div class="margin-form">
				<script type="text/javascript">
					flag_fields += \'¤url_name¤meta_title\';
				</script>
				<div class="translatable">';
		foreach ($languages as $language)
			$return .= '
					<div class="lang_'.$language['id_lang'].'" id="url_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="url_name_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$lang_value[$language['id_lang']]['url_name'], true).'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}_<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Specific URL format in block layered generation').'</p>
					</div>';
		if (version_compare(_PS_VERSION_,'1.5','<'))
			$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'url_name', true, true);
		$return .= '
						</div>
						<div class="clear"></div>
					</div>
					<label>'.$this->l('Meta title:').' </label>
					<div class="margin-form">
						<div class="translatable">';
		foreach ($languages as $language)
			$return .= '
						<div class="lang_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
							<input size="33" type="text" name="meta_title_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$lang_value[$language['id_lang']]['meta_title'], true).'" />
							<p style="clear: both">'.$this->l('Specific format for meta title').'</p>
						</div>';
		if (version_compare(_PS_VERSION_,'1.5','<'))
			$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'meta_title', true, true);
		$return .= '
						</div>
						<div class="clear"></div>
					</div>
			<label>'.$this->l('Indexable:').' </label>
				<div class="margin-form">
					<input type="radio" '.(($on) ? 'checked="checked"' : '').' value="1" id="indexable_on" name="layered_indexable">
					<label for="indexable_on" class="t"><img title="Yes" alt="Enabled" src="../img/admin/enabled.gif"></label>
					<input type="radio" '.((!$on) ? 'checked="checked"' : '').' value="0" id="indexable_off" name="layered_indexable">
					<label for="indexable_off" class="t"><img title="No" alt="Disabled" src="../img/admin/disabled.gif"></label>
					<p>'.$this->l('Use this attribute in URL generated by the layered navigation module').'</p>
				</div>';
		return $return;
	}
	
	public function hookFeatureForm($params)
	{
		$languages = Language::getLanguages(false);
		$default_form_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$indexable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT indexable FROM '._DB_PREFIX_.'layered_indexable_feature WHERE id_feature = '.(int)$params['id_feature']);
		$lang_value = array();
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
		'SELECT url_name, meta_title, id_lang FROM '._DB_PREFIX_.'layered_indexable_feature_lang_value
		WHERE id_feature = '.(int)$params['id_feature']);
		if ($result)
			foreach ($result as $data)
				$lang_value[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);
		 
		
		if ($indexable === false)
			$on = true;
		else
			$on = (bool)$indexable;
		
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$return = '
				<script type="text/javascript">
					flag_fields = \'\';
				</script>';
		else
			$return = '';
		
		$return .= '<div class="clear"></div>
				<label>'.$this->l('URL:').'</label>
				<div class="margin-form">
				<script type="text/javascript">
					flag_fields += \'¤url_name¤meta_title\';
				</script>
				<div class="translatable">';
		foreach ($languages as $language)
			$return .= '
					<div class="lang_'.$language['id_lang'].'" id="url_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="url_name_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$lang_value[$language['id_lang']]['url_name'], true).'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}_<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Specific URL format in block layered generation').'</p>
					</div>';
		if (version_compare(_PS_VERSION_,'1.5','<'))
			$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'url_name', true, true);
		$return .= '
						</div>
						<div class="clear"></div>
					</div>
					<label>'.$this->l('Meta title:').' </label>
					<div class="margin-form">
						<div class="translatable">';
		foreach ($languages as $language)
			$return .= '
						<div class="lang_'.$language['id_lang'].'" id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
							<input size="33" type="text" name="meta_title_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$lang_value[$language['id_lang']]['meta_title'], true).'" />
							<p style="clear: both">'.$this->l('Specific format for meta title').'</p>
						</div>';
		if (version_compare(_PS_VERSION_,'1.5','<'))
			$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'meta_title', true, true);
		$return .= '
						</div>
						<div class="clear"></div>
					</div>
			<label>'.$this->l('Indexable:').' </label>
				<div class="margin-form">
					<input type="radio" '.(($on) ? 'checked="checked"' : '').' value="1" id="indexable_on" name="layered_indexable">
					<label for="indexable_on" class="t"><img title="Yes" alt="Enabled" src="../img/admin/enabled.gif"></label>
					<input type="radio" '.((!$on) ? 'checked="checked"' : '').' value="0" id="indexable_off" name="layered_indexable">
					<label for="indexable_off" class="t"><img title="No" alt="Disabled" src="../img/admin/disabled.gif"></label>
					<p>'.$this->l('Use this attribute in URL generated by the layered navigation module').'</p>
				</div>';
		return $return;
	}
	
	/*
	 * $cursor $cursor in order to restart indexing from the last state
	 */
	public static function fullPricesIndexProcess($cursor = 0, $ajax = false, $smart = false)
	{
		if ($cursor == 0 && !$smart)
			self::installPriceIndexTable();
		
		return self::indexPrices($cursor, true, $ajax, $smart);
	}
	
	/*
	 * $cursor $cursor in order to restart indexing from the last state
	 */
	public static function pricesIndexProcess($cursor = 0, $ajax = false)
	{
		return self::indexPrices($cursor, false, $ajax);
	}
	
	private static function indexPrices($cursor = null, $full = false, $ajax = false, $smart = false)
	{
		if ($full)
			if (version_compare(_PS_VERSION_,'1.5','>'))
				$nb_products = (int)Db::getInstance()->getValue('
					SELECT count(DISTINCT p.`id_product`)
					FROM '._DB_PREFIX_.'product p
					INNER JOIN `'._DB_PREFIX_.'product_shop` ps
						ON (ps.`id_product` = p.`id_product` AND ps.`active` = 1)');
		else
				$nb_products = (int)Db::getInstance()->getValue('
					SELECT count(DISTINCT p.`id_product`)
					FROM '._DB_PREFIX_.'product p
					WHERE `active` = 1');
		else
			if (version_compare(_PS_VERSION_,'1.5','>'))
				$nb_products = (int)Db::getInstance()->getValue('
					SELECT COUNT(DISTINCT p.`id_product`) FROM `'._DB_PREFIX_.'product` p
					INNER JOIN `'._DB_PREFIX_.'product_shop` ps
						ON (ps.`id_product` = p.`id_product` AND ps.`active` = 1)
					LEFT JOIN  `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product)
					WHERE psi.id_product IS NULL');
			else
				$nb_products = (int)Db::getInstance()->getValue('
					SELECT COUNT(DISTINCT p.`id_product`) FROM `'._DB_PREFIX_.'product` p
					LEFT JOIN  `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product)
					WHERE `active` = 1 AND psi.id_product IS NULL');
		
		$max_executiontime = @ini_get('max_execution_time');
		if ($max_executiontime > 5 || $max_executiontime <= 0)
			$max_executiontime = 5;
		
		$start_time = microtime(true);
		
		do
		{
			$cursor = (int)self::indexPricesUnbreakable((int)$cursor, $full, $smart);
			$time_elapsed = microtime(true) - $start_time;
		}
		while ($cursor < $nb_products && (Tools::getMemoryLimit()) > memory_get_peak_usage() && $time_elapsed < $max_executiontime);
		
		if (($nb_products > 0 && !$full || $cursor < $nb_products && $full) && !$ajax)
		{
			$token = substr(Tools::encrypt('blocklayered/index'), 0, 10);
			if (Tools::usingSecureMode())
				$domain = Tools::getShopDomainSsl(true);
			else
				$domain = Tools::getShopDomain(true);
			
			if (!Tools::file_get_contents($domain.__PS_BASE_URI__.'modules/blocklayered/blocklayered-price-indexer.php?token='.$token.'&cursor='.(int)$cursor.'&full='.(int)$full))
				self::indexPrices((int)$cursor, (int)$full);
			return $cursor;
		}
		if ($ajax && $nb_products > 0 && $cursor < $nb_products && $full)
			return '{"cursor": '.$cursor.', "count": '.($nb_products - $cursor).'}';
		else if ($ajax && $nb_products > 0 && !$full)
			return '{"cursor": '.$cursor.', "count": '.($nb_products).'}';
		else
		{
			if (version_compare(_PS_VERSION_,'1.5','>'))
				Configuration::updateGlobalValue('PS_LAYERED_INDEXED', 1);
			else
				Configuration::updateValue('PS_LAYERED_INDEXED', 1);
				
			if ($ajax)
				return '{"result": "ok"}';
			else
				return -1;
		}
	}
	
	/*
	 * $cursor $cursor in order to restart indexing from the last state
	 */
	private static function indexPricesUnbreakable($cursor, $full = false, $smart = false)
	{
		static $length = 100; // Nb of products to index
		
		if (is_null($cursor))
			$cursor = 0;
		
		if ($full)
			if (version_compare(_PS_VERSION_,'1.5','>'))
			$query = '
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				INNER JOIN `'._DB_PREFIX_.'product_shop` ps
					ON (ps.`id_product` = p.`id_product` AND ps.`active` = 1)
				GROUP BY p.`id_product`
				ORDER BY p.`id_product` LIMIT '.(int)$cursor.','.(int)$length;
			else
				$query = '
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				WHERE `active` = 1
				GROUP BY p.`id_product`
				ORDER BY p.`id_product` LIMIT '.(int)$cursor.','.(int)$length;
		else
			if (version_compare(_PS_VERSION_,'1.5','>'))
			$query = '
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				INNER JOIN `'._DB_PREFIX_.'product_shop` ps
					ON (ps.`id_product` = p.`id_product` AND ps.`active` = 1)
				LEFT JOIN  `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product)
				WHERE psi.id_product IS NULL
				GROUP BY p.`id_product`
				ORDER BY p.`id_product` LIMIT 0,'.(int)$length;
			else
				$query = '
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN  `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product)
				WHERE `active` = 1 AND psi.id_product IS NULL
				GROUP BY p.`id_product`
				ORDER BY p.`id_product` LIMIT 0,'.(int)$length;
		
		foreach (Db::getInstance()->executeS($query) as $product)
			self::indexProductPrices((int)$product['id_product'], ($smart && $full));

		return (int)($cursor + $length);
	}
	
	public static function indexProductPrices($id_product, $smart = true)
	{
		static $groups = null;

		if (is_null($groups))
		{
			$groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_group FROM `'._DB_PREFIX_.'group_reduction`');
			if (!$groups)
				$groups = array();
		}
		
		$shop_list = array();
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$shop_list = Shop::getShops(false, null, true);
		else
			$shop_list[] = 0;
		
		foreach ($shop_list as $id_shop)
		{
			static $currency_list = null;
			
			if (is_null($currency_list))
			{
				if (version_compare(_PS_VERSION_,'1.5','>'))
					$currency_list = Currency::getCurrencies(false, 1, new Shop($id_shop));
				else
					$currency_list = Currency::getCurrencies(false, 1);
			}
			
			$min_price = array();
			$max_price = array();
			
			if ($smart)
				Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'layered_price_index` WHERE `id_product` = '.(int)$id_product.' AND `id_shop` = '.(int)$id_shop);
			
			if (Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX'))
			{
				if (version_compare(_PS_VERSION_,'1.5','>'))
					$max_tax_rate = Db::getInstance()->getValue('
						SELECT max(t.rate) max_rate
						FROM `'._DB_PREFIX_.'product_shop` p
						LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (trg.id_tax_rules_group = p.id_tax_rules_group AND p.id_shop = '.(int)$shop_list.')
						LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.id_tax_rules_group = trg.id_tax_rules_group)
						LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax AND t.active = 1)
						WHERE id_product = '.(int)$id_product.'
						GROUP BY id_product');
				else
					$max_tax_rate = Db::getInstance()->getValue('
						SELECT max(t.rate) max_rate
						FROM `'._DB_PREFIX_.'product` p
						LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (trg.id_tax_rules_group = p.id_tax_rules_group)
						LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.id_tax_rules_group = trg.id_tax_rules_group)
						LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax AND t.active = 1)
						WHERE id_product = '.(int)$id_product.'
						GROUP BY id_product');
			}
			else
				$max_tax_rate = 0;
			
			$product_min_prices = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT id_shop, id_currency, id_country, id_group, from_quantity
			FROM `'._DB_PREFIX_.'specific_price`
			WHERE id_product = '.(int)$id_product);
			
			// Get min price
			foreach ($currency_list as $currency)
			{
				$price = Product::priceCalculation($id_shop, (int)$id_product, null, null, null, null,
					$currency['id_currency'], null, null, false, 6, false, true, true,
					$specific_price_output, true);
				
				if (!isset($max_price[$currency['id_currency']]))
					$max_price[$currency['id_currency']] = 0;
				if (!isset($min_price[$currency['id_currency']]))
					$min_price[$currency['id_currency']] = null;
				if ($price > $max_price[$currency['id_currency']])
					$max_price[$currency['id_currency']] = $price;
				if ($price == 0)
					continue;
				if (is_null($min_price[$currency['id_currency']]) || $price < $min_price[$currency['id_currency']])
					$min_price[$currency['id_currency']] = $price;
			}
			
			foreach ($product_min_prices as $specific_price)
				foreach ($currency_list as $currency)
				{
					if ($specific_price['id_currency'] && $specific_price['id_currency'] != $currency['id_currency'])
						continue;
					$price = Product::priceCalculation((($specific_price['id_shop'] == 0) ? null : (int)$specific_price['id_shop']), (int)$id_product,
						null, (($specific_price['id_country'] == 0) ? null : $specific_price['id_country']), null, null,
						$currency['id_currency'], (($specific_price['id_group'] == 0) ? null : $specific_price['id_group']),
						$specific_price['from_quantity'], false, 6, false, true, true, $specific_price_output, true);
					
					if (!isset($max_price[$currency['id_currency']]))
						$max_price[$currency['id_currency']] = 0;
					if (!isset($min_price[$currency['id_currency']]))
						$min_price[$currency['id_currency']] = null;
					if ($price > $max_price[$currency['id_currency']])
						$max_price[$currency['id_currency']] = $price;
					if ($price == 0)
						continue;
					if (is_null($min_price[$currency['id_currency']]) || $price < $min_price[$currency['id_currency']])
						$min_price[$currency['id_currency']] = $price;
				}
			
			foreach ($groups as $group)
				foreach ($currency_list as $currency)
				{
					$price = Product::priceCalculation(null, (int)$id_product, null, null, null, null, (int)$currency['id_currency'], (int)$group['id_group'],
						null, false, 6, false, true, true, $specific_price_output, true);
					
					if (!isset($max_price[$currency['id_currency']]))
						$max_price[$currency['id_currency']] = 0;
					if (!isset($min_price[$currency['id_currency']]))
						$min_price[$currency['id_currency']] = null;
					if ($price > $max_price[$currency['id_currency']])
						$max_price[$currency['id_currency']] = $price;
					if ($price == 0)
						continue;
					if (is_null($min_price[$currency['id_currency']]) || $price < $min_price[$currency['id_currency']])
						$min_price[$currency['id_currency']] = $price;
				}
			
			$values = array();
			foreach ($currency_list as $currency)
				$values[] = '('.(int)$id_product.',
					'.(int)$currency['id_currency'].',
					'.$id_shop.',
					'.(int)$min_price[$currency['id_currency']].',
					'.(int)Tools::ps_round($max_price[$currency['id_currency']] * (100 + $max_tax_rate) / 100, 0).')';
			
			Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'layered_price_index` (id_product, id_currency, id_shop, price_min, price_max)
				VALUES '.implode(',', $values).'
				ON DUPLICATE KEY UPDATE id_product = id_product # avoid duplicate keys');
		}
	}

	public function hookLeftColumn($params)
	{
		return $this->generateFiltersBlock($this->getSelectedFilters());
	}

	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}

	public function hookHeader($params)
	{
		global $smarty, $cookie;
		
		// No filters => module disable
		if ($filter_block = $this->getFilterBlock($this->getSelectedFilters()))
			if ($filter_block['nbr_filterBlocks'] == 0)
				return false;
		
		if (Tools::getValue('id_category', Tools::getValue('id_category_layered', 1)) == 1)
			return;
		
		$id_lang = (int)$cookie->id_lang;
		$category = new Category((int)Tools::getValue('id_category'));

		// Generate meta title and meta description
		$category_title = (empty($category->meta_title[$id_lang]) ? $category->name[$id_lang] : $category->meta_title[$id_lang]);
		$title = '';
		$description = '';
		$keywords = '';
		if (is_array($filter_block['meta_values']))
			foreach ($filter_block['meta_values'] as $key => $val)
			{
				if (!empty($val['title']))
					$val['title'] = $val['title'].' ';

				foreach ($val['values'] as $value)
				{
					$title .= $category_title.' '.$val['title'].$value.' - ';
					$description .= $category_title.' '.$val['title'].$value.', ';
					$keywords .= $val['title'].$value.', ';
				}
			}
		// Title attributes (ex: <attr1> <value1>/<value2> - <attr2> <value1>)
		$title = strtolower(rtrim(substr($title, 0, -3)));
		// Title attributes (ex: <attr1> <value1>/<value2>, <attr2> <value1>)
		$description = strtolower(rtrim(substr($description, 0, -2)));
		// kewords attributes (ex: <attr1> <value1>, <attr1> <value2>, <attr2> <value1>)
		if (version_compare(_PS_VERSION_, '1.5', '>'))
			$category_metas = Meta::getMetaTags($id_lang, 'category', $title);
		else
			$category_metas = Tools::getMetaTags($id_lang, '', $title);

		if (!empty($title))
		{
			$smarty->assign('meta_title', ucfirst(substr($category_metas['meta_title'], 3)));
			$smarty->assign('meta_description', $description.'. '.$category_metas['meta_description']);
		}
		else
			$smarty->assign('meta_title', $category_metas['meta_title']);

		$keywords = substr(strtolower($keywords), 0, 1000);
		if (!empty($keywords))
			$smarty->assign('meta_keywords', rtrim($category_title.', '.$keywords.', '.$category_metas['meta_keywords'], ', '));

		if (version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$this->context->controller->addJS(($this->_path).'blocklayered.js');
			$this->context->controller->addJS(_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js');
			$this->context->controller->addJQueryUI('ui.slider');
			$this->context->controller->addCSS(($this->_path).'blocklayered-15.css', 'all');
			$this->context->controller->addJQueryPlugin('scrollTo');
		}
		else
		{
			Tools::addJS(($this->_path).'blocklayered.js');
			Tools::addJS(_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js');
			Tools::addCSS(_PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css', 'all');
			Tools::addCSS(($this->_path).'blocklayered.css', 'all');
			Tools::addJS(_PS_JS_DIR_.'jquery/jquery.scrollTo-1.4.2-min.js');
		}

		$filters = $this->getSelectedFilters();

		// Get non indexable attributes
		$attribute_group_list = Db::getInstance()->executeS('SELECT id_attribute_group FROM '._DB_PREFIX_.'layered_indexable_attribute_group WHERE indexable = 0');
		// Get non indexable features
		$feature_list = Db::getInstance()->executeS('SELECT id_feature FROM '._DB_PREFIX_.'layered_indexable_feature WHERE indexable = 0');

		$attributes = array();
		$features = array();

		$blacklist = array('weight', 'price');
		if (!Configuration::get('PS_LAYERED_FILTER_INDEX_CDT'))
			$blacklist[] = 'condition';
		if (!Configuration::get('PS_LAYERED_FILTER_INDEX_QTY'))
			$blacklist[] = 'quantity';
		if (!Configuration::get('PS_LAYERED_FILTER_INDEX_MNF'))
			$blacklist[] = 'manufacturer';
		if (!Configuration::get('PS_LAYERED_FILTER_INDEX_CAT'))
			$blacklist[] = 'category';

		foreach ($filters as $type => $val)
		{
			switch ($type)
			{
				case 'id_attribute_group':
					foreach ($val as $attr)
					{
						$attr_id = preg_replace('/_\d+$/', '', $attr);
						if (in_array($attr_id, $attributes) || in_array(array('id_attribute_group' => $attr_id), $attribute_group_list))
						{
							$smarty->assign('nobots', true);
							$smarty->assign('nofollow', true);
							return;
						}
						$attributes[] = $attr_id;
					}
					break;
				case 'id_feature':
					foreach ($val as $feat)
					{
						$feat_id = preg_replace('/_\d+$/', '', $feat);
						if (in_array($feat_id, $features) || in_array(array('id_feature' => $feat_id), $feature_list))
						{
							$smarty->assign('nobots', true);
							$smarty->assign('nofollow', true);
							return;
						}
						$features[] = $feat_id;
					}
					break;
				default:
					if (in_array($type, $blacklist))
					{
						if (count($val))
						{
							$smarty->assign('nobots', true);
							$smarty->assign('nofollow', true);
							return;
						}
					}
					elseif (count($val) > 1)
					{
						$smarty->assign('nobots', true);
						$smarty->assign('nofollow', true);
						return;
					}
					break;
			}
		}
	}

	public function hookFooter($params)
	{
		// No filters => module disable
		if ($filter_block = $this->getFilterBlock($this->getSelectedFilters()))
			if ($filter_block['nbr_filterBlocks'] == 0)
				return false;
		
		if (basename($_SERVER['PHP_SELF']) == 'category.php' && version_compare(_PS_VERSION_, '1.5', '<')
			|| version_compare(_PS_VERSION_, '1.5', '>') && Dispatcher::getInstance()->getController() == 'category')
			return '
			<script type="text/javascript">
				//<![CDATA[
				$(document).ready(function()
				{
					$(\'#selectPrductSort\').unbind(\'change\').bind(\'change\', function()
					{
						reloadContent();
					})
				});
				//]]>
			</script>';
	}

	public function hookCategoryAddition($params)
	{
		$this->rebuildLayeredCache(array(), array((int)$params['category']->id));
	}

	public function hookCategoryUpdate($params)
	{
		/* The category status might (active, inactive) have changed, we have to update the layered cache table structure */
		if (isset($params['category']) && !$params['category']->active)
			$this->hookCategoryDeletion($params);
	}

	public function hookCategoryDeletion($params)
	{
		$layered_filter_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'layered_filter');
		foreach ($layered_filter_list as $layered_filter)
		{
			$data = self::unSerialize($layered_filter['filters']);
			if (in_array((int)$params['category']->id, $data['categories']))
			{
				unset($data['categories'][array_search((int)$params['category']->id, $data['categories'])]);
				Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'layered_filter` SET `filters` = \''.pSQL(serialize($data)).'\' WHERE `id_layered_filter` = '.(int)$layered_filter['id_layered_filter'].'');
			}
		}
		$this->buildLayeredCategories();
	}

	public function getContent()
	{
		global $cookie;

		$html = '';

		if (Tools::isSubmit('SubmitFilter'))
		{
			if (!Tools::getValue('layered_tpl_name'))
				$html .= '
				<div class="error">
					<span style="float:right">
						<a href="" id="hideError"><img src="../img/admin/close.png" alt="X"></a>
					</span>
					<img src="../img/admin/error2.png">'.$this->l('Filter template name required (cannot be empty)').'
				</div>';
			else
			{
				if (isset($_POST['id_layered_filter']) && $_POST['id_layered_filter'])
				{
					Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)Tools::getValue('id_layered_filter'));
					$this->buildLayeredCategories();
				}

				if (Tools::getValue('scope') == 1)
				{
					Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'layered_filter');
					$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_category FROM '._DB_PREFIX_.'category');
					foreach ($categories as $category)
						$_POST['categoryBox'][] = (int)$category['id_category'];
				}

				if (version_compare(_PS_VERSION_, '1.5', '>'))
				{
					$id_layered_filter = (int)$_POST['id_layered_filter'];
					if (!$id_layered_filter)
						$id_layered_filter = (int)Db::getInstance()->Insert_ID();

					$shop_list = array();
					if (isset($_POST['checkBoxShopAsso_layered_filter']))
					{
						foreach ($_POST['checkBoxShopAsso_layered_filter'] as $id_shop => $row)
						{
							$assos[] = array('id_object' => (int)$id_layered_filter, 'id_shop' => (int)$id_shop);
							$shop_list[] = (int)$id_shop;
						}
					}
					else
						$shop_list = array(Context::getContext()->shop->id);
				}
				else
					$shop_list = array(0);

				if (count($_POST['categoryBox']))
				{
					/* Clean categoryBox before use */
					if (isset($_POST['categoryBox']) && is_array($_POST['categoryBox']))
						foreach ($_POST['categoryBox'] as &$category_box_tmp)
							$category_box_tmp = (int)$category_box_tmp;

					$filter_values = array();
					foreach ($_POST['categoryBox'] as $idc)
						$filter_values['categories'][] = (int)$idc;
					$filter_values['shop_list'] = $shop_list;

					$values = false;
					foreach ($_POST['categoryBox'] as $id_category_layered)
					{
						foreach ($_POST as $key => $value)
							if (substr($key, 0, 17) == 'layered_selection' && $value == 'on')
							{
								$values = true;
								$type = 0;
								$limit = 0;
								if (Tools::getValue($key.'_filter_type'))
									$type = Tools::getValue($key.'_filter_type');
								if (Tools::getValue($key.'_filter_show_limit'))
									$limit = Tools::getValue($key.'_filter_show_limit');

								$filter_values[$key] = array(
									'filter_type' => (int)$type,
									'filter_show_limit' => (int)$limit
								);
							}
					}

					if (version_compare(_PS_VERSION_, '1.5', '>'))
					{
						Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_filter_shop WHERE `id_layered_filter` = '.(int)$id_layered_filter);
						if (isset($assos))
							foreach ($assos as $asso)
								Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_filter_shop (`id_layered_filter`, `id_shop`)
									VALUES('.$id_layered_filter.', '.(int)$asso['id_shop'].')');
					}

					$values_to_insert = array(
						'name' => pSQL(Tools::getValue('layered_tpl_name')),
						'filters' => pSQL(serialize($filter_values)),
						'n_categories' => (int)count($filter_values['categories']),
						'date_add' => date('Y-m-d H:i:s'));
					if (isset($_POST['id_layered_filter']) && $_POST['id_layered_filter'])
						$values_to_insert['id_layered_filter'] = (int)Tools::getValue('id_layered_filter');

					Db::getInstance()->autoExecute(_DB_PREFIX_.'layered_filter', $values_to_insert, 'INSERT');
					$this->buildLayeredCategories();
					
					$html .= '<div class="conf">'.(version_compare(_PS_VERSION_,'1.5','>') ? '' : '<img src="../img/admin/ok2.png" alt="" />').
						$this->l('Your filter').' "'.Tools::safeOutput(Tools::getValue('layered_tpl_name')).'" '.
						((isset($_POST['id_layered_filter']) && $_POST['id_layered_filter']) ? $this->l('was updated successfully.') : $this->l('was added successfully.')).'</div>';
				}
			}
		}
		else if (Tools::isSubmit('submitLayeredSettings'))
		{
			Configuration::updateValue('PS_LAYERED_HIDE_0_VALUES', (int)Tools::getValue('ps_layered_hide_0_values'));
			Configuration::updateValue('PS_LAYERED_SHOW_QTIES', (int)Tools::getValue('ps_layered_show_qties'));
			Configuration::updateValue('PS_LAYERED_FULL_TREE', (int)Tools::getValue('ps_layered_full_tree'));
			Configuration::updateValue('PS_LAYERED_FILTER_PRICE_USETAX', (int)Tools::getValue('ps_layered_filter_price_usetax'));
			Configuration::updateValue('PS_LAYERED_FILTER_CATEGORY_DEPTH', (int)Tools::getValue('ps_layered_filter_category_depth'));
			Configuration::updateValue('PS_LAYERED_FILTER_INDEX_QTY', (int)Tools::getValue('ps_layered_filter_index_availability'));
			Configuration::updateValue('PS_LAYERED_FILTER_INDEX_CDT', (int)Tools::getValue('ps_layered_filter_index_condition'));
			Configuration::updateValue('PS_LAYERED_FILTER_INDEX_MNF', (int)Tools::getValue('ps_layered_filter_index_manufacturer'));
			Configuration::updateValue('PS_LAYERED_FILTER_INDEX_CAT', (int)Tools::getValue('ps_layered_filter_index_category'));

			$html .= '
			<div class="conf">'.
			(version_compare(_PS_VERSION_,'1.5','>') ? '' : '<img src="../img/admin/ok2.png" alt="" />').$this->l('Settings saved successfully').'
			</div>';
		}
		else if (isset($_GET['deleteFilterTemplate']))
		{
			$layered_values = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT filters 
			FROM '._DB_PREFIX_.'layered_filter 
			WHERE id_layered_filter = '.(int)$_GET['id_layered_filter']);
			
			if ($layered_values)
			{
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)$_GET['id_layered_filter'].' LIMIT 1');
				$this->buildLayeredCategories();

				$html .= '
				<div class="conf">'.(version_compare(_PS_VERSION_,'1.5','>') ? '' : '<img src="../img/admin/ok2.png" alt="" />').'
					'.$this->l('Filter template deleted, categories updated (reverted to default Filter template).').'
				</div>';
			}
			else
			{
				$html .= '
				<div class="error">
					<img src="../img/admin/error.png" alt="" title="" /> '.$this->l('Filter template not found').'
				</div>';
			}
		}

		$html .= '
		<div id="ajax-message-ok" class="conf ajax-message" style="display: none">
			'.(version_compare(_PS_VERSION_,'1.5','>') ? '' : '<img src="../img/admin/ok2.png" alt="" />').'<span class="message"></span>
		</div>
		<div id="ajax-message-ko" class="error ajax-message" style="display: none">
			'.(version_compare(_PS_VERSION_,'1.5','>') ? '' : '<img src="../img/admin/errors.png" alt="" />').'<span class="message"></span>
		</div>
		<h2>'.$this->l('Layered navigation').'</h2>
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Indexes and caches').'</legend>
			<span id="indexing-warning" style="display: none; color:red; font-weight: bold">'.$this->l('Indexing is in progress. Please do not leave this page').'<br/><br/></span>';

		if (version_compare(_PS_VERSION_, '1.5', '<') && !Configuration::get('PS_LAYERED_INDEXED')
			|| version_compare(_PS_VERSION_, '1.5', '>') && !Configuration::getGlobalValue('PS_LAYERED_INDEXED'))
			$html .= '
			<script type="text/javascript">
			$(document).ready(function() {
				$(\'#url-indexer\').click();
				$(\'#full-index\').click();
			});
			</script>';
		
		$category_ist = array();
		foreach (Db::getInstance()->executeS('SELECT id_category FROM `'._DB_PREFIX_.'category`') as $category)
			if ($category['id_category'] != 1)
				$category_ist[] = $category['id_category'];
		
		$domain = Tools::getProtocol(Tools::usingSecureMode()).$_SERVER['HTTP_HOST'];

		$html .= '
			<a class="bold ajaxcall-recurcive"
			style="width: 250px; text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
			href="'.$domain.__PS_BASE_URI__.'modules/blocklayered/blocklayered-price-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'">'.
			$this->l('Index all missing prices').'</a>
			<br />
			<a class="bold ajaxcall-recurcive"
			style="width: 250px; text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="full-index"
			href="'.$domain.__PS_BASE_URI__.'modules/blocklayered/blocklayered-price-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&full=1">'.
			$this->l('Rebuild entire price index').'</a>
			<br />
			<a class="bold ajaxcall" id="attribute-indexer" rel="attribute"
			style="width: 250px; text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="full-index"
			href="'.$domain.__PS_BASE_URI__.'modules/blocklayered/blocklayered-attribute-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'">'.
			$this->l('Build attribute index').'</a>
			<br />
			<a class="bold ajaxcall" id="url-indexer" rel="price"
			style="width: 250px; text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="full-index"
			href="'.$domain.__PS_BASE_URI__.'modules/blocklayered/blocklayered-url-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&truncate=1">'.
			$this->l('Build URL index').'</a>
			<br />
			<br />
			'.$this->l('You can set a cron job that will rebuild price index using the following URL:').'<br /><b>'.
			$domain.__PS_BASE_URI__.'modules/blocklayered/blocklayered-price-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&full=1</b>
			<br />
			'.$this->l('You can set a cron job that will rebuild URL index using the following URL:').'<br /><b>'.
			$domain.__PS_BASE_URI__.'modules/blocklayered/blocklayered-url-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&truncate=1</b>
			<br />
			'.$this->l('You can set a cron job that will rebuild attribute index using the following URL:').'<br /><b>'.
			$domain.__PS_BASE_URI__.'modules/blocklayered/blocklayered-attribute-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'</b>
			<br /><br />
			'.$this->l('A nightly rebuild is recommended.').'
			<script type="text/javascript">
				$(\'.ajaxcall\').click(function() {
					if (this.legend == undefined)
						this.legend = $(this).html();
						
					if (this.running == undefined)
						this.running = false;
					
					if (this.running == true)
						return false;
					
					$(\'.ajax-message\').hide();
					
					this.running = true;
					
					if (typeof(this.restartAllowed) == \'undefined\' || this.restartAllowed)
					{
						$(this).html(this.legend+\' '.addslashes($this->l('(in progress)')).'\');
						$(\'#indexing-warning\').show();
					}
						
					this.restartAllowed = false;
					var type = $(this).attr(\'rel\');
					
					$.ajax({
						url: this.href+\'&ajax=1\',
						context: this,
						dataType: \'json\',
						cache: \'false\',
						success: function(res)
						{
							this.running = false;
							this.restartAllowed = true;
							$(\'#indexing-warning\').hide();
							$(this).html(this.legend);
							if (type == \'price\')
								$(\'#ajax-message-ok span\').html(\''.addslashes($this->l('URL indexation finished')).'\');
							else
								$(\'#ajax-message-ok span\').html(\''.addslashes($this->l('Attribute indexation finished')).'\');
							$(\'#ajax-message-ok\').show();
							return;
						},
						error: function(res)
						{
							this.restartAllowed = true;
							$(\'#indexing-warning\').hide();
							if (type == \'price\')
								$(\'#ajax-message-ko span\').html(\''.addslashes($this->l('URL indexation failed')).'\');
							else
								$(\'#ajax-message-ko span\').html(\''.addslashes($this->l('Attribute indexation failed')).'\');
							$(\'#ajax-message-ko\').show();
							$(this).html(this.legend);
							
							this.running = false;
						}
					});
					return false;
				});
				$(\'.ajaxcall-recurcive\').each(function(it, elm) {
					$(elm).click(function() {
						if (this.cursor == undefined)
							this.cursor = 0;
						
						if (this.legend == undefined)
							this.legend = $(this).html();
							
						if (this.running == undefined)
							this.running = false;
						
						if (this.running == true)
							return false;
						
						$(\'.ajax-message\').hide();
						
						this.running = true;
						
						if (typeof(this.restartAllowed) == \'undefined\' || this.restartAllowed)
						{
							$(this).html(this.legend+\' '.addslashes($this->l('(in progress)')).'\');
							$(\'#indexing-warning\').show();
						}
							
						this.restartAllowed = false;
						
						$.ajax({
							url: this.href+\'&ajax=1&cursor=\'+this.cursor,
							context: this,
							dataType: \'json\',
							cache: \'false\',
							success: function(res)
							{
								this.running = false;
								if (res.result)
								{
									this.cursor = 0;
									$(\'#indexing-warning\').hide();
									$(this).html(this.legend);
									$(\'#ajax-message-ok span\').html(\''.addslashes($this->l('Price indexation finished')).'\');
									$(\'#ajax-message-ok\').show();
									return;
								}
								this.cursor = parseInt(res.cursor);
								$(this).html(this.legend+\' '.addslashes($this->l('(in progress, %s products price to index)')).'\'.replace(\'%s\', res.count));
								$(this).click();
							},
							error: function(res)
							{
								this.restartAllowed = true;
								$(\'#indexing-warning\').hide();
								$(\'#ajax-message-ko span\').html(\''.addslashes($this->l('Price indexation failed')).'\');
								$(\'#ajax-message-ko\').show();
								$(this).html(this.legend);
								
								this.cursor = 0;
								this.running = false;
							}
						});
						return false;
					});
				});
			</script>
		</fieldset>
		<br />
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Existing filter templates').'</legend>';

		$filters_templates = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'layered_filter ORDER BY date_add DESC');
		if (count($filters_templates))
		{
			$html .= '<p>'.count($filters_templates).' '.$this->l('filter templates are configured:').'</p>
			<table id="table-filter-templates" class="table" style="width: 700px;">
				<tr>
					<th>'.$this->l('ID').'</th>
					<th>'.$this->l('Name').'</th>
					<th>'.$this->l('Categories').'</th>
					<th>'.$this->l('Created on').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>';

			foreach ($filters_templates as $filters_template)
			{
				/* Clean request URI first */
				$_SERVER['REQUEST_URI'] = preg_replace('/&deleteFilterTemplate=[0-9]*&id_layered_filter=[0-9]*/', '', $_SERVER['REQUEST_URI']);
				
				$html .= '
				<tr>
					<td>'.(int)$filters_template['id_layered_filter'].'</td>
					<td style="text-align: left; padding-left: 10px; width: 270px;">'.$filters_template['name'].'</td>
					<td style="text-align: center;">'.(int)$filters_template['n_categories'].'</td>
					<td>'.Tools::displayDate($filters_template['date_add'], (int)$cookie->id_lang, true).'</td>
					<td>
						<a href="#" onclick="return updElements('.($filters_template['n_categories'] ? 0 : 1).', '.(int)$filters_template['id_layered_filter'].');">
						<img src="../img/admin/edit.gif" alt="" title="'.$this->l('Edit').'" /></a> 
						<a href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&deleteFilterTemplate=1&id_layered_filter='.(int)$filters_template['id_layered_filter'].'"
						onclick="return confirm(\''.addslashes(sprintf($this->l('Delete filter template #%d?'), (int)$filters_template['id_layered_filter'])).'\');">
						<img src="../img/admin/delete.gif" alt="" title="'.$this->l('Delete').'" /></a>
					</td>
				</tr>';
			}

			$html .= '
			</table>';
		}
		else
			$html .= $this->l('No filter template found.');

		$html .= '
		</fieldset><br />
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Build your own filter template').'</legend>
			<link rel="stylesheet" href="'._PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css" />
			<style type="text/css">
				#error-filter-name { display: none; }
				#layered_container_left ul, #layered_container_right ul { list-style-type: none; padding-left: 0px; }
				.ui-effects-transfer { border: 1px solid #CCC; }
				.ui-state-highlight { height: 1.5em; line-height: 1.2em; }
				ul#selected_filters, #layered_container_right ul { list-style-type: none; margin: 0; padding: 0; }
				ul#selected_filters li, #layered_container_right ul li { width: 326px; font-size: 11px; padding: 8px 9px 7px 20px; height: 14px; margin-bottom: 5px; }
				ul#selected_filters li span.ui-icon { position: absolute; margin-top: -2px; margin-left: -18px; }
				#layered_container_right ul li span { display: none; }
				#layered_container_right ul li { padding-left: 8px; position: relative; }
				#layered_container_left ul li { cursor: move; position: relative; }
				#layered-cat-counter { display: none; }
				#layered-step-2, #layered-step-3 { display: none; }
				#layered-step-2 h3 { margin-top: 0; }
				#table-filter-templates tr th, #table-filter-templates tr td { text-align: center; }
				.filter_type { width: 70px; position: absolute; right: 53px; top: 5px;}
				.filter_show_limit { position: absolute; width: 40px; right: 5px; top: 5px; }
				#layered-step-3 .alert { width: auto; }
				#fancybox-content {
					height: 400px !important;
					overflow: auto !important;
				}
			</style>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" onsubmit="return checkForm();">';
			
		$html .= '
			<h2>'.$this->l('Step 1/3 - Select categories').'</h2>
			<p style="margin-top: 20px;">
				<span style="color: #585A69;display: block;float: left;font-weight: bold;text-align: right;width: 200px;" >'.$this->l('Use this template for:').'</span>
				<input type="radio" id="scope_1" name="scope" value="1" style="margin-left: 15px;" onclick="$(\'#error-treeview\').hide(); $(\'#layered-step-2\').show(); updElements(1, 0);" /> 
				<label for="scope_1" style="float: none;">'.$this->l('All categories').'</label>
				<input type="radio" id="scope_2" name="scope" value="2" style="margin-left: 15px;" class="layered-category-selection" onclick="$(\'label a#inline\').click(); $(\'#layered-step-2\').show();" /> 
				<style>
					.link {
						color: black;
						cursor: pointer;
						text-decoration: underline;
					}
					.link:hover {
						color: gray;
					}
				</style>
				<label for="scope_2" style="float: none;"><a id="inline" href="#layered-categories-selection" style="text-decoration: underline;"></a>'.preg_replace('/\*([^*]+)\*/Usi', '<span class="link">$1</span>', $this->l('*Specific* categories')).'
				(<span id="layered-cat-counter"></span> '.$this->l('selected').')</label>
			</p>';
		
		if (version_compare(_PS_VERSION_,'1.5','>'))
		{
			$shops = Shop::getShops(true, null, true);
			if (count($shops) > 1)
			{
				$helper = new HelperForm();
				$helper->id = null;
				$helper->table = 'layered_filter';
				$helper->identifier = 'id_layered_filter';
				
				if (Shop::isFeatureActive())
				{
					$html .= '<span style="color: #585A69;display: block;float: left;font-weight: bold;text-align: right;width: 200px;" >'.$this->l('Choose shop association:').'</span>';
					$html .= '<div id="shop_association" style="width: 300px;margin-left: 215px;">'.$helper->renderAssoShop().'</div>';
				}
			}
		}
		
		$html .= '
			<div id="error-treeview" class="error" style="display: none;">
				<img src="../img/admin/error2.png" alt="" /> '.$this->l('Please select at least one specific category or select "All categories".').'
			</div>
			<div style="display: none;">
				<div id="layered-categories-selection" style="padding: 10px; text-align: left;">
					<h2>'.$this->l('Categories using this template').'</h2>
					<ol style="padding-left: 20px;">
						<li>'.$this->l('Select one ore more category using this filter template').'</li>
						<li>'.$this->l('Press "Save this selection" or close the window to save').'</li>
					</ol>';

			$selected_cat = array();
			// Translations are not automatic for the moment ;)
			if (version_compare(_PS_VERSION_,'1.5','>'))
			{
				if (Shop::getContext() == Shop::CONTEXT_SHOP)
				{
					$root_category = Category::getRootCategory();
					$root_category = array('id_category' => $root_category->id_category, 'name' => $root_category->name);
				}
				else
					$root_category = array('id_category' => '0', 'name' => $this->l('Root'));
				$helper = new Helper();
				$html .= $helper->renderCategoryTree(null, $selected_cat, 'categoryBox');
			}
			else
			{
				$trads = array(
					 'Home' => $this->l('Home'),
					 'selected' => $this->l('selected'),
					 'Collapse All' => $this->l('Collapse All'),
					 'Expand All' => $this->l('Expand All'),
					 'Check All' => $this->l('Check All'),
					 'Uncheck All'  => $this->l('Uncheck All'),
					 'search'  => $this->l('Search a category')
				);
				$html .= Helper::renderAdminCategorieTree($trads, $selected_cat, 'categoryBox');
			}
			
			$html .= '
					<br />
					<center><input type="button" class="button" value="'.$this->l('Save this selection').'" onclick="$.fancybox.close();" /></center>
				</div>
			</div>
			<div id="layered-step-2">
				<hr size="1" noshade />
				<h2>'.$this->l('Step 2/3 - Select filters').'</h2>
				<div id="layered_container">
					<div id="layered_container_left" style="width: 360px; float: left; height: 200px; overflow-y: auto;">
						<h3>'.$this->l('Selected filters').' <span id="num_sel_filters">(0)</span></h3>
						<p id="no-filters">'.$this->l('No filters selected yet.').'</p>
						<ul id="selected_filters"></ul>
					</div>
					<div id="layered-ajax-refresh">
					'.$this->ajaxCallBackOffice().'
					</div>
				</div>
				<div class="clear"></div>
				<hr size="1" noshade />';
				
			if (version_compare(_PS_VERSION_,'1.5','>'))
			{
				$this->context->controller->addJQueryPlugin('fancybox');
				$this->context->controller->addJQueryUI('ui.sortable');
				$this->context->controller->addJQueryUI('ui.draggable');
				$this->context->controller->addJQueryUI('effects.transfer');
				$id_lang = Context::getContext()->cookie->id_lang;
			}
			else
			{
				$html .= '<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery-ui-1.8.10.custom.min.js"></script>
					<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery.fancybox-1.3.4.js"></script>
					<link type="text/css" rel="stylesheet" href="'.__PS_BASE_URI__.'css/jquery.fancybox-1.3.4.css" />';
				$id_lang = (int)$cookie->id_lang;
				
			}
			
			$html .= '
				<script type="text/javascript">
					function updLayCounters(showAlert)
					{
						$(\'#num_sel_filters\').html(\'(\'+$(\'ul#selected_filters\').find(\'li\').length+\')\');
						$(\'#num_avail_filters\').html(\'(\'+$(\'#layered_container_right ul\').find(\'li\').length+\')\');
						
						if ($(\'ul#selected_filters\').find(\'li\').length >= 1)
						{
							$(\'#layered-step-3\').show();
							$(\'#layered-step-3 .alert\').hide();
						}
						else
						{
							if (showAlert)
								$(\'#layered-step-3\').show();
							else
								$(\'#layered-step-3\').hide();
							
							$(\'#layered-step-3 .alert\').show();
							
						}
					}

					function updPositions()
					{
						$(\'#layered_container_left li\').each(function(idx) {
							$(this).find(\'span.position\').html(parseInt(1+idx)+\'. \');
						});
					}

					function updCatCounter()
					{
						$(\'#layered-cat-counter\').html($(\'#categories-treeview\').find(\'input:checked\').length);
						$(\'#layered-cat-counter\').show();
					}

					function updHeight()
					{
						$(\'#layered_container_left\').css(\'height\', 30+(1+$(\'#layered_container_left\').find(\'li\').length)*34);
						$(\'#layered_container_right\').css(\'height\', 30+(1+$(\'#layered_container_right\').find(\'li\').length)*34);
					}

					function updElements(all, id_layered_filter)
					{
						if ($(\'#error-treeview\').is(\':hidden\'))
							$(\'#layered-step-2\').show();
						else
							$(\'#layered-step-2\').hide();
						$(\'#layered-ajax-refresh\').css(\'background-color\', \'black\');
						$(\'#layered-ajax-refresh\').css(\'opacity\', \'0.2\');
						$(\'#layered-ajax-refresh\').html(\'<div style="margin: 0 auto; padding: 10px; text-align: center;">\'
						+\'<img src="../img/admin/ajax-loader-big.gif" alt="" /><br /><p style="color: white;">'.addslashes($this->l('Loading...')).'</p></div>\');
						
						$.ajax(
						{
							type: \'POST\',
							url: \''.__PS_BASE_URI__.'\' + \'modules/blocklayered/blocklayered-ajax-back.php\',
							data: \'layered_token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&id_lang='.$id_lang.'&\'
								+(all ? \'\' : $(\'input[name="categoryBox[]"]\').serialize()+\'&\')
								+(id_layered_filter ? \'id_layered_filter=\'+parseInt(id_layered_filter) : \'\')
								+\'&base_folder='.urlencode(_PS_ADMIN_DIR_).'\',
							success: function(result)
							{
								$(\'#layered-ajax-refresh\').css(\'background-color\', \'transparent\');
								$(\'#layered-ajax-refresh\').css(\'opacity\', \'1\');
								$(\'#layered-ajax-refresh\').html(result);
								
								$(\'#layered_container_right li input\').each(function() {
									if ($(\'#layered_container_left\').find(\'input[id="\'+$(this).attr(\'id\')+\'"]\').length > 0)
										$(this).parent().remove();
								});
								
								updHeight();
								updLayCounters(true);
							}
						});
						return false;
					}
					
					function checkForm()
					{
						if ($(\'#layered_tpl_name\').val() == \'\')
						{
							$(\'#error-filter-name\').show();
							return false;
						}
						else if ($(\'#scope_1\').attr(\'checked\') && $(\'#n_existing\').val() > 0)
							if (!confirm(\''.addslashes($this->l('You selected -All categories-, all existing filter templates will be deleted, OK?')).'\'))
								return false;

						return true;
					}

					function launch()
					{
						$(\'#layered_container input\').live(\'click\', function ()
						{
							if ($(this).parent().hasClass(\'layered_right\'))
							{
								$(\'p#no-filters\').hide();
								$(this).parent().css(\'background\', \'url("../img/jquery-ui/ui-bg_glass_100_fdf5ce_1x400.png") repeat-x scroll 50% 50% #FDF5CE\');
								$(this).parent().removeClass(\'layered_right\');
								$(this).parent().addClass(\'layered_left\');
								$(this).effect(\'transfer\', { to: $(\'#layered_container_left ul#selected_filters\') }, 300, function() {
									$(this).parent().appendTo(\'ul#selected_filters\');
									updLayCounters(false);
									updHeight();
									updPositions();
								});
							}
							else
							{
								$(this).parent().css(\'background\', \'url("../img/jquery-ui/ui-bg_glass_100_f6f6f6_1x400.png") repeat-x scroll 50% 50% #F6F6F6\');
								$(this).effect(\'transfer\', { to: $(\'#layered_container_right ul#all_filters\') }, 300, function() {
									$(this).parent().removeClass(\'layered_left\');
									$(this).parent().addClass(\'layered_right\');
									$(this).parent().appendTo(\'ul#all_filters\');
									updLayCounters(true);
									updHeight();
									updPositions();
									if ($(\'#layered_container_left ul\').length == 0)
										$(\'p#no-filters\').show();
								});
							}
							enableSortable();
						});
						
						$(\'label a#inline\').fancybox({ 
							\'hideOnContentClick\': false,
							\'onClosed\': function() {
								lock_treeview_hidding = false;
								$(\'#categories-treeview\').parent().parent().hide();
								updCatCounter();
								if ($(\'#categories-treeview\').find(\'input:checked\').length == 0)
									$(\'#error-treeview\').show();
								else
									$(\'#error-treeview\').hide();
								updElements(0, 0);
							},
							\'onComplete\': function() {
								lock_treeview_hidding = true;
								$(\'#categories-treeview\').parent().parent().show();
								if($($(\'#categories-treeview li\')[0]).attr(\'cleaned\'))
									return;
								if($($(\'#categories-treeview li\')[0]).attr(\'cleaned\', true))
								$($(\'#categories-treeview li\')[0]).removeClass(\'static\');
								$($(\'#categories-treeview li span\')[0]).trigger(\'click\');
								$($(\'#categories-treeview li\')[0]).children(\'div\').remove();
								$($(\'#categories-treeview li\')[0]).
									removeClass(\'collapsable lastCollapsable\').
									addClass(\'last static\');
								$(\'.hitarea\').live(\'click\', function(it)
								{
									$(this).parent().find(\'> .category_label\').click();
								});
							}
						});

						updHeight();
						updLayCounters(false);
						updPositions();
						updCatCounter();
						enableSortable();
					}
					
					function enableSortable()
					{
						$(function() {
							$(\'ul#selected_filters\').sortable({
								axis: \'y\',
								update: function() { updPositions(); },
								placeholder: \'ui-state-highlight\'

							});
							$(\'ul#selected_filters\').disableSelection();
						});
					}

					$(document).ready(function() {
						launch();
					});
				</script>
			</div>
			<div id="layered-step-3">
				<div id="error-filter-name" class="error">
					<img src="../img/admin/error.png" alt="" title="" />'.$this->l('Errors:').'
					<ul>
						<li>'.$this->l('Filter template name required (cannot be empty)').'</li>
					</ul>
				</div>
				<h2>'.$this->l('Step 3/3 - Name your template').'</h2>
				<p>'.$this->l('Template name:').' <input type="text" id="layered_tpl_name" onkeyup="if ($(this).val() != \'\')
				{ $(\'#error-filter-name\').hide(); } else { $(\'#error-filter-name\').show(); }" name="layered_tpl_name" maxlength="64" value="'.sprintf($this->l('My template %s'), date('Y-m-d')).'"
				style="width: 200px; font-size: 11px;" /> <span style="font-size: 10px; font-style: italic;">('.$this->l('only as a reminder').')</span></p>
				<hr size="1" noshade />
				<p class="alert">'.$this->l('No filters selected, the blocklayered will be disable for the categories seleted.').'</p>
				<br />
				<center><input type="submit" class="button" name="SubmitFilter" value="'.$this->l('Save this filter template').'" /></center>
			</div>
				<input type="hidden" name="id_layered_filter" id="id_layered_filter" value="0" />
				<input type="hidden" name="n_existing" id="n_existing" value="'.(int)count($filters_templates).'" />
			</form>
		</fieldset><br />
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" /> '.$this->l('Configuration').'</legend>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">			
				<table border="0" style="font-size: 11px; width: 100%; margin: 0 auto;" class="table">
					<tr>
						<th style="text-align: center;">'.$this->l('Option').'</th>
						<th style="text-align: center; width: 200px;">'.$this->l('Value').'</th>
					</tr>
					<tr>
						<td style="text-align: right;">'.$this->l('Hide filter values with no product is matching').'</td>
						<td style="text-align: center;">
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_hide_0_values" value="1" '.(Configuration::get('PS_LAYERED_HIDE_0_VALUES') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_hide_0_values" value="0" '.(!Configuration::get('PS_LAYERED_HIDE_0_VALUES') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">'.$this->l('Show the number of matching products').'</td>
						<td style="text-align: center;">
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_show_qties" value="1" '.(Configuration::get('PS_LAYERED_SHOW_QTIES') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_show_qties" value="0" '.(!Configuration::get('PS_LAYERED_SHOW_QTIES') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">'.$this->l('Show products from subcategories').'</td>
						<td style="text-align: center;">
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_full_tree" value="1" '.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_full_tree" value="0" '.(!Configuration::get('PS_LAYERED_FULL_TREE') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr style="text-align: center;">
						<td style="text-align: right;">'.$this->l('Category filter depth (0 for no limits, 1 by default)').'</td>
						<td>
							<input type="text" name="ps_layered_filter_category_depth" value="'.((Configuration::get('PS_LAYERED_FILTER_CATEGORY_DEPTH') !== false) ? Configuration::get('PS_LAYERED_FILTER_CATEGORY_DEPTH') : 1).'" />
						</td>
					</tr>
					<tr style="text-align: center;">
						<td style="text-align: right;">'.$this->l('Use tax to filter price').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_filter_price_usetax" value="1" '.(Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_filter_price_usetax" value="0" '.(!Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr style="text-align: center;">
						<td style="text-align: right;">'.$this->l('Allow indexing robots (google, yahoo, bing, ...) to use condition filter').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_filter_index_condition" value="1" '.(Configuration::get('PS_LAYERED_FILTER_INDEX_CDT') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_filter_index_condition" value="0" '.(!Configuration::get('PS_LAYERED_FILTER_INDEX_CDT') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr style="text-align: center;">
						<td style="text-align: right;">'.$this->l('Allow indexing robots (google, yahoo, bing, ...) to use availability filter').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_filter_index_availability" value="1" '.(Configuration::get('PS_LAYERED_FILTER_INDEX_QTY') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_filter_index_availability" value="0" '.(!Configuration::get('PS_LAYERED_FILTER_INDEX_QTY') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr style="text-align: center;">
						<td style="text-align: right;">'.$this->l('Allow indexing robots (google, yahoo, bing, ...) to use manufacturer filter').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_filter_index_manufacturer" value="1" '.(Configuration::get('PS_LAYERED_FILTER_INDEX_MNF') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_filter_index_manufacturer" value="0" '.(!Configuration::get('PS_LAYERED_FILTER_INDEX_MNF') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr style="text-align: center;">
						<td style="text-align: right;">'.$this->l('Allow indexing robots (google, yahoo, bing, ...) to use category filter').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_filter_index_category" value="1" '.(Configuration::get('PS_LAYERED_FILTER_INDEX_CAT') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_filter_index_category" value="0" '.(!Configuration::get('PS_LAYERED_FILTER_INDEX_CAT') ? 'checked="checked"' : '').' />
						</td>
					</tr>
				</table>
				<p style="text-align: center;"><input type="submit" class="button" name="submitLayeredSettings" value="'.$this->l('Save configuration').'" /></p>
			</form>
		</fieldset>';

		return $html;
	}

	private function getSelectedFilters()
	{
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;
		
		// Force attributes selection (by url '.../2-mycategory/color-blue' or by get parameter 'selected_filters')
		if (strpos($_SERVER['SCRIPT_FILENAME'], 'blocklayered-ajax.php') === false || Tools::getValue('selected_filters') !== false)
		{
			if (Tools::getValue('selected_filters'))
				$url = Tools::getValue('selected_filters');
			else
				$url = preg_replace('/\/(?:\w*)\/(?:[0-9]+[-\w]*)([^\?]*)\??.*/', '$1', Tools::safeOutput($_SERVER['REQUEST_URI'], true));
			
			$url_attributes = explode('/', ltrim($url, '/'));
			$selected_filters = array('category' => array());
			if (!empty($url_attributes))
			{
				foreach ($url_attributes as $url_attribute)
				{
					$url_parameters = explode('-', $url_attribute);
					$attribute_name  = array_shift($url_parameters);
					if ($attribute_name == 'page')
						$this->page = (int)$url_parameters[0];
					else if (in_array($attribute_name, array('price', 'weight')))
						$selected_filters[$attribute_name] = array($url_parameters[0], $url_parameters[1]);
					else
					{
						foreach ($url_parameters as $url_parameter)
						{
							$data = Db::getInstance()->getValue('SELECT data FROM `'._DB_PREFIX_.'layered_friendly_url` WHERE `url_key` = \''.md5('/'.$attribute_name.'-'.$url_parameter).'\'');
							if ($data)
								foreach (self::unSerialize($data) as $key_params => $params)
								{
									if (!isset($selected_filters[$key_params]))
										$selected_filters[$key_params] = array();
									foreach ($params as $key_param => $param)
									{
										if (!isset($selected_filters[$key_params][$key_param]))
											$selected_filters[$key_params][$key_param] = array();
										$selected_filters[$key_params][$key_param] = $param;
									}
								}
						}
					}
				}
				return $selected_filters;
			}
		}

		/* Analyze all the filters selected by the user and store them into a tab */
		$selected_filters = array('category' => array(), 'manufacturer' => array(), 'quantity' => array(), 'condition' => array());
		foreach ($_GET as $key => $value)
			if (substr($key, 0, 8) == 'layered_')
			{
				preg_match('/^(.*)_([0-9]+|new|used|refurbished|slider)$/', substr($key, 8, strlen($key) - 8), $res);
				if (isset($res[1]))
				{
					$tmp_tab = explode('_', $value);
					$value = $tmp_tab[0];
					$id_key = false;
					if (isset($tmp_tab[1]))
						$id_key = $tmp_tab[1];
					if ($res[1] == 'condition' && in_array($value, array('new', 'used', 'refurbished')))
						$selected_filters['condition'][] = $value;
					else if ($res[1] == 'quantity' && (!$value || $value == 1))
						$selected_filters['quantity'][] = $value;
					else if (in_array($res[1], array('category', 'manufacturer')))
					{
						if (!isset($selected_filters[$res[1].($id_key ? '_'.$id_key : '')]))
							$selected_filters[$res[1].($id_key ? '_'.$id_key : '')] = array();
						$selected_filters[$res[1].($id_key ? '_'.$id_key : '')][] = (int)$value;
					}
					else if (in_array($res[1], array('id_attribute_group', 'id_feature')))
					{
						if (!isset($selected_filters[$res[1]]))
							$selected_filters[$res[1]] = array();
						$selected_filters[$res[1]][(int)$value] = $id_key.'_'.(int)$value;
					}
					else if ($res[1] == 'weight')
						$selected_filters[$res[1]] = $tmp_tab;
					else if ($res[1] == 'price')
						$selected_filters[$res[1]] = $tmp_tab;
				}
			}
		return $selected_filters;
	}

	public function getProductByFilters($selected_filters = array())
	{
		global $cookie;

		if (!empty($this->products))
			return $this->products;

		/* If the current category isn't defined or if it's homepage, we have nothing to display */
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return false;

		$alias_where = 'p';
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$alias_where = 'product_shop';
		$query_filters_where = ' AND '.$alias_where.'.`active` = 1';
		$query_filters_from = '';
		
		$parent = new Category((int)$id_parent);
		if (!count($selected_filters['category']))
		{
			if (Configuration::get('PS_LAYERED_FULL_TREE'))
				$query_filters_from .= ' INNER JOIN '._DB_PREFIX_.'category_product cp
				ON p.id_product = cp.id_product
				INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND
				'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
				AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
				AND c.active = 1)';
			else
				$query_filters_from .= ' INNER JOIN '._DB_PREFIX_.'category_product cp
				ON p.id_product = cp.id_product
				INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category
				AND c.id_category = '.(int)$id_parent.'
				AND c.active = 1)';
		}

		foreach ($selected_filters as $key => $filter_values)
		{
			if (!count($filter_values))
				continue;

			preg_match('/^(.*[^_0-9])/', $key, $res);
			$key = $res[1];

			switch ($key)
			{
				case 'id_feature':
					$sub_queries = array();
					foreach ($filter_values as $filter_value)
					{
						$filter_value_array = explode('_', $filter_value);
						if (!isset($sub_queries[$filter_value_array[0]]))
							$sub_queries[$filter_value_array[0]] = array();
						$sub_queries[$filter_value_array[0]][] = 'fp.`id_feature_value` = '.(int)$filter_value_array[1];
					}
					foreach ($sub_queries as $sub_query)
					{
						$query_filters_where .= ' AND p.id_product IN (SELECT `id_product` FROM `'._DB_PREFIX_.'feature_product` fp WHERE ';
						$query_filters_where .= implode(' OR ', $sub_query).') ';
					}
				break;

				case 'id_attribute_group':
					$sub_queries = array();
					
					
					foreach ($filter_values as $filter_value)
					{
						$filter_value_array = explode('_', $filter_value);
						if (!isset($sub_queries[$filter_value_array[0]]))
							$sub_queries[$filter_value_array[0]] = array();
						$sub_queries[$filter_value_array[0]][] = 'pac.`id_attribute` = '.(int)$filter_value_array[1];
					}
					foreach ($sub_queries as $sub_query)
					{
						$query_filters_where .= ' AND p.id_product IN (SELECT pa.`id_product`
						FROM `'._DB_PREFIX_.'product_attribute_combination` pac
						LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
						ON (pa.`id_product_attribute` = pac.`id_product_attribute`)';
						if (version_compare(_PS_VERSION_,'1.5','>'))
							$query_filters_where .= Shop::addSqlAssociation('product_attribute', 'pa');
						$query_filters_where .= 'WHERE '.implode(' OR ', $sub_query).') ';
					}
				break;

				case 'category':
					$query_filters_where .= ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'category_product cp WHERE ';
					foreach ($selected_filters['category'] as $id_category)
						$query_filters_where .= 'cp.`id_category` = '.(int)$id_category.' OR ';
					$query_filters_where = rtrim($query_filters_where, 'OR ').')';
				break;

				case 'quantity':
					if (count($selected_filters['quantity']) == 2)
						break;
					if (version_compare(_PS_VERSION_,'1.5','>'))
					{
						$query_filters_where .= ' AND sa.quantity '.(!$selected_filters['quantity'][0] ? '<=' : '>').' 0 ';
						$query_filters_from .= 'LEFT JOIN `'._DB_PREFIX_.'stock_available` sa ON (sa.id_product = p.id_product AND sa.id_shop = '.(int)Context::getContext()->shop->id.') ';
					}
					else
						$query_filters_where .= ' AND p.quantity '.(!$selected_filters['quantity'][0] ? '<=' : '>').' 0 ';
				break;

				case 'manufacturer':
					$query_filters_where .= ' AND p.id_manufacturer IN ('.implode($selected_filters['manufacturer'], ',').')';
				break;

				case 'condition':
					if (count($selected_filters['condition']) == 3)
						break;
					$query_filters_where .= ' AND '.$alias_where.'.condition IN (';
					foreach ($selected_filters['condition'] as $cond)
						$query_filters_where .= '\''.$cond.'\',';
					$query_filters_where = rtrim($query_filters_where, ',').')';
				break;

				case 'weight':
					if ($selected_filters['weight'][0] != 0 || $selected_filters['weight'][1] != 0)
						$query_filters_where .= ' AND p.`weight` BETWEEN '.(float)($selected_filters['weight'][0] - 0.001).' AND '.(float)($selected_filters['weight'][1] + 0.001);

				case 'price':
					if (isset($selected_filters['price']))
					{
						if ($selected_filters['price'][0] !== '' || $selected_filters['price'][1] !== '')
						{
							$price_filter = array();
							$price_filter['min'] = (float)($selected_filters['price'][0]);
							$price_filter['max'] = (float)($selected_filters['price'][1]);
						}
					}
					else
						$price_filter = false;
				break;
			}
		}
		
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$id_currency = (int)Context::getContext()->currency->id;
		else
			$id_currency = (int)Currency::getCurrent()->id;
		
		$price_filter_query_in = ''; // All products with price range between price filters limits
		$price_filter_query_out = ''; // All products with a price filters limit on it price range
		if (isset($price_filter) && $price_filter)
		{
			$price_filter_query_in = 'INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi
			ON
			(
				psi.price_min >= '.(int)$price_filter['min'].'
				AND psi.price_max <= '.(int)$price_filter['max'].'
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_currency` = '.$id_currency.'
			)';
			
			$price_filter_query_out = 'INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi
			ON 
				((psi.price_min < '.(int)$price_filter['min'].' AND psi.price_max > '.(int)$price_filter['min'].')
				OR
				(psi.price_max > '.(int)$price_filter['max'].' AND psi.price_min < '.(int)$price_filter['max'].'))
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_currency` = '.$id_currency;
		}
		
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$query_filters_from .= Shop::addSqlAssociation('product', 'p');
		
		$all_products_out = self::query('
		SELECT p.`id_product` id_product
		FROM `'._DB_PREFIX_.'product` p
		'.$price_filter_query_out.'
		'.$query_filters_from.'
		WHERE 1 '.$query_filters_where.' GROUP BY id_product');
		
		$all_products_in = self::query('
		SELECT p.`id_product` id_product
		FROM `'._DB_PREFIX_.'product` p
		'.$price_filter_query_in.'
		'.$query_filters_from.'
		WHERE 1 '.$query_filters_where.' GROUP BY id_product');

		$product_id_list = array();
		
		while ($product = DB::getInstance()->nextRow($all_products_in))
			$product_id_list[] = (int)$product['id_product'];

		while ($product = DB::getInstance()->nextRow($all_products_out))
			if (isset($price_filter) && $price_filter)
			{
				$price = (int)Product::getPriceStatic($product['id_product'], Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX')); // Cast to int because we don't care about cents
				if ($price < $price_filter['min'] || $price > $price_filter['max'])
					continue;
				$product_id_list[] = (int)$product['id_product'];
			}
		$this->nbr_products = count($product_id_list);
		
		if ($this->nbr_products == 0)
			$this->products = array();
		else
		{
			$n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
			$join = '';
			if (version_compare(_PS_VERSION_,'1.5','>'))
				$join = Shop::addSqlAssociation('product', 'p');
			$this->products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT
				p.*,
				'.($alias_where == 'p' ? '' : 'product_shop.*,' ).'
				'.$alias_where.'.id_category_default,
				pl.*,
				i.id_image,
				il.legend, 
				m.name manufacturer_name,
				DATEDIFF('.$alias_where.'.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new
			FROM `'._DB_PREFIX_.'category_product` cp
			LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
			LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
			'.$join.'
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl').')
			LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)
			LEFT JOIN '._DB_PREFIX_.'image_lang il ON (i.id_image = il.id_image AND il.id_lang = '.(int)($cookie->id_lang).')
			LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
			WHERE '.$alias_where.'.`active` = 1 AND
			'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
			AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
			AND c.active = 1
			AND pl.id_lang = '.(int)$cookie->id_lang.'
			AND p.id_product IN ('.implode(',', $product_id_list).')'
			.' GROUP BY p.id_product ORDER BY '.Tools::getProductsOrder('by', Tools::getValue('orderby'), true).' '.Tools::getProductsOrder('way', Tools::getValue('orderway')).
			' LIMIT '.(((int)$this->page - 1) * $n.','.$n));
		}
		
		if (Tools::getProductsOrder('by', Tools::getValue('orderby'), true) == 'p.price')
			Tools::orderbyPrice($this->products, Tools::getProductsOrder('way', Tools::getValue('orderway')));
			
		return $this->products;
	}
	
	private static function query($sql_query)
	{
		if (version_compare(_PS_VERSION_,'1.5','>'))
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->query($sql_query);
		else
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_query, false);
	}
	
	public function getFilterBlock($selected_filters = array())
	{
		global $cookie;
		static $cache = null;
		
		if (version_compare(_PS_VERSION_,'1.5','>'))
		{
			$id_lang = Context::getContext()->language->id;
			$currency = Context::getContext()->currency;
			$id_shop = (int) Context::getContext()->shop->id;
			$alias = 'product_shop';
		}
		else
		{
			$id_lang = (int)$cookie->id_lang;
			$currency = Currency::getCurrent();
			$id_shop = 0;
			$alias = 'p';
		}
		
		if (is_array($cache))
			return $cache;
			
		
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;
		
		$parent = new Category((int)$id_parent, $id_lang);
		
		/* Get the filters for the current category */
		$filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM '._DB_PREFIX_.'layered_category
			WHERE id_category = '.(int)$id_parent.'
				AND id_shop = '.$id_shop.'
			GROUP BY `type`, id_value ORDER BY position ASC');
		// Remove all empty selected filters
		foreach ($selected_filters as $key => $value)
			switch ($key)
			{
				case 'price':
				case 'weight':
					if ($value[0] === '' && $value[1] === '')
						unset($selected_filters[$key]);
					break;
				default:
					if ($value == '')
						unset($selected_filters[$key]);
					break;
			}
		
		$filter_blocks = array();
		foreach ($filters as $filter)
		{
			$sql_query = array('select' => '', 'from' => '', 'join' => '', 'where' => '', 'group' => '', 'second_query' => '');
			switch ($filter['type'])
			{
				// conditions + quantities + weight + price
				case 'price':
				case 'weight':
				case 'condition':
				case 'quantity':
					
					if (version_compare(_PS_VERSION_,'1.5','>'))
						$sql_query['select'] = 'SELECT p.`id_product`, product_shop.`condition`, p.`id_manufacturer`, sa.`quantity`, p.`weight` ';
					else
						$sql_query['select'] = 'SELECT p.`id_product`, p.`condition`, p.`id_manufacturer`, p.`quantity`, p.`weight` ';
					$sql_query['from'] = '
					FROM '._DB_PREFIX_.'product p ';
					$sql_query['join'] = '
					INNER JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
					INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND
					'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
					AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
					AND c.active = 1) ';
					if (version_compare(_PS_VERSION_,'1.5','>'))
					{
						$sql_query['join'] .= 'LEFT JOIN `'._DB_PREFIX_.'stock_available` sa
							ON (sa.id_product = p.id_product AND sa.id_shop = '.(int)$this->context->shop->id.') ';
						$sql_query['where'] = 'WHERE product_shop.`active` = 1 ';
					}
					else
						$sql_query['where'] = 'WHERE p.`active` = 1 ';
					$sql_query['group'] = ' GROUP BY p.id_product ';
					break;

				case 'manufacturer':
					$sql_query['select'] = 'SELECT m.name, COUNT(DISTINCT p.id_product) nbr, m.id_manufacturer ';
					$sql_query['from'] = '
					FROM `'._DB_PREFIX_.'category_product` cp
					INNER JOIN  `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category)
					INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product)
					INNER JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer) ';
					$sql_query['where'] = 'WHERE 
					'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
					AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
					AND c.active = 1
					AND '.$alias.'.active = 1';
					$sql_query['group'] = ' GROUP BY p.id_manufacturer ';
					
					if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
					{
						$sql_query['second_query'] = '
							SELECT m.name, 0 nbr, m.id_manufacturer 
							
							FROM `'._DB_PREFIX_.'category_product` cp
							'.(version_compare(_PS_VERSION_,'1.5','>') ? Shop::addSqlAssociation('product', 'cp') : '').'
							INNER JOIN  `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category)
							INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product)
							INNER JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer) 
							
							WHERE '.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
							AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
							AND c.active = 1
							AND '.$alias.'.active = 1
							GROUP BY p.id_manufacturer';
					}
					
					break;
				case 'id_attribute_group':// attribute group
					$sql_query['select'] = '
					SELECT COUNT(DISTINCT p.id_product) nbr, lpa.id_attribute_group,
					a.color, al.name attribute_name, agl.public_name attribute_group_name , lpa.id_attribute, ag.is_color_group,
					liagl.url_name name_url_name, liagl.meta_title name_meta_title, lial.url_name value_url_name, lial.meta_title value_meta_title';
					$sql_query['from'] = '
					FROM '._DB_PREFIX_.'layered_product_attribute lpa
					INNER JOIN '._DB_PREFIX_.'attribute a
					ON a.id_attribute = lpa.id_attribute
					INNER JOIN '._DB_PREFIX_.'attribute_lang al
					ON al.id_attribute = a.id_attribute
					AND al.id_lang = '.$id_lang.'
					INNER JOIN '._DB_PREFIX_.'product as p
					ON p.id_product = lpa.id_product
					INNER JOIN '._DB_PREFIX_.'attribute_group ag
					ON ag.id_attribute_group = lpa.id_attribute_group
					INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl
					ON agl.id_attribute_group = lpa.id_attribute_group
					AND agl.id_lang = '.$id_lang.'
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value liagl
					ON (liagl.id_attribute_group = lpa.id_attribute_group AND liagl.id_lang = '.$id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_lang_value lial
					ON (lial.id_attribute = lpa.id_attribute AND lial.id_lang = '.$id_lang.') ';
					$sql_query['where'] = 'WHERE a.id_attribute_group = '.(int)$filter['id_value'];
					if (version_compare(_PS_VERSION_,'1.5','>'))
						$sql_query['where'] .= ' AND lpa.`id_shop` = '.(int)Context::getContext()->shop->id;
					$sql_query['where'] .= ' AND '.$alias.'.active = 1 AND p.id_product IN (
					SELECT id_product
					FROM '._DB_PREFIX_.'category_product cp
					INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND 
					'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
					AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
					AND c.active = 1)) ';
					$sql_query['group'] = '
					GROUP BY lpa.id_attribute
					ORDER BY id_attribute_group, id_attribute ';
					
					if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
					{
						$sql_query['second_query'] = '
							SELECT 0 nbr, lpa.id_attribute_group,
							a.color, al.name attribute_name, agl.public_name attribute_group_name , lpa.id_attribute, ag.is_color_group,
							liagl.url_name name_url_name, liagl.meta_title name_meta_title, lial.url_name value_url_name, lial.meta_title value_meta_title
							
							FROM '._DB_PREFIX_.'layered_product_attribute lpa
							'.(version_compare(_PS_VERSION_,'1.5','>') ? Shop::addSqlAssociation('product', 'lpa') : '').'
							INNER JOIN '._DB_PREFIX_.'attribute a
							ON a.id_attribute = lpa.id_attribute
							INNER JOIN '._DB_PREFIX_.'attribute_lang al
							ON al.id_attribute = a.id_attribute
							AND al.id_lang = '.$id_lang.'
							INNER JOIN '._DB_PREFIX_.'product as p
							ON p.id_product = lpa.id_product
							INNER JOIN '._DB_PREFIX_.'attribute_group ag
							ON ag.id_attribute_group = lpa.id_attribute_group
							INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl
							ON agl.id_attribute_group = lpa.id_attribute_group
							AND agl.id_lang = '.$id_lang.'
							LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value liagl
							ON (liagl.id_attribute_group = lpa.id_attribute_group AND liagl.id_lang = '.$id_lang.')
							LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_lang_value lial
							ON (lial.id_attribute = lpa.id_attribute AND lial.id_lang = '.$id_lang.')
							
							WHERE '.$alias.'.active = 1 AND a.id_attribute_group = '.(int)$filter['id_value'].'
							'.(version_compare(_PS_VERSION_,'1.5','>') ? 'AND lpa.`id_shop` = '.(int)Context::getContext()->shop->id : '').'
							
							GROUP BY lpa.id_attribute
							ORDER BY id_attribute_group, id_attribute';
					}
					break;

				case 'id_feature':
					$sql_query['select'] = 'SELECT fl.name feature_name, fp.id_feature, fv.id_feature_value, fvl.value,
					COUNT(DISTINCT p.id_product) nbr,
					lifl.url_name name_url_name, lifl.meta_title name_meta_title, lifvl.url_name value_url_name, lifvl.meta_title value_meta_title ';
					$sql_query['from'] = '
					FROM '._DB_PREFIX_.'feature_product fp
					INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product)
					LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = fp.id_feature AND fl.id_lang = '.$id_lang.')
					INNER JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
					LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = '.$id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_lang_value lifl
					ON (lifl.id_feature = fp.id_feature AND lifl.id_lang = '.$id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_value_lang_value lifvl
					ON (lifvl.id_feature_value = fp.id_feature_value AND lifvl.id_lang = '.$id_lang.') ';
					$sql_query['where'] = 'WHERE '.$alias.'.`active` = 1 AND fp.id_feature = '.(int)$filter['id_value'].'
					AND p.id_product IN (
					SELECT id_product
					FROM '._DB_PREFIX_.'category_product cp
					INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND
					'.(Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= '.(int)$parent->nleft.'
					AND c.nright <= '.(int)$parent->nright : 'c.id_category = '.(int)$id_parent).'
					AND c.active = 1)) ';
					$sql_query['group'] = 'GROUP BY fv.id_feature_value ';
					
					if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
					{
						$sql_query['second_query'] = '
							SELECT fl.name feature_name, fp.id_feature, fv.id_feature_value, fvl.value,
							0 nbr,
							lifl.url_name name_url_name, lifl.meta_title name_meta_title, lifvl.url_name value_url_name, lifvl.meta_title value_meta_title
					
							FROM '._DB_PREFIX_.'feature_product fp
							'.(version_compare(_PS_VERSION_,'1.5','>') ? Shop::addSqlAssociation('product', 'fp') : '').'
							INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product)
							LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = fp.id_feature AND fl.id_lang = '.$id_lang.')
							INNER JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
							LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = '.$id_lang.')
							LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_lang_value lifl
							ON (lifl.id_feature = fp.id_feature AND lifl.id_lang = '.$id_lang.')
							LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_value_lang_value lifvl
							ON (lifvl.id_feature_value = fp.id_feature_value AND lifvl.id_lang = '.$id_lang.')
							WHERE '.$alias.'.`active` = 1 AND fp.id_feature = '.(int)$filter['id_value'].'
							GROUP BY fv.id_feature_value';
					}
					
					break;

				case 'category':
					$depth = Configuration::get('PS_LAYERED_FILTER_CATEGORY_DEPTH');
					if ($depth === false)
						$depth = 1;
					
					$sql_query['select'] = '
					SELECT c.id_category, c.id_parent, cl.name, (SELECT count(DISTINCT p.id_product) # ';
					$sql_query['from'] = '
					FROM '._DB_PREFIX_.'category_product cp
					LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product) ';
					$sql_query['where'] = '
					WHERE cp.id_category = c.id_category AND '.$alias.'.active = 1 ';
					$sql_query['group'] = ') count_products
					FROM '._DB_PREFIX_.'category c
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category AND cl.id_lang = '.$id_lang.')
					WHERE c.nleft > '.(int)$parent->nleft.'
					AND c.nright < '.(int)$parent->nright.'
					'.($depth ? 'AND c.level_depth <= '.($parent->level_depth+(int)$depth) : '').'
					GROUP BY c.id_category ORDER BY c.nleft, c.position';
			}
			
			foreach ($filters as $filter_tmp)
			{
				$method_name = 'get'.ucfirst($filter_tmp['type']).'FilterSubQuery';
				if (method_exists('BlockLayered', $method_name) &&
				(!in_array($filter['type'], array('price', 'weight')) && $filter['type'] != $filter_tmp['type'] || $filter['type'] == $filter_tmp['type']))
				{
					if ($filter['type'] == $filter_tmp['type'] && $filter['id_value'] == $filter_tmp['id_value'])
						$sub_query_filter = self::$method_name(array(), true);
					else
					{
						if (!is_null($filter_tmp['id_value']))
							$selected_filters_cleaned = $this->cleanFilterByIdValue(@$selected_filters[$filter_tmp['type']], $filter_tmp['id_value']);
						else
							$selected_filters_cleaned = @$selected_filters[$filter_tmp['type']];
						$sub_query_filter = self::$method_name($selected_filters_cleaned, $filter['type'] == $filter_tmp['type']);
					}
					foreach ($sub_query_filter as $key => $value)
						$sql_query[$key] .= $value;
				}
			}
			
			$products = false;
			if (!empty($sql_query['from']))
			{
				if (version_compare(_PS_VERSION_,'1.5','>'))
					$sql_query['from'] .= Shop::addSqlAssociation('product', 'p');
				$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_query['select']."\n".$sql_query['from']."\n".$sql_query['join']."\n".$sql_query['where']."\n".$sql_query['group']);
			}

			foreach ($filters as $filter_tmp)
			{
				$method_name = 'filterProductsBy'.ucfirst($filter_tmp['type']);
				if (method_exists('BlockLayered', $method_name) &&
				(!in_array($filter['type'], array('price', 'weight')) && $filter['type'] != $filter_tmp['type'] || $filter['type'] == $filter_tmp['type']))
					if ($filter['type'] == $filter_tmp['type'])
						$products = self::$method_name(array(), $products);
					else
						$products = self::$method_name(@$selected_filters[$filter_tmp['type']], $products);
			}
			
			if (!empty($sql_query['second_query']))
			{
				$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql_query['second_query']);
				if ($res)
					$products = array_merge($products, $res);
			}

			switch ($filter['type'])
			{
				case 'price':
					$price_array = array(
						'type_lite' => 'price',
						'type' => 'price',
						'id_key' => 0,
						'name' => $this->l('Price'),
						'slider' => true,
						'max' => '0',
						'min' => null,
						'values' => array ('1' => 0),
						'unit' => $currency->sign,
						'format' => $currency->format,
						'filter_show_limit' => $filter['filter_show_limit'],
						'filter_type' => $filter['filter_type']
					);
					if (isset($products) && $products)
						foreach ($products as $product)
						{
							if (is_null($price_array['min']))
							{
								$price_array['min'] = $product['price_min'];
								$price_array['values'][0] = $product['price_min'];
							}
							else if ($price_array['min'] > $product['price_min'])
							{
								$price_array['min'] = $product['price_min'];
								$price_array['values'][0] = $product['price_min'];
							}
	
							if ($price_array['max'] < $product['price_max'])
							{
								$price_array['max'] = $product['price_max'];
								$price_array['values'][1] = $product['price_max'];
							}
						}
						
					if ($price_array['max'] != $price_array['min'] && $price_array['min'] != null)
					{
						if ($filter['filter_type'] == 2)
						{
							$price_array['list_of_values'] = array();
							$nbr_of_value = $filter['filter_show_limit'];
							if ($nbr_of_value < 2)
								$nbr_of_value = 4;
							$delta = ($price_array['max'] - $price_array['min']) / $nbr_of_value;
							$current_step = $price_array['min'];
							for ($i = 0; $i < $nbr_of_value; $i++)
								$price_array['list_of_values'][] = array(
									(int)($price_array['min'] + $i * $delta),
									(int)($price_array['min'] + ($i + 1) * $delta)
								);
						}
						if (isset($selected_filters['price']) && isset($selected_filters['price'][0])
						&& isset($selected_filters['price'][1]))
						{
							$price_array['values'][0] = $selected_filters['price'][0];
							$price_array['values'][1] = $selected_filters['price'][1];
						}
						$filter_blocks[] = $price_array;
					}
					break;

				case 'weight':
					$weight_array = array(
						'type_lite' => 'weight',
						'type' => 'weight',
						'id_key' => 0,
						'name' => $this->l('Weight'),
						'slider' => true,
						'max' => '0',
						'min' => null,
						'values' => array ('1' => 0),
						'unit' => Configuration::get('PS_WEIGHT_UNIT'),
						'format' => 5, // Ex: xxxxx kg
						'filter_show_limit' => $filter['filter_show_limit'],
						'filter_type' => $filter['filter_type']
					);
					if (isset($products) && $products)
						foreach ($products as $product)
						{
							if (is_null($weight_array['min']))
							{
								$weight_array['min'] = $product['weight'];
								$weight_array['values'][0] = $product['weight'];
							}
							else if ($weight_array['min'] > $product['weight'])
							{
								$weight_array['min'] = $product['weight'];
								$weight_array['values'][0] = $product['weight'];
							}
							
							if ($weight_array['max'] < $product['weight'])
							{
								$weight_array['max'] = $product['weight'];
								$weight_array['values'][1] = $product['weight'];
							}
						}
					if ($weight_array['max'] != $weight_array['min'] && $weight_array['min'] != null)
					{
						if (isset($selected_filters['weight']) && isset($selected_filters['weight'][0])
						&& isset($selected_filters['weight'][1]))
						{
							$weight_array['values'][0] = $selected_filters['weight'][0];
							$weight_array['values'][1] = $selected_filters['weight'][1];
						}
						$filter_blocks[] = $weight_array;
					}
					break;

				case 'condition':
					$condition_array = array(
						'new' => array('name' => $this->l('New'),'nbr' => 0), 
						'used' => array('name' => $this->l('Used'), 'nbr' => 0),
						'refurbished' => array('name' => $this->l('Refurbished'),
						'nbr' => 0)
					);
					if (isset($products) && $products)
						foreach ($products as $product)
							if (isset($selected_filters['condition']) && in_array($product['condition'], $selected_filters['condition']))
								$condition_array[$product['condition']]['checked'] = true;
					foreach ($condition_array as $key => $condition)
						if (isset($selected_filters['condition']) && in_array($key, $selected_filters['condition']))
							$condition_array[$key]['checked'] = true;
					if (isset($products) && $products)
						foreach ($products as $product)
							if (isset($condition_array[$product['condition']]))
								$condition_array[$product['condition']]['nbr']++;
					$filter_blocks[] = array(
						'type_lite' => 'condition',
						'type' => 'condition',
						'id_key' => 0,
						'name' => $this->l('Condition'),
						'values' => $condition_array,
						'filter_show_limit' => $filter['filter_show_limit'],
						'filter_type' => $filter['filter_type']
					);
					break;
				
				case 'quantity':
					$quantity_array = array (
						0 => array('name' => $this->l('Not available'), 'nbr' => 0),
						1 => array('name' => $this->l('In stock'),
						'nbr' => 0));
					foreach ($quantity_array as $key => $quantity)
						if (isset($selected_filters['quantity']) && in_array($key, $selected_filters['quantity']))
							$quantity_array[$key]['checked'] = true;
					if (isset($products) && $products)
						foreach ($products as $product)
							$quantity_array[(int)($product['quantity'] > 0)]['nbr']++;
					$filter_blocks[] = array(
						'type_lite' => 'quantity',
						'type' => 'quantity',
						'id_key' => 0,
						'name' => $this->l('Availability'),
						'values' => $quantity_array,
						'filter_show_limit' => $filter['filter_show_limit'],
						'filter_type' => $filter['filter_type']
					);
					break;

				case 'manufacturer':
					if (isset($products) && $products)
					{
						$manufaturers_array = array();
							foreach ($products as $manufacturer)
							{
								if (!isset($manufaturers_array[$manufacturer['id_manufacturer']]))
									$manufaturers_array[$manufacturer['id_manufacturer']] = array('name' => $manufacturer['name'], 'nbr' => $manufacturer['nbr']);
								if (isset($selected_filters['manufacturer']) && in_array((int)$manufacturer['id_manufacturer'], $selected_filters['manufacturer']))
									$manufaturers_array[$manufacturer['id_manufacturer']]['checked'] = true;
							}
						$filter_blocks[] = array(
							'type_lite' => 'manufacturer',
							'type' => 'manufacturer',
							'id_key' => 0,
							'name' => $this->l('Manufacturer'),
							'values' => $manufaturers_array,
							'filter_show_limit' => $filter['filter_show_limit'],
							'filter_type' => $filter['filter_type']
						);
					}
					break;

				case 'id_attribute_group':
					$attributes_array = array();
					if (isset($products) && $products)
					{
						foreach ($products as $attributes)
						{
							if (!isset($attributes_array[$attributes['id_attribute_group']]))
								$attributes_array[$attributes['id_attribute_group']] = array (
									'type_lite' => 'id_attribute_group',
									'type' => 'id_attribute_group',
									'id_key' => (int)$attributes['id_attribute_group'],
									'name' =>  $attributes['attribute_group_name'],
									'is_color_group' => (bool)$attributes['is_color_group'],
									'values' => array(),
									'url_name' => $attributes['name_url_name'],
									'meta_title' => $attributes['name_meta_title'],
									'filter_show_limit' => $filter['filter_show_limit'],
									'filter_type' => $filter['filter_type']
								);
							
							if (!isset($attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']]))
								$attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']] = array(
									'color' => $attributes['color'],
									'name' => $attributes['attribute_name'],
									'nbr' => (int)$attributes['nbr'],
									'url_name' => $attributes['value_url_name'],
									'meta_title' => $attributes['value_meta_title']
								);
								
							if (isset($selected_filters['id_attribute_group'][$attributes['id_attribute']]))
								$attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']]['checked'] = true;
						}
						$filter_blocks = array_merge($filter_blocks, $attributes_array);
					}
					break;
				case 'id_feature':
					$feature_array = array();
					if (isset($products) && $products)
					{
						foreach ($products as $feature)
						{
							if (!isset($feature_array[$feature['id_feature']]))
								$feature_array[$feature['id_feature']] = array(
									'type_lite' => 'id_feature',
									'type' => 'id_feature',
									'id_key' => (int)$feature['id_feature'],
									'values' => array(),
									'name' => $feature['feature_name'],
									'url_name' => $feature['name_url_name'],
									'meta_title' => $feature['name_meta_title'],
									'filter_show_limit' => $filter['filter_show_limit'],
									'filter_type' => $filter['filter_type']
								);

							if (!isset($feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']]))
								$feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']] = array(
									'nbr' => (int)$feature['nbr'],
									'name' => $feature['value'],
									'url_name' => $feature['value_url_name'],
									'meta_title' => $feature['value_meta_title']
								);
							
							if (isset($selected_filters['id_feature'][$feature['id_feature_value']]))
								$feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']]['checked'] = true;
						}
						$filter_blocks = array_merge($filter_blocks, $feature_array);
					}
					break;

				case 'category':
					$tmp_array = array();
					if (isset($products) && $products)
					{
						$categories_with_products_count = 0;
						foreach ($products as $category)
						{
							$tmp_array[$category['id_category']] = array(
								'name' => $category['name'],
								'nbr' => (int)$category['count_products']
							);
							
							if ((int)$category['count_products'])
								$categories_with_products_count++;
							
							if (isset($selected_filters['category']) && in_array($category['id_category'], $selected_filters['category']))
								$tmp_array[$category['id_category']]['checked'] = true;
						}
						if ($categories_with_products_count || !Configuration::get('PS_LAYERED_HIDE_0_VALUES'))
							$filter_blocks[] = array (
								'type_lite' => 'category',
								'type' => 'category',
								'id_key' => 0, 'name' => $this->l('Categories'),
								'values' => $tmp_array,
								'filter_show_limit' => $filter['filter_show_limit'],
								'filter_type' => $filter['filter_type']
							);
					}
					break;
				
			}
		}
		
		// All non indexable attribute and feature
		$non_indexable = array();
		
		// Get all non indexable attribute groups
		foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT public_name
		FROM `'._DB_PREFIX_.'attribute_group_lang` agl
		LEFT JOIN `'._DB_PREFIX_.'layered_indexable_attribute_group` liag
		ON liag.id_attribute_group = agl.id_attribute_group
		WHERE indexable IS NULL OR indexable = 0
		AND id_lang = '.$id_lang) as $attribute)
			$non_indexable[] = Tools::link_rewrite($attribute['public_name']);
		
		// Get all non indexable features
		foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT name
		FROM `'._DB_PREFIX_.'feature_lang` fl
		LEFT JOIN  `'._DB_PREFIX_.'layered_indexable_feature` lif
		ON lif.id_feature = fl.id_feature
		WHERE indexable IS NULL OR indexable = 0
		AND id_lang = '.$id_lang) as $attribute)
			$non_indexable[] = Tools::link_rewrite($attribute['name']);

		//generate SEO link
		$param_selected = '';
		$param_product_url = '';
		$option_checked_array = array();
		$param_group_selected_array = array();
		$title_values = array();
		$meta_values = array();

		//get filters checked by group
		foreach ($filter_blocks as $type_filter)
		{
			$filter_name = (!empty($type_filter['url_name']) ? $type_filter['url_name'] : $type_filter['name']);
			$filter_meta = (!empty($type_filter['meta_title']) ? $type_filter['meta_title'] : '');
			$attr_key = $type_filter['type'].'_'.$type_filter['id_key'];
			
			$param_group_selected = '';
			foreach ($type_filter['values'] as $key => $value)
			{
				if (is_array($value) && array_key_exists('checked', $value ))
				{
					$value_name = !empty($value['url_name']) ? $value['url_name'] : $value['name'];
					$value_meta = !empty($value['meta_title']) ? $value['meta_title'] : $value['name'];
					$param_group_selected .= '-'.str_replace('-', '_', Tools::link_rewrite($value_name));
					$param_group_selected_array[Tools::link_rewrite($filter_name)][] = Tools::link_rewrite($value_name);
				
					if (!isset($title_values[$filter_name]))
						$title_values[$filter_name] = array();
					$title_values[$filter_name][] = $value_name;
					if (!isset($meta_values[$attr_key]))
						$meta_values[$attr_key] = array('title' => $filter_meta, 'values' => array());
					$meta_values[$attr_key]['values'][] = $value_meta;
				}
				else
					$param_group_selected_array[Tools::link_rewrite($filter_name)][] = array();
			}
			if (!empty($param_group_selected))
			{
				$param_selected .= '/'.str_replace('-', '_', Tools::link_rewrite($filter_name)).$param_group_selected;
				$option_checked_array[Tools::link_rewrite($filter_name)] = $param_group_selected;
			}
			// select only attribute and group attribute to display an unique product combination link
			if (!empty($param_group_selected) && $type_filter['type'] == 'id_attribute_group')
				$param_product_url .= '/'.str_replace('-', '_', Tools::link_rewrite($filter_name)).$param_group_selected;
			
		}
		
		if ($this->page > 1)
			$param_selected .= '/page-'.$this->page;

		$blacklist = array('weight', 'price');
		
		if (!Configuration::get('PS_LAYERED_FILTER_INDEX_CDT'))
			$blacklist[] = 'condition';
		if (!Configuration::get('PS_LAYERED_FILTER_INDEX_QTY'))
			$blacklist[] = 'quantity';
		if (!Configuration::get('PS_LAYERED_FILTER_INDEX_MNF'))
			$blacklist[] = 'manufacturer';
		if (!Configuration::get('PS_LAYERED_FILTER_INDEX_CAT'))
			$blacklist[] = 'category';
		
		$global_nofollow = false;
		
		foreach ($filter_blocks as &$type_filter)
		{
			$filter_name = (!empty($type_filter['url_name']) ? $type_filter['url_name'] : $type_filter['name']);
			
			if (count($type_filter) > 0 && !isset($type_filter['slider']))
			{
				foreach ($type_filter['values'] as $key => $values)
				{
					$nofollow = false;
					if (!empty($values['checked']) && in_array($type_filter['type'], $blacklist))
						$global_nofollow = true;
					$option_checked_clone_array = $option_checked_array;
					
					// If not filters checked, add parameter
					$value_name = !empty($values['url_name']) ? $values['url_name'] : $values['name'];
					if (!in_array(Tools::link_rewrite($value_name), $param_group_selected_array[Tools::link_rewrite($filter_name)]))
					{
						// Update parameter filter checked before
						if (array_key_exists(Tools::link_rewrite($filter_name), $option_checked_array))
						{
							$option_checked_clone_array[Tools::link_rewrite($filter_name)] = $option_checked_clone_array[Tools::link_rewrite($filter_name)].'-'.str_replace('-', '_', Tools::link_rewrite($value_name));
							$nofollow = true;
						}
						else
							$option_checked_clone_array[Tools::link_rewrite($filter_name)] = '-'.str_replace('-', '_', Tools::link_rewrite($value_name));
					}
					else
					{
						// Remove selected parameters
						$option_checked_clone_array[Tools::link_rewrite($filter_name)] = str_replace('-'.str_replace('-', '_', Tools::link_rewrite($value_name)), '', $option_checked_clone_array[Tools::link_rewrite($filter_name)]);
						if (empty($option_checked_clone_array[Tools::link_rewrite($filter_name)]))
							unset($option_checked_clone_array[Tools::link_rewrite($filter_name)]);
					}
					$parameters = '';
					ksort($option_checked_clone_array); // Order parameters
					foreach ($option_checked_clone_array as $key_group => $value_group)
						$parameters .= '/'.str_replace('-', '_', $key_group).$value_group;
					
					// Check if there is an non indexable attribute or feature in the url
					foreach ($non_indexable as $value)
						if (strpos($parameters, '/'.$value) !== false)
							$nofollow = true;
					
					if (version_compare(_PS_VERSION_,'1.5','>'))
						$type_filter['values'][$key]['link'] = Context::getContext()->link->getCategoryLink($parent, null, null, ltrim($parameters, '/'));
					else
					{
						$link = new Link();
						$link_base = $link->getCategoryLink($id_parent, Category::getLinkRewrite($id_parent, $id_lang), $id_lang);
						// Write link by mode rewriting
						if (!Configuration::get('PS_REWRITING_SETTINGS'))
							$type_filter['values'][$key]['link'] = $link_base.'&selected_filters='.$parameters;
						else
							$type_filter['values'][$key]['link'] = $link_base.$parameters;
					}
						
					$type_filter['values'][$key]['rel'] = ($nofollow) ? 'nofollow' : '';
				}
			}
		}
		
		$n_filters = 0;
		if (isset($selected_filters['price']))
			if ($price_array['min'] == $selected_filters['price'][0] && $price_array['max'] == $selected_filters['price'][1])
				unset($selected_filters['price']);
		if (isset($selected_filters['weight']))
			if ($weight_array['min'] == $selected_filters['weight'][0] && $weight_array['max'] == $selected_filters['weight'][1])
				unset($selected_filters['weight']);
				
		foreach ($selected_filters as $filters)
			$n_filters += count($filters);
		
		$cache = array(
			'layered_show_qties' => (int)Configuration::get('PS_LAYERED_SHOW_QTIES'),
			'id_category_layered' => (int)$id_parent,
			'selected_filters' => $selected_filters,
			'n_filters' => (int)$n_filters,
			'nbr_filterBlocks' => count($filter_blocks),
			'filters' => $filter_blocks,
			'title_values' => $title_values,
			'meta_values' => $meta_values,
			'current_friendly_url' => $param_selected,
			'param_product_url' => $param_product_url,
			'no_follow' => (!empty($param_selected) || $global_nofollow)
		);
		
		return $cache;
	}
	
	public function cleanFilterByIdValue($attributes, $id_value)
	{
		$selected_filters = array();
		if (is_array($attributes))
			foreach ($attributes as $attribute)
			{
				$attribute_data = explode('_', $attribute);
				if ($attribute_data[0] == $id_value)
					$selected_filters[] = $attribute_data[1];
			}
		return $selected_filters;
	}
	
	public function generateFiltersBlock($selected_filters)
	{
		global $smarty;
		if ($filter_block = $this->getFilterBlock($selected_filters))
		{
			if ($filter_block['nbr_filterBlocks'] == 0)
				return false;
				
			$smarty->assign($filter_block);
			$smarty->assign('hide_0_values', Configuration::get('PS_LAYERED_HIDE_0_VALUES'));
			
			return $this->display(__FILE__, 'blocklayered.tpl');
		}
		else
			return false;
	}
	
	private static function getPriceFilterSubQuery($filter_value)
	{
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$id_currency = (int)Context::getContext()->currency->id;
		else
			$id_currency = (int)Currency::getCurrent()->id;
		if (isset($filter_value) && $filter_value)
		{
			$price_filter_query = '
			INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product AND psi.id_currency = '.(int)$id_currency.'
			AND psi.price_min <= '.(int)$filter_value[1].' AND psi.price_max >= '.(int)$filter_value[0].') ';
		}
		else
		{
			$price_filter_query = '
			INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi 
			ON (psi.id_product = p.id_product AND psi.id_currency = '.(int)$id_currency.') ';
		}
		
		return array('join' => $price_filter_query, 'select' => ', psi.price_min, psi.price_max');
	}
	
	private static function filterProductsByPrice($filter_value, $product_collection)
	{
		if (empty($filter_value))
			return $product_collection;
		foreach ($product_collection as $key => $product)
		{
			if (isset($filter_value) && $filter_value && isset($product['price_min']) && isset($product['id_product'])
			&& ((int)$filter_value[0] > $product['price_min'] || (int)$filter_value[1] < $product['price_max']))
			{
				$price = Product::getPriceStatic($product['id_product'], Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX'));
				if ($price < $filter_value[0] || $price > $filter_value[1])
					continue;
				unset($product_collection[$key]);
			}
		}
		return $product_collection;
	}
	
	private static function getWeightFilterSubQuery($filter_value, $ignore_join)
	{
		if (isset($filter_value) && $filter_value)
			if ($filter_value[0] != 0 || $filter_value[1] != 0)
				return array('where' => ' AND p.`weight` BETWEEN '.(float)($filter_value[0] - 0.001).' AND '.(float)($filter_value[1] + 0.001).' ');
		
		return array();
	}
	
	private static function getId_featureFilterSubQuery($filter_value, $ignore_join)
	{
		if (empty($filter_value))
			return array();
		$query_filters = ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'feature_product fp WHERE ';
		foreach ($filter_value as $filter_val)
			$query_filters .= 'fp.`id_feature_value` = '.(int)$filter_val.' OR ';
		$query_filters = rtrim($query_filters, 'OR ').') ';
		
		return array('where' => $query_filters);
	}
	private static function getId_attribute_groupFilterSubQuery($filter_value, $ignore_join)
	{
		if (empty($filter_value))
			return array();
		$query_filters = '
		AND p.id_product IN (SELECT pa.`id_product`
		FROM `'._DB_PREFIX_.'product_attribute_combination` pac
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		WHERE ';
		
		foreach ($filter_value as $filter_val)
			$query_filters .= 'pac.`id_attribute` = '.(int)$filter_val.' OR ';
		$query_filters = rtrim($query_filters, 'OR ').') ';
		
		return array('where' => $query_filters);
	}
	
	private static function getCategoryFilterSubQuery($filter_value, $ignore_join)
	{
		if (empty($filter_value))
			return array();
		$query_filters_join = '';
		$query_filters_where = ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'category_product cp WHERE ';
		foreach ($filter_value as $id_category)
			$query_filters_where .= 'cp.`id_category` = '.(int)$id_category.' OR ';
		$query_filters_where = rtrim($query_filters_where, 'OR ').') ';
		
		return array('where' => $query_filters_where, 'join' => $query_filters_join);
	}
	
	private static function getQuantityFilterSubQuery($filter_value, $ignore_join)
	{
		if (count($filter_value) == 2 || empty($filter_value))
			return array();
		
		$query_filters_join = '';
		
		if (version_compare(_PS_VERSION_,'1.5','>'))
		{
			$query_filters = ' AND sav.quantity '.(!$filter_value[0] ? '<=' : '>').' 0 ';
			$query_filters_join = 'LEFT JOIN `'._DB_PREFIX_.'stock_available` sav ON (sav.id_product = p.id_product AND sav.id_shop = '.(int)Context::getContext()->shop->id.') ';
		}
		else
			$query_filters = ' AND p.quantity '.(!$filter_value[0] ? '<=' : '>').' 0 ';
			
		return array('where' => $query_filters, 'join' => $query_filters_join);
	}
	
	private static function getManufacturerFilterSubQuery($filter_value, $ignore_join)
	{
		if (empty($filter_value))
			$query_filters = '';
		else
		{
			array_walk($filter_value, create_function('&$id_manufacturer', '$id_manufacturer = (int)$id_manufacturer;'));
			$query_filters = ' AND p.id_manufacturer IN ('.implode($filter_value, ',').')';
		}
			if ($ignore_join)
				return array('where' => $query_filters, 'select' => ', m.name');
			else
				return array('where' => $query_filters, 'select' => ', m.name', 'join' => 'LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.id_manufacturer = p.id_manufacturer) ');
	}
	
	private static function getConditionFilterSubQuery($filter_value, $ignore_join)
	{
		if (count($filter_value) == 3 || empty($filter_value))
			return array();
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$query_filters = ' AND product_shop.condition IN (';
		else
			$query_filters = ' AND p.condition IN (';
		foreach ($filter_value as $cond)
			$query_filters .= '\''.$cond.'\',';
		$query_filters = rtrim($query_filters, ',').') ';
		
		return array('where' => $query_filters);
	}
	
	public function ajaxCallBackOffice($category_box = array(), $id_layered_filter = null)
	{
		global $cookie;
		
		if (!empty($id_layered_filter))
		{
			$layered_filter = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)$id_layered_filter);
			if ($layered_filter && isset($layered_filter['filters']) && !empty($layered_filter['filters']))
				$layered_values = self::unSerialize($layered_filter['filters']);
			if (isset($layered_values['categories']) && count($layered_values['categories']))
				foreach ($layered_values['categories'] as $id_category)
					$category_box[] = (int)$id_category;
		}
		
		/* Clean categoryBox before use */
		if (isset($category_box) && is_array($category_box))
			foreach ($category_box as &$value)
				$value = (int)$value;
		else
			$category_box = array();
		
		$attribute_groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT ag.id_attribute_group, ag.is_color_group, agl.name, COUNT(DISTINCT(a.id_attribute)) n
		FROM '._DB_PREFIX_.'attribute_group ag
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = ag.id_attribute_group)
		LEFT JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute_group = ag.id_attribute_group)
		'.(count($category_box) ? '
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		'.(version_compare(_PS_VERSION_,'1.5','>') ? Shop::addSqlAssociation('product_attribute', 'pa') : '').'
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = pa.id_product)' : '').'
		WHERE agl.id_lang = '.(int)$cookie->id_lang.
		(count($category_box) ? ' AND cp.id_category IN ('.implode(',', $category_box).')' : '').'
		GROUP BY ag.id_attribute_group');
		
		$features = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT fl.id_feature, fl.name, COUNT(DISTINCT(fv.id_feature_value)) n
		FROM '._DB_PREFIX_.'feature_lang fl
		LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature = fl.id_feature)
		'.(count($category_box) ? '
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature = fv.id_feature)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = fp.id_product)' : '').'
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND fl.id_lang = '.(int)$cookie->id_lang.
		(count($category_box) ? ' AND cp.id_category IN ('.implode(',', $category_box).')' : '').'
		GROUP BY fl.id_feature');
		
		$n_elements = count($attribute_groups) + count($features) + 4;
		if ($n_elements > 20)
			$n_elements = 20;
		
		$html = '
		<div id="layered_container_right" style="width: 360px; float: left; margin-left: 20px; height: '.(int)(30 + $n_elements * 38).'px; overflow-y: auto;">
			<h3>'.$this->l('Available filters').' <span id="num_avail_filters">(0)</span></h3>
			<ul id="all_filters"></ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_subcategories" name="layered_selection_subcategories" />
					<span class="position"></span>'.$this->l('Sub-categories filter').'
					
					<select class="filter_show_limit" name="layered_selection_subcategories_filter_show_limit">
						<option value="0">'.$this->l('No limit').'</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
					</select>
					<select class="filter_type" name="layered_selection_subcategories_filter_type">
						<option value="0">'.$this->l('Checkbox').'</option>
						<option value="1">'.$this->l('Radio button').'</option>
						<option value="2">'.$this->l('Drop-down list').'</option>
					</select>
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_stock" name="layered_selection_stock" /> <span class="position"></span>'.$this->l('Product stock filter').'
					
					<select class="filter_show_limit" name="layered_selection_stock_filter_show_limit">
						<option value="0">'.$this->l('No limit').'</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
					</select>
					<select class="filter_type" name="layered_selection_stock_filter_type">
						<option value="0">'.$this->l('Checkbox').'</option>
						<option value="1">'.$this->l('Radio button').'</option>
						<option value="2">'.$this->l('Drop-down list').'</option>
					</select>
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_condition" name="layered_selection_condition" />
					<span class="position"></span>'.$this->l('Product condition filter').'
					
					<select class="filter_show_limit" name="layered_selection_condition_filter_show_limit">
						<option value="0">'.$this->l('No limit').'</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
					</select>
					<select class="filter_type" name="layered_selection_condition_filter_type">
						<option value="0">'.$this->l('Checkbox').'</option>
						<option value="1">'.$this->l('Radio button').'</option>
						<option value="2">'.$this->l('Drop-down list').'</option>
					</select>
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_manufacturer" name="layered_selection_manufacturer" />
					<span class="position"></span>'.$this->l('Product manufacturer filter').'
					
					<select class="filter_show_limit" name="layered_selection_manufacturer_filter_show_limit">
						<option value="0">'.$this->l('No limit').'</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
					</select>
					<select class="filter_type" name="layered_selection_manufacturer_filter_type">
						<option value="0">'.$this->l('Checkbox').'</option>
						<option value="1">'.$this->l('Radio button').'</option>
						<option value="2">'.$this->l('Drop-down list').'</option>
					</select>
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_weight_slider" name="layered_selection_weight_slider" />
					<span class="position"></span>'.$this->l('Product weight filter (slider)').'
					
					<select class="filter_show_limit" name="layered_selection_weight_slider_filter_show_limit">
						<option value="0">'.$this->l('No limit').'</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
					</select>
					<select class="filter_type" name="layered_selection_weight_slider_filter_type">
						<option value="0">'.$this->l('Slider').'</option>
						<option value="1">'.$this->l('Inputs area').'</option>
						<option value="2">'.$this->l('List of values').'</option>
					</select>
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_price_slider" name="layered_selection_price_slider" />
					<span class="position"></span>'.$this->l('Product price filter (slider)').'
				
					<select class="filter_show_limit" name="layered_selection_price_slider_filter_show_limit">
						<option value="0">'.$this->l('No limit').'</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="10">10</option>
						<option value="20">20</option>
					</select>
					<select class="filter_type" name="layered_selection_price_slider_filter_type">
						<option value="0">'.$this->l('Slider').'</option>
						<option value="1">'.$this->l('Inputs area').'</option>
						<option value="2">'.$this->l('List of values').'</option>
					</select>
				</li>
			</ul>';
			
			if (count($attribute_groups))
			{
				$html .= '<ul>';
				foreach ($attribute_groups as $attribute_group)
					$html .= '
					<li class="ui-state-default layered_right">
						<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
						<input type="checkbox" id="layered_selection_ag_'.(int)$attribute_group['id_attribute_group'].'" name="layered_selection_ag_'.(int)$attribute_group['id_attribute_group'].'" />
						<span class="position"></span>
						'.($attribute_group['n'] > 1 ? sprintf($this->l('Attribute group: %1$s (%2$d attributes)'), $attribute_group['name'], $attribute_group['n']) : sprintf($this->l('Attribute group: %1$s (%2$d attribute)'), $attribute_group['name'], $attribute_group['n'])).')'.
						($attribute_group['is_color_group'] ? ' <img src="../img/admin/color_swatch.png" alt="" title="'.$this->l('This group will allow user to select a color').'" />' : '').'
					
						<select class="filter_show_limit" name="layered_selection_ag_'.(int)$attribute_group['id_attribute_group'].'_filter_show_limit">
							<option value="0">'.$this->l('No limit').'</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="20">20</option>
						</select>
						<select class="filter_type" name="layered_selection_ag_'.(int)$attribute_group['id_attribute_group'].'_filter_type">
							<option value="0">'.$this->l('Checkbox').'</option>
							<option value="1">'.$this->l('Radio button').'</option>
							<option value="2">'.$this->l('Drop-down list').'</option>
						</select>
					</li>';
				$html .= '</ul>';
			}

			if (count($features))
			{
				$html .= '<ul>';
				foreach ($features as $feature)
					$html .= '
					<li class="ui-state-default layered_right">
						<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
						<input type="checkbox" id="layered_selection_feat_'.(int)$feature['id_feature'].'" name="layered_selection_feat_'.(int)$feature['id_feature'].'" />
						<span class="position"></span>
						'.($feature['n'] > 1 ? sprintf($this->l('Feature: %1$s (%2$d values)'), $feature['name'], $feature['n']) : sprintf($this->l('Feature: %1$s (%2$d value)'), $feature['name'], $feature['n'])).')
					
						<select class="filter_show_limit" name="layered_selection_feat_'.(int)$feature['id_feature'].'_filter_show_limit">
							<option value="0">'.$this->l('No limit').'</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="10">10</option>
							<option value="20">20</option>
						</select>
						<select class="filter_type" name="layered_selection_feat_'.(int)$feature['id_feature'].'_filter_type">
							<option value="0">'.$this->l('Checkbox').'</option>
							<option value="1">'.$this->l('Radio button').'</option>
							<option value="2">'.$this->l('Drop-down list').'</option>
						</select>
					</li>';
				$html .= '</ul>';
			}

		$html .= '
		</div>';
		
		if (isset($layered_values))
		{
			$html .= '
			<script type="text/javascript">
				$(document).ready(function()
				{
					$(\'#selected_filters li\').remove();
			';
			foreach ($layered_values as $key => $layered_value)
				if ($key != 'categories' && $key != 'shop_list')
					$html .= '
						$(\'#'.$key.'\').click();
						$(\'select[name='.$key.'_filter_type]\').val('.$layered_value['filter_type'].');
						$(\'select[name='.$key.'_filter_show_limit]\').val('.$layered_value['filter_show_limit'].');
						';
			
			if (isset($layered_values['categories']) && count($layered_values['categories']))
			{
				$html .= '
							function expandCategories(categories, iteration, id_category, init) {
								if (categories[iteration])
								{
									category = $(\'#categories-treeview\').find(\'input[name="categoryBox[]"][value=\'+categories[iteration]+\']\');
								
									if (category.length)
									{
										if (category.parent().hasClass(\'expandable\'))
										{
											$(\'#\'+categories[iteration]+\' .hitarea\').click();
										}
										
										if (parseInt(categories[iteration]) == parseInt(id_category))
										{
											$(\'#layered-cat-counter\').html(parseInt($(\'#layered-cat-counter\').html()) + 1);
											if ($(\'#categories-treeview\').find(\'input[name="categoryBox[]"][value=\'+id_category+\']:checked\').length == 0)
											{
												$(\'#categories-treeview\').find(\'input[name="categoryBox[]"][value=\'+id_category+\']\').click();
												clickOnCategoryBox($(\'#categories-treeview\').find(\'input[name="categoryBox[]"][value=\'+id_category+\']\'));
											}
											collapseAllCategories();
										}
									}
									else {
										setTimeout(function() { expandCategories(categories, iteration, id_category, false); }, 20 );
										return;
									}
									$(\'#categories-treeview\').parent().parent().show();
									expandCategories(categories, iteration+1, id_category);
									if (typeof(lock_treeview_hidding) == \'undefined\' || !lock_treeview_hidding)
										$(\'#categories-treeview\').parent().parent().hide();
								}
							}
							$(\'#layered-cat-counter\').html(0);
							$(\'.nb_sub_cat_selected\').hide();
							$(\'#categories-treeview\').find(\'input[name="categoryBox[]"]:checked\').each(function(i, it) {
								$(it).click();
								updateNbSubCategorySelected($(it), false);
							});';
				
				foreach ($layered_values['categories'] as $id_category) {
					if ($id_category != 1) // @todo do we need to use the root of the current shop ?
					{
						$category = new Category($id_category);
						$parent_list = array_reverse($category->getParentsCategories());
					}
					else
						$parent_list = array(array('id_category' => 1));
					$html .= 'var categories = [];
					';
					foreach ($parent_list as $parent)
					{
						$html .= '
							categories.push('.(int)$parent['id_category'].');';
					}

					$html .= '
						expandCategories(categories, 0, '.(int)$id_category.', false);';
				}
				
				$html .= '
				updCatCounter();
				$(\'#scope_1\').attr(\'checked\', \'\');
				$(\'#scope_2\').attr(\'checked\', \'checked\');
				';
			}
			else
				$html .= '
				$(\'#scope_2\').attr(\'checked\', \'\');
				$(\'#scope_1\').attr(\'checked\', \'checked\');
				';
				
			$html .= '
			$(\'#layered_tpl_name\').val(\''.addslashes($layered_filter['name']).'\');
			$(\'#id_layered_filter\').val(\''.(int)$layered_filter['id_layered_filter'].'\');
			';
				
			$html .= '
				});
			</script>';
		}
	
		if (version_compare(_PS_VERSION_,'1.5','>') && !empty($id_layered_filter))
		{
			if (Shop::isFeatureActive() && Shop::getContext() != Shop::CONTEXT_ALL)
			{
				$shops = Shop::getShops(true, null, true);
				if (count($shops) > 1)
				{
					$helper = new HelperForm();
					$helper->id = (int)$id_layered_filter;
					$helper->table = 'layered_filter';
					$helper->identifier = 'id_layered_filter';
					$helper->base_folder = Tools::getValue('base_folder').'/themes/default/template/helpers/form/';
				
					$html .= '
					<div id="shop_association_ajax">'.$helper->renderAssoShop().'</div>
					<script type="text/javascript">
						$(document).ready(function() {
							$(\'#shop_association\').html($(\'#shop_association_ajax\').html());
							$(\'#shop_association_ajax\').remove();
							// Initialize checkbox
							$(\'.input_shop\').each(function(k, v) {
									check_shop_group_status($(v).val());
								check_all_shop();
							});
						});
					</script>';
				}
			}
		}

		return $html;
	}
	
	public function ajaxCall()
	{
		global $smarty;

		$selected_filters = $this->getSelectedFilters();
		
		$this->getProducts($selected_filters, $products, $nb_products, $p, $n, $pages_nb, $start, $stop, $range);
		
		// Add pagination variable
		$nArray = (int)Configuration::get('PS_PRODUCTS_PER_PAGE') != 10 ? array((int)Configuration::get('PS_PRODUCTS_PER_PAGE'), 10, 20, 50) : array(10, 20, 50);
		// Clean duplicate values
		$nArray = array_unique($nArray);
		asort($nArray);
		
		$smarty->assign(
			array(
				'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				'nb_products' => $nb_products,
				'category' => (object)array('id' => Tools::getValue('id_category_layered', 1)),
				'pages_nb' => (int)($pages_nb),
				'p' => (int)$p,
				'n' => (int)$n,
				'range' => (int)$range,
				'start' => (int)$start,
				'stop' => (int)$stop,
				'n_array' => ((int)Configuration::get('PS_PRODUCTS_PER_PAGE') != 10) ? array((int)Configuration::get('PS_PRODUCTS_PER_PAGE'), 10, 20, 50) : array(10, 20, 50),
				'comparator_max_item' => (int)(Configuration::get('PS_COMPARATOR_MAX_ITEM')),
				'products' => $products,
				'products_per_page' => (int)Configuration::get('PS_PRODUCTS_PER_PAGE'),
				'static_token' => Tools::getToken(false),
				'page_name' => 'category',
				'nArray' => $nArray,
			)
		);
		
		// Prevent bug with old template where category.tpl contain the title of the category and category-count.tpl do not exists
		if (file_exists(_PS_THEME_DIR_.'category-count.tpl'))
			$category_count = $smarty->fetch(_PS_THEME_DIR_.'category-count.tpl');
		else
			$category_count = '';

		if ($nb_products == 0)
			$product_list = $this->display(__FILE__, 'blocklayered-no-products.tpl');
		else
			$product_list = $smarty->fetch(_PS_THEME_DIR_.'product-list.tpl');
		
		/* We are sending an array in jSon to the .js controller, it will update both the filters and the products zones */
		return Tools::jsonEncode(array(
		'filtersBlock' => utf8_encode($this->generateFiltersBlock($selected_filters)),
		'productList' => utf8_encode($product_list),
		'pagination' => $smarty->fetch(_PS_THEME_DIR_.'pagination.tpl'),
		'categoryCount' => $category_count));
	}
	
	public function getProducts($selected_filters, &$products, &$nb_products, &$p, &$n, &$pages_nb, &$start, &$stop, &$range)
	{
		global $cookie;
		
		$products = $this->getProductByFilters($selected_filters);
		$products = Product::getProductsProperties((int)$cookie->id_lang, $products);
		
		$nb_products = $this->nbr_products;
		$range = 2; /* how many pages around page selected */
		
		$n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
		$p = $this->page;
		
		if ($p < 0)
			$p = 0;
		
		if ($p > ($nb_products / $n))
			$p = ceil($nb_products / $n);
		$pages_nb = ceil($nb_products / (int)($n));

		$start = (int)($p - $range);
		if ($start < 1)
			$start = 1;
			
		$stop = (int)($p + $range);
		if ($stop > $pages_nb)
			$stop = (int)($pages_nb);
	}

	public function rebuildLayeredStructure()
	{
		@set_time_limit(0);
		
		/* Set memory limit to 128M only if current is lower */
		$memory_limit = @ini_get('memory_limit');
		if (substr($memory_limit, -1) != 'G' && ((substr($memory_limit, -1) == 'M' && substr($memory_limit, 0, -1) < 128) || is_numeric($memory_limit) && (intval($memory_limit) < 131072)))
			@ini_set('memory_limit', '128M');

		/* Delete and re-create the layered categories table */
		Db::getInstance()->execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_category');
		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_category` (
		`id_layered_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_shop` INT(11) UNSIGNED NOT NULL,
		`id_category` INT(10) UNSIGNED NOT NULL,
		`id_value` INT(10) UNSIGNED NULL DEFAULT \'0\',
		`type` ENUM(\'category\',\'id_feature\',\'id_attribute_group\',\'quantity\',\'condition\',\'manufacturer\',\'weight\',\'price\') NOT NULL,
		`position` INT(10) UNSIGNED NOT NULL,
		`filter_type` int(10) UNSIGNED NOT NULL DEFAULT 0,
		`filter_show_limit` int(10) UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY (`id_layered_category`),
		KEY `id_category` (`id_category`,`type`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;'); /* MyISAM + latin1 = Smaller/faster */
		
		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_filter` (
		`id_layered_filter` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`name` VARCHAR(64) NOT NULL,
		`filters` TEXT NULL,
		`n_categories` INT(10) UNSIGNED NOT NULL,
		`date_add` DATETIME NOT NULL
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		
		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_filter_shop` (
		`id_layered_filter` INT(10) UNSIGNED NOT NULL,
		`id_shop` INT(11) UNSIGNED NOT NULL,
		PRIMARY KEY (`id_layered_filter`, `id_shop`),
		KEY `id_shop` (`id_shop`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
	}
	
	public function rebuildLayeredCache($products_ids = array(), $categories_ids = array())
	{
		@set_time_limit(0);
		
		$filter_data = array('categories' => array());
		
		/* Set memory limit to 128M only if current is lower */
		$memory_limit = @ini_get('memory_limit');
		if (substr($memory_limit, -1) != 'G' && ((substr($memory_limit, -1) == 'M' && substr($memory_limit, 0, -1) < 128) || is_numeric($memory_limit) && (intval($memory_limit) < 131072)))
			@ini_set('memory_limit', '128M');

		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
		$n_categories = array();
		$done_categories = array();
		$alias = 'p';
		$join_product_attribute = $join_product = '';
		if (version_compare(_PS_VERSION_,'1.5','>'))
		{
			$alias = 'product_shop';
			$join_product = Shop::addSqlAssociation('product', 'p');
			$join_product_attribute = Shop::addSqlAssociation('product_attribute', 'pa');
		}

		$attribute_groups = self::query('
		SELECT a.id_attribute, a.id_attribute_group
		FROM '._DB_PREFIX_.'attribute a
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = pa.id_product)
		'.$join_product.$join_product_attribute.'
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		WHERE c.active = 1'.
		(count($categories_ids) ? ' AND cp.id_category IN ('.implode(',', $categories_ids).')' : '').'
		AND '.$alias.'.active = 1'.
		(count($products_ids) ? ' AND p.id_product IN ('.implode(',', $products_ids).')' : ''));

		$attribute_groups_by_id = array();
		while ($row = $db->nextRow($attribute_groups))
			$attribute_groups_by_id[(int)$row['id_attribute']] = (int)$row['id_attribute_group'];

		$features = self::query('
		SELECT fv.id_feature_value, fv.id_feature
		FROM '._DB_PREFIX_.'feature_value fv
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature_value = fv.id_feature_value)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product)
		'.$join_product.'
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND c.active = 1'.(count($categories_ids) ? ' AND cp.id_category IN ('.implode(',', $categories_ids).')' : '').'
		AND '.$alias.'.active = 1'.(count($products_ids) ? ' AND p.id_product IN ('.implode(',', $products_ids).')' : ''));

		$features_by_id = array();
		while ($row = $db->nextRow($features))
			$features_by_id[(int)$row['id_feature_value']] = (int)$row['id_feature'];

		$result = self::query('
		SELECT p.id_product,
		GROUP_CONCAT(DISTINCT fv.id_feature_value) features,
		GROUP_CONCAT(DISTINCT cp.id_category) categories,
		GROUP_CONCAT(DISTINCT pac.id_attribute) attributes
		FROM '._DB_PREFIX_.'product p
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product = p.id_product)
		'.$join_product.$join_product_attribute.'
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)
		WHERE c.active = 1'.(count($categories_ids) ? ' AND cp.id_category IN ('.implode(',', $categories_ids).')' : '').
		' AND '.$alias.'.active = 1'.
		(count($products_ids) ? ' AND p.id_product IN ('.implode(',', $products_ids).')' : '').
		' AND (fv.custom IS NULL OR fv.custom = 0)
		GROUP BY p.id_product');

		
		if (version_compare(_PS_VERSION_,'1.5','>'))
			$shop_list = Shop::getShops(false, null, true);
		else
			$shop_list = array(0);
		
		$to_insert = false;
		while ($product = $db->nextRow($result))
		{
			$a = $c = $f = array();
			if (!empty($product['attributes']))
				$a = array_flip(explode(',', $product['attributes']));
			if (!empty($product['categories']))
				$c = array_flip(explode(',', $product['categories']));
			if (!empty($product['features']))
				$f = array_flip(explode(',', $product['features']));
			
			$filter_data['shop_list'] = $shop_list;
			
			foreach ($shop_list as $id_shop)
			{
				foreach ($c as $id_category => $category)
				{
					if (!in_array($id_category, $filter_data['categories']))
						$filter_data['categories'][] = $id_category;
					
					if (!isset($n_categories[(int)$id_category]))
						$n_categories[(int)$id_category] = 1;
					if (!isset($done_categories[(int)$id_category]['cat']))
					{
						$filter_data['layered_selection_subcategories'] = array('filter_type' => 0, 'filter_show_limit' => 0);
						$done_categories[(int)$id_category]['cat'] = true;
						$to_insert = true;
					}
					foreach ($a as $k_attribute => $attribute)
						if (!isset($done_categories[(int)$id_category]['a'.(int)$attribute_groups_by_id[(int)$k_attribute]]))
						{
							$filter_data['layered_selection_ag_'.(int)$attribute_groups_by_id[(int)$k_attribute]] = array('filter_type' => 0, 'filter_show_limit' => 0);
							$done_categories[(int)$id_category]['a'.(int)$attribute_groups_by_id[(int)$k_attribute]] = true;
							$to_insert = true;
						}
					foreach ($f as $k_feature => $feature)
						if (!isset($done_categories[(int)$id_category]['f'.(int)$features_by_id[(int)$k_feature]]))
						{
							$filter_data['layered_selection_feat_'.(int)$features_by_id[(int)$k_feature]] = array('filter_type' => 0, 'filter_show_limit' => 0);
							$done_categories[(int)$id_category]['f'.(int)$features_by_id[(int)$k_feature]] = true;
							$to_insert = true;
						}
					if (!isset($done_categories[(int)$id_category]['q']))
					{
						$filter_data['layered_selection_stock'] = array('filter_type' => 0, 'filter_show_limit' => 0);
						$done_categories[(int)$id_category]['q'] = true;
						$to_insert = true;
					}
					if (!isset($done_categories[(int)$id_category]['m']))
					{
						$filter_data['layered_selection_manufacturer'] = array('filter_type' => 0, 'filter_show_limit' => 0);
						$done_categories[(int)$id_category]['m'] = true;
						$to_insert = true;
					}
					if (!isset($done_categories[(int)$id_category]['c']))
					{
						$filter_data['layered_selection_condition'] = array('filter_type' => 0, 'filter_show_limit' => 0);
						$done_categories[(int)$id_category]['c'] = true;
						$to_insert = true;
					}
					if (!isset($done_categories[(int)$id_category]['w']))
					{
						$filter_data['layered_selection_weight_slider'] = array('filter_type' => 0, 'filter_show_limit' => 0);
						$done_categories[(int)$id_category]['w'] = true;
						$to_insert = true;
					}
					if (!isset($done_categories[(int)$id_category]['p']))
					{
						$filter_data['layered_selection_price_slider'] = array('filter_type' => 0, 'filter_show_limit' => 0);
						$done_categories[(int)$id_category]['p'] = true;
						$to_insert = true;
					}
				}
			}
		}
		if ($to_insert)
		{
			Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_filter(name, filters, n_categories, date_add)
				VALUES (\''.sprintf($this->l('My template %s'), date('Y-m-d')).'\', \''.pSQL(serialize($filter_data)).'\', '.count($filter_data['categories']).', NOW())');
			
			if (version_compare(_PS_VERSION_,'1.5','>'))
			{
				$last_id = Db::getInstance()->Insert_ID();
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'layered_filter_shop WHERE `id_layered_filter` = '.$last_id);
				foreach ($shop_list as $id_shop)
					Db::getInstance()->execute('INSERT INTO '._DB_PREFIX_.'layered_filter_shop (`id_layered_filter`, `id_shop`)
						VALUES('.$last_id.', '.(int)$id_shop.')');
			}
			
			$this->buildLayeredCategories();
		}
	}
	
	public function buildLayeredCategories()
	{
		// Get all filter template
		$res = Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'layered_filter ORDER BY date_add DESC');
		$categories = array();
		
		// Remove all from layered_category
		Db::getInstance()->execute('TRUNCATE '._DB_PREFIX_.'layered_category');
			
		if (!count($res)) // No filters templates defined, nothing else to do
			return true;
		
		$sql_to_insert = 'INSERT INTO '._DB_PREFIX_.'layered_category (id_category, id_shop, id_value, type, position, filter_show_limit, filter_type) VALUES ';
		$values = false;
		foreach ($res as $filter_template)
		{
			$data = self::unSerialize($filter_template['filters']);
			foreach ($data['categories'] as  $id_category)
			{
				$n = 0;
				if (!in_array($id_category, $categories)) // Last definition, erase preivious categories defined
				{
					$categories[] = $id_category;
					foreach ($data as $key => $value)
						if (substr($key, 0, 17) == 'layered_selection')
						{
							$values = true;
							$type = $value['filter_type'];
							$limit = $value['filter_show_limit'];
							$n++;
							
							foreach ($data['shop_list'] as $id_shop)
							{
								if ($key == 'layered_selection_stock')
									$sql_to_insert .= '('.(int)$id_category.', '.(int)$id_shop.', NULL,\'quantity\','.(int)$n.', '.(int)$limit.', '.(int)$type.'),';
								else if ($key == 'layered_selection_subcategories')
									$sql_to_insert .= '('.(int)$id_category.', '.(int)$id_shop.', NULL,\'category\','.(int)$n.', '.(int)$limit.', '.(int)$type.'),';
								else if ($key == 'layered_selection_condition')
									$sql_to_insert .= '('.(int)$id_category.', '.(int)$id_shop.', NULL,\'condition\','.(int)$n.', '.(int)$limit.', '.(int)$type.'),';
								else if ($key == 'layered_selection_weight_slider')
									$sql_to_insert .= '('.(int)$id_category.', '.(int)$id_shop.', NULL,\'weight\','.(int)$n.', '.(int)$limit.', '.(int)$type.'),';
								else if ($key == 'layered_selection_price_slider')
									$sql_to_insert .= '('.(int)$id_category.', '.(int)$id_shop.', NULL,\'price\','.(int)$n.', '.(int)$limit.', '.(int)$type.'),';
								else if ($key == 'layered_selection_manufacturer')
									$sql_to_insert .= '('.(int)$id_category.', '.(int)$id_shop.', NULL,\'manufacturer\','.(int)$n.', '.(int)$limit.', '.(int)$type.'),';
								else if (substr($key, 0, 21) == 'layered_selection_ag_')
									$sql_to_insert .= '('.(int)$id_category.', '.(int)$id_shop.', '.(int)str_replace('layered_selection_ag_', '', $key).',
										\'id_attribute_group\','.(int)$n.', '.(int)$limit.', '.(int)$type.'),';
								else if (substr($key, 0, 23) == 'layered_selection_feat_')
									$sql_to_insert .= '('.(int)$id_category.', '.(int)$id_shop.', '.(int)str_replace('layered_selection_feat_', '', $key).',
										\'id_feature\','.(int)$n.', '.(int)$limit.', '.(int)$type.'),';
							}
						}
				}
			}
		}
		if ($values)
			Db::getInstance()->execute(rtrim($sql_to_insert, ','));
	}
	
	/**
	 * Define our own Tools::unSerialize() (since 1.5), to be available in PrestaShop 1.4
	 */
	protected static function unSerialize($serialized)
	{
		if (method_exists('Tools', 'unserialize'))
			return Tools::unSerialize($serialized);
		
		if (is_string($serialized) && (strpos($serialized, 'O:') === false || !preg_match('/(^|;|{|})O:[0-9]+:"/', $serialized)))
			return @unserialize($serialized);

		return false;
	}
}
