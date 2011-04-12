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

class ManufacturerCore extends ObjectModel
{
	public 		$id;

	/** @var integer manufacturer ID */
	public		$id_manufacturer;//FIXME is it really usefull...?

	/** @var string Name */
	public 		$name;

	/** @var string A description */
	public 		$description;

	/** @var string A short description */
	public 		$short_description;

	/** @var int Address */
	public 		$id_address;

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

	protected	$fieldsSizeLang = array('short_description' => 254, 'meta_title' => 128, 'meta_description' => 255, 'meta_description' => 255);
	protected	$fieldsValidateLang = array('description' => 'isString', 'short_description' => 'isString', 'meta_title' => 'isGenericName', 'meta_description' => 'isGenericName', 'meta_keywords' => 'isGenericName');

	protected 	$table = 'manufacturer';
	protected 	$identifier = 'id_manufacturer';

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_address' => array('xlink_resource'=> 'addresses'),
			'link_rewrite' => array(),
		),
	);

	public function __construct($id = NULL, $id_lang = NULL)
	{
		parent::__construct($id, $id_lang);

		/* Get the manufacturer's id_address */
		$this->id_address = $this->getManufacturerAddress();

		$this->link_rewrite = $this->getLink();
	}

	public function getFields()
	{
		parent::validateFields();
		if (isset($this->id))
			$fields['id_manufacturer'] = (int)($this->id);
		$fields['name'] = pSQL($this->name);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['active'] = (int)($this->active);
		return $fields;
	}

	public function getTranslationsFieldsChild()
	{
		$fieldsArray = array('description', 'short_description', 'meta_title', 'meta_keywords', 'meta_description');
		$fields = array();
		$languages = Language::getLanguages(false);
		$defaultLanguage = Configuration::get('PS_LANG_DEFAULT');
		foreach ($languages as $language)
		{
			$fields[$language['id_lang']]['id_lang'] = $language['id_lang'];
			$fields[$language['id_lang']][$this->identifier] = (int)($this->id);
			$fields[$language['id_lang']]['description'] = (isset($this->description[$language['id_lang']])) ? pSQL($this->description[$language['id_lang']], true) : '';
			$fields[$language['id_lang']]['short_description'] = (isset($this->short_description[$language['id_lang']])) ? pSQL($this->short_description[$language['id_lang']], true) : '';

			foreach ($fieldsArray as $field)
			{
				if (!Validate::isTableOrIdentifier($field))
					die(Tools::displayError());

				/* Check fields validity */
				if (isset($this->{$field}[$language['id_lang']]) AND !empty($this->{$field}[$language['id_lang']]))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$language['id_lang']], true);
				elseif (in_array($field, $this->fieldsRequiredLang))
					$fields[$language['id_lang']][$field] = pSQL($this->{$field}[$defaultLanguage]);
				else
					$fields[$language['id_lang']][$field] = '';

			}
		}
		return $fields;
	}

	public function delete()
	{
		$address = new Address($this->id_address);
		if (!$address->delete())
			return false;
		return parent::delete();
	}

	/**
	 * Delete several objects from database
	 *
	 * return boolean Deletion result
	 */
	public function deleteSelection($selection)
	{
		if (!is_array($selection) OR !Validate::isTableOrIdentifier($this->identifier) OR !Validate::isTableOrIdentifier($this->table))
			die(Tools::displayError());
		$result = true;
		foreach ($selection AS $id)
		{
			$this->id = (int)($id);
			$this->id_address = self::getManufacturerAddress();
			$result = $result AND $this->delete();
		}
		return $result;
	}

	protected function getManufacturerAddress()
	{
		if (!(int)($this->id))
			return false;
		$result = Db::GetInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT `id_address` FROM '._DB_PREFIX_.'address WHERE `id_manufacturer` = '.(int)($this->id));
		if (!$result)
			return false;
		return $result['id_address'];
	}

	/**
	  * Return manufacturers
	  *
	  * @param boolean $getNbProducts [optional] return products numbers for each
	  * @return array Manufacturers
	  */
	static public function getManufacturers($getNbProducts = false, $id_lang = 0, $active = true, $p = false, $n = false, $all_group = false)
	{
		if (!$id_lang)
			$id_lang = (int)Configuration::get('PS_LANG_DEFAULT');
		$sql = 'SELECT m.*, ml.`description`';
		$sql.= ' FROM `'._DB_PREFIX_.'manufacturer` m
		LEFT JOIN `'._DB_PREFIX_.'manufacturer_lang` ml ON (m.`id_manufacturer` = ml.`id_manufacturer` AND ml.`id_lang` = '.(int)($id_lang).')
		'.($active ? ' WHERE m.`active` = 1' : '');
		$sql.= ' ORDER BY m.`name` ASC'.($p ? ' LIMIT '.(((int)($p) - 1) * (int)($n)).','.(int)($n) : '');
		$manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		if ($manufacturers === false)
			return false;
		if ($getNbProducts)
		{
			$sqlGroups = '';
			if (!$all_group)
			{
				$groups = FrontController::getCurrentCustomerGroups();
				$sqlGroups = (count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
			}
			foreach ($manufacturers as $key => $manufacturer)
			{
				$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` as m ON (m.`id_manufacturer`= p.`id_manufacturer`)
				WHERE m.`id_manufacturer` = '.(int)($manufacturer['id_manufacturer']).
				($active ? ' AND p.`active` = 1 ' : '').
				($all_group ? '' : ' AND p.`id_product` IN (
					SELECT cp.`id_product`
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)
					WHERE cg.`id_group` '.$sqlGroups.')'));

				$manufacturers[$key]['nb_products'] = sizeof($result);
			}
		}
		for ($i = 0; $i < sizeof($manufacturers); $i++)
			if ((int)(Configuration::get('PS_REWRITING_SETTINGS')))
				$manufacturers[$i]['link_rewrite'] = Tools::link_rewrite($manufacturers[$i]['name'], false);
			else
				$manufacturers[$i]['link_rewrite'] = 0;
		return $manufacturers;
	}

	/**
	 * @deprecated
	 */
	static public function getManufacturersWithoutAddress()
	{
		Tools::displayAsDeprecated();
		$sql = 'SELECT m.* FROM `'._DB_PREFIX_.'manufacturer` m
				LEFT JOIN `'._DB_PREFIX_.'address` a ON (a.`id_manufacturer` = m.`id_manufacturer` AND a.`deleted` = 0)
				WHERE a.`id_manufacturer` IS NULL';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
	}

	/**
	  * Return name from id
	  *
	  * @param integer $id_manufacturer Manufacturer ID
	  * @return string name
	  */
	static protected $cacheName = array();
	static public function getNameById($id_manufacturer)
	{
		if (!isset(self::$cacheName[$id_manufacturer]))
			self::$cacheName[$id_manufacturer] = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `name` FROM `'._DB_PREFIX_.'manufacturer` WHERE `id_manufacturer` = '.(int)($id_manufacturer).' AND `active` = 1');
		return self::$cacheName[$id_manufacturer];
	}

	static public function getIdByName($name)
	{
		$result = Db::getInstance()->getRow('
		SELECT `id_manufacturer`
		FROM `'._DB_PREFIX_.'manufacturer`
		WHERE `name` = \''.pSQL($name).'\'');
		if (isset($result['id_manufacturer']))
			return (int)($result['id_manufacturer']);
		return false;
	}

	public function getLink()
	{
		return Tools::link_rewrite($this->name, false);
	}

	static public function getProducts($id_manufacturer, $id_lang, $p, $n, $orderBy = NULL, $orderWay = NULL, $getTotal = false, $active = true)
	{
		if ($p < 1) $p = 1;
	 	if (empty($orderBy) ||$orderBy == 'position') $orderBy = 'name';
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
				WHERE p.id_manufacturer = '.(int)($id_manufacturer)
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
		SELECT p.*, pa.`id_product_attribute`, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, i.`id_image`, il.`legend`, m.`name` AS manufacturer_name, tl.`name` AS tax_name, t.`rate`, DATEDIFF(p.`date_add`, DATE_SUB(NOW(), INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY)) > 0 AS new,
			(p.`price` * ((100 + (t.`rate`))/100)) AS orderprice
		FROM `'._DB_PREFIX_.'product` p
		LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product` AND default_on = 1)
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product` AND i.`cover` = 1)
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (i.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group`
		                                           AND tr.`id_country` = '.(int)Country::getDefaultCountryId().'
	                                           	   AND tr.`id_state` = 0)
	    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		LEFT JOIN `'._DB_PREFIX_.'tax_lang` tl ON (t.`id_tax` = tl.`id_tax` AND tl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
		WHERE p.`id_manufacturer` = '.(int)($id_manufacturer).($active ? ' AND p.`active` = 1' : '').'
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
		WHERE p.`id_manufacturer` = '.(int)($this->id));
	}
	/*
	* Specify if a manufacturer already in base
	*
	* @param $id_manufacturer Manufacturer id
	* @return boolean
	*/
	static public function manufacturerExists($id_manufacturer)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_manufacturer`
		FROM '._DB_PREFIX_.'manufacturer m
		WHERE m.`id_manufacturer` = '.(int)($id_manufacturer));

		return isset($row['id_manufacturer']);
	}

	public function getAddresses($id_lang)
	{
		return Db::getInstance()->ExecuteS('
		SELECT a.*, cl.name AS `country`, s.name AS `state`
		FROM `'._DB_PREFIX_.'address` AS a
		LEFT JOIN `'._DB_PREFIX_.'country_lang` AS cl ON (cl.`id_country` = a.`id_country` AND cl.`id_lang` = '.(int)($id_lang).')
		LEFT JOIN `'._DB_PREFIX_.'state` AS s ON (s.`id_state` = a.`id_state`)
		WHERE `id_manufacturer` = '.(int)($this->id).'
		AND a.`deleted` = 0');
	}
}

