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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registred Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class BlockLayered extends Module
{
	public function __construct()
	{
		$this->name = 'blocklayered';
		$this->tab = 'front_office_features';
		$this->version = 1.3;
		$this->author = 'PrestaShop';

		parent::__construct();

		$this->displayName = $this->l('Layered navigation block');
		$this->description = $this->l('Displays a block with layered navigation filters.');
	}

	public function install()
	{		
		if ($result = parent::install() AND $this->registerHook('leftColumn') AND $this->registerHook('header')
		AND $this->registerHook('addProduct') AND $this->registerHook('updateProduct') AND $this->registerHook('deleteProduct')
		AND $this->registerHook('categoryAddition') AND $this->registerHook('categoryUpdate') AND $this->registerHook('categoryDeletion'))
		{
			Configuration::updateValue('PS_LAYERED_NAVIGATION_CHECKBOXES', 1);
			$this->rebuildLayeredStructure();
		}

		return $result;
	}
	
	public function uninstall()
	{
		/* Delete all configurations */
		Configuration::deleteByName('PS_LAYERED_NAVIGATION_CHECKBOXES');
		
		return parent::uninstall();
	}
	
	public function hookLeftColumn($params)
	{
		return $this->generateFilters();
	}
	
	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	
	public function hookHeader($params)
	{
		Tools::addJS(($this->_path).'blocklayered.js');
		Tools::addCSS(($this->_path).'blocklayered.css', 'all');
	}
	
	public function hookAddProduct($params)
	{
		$this->rebuildLayeredCache(array((int)$params['product']->id));
	}
	
	public function hookUpdateProduct($params)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_cache WHERE id_product = '.(int)$params['product']->id.' LIMIT 1');		
		$this->rebuildLayeredCache(array((int)$params['product']->id));
	}
	
	public function hookDeleteProduct($params)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_cache WHERE id_product = '.(int)$params['product']->id.' LIMIT 1');
	}
	
	public function hookCategoryAddition($params)
	{
		Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'layered_cache` ADD `c'.(int)$params['category']->id.'` TINYINT UNSIGNED NOT NULL DEFAULT \'0\'');
		Configuration::updateValue('PS_LAYERED_COLUMNS', Configuration::get('PS_LAYERED_COLUMNS').',c'.(int)$params['category']->id);
		$this->rebuildLayeredCache(array(), array((int)$params['category']->id));
	}
	
	public function hookCategoryUpdate($params)
	{
		/* The category status might (active, inactive) have changed, we have to update the layered cache table structure */
		if (!$params['category']->active)
			$this->hookCategoryDeletion($params);
		else
		{
			$oneRow = Db::getInstance()->getRow('SELECT c'.(int)$params['category']->id.' FROM `'._DB_PREFIX_.'layered_cache`');
			if (!isset($oneRow['c'.(int)$params['category']->id]))
			{
				Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'layered_cache` ADD `c'.(int)$params['category']->id.'` TINYINT UNSIGNED NOT NULL DEFAULT \'0\'');
				Configuration::updateValue('PS_LAYERED_COLUMNS', Configuration::get('PS_LAYERED_COLUMNS').',c'.(int)$params['category']->id);
			}
			if (!Db::getInstance()->getRow('SELECT id_layered_category FROM `'._DB_PREFIX_.'layered_category` WHERE id_category = '.(int)$params['category']->id))
				$this->rebuildLayeredCache(array(), array((int)$params['category']->id));
		}
	}
	
	public function hookCategoryDeletion($params)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_category WHERE id_category = '.(int)$params['category']->id);
		$oneRow = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'layered_cache`');
		if (isset($oneRow['c'.(int)$params['category']->id]))
		{
			Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'layered_cache` DROP `c'.(int)$params['category']->id.'`');
			Configuration::updateValue('PS_LAYERED_COLUMNS', str_replace(',c'.(int)$params['category']->id, '', Configuration::get('PS_LAYERED_COLUMNS')));
		}
	}
	
	public function getContent()
	{
		if (Tools::isSubmit('submitLayeredCache'))
		{
			$this->rebuildLayeredStructure();
			$this->rebuildLayeredCache();
			
			echo '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				'.$this->l('Layered navigation database was initialized successfully').'
			</div>';
		}
		elseif (Tools::isSubmit('submitLayeredSettings'))
		{
			echo '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				'.$this->l('Settings saved successfully').'
			</div>';
		}
		
		echo '
		<h2>'.$this->l('Layered navigation').'</h2>
		
		<p class="warning" style="font-weight: bold;"><img src="../img/admin/information.png" alt="" /> '.$this->l('This module is in beta version and will be improved in PrestaShop v1.4.1').'</p><br />
		<fieldset class="width2">
			<legend><img src="../img/admin/asterisk.gif" alt="" />'.$this->l('10 upcoming improvements in PrestaShop v1.4.1').'</legend>
			<ol>				
				<li>'.$this->l('Real-time refresh of the cache table').'</li>
				<li>'.$this->l('Additional filters (prices, weight)').'</li>
				<li>'.$this->l('Ability to manage filters by category in the module configuration').'</li>
				<li>'.$this->l('Ability to hide filter groups with no values and filter values with 0 products').'</li>
				<li>'.$this->l('Statistics and analysis').'</li>
				<li>'.$this->l('Manage products sort & pagination').'</li>
				<li>'.$this->l('Add a check on the category_group table').'</li>
				<li>'.$this->l('SEO links & real time URL building (ability to give the URL to someone)').'</li>
				<li>'.$this->l('Add more options in the module configuration').'</li>
				<li>'.$this->l('Performances improvements').'</li>
			</ol>
		</fieldset><br />
		<!--
		<fieldset class="width2">
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Settings').'</legend>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">				
				<p style="text-align: center;"><input type="submit" class="button" name="submitLayeredSettings" value="'.$this->l('Update settings').'" /></p>
			</form>
		</fieldset>
		<br />
		-->
		<fieldset class="width2">
			<legend><img src="../img/admin/database_gear.gif" alt="" />'.$this->l('Cache initialization').'</legend>
			<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
				<div class="warning">
					<p style="font-weight: bold;"><img src="../img/admin/warning.gif" alt="" /> '.$this->l('When do you have to initialize the cache?').'</p>					
					<ul>
						<li style="color: red; font-weight: bold;">'.$this->l('Before using this module for the first time').'</li>
						<li>'.$this->l('You add/update/delete').' '.$this->l('a feature or a feature value').'</li>
						<li>'.$this->l('You add/update/delete').' '.$this->l('an attribute group or an attribute value').'</li>
						<li>'.$this->l('You update one or more feature values for a product').'</li>
					</ul>
					<p><b>'.$this->l('Warning: This could take several minutes.').'</b><br /><br />
					'.$this->l('If you do not, this cache table might become larger and larger (less efficient), and all the new choices (attributes, features) will not be offered to your visitors.').'</p>
				</div>
				<p style="text-align: center;"><input type="submit" class="button" name="submitLayeredCache" value="'.$this->l('Initialize the layered navigation database').'" /></p>
			</form>
		</fieldset>';
	}
	
	public function generateFilters($selectedFilters = array())
	{
		ini_set('display_errors', 'On');
		
		global $smarty, $link, $cookie;

		/* If the current category isn't defined of if it's homepage, we have nothing to display */
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;

		/* First we need to get all subcategories of current category */
		$category = new Category((int)$id_parent);
		
		$subCategories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.id_category, c.id_parent
		FROM '._DB_PREFIX_.'category c
		WHERE c.nleft > '.(int)$category->nleft.' and c.nright <= '.(int)$category->nright.' AND c.active = 1
		ORDER BY c.position ASC');
		
		$oneRow = Db::getInstance()->getRow('SELECT * FROM '._DB_PREFIX_.'layered_cache');
		if (!$oneRow)
			return;		
		
		$whereC = ' AND (c'.(int)$id_parent.' = 1 OR ';
		$queryProduct = ', c'.(int)$id_parent;
		foreach ($subCategories AS $subcategory)
			if (isset($oneRow['c'.(int)$subcategory['id_category']]))
			{
				$whereC .= ' c'.(int)$subcategory['id_category'].' = 1 OR ';
				$queryProduct .= ', c'.(int)$subcategory['id_category'];
				$selectedFilters['category'][] = (int)$subcategory['id_category'];
			}
		$whereC = rtrim($whereC, 'OR ').')';
		
		/* Get the filters for the current category */
		$filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM '._DB_PREFIX_.'layered_category WHERE id_category = '.(int)$id_parent);		
		$filterBlocks = $f = $a = array();
		foreach ($filters AS $filter)
		{
			$filterBlocks[(int)$filter['position']]['type_lite'] = $filter['type'];
			$filterBlocks[(int)$filter['position']]['type'] = $filter['type'].($filter['id_value'] ? '_'.(int)$filter['id_value'] : '');
			$filterBlocks[(int)$filter['position']]['id_key'] = (int)$filter['id_value'];
			switch ($filter['type'])
			{					
				case 'id_feature':
					$f[] = (int)$filter['id_value'];
					
					$filterBlocks[(int)$filter['position']]['SQLvalues'] = Db::getInstance()->ExecuteS('
					SELECT fvl.id_feature_value, fvl.value
					FROM '._DB_PREFIX_.'feature_value fv
					LEFT JOIN '._DB_PREFIX_.'feature_value_lang fvl ON (fvl.id_feature_value = fv.id_feature_value)
					WHERE (fv.custom IS NULL OR fv.custom = 0) AND fv.id_feature = '.(int)$filterBlocks[(int)$filter['position']]['id_key'].' AND fvl.id_lang = '.(int)$cookie->id_lang);
					
					foreach ($filterBlocks[(int)$filter['position']]['SQLvalues'] AS $key => $value)
						if (isset($oneRow['f'.(int)$value['id_feature_value']]))
							$queryProduct .= ', f'.(int)$value['id_feature_value'];
					
					break;

				case 'id_attribute_group':
					$a[] = (int)$filter['id_value'];
					
					$filterBlocks[(int)$filter['position']]['SQLvalues'] = Db::getInstance()->ExecuteS('
					SELECT al.id_attribute, al.name, a.color
					FROM '._DB_PREFIX_.'attribute a
					LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = a.id_attribute)
					WHERE a.id_attribute_group = '.(int)$filterBlocks[(int)$filter['position']]['id_key'].' AND al.id_lang = '.(int)$cookie->id_lang);
					
					foreach ($filterBlocks[(int)$filter['position']]['SQLvalues'] AS $value)
						if (isset($oneRow['a'.(int)$value['id_attribute']]))
							$queryProduct .= ', a'.(int)$value['id_attribute'];
					
					break;
			}
		}
		
		$productsSQL = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT lc.id_product, p.condition, p.id_manufacturer, p.quantity'.$queryProduct.'
		FROM '._DB_PREFIX_.'layered_cache lc
		INNER JOIN '._DB_PREFIX_.'product p ON (p.id_product = lc.id_product)
		WHERE 1 '.$whereC, false);

		$products = array();
		$db = Db::getInstance();
		while ($product = $db->nextRow($productsSQL))
		{
			$row = array();
			foreach ($product AS $key => $value)
				if ($value == 1)
					$row[$key] = true;
			$row['id_manufacturer'] = (int)$product['id_manufacturer'];
			$row['quantity'] = (bool)$product['quantity'];
			$row['condition'] = $product['condition'];
			$products[(int)$product['id_product']] = $row;
		}
		
		/* Get the feature block names & values */
		if (sizeof($f))
		{
			$fNames = Db::getInstance()->ExecuteS('
			SELECT id_feature, name
			FROM '._DB_PREFIX_.'feature_lang
			WHERE id_lang = '.(int)$cookie->id_lang.' AND id_feature IN ('.implode(',', $f).')');
			
			$fNameByID = array();
			foreach ($fNames AS $fName)
				$fNameByID[(int)$fName['id_feature']] = $fName['name'];
		}
		
		/* Get the feature block names & values */
		if (sizeof($a))
		{
			$aNames = Db::getInstance()->ExecuteS('
			SELECT ag.id_attribute_group, agl.public_name, ag.is_color_group
			FROM '._DB_PREFIX_.'attribute_group ag
			LEFT JOIN '._DB_PREFIX_.'attribute_group_lang agl ON (agl.id_attribute_group = ag.id_attribute_group)
			WHERE agl.id_lang = '.(int)$cookie->id_lang.' AND ag.id_attribute_group IN ('.implode(',', $a).')');

			$aNameByID = $colorGroups = array();
			foreach ($aNames AS $aName)
			{
				$aNameByID[(int)$aName['id_attribute_group']] = $aName['public_name'];
				if ($aName['is_color_group'])
					$colorGroups[(int)$aName['id_attribute_group']] = true;
			}
		}
		
		foreach ($filterBlocks AS &$filterBlock)
		{
			if ($filterBlock['type_lite'] == 'category')
			{
				$filterBlock['name'] = $this->l('Categories');
				
				$productCat = $this->filterProducts($products, $selectedFilters, 'category');
				
				$filterCategories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT c.id_category, c.id_parent, cl.name, 
				(SELECT GROUP_CONCAT(c2.id_category) FROM '._DB_PREFIX_.'category c2 WHERE c2.active = 1 AND c2.nleft > c.nleft and c2.nright < c.nright) subcategories
				FROM '._DB_PREFIX_.'category c
				LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category)
				WHERE c.active = 1 AND c.id_parent = '.(int)$category->id.' AND cl.id_lang = '.(int)$cookie->id_lang.'
				ORDER BY c.position ASC');

				foreach ($filterCategories AS $filterCategory)
				{
					$filterBlock['values'][(int)$filterCategory['id_category']]['name'] = method_exists('Category', 'hideCategoryPosition') ? Category::hideCategoryPosition($filterCategory['name']) : $filterCategory['name'];
					if (!isset($filterBlock['values'][(int)$filterCategory['id_category']]['n']))
						$filterBlock['values'][(int)$filterCategory['id_category']]['n'] = 0;
					if (isset($selectedFilters['category']) AND in_array($filterCategory['id_category'], $selectedFilters['category']))
						$filterBlock['values'][(int)$filterCategory['id_category']]['checked'] = true;
					
					foreach ($productCat AS $product)
					{
						$tmpTab = explode(',', $filterCategory['subcategories']);
						$tmpTab[] = (int)$filterCategory['id_category'];
						foreach ($tmpTab AS $idSubCategory)
							if (isset($product['c'.(int)$idSubCategory]))
								$filterBlock['values'][(int)$filterCategory['id_category']]['n']++;
					}
				}
			}
			elseif ($filterBlock['type_lite'] == 'id_feature')
			{
				$filterBlock['name'] = $fNameByID[(int)$filterBlock['id_key']];
				$filterBlock['values'] = array();
				
				$productFeat = $this->filterProducts($products, $selectedFilters, 'id_feature_'.(int)$filterBlock['id_key']);
				
				foreach ($filterBlock['SQLvalues'] AS $value)
				{					
					foreach ($productFeat AS $product)
						if (isset($product['f'.$value['id_feature_value']]))
						{
							$filterBlock['values'][(int)$value['id_feature_value']]['name'] = $value['value'];
							if (!isset($filterBlock['values'][(int)$value['id_feature_value']]['n']))
								$filterBlock['values'][(int)$value['id_feature_value']]['n'] = 0;
							$filterBlock['values'][(int)$value['id_feature_value']]['n']++;							
						}
					foreach ($products AS $product)
						if (isset($product['f'.$value['id_feature_value']]) AND !isset($filterBlock['values'][(int)$value['id_feature_value']]))
						{
							$filterBlock['values'][(int)$value['id_feature_value']]['name'] = $value['value'];
							$filterBlock['values'][(int)$value['id_feature_value']]['n'] = 0;
						}
					if (isset($selectedFilters['id_feature_'.(int)$filterBlock['id_key']]) AND in_array((int)$value['id_feature_value'].'_'.(int)$filterBlock['id_key'], $selectedFilters['id_feature_'.(int)$filterBlock['id_key']]))
						$filterBlock['values'][(int)$value['id_feature_value']]['checked'] = true;
				}
				
				unset($filterBlock['SQLvalues']);
			}
			elseif ($filterBlock['type_lite'] == 'id_attribute_group')
			{
				$filterBlock['name'] = $aNameByID[(int)$filterBlock['id_key']];
				$filterBlock['is_color_group'] = isset($colorGroups[(int)$filterBlock['id_key']]);
				$filterBlock['values'] = array();
				
				$productsAttr = $this->filterProducts($products, $selectedFilters, 'id_attribute_group_'.(int)$filterBlock['id_key']);

				foreach ($filterBlock['SQLvalues'] AS $value)
				{
					foreach ($productsAttr AS $product)
						if (isset($product['a'.$value['id_attribute']]))
						{
							$filterBlock['values'][(int)$value['id_attribute']]['name'] = $value['name'];
							$filterBlock['values'][(int)$value['id_attribute']]['color'] = $value['color'];
							if (!isset($filterBlock['values'][(int)$value['id_attribute']]['n']))
								$filterBlock['values'][(int)$value['id_attribute']]['n'] = 0;
							$filterBlock['values'][(int)$value['id_attribute']]['n']++;							
						}
					foreach ($products AS $product)
						if (isset($product['a'.$value['id_attribute']]) AND !isset($filterBlock['values'][(int)$value['id_attribute']]))
						{
							$filterBlock['values'][(int)$value['id_attribute']]['name'] = $value['name'];
							$filterBlock['values'][(int)$value['id_attribute']]['n'] = 0;
						}
					if (isset($selectedFilters['id_attribute_group_'.(int)$filterBlock['id_key']]) AND in_array((int)$value['id_attribute'].'_'.(int)$filterBlock['id_key'], $selectedFilters['id_attribute_group_'.(int)$filterBlock['id_key']]))
						$filterBlock['values'][(int)$value['id_attribute']]['checked'] = true;
				}
				unset($filterBlock['SQLvalues']);
			}
			elseif ($filterBlock['type_lite'] == 'condition')
			{
				$filterBlock['name'] = $this->l('Condition');
				$filterBlock['values'] = array(
				'new' => array('name' => $this->l('New'), 'n' => 0), 
				'used' => array('name' => $this->l('Used'), 'n' => 0), 
				'refurbished' => array('name' => $this->l('Refurbished'), 'n' => 0));
				
				$productCond = $this->filterProducts($products, $selectedFilters, 'condition');
				foreach ($filterBlock['values'] AS $conditionKey => &$condition)
				{
					foreach ($productCond AS $product)
						if ($product['condition'] == $conditionKey)
							$condition['n']++;
					if (isset($selectedFilters['condition']) AND in_array($conditionKey, $selectedFilters['condition']))
						$condition['checked'] = true;
				}
			}
			elseif ($filterBlock['type_lite'] == 'quantity')
			{
				$filterBlock['name'] = $this->l('Availability');
				$filterBlock['values'] = array(
				'1' => array('name' => $this->l('In stock'), 'n' => 0),
				'0' => array('name' => $this->l('Not available'), 'n' => 0));				
				$productQuant = $this->filterProducts($products, $selectedFilters, 'quantity');
				foreach ($filterBlock['values'] AS $quantKey => &$quantity)
				{
					foreach ($productQuant AS $product)
						if ($product['quantity'] == $quantKey)
							$quantity['n']++;
					if (isset($selectedFilters['quantity']) AND in_array($quantKey, $selectedFilters['quantity']))
						$quantity['checked'] = true;
				}
			}
			elseif ($filterBlock['type_lite'] == 'manufacturer')
			{
				$filterBlock['name'] = $this->l('Manufacturer');

				$productsManuf = $this->filterProducts($products, $selectedFilters, 'manufacturer');
				
				$man = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT DISTINCT(p.id_manufacturer), m.name
				FROM '._DB_PREFIX_.'product p			
				LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				WHERE p.id_product IN ('.implode(',', array_keys($products)).') AND p.id_manufacturer != 0');

				foreach ($man AS $manufacturer)
				{
					$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['name'] = $manufacturer['name'];
					if (!isset($filterBlock['values'][(int)$manufacturer['id_manufacturer']]['n']))
						$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['n'] = 0;					
					foreach ($productsManuf AS $product)
						if ($product['id_manufacturer'] == $manufacturer['id_manufacturer'])
							$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['n']++;
					if (isset($selectedFilters['manufacturer']) AND in_array($manufacturer['id_manufacturer'], $selectedFilters['manufacturer']))
						$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['checked'] = true;
				}
			}
		}
		
		$nFilters = 0;
		foreach ($selectedFilters AS $filters)
			$nFilters += sizeof($filters);
		
		$smarty->assign(array(
		'layered_use_checkboxes' => (int)Configuration::get('PS_LAYERED_NAVIGATION_CHECKBOXES'),
		'id_category_layered' => (int)$id_parent,
		'selected_filters' => $selectedFilters,
		'n_filters' => (int)$nFilters,
		'filters' => $filterBlocks));
		
		return $smarty->fetch(_PS_MODULE_DIR_.$this->name.'/blocklayered.tpl');
	}
	
	public function ajaxCall()
	{
		global $smarty, $cookie;

		/* Analyze all the filters selected by the user and store them into a tab */
		$filters = array('category' => array(), 'manufacturer' => array(), 'quantity' => array(), 'condition' => array());
		foreach ($_GET AS $key => $value)
			if (substr($key, 0, 8) == 'layered_')
			{
				preg_match('/^(.*)_[0-9|new|used|refurbished]+$/', substr($key, 8, strlen($key) - 8), $res);
				if (isset($res[1]))
				{
					$tmpTab = explode('_', $value);
					$value = $tmpTab[0];
					$id_key = false;
					if (isset($tmpTab[1]))
						$id_key = $tmpTab[1];
					if ($res[1] == 'condition' AND in_array($value, array('new', 'used', 'refurbished')))
						$filters['condition'][] = $value;
					elseif ($res[1] == 'quantity' AND (!$value OR $value == 1))
						$filters['quantity'][] = $value;
					elseif (in_array($res[1], array('id_attribute_group', 'category', 'id_feature', 'manufacturer')))
					{
						if (!isset($filters[$res[1].($id_key ? '_'.$id_key : '')]))
							$filters[$res[1].($id_key ? '_'.$id_key : '')] = array();
						$filters[$res[1].($id_key ? '_'.$id_key : '')][] = (int)$value;
					}
				}
			}
		
		$queryFilters = '';
		foreach ($filters AS $key => $filterValues)
		{
			if (!sizeof($filterValues))
				continue;

			preg_match('/^(.*[^_0-9])/', $key, $res);
			$key = $res[1];
			
			switch ($key)
			{
				case 'id_feature':
					$queryFilters .= ' AND (';
					foreach ($filterValues AS $filterValue)
						$queryFilters .= 'lc.f'.(int)$filterValue.' = 1 OR ';
					$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;
				
				case 'id_attribute_group':
					$queryFilters .= ' AND (';
					foreach ($filterValues AS $filterValue)
						$queryFilters .= 'lc.a'.(int)$filterValue.' = 1 OR ';
					$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;
				
				case 'category':
					$queryFilters .= ' AND (lc.c'.(int)Tools::getValue('id_category_layered').' = 1 OR ';
					foreach ($filters['category'] AS $id_category)
					{
						$category = new Category((int)$id_category);
						if (Validate::isLoadedObject($category))
						{
							$subCategories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
							SELECT c.id_category, c.id_parent
							FROM '._DB_PREFIX_.'category c
							WHERE c.nleft >= '.(int)$category->nleft.' and c.nright <= '.(int)$category->nright.' AND c.active = 1
							ORDER BY c.position ASC');
					
							foreach ($subCategories AS $subCategory)
								$queryFilters .= 'lc.c'.(int)$subCategory['id_category'].' = 1 OR ';
						}						
					}
					$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;
				
				case 'quantity':
					if (sizeof($filters['quantity']) == 2)
						break;
					$queryFilters .= ' AND p.quantity '.(!$filters['quantity'][0] ? '=' : '>').' 0';
				break;
				
				case 'manufacturer':
					$queryFilters .= ' AND p.id_manufacturer IN ('.implode($filters['manufacturer'], ',').')';
				break;
					
				case 'condition':
					if (sizeof($filters['condition']) == 3)
						break;
					$queryFilters .= ' AND p.condition IN (';
					foreach ($filters['condition'] AS $cond)
						$queryFilters .= '\''.$cond.'\',';
					$queryFilters = rtrim($queryFilters, ',').')';
				break;
			}
		}
		
		if (!sizeof($filters['category']))
			$queryFilters .= ' AND (lc.c'.(int)Tools::getValue('id_category_layered').' = 1)';
		$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.id_product, p.out_of_stock, p.available_for_order, p.quantity, p.id_category_default, p.customizable, p.show_price,
		p.ean13, pl.available_later, pl.description_short, pl.link_rewrite, pl.name, i.id_image, il.legend,  m.name manufacturer_name,
		DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new
		FROM '._DB_PREFIX_.'layered_cache lc
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = lc.id_product)
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)
		LEFT JOIN '._DB_PREFIX_.'image_lang il ON (i.id_image = il.id_image AND il.id_lang = '.(int)($cookie->id_lang).')
		LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
		WHERE pl.id_lang = '.(int)$cookie->id_lang.$queryFilters);

		$products = Product::getProductsProperties((int)$cookie->id_lang, $products);

		$smarty->assign('products', $products);

		/* We are sending an array in jSon to the .js controller, it will update both the filters and the products zones */
		return '<div id="layered_ajax_column">'.$this->generateFilters($filters).'</div><div id="layered_ajax_products">'.$smarty->fetch(_PS_THEME_DIR_.'product-list.tpl').'</div>';
	}
	
	public function rebuildLayeredStructure()
	{
		@set_time_limit(0);
		@ini_set('memory_limit', '64M');

		/* Delete and re-create the products cache table */
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_cache');
		$createTable = 'CREATE TABLE `'._DB_PREFIX_.'layered_cache` (`id_product` INT UNSIGNED NOT NULL,';
		$confValue = 'id_product,';
		
		/* Add the missing feature values columns */
		$featureValues = Db::getInstance()->ExecuteS('
		SELECT fv.id_feature_value
		FROM '._DB_PREFIX_.'feature_product fp
		LEFT JOIN '._DB_PREFIX_.'feature_value fv ON (fv.id_feature_value = fp.id_feature_value)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product)
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND p.active = 1
		GROUP BY fv.id_feature_value');
		
		/* Add the missing attribute values columns */
		$attributeValues = Db::getInstance()->ExecuteS('
		SELECT pac.id_attribute
		FROM '._DB_PREFIX_.'product_attribute_combination pac
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = pa.id_product)
		WHERE p.active = 1
		GROUP BY pac.id_attribute');
		
		/* Add the missing categories columns */
		$categories = Db::getInstance()->ExecuteS('
		SELECT cp.id_category
		FROM '._DB_PREFIX_.'category_product cp
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = cp.id_product)
		WHERE p.active = 1 AND c.active = 1
		GROUP BY cp.id_category');
		
		foreach ($featureValues AS $featureValue)
		{
			$createTable .= '`f'.(int)$featureValue['id_feature_value'].'` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',';
			$confValue .= 'f'.(int)$featureValue['id_feature_value'].',';
		}
		
		foreach ($attributeValues AS $attributeValue)
		{
			$createTable .= '`a'.(int)$attributeValue['id_attribute'].'` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',';
			$confValue .= 'a'.(int)$attributeValue['id_attribute'].',';
		}
		
		foreach ($categories AS $category)
		{
			$createTable .= '`c'.(int)$category['id_category'].'` TINYINT(1) UNSIGNED NOT NULL DEFAULT \'0\',';
			$confValue .= 'c'.(int)$category['id_category'].',';
		}

		Configuration::updateValue('PS_LAYERED_COLUMNS', rtrim($confValue, ','));
		Db::getInstance()->Execute($createTable.' PRIMARY KEY (`id_product`)) ENGINE=MyISAM CHARSET=latin1');
		
		/* Delete and re-create the layered categories table */
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_category');
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_category` (
		`id_layered_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_category` INT(10) UNSIGNED NOT NULL,
		`id_value` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
		`type` ENUM(\'category\',\'id_feature\',\'id_attribute_group\',\'quantity\',\'condition\',\'manufacturer\') NOT NULL,
		`position` INT(10) UNSIGNED NOT NULL,
		PRIMARY KEY (`id_layered_category`),
		KEY `id_category` (`id_category`,`type`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;');
	}
	
	public function rebuildLayeredCache($productsIds = array(), $categoriesIds = array())
	{
		@set_time_limit(0);
		@ini_set('memory_limit', '64M');		
		$db = Db::getInstance();
		$nCategories = array();
		$doneCategories = array();
		
		$attributeGroups = Db::getInstance()->ExecuteS('
		SELECT a.id_attribute, a.id_attribute_group
		FROM '._DB_PREFIX_.'attribute a
		LEFT JOIN '._DB_PREFIX_.'product_attribute_combination pac ON (pac.id_attribute = a.id_attribute)
		LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product_attribute = pac.id_product_attribute)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = pa.id_product)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		WHERE c.active = 1'.(sizeof($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').' AND p.active = 1'.(sizeof($productsIds) ? ' AND p.id_product IN ('.implode(',', $productsIds).')' : ''), false);
		
		$attributeGroupsById = array();
		while ($row = $db->nextRow($attributeGroups))
			$attributeGroupsById[(int)$row['id_attribute']] = (int)$row['id_attribute_group'];
			
		$features = Db::getInstance()->ExecuteS('
		SELECT fv.id_feature_value, fv.id_feature
		FROM '._DB_PREFIX_.'feature_value fv
		LEFT JOIN '._DB_PREFIX_.'feature_product fp ON (fp.id_feature_value = fv.id_feature_value)
		LEFT JOIN '._DB_PREFIX_.'product p ON (p.id_product = fp.id_product)
		LEFT JOIN '._DB_PREFIX_.'category_product cp ON (cp.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category)
		WHERE (fv.custom IS NULL OR fv.custom = 0) AND c.active = 1'.(sizeof($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').' AND p.active = 1'.(sizeof($productsIds) ? ' AND p.id_product IN ('.implode(',', $productsIds).')' : ''), false);
		
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
		WHERE c.active = 1'.(sizeof($categoriesIds) ? ' AND cp.id_category IN ('.implode(',', $categoriesIds).')' : '').' AND p.active = 1'.(sizeof($productsIds) ? ' AND p.id_product IN ('.implode(',', $productsIds).')' : '').' AND (fv.custom IS NULL OR fv.custom = 0)
		GROUP BY p.id_product', false);
		
		/* Get all the columns to fill */
		$columns = explode(',', Configuration::get('PS_LAYERED_COLUMNS'));

		/* We do not need to build the query for products when we are just updating the layered_category table */
		if (!sizeof($categoriesIds))
		{
			$query = 'INSERT INTO `'._DB_PREFIX_.'layered_cache` VALUES ';
			$values = '';
		}
		while ($product = $db->nextRow($result))
		{
			$a = $c = $f = array();
			if (!empty($product['attributes']))
				$a = array_flip(explode(',', $product['attributes']));
			if (!empty($product['categories']))
				$c = array_flip(explode(',', $product['categories']));
			if (!empty($product['features']))
				$f = array_flip(explode(',', $product['features']));

			/* We do not need to build the query for products when we are just updating the layered_category table */
			if (!sizeof($categoriesIds))
			{
				$values .= '(';
				$n = 0;
				foreach ($columns AS $column)
				{
					if (!$n)
						$values .= (int)$product['id_product'].',';
					else
					{
						if (isset(${$column{0}}[ltrim($column, $column{0})]))
							$values .= '1,';
						else
							$values .= '0,';
					}
					$n++;
				}
				$values = rtrim($values, ',').'),';
			}

			$queryCategory = 'INSERT INTO '._DB_PREFIX_.'layered_category (id_category, id_value, type, position) VALUES ';
			$toInsert = false;
			foreach ($c AS $id_category => $category)
			{
				if (!isset($nCategories[(int)$id_category]))
					$nCategories[(int)$id_category] = 1;
				if (!isset($doneCategories[(int)$id_category]['cat']))
				{
					$doneCategories[(int)$id_category]['cat'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'category\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				foreach ($a AS $kAttribute => $attribute)
					if (!isset($doneCategories[(int)$id_category]['a'.(int)$attributeGroupsById[(int)$kAttribute]]))
					{
						$doneCategories[(int)$id_category]['a'.(int)$attributeGroupsById[(int)$kAttribute]] = true;
						$queryCategory .= '('.(int)$id_category.','.(int)$attributeGroupsById[(int)$kAttribute].',\'id_attribute_group\','.(int)$nCategories[(int)$id_category]++.'),';
						$toInsert = true;
					}
				foreach ($f AS $kFeature => $feature)
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
			}
			if ($toInsert)
				Db::getInstance()->Execute(rtrim($queryCategory, ','));
		}
		
		/* We do not need to build the query for products when we are just updating the layered_category table */
		if (!sizeof($categoriesIds))
			$db->Execute($query.rtrim($values, ','));
	}
	
	function filterProducts($products, $selectedFilters, $excludeType = false)
	{
		$productsToKeep = array();
		$filterByLetter = array('id_attribute_group' => 'a', 'id_feature' => 'f', 'category' => 'c', 'manufacturer' => 'id_manufacturer', 'quantity' => 'quantity', 'condition' => 'condition');
		foreach ($selectedFilters AS $type => $filters)
		{		
			if ($type == $excludeType OR !sizeof($filters))
				continue;
			else
			{			
				$type = preg_match('/^(.*[^_0-9])/', $type, $res);
				$type = $res[1];
				
				switch ($type)
				{
					case 'id_attribute_group':
					case 'id_feature':
					case 'category':
						foreach ($products AS $k => $product)
							foreach ($filters AS $filter)
								if (isset($product[$filterByLetter[$type].(int)$filter]))
									$productsToKeep[] = (int)$k;
						break;
						
					case 'manufacturer':
					case 'condition':
					case 'quantity':
						foreach ($products AS $k => $product)
							foreach ($filters AS $filter)
								if ($product[$filterByLetter[$type]] == $filter)
									$productsToKeep[] = (int)$k;
						break;
				}
				
				foreach ($products AS $k => $product)
					if (!in_array($k, $productsToKeep))
						unset($products[(int)$k]);
				$productsToKeep = array();
			}
		}
		
		return $products;
	}
}