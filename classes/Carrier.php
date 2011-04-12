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

class CarrierCore extends ObjectModel
{
	const SHIPPING_METHOD_DEFAULT = 0;
	const SHIPPING_METHOD_WEIGHT = 1;
	const SHIPPING_METHOD_PRICE = 2;

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

	/** @var int shipping behavior: by weight or by price */
	public 		$shipping_method = 0;

	/** @var boolean Shipping external */
	public		$shipping_external = 0;

	/** @var string Shipping external */
	public		$external_module_name = NULL;

	/** @var boolean Need Range */
	public		$need_range = 0;

 	protected 	$fieldsRequired = array('name', 'active');
 	protected 	$fieldsSize = array('name' => 64);
 	protected 	$fieldsValidate = array('id_tax_rules_group' => 'isInt', 'name' => 'isCarrierName', 'active' => 'isBool', 'url' => 'isAbsoluteUrl', 'shipping_handling' => 'isBool', 'range_behavior' => 'isBool', 'shipping_method' => 'isUnsignedInt');
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
		if (!$result = Db::getInstance()->ExecuteS('SELECT `id_carrier` FROM `'._DB_PREFIX_.$this->table.'` WHERE `deleted` = 0'))
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
	public function getDeliveryPriceByWeight($totalWeight, $id_zone)
	{
		$cache_key = $this->id.'_'.$totalWeight.'_'.$id_zone;
		if (!isset(self::$priceByWeight[$cache_key]))
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT d.`price`
			FROM `'._DB_PREFIX_.'delivery` d
			LEFT JOIN `'._DB_PREFIX_.'range_weight` w ON (d.`id_range_weight` = w.`id_range_weight`)
			WHERE d.`id_zone` = '.(int)($id_zone).'
			AND '.(float)($totalWeight).' >= w.`delimiter1`
			AND '.(float)($totalWeight).' < w.`delimiter2`
			AND d.`id_carrier` = '.(int)($this->id).'
			ORDER BY w.`delimiter1` ASC');
			if (!isset($result['price']))
				self::$priceByWeight[$cache_key] = $this->getMaxDeliveryPriceByWeight($id_zone);
			else
				self::$priceByWeight[$cache_key] = $result['price'];
		}
		return self::$priceByWeight[$cache_key];
	}

	static public function checkDeliveryPriceByWeight($id_carrier, $totalWeight, $id_zone)
	{
		$cache_key = $id_carrier.'_'.$totalWeight.'_'.$id_zone;
		if (!isset(self::$priceByWeight2[$cache_key]))
		{
			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT d.`price`
			FROM `'._DB_PREFIX_.'delivery` d
			LEFT JOIN `'._DB_PREFIX_.'range_weight` w ON d.`id_range_weight` = w.`id_range_weight`
			WHERE d.`id_zone` = '.(int)($id_zone).'
			AND '.(float)($totalWeight).' >= w.`delimiter1`
			AND '.(float)($totalWeight).' < w.`delimiter2`
			AND d.`id_carrier` = '.(int)($id_carrier).'
			ORDER BY w.`delimiter1` ASC');
			self::$priceByWeight2[$cache_key] = (isset($result['price']));
		}
		return self::$priceByWeight2[$cache_key];
	}

	public function getMaxDeliveryPriceByWeight($id_zone)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT d.`price`
		FROM `'._DB_PREFIX_.'delivery` d
		INNER JOIN `'._DB_PREFIX_.'range_weight` w ON d.`id_range_weight` = w.`id_range_weight`
		WHERE d.`id_zone` = '.(int)($id_zone).'
		AND d.`id_carrier` = '.(int)($this->id).'
		ORDER BY w.`delimiter2` DESC LIMIT 1');
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
	public function getDeliveryPriceByPrice($orderTotal, $id_zone, $id_currency = NULL)
	{
		$cache_key = $this->id.'_'.$orderTotal.'_'.$id_zone.'_'.$id_currency;
		if (!isset(self::$priceByPrice[$cache_key]))
		{
			if (!empty($id_currency))
				$orderTotal = Tools::convertPrice($orderTotal, $id_currency, false);

			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT d.`price`
			FROM `'._DB_PREFIX_.'delivery` d
			LEFT JOIN `'._DB_PREFIX_.'range_price` r ON d.`id_range_price` = r.`id_range_price`
			WHERE d.`id_zone` = '.(int)($id_zone).'
			AND '.(float)($orderTotal).' >= r.`delimiter1`
			AND '.(float)($orderTotal).' < r.`delimiter2`
			AND d.`id_carrier` = '.(int)($this->id).'
			ORDER BY r.`delimiter1` ASC');
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
	static public function checkDeliveryPriceByPrice($id_carrier, $orderTotal, $id_zone, $id_currency = NULL)
	{
		$cache_key = $id_carrier.'_'.$orderTotal.'_'.$id_zone.'_'.$id_currency;
		if (!isset(self::$priceByPrice2[$cache_key]))
		{
			if (!empty($id_currency))
				$orderTotal = Tools::convertPrice($orderTotal, $id_currency, false);

			$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT d.`price`
			FROM `'._DB_PREFIX_.'delivery` d
			LEFT JOIN `'._DB_PREFIX_.'range_price` r ON d.`id_range_price` = r.`id_range_price`
			WHERE d.`id_zone` = '.(int)($id_zone).'
			AND '.(float)($orderTotal).' >= r.`delimiter1`
			AND '.(float)($orderTotal).' < r.`delimiter2`
			AND d.`id_carrier` = '.(int)($id_carrier).'
			ORDER BY r.`delimiter1` ASC');
			self::$priceByPrice2[$cache_key] = (isset($result['price']));
		}
		return self::$priceByPrice2[$cache_key];
	}

	public function getMaxDeliveryPriceByPrice($id_zone)
	{
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT d.`price`
		FROM `'._DB_PREFIX_.'delivery` d
		INNER JOIN `'._DB_PREFIX_.'range_price` r ON d.`id_range_price` = r.`id_range_price`
		WHERE d.`id_zone` = '.(int)($id_zone).'
		AND d.`id_carrier` = '.(int)($this->id).'
		ORDER BY r.`delimiter2` DESC LIMIT 1');
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
	public static function getDeliveryPriceByRanges($rangeTable, $id_carrier)
	{
		$rangeTable = pSQL($rangeTable);
		return Db::getInstance()->ExecuteS('
		SELECT d.`id_'.$rangeTable.'`, d.`id_carrier`, d.`id_zone`, d.`price`
		FROM `'._DB_PREFIX_.'delivery` d
		LEFT JOIN `'._DB_PREFIX_.$rangeTable.'` r ON r.`id_'.$rangeTable.'` = d.`id_'.$rangeTable.'`
		WHERE (d.`id_'.$rangeTable.'` IS NOT NULL AND d.`id_'.$rangeTable.'` != 0 AND d.`id_carrier` = '.(int)($id_carrier).')
		ORDER BY r.`delimiter1` ASC');
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
	public static function getCarriers($id_lang, $active = false, $delete = false, $id_zone = false, $ids_group = NULL, $modules_filters = 1)
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
				return (array());
		}
		$sql = '
			SELECT c.*, cl.delay
			FROM `'._DB_PREFIX_.'carrier` c
			LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = '.(int)($id_lang).')
			LEFT JOIN `'._DB_PREFIX_.'carrier_zone` cz  ON (cz.`id_carrier` = c.`id_carrier`)'.
			($id_zone ? 'LEFT JOIN `'._DB_PREFIX_.'zone` z  ON (z.`id_zone` = '.(int)($id_zone).')' : '').'
			WHERE c.`deleted` '.($delete ? '= 1' : ' = 0').
			($active ? ' AND c.`active` = 1' : '').
			($id_zone ? ' AND cz.`id_zone` = '.(int)($id_zone).'
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

	public static function getCarriersForOrder($id_zone, $groups = NULL)
	{
		global $cookie, $cart;

		if (is_array($groups) AND !empty($groups))
			$result = Carrier::getCarriers((int)($cookie->id_lang), true, false, (int)($id_zone), $groups, PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
		else
			$result = Carrier::getCarriers((int)($cookie->id_lang), true, false, (int)($id_zone), array(1), PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE);
		$resultsArray = array();
		foreach ($result AS $k => $row)
		{
			$carrier = new Carrier((int)($row['id_carrier']));
			// Get only carriers that are compliant with shipping method
			if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT AND $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)
			OR ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE AND $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
			{
				unset($result[$k]);
				continue ;
			}

			// If out-of-range behavior carrier is set on "Desactivate carrier"
			if ($row['range_behavior'])
			{
				// Get id zone
		        if (!$id_zone)
					$id_zone = (int)($defaultCountry->id_zone);

				// Get only carriers that have a range compatible with cart
				if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT AND (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $cart->getTotalWeight(), $id_zone)))
				OR ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE AND (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING), $id_zone, $cart->id_currency))))
					{
						unset($result[$k]);
						continue ;
					}
			}
			$row['name'] = (strval($row['name']) != '0' ? $row['name'] : Configuration::get('PS_SHOP_NAME'));
			$row['price'] = $cart->getOrderShippingCost((int)($row['id_carrier']));
			$row['price_tax_exc'] = $cart->getOrderShippingCost((int)($row['id_carrier']), false);
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

	/**
	 * @param int $id_country
	 * @param array $groups
	 * @return Array carriers list
	 * @deprecated
	 */
	public static function getCarriersOpc($id_country, $groups = NULL)
	{
		Tools::displayAsDeprecated();

		return self::getCarriersForOrder((int)Country::getIdZone((int)($id_country)), $groups);
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
	public function deleteDeliveryPrice($rangeTable)
	{
		return Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'delivery` WHERE `id_carrier` = '.(int)($this->id).' AND (`id_'.$rangeTable.'` IS NOT NULL OR `id_'.$rangeTable.'` = 0)');
	}

	/**
	 * Add new delivery prices
	 *
	 * @param string $priceList Prices list separated by commas
	 * @return boolean Insertion result
	 */
	public function addDeliveryPrice($priceList)
	{
	 	if (!Validate::isValuesList($priceList))
	 		die(Tools::displayError());
		return Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'delivery` (`id_range_price`, `id_range_weight`, `id_carrier`, `id_zone`, `price`)
		VALUES '.$priceList);
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

		$oldLogo = _PS_SHIP_IMG_DIR_.'/'.(int)($oldId).'.jpg';
		if (file_exists($oldLogo))
			copy($oldLogo, _PS_SHIP_IMG_DIR_.'/'.(int)($this->id).'.jpg');

		// Copy existing ranges price
		$res = Db::getInstance()->ExecuteS('
		SELECT * FROM `'._DB_PREFIX_.'range_price`
		WHERE id_carrier = '.(int)($oldId));
		foreach ($res AS $val)
		{
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'range_price` (`id_carrier`, `delimiter1`, `delimiter2`)
			VALUES ('.(int)($this->id).','.(float)($val['delimiter1']).','.(float)($val['delimiter2']).')');
			$maxRangePrice = Db::getInstance()->Insert_ID();
			$res2 = Db::getInstance()->ExecuteS('
			SELECT * FROM `'._DB_PREFIX_.'delivery`
			WHERE id_carrier = '.(int)($oldId).'
			AND id_range_price = '.(int)($val['id_range_price']));
			foreach ($res2 AS $val2)
				Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'delivery` (`id_carrier`,`id_range_price`,`id_range_weight`,`id_zone`, `price`)
				VALUES ('.(int)($this->id).','.(int)($maxRangePrice).',NULL,'.(int)($val2['id_zone']).','.(float)($val2['price']).')');
		}

		// Copy existing ranges weight
		$res = Db::getInstance()->ExecuteS('
		SELECT * FROM `'._DB_PREFIX_.'range_weight`
		WHERE id_carrier = '.(int)($oldId));
		foreach ($res as $val)
		{
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'range_weight` (`id_carrier`, `delimiter1`, `delimiter2`)
			VALUES ('.(int)($this->id).','.(float)($val['delimiter1']).','.(float)($val['delimiter2']).')');
			$maxRangeWeight = Db::getInstance()->Insert_ID();
			$res2 = Db::getInstance()->ExecuteS('
			SELECT * FROM `'._DB_PREFIX_.'delivery`
			WHERE id_carrier = '.(int)($oldId).'
			AND id_range_weight = '.(int)($val['id_range_weight']));
			foreach ($res2 as $val2)
				Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'delivery` (`id_carrier`,`id_range_price`,`id_range_weight`,`id_zone`, `price`)
				VALUES ('.(int)($this->id).',NULL,'.(int)($maxRangeWeight).','.(int)($val2['id_zone']).','.(float)($val2['price']).')');
		}

		// Copy existing zones
		$res = Db::getInstance()->ExecuteS('
		SELECT * FROM `'._DB_PREFIX_.'carrier_zone`
		WHERE id_carrier = '.(int)($oldId));
		foreach ($res as $val)
			Db::getInstance()->Execute('
			INSERT INTO `'._DB_PREFIX_.'carrier_zone` (`id_carrier`, `id_zone`)
			VALUES ('.(int)($this->id).','.(int)($val['id_zone']).')');

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


	/**
	* Get the price without taxes defined in carrier
	* @deprecated
	**/
	public function getPriceWithoutTaxes($productPrice)
	{
	    Tools::displayAsDeprecated();
		$tax = new Tax($this->id_tax);
		return round($productPrice - ($productPrice * $tax->rate / 100), 2);
	}


	public function getShippingMethod()
	{
		$method = (int)($this->shipping_method);

		if ($this->shipping_method == Carrier::SHIPPING_METHOD_DEFAULT)
		{
			// backward compatibility
			if ((int)(Configuration::get('PS_SHIPPING_METHOD')))
				$method = Carrier::SHIPPING_METHOD_WEIGHT;
			else
				$method = Carrier::SHIPPING_METHOD_PRICE;
		}

		return $method;
	}

	public function getRangeTable()
	{
		return ($this->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) ? 'range_weight' : 'range_price';
	}

	public function getRangeObject()
	{
		return ($this->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT) ? new RangeWeight() : new RangePrice();
	}

	public function getRangeSuffix()
	{
		$suffix = Configuration::get('PS_WEIGHT_UNIT');
		if ($this->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE)
		{
			$currency = new Currency(Configuration::get('PS_CURRENCY_DEFAULT'));
			$suffix = $currency->sign;
		}
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
}

