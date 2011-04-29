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
	
	private $products ;
	
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
		return $this->generateFiltersBlock($this->getSelectedFilters());
	}
	
	public function hookRightColumn($params)
	{
		return $this->hookLeftColumn($params);
	}
	
	public function hookHeader($params)
	{
		Tools::addJS(($this->_path).'blocklayered.js');
		Tools::addJS(_PS_JS_DIR_.'jquery/jquery-ui-1.8.10.custom.min.js');
		Tools::addCSS(($this->_path).'blocklayered.css', 'all');
		Tools::addCSS(_PS_CSS_DIR_.'jquery-ui-1.8.10.custom.css', 'all');
		
	}
		
	public function hookCategoryAddition($params)
	{
		$this->rebuildLayeredCache(array(), array((int)$params['category']->id));
	}
	
	public function hookCategoryUpdate($params)
	{
		/* The category status might (active, inactive) have changed, we have to update the layered cache table structure */
		/*
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
*/
	}
	
	public function hookCategoryDeletion($params)
	{
		Db::getInstance()->Execute('DELETE FROM '._DB_PREFIX_.'layered_category WHERE id_category = '.(int)$params['category']->id);
	}
	
	public function getContent()
	{
		$html = '';
		if (Tools::isSubmit('submitLayeredCache'))
		{
			$this->rebuildLayeredStructure();
			$this->rebuildLayeredCache();
			
			$html .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				'.$this->l('Layered navigation database was initialized successfully').'
			</div>';
		}
		elseif (Tools::isSubmit('submitLayeredSettings'))
		{
			$html .= '
			<div class="conf confirm">
				<img src="../img/admin/ok.gif" alt="" title="" />
				'.$this->l('Settings saved successfully').'
			</div>';
		}
		
		$html .= '
		<h2>'.$this->l('Layered navigation').'</h2>
		<p class="warning" style="font-weight: bold;"><img src="../img/admin/information.png" alt="" /> '.$this->l('This module is in beta version and will be improved').'</p><br />
		<fieldset class="width2">
			<legend><img src="../img/admin/asterisk.gif" alt="" />'.$this->l('10 upcoming improvements').'</legend>
			<ol>				
				<li>'.$this->l('Real-time refresh of the cache table').' <img src="../img/admin/enabled.gif" alt="" /></li>
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
						<p style="color: red; font-weight: bold;">'.$this->l('Before using this module for the first time you have to initialize the cache').'</p>
					<p><b>'.$this->l('Warning: This could take several minutes.').'</b><br /><br />
					'.$this->l('If you do not, this cache table might become larger and larger (less efficient), and all the new choices (attributes, features) will not be offered to your visitors.').'</p>
				</div>
				<p style="text-align: center;"><input type="submit" class="button" name="submitLayeredCache" value="'.$this->l('Initialize the layered navigation database').'" /></p>
			</form>
		</fieldset>';
		return $html;
	}
	
	private function getSelectedFilters()
	{
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;
		
		/* Analyze all the filters selected by the user and store them into a tab */
		$selectedFilters = array('category' => array(), 'manufacturer' => array(), 'quantity' => array(), 'condition' => array());
		foreach ($_GET AS $key => $value)
		{
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
					if ($res[1] == 'condition' AND in_array($value, array('new', 'used', 'refurbished')))
						$selectedFilters['condition'][] = $value;
					elseif ($res[1] == 'quantity' AND (!$value OR $value == 1))
						$selectedFilters['quantity'][] = $value;
					elseif (in_array($res[1], array('id_attribute_group', 'category', 'id_feature', 'manufacturer')))
					{
						if (!isset($selectedFilters[$res[1].($id_key ? '_'.$id_key : '')]))
							$selectedFilters[$res[1].($id_key ? '_'.$id_key : '')] = array();
						$selectedFilters[$res[1].($id_key ? '_'.$id_key : '')][] = (int)$value;
					}
					elseif (in_array($res[1], array('weight')))
						$selectedFilters[$res[1]] = $tmpTab;
				}
			}
		}
		return $selectedFilters;
	}
	
	public function getProductByFilters($selectedFilters = array())
	{
		global $cookie;
		
		if (!empty($this->products))
			return $this->products;
		
		/* If the current category isn't defined of if it's homepage, we have nothing to display */
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;
		
		if (!sizeof($selectedFilters['category']))
			$selectedFilters['category'][] = $id_parent;
		
		$queryFilters = '';
		
		foreach ($selectedFilters AS $key => $filterValues)
		{
			if (!sizeof($filterValues))
				continue;

			preg_match('/^(.*[^_0-9])/', $key, $res);
			$key = $res[1];
			
			switch ($key)
			{
				case 'id_feature':
					$queryFilters .= ' AND p.id_product IN ( SELECT id_product FROM '._DB_PREFIX_.'feature_product fp WHERE ';
					foreach ($filterValues AS $filterValue)
						$queryFilters .= 'fp.`id_feature_value` = '.(int)$filterValue.' OR ';
					$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;
				case 'id_attribute_group':
					$queryFilters .= ' AND p.id_product IN ( SELECT pa.`id_product`
										FROM `'._DB_PREFIX_.'product_attribute_combination` pac
										LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
										ON (pa.`id_product_attribute` = pac.`id_product_attribute`) WHERE ';
										
					foreach ($filterValues AS $filterValue)
						$queryFilters .= 'pac.`id_attribute` = '.(int)$filterValue.' OR ';
					$queryFilters = rtrim($queryFilters, 'OR ').')';
				break;
				case 'category':
					$parent = new Category($id_parent);
					if (!sizeof($selectedFilters['category']))
                         $queryFilters .= ' AND p.id_product IN ( SELECT id_product FROM '._DB_PREFIX_.'category_product cp 
                         LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category) 
                         WHERE 1 AND c.nleft >= parent->nleft AND c.nright <= parent->nright';
					else
					{
						$queryFilters .= ' AND p.id_product IN ( SELECT id_product FROM '._DB_PREFIX_.'category_product cp WHERE 1 AND cp.`id_category` = '.(int)$id_parent;					
						if (sizeof($selectedFilters['category']))
							$queryFilters .= ' OR ';
						foreach ($selectedFilters['category'] AS $id_category)
							$queryFilters .= 'cp.`id_category` = '.(int)$id_category.' OR ';
						$queryFilters = rtrim($queryFilters, 'OR ').')';
					}
				break;
				
				case 'quantity':
					if (sizeof($selectedFilters['quantity']) == 2)
						break;
					$queryFilters .= ' AND p.quantity '.(!$selectedFilters['quantity'][0] ? '=' : '>').' 0';
				break;
				
				case 'manufacturer':
					$queryFilters .= ' AND p.id_manufacturer IN ('.implode($selectedFilters['manufacturer'], ',').')';
				break;
					
				case 'condition':
					if (sizeof($selectedFilters['condition']) == 3)
						break;
					$queryFilters .= ' AND p.condition IN (';
					foreach ($selectedFilters['condition'] AS $cond)
						$queryFilters .= '\''.$cond.'\',';
					$queryFilters = rtrim($queryFilters, ',').')';
				break;
				
				case 'weight':
					$queryFilters .= ' AND p.`weight` BETWEEN '.(float)$selectedFilters['weight'][0].' AND '.(float)$selectedFilters['weight'][1];
				break;
			}
		}
		//id_category_layered = current displayed category
		if (!sizeof($selectedFilters['category']))
			$queryFilters .= ' AND p.id_product IN (
				SELECT id_product FROM '._DB_PREFIX_.'category_product cp 
				WHERE cp.`id_category` = '.(int)$id_parent.')';
		
		$sql = '
		SELECT p.id_product, p.out_of_stock, p.available_for_order, p.quantity, p.id_category_default, p.customizable, p.show_price, p.`weight`,
		p.ean13, pl.available_later, pl.description_short, pl.link_rewrite, pl.name, i.id_image, il.legend,  m.name manufacturer_name, p.condition, p.id_manufacturer,
		DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new
		FROM '._DB_PREFIX_.'product p
		LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (pl.id_product = p.id_product)
		LEFT JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product AND i.cover = 1)
		LEFT JOIN '._DB_PREFIX_.'image_lang il ON (i.id_image = il.id_image AND il.id_lang = '.(int)($cookie->id_lang).')
		LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
		WHERE pl.id_lang = '.(int)$cookie->id_lang.$queryFilters;
			
		$this->products = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		
		return $this->products;
	}
	
	public function generateFiltersBlock($selectedFilters = array())
	{
		
		global $smarty, $link, $cookie;

		/* If the current category isn't defined of if it's homepage, we have nothing to display */
		$id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', 1));
		if ($id_parent == 1)
			return;

		/* First we need to get all subcategories of current category */
		$category = new Category((int)$id_parent);
		
		$subCategories = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.id_category, c.id_parent, cl.name
		FROM '._DB_PREFIX_.'category c
		LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category)
		WHERE c.nleft > '.(int)$category->nleft.' and c.nright <= '.(int)$category->nright.' AND c.active = 1 AND c.id_parent = '.(int)$category->id.' AND cl.id_lang = '.(int)$cookie->id_lang.'
		ORDER BY c.position ASC');
		
		$whereC = ' cp.`id_category` = '.(int)$id_parent.' OR ';
		foreach ($subCategories AS $subcategory)
				$whereC .= ' cp.`id_category` = '.(int)$subcategory['id_category'].' OR ';

		$whereC = rtrim($whereC, 'OR ').')';
		$productsSQL = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT p.`id_product`, p.`condition`, p.`id_manufacturer`, p.`quantity`, p.`weight`,
		(SELECT GROUP_CONCAT(`id_category`) FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.`id_product` = p.`id_product`) as ids_cat,
			(SELECT GROUP_CONCAT(`id_feature_value`) FROM `'._DB_PREFIX_.'feature_product` fp WHERE fp.`id_product` = p.`id_product`) as ids_feat,
			(SELECT GROUP_CONCAT(DISTINCT(pac.`id_attribute`)) 
				FROM `'._DB_PREFIX_.'product_attribute_combination` pac 
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`) 
				WHERE pa.`id_product` = p.`id_product` ) as ids_attr
		FROM '._DB_PREFIX_.'product p 
		WHERE p.`active` = 1 AND p.`id_product` IN ( SELECT id_product FROM `'._DB_PREFIX_.'category_product` cp WHERE'.$whereC, false);

		$products = array();
		$db = Db::getInstance();
		$weight = array();
		while ($product = $db->nextRow($productsSQL))
		{
			$row = array();
			foreach ($product AS $key => $value)
			{
				if($key == 'ids_feat')
					$row['f'] = explode(',', $value);
				if($key == 'ids_attr')
					$row['a'] = explode(',', $value);
				if($key == 'ids_cat')
					$row['c'] = explode(',', $value);
				if($key == 'weight')
					$weight[] = $value;
			}
			
			$row['id_manufacturer'] = (int)$product['id_manufacturer'];
			$row['quantity'] = (bool)$product['quantity'];
			$row['condition'] = $product['condition'];
			$row['weight'] = $product['weight'];
			$products[(int)$product['id_product']] = $row;
		}

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

					break;

				case 'id_attribute_group':
					$a[] = (int)$filter['id_value'];
					
					$filterBlocks[(int)$filter['position']]['SQLvalues'] = Db::getInstance()->ExecuteS('
					SELECT al.id_attribute, al.name, a.color
					FROM '._DB_PREFIX_.'attribute a
					LEFT JOIN '._DB_PREFIX_.'attribute_lang al ON (al.id_attribute = a.id_attribute)
					WHERE a.id_attribute_group = '.(int)$filterBlocks[(int)$filter['position']]['id_key'].' AND al.id_lang = '.(int)$cookie->id_lang);					
					break;
			}
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
		
		/* Get the attribute block names & values */
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
				
				$c = array();
				foreach ($subCategories AS $subCat)
				{
					$c[] = (int)$subCat['id_category'];
					$filterBlock['values'][(int)$subCat['id_category']]['name'] = $subCat['name'];
					
					//init the number of product in this category
					if (!isset($filterBlock['values'][(int)$subCat['id_category']]['nbr']))
						$filterBlock['values'][(int)$subCat['id_category']]['nbr'] = 0;

					//check if the category is selected and set to true
					if (isset($selectedFilters['category']) AND in_array($subCat['id_category'], $selectedFilters['category']))
						$filterBlock['values'][(int)$subCat['id_category']]['checked'] = true;
				}
				
				$productCat = $this->filterProducts($products, $selectedFilters, 'category');
				
				//count nbr product in category
				foreach ($c AS $idSubCategory)
					foreach ($productCat AS $product)
						if(in_array($idSubCategory, $product['c']))
							$filterBlock['values'][(int)$idSubCategory]['nbr']++;
			
			}
			elseif ($filterBlock['type_lite'] == 'id_feature')
			{
				$filterBlock['name'] = $fNameByID[(int)$filterBlock['id_key']];
				$filterBlock['values'] = array();
				
				$productFeat = $this->filterProducts($products, $selectedFilters, 'id_feature_'.(int)$filterBlock['id_key']);
					
				foreach ($filterBlock['SQLvalues'] AS $value)
				{	
					foreach ($productFeat AS $product)
					{
						if (in_array($value['id_feature_value'], $product['f']))
						{
							$filterBlock['values'][(int)$value['id_feature_value']]['name'] = $value['value'];
							if (!isset($filterBlock['values'][(int)$value['id_feature_value']]['nbr']))
								$filterBlock['values'][(int)$value['id_feature_value']]['nbr'] = 0;
							$filterBlock['values'][(int)$value['id_feature_value']]['nbr']++;							
						}
						if (in_array($value['id_feature_value'], $product['f']) AND !isset($filterBlock['values'][(int)$value['id_feature_value']]))
						{
							$filterBlock['values'][(int)$value['id_feature_value']]['name'] = $value['value'];
							$filterBlock['values'][(int)$value['id_feature_value']]['nbr'] = 0;
						}
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
					{
						if(in_array($value['id_attribute'], $product['a']))
						{
							$filterBlock['values'][(int)$value['id_attribute']]['name'] = $value['name'];
							$filterBlock['values'][(int)$value['id_attribute']]['color'] = $value['color'];
							if (!isset($filterBlock['values'][(int)$value['id_attribute']]['nbr']))
								$filterBlock['values'][(int)$value['id_attribute']]['nbr'] = 0;
								$filterBlock['values'][(int)$value['id_attribute']]['nbr']++;							
						}
						if (isset($product['a'.$value['id_attribute']]) AND !isset($filterBlock['values'][(int)$value['id_attribute']]))
						{
							$filterBlock['values'][(int)$value['id_attribute']]['name'] = $value['name'];
							$filterBlock['values'][(int)$value['id_attribute']]['nbr'] = 0;
						}
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
				'new' => array('name' => $this->l('New'), 'nbr' => 0), 
				'used' => array('name' => $this->l('Used'), 'nbr' => 0), 
				'refurbished' => array('name' => $this->l('Refurbished'), 'nbr' => 0));
				
				$productCond = $this->filterProducts($products, $selectedFilters, 'condition');
				
				foreach ($filterBlock['values'] AS $conditionKey => &$condition)
				{
					foreach ($productCond AS $product)
						if ($product['condition'] == $conditionKey)
							$condition['nbr']++;
					if (isset($selectedFilters['condition']) AND in_array($conditionKey, $selectedFilters['condition']))
						$condition['checked'] = true;
				}
			}
			elseif ($filterBlock['type_lite'] == 'quantity')
			{
				$filterBlock['name'] = $this->l('Availability');
				$filterBlock['values'] = array(
				'1' => array('name' => $this->l('In stock'), 'nbr' => 0),
				'0' => array('name' => $this->l('Not available'), 'nbr' => 0));				
				
				$productQuant = $this->filterProducts($products, $selectedFilters, 'quantity');
				
				foreach ($filterBlock['values'] AS $quantKey => &$quantity)
				{
					foreach ($productQuant AS $product)
						if ($product['quantity'] == $quantKey)
							$quantity['nbr']++;
					if (isset($selectedFilters['quantity']) AND in_array($quantKey, $selectedFilters['quantity']))
						$quantity['checked'] = true;
				}
			}
			elseif ($filterBlock['type_lite'] == 'manufacturer')
			{
				$filterBlock['name'] = $this->l('Manufacturer');
				
				$man = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT DISTINCT(p.id_manufacturer), m.name
				FROM '._DB_PREFIX_.'product p			
				LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				WHERE p.id_product IN ('.implode(',', array_keys($products)).') AND p.id_manufacturer != 0');
				
				$productsManuf = $this->filterProducts($products, $selectedFilters, 'manufacturer');

				foreach ($man AS $manufacturer)
				{
					$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['name'] = $manufacturer['name'];
					if (!isset($filterBlock['values'][(int)$manufacturer['id_manufacturer']]['nbr']))
						$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['nbr'] = 0;					
					foreach ($productsManuf AS $product)
						if ($product['id_manufacturer'] == $manufacturer['id_manufacturer'])
							$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['nbr']++;
					if (isset($selectedFilters['manufacturer']) AND in_array($manufacturer['id_manufacturer'], $selectedFilters['manufacturer']))
						$filterBlock['values'][(int)$manufacturer['id_manufacturer']]['checked'] = true;
				}
			}
			elseif ($filterBlock['type_lite'] == 'weight')
			{
				if (max($weight) != min($weight))
				{
					$filterBlock['name'] = $this->l('Weight');
					$filterBlock['slider'] = true;
					$filterBlock['max'] = max($weight);
					$filterBlock['min'] = min($weight);
					if (isset($selectedFilters['weight']))
						$filterBlock['values'] = array($selectedFilters['weight'][0], $selectedFilters['weight'][1]);
					else
						$filterBlock['values'] = array(min($weight), max($weight));
					$filterBlock['unit'] = Configuration::get('PS_WEIGHT_UNIT');
				}
				else
					unset($selectedFilters['weight']);
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
		'nbr_filterBlocks' => sizeof($filterBlocks),
		'filters' => $filterBlocks));
				
		return $smarty->fetch(_PS_MODULE_DIR_.$this->name.'/blocklayered.tpl');
	}
	
	public function ajaxCall()
	{
		global $smarty, $cookie;

		$selectedFilters = $this->getSelectedFilters();
						
		$products = $this->getProductByFilters($selectedFilters);
		
		$products = Product::getProductsProperties((int)$cookie->id_lang, $products);
							
		$smarty->assign('products', $products);
		
		/* We are sending an array in jSon to the .js controller, it will update both the filters and the products zones */
		return Tools::jsonEncode(array(
			'filtersBlock' => $this->generateFiltersBlock($selectedFilters),
			'productList' => $smarty->fetch(_PS_THEME_DIR_.'product-list.tpl')
		));
	//	return '<div id="layered_ajax_column">'.$this->generateFiltersBlock($selectedFilters).'</div><div id="layered_ajax_products">'.$smarty->fetch(_PS_THEME_DIR_.'product-list.tpl').'</div>';	
	}
	
	public function rebuildLayeredStructure()
	{
		@set_time_limit(0);
		@ini_set('memory_limit', '64M');

		/* Delete and re-create the layered categories table */
		Db::getInstance()->Execute('DROP TABLE IF EXISTS '._DB_PREFIX_.'layered_category');
		Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'layered_category` (
		`id_layered_category` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_category` INT(10) UNSIGNED NOT NULL,
		`id_value` INT(10) UNSIGNED NOT NULL DEFAULT \'0\',
		`type` ENUM(\'category\',\'id_feature\',\'id_attribute_group\',\'quantity\',\'condition\',\'manufacturer\',\'weight\') NOT NULL,
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
				if (!isset($doneCategories[(int)$id_category]['w']))
				{
					$doneCategories[(int)$id_category]['w'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'weight\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
				/*
if (!isset($doneCategories[(int)$id_category]['p']))
				{
					$doneCategories[(int)$id_category]['p'] = true;
					$queryCategory .= '('.(int)$id_category.',NULL,\'price\','.(int)$nCategories[(int)$id_category]++.'),';
					$toInsert = true;
				}
*/
			}
			if ($toInsert)
				Db::getInstance()->Execute(rtrim($queryCategory, ','));
		}
	}
	
	function filterProducts($products, $selectedFilters, $excludeType = false)
	{
		$productsToKeep = array();
		$filterByLetter = array('id_attribute_group' => 'a', 'id_feature' => 'f', 'category' => 'c', 'manufacturer' => 'id_manufacturer',
								'quantity' => 'quantity', 'condition' => 'condition', 'weight' => 'weight', 'price' => 'price');
		
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
				case 'category':
						foreach ($products AS $k => $product)
							if($filter = Tools::getValue('id_category_layered'))
								$productsToKeep[] = (int)$k;
						//don't break me
					case 'id_attribute_group':
					case 'id_feature':
						foreach ($products AS $k => $product)
							foreach ($filters AS $filter)
								if (in_array($filter, $product[$filterByLetter[$type]]))
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
					case 'weight':
					case 'price':
						$min = $filters[0];
						$max = $filters[1]; 
						foreach ($products AS $k => $product)
							if((float)$min <= (float)$product[$filterByLetter[$type]] AND (float)$product[$filterByLetter[$type]] <= (float)$max)
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
