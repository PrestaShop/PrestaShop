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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CarrierCore extends ObjectModel
{
	/**
	 * getCarriers method filter
	 */
	const PS_CARRIERS_ONLY = 1;
	const CARRIERS_MODULE = 2;
	const CARRIERS_MODULE_NEED_RANGE = 3;
	const PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE = 4;
	const ALL_CARRIERS = 5;
	
	const SHIPPING_METHOD_DEFAULT = 0;
	const SHIPPING_METHOD_WEIGHT = 1;
	const SHIPPING_METHOD_PRICE = 2;
	const SHIPPING_METHOD_FREE = 3;

	/** @var int Tax id (none = 0) */
	public		$id_tax_rules_group;

 	/** @var string Name */
	public 		$name;

 	/** @var string URL with a '@' for */
	public 		$url;

	/** @var string Delay needed to deliver customer */
	public 		$delay;

	/** @var boolean Carrier statuts */
	public 		$active = true;

	/** @var boolean True if carrier has been deleted (staying in database as deleted) */
	public 		$deleted = 0;

	/** @var boolean Active or not the shipping handling */
	public		$shipping_handling = true;

	/** @var int Behavior taken for unknown range */
	public		$range_behavior;

	/** @var boolean Carrier module */
	public		$is_module;

	/** @var boolean Free carrier */
	public		$is_free = false;
	
	/** @var int shipping behavior: by weight or by price */
	public 		$shipping_method = 0;

	/** @var boolean Shipping external */
	public		$shipping_external = 0;

	/** @var string Shipping external */
	public		$external_module_name = NULL;

	/** @var boolean Need Range */
	public		$need_range = 0;
	
	protected	$langMultiShop = true;

 	protected 	$fieldsRequired = array('name', 'active');
 	protected 	$fieldsSize = array('name' => 64);
 	protected 	$fieldsValidate = array('id_tax_rules_group' => 'isInt', 'name' => 'isCarrierName', 'active' => 'isBool', 'is_free' => 'isBool', 'url' => 'isAbsoluteUrl', 'shipping_handling' => 'isBool', 'range_behavior' => 'isBool', 'shipping_method' => 'isUnsignedInt');
 	protected 	$fieldsRequiredLang = array('delay');
 	protected 	$fieldsSizeLang = array('delay' => 128);
 	protected 	$fieldsValidateLang = array('delay' => 'isGenericName');

	protected 	$table = 'carrier';
	protected 	$identifier = 'id_carrier';

	protected static $priceByWeight = array();
	protected static $priceByWeight2 = array();
	protected static $priceByPrice = array();
	protected static $priceByPrice2 = array();

	protected static $_cache_tax_rule = array();

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_tax_rules_group' => array(),
			'deleted' => array(),
			'is_module' => array(),
		),
	);

	public function getFields()
	{
		parent::validateFields();
		$fields['id_tax_rules_group'] = (int)($this->id_tax_rules_group);
		$fields['name'] = pSQL($this->name);
		$fields['url'] = pSQL($this->url);
		$fields['active'] = (int)($this->active);
		$fields['deleted'] = (int)($this->deleted);
		$fields['shipping_handling'] = (int)($this->shipping_handling);
		$fields['range_behavior'] = (int)($this->range_behavior);
		$fields['shipping_method'] = (int)($this->shipping_method);
		$fields['is_module'] = (int)($this->is_module);
		$fields['is_free'] = (int)($this->is_free);
		$fields['shipping_external'] = (int)($this->shipping_external);
		$fields['external_module_name'] = $this->external_module_name;
		$fields['need_range'] = $this->need_range;

		return $fields;
	}

	public function __construct($id = NULL, $id_lang = NULL)
	{
		parent::__construct($id, $id_lang);
		if ($this->name == '0')
			$this->name = Configuration::get('PS_SHOP_NAME');
	}

	/**
	* Check then return multilingual fields for database interaction
	*
	* @return array Multilingual fields
	*/
	public function getTranslationsFieldsChild()
	{
		parent::validateFieldsLang();
		return parent::getTranslationsFields(array('delay'));
	}

	public function add($autodate = true, $nullValues = false)
	{
		if (!parent::add($autodate, $nullValues) OR !Validate::isLoadedObject($this))
			return false;
		if (!Db::getInstance()->ExecuteS('SELECT `id_carrier` FROM `'._DB_PREFIX_.$this->table.'` WHERE `deleted` = 0'))
			return false;
		if (!$numRows = Db::getInstance()->NumRows())
			return false;
		if ((int)($numRows) == 1)
			Configuration::updateValue('PS_CARRIER_DEFAULT', (int)($this->id));
		return true;
	}

	/**
	* Change carrier id in delivery prices when updating a carrier
	*
	* @param integer $id_old Old id carrier
	*/
	public function setConfiguration($id_old)
	{
		Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'delivery` SET `id_carrier` = '.(int)($this->id).' WHERE `id_carrier` = '.(int)($id_old));
	}

	/**
	 * Get delivery prices for a given order
	 *
	 * @param floatval $totalWeight Order total weight
	 * @param integer $id_zone Zone id (for customer delivery address)
	 * @return float Delivery price
	 */
	public function getDeliveryPriceByWeight($totalWeight, $id_zone, Shop $shop = null)
	{
		$cache_key = $this->id.'_'.$totalWeight.'_'.$id_zone;
		if (!isset(self::$priceByWeight[$cache_key]))
		{
			$sql = 'SELECT d.`price`
					FROM `'._DB_PREFIX_.'delivery` d
					LEFT JOIN `'._DB_PREFIX_.'range_weight` w ON (d.`id_range_weight` = w.`id_range_weight`)
					WHERE d.`id_zone` = '.(int)($id_zone).'
						AND '.(float)$totalWeight.' >= w.`delimiter1`
						AND '.(float)$totalWeight.' < w.`delimiter2`
						AND d.`id_carrier` = '.(int)$this->id.'
						'.Carrier::sqlDeliveryRangeShop('range_weight', $shop).'
					ORDER BY w.`delimiter1` ASC';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
			if (!isset($result['price']))
				self::$priceByWeight[$cache_key] = $this->getMaxDeliveryPriceByWeight($id_zone);
			else
				self::$priceByWeight[$cache_key] = $result['price'];
		}
		return self::$priceByWeight[$cache_key];
	}

	static public function checkDeliveryPriceByWeight($id_carrier, $totalWeight, $id_zone, Shop $shop = null)
	{
		$cache_key = $id_carrier.'_'.$totalWeight.'_'.$id_zone;
		if (!isset(self::$priceByWeight2[$cache_key]))
		{
			$sql = 'SELECT d.`price`
					FROM `'._DB_PREFIX_.'delivery` d
					LEFT JOIN `'._DB_PREFIX_.'range_weight` w ON d.`id_range_weight` = w.`id_range_weight`
					WHERE d.`id_zone` = '.(int)$id_zone.'
						AND '.(float)$totalWeight.' >= w.`delimiter1`
						AND '.(float)$totalWeight.' < w.`delimiter2`
						AND d.`id_carrier` = '.(int)$id_carrier.'
						'.Carrier::sqlDeliveryRangeShop('range_weight', $shop).'
					ORDER BY w.`delimiter1` ASC';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
			self::$priceByWeight2[$cache_key] = (isset($result['price']));
		}
		return self::$priceByWeight2[$cache_key];
	}

	public function getMaxDeliveryPriceByWeight($id_zone, Shop $shop = null)
	{
		$sql = 'SELECT d.`price`
				FROM `'._DB_PREFIX_.'delivery` d
				INNER JOIN `'._DB_PREFIX_.'range_weight` w ON d.`id_range_weight` = w.`id_range_weight`
				WHERE d.`id_zone` = '.(int)$id_zone.'
					AND d.`id_carrier` = '.(int)$this->id.'
					'.Carrier::sqlDeliveryRangeShop('range_weight', $shop).'
				ORDER BY w.`delimiter2` DESC LIMIT 1';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		if (!isset($result[0]['price']))
			return false;
		return $result[0]['price'];
	}

	/**
	 * Get delivery prices for a given order
	 *
	 * @param floatval $orderTotal Order total to pay
	 * @param integer $id_zone Zone id (for customer delivery address)
	 * @return float Delivery price
	 */
	public function getDeliveryPriceByPrice($orderTotal, $id_zone, $id_currency = NULL, Shop $shop = null)
	{
		$cache_key = $this->id.'_'.$orderTotal.'_'.$id_zone.'_'.$id_currency;
		if (!isset(self::$priceByPrice[$cache_key]))
		{
			if (!empty($id_currency))
				$orderTotal = Tools::convertPrice($orderTotal, $id_currency, false);

			$sql = 'SELECT d.`price`
					FROM `'._DB_PREFIX_.'delivery` d
					LEFT JOIN `'._DB_PREFIX_.'range_price` r ON d.`id_range_price` = r.`id_range_price`
					WHERE d.`id_zone` = '.(int)($id_zone).'
						AND '.(float)$orderTotal.' >= r.`delimiter1`
						AND '.(float)$orderTotal.' < r.`delimiter2`
						AND d.`id_carrier` = '.(int)($this->id).'
						'.Carrier::sqlDeliveryRangeShop('range_price', $shop).'
					ORDER BY r.`delimiter1` ASC';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
			if (!isset($result['price']))
				self::$priceByPrice[$cache_key] = $this->getMaxDeliveryPriceByPrice($id_zone);
			else
				self::$priceByPrice[$cache_key] = $result['price'];
		}
		return self::$priceByPrice[$cache_key];
	}

	/**
	 * Check delivery prices for a given order
	 *
	 * @param id_carrier
	 * @param floatval $orderTotal Order total to pay
	 * @param integer $id_zone Zone id (for customer delivery address)
	 * @param integer $id_currency
	 * @return float Delivery price
	 */
	static public function checkDeliveryPriceByPrice($id_carrier, $orderTotal, $id_zone, $id_currency = NULL, Shop $shop = null)
	{
		$cache_key = $id_carrier.'_'.$orderTotal.'_'.$id_zone.'_'.$id_currency;
		if (!isset(self::$priceByPrice2[$cache_key]))
		{
			if (!empty($id_currency))
				$orderTotal = Tools::convertPrice($orderTotal, $id_currency, false);

			$sql = 'SELECT d.`price`
					FROM `'._DB_PREFIX_.'delivery` d
					LEFT JOIN `'._DB_PREFIX_.'range_price` r ON d.`id_range_price` = r.`id_range_price`
					WHERE d.`id_zone` = '.(int)($id_zone).'
						AND '.(float)$orderTotal.' >= r.`delimiter1`
						AND '.(float)$orderTotal.' < r.`delimiter2`
						AND d.`id_carrier` = '.(int)$id_carrier.'
						'.Carrier::sqlDeliveryRangeShop('range_price', $shop).'
					ORDER BY r.`delimiter1` ASC';
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
			self::$priceByPrice2[$cache_key] = (isset($result['price']));
		}
		return self::$priceByPrice2[$cache_key];
	}

	public function getMaxDeliveryPriceByPrice($id_zone, Shop $shop = null)
	{
		$sql = 'SELECT d.`price`
				FROM `'._DB_PREFIX_.'delivery` d
				INNER JOIN `'._DB_PREFIX_.'range_price` r ON d.`id_range_price` = r.`id_range_price`
				WHERE d.`id_zone` = '.(int)$id_zone.'
					AND d.`id_carrier` = '.(int)$this->id.'
					'.Carrier::sqlDeliveryRangeShop('range_price', $shop).'
				ORDER BY r.`delimiter2` DESC LIMIT 1';
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
		if (!isset($result[0]['price']))
			return false;
		return $result[0]['price'];
	}

	/**
	 * Get delivery prices for a given shipping method (price/weight)
	 *
	 * @param string $rangeTable Table name (price or weight)
	 * @return array Delivery prices
	 */
	public static function getDeliveryPriceByRanges($rangeTable, $id_carrier, Shop $shop = null)
	{
		$rangeTable = pSQL($rangeTable);
		$sql = 'SELECT d.id_'.$rangeTable.', d.id_carrier, d.id_zone, d.price
				FROM '._DB_PREFIX_.'delivery d
				LEFT JOIN '._DB_PREFIX_.$rangeTable.' r ON r.id_'.$rangeTable.' = d.id_'.$rangeTable.'
				WHERE d.id_carrier = '.(int)$id_carrier.'
					AND d.id_'.$rangeTable.' IS NOT NULL
					AND d.id_'.$rangeTable.' != 0
					'.Carrier::sqlDeliveryRangeShop($rangeTable, $shop).'
				ORDER BY r.delimiter1';
		return Db::getInstance()->ExecuteS($sql);
	}

	/**
	 * Get all carriers in a given language
	 *
	 * @param integer $id_lang Language id
	 * @param $modules_filters, possible values:
			PS_CARRIERS_ONLY
			CARRIERS_MODULE
			CARRIERS_MODULE_NEED_RANGE
			PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
			ALL_CARRIERS
	 * @param boolean $active Returns only active carriers when true
	 * @return array Carriers
	 */
	public static function getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = NULL, $modules_filters = self::PS_CARRIERS_ONLY)
	{
	 	if (!Validate::isBool($active))
	 		die(Tools::displayError());
	 	if ($ids_group)
		{
			$ids = '';
			foreach ($ids_group as $id)
				$ids .= (int)($id).', ';
			$ids = rtrim($ids, ', ');
			if ($ids == '')
				return array();
		}

		$sql = 'SELECT c.*, cl.delay
				FROM `'._DB_PREFIX_.'carrier` c
				LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = '.(int)$id_lang.Context::getContext()->shop->sqlLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'carrier_zone` cz  ON (cz.`id_carrier` = c.`id_carrier`)'.
				($id_zone ? 'LEFT JOIN `'._DB_PREFIX_.'zone` z  ON (z.`id_zone` = '.(int)$id_zone.')' : '').'
				WHERE c.`deleted` = '.($delete ? '1' : '0').
					($active ? ' AND c.`active` = 1' : '').
					($id_zone ? ' AND cz.`id_zone` = '.(int)$id_zone.'
					AND z.`active` = 1 ' : ' ');
		switch ($modules_filters)
		{
			case 1 :
				$sql .= 'AND c.is_module = 0 ';
			break;
			case 2 :
				$sql .= 'AND c.is_module = 1 ';
			break;
			case 3 :
				$sql .= 'AND c.is_module = 1 AND c.need_range = 1 ';
			break;
			case 4 :
				$sql .= 'AND (c.is_module = 0 OR c.need_range = 1) ';
			break;
			case 5 :
				$sql .= '';
			break;

		}
		$sql .= ($ids_group ? ' AND c.id_carrier IN (SELECT id_carrier FROM '._DB_PREFIX_.'carrier_group WHERE id_group IN ('.$ids.')) ' : '').'
			GROUP BY c.`id_carrier`';

		$carriers = Db::getInstance()->ExecuteS($sql);

		if (is_array($carriers) AND count($carriers))
		{
			foreach ($carriers as $key => $carrier)
				if ($carrier['name'] == '0')
					$carriers[$key]['name'] = Configuration::get('PS_SHOP_NAME');
		}
		else
			$carriers = array();

		return $carriers;
	}

	public static function getDeliveredCountries($id_lang, $activeCountries = false, $activeCarriers = false, $containStates = NULL)
	{
		if (!Validate::isBool($activeCountries) OR !Validate::isBool($activeCarriers))
	 		die(Tools::displayError());
	 		
		$states = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT s.*
		FROM `'._DB_PREFIX_.'state` s
		ORDER BY s.`name` ASC');

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT cl.*,c.*, cl.`name` AS country, zz.`name` AS zone FROM `'._DB_PREFIX_.'country` c 
			LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = 1) 
			INNER JOIN (`'._DB_PREFIX_.'carrier_zone` cz INNER JOIN `'._DB_PREFIX_.'carrier` cr ON ( cr.id_carrier = cz.id_carrier AND cr.deleted = 0 '.($activeCarriers ?
			'AND cr.active = 1) ' : ') ').'
			LEFT JOIN `'._DB_PREFIX_.'zone` zz ON cz.id_zone = zz.id_zone) ON zz.`id_zone` = c.`id_zone` 
			WHERE 1
			'.($activeCountries ? 'AND c.active = 1' : '').'
			'.(!is_null($containStates) ? 'AND c.`contains_states` = '.(int)($containStates) : '').'
			ORDER BY cl.name ASC');
	
		$countries = array();
		foreach ($result AS &$country)
			$countries[$country['id_country']] = $country;
		foreach ($states AS &$state)
			if (isset($countries[$state['id_country']])) /* Does not keep the state if its country has been disabled and not selected */
				$countries[$state['id_country']]['states'][] = $state;

		return $countries;
	}
	
	/**
	 *
	 * @param int $id_zone
	 * @param Array $groups group of the customer
	 * @return Array 
	 */
	public static function getCarriersForOrder($id_zone, $groups = NULL)
	{
		$context = Context::getContext();
		$id_lang = $context->language->id;
		$cart = $context->cart;
		$id_currency = $context->currency->id;

		if (is_array($groups) AND !empty($groups))
			$result = Carrier::getCarriers($id_lang, true, false, (int)$id_zone, $groups, self::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
		else
			$result = Carrier::getCarriers($id_lang, true, false, (int)$id_zone, array(1), self::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
		$resultsArray = array();

		foreach ($result AS $k => $row)
		{
			$carrier = new Carrier((int)$row['id_carrier']);
			$shippingMethod = $carrier->getShippingMethod();
			if ($shippingMethod != Carrier::SHIPPING_METHOD_FREE)
			{
			// Get only carriers that are compliant with shipping method
				if (($shippingMethod == Carrier::SHIPPING_METHOD_WEIGHT AND $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)
					OR ($shippingMethod == Carrier::SHIPPING_METHOD_PRICE AND $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
			{
				unset($result[$k]);
				continue ;
			}

			// If out-of-range behavior carrier is set on "Desactivate carrier"
			if ($row['range_behavior'])
			{
				// Get id zone
		        if (!$id_zone)
						$id_zone = Country::getIdZone(Country::getDefaultCountryId());

				// Get only carriers that have a range compatible with cart
					if (($shippingMethod == Carrier::SHIPPING_METHOD_WEIGHT AND (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $cart->getTotalWeight(), $id_zone)))
						OR ($shippingMethod == Carrier::SHIPPING_METHOD_PRICE AND (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone, $id_currency))))
					{
						unset($result[$k]);
						continue ;
					}
			}
			}
			
			$row['name'] = (strval($row['name']) != '0' ? $row['name'] : Configuration::get('PS_SHOP_NAME'));
			$row['price'] = ($shippingMethod == Carrier::SHIPPING_METHOD_FREE ? 0 : $cart->getOrderShippingCost((int)$row['id_carrier']));
			$row['price_tax_exc'] = ($shippingMethod == Carrier::SHIPPING_METHOD_FREE ? 0 : $cart->getOrderShippingCost((int)$row['id_carrier'], false));
			$row['img'] = file_exists(_PS_SHIP_IMG_DIR_.(int)($row['id_carrier']).'.jpg') ? _THEME_SHIP_DIR_.(int)($row['id_carrier']).'.jpg' : '';

			// If price is false, then the carrier is unavailable (carrier module)
			if ($row['price'] === false)
			{
				unset($result[$k]);
				continue ;
			}

			$resultsArray[] = $row;
		}
		return $resultsArray;
	}

	public static function checkCarrierZone($id_carrier, $id_zone)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT c.`id_carrier`
			FROM `'._DB_PREFIX_.'carrier` c
			LEFT JOIN `'._DB_PREFIX_.'carrier_zone` cz  ON (cz.`id_carrier` = c.`id_carrier`)
			LEFT JOIN `'._DB_PREFIX_.'zone` z  ON (z.`id_zone` = '.(int)($id_zone).')
			WHERE c.`id_carrier` = '.(int)($id_carrier).'
			AND c.`deleted` = 0
			AND c.`active` = 1
			AND cz.`id_zone` = '.(int)($id_zone).'
			AND z.`active` = 1'
		);
	}

	/**
	 * Get all zones
	 *
	 * @return array Zones
	 */
	public function getZones()
	{
		return Db::getInstance()->ExecuteS('
			SELECT *
			FROM `'._DB_PREFIX_.'carrier_zone` cz
			LEFT JOIN `'._DB_PREFIX_.'zone` z ON cz.`id_zone` = z.`id_zone`
			WHERE cz.`id_carrier` = '. (int)($this->id));
	}

	/**
	 * Get a specific zones
	 *
	 * @return array Zone
	 */
	public function getZone($id_zone)
	{
		return Db::getInstance()->ExecuteS('
			SELECT *
			FROM `'._DB_PREFIX_.'carrier_zone`
			WHERE `id_carrier` = '.(int)($this->id).'
			AND `id_zone` = '.(int)($id_zone));
	}

	/**
	 * Add zone
	 */
	public function addZone($id_zone)
	{
		return Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'carrier_zone` (`id_carrier` , `id_zone`)
			VALUES ('.(int)($this->id).', '.(int)($id_zone).')');
	}

	/**
	 * Delete zone
	 */
	public function deleteZone($id_zone)
	{
		return Db::getInstance()->Execute('
			DELETE FROM `'._DB_PREFIX_.'carrier_zone`
			WHERE `id_carrier` = '.(int)($this->id).'
			AND `id_zone` = '.(int)($id_zone).' LIMIT 1');
	}

	/**
	 * Clean delivery prices (weight/price)
	 *
	 * @param string $rangeTable Table name to clean (weight or price according to shipping method)
	 * @return boolean Deletion result
	 */
	public function deleteDeliveryPrice($rangeTable, Shop $shop = null)
	{
		$where = '`id_carrier` = '.(int)$this->id.' AND (`id_'.$rangeTable.'` IS NOT NULL OR `id_'.$rangeTable.'` = 0) ';

		if (!$shop)
			$shop = Context::getContext()->shop;
		$shopID = $shop->getID();
		$shopGroupID = $shop->getGroupID();
		if (!$shopID && !$shopGroupID)
			$where .= 'AND id_shop IS NULL AND id_group_shop IS NULL';
		else if (!$shopID)
			$where .= 'AND id_shop IS NULL AND id_group_shop = '.$shopGroupID;
		else
			$where .= 'AND id_shop = '.$shopID;
		
		return Db::getInstance()->delete(_DB_PREFIX_.'delivery', $where);
	}

	/**
	 * Add new delivery prices
	 *
	 * @param array $priceList Prices list in multiple arrays (changed to array since 1.5.0)
	 * @return boolean Insertion result
	 */
	public function addDeliveryPrice($priceList, Shop $shop = null)
	{
		if (!$priceList)
			return false;

		$keys = array_keys($priceList[0]);
		if (!in_array('id_shop', $keys))
			$keys[] = 'id_shop';
		if (!in_array('id_group_shop', $keys))
			$keys[] = 'id_group_shop';
		
		if (!$shop)
			$shop = Context::getContext()->shop;
		$shopID = $shop->getID();
		$shopGroupID = $shop->getGroupID();

		$sql = 'INSERT INTO `'._DB_PREFIX_.'delivery` ('.implode(', ', $keys).') VALUES ';
		foreach ($priceList as $values)
		{
			if (!isset($values['id_shop']))
				$values['id_shop'] = ($shopID) ? $shopID : null;
			if (!isset($values['id_group_shop']))
				$values['id_group_shop'] = ($shopGroupID) ? $shopGroupID : null;
			
			$sql .= '(';
			foreach ($values as $v)
			{
				if (is_null($v))
					$sql .= 'NULL';
				else if (is_int($v) || is_float($v))
					$sql .= $v;
				else
					$sql .= "'$v'";
				$sql .= ', ';
			}
			$sql = rtrim($sql, ', ').'), ';
		}
		$sql = rtrim($sql, ', ');
		return Db::getInstance()->Execute($sql);
	}

	/**
	 * Copy old carrier informations when update carrier
	 *
	 * @param integer $oldId Old id carrier (copy from that id)
	 */
	public function copyCarrierData($oldId)
	{
		if (!Validate::isUnsignedId($oldId))
			die(Tools::displayError());
			
		if (!$this->id)
			return false;

		$oldLogo = _PS_SHIP_IMG_DIR_.'/'.(int)$oldId.'.jpg';
		if (file_exists($oldLogo))
			copy($oldLogo, _PS_SHIP_IMG_DIR_.'/'.$this->id.'.jpg');

		// Copy existing ranges price
		foreach (array('range_price', 'range_weight') as $range)
		{
			$sql = 'SELECT * FROM `'._DB_PREFIX_.$range.'`
					WHERE id_carrier = '.(int)$oldId;
			$res = Db::getInstance()->ExecuteS($sql);
			foreach ($res AS $val)
			{
				$sql = 'INSERT INTO `'._DB_PREFIX_.$range.'` (`id_carrier`, `delimiter1`, `delimiter2`)
						VALUES ('.$this->id.','.(float)$val['delimiter1'].','.(float)$val['delimiter2'].')';
				Db::getInstance()->Execute($sql);
				$rangeID = (int)Db::getInstance()->Insert_ID();
				
				$rangePriceID = ($range == 'range_price') ? $rangeID : 'NULL';
				$rangeWeightID = ($range == 'range_weight') ? $rangeID : 'NULL';
				$sql = 'INSERT INTO '._DB_PREFIX_.$range.' (id_carrier, id_shop, id_group_shop, id_range_price, id_range_weight, id_zone, price)
						SELECT '.$this->id.', id_shop, id_group_shop, '.$rangePriceID.', '.$rangeWeightID.', id_zone, price FROM '._DB_PREFIX_.$range;
				Db::getInstance()->Execute($sql);
			}
		}

		// Copy existing zones
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'carrier_zone`
				WHERE id_carrier = '.(int)$oldId;
		$res = Db::getInstance()->ExecuteS($sql);
		foreach ($res as $val)
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'carrier_zone` (`id_carrier`, `id_zone`)
			VALUES ('.$this->id.','.(int)$val['id_zone'].')');

		//Copy default carrier
		if ((int)(Configuration::get('PS_CARRIER_DEFAULT')) == $oldId)
			Configuration::updateValue('PS_CARRIER_DEFAULT', (int)($this->id));
	}

	/**
	 * Check if carrier is used (at least one order placed)
	 *
	 * @return integer Order count for this carrier
	 */
	public function isUsed()
	{
		$row = Db::getInstance()->getRow('
		SELECT COUNT(`id_carrier`) AS total
		FROM `'._DB_PREFIX_.'orders`
		WHERE `id_carrier` = '.(int)($this->id));

		return (int)($row['total']);
	}

	public function getShippingMethod()
	{
		if ($this->is_free)
			return Carrier::SHIPPING_METHOD_FREE;

		$method = (int)$this->shipping_method;

		if ($this->shipping_method == Carrier::SHIPPING_METHOD_DEFAULT)
		{
			// backward compatibility
			if ((int)Configuration::get('PS_SHIPPING_METHOD'))
				$method = Carrier::SHIPPING_METHOD_WEIGHT;
			else
				$method = Carrier::SHIPPING_METHOD_PRICE;
		}

		return $method;
	}

	public function getRangeTable()
	{
		$shippingMethod = $this->getShippingMethod();
		if ($shippingMethod == Carrier::SHIPPING_METHOD_WEIGHT)
			return 'range_weight';
		elseif ($shippingMethod == Carrier::SHIPPING_METHOD_PRICE)
			return 'range_price';
		return false;
	}

	public function getRangeObject()
	{
		$shippingMethod = $this->getShippingMethod();
		if ($shippingMethod == Carrier::SHIPPING_METHOD_WEIGHT)
			return new RangeWeight();
		elseif ($shippingMethod == Carrier::SHIPPING_METHOD_PRICE)
			return new RangePrice();
		return false;
	}

	public function getRangeSuffix($currency = null)
	{
		if (!$currency)
			$currency = Context::getContext()->currency;
		$suffix = Configuration::get('PS_WEIGHT_UNIT');
		if ($this->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE)
			$suffix = $currency->sign;
		return $suffix;
	}

	public static function getIdTaxRulesGroupByIdCarrier($id_carrier)
	{
		if (!isset(self::$_cache_tax_rule[(int)$id_carrier]))
		{
			 self::$_cache_tax_rule[$id_carrier] = Db::getInstance()->getValue('
			 SELECT `id_tax_rules_group`
			 FROM `'._DB_PREFIX_.'carrier`
			 WHERE `id_carrier` = '.(int)$id_carrier);
	   }

	   return self::$_cache_tax_rule[$id_carrier];
	}
	
	/**
	 * This tricky method generate a sql clause to check if ranged data are overloaded by multishop
	 * 
	 * @since 1.5.0
	 * @param string $rangeTable
	 * @param Shop $shop
	 * @return string
	 */
	public static function sqlDeliveryRangeShop($rangeTable, Shop $shop = null, $alias = 'd')
	{
		if (!$shop)
			$shop = Context::getContext()->shop;
		$shopID = $shop->getID();
		$shopGroupID = $shop->getGroupID();
		$where = '';
		if (!$shopID && !$shopGroupID)
			$where = 'AND d2.id_shop IS NULL AND d2.id_group_shop IS NULL';
		else if (!$shopID)
			$where = 'AND ((d2.id_group_shop IS NULL OR d2.id_group_shop = '.$shopGroupID.') AND d2.id_shop IS NULL)';
		else
			$where = 'AND (d2.id_shop = '.$shopID.' OR (d2.id_group_shop = '.$shopGroupID.' AND d2.id_shop IS NULL) OR (d2.id_group_shop IS NULL AND d2.id_shop IS NULL))';
		
		$sql = 'AND '.$alias.'.id_delivery = (
					SELECT d2.id_delivery
					FROM '._DB_PREFIX_.'delivery d2
					WHERE d2.id_carrier = '.$alias.'.id_carrier
						AND d2.id_zone = '.$alias.'.id_zone
						AND d2.id_'.$rangeTable.' = '.$alias.'.id_'.$rangeTable.'
						'.$where.'
					ORDER BY d2.id_shop DESC, d2.id_group_shop DESC
					LIMIT 1
				)';
		return $sql;
	}
}

