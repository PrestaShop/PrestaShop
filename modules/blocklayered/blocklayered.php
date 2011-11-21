<?php
/*
* 2007-2011 PrestaShop 
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
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class BlockLayered extends Module
{
	private $products;
	private $nbr_products;

	public function __construct()
	{
		$this->name = 'blocklayered';
		$this->tab = 'front_office_features';
		$this->version = 1.5;
		$this->author = 'PrestaShop';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Layered navigation block');
		$this->description = $this->l('Displays a block with layered navigation filters.');
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
			Configuration::updateValue('PS_LAYERED_HIDE_0_VALUES', 0);
			Configuration::updateValue('PS_LAYERED_SHOW_QTIES', 1);
			
			$this->rebuildLayeredStructure();
			$this->rebuildLayeredCache();
			self::installPriceIndexTable();
			$this->installFriendlyUrlTable();
			$this->installIndexableAttributeTable();
			$this->installProductAttributeTable();
			
			$this->indexUrl();
			$this->indexAttribute();
			
			if (Db::getInstance()->getValue('SELECT COUNT(*) FROM `'._DB_PREFIX_.'product`') < 10000) // Lock price indexation if too many products
				self::fullPricesIndexProcess();
			
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
		Configuration::deleteByName('PS_LAYERED_INDEXED');
		
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_price_index');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_friendly_url');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_indexable_attribute_group');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_indexable_feature');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_indexable_feature_lang_value');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_category');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_filter');
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_product_attribute');
		return parent::uninstall();
	}
	
	private static function installPriceIndexTable()
	{
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_price_index`');
		
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_price_index` (
		`id_product` INT  NOT NULL, `id_currency` INT NOT NULL,
		`price_min` INT NOT NULL, `price_max` INT NOT NULL,
		PRIMARY KEY (`id_product`, `id_currency`), INDEX `id_currency` (`id_currency`),
		INDEX `price_min` (`price_min`), INDEX `price_max` (`price_max`)) ENGINE = '._MYSQL_ENGINE_);
	}
	
	private function installFriendlyUrlTable()
	{
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_friendly_url`');
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_friendly_url` (
		`id_layered_friendly_url` INT NOT NULL AUTO_INCREMENT,
		`url_key` varchar(32) NOT NULL,
		`data` varchar(200) NOT NULL,
		`id_lang` INT NOT NULL,
		PRIMARY KEY (`id_layered_friendly_url`),
		INDEX `id_lang` (`id_lang`)) ENGINE = '._MYSQL_ENGINE_);

		Db::getInstance()->Execute('CREATE INDEX `url_key` ON `'._DB_PREFIX_.'layered_friendly_url`(url_key(5))');
	}
	
	private function installIndexableAttributeTable()
	{
		// Attributes Groups
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_attribute_group`');
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_attribute_group` (
		`id_attribute_group` INT NOT NULL,
		`indexable` BOOL NOT NULL DEFAULT 0,
		PRIMARY KEY (`id_attribute_group`)) ENGINE = '._MYSQL_ENGINE_);
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'layered_indexable_attribute_group`
		SELECT id_attribute_group, 1 FROM `'._DB_PREFIX_.'attribute_group`');
		
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value`');
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_attribute_group_lang_value` (
		`id_attribute_group` INT NOT NULL,
		`id_lang` INT NOT NULL,
		`url_name` VARCHAR(20),
		`meta_title` VARCHAR(20),
		PRIMARY KEY (`id_attribute_group`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_);
		
		// Attributes
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_attribute_lang_value`');
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_attribute_lang_value` (
		`id_attribute` INT NOT NULL,
		`id_lang` INT NOT NULL,
		`url_name` VARCHAR(20),
		`meta_title` VARCHAR(20),
		PRIMARY KEY (`id_attribute`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_);
		
		
		// Features
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_feature`');
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_feature` (
		`id_feature` INT NOT NULL,
		`indexable` BOOL NOT NULL DEFAULT 0,
		PRIMARY KEY (`id_feature`)) ENGINE = '._MYSQL_ENGINE_);
		
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'layered_indexable_feature`
		SELECT id_feature, 1 FROM `'._DB_PREFIX_.'feature`');
		
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_feature_lang_value`');
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_feature_lang_value` (
		`id_feature` INT NOT NULL,
		`id_lang` INT NOT NULL,
		`url_name` VARCHAR(20) NOT NULL,
		`meta_title` VARCHAR(20),
		PRIMARY KEY (`id_feature`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_);
		
		// Features values
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_indexable_feature_value_lang_value`');
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_indexable_feature_value_lang_value` (
		`id_feature_value` INT NOT NULL,
		`id_lang` INT NOT NULL,
		`url_name` VARCHAR(20),
		`meta_title` VARCHAR(20),
		PRIMARY KEY (`id_feature_value`, `id_lang`)) ENGINE = '._MYSQL_ENGINE_);
	}
	
	/**
	 * 
	 * create table product attribute
	 */
	public function installProductAttributeTable()
	{
		Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'layered_product_attribute`');
		Db::getInstance()->Execute('
		CREATE TABLE `'._DB_PREFIX_.'layered_product_attribute` (
		`id_attribute` int(10) unsigned NOT NULL,
		`id_product` int(10) unsigned NOT NULL,
		`id_attribute_group` int(10) unsigned NOT NULL DEFAULT "0",
		KEY `id_attribute` (`id_attribute`)
		) ENGINE= '._MYSQL_ENGINE_);
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
		
		Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'layered_product_attribute` (`id_attribute`, `id_product`, `id_attribute_group`) 
		SELECT pac.id_attribute, pa.id_product, ag.id_attribute_group 
		FROM '._DB_PREFIX_.'product_attribute pa 
		INNER JOIN '._DB_PREFIX_.'product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute 
		INNER JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute = pac.id_attribute) 
		INNER JOIN '._DB_PREFIX_.'attribute_group ag ON ag.id_attribute_group = a.id_attribute_group
		'.(is_null($id_product) ? '' : 'AND pa.id_product = '.(int)$id_product).'
		GROUP BY a.id_attribute, pa.id_product');
		
		return 1;
	}
	/*
	 * Url indexation
	 */
	public function indexUrl($ajax = false, $truncate = true)
	{
		if ($truncate)
			Db::getInstance()->execute('TRUNCATE '._DB_PREFIX_.'layered_friendly_url');
		
		$attributeValuesByLang = array();
		$filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT lc.*, id_lang, name, link_rewrite, cl.id_category
		FROM '._DB_PREFIX_.'layered_category lc
		INNER JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = lc.id_category AND lc.id_category <> 1 )
		GROUP BY type, id_value, id_lang');
		if (!$filters)
			return;

		foreach ($filters as $filter)
			switch ($filter['type'])
			{
				case 'id_attribute_group':
					$attributes = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
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
						if (!isset($attributeValuesByLang[$attribute['id_lang']]))
							$attributeValuesByLang[$attribute['id_lang']] = array();
						if (!isset($attributeValuesByLang[$attribute['id_lang']]['c'.$attribute['id_name']]))
							$attributeValuesByLang[$attribute['id_lang']]['c'.$attribute['id_name']] = array();
						$attributeValuesByLang[$attribute['id_lang']]['c'.$attribute['id_name']][] = array(
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
					$features = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
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
						if (!isset($attributeValuesByLang[$feature['id_lang']]))
							$attributeValuesByLang[$feature['id_lang']] = array();
						if (!isset($attributeValuesByLang[$feature['id_lang']]['f'.$feature['id_name']]))
							$attributeValuesByLang[$feature['id_lang']]['f'.$feature['id_name']] = array();
						$attributeValuesByLang[$feature['id_lang']]['f'.$feature['id_name']][] = array(
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
					$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT cl.name, cl.id_lang, c.id_category
					FROM '._DB_PREFIX_.'category c
					INNER JOIN '._DB_PREFIX_.'category_lang cl ON (c.id_category = cl.id_category)
					WHERE cl.id_lang = '.(int)$filter['id_lang']);
					foreach ($categories as $category)
					{
						if (!isset($attributeValuesByLang[$category['id_lang']]))
							$attributeValuesByLang[$category['id_lang']] = array();
						if (!isset($attributeValuesByLang[$category['id_lang']]['category']))
							$attributeValuesByLang[$category['id_lang']]['category'] = array();
						$attributeValuesByLang[$category['id_lang']]['category'][] = array('name' => $this->l('Categories'),
						'id_name' => null, 'value' => $category['name'], 'id_value' => $category['id_category'],
						'category_name' => $filter['link_rewrite'], 'type' => $filter['type']);
					}
					break;
						
				case 'manufacturer':
					$manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
					SELECT m.name as name,l.id_lang as id_lang,  id_manufacturer
					FROM '._DB_PREFIX_.'manufacturer m , '._DB_PREFIX_.'lang l
					WHERE l.id_lang = '.(int)$filter['id_lang'].' ');
				
					foreach ($manufacturers as $manufacturer)
					{
						if (!isset($attributeValuesByLang[$manufacturer['id_lang']]))
							$attributeValuesByLang[$manufacturer['id_lang']] = array();
						if (!isset($attributeValuesByLang[$manufacturer['id_lang']]['manufacturer']))
							$attributeValuesByLang[$manufacturer['id_lang']]['manufacturer'] = array();
						$attributeValuesByLang[$manufacturer['id_lang']]['manufacturer'][] = array('name' => $this->translateWord('Manufacturer', $manufacturer['id_lang']),
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
						$attributeValuesByLang[$filter['id_lang']]['quantity'][] = array('name' => $this->translateWord('Availability', (int)$filter['id_lang']),
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
						$attributeValuesByLang[$filter['id_lang']]['condition'][] = array('name' => $this->translateWord('Condition', (int)$filter['id_lang']),
						'id_name' => null, 'value' => $condition, 'id_value' => $key,
						'category_name' => $filter['link_rewrite'], 'type' => $filter['type']);
					break;
			}
		
		// Foreach langs
		foreach ($attributeValuesByLang as $id_lang => $attributeValues)
		{
			// Foreach attributes generate a couple "/<attribute_name>_<atttribute_value>". For example: color_blue
			foreach ($attributeValues as $attribute)
					foreach ($attribute as $param)
					{
						$selectedFilters = array();
						$link = '/'.str_replace('-', '_', Tools::link_rewrite($param['name'])).'-'.str_replace('-', '_', Tools::link_rewrite($param['value']));
						$selectedFilters[$param['type']] = array();
						if (!isset($param['id_id_value']))
							$param['id_id_value'] = $param['id_value'];
						$selectedFilters[$param['type']][$param['id_id_value']] = $param['id_value'];
						$urlKey = md5($link);
						$idLayeredFriendlyUrl = Db::getInstance()->getValue('SELECT id_layered_friendly_url
						FROM `'._DB_PREFIX_.'layered_friendly_url` WHERE `id_lang` = '.$id_lang.' AND `url_key` = \''.$urlKey.'\'');
						if ($idLayeredFriendlyUrl == false)
						{
							Db::getInstance()->AutoExecute(_DB_PREFIX_.'layered_friendly_url', array('url_key' => $urlKey, 'data' => serialize($selectedFilters), 'id_lang' => $id_lang), 'INSERT');
							$idLayeredFriendlyUrl = Db::getInstance()->Insert_ID();
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
		$currentKey = '<{'.strtolower( $this->name).'}'.strtolower(_THEME_NAME_).'>'.strtolower($this->name).'_'.md5($string);
		$defaultKey = '<{'.strtolower( $this->name).'}prestashop>'.strtolower($this->name).'_'.md5($string);
			
		if (isset($_MODULES[$id_lang][$currentKey]))
			$ret = stripslashes($_MODULES[$id_lang][$currentKey]);
		else if (isset($_MODULES[$id_lang][Tools::strtolower($currentKey)]))
			$ret = stripslashes($_MODULES[$id_lang][Tools::strtolower($currentKey)]);
		else if (isset($_MODULES[$id_lang][$defaultKey]))
			$ret = stripslashes($_MODULES[$id_lang][$defaultKey]);
		else if (isset($_MODULES[$id_lang][Tools::strtolower($defaultKey)]))
			$ret = stripslashes($_MODULES[$id_lang][Tools::strtolower($defaultKey)]);
		else
			$ret = stripslashes($string);

		return str_replace('"', '&quot;', $ret);
	}
	
	public function hookProductListAssign($params)
	{
		global $smarty;
		if (!Configuration::get('PS_LAYERED_INDEXED'))
			return;
		// Inform the hook was executed
		$params['hookExecuted'] = true;
		// List of product to overrride categoryController
		$params['catProducts'] = array();
		$selectedFilters = $this->getSelectedFilters();
		$filterBlock = self::getFilterBlock($selectedFilters);
		$title = '';
		if (is_array($filterBlock['title_values']))
			foreach ($filterBlock['title_values'] as $key => $val)
				$title .= ' – '.$key.' '.implode('/', $val);
		
		$smarty->assign('categoryNameComplement', $title);
		$this->getProducts($selectedFilters, $params['catProducts'], $params['nbProducts'], $p, $n, $pages_nb, $start, $stop, $range);
		// Need a nofollow on the pagination links?
		$smarty->assign('no_follow', $filterBlock['nofollow']);
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
		
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature WHERE id_feature = '.(int)$params['id_feature']);
		Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_feature VALUES ('.(int)$params['id_feature'].', '.(int)Tools::getValue('layered_indexable').')');
		
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature_lang_value WHERE id_feature = '.(int)$params['id_feature']); // don't care about the id_lang
		foreach (Language::getLanguages(false) as $language)
		{
			// Data are validated by method "hookPostProcessFeature"
			$id_lang = (int)$language['id_lang'];
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_feature_lang_value
			VALUES ('.(int)$params['id_feature'].', '.$id_lang.', \''.pSQL(Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang))).'\',
			\''.pSQL(Tools::safeOutput(Tools::getValue('meta_title_'.$id_lang), true)).'\')');
		}
	}

	public function hookAfterSaveFeatureValue($params)
	{
		if (!$params['id_feature_value'])
			return;
		
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature_value_lang_value WHERE id_feature_value = '.(int)$params['id_feature_value']); // don't care about the id_lang
		foreach (Language::getLanguages(false) as $language)
		{
			// Data are validated by method "hookPostProcessFeatureValue"
			$id_lang = (int)$language['id_lang'];
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_feature_value_lang_value
			VALUES ('.(int)$params['id_feature_value'].', '.$id_lang.', \''.pSQL(Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang))).'\',
			\''.pSQL(Tools::safeOutput(Tools::getValue('meta_title_'.$id_lang), true)).'\')');
		}
	}
	
	public function hookAfterDeleteFeatureValue($params)
	{
		if (!$params['id_feature_value'])
			return;
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature_value_lang_value WHERE id_feature_value = '.(int)$params['id_feature_value']);
	}
	
	public function hookPostProcessFeatureValue($params)
	{
		$this->hookPostProcessAttributeGroup($params);
	}
	
	public function hookFeatureValueForm($params)
	{
		$languages = Language::getLanguages(false);
		$default_form_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$langValue = array();
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
		'SELECT url_name, meta_title, id_lang FROM '._DB_PREFIX_.'layered_indexable_feature_value_lang_value
		WHERE id_feature_value = '.(int)$params['id_feature_value']);
		if ($result)
			foreach ($result as $data)
				$langValue[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);
		$return = '<div class="clear"></div>
				<label>'.$this->l('Url:').'</label>
				<div class="margin-form">
				<script type="text/javascript">
					flag_fields += \'¤url_name¤meta_title\';
				</script>';
		foreach ($languages as $language)
			$return .= '
					<div id="url_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="url_name_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$langValue[$language['id_lang']]['url_name'], true).'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}_<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Specific format in url block layered generation').'</p>
					</div>';
		$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'url_name', true, true);
		$return .= '
						<div class="clear"></div>
					</div>
					<label>'.$this->l('Meta title:').' </label>
					<div class="margin-form">';
		foreach ($languages as $language)
			$return .= '
						<div id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
							<input size="33" type="text" name="meta_title_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$langValue[$language['id_lang']]['meta_title'], true).'" />
							<p style="clear: both">'.$this->l('Specific format for meta title').'</p>
						</div>';
		$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'meta_title', true, true);
		$return .= '
						<div class="clear"></div>
					</div>';
		return $return;
	}
	
	public function hookAfterSaveAttribute($params)
	{
		if (!$params['id_attribute'])
			return;
		
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_lang_value WHERE id_attribute = '.(int)$params['id_attribute']); // don't care about the id_lang
		foreach (Language::getLanguages(false) as $language)
		{
			// Data are validated by method "hookPostProcessAttribute"
			$id_lang = (int)$language['id_lang'];
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_attribute_lang_value
			VALUES ('.(int)$params['id_attribute'].', '.$id_lang.', \''.pSQL(Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang))).'\',
			\''.pSQL(Tools::safeOutput(Tools::getValue('meta_title_'.$id_lang), true)).'\')');
		}
	}
	
	public function hookAfterDeleteAttribute($params)
	{
		if (!$params['id_attribute'])
			return;
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_lang_value WHERE id_attribute = '.(int)$params['id_attribute']);
	}
	
	public function hookPostProcessAttribute($params)
	{
		$this->hookPostProcessAttributeGroup($params);
	}
	
	public function hookAttributeForm($params)
	{
		$languages = Language::getLanguages(false);
		$default_form_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$langValue = array();
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
		'SELECT url_name, meta_title, id_lang FROM '._DB_PREFIX_.'layered_indexable_attribute_lang_value
		WHERE id_attribute = '.(int)$params['id_attribute']);
		if ($result)
			foreach ($result as $data)
				$langValue[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);
		$return = '<div class="clear"></div>
				<label>'.$this->l('Url:').'</label>
				<div class="margin-form">
				<script type="text/javascript">
					flag_fields += \'¤url_name¤meta_title\';
				</script>';
		foreach ($languages as $language)
			$return .= '
					<div id="url_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="url_name_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$langValue[$language['id_lang']]['url_name'], true).'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}_<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Specific format in url block layered generation').'</p>
					</div>';
		$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'url_name', true, true);
		$return .= '
						<div class="clear"></div>
					</div>
					<label>'.$this->l('Meta title:').' </label>
					<div class="margin-form">';
		foreach ($languages as $language)
			$return .= '
						<div id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
							<input size="33" type="text" name="meta_title_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$langValue[$language['id_lang']]['meta_title'], true).'" />
							<p style="clear: both">'.$this->l('Specific format for meta title').'</p>
						</div>';
		$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'meta_title', true, true);
		$return .= '
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
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_feature WHERE id_feature = '.(int)$params['id_feature']);
	}
	
	public function hookAfterSaveAttributeGroup($params)
	{
		if (!$params['id_attribute_group'] || Tools::getValue('layered_indexable') === false)
			return;
		
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_group WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
		Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_attribute_group VALUES ('.(int)$params['id_attribute_group'].', '.(int)Tools::getValue('layered_indexable').')');

		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value WHERE id_attribute_group = '.(int)$params['id_attribute_group']); // don't care about the id_lang
		foreach (Language::getLanguages(false) as $language)
		{
			// Data are validated by method "hookPostProcessAttributeGroup"
			$id_lang = (int)$language['id_lang'];
			Db::getInstance()->Execute('INSERT INTO '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value
			VALUES ('.(int)$params['id_attribute_group'].', '.$id_lang.', \''.pSQL(Tools::link_rewrite(Tools::getValue('url_name_'.$id_lang))).'\',
			\''.pSQL(Tools::safeOutput(Tools::getValue('meta_title_'.$id_lang), true)).'\')');
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

		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_group WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
	}
	
	public function hookAttributeGroupForm($params)
	{
		$languages = Language::getLanguages(false);
		$default_form_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$indexable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT indexable FROM '._DB_PREFIX_.'layered_indexable_attribute_group
		WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
		$langValue = array();
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
		'SELECT url_name, meta_title, id_lang FROM '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value
		WHERE id_attribute_group = '.(int)$params['id_attribute_group']);
		if ($result)
			foreach ($result as $data)
				$langValue[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);

		if ($indexable === false)
			$on = true;
		else
			$on = (bool)$indexable;

		$return = '<div class="clear"></div>
				<label>'.$this->l('Url:').'</label>
				<div class="margin-form">
				<script type="text/javascript">
					flag_fields += \'¤url_name¤meta_title\';
				</script>';
		foreach ($languages as $language)
			$return .= '
					<div id="url_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="url_name_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$langValue[$language['id_lang']]['url_name'], true).'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}_<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Specific format in url block layered generation').'</p>
					</div>';
		$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'url_name', true, true);
		$return .= '
						<div class="clear"></div>
					</div>
					<label>'.$this->l('Meta title:').' </label>
					<div class="margin-form">';
		foreach ($languages as $language)
			$return .= '
						<div id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
							<input size="33" type="text" name="meta_title_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$langValue[$language['id_lang']]['meta_title'], true).'" />
							<p style="clear: both">'.$this->l('Specific format for meta title').'</p>
						</div>';
		$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'meta_title', true, true);
		$return .= '
						<div class="clear"></div>
					</div>
			<label>'.$this->l('Indexable:').' </label>
				<div class="margin-form">
					<input type="radio" '.(($on) ? 'checked="checked"' : '').' value="1" id="indexable_on" name="layered_indexable">
					<label for="indexable_on" class="t"><img title="Yes" alt="Enabled" src="../img/admin/enabled.gif"></label>
					<input type="radio" '.((!$on) ? 'checked="checked"' : '').' value="0" id="indexable_off" name="layered_indexable">
					<label for="indexable_off" class="t"><img title="No" alt="Disabled" src="../img/admin/disabled.gif"></label>
					<p>'.$this->l('Use this attribute in url generated by the module block layered navigation').'</p>
				</div>';
		return $return;
	}
	
	public function hookFeatureForm($params)
	{
		$languages = Language::getLanguages(false);
		$default_form_language = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$indexable = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT indexable FROM '._DB_PREFIX_.'layered_indexable_feature WHERE id_feature = '.(int)$params['id_feature']);
		$langValue = array();
		
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS(
		'SELECT url_name, meta_title, id_lang FROM '._DB_PREFIX_.'layered_indexable_feature_lang_value
		WHERE id_feature = '.(int)$params['id_feature']);
		if ($result)
			foreach ($result as $data)
				$langValue[$data['id_lang']] = array('url_name' => $data['url_name'], 'meta_title' => $data['meta_title']);
		 
		
		if ($indexable === false)
			$on = true;
		else
			$on = (bool)$indexable;
		
		$return = '<div class="clear"></div>
				<label>'.$this->l('Url:').'</label>
				<div class="margin-form">
				<script type="text/javascript">
					flag_fields += \'¤url_name¤meta_title\';
				</script>';
		foreach ($languages as $language)
			$return .= '
					<div id="url_name_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
						<input size="33" type="text" name="url_name_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$langValue[$language['id_lang']]['url_name'], true).'" />
						<span class="hint" name="help_box">'.$this->l('Invalid characters:').' <>;=#{}_<span class="hint-pointer">&nbsp;</span></span>
						<p style="clear: both">'.$this->l('Specific format in url block layered generation').'</p>
					</div>';
		$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'url_name', true, true);
		$return .= '
						<div class="clear"></div>
					</div>
					<label>'.$this->l('Meta title:').' </label>
					<div class="margin-form">';
		foreach ($languages as $language)
			$return .= '
						<div id="meta_title_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $default_form_language ? 'block' : 'none').'; float: left;">
							<input size="33" type="text" name="meta_title_'.$language['id_lang'].'" value="'.Tools::safeOutput(@$langValue[$language['id_lang']]['meta_title'], true).'" />
							<p style="clear: both">'.$this->l('Specific format for meta title').'</p>
						</div>';
		$return .= $this->displayFlags($languages, $default_form_language, 'flag_fields', 'meta_title', true, true);
		$return .= '
						<div class="clear"></div>
					</div>
			<label>'.$this->l('Indexable:').' </label>
				<div class="margin-form">
					<input type="radio" '.(($on) ? 'checked="checked"' : '').' value="1" id="indexable_on" name="layered_indexable">
					<label for="indexable_on" class="t"><img title="Yes" alt="Enabled" src="../img/admin/enabled.gif"></label>
					<input type="radio" '.((!$on) ? 'checked="checked"' : '').' value="0" id="indexable_off" name="layered_indexable">
					<label for="indexable_off" class="t"><img title="No" alt="Disabled" src="../img/admin/disabled.gif"></label>
					<p>'.$this->l('Use this attribute in url generated by the module block layered navigation').'</p>
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
			$nbProducts = (int)Db::getInstance()->getValue('SELECT count(*) FROM '._DB_PREFIX_.'product WHERE `active` = 1');
		else
			$nbProducts = (int)Db::getInstance()->getValue(
			'SELECT COUNT(*) FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN  `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product)
			WHERE `active` = 1 AND psi.id_product IS NULL');
		
		$maxExecutionTime = @ini_get('max_execution_time');
		if ($maxExecutionTime > 5 || $maxExecutionTime <= 0)
			$maxExecutionTime = 5;
		
		$startTime = microtime(true);
		
		do
		{
			$cursor = (int)self::indexPricesUnbreakable((int)$cursor, $full, $smart);
			$timeElapsed = microtime(true) - $startTime;
		}
		while ($cursor < $nbProducts && (Tools::getMemoryLimit()) > memory_get_peak_usage() && $timeElapsed < $maxExecutionTime);
		
		if (($nbProducts > 0 && !$full || $cursor < $nbProducts && $full) && !$ajax)
		{
			$token = substr(Tools::encrypt('blocklayered/index'), 0, 10);
			if (!Tools::file_get_contents(Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-price-indexer.php?token='.$token.'&cursor='.(int)$cursor.'&full='.(int)$full))
				self::indexPrices((int)$cursor, (int)$full);
			return $cursor;
		}
		if ($ajax && $nbProducts > 0 && $cursor < $nbProducts && $full)
			return '{"cursor": '.$cursor.', "count": '.($nbProducts - $cursor).'}';
		else if ($ajax && $nbProducts > 0 && !$full)
			return '{"cursor": '.$cursor.', "count": '.($nbProducts).'}';
		else
		{
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
			$query = '
			SELECT id_product
			FROM `'._DB_PREFIX_.'product`
			WHERE `active` = 1
			ORDER by id_product LIMIT '.(int)$cursor.','.(int)$length;
		else
			$query = '
			SELECT p.id_product
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN  `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product)
			WHERE `active` = 1 AND psi.id_product is null
			ORDER by id_product LIMIT 0,'.(int)$length;
		
		foreach (Db::getInstance()->ExecuteS($query) as $product)
			self::indexProductPrices((int)$product['id_product'], ($smart && $full));

		return (int)($cursor + $length);
	}
	
	public static function indexProductPrices($idProduct, $smart = true)
	{
		static $groups = null;

		if (is_null($groups))
		{
			$groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_group FROM `'._DB_PREFIX_.'group_reduction`');
			if (!$groups)
				$groups = array();
		}
		
		static $currencyList = null;
		if (is_null($currencyList))
			$currencyList = Currency::getCurrencies();
		
		$minPrice = array();
		$maxPrice = array();
		
		if ($smart)
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'layered_price_index` WHERE `id_product` = '.(int)$idProduct);
		
		$maxTaxRate = Db::getInstance()->getValue('
		SELECT max(t.rate) max_rate
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (trg.id_tax_rules_group = p.id_tax_rules_group)
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.id_tax_rules_group = trg.id_tax_rules_group)
		LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax AND t.active = 1)
		WHERE id_product = '.(int)$idProduct.'
		GROUP BY id_product');
		
		$productMinPrices = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT id_shop, id_currency, id_country, id_group, from_quantity
		FROM `'._DB_PREFIX_.'specific_price`
		WHERE id_product = '.(int)$idProduct);
		
		// Get min price
		foreach ($currencyList as $currency)
		{
			$price = Product::priceCalculation(null, (int)$idProduct, null, null, null, null,
				$currency['id_currency'], null, null, false, true, false, true, true,
				$specificPriceOutput, true);
			
			if (!isset($maxPrice[$currency['id_currency']]))
				$maxPrice[$currency['id_currency']] = 0;
			if (!isset($minPrice[$currency['id_currency']]))
				$minPrice[$currency['id_currency']] = null;
			if ($price > $maxPrice[$currency['id_currency']])
				$maxPrice[$currency['id_currency']] = $price;
			if ($price == 0)
				continue;
			if (is_null($minPrice[$currency['id_currency']]) || $price < $minPrice[$currency['id_currency']])
				$minPrice[$currency['id_currency']] = $price;
		}
		
		foreach ($productMinPrices as $specificPrice)
			foreach ($currencyList as $currency)
			{
				if ($specificPrice['id_currency'] && $specificPrice['id_currency'] != $currency['id_currency'])
					continue;
				$price = Product::priceCalculation((($specificPrice['id_shop'] == 0) ? null : (int)$specificPrice['id_shop']), (int)$idProduct,
					null, (($specificPrice['id_country'] == 0) ? null : $specificPrice['id_country']), null, null,
					$currency['id_currency'], (($specificPrice['id_group'] == 0) ? null : $specificPrice['id_group']),
					$specificPrice['from_quantity'], false, true, false, true, true, $specificPriceOutput, true);
				
				if (!isset($maxPrice[$currency['id_currency']]))
					$maxPrice[$currency['id_currency']] = 0;
				if (!isset($minPrice[$currency['id_currency']]))
					$minPrice[$currency['id_currency']] = null;
				if ($price > $maxPrice[$currency['id_currency']])
					$maxPrice[$currency['id_currency']] = $price;
				if ($price == 0)
					continue;
				if (is_null($minPrice[$currency['id_currency']]) || $price < $minPrice[$currency['id_currency']])
					$minPrice[$currency['id_currency']] = $price;
			}
		
		foreach ($groups as $group)
			foreach ($currencyList as $currency)
			{
				$price = Product::priceCalculation(null, (int)$idProduct, null, null, null, null, (int)$currency['id_currency'], (int)$group['id_group'],
					null, false, true, false, true, true, $specificPriceOutput, true);
				
				if (!isset($maxPrice[$currency['id_currency']]))
					$maxPrice[$currency['id_currency']] = 0;
				if (!isset($minPrice[$currency['id_currency']]))
					$minPrice[$currency['id_currency']] = null;
				if ($price > $maxPrice[$currency['id_currency']])
					$maxPrice[$currency['id_currency']] = $price;
				if ($price == 0)
					continue;
				if (is_null($minPrice[$currency['id_currency']]) || $price < $minPrice[$currency['id_currency']])
					$minPrice[$currency['id_currency']] = $price;
			}
		
		$values = array();
		foreach ($currencyList as $currency)
			$values[] = '('.(int)$idProduct.', '.(int)$currency['id_currency'].',
			'.(int)$minPrice[$currency['id_currency']].', '.(int)($maxPrice[$currency['id_currency']] * (100 + $maxTaxRate) / 100).')';
		
		Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'layered_price_index` (id_product, id_currency, price_min, price_max)
			VALUES '.implode(',', $values).'
			ON DUPLICATE KEY UPDATE id_product = id_product # avoid duplicate keys');
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
		if ($filterBlock = $this->getFilterBlock($this->getSelectedFilters()))
			if ($filterBlock['nbr_filterBlocks'] == 0)
				return false;
		
		if (Tools::getValue('id_category', Tools::getValue('id_category_layered', 1)) == 1)
			return;
		
		$idLang = (int)$cookie->id_lang;
		$category = new Category((int)Tools::getValue('id_category'));

		// Generate meta title and meta description
		$title = '';
		if (is_array($filterBlock['title_values']))
			foreach ($filterBlock['title_values'] as $key => $val)
				$title .= $key.' '.implode('/', $val).' – ';
		$title = rtrim($title, ' – ');
		$categoryMetas = Tools::getMetaTags($idLang, '', $title);
		$categoryTitle = (empty($category->meta_title[$idLang]) ? $category->name[$idLang] : $category->meta_title[$idLang]);
		
		if (!empty($title))
		{
			$smarty->assign('meta_title', $categoryTitle.$categoryMetas['meta_title']); 
			$smarty->assign('meta_description', $categoryTitle.$categoryMetas['meta_description']);
		}
		else
			$smarty->assign('meta_title', $categoryMetas['meta_title']);
		
		$metaKeyWordsComplement = substr(str_replace(' – ', ', ', strtolower($title)), 1000);
		if (!empty($metaKeyWordsComplement))
			$smarty->assign('meta_keywords', rtrim($categoryTitle.', '.$metaKeyWordsComplement.', '.$categoryMetas['meta_keywords'], ', '));
		
		Tools::addJS(($this->_path).'blocklayered.js');
		Tools::addJS(_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js');
		Tools::addCSS(_PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css', 'all');
		Tools::addCSS(($this->_path).'blocklayered.css', 'all');
		Tools::addJS(_PS_JS_DIR_.'jquery/jquery.scrollTo-1.4.2-min.js');
	}
	
	public function hookFooter($params)
	{
		if (basename($_SERVER['PHP_SELF']) == 'category.php')
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
		if (!$params['category']->active)
			$this->hookCategoryDeletion($params);
	}

	public function hookCategoryDeletion($params)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_category WHERE id_category = '.(int)$params['category']->id);
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
					Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)Tools::getValue('id_layered_filter'));
				
				if (Tools::getValue('scope') == 1)
				{
					Db::getInstance()->Execute('TRUNCATE TABLE '._DB_PREFIX_.'layered_filter');
					$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_category FROM '._DB_PREFIX_.'category');
					foreach ($categories as $category)
						$_POST['categoryBox'][] = (int)$category['id_category'];
				}
				
				if (count($_POST['categoryBox']))
				{
					/* Clean categoryBox before use */
					if (isset($_POST['categoryBox']) && is_array($_POST['categoryBox']))
						foreach ($_POST['categoryBox'] as &$categoryBoxTmp)
							$categoryBoxTmp = (int)$categoryBoxTmp;
					
					Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_category WHERE id_category IN ('.implode(',', array_map('intval', $_POST['categoryBox'])).')');
	
					$filterValues = array();
					foreach ($_POST['categoryBox'] as $idc)
						$filterValues['categories'][] = (int)$idc;
	
					$sqlToInsert = 'INSERT INTO '._DB_PREFIX_.'layered_category (id_category, id_value, type, position) VALUES ';
					foreach ($_POST['categoryBox'] as $id_category_layered)
					{
						$n = 0;
						foreach ($_POST as $key => $value)
							if (substr($key, 0, 17) == 'layered_selection' && $value == 'on')
							{
								$filterValues[$key] = $value;
								$n++;
								if ($key == 'layered_selection_stock')
									$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'quantity\','.(int)$n.'),';
								else if ($key == 'layered_selection_subcategories')
									$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'category\','.(int)$n.'),';
								else if ($key == 'layered_selection_condition')
									$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'condition\','.(int)$n.'),';
								else if ($key == 'layered_selection_weight_slider')
									$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'weight\','.(int)$n.'),';
								else if ($key == 'layered_selection_price_slider')
									$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'price\','.(int)$n.'),';
								else if ($key == 'layered_selection_manufacturer')
									$sqlToInsert .= '('.(int)$id_category_layered.',NULL,\'manufacturer\','.(int)$n.'),';
								else if (substr($key, 0, 21) == 'layered_selection_ag_')
									$sqlToInsert .= '('.(int)$id_category_layered.','.(int)str_replace('layered_selection_ag_', '', $key).',\'id_attribute_group\','.(int)$n.'),';
								else if (substr($key, 0, 23) == 'layered_selection_feat_')
									$sqlToInsert .= '('.(int)$id_category_layered.','.(int)str_replace('layered_selection_feat_', '', $key).',\'id_feature\','.(int)$n.'),';
							}
					}
					
					Db::getInstance()->Execute(rtrim($sqlToInsert, ','));
					
					$valuesToInsert = array(
						'name' => pSQL(Tools::getValue('layered_tpl_name')),
						'filters' => pSQL(serialize($filterValues)),
						'n_categories' => (int)count($filterValues['categories']),
						'date_add' => date('Y-m-d H:i:s'));
					if (isset($_POST['id_layered_filter']) && $_POST['id_layered_filter'])
						$valuesToInsert['id_layered_filter'] = (int)Tools::getValue('id_layered_filter');
					
					Db::getInstance()->AutoExecute(_DB_PREFIX_.'layered_filter', $valuesToInsert, 'INSERT');
					
					echo '<div class="conf"><img src="../img/admin/ok2.png" alt="" />
					'.$this->l('Your filter').' "'.Tools::safeOutput(Tools::getValue('layered_tpl_name')).'" '.
					((isset($_POST['id_layered_filter']) && $_POST['id_layered_filter']) ? $this->l('was updated successfully.') : $this->l('was added successfully.')).'</div>';
				}
			}
		}
		else if (Tools::isSubmit('submitLayeredSettings'))
		{
			Configuration::updateValue('PS_LAYERED_HIDE_0_VALUES', Tools::getValue('ps_layered_hide_0_values'));
			Configuration::updateValue('PS_LAYERED_SHOW_QTIES', Tools::getValue('ps_layered_show_qties'));
			
			$html .= '
			<div class="conf">
				<img src="../img/admin/ok2.png" alt="" /> '.$this->l('Settings saved successfully').'
			</div>';
		}
		else if (isset($_GET['deleteFilterTemplate']))
		{
			$layeredValues = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT filters 
			FROM '._DB_PREFIX_.'layered_filter 
			WHERE id_layered_filter = '.(int)$_GET['id_layered_filter']);
			
			if ($layeredValues)
			{
				Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)$_GET['id_layered_filter'].' LIMIT 1');
				
				$html .= '
				<div class="conf">
					<img src="../img/admin/ok2.png" alt="" /> '.$this->l('Filters template deleted, categories updated (reverted to default Filters template).').'
				</div>';
			}
			else
			{
				$html .= '
				<div class="error">
					<img src="../img/admin/error.png" alt="" title="" /> '.$this->l('Filters template not found').'
				</div>';
			}
		}
		
		$html .= '
		<div id="ajax-message-ok" class="conf ajax-message" style="display: none">
			<img alt="" src="../img/admin/ok2.png"><span class="message"></span>
		</div>
		<div id="ajax-message-ko" class="error ajax-message" style="display: none">
			<img src="../img/admin/error2.png" alt="" /><span class="message"></span>
		</div>
		<h2>'.$this->l('Layered navigation').'</h2>
		<fieldset class="width4">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Indexes and caches').'</legend>
			<span id="indexing-warning" style="display: none; color:red; font-weight: bold">'.$this->l('Indexing are in progress. Please don\'t leave this page').'<br/><br/></span>';

		if (!Configuration::get('PS_LAYERED_INDEXED'))
			$html .= '
			<script type="text/javascript">
			$(document).ready(function() {
				$(\'#url-indexer\').click();
				$(\'#full-index\').click();
			});
			</script>';
		
		$categoryList = array();
		foreach (Db::getInstance()->ExecuteS('SELECT id_category FROM `'._DB_PREFIX_.'category`') as $category)
			if ($category['id_category'] != 1)
				$categoryList[] = $category['id_category'];
		
		$html .= '
			<a class="bold ajaxcall-recurcive"
			style="width: 250px; text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px"
			href="'.Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-price-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'">'.
			$this->l('Index all missing prices').'</a>
			<br />
			<a class="bold ajaxcall-recurcive"
			style="width: 250px; text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="full-index"
			href="'.Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-price-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&full=1">'.
			$this->l('Re-build entire price index').'</a>
			<br />
			<a class="bold ajaxcall" id="attribute-indexer"
			style="width: 250px; text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="full-index"
			href="'.Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-attribute-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'">'.
			$this->l('Build attribute index').'</a>
			<br />
			<a class="bold ajaxcall" id="url-indexer"
			style="width: 250px; text-align:center;display:block;border:1px solid #aaa;text-decoration:none;background-color:#fafafa;color:#123456;margin:2px;padding:2px" id="full-index"
			href="'.Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-url-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&truncate=1">'.
			$this->l('Build url index').'</a>
			<br />
			<br />
			'.$this->l('You can set a cron job that will re-build price index using the following URL:').'<br /><b>'.
			Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-price-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&full=1</b>
			<br />
			'.$this->l('You can set a cron job that will re-build url index using the following URL:').'<br /><b>'.
			Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-url-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&truncate=1</b>
			<br />
			'.$this->l('You can set a cron job that will re-build attribute index using the following URL:').'<br /><b>'.
			Tools::getCurrentUrlProtocolPrefix().Tools::getHttpHost().__PS_BASE_URI__.'modules/blocklayered/blocklayered-attribute-indexer.php'.'?token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'</b>
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
							$(\'#ajax-message-ok span\').html(\''.addslashes($this->l('Url indexation finished')).'\');
							$(\'#ajax-message-ok\').show();
							return;
						},
						error: function(res)
						{
							this.restartAllowed = true;
							$(\'#indexing-warning\').hide();
							$(\'#ajax-message-ko span\').html(\''.addslashes($this->l('Url indexation failed')).'\');
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
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Existing filters templates').'</legend>';
	
		$filtersTemplates = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM '._DB_PREFIX_.'layered_filter ORDER BY date_add DESC');
		if (count($filtersTemplates))
		{
			$html .= '<p>'.count($filtersTemplates).' '.$this->l('filters templates are configured:').'</p>
			<table id="table-filter-templates" class="table" style="width: 700px;">
				<tr>
					<th>'.$this->l('ID').'</th>
					<th>'.$this->l('Name').'</th>
					<th>'.$this->l('Categories').'</th>
					<th>'.$this->l('Created on').'</th>
					<th>'.$this->l('Actions').'</th>
				</tr>';
				
			foreach ($filtersTemplates as $filtersTemplate)
			{
				/* Clean request URI first */
				$_SERVER['REQUEST_URI'] = preg_replace('/&deleteFilterTemplate=[0-9]*&id_layered_filter=[0-9]*/', '', $_SERVER['REQUEST_URI']);
				
				$html .= '
				<tr>
					<td>'.(int)$filtersTemplate['id_layered_filter'].'</td>
					<td style="text-align: left; padding-left: 10px; width: 270px;">'.$filtersTemplate['name'].'</td>
					<td style="text-align: center;">'.(int)$filtersTemplate['n_categories'].'</td>
					<td>'.Tools::displayDate($filtersTemplate['date_add'], (int)$cookie->id_lang, true).'</td>
					<td>
						<a href="#" onclick="updElements('.($filtersTemplate['n_categories'] ? 0 : 1).', '.(int)$filtersTemplate['id_layered_filter'].');">
						<img src="../img/admin/edit.gif" alt="" title="'.$this->l('Edit').'" /></a> 
						<a href="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'&deleteFilterTemplate=1&id_layered_filter='.(int)$filtersTemplate['id_layered_filter'].'"
						onclick="return confirm(\''.addslashes($this->l('Delete filter template #')).(int)$filtersTemplate['id_layered_filter'].$this->l('?').'\');">
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
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Build your own filters template').'</legend>
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
				#layered_container_right ul li { padding-left: 8px; }
				#layered_container_left ul li { cursor: move; }
				#layered-cat-counter { display: none; }
				#layered-step-2, #layered-step-3 { display: none; }
				#table-filter-templates tr th, #table-filter-templates tr td { text-align: center; }
			</style>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post" onsubmit="return checkForm();">';
			
		$html .= '
			<h2>'.$this->l('Step 1/3 - Select categories').'</h2>
			<p style="margin-top: 20px;">'.$this->l('Use this template for:').'
				<input type="radio" id="scope_1" name="scope" value="1" style="margin-left: 15px;" onclick="$(\'#error-treeview\').hide(); $(\'#layered-step-2\').show(); updElements(1, 0);" /> 
				<label for="scope_1" style="float: none;">'.$this->l('All categories').'</label>
				<input type="radio" id="scope_2" name="scope" value="2" style="margin-left: 15px;" onclick="$(\'label a#inline\').click(); $(\'#layered-step-2\').show();" /> 
				<label for="scope_2" style="float: none;"><a id="inline" href="#layered-categories-selection" style="text-decoration: underline;">'.$this->l('Specific').'</a>
				'.$this->l('categories').' (<span id="layered-cat-counter"></span> '.$this->l('selected').')</label>
			</p>
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

			$trads = array();
			$selectedCat = array();
			foreach (Helper::$translationsKeysForAdminCategorieTree as $key)
				$trads[$key] = $this->l($key);
			$html .= Helper::renderAdminCategorieTree($trads, $selectedCat, 'categoryBox');
			
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
				<hr size="1" noshade />
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery-ui-1.8.10.custom.min.js"></script>
				<script type="text/javascript" src="'.__PS_BASE_URI__.'js/jquery/jquery.fancybox-1.3.4.js"></script>
				<link type="text/css" rel="stylesheet" href="'.__PS_BASE_URI__.'css/jquery.fancybox-1.3.4.css" />
				<script type="text/javascript">
					
					function updLayCounters()
					{
						$(\'#num_sel_filters\').html(\'(\'+$(\'ul#selected_filters\').find(\'li\').length+\')\');
						$(\'#num_avail_filters\').html(\'(\'+$(\'#layered_container_right ul\').find(\'li\').length+\')\');
						
						if ($(\'ul#selected_filters\').find(\'li\').length >= 1)
							$(\'#layered-step-3\').show();
						else
							$(\'#layered-step-3\').hide();
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
							type: \'GET\',
							url: \''.__PS_BASE_URI__.'\' + \'modules/blocklayered/blocklayered-ajax-back.php\',
							data: \'layered_token='.substr(Tools::encrypt('blocklayered/index'), 0, 10).'&\'
								+(all ? \'\' : $(\'input[name="categoryBox[]"]\').serialize()+\'&\')
								+(id_layered_filter ? \'id_layered_filter=\'+parseInt(id_layered_filter) : \'\'),
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
								updLayCounters();
							}
						});
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
									updLayCounters();
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
									updLayCounters();
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
								updCatCounter();
								if ($(\'#categories-treeview\').find(\'input:checked\').length == 0)
									$(\'#error-treeview\').show(500);
								else
									$(\'#error-treeview\').hide(500);
								updElements(0, 0);
							},
							\'onComplete\': function() {
								if($(\'#categories-treeview li#1\').attr(\'cleaned\'))
									return;
								if($(\'#categories-treeview li#1\').attr(\'cleaned\', true))
								$(\'#categories-treeview li#1\').removeClass(\'static\');
								$(\'#categories-treeview li#1 span\').trigger(\'click\');
								$(\'#categories-treeview li#1\').children(\'div\').remove();
								$(\'#categories-treeview li#1\').
									removeClass(\'collapsable lastCollapsable\').
									addClass(\'last static\');
								$(\'.hitarea\').click(function(it)
								{
									$(this).parent().find(\'> .category_label\').click();
								});
							}
						});

						updHeight();
						updLayCounters();
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
				{ $(\'#error-filter-name\').hide(); } else { $(\'#error-filter-name\').show(); }" name="layered_tpl_name" maxlength="64" value="'.$this->l('My template').' '.date('Y-m-d').'"
				style="width: 200px; font-size: 11px;" /> <span style="font-size: 10px; font-style: italic;">('.$this->l('only as a reminder').')</span></p>
				<hr size="1" noshade />
				<br />
				<center><input type="submit" class="button" name="SubmitFilter" value="'.$this->l('Save this filter template').'" /></center>
			</div>
				<input type="hidden" name="id_layered_filter" id="id_layered_filter" value="0" />
				<input type="hidden" name="n_existing" id="n_existing" value="'.(int)count($filtersTemplates).'" />
			</form>
		</fieldset><br />
		<fieldset class="width2">
			<legend><img src="../img/admin/cog.gif" alt="" /> '.$this->l('Configuration').'</legend>
			<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">			
				<table border="0" style="font-size: 11px; width: 100%; margin: 0 auto;" class="table">
					<tr>
						<th style="text-align: center;">'.$this->l('Option').'</th>
						<th style="text-align: center;">'.$this->l('Value').'</th>
					</tr>
					<tr>
						<td style="text-align: right;">'.$this->l('Hide filter values with no product is matching').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_hide_0_values" value="1" '.(Configuration::get('PS_LAYERED_HIDE_0_VALUES') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_hide_0_values" value="0" '.(!Configuration::get('PS_LAYERED_HIDE_0_VALUES') ? 'checked="checked"' : '').' />
						</td>
					</tr>
					<tr>
						<td style="text-align: right;">'.$this->l('Show the number of matching products').'</td>
						<td>
							<img src="../img/admin/enabled.gif" alt="'.$this->l('Yes').'" title="'.$this->l('Yes').'" />
							'.$this->l('Yes').' <input type="radio" name="ps_layered_show_qties" value="1" '.(Configuration::get('PS_LAYERED_SHOW_QTIES') ? 'checked="checked"' : '').' />
							<img src="../img/admin/disabled.gif" alt="'.$this->l('No').'" title="'.$this->l('No').'" style="margin-left: 10px;" />
							'.$this->l('No').' <input type="radio" name="ps_layered_show_qties" value="0" '.(!Configuration::get('PS_LAYERED_SHOW_QTIES') ? 'checked="checked"' : '').' />
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
			
			$urlAttributes = explode('/', $url);
			array_shift($urlAttributes);
			$selectedFilters = array('category' => array());
			if (!empty($urlAttributes))
			{
				foreach ($urlAttributes as $urlAttribute)
				{
					$urlParameters = explode('-', $urlAttribute);
					$attributeName  = array_shift($urlParameters);
					if (in_array($attributeName, array('price', 'weight')))
						$selectedFilters[$attributeName] = array($urlParameters[0], $urlParameters[1]);
					else
					{
						foreach ($urlParameters as $urlParameter)
						{
							$data = Db::getInstance()->getValue('SELECT data FROM `'._DB_PREFIX_.'layered_friendly_url` WHERE `url_key` = \''.md5('/'.$attributeName.'-'.$urlParameter).'\'');
							if ($data)
								foreach (unserialize($data) as $keyParams => $params)
								{
									if (!isset($selectedFilters[$keyParams]))
										$selectedFilters[$keyParams] = array();
									foreach ($params as $keyParam => $param)
									{
										if (!isset($selectedFilters[$keyParams][$keyParam]))
											$selectedFilters[$keyParams][$keyParam] = array();
										$selectedFilters[$keyParams][$keyParam] = $param;
									}
								}
						}
					}
				}
				return $selectedFilters;
			}
		}

		/* Analyze all the filters selected by the user and store them into a tab */
		$selectedFilters = array('category' => array(), 'manufacturer' => array(), 'quantity' => array(), 'condition' => array());
		foreach ($_GET as $key => $value)
			if (substr($key, 0, 8) == 'layered_')
			{
				preg_match('/^(.*)_[0-9|new|used|refurbished|slider]+$/', substr($key, 8, strlen($key) - 8), $res);
				if (isset($res[1]))
				{
					$tmpTab = explode('_', $value);
					$value = $tmpTab[0];
					$id_key = false;
					if (isset($tmpTab[1]))
						$id_key = $tmpTab[1];
					if ($res[1] == 'condition' && in_array($value, array('new', 'used', 'refurbished')))
						$selectedFilters['condition'][] = $value;
					else if ($res[1] == 'quantity' && (!$value || $value == 1))
						$selectedFilters['quantity'][] = $value;
					else if (in_array($res[1], array('category', 'manufacturer')))
					{
						if (!isset($selectedFilters[$res[1].($id_key ? '_'.$id_key : '')]))
							$selectedFilters[$res[1].($id_key ? '_'.$id_key : '')] = array();
						$selectedFilters[$res[1].($id_key ? '_'.$id_key : '')][] = (int)$value;
					}
					else if (in_array($res[1], array('id_attribute_group', 'id_feature')))
					{
						if (!isset($selectedFilters[$res[1]]))
							$selectedFilters[$res[1]] = array();
						$selectedFilters[$res[1]][(int)$value] = $id_key.'_'.(int)$value;
					}
					else if ($res[1] == 'weight')
						$selectedFilters[$res[1]] = $tmpTab;
					else if ($res[1] == 'price')
						$selectedFilters[$res[1]] = $tmpTab;
				}
			}
		return $selectedFilters;
	}

	public function getProductByFilters($selectedFilters = array())
	{
		global $cookie;

		if (!empty($this->products))
			return $this->products;

		/* If the current category isn't defined or if it's homepage, we have nothing to display */
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return false;

		$queryFiltersWhere = ' AND p.active = 1';
		$queryFiltersFrom = '';
		
		$parent = new Category((int)$id_parent);
		if (!count($selectedFilters['category']))
			$queryFiltersFrom .= ' INNER JOIN '._DB_PREFIX_.'category_product cp
			ON p.id_product = cp.id_product
			INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category
			AND c.nleft >= '.(int)$parent->nleft.' AND c.nright <= '.(int)$parent->nright.')';

		foreach ($selectedFilters as $key => $filterValues)
		{
			if (!count($filterValues))
				continue;

			preg_match('/^(.*[^_0-9])/', $key, $res);
			$key = $res[1];

			switch ($key)
			{
				case 'id_feature':
					$subQueries = array();
					foreach ($filterValues as $filterValue)
					{
						$filterValueArray = explode('_', $filterValue);
						if (!isset($subQueries[$filterValueArray[0]]))
							$subQueries[$filterValueArray[0]] = array();
						$subQueries[$filterValueArray[0]][] = 'fp.`id_feature_value` = '.(int)$filterValueArray[1];
					}
					foreach ($subQueries as $subQuery)
					{
						$queryFiltersWhere .= ' AND p.id_product IN (SELECT `id_product` FROM `'._DB_PREFIX_.'feature_product` fp WHERE ';
						$queryFiltersWhere .= implode(' OR ', $subQuery).') ';
					}
				break;

				case 'id_attribute_group':
					$subQueries = array();
					
					
					foreach ($filterValues as $filterValue)
					{
						$filterValueArray = explode('_', $filterValue);
						if (!isset($subQueries[$filterValueArray[0]]))
							$subQueries[$filterValueArray[0]] = array();
						$subQueries[$filterValueArray[0]][] = 'pac.`id_attribute` = '.(int)$filterValueArray[1];
					}
					foreach ($subQueries as $subQuery)
					{
						$queryFiltersWhere .= ' AND p.id_product IN (SELECT pa.`id_product`
						FROM `'._DB_PREFIX_.'product_attribute_combination` pac
						LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
						ON (pa.`id_product_attribute` = pac.`id_product_attribute`) WHERE ';
						$queryFiltersWhere .= implode(' OR ', $subQuery).') ';
					}
				break;

				case 'category':
					$queryFiltersWhere .= ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'category_product cp WHERE ';
					foreach ($selectedFilters['category'] as $id_category)
						$queryFiltersWhere .= 'cp.`id_category` = '.(int)$id_category.' OR ';
					$queryFiltersWhere = rtrim($queryFiltersWhere, 'OR ').')';
				break;

				case 'quantity':
					if (count($selectedFilters['quantity']) == 2)
						break;
					$queryFiltersWhere .= ' AND p.quantity '.(!$selectedFilters['quantity'][0] ? '=' : '>').' 0';
				break;

				case 'manufacturer':
					$queryFiltersWhere .= ' AND p.id_manufacturer IN ('.implode($selectedFilters['manufacturer'], ',').')';
				break;

				case 'condition':
					if (count($selectedFilters['condition']) == 3)
						break;
					$queryFiltersWhere .= ' AND p.condition IN (';
					foreach ($selectedFilters['condition'] as $cond)
						$queryFiltersWhere .= '\''.$cond.'\',';
					$queryFiltersWhere = rtrim($queryFiltersWhere, ',').')';
				break;

				case 'weight':
					if ($selectedFilters['weight'][0] != 0 || $selectedFilters['weight'][1] != 0)
						$queryFiltersWhere .= ' AND p.`weight` BETWEEN '.(float)($selectedFilters['weight'][0] - 0.001).' AND '.(float)($selectedFilters['weight'][1] + 0.001);

				case 'price':
					if (isset($selectedFilters['price']))
					{
						if ($selectedFilters['price'][0] != 0 || $selectedFilters['price'][1] != 0)
						{
							$priceFilter = array();
							$priceFilter['min'] = (float)($selectedFilters['price'][0]);
							$priceFilter['max'] = (float)($selectedFilters['price'][1]);
						}
					}
					else
						$priceFilter = false;
				break;
			}
		}
		
		$idCurrency = Currency::getCurrent()->id;
		$priceFilterQueryIn = ''; // All products with price range between price filters limits
		$priceFilterQueryOut = ''; // All products with a price filters limit on it price range
		if (isset($priceFilter) && $priceFilter)
		{
			$priceFilterQueryIn = 'INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi
			ON psi.price_min >= '.(int)$priceFilter['min'].'
				AND psi.price_max <= '.(int)$priceFilter['max'].'
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_currency` = '.(int)$idCurrency;
			
			$priceFilterQueryOut = 'INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi
			ON 
				((psi.price_min <= '.(int)$priceFilter['min'].' AND psi.price_max >= '.(int)$priceFilter['min'].')
				OR
				(psi.price_max >= '.(int)$priceFilter['max'].' AND psi.price_min <= '.(int)$priceFilter['max'].'))
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_currency` = '.(int)$idCurrency;
		}
		
		$allProductsOut = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product` id_product
		FROM `'._DB_PREFIX_.'product` p
		'.$priceFilterQueryOut.'
		'.$queryFiltersFrom.'
		WHERE 1 '.$queryFiltersWhere.' GROUP BY id_product', false);
		
		$allProductsIn = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product` id_product
		FROM `'._DB_PREFIX_.'product` p
		'.$priceFilterQueryIn.'
		'.$queryFiltersFrom.'
		WHERE 1 '.$queryFiltersWhere.' GROUP BY id_product', false);

		$productIdList = array();
		
		while ($product = DB::getInstance()->nextRow($allProductsIn))
			$productIdList[] = (int)$product['id_product'];

		while ($product = DB::getInstance()->nextRow($allProductsOut))
			if (isset($priceFilter) && $priceFilter)
			{
				$price = (int)Product::getPriceStatic($product['id_product']); // Cast to int because we don't care about cents
				if ($price < $priceFilter['min'] || $price > $priceFilter['max'])
					continue;
				$productIdList[] = (int)$product['id_product'];
			}
		$this->nbr_products = count($productIdList);
		
		if ($this->nbr_products == 0)
			$this->products = array();
		else
		{
			$n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
			$this->products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT p.id_product, p.on_sale, p.out_of_stock, p.available_for_order, p.quantity, p.minimal_quantity, p.id_category_default, p.customizable, p.show_price, p.`weight`,
			p.ean13, pl.available_later, pl.description_short, pl.link_rewrite, pl.name, i.id_image, il.legend,  m.name manufacturer_name, p.condition, p.id_manufacturer,
			DATEDIFF(p.`date_add`,
			DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new
			FROM `'._DB_PREFIX_.'category_product` cp
			LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
			LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
			LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product)
			LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)
			LEFT JOIN '._DB_PREFIX_.'image_lang il ON (i.id_image = il.id_image AND il.id_lang = '.(int)($cookie->id_lang).')
			LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
			WHERE p.`active` = 1 AND c.nleft >= '.(int)$parent->nleft.' AND c.nright <= '.(int)$parent->nright.' AND pl.id_lang = '.(int)$cookie->id_lang.'
			AND p.id_product IN ('.implode(',', $productIdList).')'
			.' GROUP BY p.id_product ORDER BY '.Tools::getProductsOrder('by', Tools::getValue('orderby'), true).' '.Tools::getProductsOrder('way', Tools::getValue('orderway')).
			' LIMIT '.(((int)Tools::getValue('p', 1) - 1) * $n.','.$n));
		}
		return $this->products;
	}
	
	public function getFilterBlock($selectedFilters = array())
	{
		global $cookie;
		static $cache = null;
		
		if (is_array($cache))
			return $cache;
		
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;
		
		$parent = new Category((int)$id_parent);
		
		/* Get the filters for the current category */
		$filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM '._DB_PREFIX_.'layered_category WHERE id_category = '.(int)$id_parent.'
		GROUP BY `type`, id_value ORDER BY position ASC');
		// Remove all empty selected filters
		foreach ($selectedFilters as $key => $value)
			switch ($key)
			{
				case 'price':
				case 'weight':
					if ($value[0] == '' && $value[1] == '' || $value[0] == 0 && $value[1] == 0)
						unset($selectedFilters[$key]);
					break;
				default:
					if ($value == '')
						unset($selectedFilters[$key]);
					break;
			}
		
		$filterBlocks = array();
		foreach ($filters as $filter)
		{
			$sqlQuery = array('select' => '', 'from' => '', 'join' => '', 'where' => '', 'group' => '');
			switch ($filter['type'])
			{
				// conditions + quantities + weight + price
				case 'price':
				case 'weight':
				case 'condition':
				case 'quantity':
					$sqlQuery['select'] = '
					SELECT p.`id_product`, p.`condition`, p.`id_manufacturer`, p.`quantity`, p.`weight`
					';
					$sqlQuery['from'] = '
					FROM '._DB_PREFIX_.'product p ';
					$sqlQuery['join'] = '
					INNER JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
					INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND c.nleft >= '.(int)$parent->nleft.' AND c.nright <= '.(int)$parent->nright.') ';
					$sqlQuery['where'] = 'WHERE p.`active` = 1 ';
					$sqlQuery['group'] = ' GROUP BY p.id_product ';
					break;

				case 'manufacturer':
					$sqlQuery['select'] = 'SELECT m.name, COUNT(DISTINCT p.id_product) nbr, m.id_manufacturer ';
					$sqlQuery['from'] = '
					FROM `'._DB_PREFIX_.'category_product` cp
					INNER JOIN  `'._DB_PREFIX_.'category` c ON (c.id_category = cp.id_category)
					INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product AND p.active = 1)
					INNER JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer) ';
					$sqlQuery['where'] = '
					WHERE c.nleft >= '.(int)$parent->nleft.' AND c.nright <= '.(int)$parent->nright.' ';
					$sqlQuery['group'] = ' GROUP BY p.id_manufacturer ';
					break;

				case 'id_attribute_group':// attribute group
					$sqlQuery['select'] = '
					SELECT COUNT(DISTINCT p.id_product) nbr, lpa.id_attribute_group,
					a.color, al.name attribute_name, agl.public_name attribute_group_name , lpa.id_attribute, ag.is_color_group,
					liagl.url_name name_url_name, liagl.meta_title name_meta_title, lial.url_name value_url_name, lial.meta_title value_meta_title';
					$sqlQuery['from'] = '
					FROM '._DB_PREFIX_.'layered_product_attribute lpa
					INNER JOIN '._DB_PREFIX_.'attribute a
					ON a.id_attribute = lpa.id_attribute
					INNER JOIN '._DB_PREFIX_.'attribute_lang al
					ON al.id_attribute = a.id_attribute
					AND al.id_lang = '.(int)$cookie->id_lang.'
					INNER JOIN '._DB_PREFIX_.'product as p
					ON p.id_product = lpa.id_product
					AND p.active = 1
					INNER JOIN '._DB_PREFIX_.'attribute_group ag
					ON ag.id_attribute_group = lpa.id_attribute_group
					INNER JOIN '._DB_PREFIX_.'attribute_group_lang agl
					ON agl.id_attribute_group = lpa.id_attribute_group
					AND agl.id_lang = '.(int)$cookie->id_lang.'
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_group_lang_value liagl
					ON (liagl.id_attribute_group = lpa.id_attribute_group AND liagl.id_lang = '.(int)$cookie->id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_attribute_lang_value lial
					ON (lial.id_attribute = lpa.id_attribute AND lial.id_lang = '.(int)$cookie->id_lang.') ';
					$sqlQuery['where'] = 'WHERE a.id_attribute_group = '.(int)$filter['id_value'].'
					AND p.id_product IN (
					SELECT id_product
					FROM '._DB_PREFIX_.'category_product cp
					INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND c.nleft >= '.(int)$parent->nleft.' AND c.nright <= '.(int)$parent->nright.')) ';
					$sqlQuery['group'] = '
					GROUP BY lpa.id_attribute
					ORDER BY id_attribute_group, id_attribute ';
					
					break;

				case 'id_feature':
					$sqlQuery['select'] = 'SELECT fl.name feature_name, fp.id_feature, fv.id_feature_value, fvl.value,
					COUNT(DISTINCT p.id_product) nbr,
					lifl.url_name name_url_name, lifl.meta_title name_meta_title, lifvl.url_name value_url_name, lifvl.meta_title value_meta_title ';
					$sqlQuery['from'] = '
					FROM '._DB_PREFIX_.'feature_product fp
					INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product AND p.active = 1)
					LEFT JOIN '._DB_PREFIX_.'feature_lang fl ON (fl.id_feature = fp.id_feature AND fl.id_lang = '.(int)$cookie->id_lang.')
					INNER JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
					LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = '.(int)$cookie->id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_lang_value lifl
					ON (lifl.id_feature = fp.id_feature AND lifl.id_lang = '.(int)$cookie->id_lang.')
					LEFT JOIN '._DB_PREFIX_.'layered_indexable_feature_value_lang_value lifvl
					ON (lifvl.id_feature_value = fp.id_feature_value AND lifvl.id_lang = '.(int)$cookie->id_lang.') ';
					$sqlQuery['where'] = 'WHERE p.`active` = 1 AND fp.id_feature = '.(int)$filter['id_value'].'
					AND p.id_product IN (
					SELECT id_product
					FROM '._DB_PREFIX_.'category_product cp
					INNER JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category AND c.nleft >= '.(int)$parent->nleft.' AND c.nright <= '.(int)$parent->nright.')) ';
					$sqlQuery['group'] = 'GROUP BY fv.id_feature_value ';
					break;

				case 'category':
					$sqlQuery['select'] = '
					SELECT c.id_category, c.id_parent, cl.name, (SELECT count(DISTINCT p.id_product) # ';
					$sqlQuery['from'] = '
					FROM '._DB_PREFIX_.'category_product cp
					LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product AND p.active = 1) ';
					$sqlQuery['where'] = '
					WHERE cp.id_category = c.id_category ';
					$sqlQuery['group'] = ') count_products
					FROM '._DB_PREFIX_.'category c
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category AND cl.id_lang = '.(int)$cookie->id_lang.')
					WHERE c.id_parent = '.(int)$id_parent.'
					GROUP BY c.id_category ORDER BY level_depth, c.position';
			}
			
			foreach ($filters as $filterTmp)
			{
				$methodName = 'get'.ucfirst($filterTmp['type']).'FilterSubQuery';
				if (method_exists('BlockLayered', $methodName) &&
				(!in_array($filter['type'], array('price', 'weight')) && $filter['type'] != $filterTmp['type'] || $filter['type'] == $filterTmp['type']))
				{
					if ($filter['type'] == $filterTmp['type'] && $filter['id_value'] == $filterTmp['id_value'])
						$subQueryFilter = self::$methodName(array(), true);
					else
					{
						if (!is_null($filterTmp['id_value']))
							$selected_filters_cleaned = $this->cleanFilterByIdValue(@$selectedFilters[$filterTmp['type']], $filterTmp['id_value']);
						else
							$selected_filters_cleaned = @$selectedFilters[$filterTmp['type']];
						$subQueryFilter = self::$methodName($selected_filters_cleaned, $filter['type'] == $filterTmp['type']);
					}
					foreach ($subQueryFilter as $key => $value)
						$sqlQuery[$key] .= $value;
				}
			}
			
			$products = false;
			if (!empty($sqlQuery['from']))
				$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sqlQuery['select']."\n".$sqlQuery['from']."\n".$sqlQuery['join']."\n".$sqlQuery['where']."\n".$sqlQuery['group']);
			
			foreach ($filters as $filterTmp)
			{
				$methodName = 'filterProductsBy'.ucfirst($filterTmp['type']);
				if (method_exists('BlockLayered', $methodName) &&
				(!in_array($filter['type'], array('price', 'weight')) && $filter['type'] != $filterTmp['type'] || $filter['type'] == $filterTmp['type']))
					if ($filter['type'] == $filterTmp['type'])
						$products = self::$methodName(array(), $products);
					else
						$products = self::$methodName(@$selectedFilters[$filterTmp['type']], $products);
			}

			switch ($filter['type'])
			{
				case 'price':
					$priceArray = array('type_lite' => 'price', 'type' => 'price', 'id_key' => 0, 'name' => $this->l('Price'),
					'slider' => true, 'max' => '0', 'min' => null, 'values' => array ('1' => 0), 'unit' => Currency::getCurrent()->sign);
					if (isset($products) && $products)
						foreach ($products as $product)
						{
							if (is_null($priceArray['min']))
							{
								$priceArray['min'] = $product['price_min'];
								$priceArray['values'][0] = $product['price_min'];
							}
							else if ($priceArray['min'] > $product['price_min'])
							{
								$priceArray['min'] = $product['price_min'];
								$priceArray['values'][0] = $product['price_min'];
							}
	
							if ($priceArray['max'] < $product['price_max'])
							{
								$priceArray['max'] = $product['price_max'];
								$priceArray['values'][1] = $product['price_max'];
							}
						}
						
					if ($priceArray['max'] != $priceArray['min'] && $priceArray['min'] != null)
					{
						if (isset($selectedFilters['price']) && isset($selectedFilters['price'][0])
						&& isset($selectedFilters['price'][1]))
						{
							$priceArray['values'][0] = $selectedFilters['price'][0];
							$priceArray['values'][1] = $selectedFilters['price'][1];
						}
						$filterBlocks[] = $priceArray;
					}
					break;

				case 'weight':
					$weightArray = array('type_lite' => 'weight', 'type' => 'weight', 'id_key' => 0, 'name' => $this->l('Weight'), 'slider' => true,
					'max' => '0', 'min' => null, 'values' => array ('1' => 0), 'unit' => Configuration::get('PS_WEIGHT_UNIT'));
					if (isset($products) && $products)
						foreach ($products as $product)
						{
							if (is_null($weightArray['min']))
							{
								$weightArray['min'] = $product['weight'];
								$weightArray['values'][0] = $product['weight'];
							}
							else if ($weightArray['min'] > $product['weight'])
							{
								$weightArray['min'] = $product['weight'];
								$weightArray['values'][0] = $product['weight'];
							}
							
							if ($weightArray['max'] < $product['weight'])
							{
								$weightArray['max'] = $product['weight'];
								$weightArray['values'][1] = $product['weight'];
							}
						}
					if ($weightArray['max'] != $weightArray['min'] && $weightArray['min'] != null)
					{
						if (isset($selectedFilters['weight']) && isset($selectedFilters['weight'][0])
						&& isset($selectedFilters['weight'][1]))
						{
							$weightArray['values'][0] = $selectedFilters['weight'][0];
							$weightArray['values'][1] = $selectedFilters['weight'][1];
						}
						$filterBlocks[] = $weightArray;
					}
					break;

				case 'condition':
					$conditionArray = array('new' => array('name' => $this->l('New'), 'nbr' => 0), 
					'used' => array('name' => $this->l('Used'), 'nbr' => 0), 'refurbished' => array('name' => $this->l('Refurbished'), 'nbr' => 0));
					if (isset($products) && $products)
						foreach ($products as $product)
							if (isset($selectedFilters['condition']) && in_array($product['condition'], $selectedFilters['condition']))
								$conditionArray[$product['condition']]['checked'] = true;
					foreach ($conditionArray as $key => $condition)
						if (isset($selectedFilters['condition']) && in_array($key, $selectedFilters['condition']))
							$conditionArray[$key]['checked'] = true;
					if (isset($products) && $products)
						foreach ($products as $product)
							if (isset($conditionArray[$product['condition']]))
								$conditionArray[$product['condition']]['nbr']++;
						$filterBlocks[] = array('type_lite' => 'condition', 'type' => 'condition', 'id_key' => 0, 'name' => $this->l('Condition'), 'values' => $conditionArray);
					break;
				
				case 'quantity':
					$quantityArray = array (0 => array('name' => $this->l('Not available'), 'nbr' => 0), 1 => array('name' => $this->l('In stock'), 'nbr' => 0));
					foreach ($quantityArray as $key => $quantity)
						if (isset($selectedFilters['quantity']) && in_array($key, $selectedFilters['quantity']))
							$quantityArray[$key]['checked'] = true;
					if (isset($products) && $products)
						foreach ($products as $product)
							$quantityArray[(int)($product['quantity'] > 0)]['nbr']++;
					$filterBlocks[] = array('type_lite' => 'quantity', 'type' => 'quantity', 'id_key' => 0, 'name' => $this->l('Availability'), 'values' => $quantityArray);
					break;

				case 'manufacturer':
					if (isset($products) && $products)
					{
						$manufaturersArray = array();
							foreach ($products as $manufacturer)
							{
								$manufaturersArray[$manufacturer['id_manufacturer']] = array('name' => $manufacturer['name'], 'nbr' => $manufacturer['nbr']);
								if (isset($selectedFilters['manufacturer']) && in_array((int)$manufacturer['id_manufacturer'], $selectedFilters['manufacturer']))
									$manufaturersArray[$manufacturer['id_manufacturer']]['checked'] = true;
							}
						$filterBlocks[] = array('type_lite' => 'manufacturer', 'type' => 'manufacturer', 'id_key' => 0, 'name' => $this->l('Manufacturer'), 'values' => $manufaturersArray);
					}
					break;

				case 'id_attribute_group':
					$attributesArray = array();
					if (isset($products) && $products)
					{
						foreach ($products as $attributes)
						{
							if (!isset($attributesArray[$attributes['id_attribute_group']]))
								$attributesArray[$attributes['id_attribute_group']] = array ('type_lite' => 'id_attribute_group',
								'type' => 'id_attribute_group', 'id_key' => (int)$attributes['id_attribute_group'],
								'name' =>  $attributes['attribute_group_name'], 'is_color_group' => (bool)$attributes['is_color_group'], 'values' => array(),
								'url_name' => $attributes['name_url_name'], 'meta_title' => $attributes['name_meta_title']);
							
							$attributesArray[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']] = array(
							'color' => $attributes['color'], 'name' => $attributes['attribute_name'], 'nbr' => (int)$attributes['nbr'],
							'url_name' => $attributes['value_url_name'], 'meta_title' => $attributes['value_meta_title']);
							if (isset($selectedFilters['id_attribute_group'][$attributes['id_attribute']]))
								$attributesArray[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']]['checked'] = true;
						}
						$filterBlocks = array_merge($filterBlocks, $attributesArray);
					}
					break;
				case 'id_feature':
					$featureArray = array();
					if (isset($products) && $products)
					{
						foreach ($products as $feature)
						{
							if (!isset($featureArray[$feature['id_feature']]))
								$featureArray[$feature['id_feature']] = array('type_lite' => 'id_feature', 'type' => 'id_feature',
								'id_key' => (int)$feature['id_feature'], 'values' => array(), 'name' => $feature['feature_name'],
								'url_name' => $feature['name_url_name'], 'meta_title' => $feature['name_meta_title']);

							$featureArray[$feature['id_feature']]['values'][$feature['id_feature_value']] = array('nbr' => (int)$feature['nbr'], 'name' => $feature['value'],
							'url_name' => $feature['value_url_name'], 'meta_title' => $feature['value_meta_title']);
							if (isset($selectedFilters['id_feature'][$feature['id_feature_value']]))
								$featureArray[$feature['id_feature']]['values'][$feature['id_feature_value']]['checked'] = true;
						}
						$filterBlocks = array_merge($filterBlocks, $featureArray);
					}
					break;

				case 'category':
					$tmpArray = array();
					if (isset($products) && $products)
					{
						foreach ($products as $category)
						{
							$tmpArray[$category['id_category']] = array('name' => $category['name'], 'nbr' => (int)$category['count_products']);
							if (isset($selectedFilters['category']) && in_array($category['id_category'], $selectedFilters['category']))
								$tmpArray[$category['id_category']]['checked'] = true;
						}
						$filterBlocks[] = array ('type_lite' => 'category', 'type' => 'category', 'id_key' => 0, 'name' => $this->l('Categories'), 'values' => $tmpArray);
					}
					break;
				
			}
		}
		
		// All non indexable attribute and feature
		$nonIndexable = array();
		
		// Get all non indexable attribute groups
		foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT public_name
		FROM `'._DB_PREFIX_.'attribute_group_lang` agl
		LEFT JOIN `'._DB_PREFIX_.'layered_indexable_attribute_group` liag
		ON liag.id_attribute_group = agl.id_attribute_group
		WHERE indexable IS NULL OR indexable = 0
		AND id_lang = '.(int)$cookie->id_lang) as $attribute)
			$nonIndexable[] = Tools::link_rewrite($attribute['public_name']);
		
		// Get all non indexable features
		foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT name
		FROM `'._DB_PREFIX_.'feature_lang` fl
		LEFT JOIN  `'._DB_PREFIX_.'layered_indexable_feature` lif
		ON lif.id_feature = fl.id_feature
		WHERE indexable IS NULL OR indexable = 0
		AND id_lang = '.(int)$cookie->id_lang) as $attribute)
			$nonIndexable[] = Tools::link_rewrite($attribute['name']);

		//generate SEO link
		$paramSelected = '';
		$optionCheckedArray = array();
		$paramGroupSelectedArray = array();
		$titleValues = array();
		$link = new Link();
		
		$linkBase = $link->getCategoryLink($id_parent, Category::getLinkRewrite($id_parent, (int)($cookie->id_lang)), (int)($cookie->id_lang));
		$filterBlockList = array();
		
		//get filters checked by group
		foreach ($filterBlocks as $typeFilter)
		{
			$filterName = (!empty($typeFilter['url_name']) ? $typeFilter['url_name'] : $typeFilter['name']);
			
			$paramGroupSelected = '';
			foreach ($typeFilter['values'] as $key => $value)
			{
				if (is_array($value) && array_key_exists('checked', $value ))
				{
					$valueName = !empty($value['url_name']) ? $value['url_name'] : $value['name'];
					$paramGroupSelected .= '-'.str_replace('-', '_', Tools::link_rewrite($valueName));
					$paramGroupSelectedArray[Tools::link_rewrite($filterName)][] = Tools::link_rewrite($valueName);
				
					if (!isset($titleValues[$filterName]))
						$titleValues[$filterName] = array();
					$titleValues[$filterName][] = $valueName;
				}
				else
					$paramGroupSelectedArray[Tools::link_rewrite($filterName)][] = array();
			}
			if (!empty($paramGroupSelected))
			{
				$paramSelected .= '/'.str_replace('-', '_', Tools::link_rewrite($filterName)).$paramGroupSelected;
				$optionCheckedArray[Tools::link_rewrite($filterName)] = $paramGroupSelected;
			}
		}

		$blackList = array('weight','price');
		$nofollow = false;
		foreach ($filterBlocks as &$typeFilter)
		{
			$filterName = (!empty($typeFilter['url_name']) ? $typeFilter['url_name'] : $typeFilter['name']);
			
			if (count($typeFilter) > 0 && !in_array($typeFilter['type'], $blackList))
			{
				foreach ($typeFilter['values'] as $key => $values)
				{
					$nofollow = false;
					$optionCheckedCloneArray = $optionCheckedArray;
					
					//if not filters checked, add parameter
					$valueName = !empty($values['url_name']) ? $values['url_name'] : $values['name'];
					if (!in_array(Tools::link_rewrite($valueName), $paramGroupSelectedArray[Tools::link_rewrite($filterName)]))
					{
						//update parameter filter checked before
						if (array_key_exists(Tools::link_rewrite($filterName), $optionCheckedArray))
						{
							$optionCheckedCloneArray[Tools::link_rewrite($filterName)] = $optionCheckedCloneArray[Tools::link_rewrite($filterName)].'-'.str_replace('-', '_', Tools::link_rewrite($valueName));
							$nofollow = true;
						}
						else
							$optionCheckedCloneArray[Tools::link_rewrite($filterName)] = '-'.str_replace('-', '_', Tools::link_rewrite($valueName));
					}
					else
					{
						// Remove selected parameters
						$optionCheckedCloneArray[Tools::link_rewrite($filterName)] = str_replace('-'.str_replace('-', '_', Tools::link_rewrite($valueName)), '', $optionCheckedCloneArray[Tools::link_rewrite($filterName)]);
						if (empty($optionCheckedCloneArray[Tools::link_rewrite($filterName)]))
							unset($optionCheckedCloneArray[Tools::link_rewrite($filterName)]);
					}
					$parameters = '';
					foreach ($optionCheckedCloneArray as $keyGroup => $valueGroup)
						$parameters .= '/'.str_replace('-', '_', $keyGroup).$valueGroup;
					
					// Check if there is an non indexable attribute or feature in the url
					foreach ($nonIndexable as $value)
						if (strpos($parameters, '/'.$value) !== false)
							$nofollow = true;
					//write link by mode rewriting
					if (!Configuration::get('PS_REWRITING_SETTINGS'))
						$typeFilter['values'][$key]['link'] = $linkBase.'&selected_filters='.$parameters;
					else
						$typeFilter['values'][$key]['link'] = $linkBase.$parameters;
						
					$typeFilter['values'][$key]['rel'] = ($nofollow) ? 'nofollow' : '';
				}
			}
		}
		
		$nFilters = 0;
		if (isset($selectedFilters['price']))
			if ($priceArray['min'] == $selectedFilters['price'][0] && $priceArray['max'] == $selectedFilters['price'][1])
				unset($selectedFilters['price']);
		if (isset($selectedFilters['weight']))
			if ($weightArray['min'] == $selectedFilters['weight'][0] && $weightArray['max'] == $selectedFilters['weight'][1])
				unset($selectedFilters['weight']);
				
		foreach ($selectedFilters as $filters)
			$nFilters += count($filters);
		
		$cache = array('layered_show_qties' => (int)Configuration::get('PS_LAYERED_SHOW_QTIES'), 'id_category_layered' => (int)$id_parent,
		'selected_filters' => $selectedFilters, 'n_filters' => (int)$nFilters, 'nbr_filterBlocks' => count($filterBlocks), 'filters' => $filterBlocks,
		'title_values' => $titleValues, 'current_friendly_url' => htmlentities($paramSelected), 'nofollow' => !empty($paramSelected) || $nofollow);
		
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
	
	public function generateFiltersBlock($selectedFilters)
	{
		global $smarty;
		if ($filterBlock = $this->getFilterBlock($selectedFilters))
		{
			if ($filterBlock['nbr_filterBlocks'] == 0)
				return false;
				
			$smarty->assign($filterBlock);
			
			return $this->display(__FILE__, 'blocklayered.tpl');
		}
		else
			return false;
	}
	
	private static function getPriceFilterSubQuery($filterValue)
	{
		$idCurrency = (int)Currency::getCurrent()->id;
		$priceFilterQuery = '';
		if (isset($filterValue) && $filterValue)
		{
			$idCurrency = Currency::getCurrent()->id;
			$priceFilterQuery = '
			INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi ON (psi.id_product = p.id_product AND psi.id_currency = '.(int)$idCurrency.'
			AND psi.price_min <= '.(int)$filterValue[1].' AND psi.price_max >= '.(int)$filterValue[0].') ';
		}
		else
		{
			$idCurrency = Currency::getCurrent()->id;
			$priceFilterQuery = '
			INNER JOIN `'._DB_PREFIX_.'layered_price_index` psi 
			ON (psi.id_product = p.id_product AND psi.id_currency = '.(int)$idCurrency.') ';
		}
		
		return array('join' => $priceFilterQuery, 'select' => ', psi.price_min, psi.price_max');
	}
	
	private static function filterProductsByPrice($filterValue, $productCollection)
	{
		if (empty($filterValue))
			return $productCollection;
		foreach ($productCollection as $key => $product)
		{
			if (isset($filterValue) && $filterValue && isset($product['price_min']) && isset($product['id_product'])
			&& ((int)$filterValue[0] > $product['price_min'] || (int)$filterValue[1] < $product['price_max']))
			{
				$price = Product::getPriceStatic($product['id_product']);
				if ($price < $filterValue[0] || $price > $filterValue[1])
					continue;
				unset($productCollection[$key]);
			}
		}
		return $productCollection;
	}
	
	private static function getWeightFilterSubQuery($filterValue, $ignoreJoin)
	{
		if (isset($filterValue) && $filterValue)
			if ($filterValue[0] != 0 || $filterValue[1] != 0)
				return array('where' => ' AND p.`weight` BETWEEN '.(float)($filterValue[0] - 0.001).' AND '.(float)($filterValue[1] + 0.001).' ');
		
		return array();
	}
	
	private static function getId_featureFilterSubQuery($filterValue, $ignoreJoin)
	{
		if (empty($filterValue))
			return array();
		$queryFilters = ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'feature_product fp WHERE ';
		foreach ($filterValue as $filterVal)
			$queryFilters .= 'fp.`id_feature_value` = '.(int)$filterVal.' OR ';
		$queryFilters = rtrim($queryFilters, 'OR ').') ';
		
		return array('where' => $queryFilters);
	}
	private static function getId_attribute_groupFilterSubQuery($filterValue, $ignoreJoin)
	{
		if (empty($filterValue))
			return array();
		$queryFilters = '
		AND p.id_product IN (SELECT pa.`id_product`
		FROM `'._DB_PREFIX_.'product_attribute_combination` pac
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		WHERE ';
		
		foreach ($filterValue as $filterVal)
			$queryFilters .= 'pac.`id_attribute` = '.(int)$filterVal.' OR ';
		$queryFilters = rtrim($queryFilters, 'OR ').') ';
		
		return array('where' => $queryFilters);
	}
	
	private static function getCategoryFilterSubQuery($filterValue, $ignoreJoin)
	{
		if (empty($filterValue))
			return array();
		$queryFiltersJoin = '';
		$queryFiltersWhere = ' AND p.id_product IN (SELECT id_product FROM '._DB_PREFIX_.'category_product cp WHERE ';
		foreach ($filterValue as $id_category)
			$queryFiltersWhere .= 'cp.`id_category` = '.(int)$id_category.' OR ';
		$queryFiltersWhere = rtrim($queryFiltersWhere, 'OR ').') ';
		
		return array('where' => $queryFiltersWhere, 'join' => $queryFiltersJoin);
	}
	
	private static function getQuantityFilterSubQuery($filterValue, $ignoreJoin)
	{
		if (count($filterValue) == 2 || empty($filterValue))
			return array();
		$queryFilters = ' AND p.quantity '.(!$filterValue[0] ? '=' : '>').' 0 ';
		
		return array('where' => $queryFilters);
	}
	
	private static function getManufacturerFilterSubQuery($filterValue, $ignoreJoin)
	{
		if (empty($filterValue))
			$queryFilters = '';
		else
		{
			array_walk($filterValue, create_function('&$id_manufacturer', '$id_manufacturer = (int)$id_manufacturer;'));
			$queryFilters = ' AND p.id_manufacturer IN ('.implode($filterValue, ',').')';
		}
			if ($ignoreJoin)
				return array('where' => $queryFilters, 'select' => ', m.name');
			else
				return array('where' => $queryFilters, 'select' => ', m.name', 'join' => 'LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.id_manufacturer = p.id_manufacturer) ');
	}
	
	private static function getConditionFilterSubQuery($filterValue, $ignoreJoin)
	{
		if (count($filterValue) == 3 || empty($filterValue))
			return array();
		$queryFilters = ' AND p.condition IN (';
		foreach ($filterValue as $cond)
			$queryFilters .= '\''.$cond.'\',';
		$queryFilters = rtrim($queryFilters, ',').') ';
		
		return array('where' => $queryFilters);
	}
	
	public function ajaxCallBackOffice($categoryBox = array(), $id_layered_filter = null)
	{
		global $cookie;
		
		if (!empty($id_layered_filter))
		{
			$layeredFilter = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM '._DB_PREFIX_.'layered_filter WHERE id_layered_filter = '.(int)$id_layered_filter);
			if ($layeredFilter && isset($layeredFilter['filters']) && !empty($layeredFilter['filters']))
				$layeredValues = unserialize($layeredFilter['filters']);
			if (isset($layeredValues['categories']) && count($layeredValues['categories']))
				foreach ($layeredValues['categories'] as $id_category)
					$categoryBox[] = (int)$id_category;
		}
		
		/* Clean categoryBox before use */
		if (isset($categoryBox) && is_array($categoryBox))
			foreach ($categoryBox as &$value)
				$value = (int)$value;
		
		$attributeGroups = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT ag.id_attribute_group, ag.is_color_group, agl.name, COUNT(DISTINCT(a.id_attribute)) n
		FROM '._DB_PREFIX_.'attribute_group ag
		LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = ag.id_attribute_group)
		LEFT JOIN '._DB_PREFIX_.'attribute a ON (a.id_attribute_group = ag.id_attribute_group)
		'.(count($categoryBox) ? '
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = pa.id_product)' : '').'
		WHERE agl.id_lang = '.(int)$cookie->id_lang.
		(count($categoryBox) ? ' AND cp.id_category IN ('.implode(',', $categoryBox).')' : '').'
		GROUP BY ag.id_attribute_group');
		
		$features = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT fl.id_feature, fl.name, COUNT(DISTINCT(fv.id_feature_value)) n
		FROM '._DB_PREFIX_.'feature_lang fl
		LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature = fl.id_feature)
		'.(count($categoryBox) ? '
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature = fv.id_feature)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = fp.id_product)' : '').'		
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND fl.id_lang = '.(int)$cookie->id_lang.
		(count($categoryBox) ? ' AND cp.id_category IN ('.implode(',', $categoryBox).')' : '').'
		GROUP BY fl.id_feature');
		
		$nElements = count($attributeGroups) + count($features) + 4;
		if ($nElements > 20)
			$nElements = 20;
		
		$html = '
		<div id="layered_container_right" style="width: 360px; float: left; margin-left: 20px; height: '.(int)(30 + $nElements * 38).'px; overflow-y: auto;">
			<h3>'.$this->l('Available filters').' <span id="num_avail_filters">(0)</span></h3>
			<ul id="all_filters"></ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_subcategories" name="layered_selection_subcategories" />
					<span class="position"></span>'.$this->l('Sub-categories filter').'
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_stock" name="layered_selection_stock" /> <span class="position"></span>'.$this->l('Product stock filter').'</li></ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_condition" name="layered_selection_condition" />
					<span class="position"></span>'.$this->l('Product condition filter').'
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_manufacturer" name="layered_selection_manufacturer" />
					<span class="position"></span>'.$this->l('Product manufacturer filter').'
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_weight_slider" name="layered_selection_weight_slider" />
					<span class="position"></span>'.$this->l('Product weight filter (slider)').'
				</li>
			</ul>
			<ul>
				<li class="ui-state-default layered_right">
					<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
					<input type="checkbox" id="layered_selection_price_slider" name="layered_selection_price_slider" />
					<span class="position"></span>'.$this->l('Product price filter (slider)').'
				</li>
			</ul>';
			
			if (count($attributeGroups))
			{
				$html .= '<ul>';
				foreach ($attributeGroups as $attributeGroup)
					$html .= '
					<li class="ui-state-default layered_right">
						<span class="ui-icon ui-icon-arrowthick-2-n-s"></span>
						<input type="checkbox" id="layered_selection_ag_'.(int)$attributeGroup['id_attribute_group'].'" name="layered_selection_ag_'.(int)$attributeGroup['id_attribute_group'].'" />
						<span class="position"></span>
						'.$this->l('Attribute group:').' '.$attributeGroup['name'].' ('.(int)$attributeGroup['n'].' '.($attributeGroup['n'] > 1 ? $this->l('attributes') : $this->l('attribute')).')'.
						($attributeGroup['is_color_group'] ? ' <img src="../img/admin/color_swatch.png" alt="" title="'.$this->l('This group will allow user to select a color').'" />' : '').'
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
						'.$this->l('Feature:').' '.$feature['name'].' ('.(int)$feature['n'].' '.($feature['n'] > 1 ? $this->l('values') : $this->l('value')).')
					</li>';
				$html .= '</ul>';
			}

		$html .= '
		</div>';
		
		if (isset($layeredValues))
		{
			$html .= '
			<script type="text/javascript">
				//<![CDATA[
				$(document).ready(function()
				{
					$(\'#selected_filters li\').remove();
			';
				
			foreach ($layeredValues as $key => $layeredValue)
				if ($key != 'categories')
					$html .= '$(\'#'.$key.'\').click();'."\n";
			
			if (isset($layeredValues['categories']) && count($layeredValues['categories']))
			{
				foreach ($layeredValues['categories'] as $id_category)
					$html .= '$(\'#categories-treeview\').find(\'input[name="categoryBox[]"][value='.(int)$id_category.']\').attr(\'checked\', \'checked\');'."\n";
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
			$(\'#layered_tpl_name\').val(\''.addslashes($layeredFilter['name']).'\');
			$(\'#id_layered_filter\').val(\''.(int)$layeredFilter['id_layered_filter'].'\')';
				
			$html .= '
				});
			</script>';
		}

		return $html;
	}
	
	public function ajaxCall()
	{
		global $smarty;

		$selectedFilters = $this->getSelectedFilters();
		
		$this->getProducts($selectedFilters, $products, $nbProducts, $p, $n, $pages_nb, $start, $stop, $range);
			
		$smarty->assign('nb_products', $nbProducts);
		$smarty->assign('category', (object)array('id' => Tools::getValue('id_category_layered', 1)));
		$pagination_infos = array('pages_nb' => (int)($pages_nb), 'p' => (int)$p, 'n' => (int)$n, 'range' => (int)$range, 'start' => (int)$start, 'stop' => (int)$stop,
		'nArray' => $nArray = (int)Configuration::get('PS_PRODUCTS_PER_PAGE') != 10 ? array((int)Configuration::get('PS_PRODUCTS_PER_PAGE'), 10, 20, 50) : array(10, 20, 50));
		$smarty->assign($pagination_infos);
		$smarty->assign('comparator_max_item', (int)(Configuration::get('PS_COMPARATOR_MAX_ITEM')));
		$smarty->assign('products', $products);
		$smarty->assign('products_per_page', (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
		
		// Prevent bug with old template where category.tpl contain the title of the category and category-count.tpl do not exists
		if (file_exists(_PS_THEME_DIR_.'category-count.tpl'))
			$categoryCount = $smarty->fetch(_PS_THEME_DIR_.'category-count.tpl');
		else
			$categoryCount = '';

		if ($nbProducts == 0)
			$product_list = $this->display(__FILE__, 'blocklayered-no-products.tpl');
		else
			$product_list = $smarty->fetch(_PS_THEME_DIR_.'product-list.tpl');
		
		/* We are sending an array in jSon to the .js controller, it will update both the filters and the products zones */
		return Tools::jsonEncode(array(
		'filtersBlock' => $this->generateFiltersBlock($selectedFilters),
		'productList' => $product_list,
		'pagination' => $smarty->fetch(_PS_THEME_DIR_.'pagination.tpl'),
		'categoryCount' => $categoryCount));
	}
	
	public function getProducts($selectedFilters, &$products, &$nbProducts, &$p, &$n, &$pages_nb, &$start, &$stop, &$range)
	{
		global $cookie;
		
		$products = $this->getProductByFilters($selectedFilters);
		$products = Product::getProductsProperties((int)$cookie->id_lang, $products);
		
		$nbProducts = $this->nbr_products;
		$range = 2; /* how many pages around page selected */
		
		$n = (int)Tools::getValue('n', Configuration::get('PS_PRODUCTS_PER_PAGE'));
		$p = Tools::getValue('p', 1);
		
		if ($p < 0)
			$p = 0;
		
		if ($p > ($nbProducts / $n))
			$p = ceil($nbProducts / $n);
		$pages_nb = ceil($nbProducts / (int)($n));

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
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_category');
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_category` (
		`id_layered_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_category` INT(10) UNSIGNED NOT NULL,
		`id_value` INT(10) UNSIGNED NULL DEFAULT \'0\',
		`type` ENUM(\'category\',\'id_feature\',\'id_attribute_group\',\'quantity\',\'condition\',\'manufacturer\',\'weight\',\'price\') NOT NULL,
		`position` INT(10) UNSIGNED NOT NULL,
		PRIMARY KEY (`id_layered_category`),
		KEY `id_category` (`id_category`,`type`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;'); /* MyISAM + latin1 = Smaller/faster */
		
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_filter` (
		`id_layered_filter` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`name` VARCHAR(64) NOT NULL,
		`filters` TEXT NULL,
		`n_categories` INT(10) UNSIGNED NOT NULL,
		`date_add` DATETIME NOT NULL)');
	}
	
	public function rebuildLayeredCache($productsIds = array(), $categoriesIds = array())
	{
		@set_time_limit(0);
		
		/* Set memory limit to 128M only if current is lower */
		$memory_limit = @ini_get('memory_limit');
		if (substr($memory_limit, -1) != 'G' && ((substr($memory_limit, -1) == 'M' && substr($memory_limit, 0, -1) < 128) || is_numeric($memory_limit) && (intval($memory_limit) < 131072)))
			@ini_set('memory_limit', '128M');

		$db = Db::getInstance(_PS_USE_SQL_SLAVE_);
		$nCategories = array();
		$doneCategories = array();

		$attributeGroups = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT a.id_attribute, a.id_attribute_group
		FROM '._DB_PREFIX_.'attribute a
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = pa.id_product)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		WHERE c.active = 1'.(count($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').' AND p.active = 1'.(count($productsIds) ? '
		AND p.id_product IN ('.implode(',', $productsIds).')' : ''), false);

		$attributeGroupsById = array();
		while ($row = $db->nextRow($attributeGroups))
			$attributeGroupsById[(int)$row['id_attribute']] = (int)$row['id_attribute_group'];

		$features = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT fv.id_feature_value, fv.id_feature
		FROM '._DB_PREFIX_.'feature_value fv
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature_value = fv.id_feature_value)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND c.active = 1'.(count($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').'
		AND p.active = 1'.(count($productsIds) ? ' AND p.id_product IN ('.implode(',', $productsIds).')' : ''), false);

		$featuresById = array();
		while ($row = $db->nextRow($features))
			$featuresById[(int)$row['id_feature_value']] = (int)$row['id_feature'];

		$result = $db->ExecuteS('
		SELECT p.id_product, GROUP_CONCAT(DISTINCT fv.id_feature_value) features, GROUP_CONCAT(DISTINCT cp.id_category) categories, GROUP_CONCAT(DISTINCT pac.id_attribute) attributes
		FROM '._DB_PREFIX_.'product p
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_product_attribute = pa.id_product_attribute)
		WHERE c.active = 1'.(count($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').' AND p.active = 1'.(count($productsIds) ? '
		AND p.id_product IN ('.implode(',', $productsIds).')' : '').' AND (fv.custom IS NULL OR fv.custom = 0)
		GROUP BY p.id_product', false);

		while ($product = $db->nextRow($result))
		{
			$a = $c = $f = array();
			if (!empty($product['attributes']))
				$a = array_flip(explode(',', $product['attributes']));
			if (!empty($product['categories']))
				$c = array_flip(explode(',', $product['categories']));
			if (!empty($product['features']))
				$f = array_flip(explode(',', $product['features']));

			$queryCategory = 'INSERT INTO '._DB_PREFIX_.'layered_category (id_category, id_value, type, position) VALUES ';
			$toInsert = false;
			foreach ($c as $id_category => $category)
			{
				if (!isset($nCategories[(int)$id_category]))
					$nCategories[(int)$id_category] = 1;
				if (!isset($doneCategories[(int)$id_category]['cat']))
				{
					$doneCategories[(int)$id_category]['cat'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'category\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				foreach ($a as $kAttribute => $attribute)
					if (!isset($doneCategories[(int)$id_category]['a'.(int)$attributeGroupsById[(int)$kAttribute]]))
					{
						$doneCategories[(int)$id_category]['a'.(int)$attributeGroupsById[(int)$kAttribute]] = true;
						$queryCategory .= '('.(int)$id_category.','.(int)$attributeGroupsById[(int)$kAttribute].',\'id_attribute_group\','.(int)$nCategories[(int)$id_category]++.'),';
						$toInsert = true;
					}
				foreach ($f as $kFeature => $feature)
					if (!isset($doneCategories[(int)$id_category]['f'.(int)$featuresById[(int)$kFeature]]))
					{
						$doneCategories[(int)$id_category]['f'.(int)$featuresById[(int)$kFeature]] = true;
						$queryCategory .= '('.(int)$id_category.','.(int)$featuresById[(int)$kFeature].',\'id_feature\','.(int)$nCategories[(int)$id_category]++.'),';
						$toInsert = true;
					}
				if (!isset($doneCategories[(int)$id_category]['q']))
				{
					$doneCategories[(int)$id_category]['q'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'quantity\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				if (!isset($doneCategories[(int)$id_category]['m']))
				{
					$doneCategories[(int)$id_category]['m'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'manufacturer\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				if (!isset($doneCategories[(int)$id_category]['c']))
				{
					$doneCategories[(int)$id_category]['c'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'condition\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				if (!isset($doneCategories[(int)$id_category]['w']))
				{
					$doneCategories[(int)$id_category]['w'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'weight\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				if (!isset($doneCategories[(int)$id_category]['p']))
				{
					$doneCategories[(int)$id_category]['p'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'price\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
			}
			if ($toInsert)
				Db::getInstance()->Execute(rtrim($queryCategory, ','));
		}
	}
}
