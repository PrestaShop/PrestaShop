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

class SupplierCore extends ObjectModel
{
	public 		$id;

	/** @var integer supplier ID */
	public		$id_supplier;

	/** @var string Name */
	public 		$name;

	/** @var string A short description for the discount */
	public 		$description;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	/** @var string Friendly URL */
	public 		$link_rewrite;

	/** @var string Meta title */
	public 		$meta_title;

	/** @var string Meta keywords */
	public 		$meta_keywords;

	/** @var string Meta description */
	public 		$meta_description;

	/** @var boolean active */
	public		$active;

 	protected 	$fieldsRequired = array('name');
 	protected 	$fieldsSize = array('name' => 64);
 	protected 	$fieldsValidate = array('name' => 'isCatalogName');

	protected	$fieldsSizeLang = array('meta_title' => 128, 'meta_description' => 255, 'meta_keywords' => 255);
	protected	$fieldsValidateLang = array('description' => 'isGenericName', 'meta_title' => 'isGenericName', 'meta_description' => 'isGenericName', 'meta_keywords' => 'isGenericName');

	protected 	$table = 'supplier';
	protected 	$identifier = 'id_supplier';

	protected	$webserviceParameters = array(
		'fields' => array(
			'link_rewrite' => array('sqlId' => 'link_rewrite'),
		),
	);

	public function __construct($id = NULL, $id_lang = NULL)
	{
		parent::__construct($id, $id_lang);

		$this->link_rewrite = $this->getLink();
	}

	public function getLink()
	{
		return Tools::link_rewrite($this->name, false);
	}

	public function getFields()
	{
		parent::validateFields();
		if (isset($this->id))
			$fields['id_supplier'] = (int)($this->id);
		$fields['name'] = pSQL($this->name);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['active'] = (int)($this->active);
		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('description', 'meta_title', 'meta_keywords', 'meta_description'));
	}

	/**
	  * Return suppliers
	  *
	  * @return array Suppliers
	  */
	static public function getSuppliers($getNbProducts = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_groups = false)
	{
			global $cookie;

		if (!$id_lang)
			$id_lang = Configuration::get('PS_LANG_DEFAULT');
		$query = 'SELECT s.*, sl.`description`';
		$query .= ' FROM `'._DB_PREFIX_.'supplier` as s
		LEFT JOIN `'._DB_PREFIX_.'supplier_lang` sl ON (s.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)($id_lang).')
		'.($active ? ' WHERE s.`active` = 1 ' : '');
		$query .= ' ORDER BY s.`name` ASC'.($p ? ' LIMIT '.(((int)($p) - 1) * (int)($n)).','.(int)($n) : '');
		$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
		if ($suppliers === false)
			return false;
		if ($getNbProducts)
		{
			$sqlGroups = '';
			if (!$all_groups)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}
			foreach ($suppliers as $key => $supplier)
			{
				$sql = '
					SELECT p.`id_product`
					FROM `'._DB_PREFIX_.'product` p
					LEFT JOIN `'._DB_PREFIX_.'supplier` as m ON (m.`id_supplier`= p.`id_supplier`)
					WHERE m.`id_supplier` = '.(int)($supplier['id_supplier']).
					($active ? ' AND p.`active` = 1' : '').
					($all_groups ? '' :'
					AND p.`id_product` IN (
						SELECT cp.`id_product`
						FROM `'._DB_PREFIX_.'category_group` cg
						LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
						WHERE cg.`id_group` '.$sqlGroups.'
					)');
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
				$suppliers[$key]['nb_products'] = sizeof($result);
			}
		}
		for ($i = 0; $i < sizeof($suppliers); $i++)
			if ((int)(Configuration::get('PS_REWRITING_SETTINGS')))
				$suppliers[$i]['link_rewrite'] = Tools::link_rewrite($suppliers[$i]['name'], false);
			else
				$suppliers[$i]['link_rewrite'] = 0;
		return $suppliers;
	}

	/**
	  * Return name from id
	  *
	  * @param integer $id_supplier Supplier ID
	  * @return string name
	  */
	static protected $cacheName = array();
	static public function getNameById($id_supplier)
	{
		if (!isset(self::$cacheName[$id_supplier]))
			self::$cacheName[$id_supplier] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `name` FROM `'._DB_PREFIX_.'supplier` WHERE `id_supplier` = '.(int)($id_supplier));
		return self::$cacheName[$id_supplier];
	}
	static public function getIdByName($name)
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_supplier`
		FROM `'._DB_PREFIX_.'supplier`
		WHERE `name` = \''.pSQL($name).'\'');
		if (isset($result['id_supplier']))
			return (int)($result['id_supplier']);
		return false;
 	}

	static public function getProducts($id_supplier, $id_lang, $p, $n, $orderBy = NULL, $orderWay = NULL, $getTotal = false, $active = true)
	{
		if ($p < 1) $p = 1;
	 	if (empty($orderBy) OR $orderBy == 'position') $orderBy = 'name';
	 	if (empty($orderWay)) $orderWay = 'ASC';

		if (!Validate::isOrderBy($orderBy) OR !Validate::isOrderWay($orderWay))
			die (Tools::displayError());
			
		$groups = FrontController::getCurrentCustomerGroups();
		$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');

		/* Return only the number of products */
		if ($getTotal)
		{
			$sql = '
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				WHERE p.id_supplier = '.(int)($id_supplier)
				.($active ? ' AND p.`active` = 1' : '').'
				AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
					WHERE cg.`id_group` '.$sqlGroups.'
				)';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
			return (int)(sizeof($result));
		}

		$sql = '
		SELECT p.*, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, s.`name` AS supplier_name, tl.`name` AS tax_name, t.`rate`, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new,
			(p.`price` * ((100 + (t.`rate`))/100)) AS orderprice, m.`name` AS manufacturer_name
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
		                                           AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
	                                           	   AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'supplier` s ON s.`id_supplier` = p.`id_supplier`
		LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
		WHERE p.`id_supplier` = '.(int)($id_supplier).($active ? ' AND p.`active` = 1' : '').'
		AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
					WHERE cg.`id_group` '.$sqlGroups.'
				)
		ORDER BY '.(($orderBy == 'id_product') ? 'p.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).'
		LIMIT '.(((int)($p) - 1) * (int)($n)).','.(int)($n);
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		if (!$result)
			return false;
		if ($orderBy == 'price')
			Tools::orderbyPrice($result, $orderWay);
		return Product::getProductsProperties($id_lang, $result);
	}

	public function getProductsLite($id_lang)
	{
		return Db::getInstance()->ExecuteS('
		SELECT p.`id_product`,  pl.`name`
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')
		WHERE p.`id_supplier` = '.(int)($this->id));
	}
	/*
	* Specify if a supplier already in base
	*
	* @param $id_supplier Supplier id
	* @return boolean
	*/
	static public function supplierExists($id_supplier)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_supplier`
		FROM '._DB_PREFIX_.'supplier s
		WHERE s.`id_supplier` = '.(int)($id_supplier));

		return isset($row['id_supplier']);
	}
}

