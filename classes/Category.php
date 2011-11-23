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
*  @version  Release: $Revision: 7515 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CategoryCore extends ObjectModel
{
	public $id;

	/** @var integer category ID */
	public $id_category;

	/** @var string Name */
	public $name;

	/** @var boolean Status for display */
	public $active = 1;

	/** @var  integer category position */
	public $position;

	/** @var string Description */
	public $description;

	/** @var integer Parent category ID */
	public $id_parent;

	/** @var integer Parents number */
	public $level_depth;

	/** @var integer Nested tree model "left" value */
	public $nleft;

	/** @var integer Nested tree model "right" value */
	public $nright;

	/** @var string string used in rewrited URL */
	public $link_rewrite;

	/** @var string Meta title */
	public $meta_title;

	/** @var string Meta keywords */
	public $meta_keywords;

	/** @var string Meta description */
	public $meta_description;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	protected $langMultiShop = true;

	public $groupBox;

	protected static $_links = array();

	protected $tables = array ('category', 'category_lang');

	protected $fieldsRequired = array('active');
 	protected $fieldsSize = array('active' => 1);
 	protected $fieldsValidate = array(
 		'nleft' => 'isUnsignedInt',
 		'nright' => 'isUnsignedInt',
 		'level_depth' => 'isUnsignedInt',
 		'active' => 'isBool',
 		'id_parent' => 'isUnsignedInt',
 		'groupBox' => 'isArrayWithIds'
 	);
	protected $fieldsRequiredLang = array('name', 'link_rewrite');
 	protected $fieldsSizeLang = array('name' => 64, 'link_rewrite' => 64, 'meta_title' => 128, 'meta_description' => 255, 'meta_keywords' => 255);
 	protected $fieldsValidateLang = array(
 		'name' => 'isCatalogName',
 		'link_rewrite' => 'isLinkRewrite',
 		'description' => 'isString',
 		'meta_title' => 'isGenericName',
 		'meta_description' => 'isGenericName',
 		'meta_keywords' => 'isGenericName'
 	);

	protected $table = 'category';
	protected $identifier = 'id_category';

	/** @var string id_image is the category ID when an image exists and 'default' otherwise */
	public $id_image = 'default';

	protected $webserviceParameters = array(
		'objectsNodeName' => 'categories',
		'hidden_fields' => array('nleft', 'nright', 'groupBox'),
		'fields' => array(
			'id_parent' => array('xlink_resource'=> 'categories'),
			'level_depth' => array('setter' => false),
			'nb_products_recursive' => array('getter' => 'getWsNbProductsRecursive', 'setter' => false),
		),
		'associations' => array(
			'categories' => array('getter' => 'getChildrenWs', 'resource' => 'category', ),
			'products' => array('getter' => 'getProductsWs', 'resource' => 'product', ),
		),
	);

	public function __construct($id_category = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id_category, $id_lang, $id_shop);
		$this->id_image = ($this->id && file_exists(_PS_CAT_IMG_DIR_.(int)$this->id.'.jpg')) ? (int)$this->id : false;
		$this->image_dir = _PS_CAT_IMG_DIR_;
	}

	public function getFields()
	{
		$this->validateFields();
		if (isset($this->id))
			$fields['id_category'] = (int)$this->id;
		$fields['active'] = (int)$this->active;
		$fields['id_parent'] = (int)$this->id_parent;
		$fields['position'] = (int)$this->position;
		$fields['level_depth'] = (int)$this->level_depth;
		$fields['nleft'] = (int)$this->nleft;
		$fields['nright'] = (int)$this->nright;
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}

	/**
	  * Allows to display the category description without HTML tags and slashes
	  *
	  * @return string
	  */
	public static function getDescriptionClean($description)
	{
		return strip_tags(stripslashes($description));
	}

	/**
	  * Check then return multilingual fields for database interaction
	  *
	  * @return array Multilingual fields
	  */
	public function getTranslationsFieldsChild()
	{
		$this->validateFieldsLang();
		return $this->getTranslationsFields(array(
			'name',
			'description' => array('html' => true),
			'link_rewrite',
			'meta_title',
			'meta_keywords',
			'meta_description',
		));
	}

	public	function add($autodate = true, $nullValues = false)
	{
		$this->position = self::getLastPosition((int)$this->id_parent);
		if (!isset($this->level_depth))
			$this->level_depth = $this->calcLevelDepth();
		$ret = parent::add($autodate);
		if (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree)
			self::regenerateEntireNtree();
		$this->updateGroup($this->groupBox);
		Hook::exec('categoryAddition', array('category' => $this));
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
		if ($this->id_parent == $this->id)
			throw new PrestashopException('a category cannot be it\'s own parent');
		// Update group selection
		$this->updateGroup($this->groupBox);
		$this->level_depth = $this->calcLevelDepth();
		$this->cleanPositions((int)$this->id_parent);
		// If the parent category was changed, we don't want to have 2 categories with the same position
		if ($this->getDuplicatePosition())
			$this->position = self::getLastPosition((int)$this->id_parent);
		$ret = parent::update($nullValues);
		if (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree)
		{
			self::regenerateEntireNtree();
			$this->recalculateLevelDepth($this->id_category);
		}
		Hook::exec('categoryUpdate', array('category' => $this));
		return $ret;
	}

	/**
	 * @see ObjectModel::toggleStatus()
	 */
	public function toggleStatus()
	{
		$result = parent::toggleStatus();
		Hook::exec('categoryUpdate');
		return $result;
	}

	/**
	  * Recursive scan of subcategories
	  *
	  * @param integer $max_depth Maximum depth of the tree (i.e. 2 => 3 levels depth)
 	  * @param integer $current_depth specify the current depth in the tree (don't use it, only for rucursivity!)
 	  * @param integer $id_lang Specify the id of the language used
	  * @param array $excluded_ids_array specify a list of ids to exclude of results
	  *
 	  * @return array Subcategories lite tree
	  */
	public function recurseLiteCategTree($max_depth = 3, $current_depth = 0, $id_lang = null, $excluded_ids_array = null)
	{
		$id_lang = is_null($id_lang) ? Context::getContext()->language->id : (int)$id_lang;

		if (!(int)$id_lang)
			$id_lang = _USER_ID_LANG_;

		$children = array();
		if (($max_depth == 0 || $current_depth < $max_depth) && $subcats = $this->getSubCategories($id_lang, true) && count($subcats))
			foreach ($subcats as &$subcat)
			{
				if (!$subcat['id_category'])
					break;
				else if (!is_array($excluded_ids_array) || !in_array($subcat['id_category'], $excluded_ids_array))
				{
					$categ = new Category($subcat['id_category'], $id_lang);
					$children[] = $categ->recurseLiteCategTree($max_depth, $current_depth + 1, $id_lang, $excluded_ids_array);
				}
			}

		return array(
			'id' => (int)$this->id_category,
			'link' => Context::getContext()->link->getCategoryLink($this->id, $this->link_rewrite),
			'name' => $this->name,
			'desc'=> Category::getDescriptionClean($this->description),
			'children' => $children
		);
	}

	public static function recurseCategory($categories, $current, $id_category = 1, $id_selected = 1)
	{
		echo '<option value="'.$id_category.'"'.(($id_selected == $id_category) ? ' selected="selected"' : '').'>'.
		str_repeat('&nbsp;', $current['infos']['level_depth'] * 5).stripslashes($current['infos']['name']).'</option>';
		if (isset($categories[$id_category]))
			foreach (array_keys($categories[$id_category]) as $key)
				self::recurseCategory($categories, $categories[$id_category][$key], $key, $id_selected);
	}


	/**
	  * Recursively add specified category childs to $to_delete array
	  *
	  * @param array &$to_delete Array reference where categories ID will be saved
	  * @param array $id_category Parent category ID
	  */
	protected function recursiveDelete(&$to_delete, $id_category)
	{
	 	if (!is_array($to_delete) || !$id_category)
	 		die(Tools::displayError());

		$result = Db::getInstance()->executeS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category`
		WHERE `id_parent` = '.(int)$id_category);
		foreach ($result as $row)
		{
			$to_delete[] = (int)$row['id_category'];
			$this->recursiveDelete($to_delete, (int)$row['id_category']);
		}
	}

	public function delete()
	{
		if ((int)$this->id === 0 || (int)$this->id === 1)
			return false;

		$this->clearCache();

		/* Get childs categories */
		$to_delete = array((int)$this->id);
		$this->recursiveDelete($to_delete, (int)$this->id);
		$to_delete = array_unique($to_delete);

		/* Delete category and its child from database */
		$list = count($to_delete) > 1 ? implode(',', array_map('intval', $to_delete)) : (int)$this->id;
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category` WHERE `id_category` IN ('.$list.')');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_lang` WHERE `id_category` IN ('.$list.')');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_product` WHERE `id_category` IN ('.$list.')');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_category` IN ('.$list.')');

		self::cleanPositions($this->id_parent);

		/* Delete category images and its children images */
		$tmp_category = new Category();
		foreach ($to_delete as $id_category)
		{
			$tmp_category->id = (int)$id_category;
			$tmp_category->deleteImage();
		}

		/* Delete products which were not in others categories */
		$result = Db::getInstance()->executeS('
			SELECT `id_product`
			FROM `'._DB_PREFIX_.'product`
			WHERE `id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'category_product`)
		');
		foreach ($result as $p)
		{
			$product = new Product((int)$p['id_product']);
			if (Validate::isLoadedObject($product))
				$product->delete();
		}

		/* Set category default to 1 where categorie no more exists */
		$result = Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'product`
			SET `id_category_default` = 1
			WHERE `id_category_default`
			NOT IN (SELECT `id_category` FROM `'._DB_PREFIX_.'category`)
		');

		/* Rebuild the nested tree */
		if (!isset($this->doNotRegenerateNTree) || !$this->doNotRegenerateNTree)
			self::regenerateEntireNtree();

		Hook::exec('categoryDeletion', array('category' => $this));

		/* Delete Categories in GroupReduction */
		foreach ($to_delete as $category)
			if (GroupReduction::getGroupReductionByCategoryId((int)$category))
				GroupReduction::deleteCategory($category);

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
		foreach ($categories as $id_category)
		{
			$category = new Category($id_category);
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

		$parent_category = new Category($this->id_parent);
		if (!Validate::isLoadedObject($parent_category))
			die('parent category does not exist');
		return $parent_category->level_depth + 1;
	}

	/**
	  * Re-calculate the values of all branches of the nested tree
	  */
	public static function regenerateEntireNtree()
	{
		$categories = Db::getInstance()->executeS('SELECT id_category, id_parent FROM '._DB_PREFIX_.'category ORDER BY id_category ASC');
		$categories_array = array();
		foreach ($categories as $category)
			$categories_array[(int)$category['id_parent']]['subcategories'][(int)$category['id_category']] = 1;
			$n = 1;
		self::_subTree($categories_array, 1, $n);
	}

	protected static function _subTree(&$categories, $id_category, &$n)
	{
		$left = (int)$n++;
		if (isset($categories[(int)$id_category]['subcategories']))
			foreach (array_keys($categories[(int)$id_category]['subcategories']) as $id_subcategory)
				self::_subTree($categories, (int)$id_subcategory, $n);
		$right = (int)$n++;

		Db::getInstance()->execute('
			UPDATE '._DB_PREFIX_.'category
			SET nleft = '.(int)$left.', nright = '.(int)$right.'
			WHERE id_category = '.(int)$id_category.' LIMIT 1
		');
	}

	/**
	  * Updates level_depth for all children of the given id_category
	  *
	  * @param integer $id_category parent category
	  */
	public function recalculateLevelDepth($id_category)
	{
		if (!is_numeric($id_category))
			throw new PrestashopException('id category is not numeric');
		/* Gets all children */
		$categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT id_category, id_parent, level_depth
			FROM '._DB_PREFIX_.'category
			WHERE id_parent = '.(int)$id_category);
		/* Gets level_depth */
		$level = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT level_depth
			FROM '._DB_PREFIX_.'category
			WHERE id_category = '.(int)$id_category);
		/* Updates level_depth for all children */
		foreach ($categories as $sub_category)
		{
			Db::getInstance()->execute('
				UPDATE '._DB_PREFIX_.'category
				SET level_depth = '.(int)($level['level_depth'] + 1).'
				WHERE id_category = '.(int)$sub_category['id_category']);
			/* Recursive call */
			$this->recalculateLevelDepth($sub_category['id_category']);
		}
	}

	/**
	  * Return available categories
	  *
	  * @param integer $id_lang Language ID
	  * @param boolean $active return only active categories
	  * @return array Categories
	  */
	public static function getCategories($id_lang = false, $active = true, $order = true, $sql_filter = '', $sql_sort = '', $sql_limit = '')
	{
	 	if (!Validate::isBool($active))
	 		die(Tools::displayError());
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Context::getContext()->shop->addSqlRestrictionOnLang('cl').'
			WHERE 1 '.$sql_filter.' '.($id_lang ? 'AND `id_lang` = '.(int)$id_lang : '').'
			'.($active ? 'AND `active` = 1' : '').'
			'.(!$id_lang ? 'GROUP BY c.id_category' : '').'
			'.($sql_sort != '' ? $sql_sort : 'ORDER BY c.`level_depth` ASC, c.`position` ASC').'
			'.($sql_limit != '' ? $sql_limit : '')
		);

		if (!$order)
			return $result;

		$categories = array();
		foreach ($result as $row)
			$categories[$row['id_parent']][$row['id_category']]['infos'] = $row;

		return $categories;
	}

	public static function getSimpleCategories($id_lang)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category`, cl.`name`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
		WHERE cl.`id_lang` = '.(int)$id_lang.'
		GROUP BY c.id_category
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
	 	if (!Validate::isBool($active))
	 		die(Tools::displayError());

		$groups = FrontController::getCurrentCustomerGroups();
		$sql_groups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.*, cl.id_lang, cl.name, cl.description, cl.link_rewrite, cl.meta_title, cl.meta_keywords, cl.meta_description
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
				ON (c.`id_category` = cl.`id_category`
				AND `id_lang` = '.(int)$id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
			LEFT JOIN `'._DB_PREFIX_.'category_group` cg
				ON (cg.`id_category` = c.`id_category`)
			WHERE `id_parent` = '.(int)$this->id.'
				'.($active ? 'AND `active` = 1' : '').'
				AND cg.`id_group` '.$sql_groups.'
			GROUP BY c.`id_category`
			ORDER BY `level_depth` ASC, c.`position` ASC
		');

		foreach ($result as &$row)
		{
			$row['id_image'] = file_exists(_PS_CAT_IMG_DIR_.$row['id_category'].'.jpg') ? (int)$row['id_category'] : Language::getIsoById($id_lang).'-default';
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
	  * @param boolean $get_total return the number of results instead of the results themself
	  * @param boolean $active return only active products
	  * @param boolean $random active a random filter for returned products
	  * @param int $random_number_products number of products to return if random is activated
	  * @param boolean $check_access set to false to return all products (even if customer hasn't access)
	  * @return mixed Products or number of products
	  */
	public function getProducts($id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $random = false, $random_number_products = 1, $check_access = true, Context $context = null)
	{
		if (!$context)
			$context = Context::getContext();
		if (!$check_access || !$this->checkAccess($context->customer->id))
			return false;

		if ($p < 1) $p = 1;

		if (empty($order_by))
			$order_by = 'position';
		else
			/* Fix for all modules which are now using lowercase values for 'orderBy' parameter */
			$order_by = strtolower($order_by);

		if (empty($order_way))
			$order_way = 'ASC';
		if ($order_by == 'id_product' ||	$order_by == 'date_add')
			$order_by_prefix = 'p';
		else if ($order_by == 'name')
			$order_by_prefix = 'pl';
		else if ($order_by == 'manufacturer')
		{
			$order_by_prefix = 'm';
			$order_by = 'name';
		}
		else if ($order_by == 'position')
			$order_by_prefix = 'cp';

		if ($order_by == 'price')
			$order_by = 'orderprice';

		if (!Validate::isBool($active) || !Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way))
			die (Tools::displayError());

		$id_supplier = (int)Tools::getValue('id_supplier');

		/* Return only the number of products */
		if ($get_total)
		{
			$sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.$context->shop->addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` = '.(int)$this->id.
					($active ? ' AND p.`active` = 1' : '').
					($id_supplier ? 'AND p.id_supplier = '.(int)$id_supplier : '').
					' GROUP BY p.id_product';
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
		}

		$sql = 'SELECT p.*, stock.out_of_stock, stock.quantity, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`,
					il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, cl.`name` AS category_default,
					DATEDIFF(p.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new,
					(p.`price` * IF(t.`rate`,((100 + (t.`rate`))/100),1)) AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product` AND default_on = 1)
				'.$context->shop->addSqlAssociation('product', 'p').'
				'.Product::sqlStock('p', 'pa', false, $context->shop).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (p.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.$context->shop->addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`
					AND i.`cover` = 1)
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (i.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr
					ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
					AND tr.`id_country` = '.(int)$context->country->id.'
					AND tr.`id_state` = 0
					AND tr.`zipcode_from` = 0)
				LEFT JOIN `'._DB_PREFIX_.'tax` t
					ON (t.`id_tax` = tr.`id_tax`)
				LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl
					ON (t.`id_tax` = tl.`id_tax`
					AND tl.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE cp.`id_category` = '.(int)$this->id
					.($active ? ' AND p.`active` = 1' : '')
					.($id_supplier ? ' AND p.id_supplier = '.(int)$id_supplier : '').
					' GROUP BY p.id_product';
		if ($random === true)
		{
			$sql .= ' ORDER BY RAND()';
			$sql .= ' LIMIT 0, '.(int)$random_number_products;
		}
		else
			$sql .= ' ORDER BY '.(isset($order_by_prefix) ? $order_by_prefix.'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way).'
			LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		if ($order_by == 'orderprice')
			Tools::orderbyPrice($result, $order_way);

		if (!$result)
			return false;

		/* Modify SQL result */
		return Product::getProductsProperties($id_lang, $result);
	}

	/**
	  * Return main categories
	  *
	  * @param integer $id_lang Language ID
	  * @param boolean $active return only active categories
	  * @return array categories
	  */
	public static function getHomeCategories($id_lang, $active = true)
	{
		return self::getChildren(1, $id_lang, $active);
	}

	public static function getRootCategory($id_lang = null, Shop $shop = null)
	{
		if (is_null($id_lang))
			$id_lang = Context::getContext()->language->id;
		if (!$shop)
			$shop = Context::getContext()->shop;

		return new Category($shop->getCategory(), $id_lang);
	}

	/**
	 *
	 * @param int $id_parent
	 * @param int $id_lang
	 * @param bool $active
	 * @return array
	 */
	public static function getChildren($id_parent, $id_lang, $active = true)
	{
		if (!Validate::isBool($active))
	 		die(Tools::displayError());

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Context::getContext()->shop->addSqlRestrictionOnLang('cl').'
		WHERE `id_lang` = '.(int)$id_lang.'
		AND c.`id_parent` = '.(int)$id_parent.'
		'.($active ? 'AND `active` = 1' : '').'
		ORDER BY `position` ASC');
	}

	/** return an array of all children of the current category
	 *
	 * @return array rows of table category
	 * @todo return hydrateCollection
	 */
	public function getAllChildren()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM `'._DB_PREFIX_.'category`
			WHERE '.(int)$this->nleft.' < nleft AND nright < '.(int)$this->nright
		);
	}

	/**
	 * This method allow to return children categories with the number of sub children selected for a product
	 *
	 * @param int $id_parent
	 * @param int $id_product
	 * @param int $id_lang
	 * @return array
	 */
	public static function getChildrenWithNbSelectedSubCat($id_parent, $selected_cat, $id_lang, Shop $shop = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		$selected_cat = explode(',', str_replace(' ', '', $selected_cat));
		$sql = 'SELECT c.`id_category`, c.`level_depth`, cl.`name`, IF((
						SELECT COUNT(*)
						FROM `'._DB_PREFIX_.'category` c2
						WHERE c2.`id_parent` = c.`id_category`
					) > 0, 1, 0) AS has_children, '.($selected_cat ? '(
						SELECT count(c3.`id_category`)
						FROM `'._DB_PREFIX_.'category` c3
						WHERE c3.`nleft` > c.`nleft`
						AND c3.`nright` < c.`nright`
			AND c3.`id_category`  IN ('.implode(',', array_map('intval', $selected_cat)).')
					)' : '0').' AS nbSelectedSubCat
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.$shop->addSqlRestrictionOnLang('cl').'
				WHERE `id_lang` = '.(int)$id_lang.'
					AND c.`id_parent` = '.(int)$id_parent.'
				ORDER BY `position` ASC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
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
		$sql = 'SELECT `id_category`
				FROM `'._DB_PREFIX_.'category_product`
				WHERE `id_product` = '.(int)$id_old;
		$result = Db::getInstance()->executeS($sql);

		$row = array();
		if ($result)
			foreach ($result as $i)
				$row[] = '('.implode(', ', array((int)$id_new, $i['id_category'], '(SELECT tmp.max + 1 FROM (
					SELECT MAX(cp.`position`) AS max
					FROM `'._DB_PREFIX_.'category_product` cp
					WHERE cp.`id_category`='.(int)$i['id_category'].') AS tmp)'
				)).')';

		$flag = Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'category_product` (`id_product`, `id_category`, `position`)
			VALUES '.implode(',', $row)
		);
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
		$i = (int)$id_parent;

		while (42)
		{
			$result = Db::getInstance()->getRow('SELECT `id_parent` FROM `'._DB_PREFIX_.'category` WHERE `id_category` = '.(int)$i);
			if (!isset($result['id_parent'])) return false;
			if ($result['id_parent'] == $id_category) return false;
			if ($result['id_parent'] == 1) return true;
			$i = $result['id_parent'];
		}
	}

	public static function getLinkRewrite($id_category, $id_lang)
	{
		if (!Validate::isUnsignedId($id_category) || !Validate::isUnsignedId($id_lang))
			return false;

		if (isset(self::$_links[$id_category.'-'.$id_lang]))
			return self::$_links[$id_category.'-'.$id_lang];

		$result = Db::getInstance()->getRow('
		SELECT cl.`link_rewrite`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON c.`id_category` = cl.`id_category`'.Context::getContext()->shop->addSqlRestrictionOnLang('cl').'
		WHERE `id_lang` = '.(int)$id_lang.'
		AND c.`id_category` = '.(int)$id_category);
		self::$_links[$id_category.'-'.$id_lang] = $result['link_rewrite'];
		return $result['link_rewrite'];
	}

	public function getLink(Link $link = null)
	{
		if (!$link)
			$link = Context::getContext()->link;
		return $link->getCategoryLink($this->id, $this->link_rewrite);
	}

	public function getName($id_lang = null)
	{
		if (!$id_lang)
		{
			if (isset($this->name[Context::getContext()->language->id]))
				$id_lang = Context::getContext()->language->id;
			else
				$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
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
	public static function searchByName($id_lang, $query, $unrestricted = false)
	{
		if ($unrestricted === true)
			return Db::getInstance()->getRow('
				SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (c.`id_category` = cl.`id_category`'.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
				WHERE `name` LIKE \''.pSQL($query).'\'
			');
		else
			return Db::getInstance()->executeS('
				SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (c.`id_category` = cl.`id_category`
					AND `id_lang` = '.(int)$id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
				WHERE `name` LIKE \'%'.pSQL($query).'%\'
				AND c.`id_category` != 1
			');
	}

	/**
	  * Retrieve category by name and parent category id
	  *
	  * @param integer $id_lang Language ID
	  * @param string  $category_name Searched category name
	  * @param integer $id_parent_category parent category ID
	  * @return array Corresponding category
	  */
	public static function searchByNameAndParentCategoryId($id_lang, $category_name, $id_parent_category)
	{
		return Db::getInstance()->getRow('
			SELECT c.*, cl.*
		    FROM `'._DB_PREFIX_.'category` c
		    LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
		    	ON (c.`id_category` = cl.`id_category`
		    	AND `id_lang` = '.(int)$id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
		    WHERE `name`  LIKE \''.pSQL($category_name).'\'
				AND c.`id_category` != 1
				AND c.`id_parent` = '.(int)$id_parent_category
		);
	}

	/**
	  * Get Each parent category of this category until the root category
	  *
	  * @param integer $id_lang Language ID
	  * @return array Corresponding categories
	  */
	public function getParentsCategories($id_lang = null)
	{
		if (is_null($id_lang))
			$id_lang = Context::getContext()->language->id;

		$categories = null;
		$id_current = $this->id;
		while (true)
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT c.*, cl.*
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (c.`id_category` = cl.`id_category`
					AND `id_lang` = '.(int)$id_lang.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
				WHERE c.`id_category` = '.(int)$id_current.'
					AND c.`id_parent` != 0
			');

			isset($result[0]) ? $categories[] = $result[0] : $categories = array();
			if (!$result || $result[0]['id_parent'] == 1)
				return $categories;
			$id_current = $result[0]['id_parent'];
		}
	}
	/**
	* Specify if a category already in base
	*
	* @param $id_category Category id
	* @return boolean
	*/
	public static function categoryExists($id_category)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_category`
		FROM '._DB_PREFIX_.'category c
		WHERE c.`id_category` = '.(int)$id_category);

		return isset($row['id_category']);
	}


	public function cleanGroups()
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'category_group` WHERE `id_category` = '.(int)$this->id);
	}

	public function addGroups($groups)
	{
		foreach ($groups as $group)
		{
			$row = array('id_category' => (int)$this->id, 'id_group' => (int)$group);
			Db::getInstance()->AutoExecute(_DB_PREFIX_.'category_group', $row, 'INSERT');
		}
	}

	public function getGroups()
	{
		$groups = array();
		$result = Db::getInstance()->executeS('
			SELECT cg.`id_group`
			FROM '._DB_PREFIX_.'category_group cg
			WHERE cg.`id_category` = '.(int)$this->id
		);
		foreach ($result as $group)
			$groups[] = $group['id_group'];
		return $groups;
	}

	public function addGroupsIfNoExist($id_group)
	{
		$groups = $this->getGroups();
		if (!in_array((int)$id_group, $groups))
			return $this->addGroups(array((int)$id_group));
		else
			return false;
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
				WHERE ctg.`id_category` = '.(int)$this->id.' AND ctg.`id_group` = 1
			');
		} else {
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
				SELECT ctg.`id_group`
				FROM '._DB_PREFIX_.'category_group ctg
				INNER JOIN '._DB_PREFIX_.'customer_group cg on (cg.`id_group` = ctg.`id_group` AND cg.`id_customer` = '.(int)$id_customer.')
				WHERE ctg.`id_category` = '.(int)$this->id
			);
		}
		if ($result && isset($result['id_group']) && $result['id_group'])
			return true;
		return false;
	}

	public function updateGroup($list)
	{
		$this->cleanGroups();
		if ($list && count($list))
			$this->addGroups($list);
		else
			$this->addGroups(array(1));
	}

	public static function setNewGroupForHome($id_group)
	{
		if (!(int)$id_group)
			return false;
		return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'category_group`
			VALUES (1, '.(int)$id_group.')
		');
	}

	public function updatePosition($way, $position)
	{
		if (!$res = Db::getInstance()->executeS('
			SELECT cp.`id_category`, cp.`position`, cp.`id_parent`
			FROM `'._DB_PREFIX_.'category` cp
			WHERE cp.`id_parent` = '.(int)$this->id_parent.'
			ORDER BY cp.`position` ASC'
		))
			return false;

		foreach ($res as $category)
			if ((int)$category['id_category'] == (int)$this->id)
				$moved_category = $category;

		if (!isset($moved_category) || !isset($position))
			return false;
		// < and > statements rather than BETWEEN operator
		// since BETWEEN is treated differently according to databases
		$result = (Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'category`
			SET `position`= `position` '.($way ? '- 1' : '+ 1').'
			WHERE `position`
			'.($way
				? '> '.(int)$moved_category['position'].' AND `position` <= '.(int)$position
				: '< '.(int)$moved_category['position'].' AND `position` >= '.(int)$position).'
			AND `id_parent`='.(int)$moved_category['id_parent'])
		&& Db::getInstance()->execute('
			UPDATE `'._DB_PREFIX_.'category`
			SET `position` = '.(int)$position.'
			WHERE `id_parent` = '.(int)$moved_category['id_parent'].'
			AND `id_category`='.(int)$moved_category['id_category']));
		Hook::exec('categoryUpdate');
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
	public static function cleanPositions($id_category_parent)
	{
		$return = true;

		$result = Db::getInstance()->executeS('
			SELECT `id_category`
			FROM `'._DB_PREFIX_.'category`
			WHERE `id_parent` = '.(int)$id_category_parent.'
			ORDER BY `position`
		');
		$count = count($result);
		for ($i = 0; $i < $count; $i++)
		{
			$sql = '
				UPDATE `'._DB_PREFIX_.'category`
				SET `position` = '.(int)$i.'
				WHERE `id_parent` = '.(int)$id_category_parent.'
				AND `id_category` = '.(int)$result[$i]['id_category'];
			$return &= Db::getInstance()->execute($sql);
		}
		return $return;
	}

	/** this function return the number of category + 1 having $id_category_parent as parent.
	 *
	 * @todo rename that function to make it understandable (getNewLastPosition for example)
	 * @param int $id_category_parent the parent category
	 * @return int
	 */
	public static function getLastPosition($id_category_parent)
	{
		return (Db::getInstance()->getValue('SELECT MAX(position)+1 FROM `'._DB_PREFIX_.'category` WHERE `id_parent` = '.(int)$id_category_parent));
	}

    public static function getUrlRewriteInformations($id_category)
    {
		return Db::getInstance()->executeS('
			SELECT l.`id_lang`, c.`link_rewrite`
			FROM `'._DB_PREFIX_.'category_lang` AS c
			LEFT JOIN  `'._DB_PREFIX_.'lang` AS l ON c.`id_lang` = l.`id_lang`
			WHERE c.`id_category` = '.(int)$id_category.'
			AND l.`active` = 1'
		);
    }

	/**
	 * Return nleft and nright fields for a given category
	 *
	 * @since 1.5.0
	 * @param int $id
	 * @return array
	 */
	public static function getInterval($id)
	{
		$sql = 'SELECT nleft, nright, level_depth
				FROM '._DB_PREFIX_.'category
				WHERE id_category = '.(int)$id;
		if (!$result = Db::getInstance()->getRow($sql))
			return false;
		return $result;
	}

	/**
	 * Check if current category is a child of shop root category
	 *
	 * @since 1.5.0
	 * @param Shop $shop
	 * @return bool
	 */
	public function inShop(Shop $shop = null)
	{
		if (!$shop)
			$shop = Context::getContext()->shop;

		if (!$interval = Category::getInterval($shop->getCategory()))
			return false;
		return ($this->nleft >= $interval['nleft'] && $this->nright <= $interval['nright']);
	}

	public function getChildrenWs()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.`id_category` as id
		FROM `'._DB_PREFIX_.'category` c
		WHERE c.`id_parent` = '.(int)$this->id.'
		AND `active` = 1
		ORDER BY `position` ASC');
		return $result;
	}

	public function getProductsWs()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT cp.`id_product` as id
		FROM `'._DB_PREFIX_.'category_product` cp
		WHERE cp.`id_category` = '.(int)$this->id.'
		ORDER BY `position` ASC');
		return $result;
	}

	/**
	 * Search for another category with the same parent and the same position
	 *
	 * @return array first category found
	 */
	public function getDuplicatePosition()
	{
		return Db::getInstance()->getRow('
		SELECT c.`id_category` as id
		FROM `'._DB_PREFIX_.'category` c
		WHERE c.`id_parent` = '.(int)$this->id_parent.'
		AND `position` = '.(int)$this->position.'
		AND c.`id_category` != '.(int)$this->id);
	}

	public function getWsNbProductsRecursive()
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT count(distinct(id_product)) as nb_product_recursive FROM  `'._DB_PREFIX_.'category_product`
		WHERE id_category IN (SELECT id_category
		FROM `'._DB_PREFIX_.'category`
		WHERE nleft > '.(int)$this->nleft.
		' AND nright < '.(int)$this->nright.' AND active = 1 UNION SELECT '.(int)$this->id.')');
		if (!$result)
			return -1;
		return $result[0]['nb_product_recursive'];
	}

	/**
	 *
	 * @param Array $ids_category
	 * @param int $id_lang
	 * @return Array
	 */
	public static function getCategoryInformations($ids_category, $id_lang = null)
	{
		if ($id_lang === null)
			$id_lang = Context::getContext()->language->id;

		if (!is_array($ids_category) || !count($ids_category))
			return;

		$categories = array();
		$results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT c.`id_category`, cl.`name`, cl.`link_rewrite`, cl.`id_lang`
			FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`'.Context::getContext()->shop->addSqlRestrictionOnLang('cl').')
			WHERE cl.`id_lang` = '.(int)$id_lang.'
			AND c.`id_category` IN ('.implode(',', array_map('intval', $ids_category)).')
		');

		foreach ($results as $category)
			$categories[$category['id_category']] = $category;

		return $categories;
	}
}

