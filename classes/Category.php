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
*  @version  Release: $Revision: 1.4 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CategoryCore extends ObjectModel
{
	public 		$id;

	/** @var integer category ID */
	public 		$id_category;

	/** @var string Name */
	public 		$name;

	/** @var boolean Status for display */
	public 		$active = 1;

	/** @var  integer category position */
	public 		$position;

	/** @var string Description */
	public 		$description;

	/** @var integer Parent category ID */
	public 		$id_parent;

	/** @var integer Parents number */
	public 		$level_depth;

	/** @var integer Nested tree model "left" value */
	public 		$nleft;

	/** @var integer Nested tree model "right" value */
	public 		$nright;

	/** @var string string used in rewrited URL */
	public 		$link_rewrite;

	/** @var string Meta title */
	public 		$meta_title;

	/** @var string Meta keywords */
	public 		$meta_keywords;

	/** @var string Meta description */
	public 		$meta_description;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	protected static $_links = array();

	protected $tables = array ('category', 'category_lang');

	protected 	$fieldsRequired = array('active');
 	protected 	$fieldsSize = array('active' => 1);
 	protected 	$fieldsValidate = array('nleft' => 'isUnsignedInt', 'nright' => 'isUnsignedInt', 'level_depth' => 'isUnsignedInt', 'active' => 'isBool');
	protected 	$fieldsRequiredLang = array('name', 'link_rewrite');
 	protected 	$fieldsSizeLang = array('name' => 64, 'link_rewrite' => 64, 'meta_title' => 128, 'meta_description' => 255, 'meta_keywords' => 255);
 	protected 	$fieldsValidateLang = array('name' => 'isCatalogName', 'link_rewrite' => 'isLinkRewrite', 'description' => 'isCleanHtml',
											'meta_title' => 'isGenericName', 'meta_description' => 'isGenericName', 'meta_keywords' => 'isGenericName');

	protected 	$table = 'category';
	protected 	$identifier = 'id_category';

	/** @var string id_image is the category ID when an image exists and 'default' otherwise */
	public		$id_image = 'default';

	protected	$webserviceParameters = array(
		'objectsNodeName' => 'categories',
		'fields' => array(
			'id_parent' => array('xlink_resource'=> 'categories'),
		),
		'associations' => array(
				'categories' => array('getter' => 'getChildrenWs', 'resource' => 'category', ),
				'products' => array('getter' => 'getProductsWs', 'resource' => 'product', ),
				
			
		),
	);

	public function __construct($id_category = NULL, $id_lang = NULL)
	{
		parent::__construct($id_category, $id_lang);
		$this->id_image = ($this->id AND file_exists(_PS_CAT_IMG_DIR_.(int)($this->id).'.jpg')) ? (int)($this->id) : false;
	}

	public function getFields()
	{
		parent::validateFields();
		if (isset($this->id))
			$fields['id_category'] = (int)($this->id);
		$fields['active'] = (int)($this->active);
		$fields['id_parent'] = (int)($this->id_parent);
		$fields['position'] = (int)($this->position);
		$fields['level_depth'] = (int)($this->level_depth);
		$fields['nleft'] = (int)($this->nleft);
		$fields['nright'] = (int)($this->nright);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}

	/**
	  * Check then return multilingual fields for database interaction
	  *
	  * @return array Multilingual fields
	  */
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('name', 'description', 'link_rewrite', 'meta_title', 'meta_keywords', 'meta_description'));
	}

	public	function add($autodate = true, $nullValues = false)
	{
		$this->position = self::getLastPosition((int)(Tools::getValue('id_parent')));
		if (!isset($this->level_depth) OR $this->level_depth != 0)
			$this->level_depth = $this->calcLevelDepth();
		$ret = parent::add($autodate);
		if (!isset($this->doNotRegenerateNTree) OR !$this->doNotRegenerateNTree)
			self::regenerateEntireNtree();
		$this->updateGroup(Tools::getValue('groupBox'));
		Module::hookExec('categoryAddition', array('category' => $this));
		return $ret;
	}

	/**
	 * update category positions in parent
	 * 
	 * @param mixed $nullValues 
	 * @return void
	 */
	public function update($nullValues = false)
	{
		$this->level_depth = $this->calcLevelDepth();
		$this->cleanPositions((int)$this->id_parent);
		$ret = parent::update($nullValues);
		if (!isset($this->doNotRegenerateNTree) OR !$this->doNotRegenerateNTree)
			self::regenerateEntireNtree();
		Module::hookExec('categoryUpdate', array('category' => $this));
		return $ret;
	}

	/**
	  * Recursive scan of subcategories
	  *
	  * @param integer $maxDepth Maximum depth of the tree (i.e. 2 => 3 levels depth)
 	  * @param integer $currentDepth specify the current depth in the tree (don't use it, only for rucursivity!)
	  * @param array $excludedIdsArray specify a list of ids to exclude of results
 	  * @param integer $idLang Specify the id of the language used
	  *
 	  * @return array Subcategories lite tree
	  */
	function recurseLiteCategTree($maxDepth = 3, $currentDepth = 0, $idLang = NULL, $excludedIdsArray = NULL)
	{
		global $link;

		$idLang = is_null($idLang) ? _USER_ID_LANG_ : (int)($idLang);

		$children = array();
		if (($maxDepth == 0 OR $currentDepth < $maxDepth) AND $subcats = $this->getSubCategories((int)$idLang, true) AND sizeof($subcats))
			foreach ($subcats AS &$subcat)
			{
				if (!$subcat['id_category'])
					break;
				elseif (!is_array($excludedIdsArray) || !in_array($subcat['id_category'], $excludedIdsArray))
				{
					$categ = new Category((int)$subcat['id_category'], (int)$idLang);
					$children[] = $categ->recurseLiteCategTree($maxDepth, $currentDepth + 1, (int)$idLang, $excludedIdsArray);
				}
			}

		return array(
			'id' => (int)$this->id_category,
			'link' => $link->getCategoryLink((int)$this->id, $this->link_rewrite),
			'name' => $this->name,
			'desc'=> $this->description,
			'children' => $children
		);
	}

	static public function recurseCategory($categories, $current, $id_category = 1, $id_selected = 1)
	{
		global $currentIndex;
		echo '<option value="'.$id_category.'"'.(($id_selected == $id_category) ? ' selected="selected"' : '').'>'.
		str_repeat('&nbsp;', $current['infos']['level_depth'] * 5).stripslashes($current['infos']['name']).'</option>';
		if (isset($categories[$id_category]))
			foreach ($categories[$id_category] AS $key => $row)
				self::recurseCategory($categories, $categories[$id_category][$key], $key, $id_selected);
	}


	/**
	  * Recursively add specified category childs to $toDelete array
	  *
	  * @param array &$toDelete Array reference where categories ID will be saved
	  * @param array $id_category Parent category ID
	  */
	protected function recursiveDelete(&$toDelete, $id_category)
	{
	 	if (!is_array($toDelete) OR !$id_category)
	 		die(Tools::displayError());

		$result = Db::getInstance()->ExecuteS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category`
		WHERE `id_parent` = '.(int)($id_category));
		foreach ($result AS $k => $row)
		{
			$toDelete[] = (int)($row['id_category']);
			$this->recursiveDelete($toDelete, (int)($row['id_category']));
		}
	}

	public function delete()
	{
		if ((int)($this->id) === 0 OR (int)($this->id) === 1) return false;

		$this->clearCache();

		/* Get childs categories */
		$toDelete = array((int)($this->id));
		$this->recursiveDelete($toDelete, (int)($this->id));
		$toDelete = array_unique($toDelete);

		/* Delete category and its child from database */
		$list = sizeof($toDelete) > 1 ?  implode(',', array_map('intval',$toDelete)) : (int)($this->id);
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE `id_category` IN ('.$list.')');
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE `id_category` IN ('.$list.')');
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_product` WHERE `id_category` IN ('.$list.')');
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_category` IN ('.$list.')');

		self::cleanPositions($this->id_parent);

		/* Delete categories images */
		require_once(_PS_ROOT_DIR_.'/images.inc.php');
		foreach ($toDelete AS $id_category)
			deleteImage((int)$id_category);

		/* Delete products which were not in others categories */
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_product`
		FROM `'._DB_PREFIX_.'product`
		WHERE `id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'category_product`)');
		foreach ($result as $p)
		{
			$product = new Product((int)$p['id_product']);
			if (Validate::isLoadedObject($product))
				$product->delete();
		}

		/* Set category default to 1 where categorie no more exists */
		$result = Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product`
		SET `id_category_default` = 1
		WHERE `id_category_default`
		NOT IN (SELECT `id_category` FROM `'._DB_PREFIX_.'category`)');

		/* Rebuild the nested tree */
		if (!isset($this->doNotRegenerateNTree) OR !$this->doNotRegenerateNTree)
			self::regenerateEntireNtree();

		Module::hookExec('categoryDeletion', array('category' => $this));
		return true;
	}

	/**
	 * Delete several categories from database
	 *
	 * return boolean Deletion result
	 */
	public function deleteSelection($categories)
	{
		$return = 1;
		foreach ($categories AS $id_category)
		{
			$category = new Category((int)($id_category));
			$return &= $category->delete();
		}
		return $return;
	}

	/**
	  * Get the depth level for the category
	  *
	  * @return integer Depth level
	  */
	public function calcLevelDepth()
	{
		/* Root category */
		if (!$this->id_parent)
			return 0;

		$parentCategory = new Category((int)($this->id_parent));
		if (!Validate::isLoadedObject($parentCategory))
			die('parent category does not exist');
		return $parentCategory->level_depth + 1;
	}

	/**
	  * Re-calculate the values of all branches of the nested tree
	  */
	public static function regenerateEntireNtree()
	{
		$categories = Db::getInstance()->ExecuteS('SELECT id_category, id_parent FROM '._DB_PREFIX_.'category ORDER BY id_category ASC');
		$categoriesArray = array();
		foreach ($categories AS $category)
			$categoriesArray[(int)$category['id_parent']]['subcategories'][(int)$category['id_category']] = 1;
			$n = 1;
		self::_subTree($categoriesArray, 1, $n);
	}

	protected static function _subTree(&$categories, $id_category, &$n)
	{
		$left = (int)$n++;
		if (isset($categories[(int)$id_category]['subcategories']))
			foreach ($categories[(int)$id_category]['subcategories'] AS $id_subcategory => $value)
				self::_subTree($categories, (int)$id_subcategory, $n);
		$right = (int)$n++;

		Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'category SET nleft = '.(int)$left.', nright = '.(int)$right.' WHERE id_category = '.(int)$id_category.' LIMIT 1');
	}

	/**
	  * Return available categories
	  *
	  * @param integer $id_lang Language ID
	  * @param boolean $active return only active categories
	  * @return array Categories
	  */
	static public function getCategories($id_lang = false, $active = true, $order = true, $sql_filter = '', $sql_sort = '',$sql_limit = '')
	{
	 	if (!Validate::isBool($active))
	 		die(Tools::displayError());

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`
		WHERE 1 '.$sql_filter.' '.($id_lang ? 'AND `id_lang` = '.(int)($id_lang) : '').'
		'.($active ? 'AND `active` = 1' : '').'
		'.(!$id_lang ? 'GROUP BY c.id_category' : '').'
		'.($sql_sort != '' ? $sql_sort : 'ORDER BY c.`level_depth` ASC, c.`position` ASC').'
		'.($sql_limit != '' ? $sql_limit : '')
		);

		if (!$order)
			return $result;

		$categories = array();
		foreach ($result AS $row)
			$categories[$row['id_parent']][$row['id_category']]['infos'] = $row;

		return $categories;
	}

	static public function getSimpleCategories($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.`id_category`, cl.`name`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`)
		WHERE cl.`id_lang` = '.(int)($id_lang).'
		ORDER BY c.`position`');
	}

	/**
	  * Return current category childs
	  *
	  * @param integer $id_lang Language ID
	  * @param boolean $active return only active categories
	  * @return array Categories
	  */
	public function getSubCategories($id_lang, $active = true)
	{
	 	global $cookie;
	 	if (!Validate::isBool($active))
	 		die(Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.*, cl.id_lang, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)
		WHERE `id_parent` = '.(int)($this->id).'
		'.($active ? 'AND `active` = 1' : '').'
		AND cg.`id_group` '.$sqlGroups.'
		GROUP BY c.`id_category`
		ORDER BY `level_depth` ASC, c.`position` ASC');

		foreach ($result AS &$row)
		{
			$row['id_image'] = (file_exists(_PS_CAT_IMG_DIR_.$row['id_category'].'.jpg')) ? (int)($row['id_category']) : Language::getIsoById($id_lang).'-default';
			$row['legend'] = 'no picture';
		}
		return $result;
	}

	/**
	  * Return current category products
	  *
	  * @param integer $id_lang Language ID
	  * @param integer $p Page number
	  * @param integer $n Number of products per page
	  * @param boolean $getTotal return the number of results instead of the results themself
	  * @param boolean $active return only active products
	  * @param boolean $random active a random filter for returned products
	  * @param int $randomNumberProducts number of products to return if random is activated
	  * @param boolean $checkAccess set to false to return all products (even if customer hasn't access)
	  * @return mixed Products or number of products
	  */
	public function getProducts($id_lang, $p, $n, $orderBy = NULL, $orderWay = NULL, $getTotal = false, $active = true, $random = false, $randomNumberProducts = 1, $checkAccess = true)
	{
		global $cookie;
		if (!$checkAccess OR !$this->checkAccess($cookie->id_customer))
			return false;	
		
		if ($p < 1) $p = 1;

		if (empty($orderBy))
			$orderBy = 'position';
		else
			/* Fix for all modules which are now using lowercase values for 'orderBy' parameter */
			$orderBy = strtolower($orderBy);
			
		if (empty($orderWay))
			$orderWay = 'ASC';
		if ($orderBy == 'id_product' OR	$orderBy == 'date_add')
			$orderByPrefix = 'p';
		elseif ($orderBy == 'name')
			$orderByPrefix = 'pl';
		elseif ($orderBy == 'manufacturer')
		{
			$orderByPrefix = 'm';
			$orderBy = 'name';
		}
		elseif ($orderBy == 'position')
			$orderByPrefix = 'cp';

		if ($orderBy == 'price')
			$orderBy = 'orderprice';

		if (!Validate::isBool($active) OR !Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay))
			die (Tools::displayError());

		$id_supplier = (int)(Tools::getValue('id_supplier'));

		/* Return only the number of products */
		if ($getTotal)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT COUNT(cp.`id_product`) AS total
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
			WHERE cp.`id_category` = '.(int)($this->id).($active ? ' AND p.`active` = 1' : '').'
			'.($id_supplier ? 'AND p.id_supplier = '.(int)($id_supplier) : ''));
			return isset($result) ? $result['total'] : 0;
		}

		$sql = '
		SELECT p.*, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new,
			(p.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
		FROM `'._DB_PREFIX_.'category_product` cp
		LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = cp.`id_product`
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (p.`id_category_default` = cl.`id_category` AND cl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
		                                           AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
	                                           	   AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
		WHERE cp.`id_category` = '.(int)($this->id).($active ? ' AND p.`active` = 1' : '').'
		'.($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '');

		if ($random === true)
		{
			$sql .= ' ORDER BY RAND()';
			$sql .= ' LIMIT 0, '.(int)($randomNumberProducts);
		}
		else
		{
			$sql .= ' ORDER BY '.(isset($orderByPrefix) ? $orderByPrefix.'.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).'
			LIMIT '.(((int)($p) - 1) * (int)($n)).','.(int)($n);
		}

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

		if ($orderBy == 'orderprice')
			Tools::orderbyPrice($result, $orderWay);

		if (!$result)
			return false;

		/* Modify SQL result */
		return Product::getProductsProperties($id_lang, $result);
	}

	/**
	  * Hide category prefix used for position
	  *
	  * @param string $name Category name
	  * @return string Name without position
	  */
	static public function hideCategoryPosition($name)
	{
		Tools::displayAsDeprecated();
		return preg_replace('/^[0-9]+\./', '', $name);
	}

	/**
	  * Return main categories
	  *
	  * @param integer $id_lang Language ID
	  * @param boolean $active return only active categories
	  * @return array categories
	  */
	static public function getHomeCategories($id_lang, $active = true)
	{
		return self::getChildren(1, $id_lang, $active);
	}

	static public function getRootCategory($id_lang = NULL)
	{
		return new Category (1, is_null($id_lang) ? (int)_USER_ID_LANG_ : (int)($id_lang));
	}

	static public function getChildren($id_parent, $id_lang, $active = true)
	{
		if (!Validate::isBool($active))
	 		die(Tools::displayError());

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`
		WHERE `id_lang` = '.(int)($id_lang).'
		AND c.`id_parent` = '.(int)($id_parent).'
		'.($active ? 'AND `active` = 1' : '').'
		ORDER BY `position` ASC');

		return $result;
	}

	/**
	  * Copy products from a category to another
	  *
	  * @param integer $id_old Source category ID
	  * @param boolean $id_new Destination category ID
	  * @return boolean Duplication result
	  */
	public static function duplicateProductCategories($id_old, $id_new)
	{
		$result = Db::getInstance()->ExecuteS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category_product`
		WHERE `id_product` = '.(int)($id_old));

		$row = array();
		if ($result)
			foreach ($result AS $i)
				$row[] = '('.implode(', ', array((int)($id_new), $i['id_category'], '(SELECT tmp.max + 1 FROM (SELECT MAX(cp.`position`) AS max FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.`id_category`='.(int)($i['id_category']).') AS tmp)')).')';

		$flag = Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'category_product` (`id_product`, `id_category`, `position`) VALUES '.implode(',', $row));
		return $flag;
	}

	/**
	  * Check if category can be moved in another one.
		* The category cannot be moved in a child category.
	  *
		* @param integer $id_category current category
	  * @param integer $id_parent Parent candidate
	  * @return boolean Parent validity
	  */
	public static function checkBeforeMove($id_category, $id_parent)
	{
		if ($id_category == $id_parent) return false;
		if ($id_parent == 1) return true;
		$i = (int)($id_parent);

		while (42)
		{
			$result = Db::getInstance()->getRow('SELECT `id_parent` FROM `'._DB_PREFIX_.'category` WHERE `id_category` = '.(int)($i));
			if (!isset($result['id_parent'])) return false;
			if ($result['id_parent'] == $id_category) return false;
			if ($result['id_parent'] == 1) return true;
			$i = $result['id_parent'];
		}
	}

	public static function getLinkRewrite($id_category, $id_lang)
	{
		if (!Validate::isUnsignedId($id_category) OR !Validate::isUnsignedId($id_lang))
			return false;

		if (isset(self::$_links[$id_category.'-'.$id_lang]))
			return self::$_links[$id_category.'-'.$id_lang];

		$result = Db::getInstance()->getRow('
		SELECT cl.`link_rewrite`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`
		WHERE `id_lang` = '.(int)($id_lang).'
		AND c.`id_category` = '.(int)($id_category));
		self::$_links[$id_category.'-'.$id_lang] = $result['link_rewrite'];
		return $result['link_rewrite'];
	}

	public function getLink()
	{
		global $link;
		return $link->getCategoryLink($this->id, $this->link_rewrite);
	}

	public function getName($id_lang = NULL)
	{
		if (!$id_lang)
		{
			global $cookie;

			if (isset($this->name[$cookie->id_lang]))
				$id_lang = $cookie->id_lang;
			else
				$id_lang = (int)(Configuration::get('PS_LANG_DEFAULT'));
		}
		return isset($this->name[$id_lang]) ? $this->name[$id_lang] : '';
	}

	/**
	  * Light back office search for categories
	  *
	  * @param integer $id_lang Language ID
	  * @param string $query Searched string
	  * @param boolean $unrestricted allows search without lang and includes first category and exact match
	  * @return array Corresponding categories
	  */
	static public function searchByName($id_lang, $query, $unrestricted = false)
	{
		if ($unrestricted === true)
			return Db::getInstance()->getRow('
			SELECT c.*, cl.*
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`)
			WHERE `name` LIKE \''.pSQL($query).'\'');
		else
			return Db::getInstance()->ExecuteS('
			SELECT c.*, cl.*
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)($id_lang).')
			WHERE `name` LIKE \'%'.pSQL($query).'%\' AND c.`id_category` != 1');
	}

	/**
	  * Retrieve category by name and parent category id
	  *
	  * @param integer $id_lang Language ID
	  * @param string  $category_name Searched category name
	  * @param integer $id_parent_category parent category ID
	  * @return array Corresponding category
	  */
	static public function searchByNameAndParentCategoryId($id_lang, $category_name, $id_parent_category)
	{
		return Db::getInstance()->getRow('
		SELECT c.*, cl.*
	    FROM `'._DB_PREFIX_.'category` c
	    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)($id_lang).')
	    WHERE `name`  LIKE \''.pSQL($category_name).'\'
		AND c.`id_category` != 1
		AND c.`id_parent` = '.(int)($id_parent_category));
	}

	/**
	  * Get Each parent category of this category until the root category
	  *
	  * @param integer $id_lang Language ID
	  * @return array Corresponding categories
	  */
	public function getParentsCategories($idLang = null)
	{
		//get idLang
		$idLang = is_null($idLang) ? _USER_ID_LANG_ : (int)($idLang);

		$categories = null;
		$idCurrent = (int)($this->id);
		while (true)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int)($idLang).')
				WHERE c.`id_category` = '.(int)$idCurrent.' AND c.`id_parent` != 0
			');

			$categories[] = $result[0];
			if(!$result OR $result[0]['id_parent'] == 1)
				return $categories;
			$idCurrent = $result[0]['id_parent'];
		}
	}
	/**
	* Specify if a category already in base
	*
	* @param $id_category Category id
	* @return boolean
	*/
	static public function categoryExists($id_category)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_category`
		FROM '._DB_PREFIX_.'category c
		WHERE c.`id_category` = '.(int)($id_category));

		return isset($row['id_category']);
	}


	public function cleanGroups()
	{
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_category` = '.(int)($this->id));
	}

	public function addGroups($groups)
	{
		foreach ($groups as $group)
		{
			$row = array('id_category' => (int)($this->id), 'id_group' => (int)($group));
			Db::getInstance()->AutoExecute(_DB_PREFIX_.'category_group', $row, 'INSERT');
		}
	}

	public function getGroups()
	{
		$groups = array();
		$result = Db::getInstance()->ExecuteS('
		SELECT cg.`id_group`
		FROM '._DB_PREFIX_.'category_group cg
		WHERE cg.`id_category` = '.(int)($this->id));
		foreach ($result as $group)
			$groups[] = $group['id_group'];
		return $groups;
	}

	/**
	 * checkAccess return true if id_customer is in a group allowed to see this category.
	 * 
	 * @param mixed $id_customer 
	 * @access public
	 * @return boolean true if access allowed for customer $id_customer
	 */
	public function checkAccess($id_customer)
	{
		if (!$id_customer)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT ctg.`id_group`
			FROM '._DB_PREFIX_.'category_group ctg
			WHERE ctg.`id_category` = '.(int)($this->id).' AND ctg.`id_group` = 1');
		} else {
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT ctg.`id_group`
			FROM '._DB_PREFIX_.'category_group ctg
			INNER JOIN '._DB_PREFIX_.'customer_group cg on (cg.`id_group` = ctg.`id_group` AND cg.`id_customer` = '.(int)($id_customer).')
			WHERE ctg.`id_category` = '.(int)($this->id));
		}
		if ($result AND isset($result['id_group']) AND $result['id_group'])
			return true;
		return false;
	}

	public function updateGroup($list)
	{
		$this->cleanGroups();
		if ($list AND sizeof($list))
			$this->addGroups($list);
		else
			$this->addGroups(array(1));
	}

	static public function setNewGroupForHome($id_group)
	{
		if (!(int)($id_group))
			return false;
		return Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'category_group`
		VALUES (1, '.(int)($id_group).')
		');
	}

	public function updatePosition($way, $position)
	{
		if (!$res = Db::getInstance()->ExecuteS('
			SELECT cp.`id_category`, cp.`position`, cp.`id_parent`
			FROM `'._DB_PREFIX_.'category` cp
			WHERE cp.`id_parent` = '.(int)(Tools::getValue('id_category_parent', 1)).'
			ORDER BY cp.`position` ASC'
		))
			return false;

		foreach ($res AS $category)
			if ((int)($category['id_category']) == (int)($this->id))
				$movedCategory = $category;
		
		if (!isset($movedCategory) || !isset($position))
			return false;
		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		$result = (Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'category`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
				? '> '.(int)($movedCategory['position']).' AND `position` <= '.(int)($position)
				: '< '.(int)($movedCategory['position']).' AND `position` >= '.(int)($position)).'
			AND `id_parent`='.(int)($movedCategory['id_parent']))
		AND Db::getInstance()->Execute('
			UPDATE `'._DB_PREFIX_.'category`
			SET `position` = '.(int)($position).'
			WHERE `id_parent` = '.(int)($movedCategory['id_parent']).'
			AND `id_category`='.(int)($movedCategory['id_category'])));
		Module::hookExec('categoryUpdate');
		return $result;
	}

	/**
	 * cleanPositions keep order of category in $id_category_parent,
	 * but remove duplicate position. Should not be used if positions
	 * are clean at the beginning !
	 * 
	 * @param mixed $id_category_parent 
	 * @return boolean true if succeed
	 */
	static public function cleanPositions($id_category_parent)
	{
		$return = true;

		$result = Db::getInstance()->ExecuteS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category`
		WHERE `id_parent` = '.(int)($id_category_parent).'
		ORDER BY `position`');
		$sizeof = sizeof($result);
		for ($i = 0; $i < $sizeof; $i++){
				$sql = '
				UPDATE `'._DB_PREFIX_.'category`
				SET `position` = '.(int)($i).'
				WHERE `id_parent` = '.(int)($id_category_parent).'
				AND `id_category` = '.(int)($result[$i]['id_category']);
				$return &= Db::getInstance()->Execute($sql);
			}
		return $return;
	}

	static public function getLastPosition($id_category_parent)
	{
		return (Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'category` WHERE `id_parent` = '.(int)($id_category_parent)));
	}
	
    public static function getUrlRewriteInformations($id_category)
	{
		return Db::getInstance()->ExecuteS('
		SELECT l.`id_lang`, c.`link_rewrite`
		FROM `'._DB_PREFIX_.'category_lang` AS c
		LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
		WHERE c.`id_category` = '.(int)$id_category.'
		AND l.`active` = 1'
		);

	}
	
	public function getChildrenWs()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.`id_category` as id
		FROM `'._DB_PREFIX_.'category` c
		WHERE c.`id_parent` = '.(int)($this->id).'
		AND `active` = 1
		ORDER BY `position` ASC');
		return $result;
	}
	
	public function getProductsWs()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT cp.`id_product` as id
		FROM `'._DB_PREFIX_.'category_product` cp
		WHERE cp.`id_category` = '.(int)($this->id).'
		ORDER BY `position` ASC');
		return $result;
	}
}
