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

class AddressCore extends ObjectModel
{
	/** @var integer Customer id which address belongs */
	public		$id_customer = NULL;

	/** @var integer Manufacturer id which address belongs */
	public		$id_manufacturer = NULL;

	/** @var integer Supplier id which address belongs */
	public		$id_supplier = NULL;

	/** @var integer Country id */
	public		$id_country;

	/** @var integer State id */
	public		$id_state;

	/** @var string Country name */
	public		$country;

	/** @var string Alias (eg. Home, Work...) */
	public		$alias;

	/** @var string Company (optional) */
	public 		$company;

	/** @var string Lastname */
	public 		$lastname;

	/** @var string Firstname */
	public 		$firstname;

	/** @var string Address first line */
	public 		$address1;

	/** @var string Address second line (optional) */
	public 		$address2;

	/** @var string Postal code */
	public 		$postcode;

	/** @var string City */
	public 		$city;

	/** @var string Any other useful information */
	public 		$other;

	/** @var string Phone number */
	public 		$phone;

	/** @var string Mobile phone number */
	public 		$phone_mobile;

	/** @var string VAT number */
	public 		$vat_number;

	/** @var string DNI number */
	public		$dni;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	/** @var boolean True if address has been deleted (staying in database as deleted) */
	public 		$deleted = 0;

	protected static $_idZones = array();
	protected static $_idCountries = array();

	protected	$fieldsRequired = array('id_country', 'alias', 'lastname', 'firstname', 'address1', 'city');
	protected	$fieldsSize = array('alias' => 32, 'company' => 32, 'lastname' => 32, 'firstname' => 32,
									'address1' => 128, 'address2' => 128, 'postcode' => 12, 'city' => 64,
									'other' => 300, 'phone' => 16, 'phone_mobile' => 16, 'dni' => 16);
	protected	$fieldsValidate = array('id_customer' => 'isNullOrUnsignedId', 'id_manufacturer' => 'isNullOrUnsignedId',
										'id_supplier' => 'isNullOrUnsignedId', 'id_country' => 'isUnsignedId', 'id_state' => 'isNullOrUnsignedId',
										'alias' => 'isGenericName', 'company' => 'isGenericName', 'lastname' => 'isName','vat_number' => 'isGenericName',
										'firstname' => 'isName', 'address1' => 'isAddress', 'address2' => 'isAddress', 'postcode'=>'isPostCode',
										'city' => 'isCityName', 'other' => 'isMessage',
										'phone' => 'isPhoneNumber', 'phone_mobile' => 'isPhoneNumber', 'deleted' => 'isBool', 'dni' => 'isDniLite');

	protected 	$table = 'address';
	protected 	$identifier = 'id_address';
	protected	$_includeVars = array('addressType' => 'table');
	protected	$_includeContainer = false;

	protected	$webserviceParameters = array(
		'objectsNodeName' => 'addresses',
		'fields' => array(
			'id_customer' => array('xlink_resource'=> 'customers'),
			'id_manufacturer' => array('xlink_resource'=> 'manufacturers'),
			'id_supplier' => array('xlink_resource'=> 'suppliers'),
			'id_country' => array('xlink_resource'=> 'countries'),
			'id_state' => array('xlink_resource'=> 'states'),
		),
	);

	/**
	 * Build an address
	 *
	 * @param integer $id_address Existing address id in order to load object (optional)
	 */
	public	function __construct($id_address = NULL, $id_lang = NULL)
	{
		parent::__construct($id_address);

		/* Get and cache address country name */
		if ($this->id)
		{
			$result = Db::getInstance()->getRow('SELECT `name` FROM `'._DB_PREFIX_.'country_lang`
												WHERE `id_country` = '.(int)($this->id_country).'
												AND `id_lang` = '.($id_lang ? (int)($id_lang) : Configuration::get('PS_LANG_DEFAULT')));
			$this->country = $result['name'];
		}
	}

	public function add($autodate = true, $nullValues = false)
	{
		if (!parent::add($autodate, $nullValues))
			return false;

		if (Validate::isUnsignedId($this->id_customer))
			Customer::resetAddressCache($this->id_customer);
		return true;
	}

	public function delete()
	{
		if (Validate::isUnsignedId($this->id_customer))
			Customer::resetAddressCache($this->id_customer);

		if (!$this->isUsed())
			return parent::delete();
		else
		{
			$class =  get_class($this);
			$obj = new $class($this->id);
			$obj->deleted = true;
			return $obj->update();
		}
	}

	public function getFields()
	{
		parent::validateFields();
		if (isset($this->id))
			$fields['id_address'] = (int)($this->id);
		$fields['id_customer'] = is_null($this->id_customer) ? 0 : (int)($this->id_customer);
		$fields['id_manufacturer'] = is_null($this->id_manufacturer) ? 0 : (int)($this->id_manufacturer);
		$fields['id_supplier'] = is_null($this->id_supplier) ? 0 : (int)($this->id_supplier);
		$fields['id_country'] = (int)($this->id_country);
		$fields['id_state'] = (int)($this->id_state);
		$fields['alias'] = pSQL($this->alias);
		$fields['company'] = pSQL($this->company);
		$fields['lastname'] = pSQL($this->lastname);
		$fields['firstname'] = pSQL($this->firstname);
		$fields['address1'] = pSQL($this->address1);
		$fields['address2'] = pSQL($this->address2);
		$fields['postcode'] = pSQL($this->postcode);
		$fields['city'] = pSQL($this->city);
		$fields['other'] = pSQL($this->other);
		$fields['phone'] = pSQL($this->phone);
		$fields['phone_mobile'] = pSQL($this->phone_mobile);
		$fields['vat_number'] = pSQL($this->vat_number);
		$fields['dni'] = pSQL($this->dni);
		$fields['deleted'] = (int)($this->deleted);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}

	public function validateControler($htmlentities = true)
	{
		$errors = parent::validateControler($htmlentities);
		if (!Configuration::get('VATNUMBER_CHECKING'))
			return $errors;
		include_once(_PS_MODULE_DIR_.'vatnumber/vatnumber.php');
		if (class_exists('VatNumber', false))
			return array_merge($errors, VatNumber::WebServiceCheck($this->vat_number));
		return $errors;
	}
	/**
	 * Get zone id for a given address
	 *
	 * @param integer $id_address Address id for which we want to get zone id
	 * @return integer Zone id
	 */
	public static function getZoneById($id_address)
	{
		if (isset(self::$_idZones[$id_address]))
			return self::$_idZones[$id_address];

		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT s.`id_zone` AS id_zone_state, c.`id_zone`
		FROM `'._DB_PREFIX_.'address` a
		LEFT JOIN `'._DB_PREFIX_.'country` c ON c.`id_country` = a.`id_country`
		LEFT JOIN `'._DB_PREFIX_.'state` s ON s.`id_state` = a.`id_state`
		WHERE a.`id_address` = '.(int)($id_address));

		self::$_idZones[$id_address] = (int)((int)($result['id_zone_state']) ? $result['id_zone_state'] : $result['id_zone']);
		return self::$_idZones[$id_address];
	}

	/**
	 * Check if country is active for a given address
	 *
	 * @param integer $id_address Address id for which we want to get country status
	 * @return integer Country status
	 */
	public static function isCountryActiveById($id_address)
	{
		if (!$result = Db::getInstance()->getRow('
		SELECT c.`active`
		FROM `'._DB_PREFIX_.'address` a
		LEFT JOIN `'._DB_PREFIX_.'country` c ON c.`id_country` = a.`id_country`
		WHERE a.`id_address` = '.(int)($id_address)))
			return false;
		return ($result['active']);
	}

	/**
	 * Check if address is used (at least one order placed)
	 *
	 * @return integer Order count for this address
	 */
	public function isUsed()
	{
		$result = Db::getInstance()->getRow('
		SELECT COUNT(`id_order`) AS used
		FROM `'._DB_PREFIX_.'orders`
		WHERE `id_address_delivery` = '.(int)($this->id).'
		OR `id_address_invoice` = '.(int)($this->id));

		return isset($result['used']) ? $result['used'] : false;
	}

	/**
	 * @param int $id_address
	 * @return int
	 * @deprecated
	 */
	static public function getManufacturerIdByAddress($id_address)
	{
		Tools::displayAsDeprecated();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT `id_manufacturer` FROM `'._DB_PREFIX_.'address`
			WHERE `id_address` = '.(int)($id_address));
		return isset($result['id_manufacturer']) ? $result['id_manufacturer'] : false;
	}

	static public function getCountryAndState($id_address)
	{
		if (isset(self::$_idCountries[$id_address]))
			return self::$_idCountries[$id_address];
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_country`, `id_state`, `vat_number`, `postcode` FROM `'._DB_PREFIX_.'address`
		WHERE `id_address` = '.(int)($id_address));
		self::$_idCountries[$id_address] = $result;
		return $result;
	}

	/**
	* Specify if an address is already in base
	*
	* @param $id_address Address id
	* @return boolean
	*/
	static public function addressExists($id_address)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_address`
		FROM '._DB_PREFIX_.'address a
		WHERE a.`id_address` = '.(int)($id_address));

		return isset($row['id_address']);
	}

	static public function getFirstCustomerAddressId($id_customer, $active = true)
	{
		return Db::getInstance()->getValue('
			SELECT `id_address`
			FROM `'._DB_PREFIX_.'address`
			WHERE `id_customer` = '.(int)($id_customer).' AND `deleted` = 0'.($active ? ' AND `active` = 1' : '')
		);
	}
}

