<?php
/*
* 2007-2012 PrestaShop
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
*  @copyright  2007-2012 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class CustomerCore extends ObjectModel
{
	public $id;

	public $id_shop;

	public $id_shop_group;

	/** @var string Secure key */
	public $secure_key;

	/** @var string protected note */
	public $note;

	/** @var integer Gender ID */
	public $id_gender = 0;

	/** @var integer Default group ID */
	public $id_default_group = _PS_DEFAULT_CUSTOMER_GROUP_;

	/** @var string Lastname */
	public $lastname;

	/** @var string Firstname */
	public $firstname;

	/** @var string Birthday (yyyy-mm-dd) */
	public $birthday = null;

	/** @var string e-mail */
	public $email;

	/** @var boolean Newsletter subscription */
	public $newsletter;

	/** @var string Newsletter ip registration */
	public $ip_registration_newsletter;

	/** @var string Newsletter ip registration */
	public $newsletter_date_add;

	/** @var boolean Opt-in subscription */
	public $optin;

	/** @var string WebSite **/
	public $website;

	/** @var string Company */
	public $company;

	/** @var string SIRET */
	public $siret;

	/** @var string APE */
	public $ape;

	/** @var float Outstanding allow amount (B2B opt)  */
	public $outstanding_allow_amount = 0;

	/** @var integer Show public prices (B2B opt) */
	public $show_public_prices = 0;

	/** @var int Risk ID (B2B opt) */
	public $id_risk;

	/** @var integer Max payment day */
	public $max_payment_days = 0;

	/** @var integer Password */
	public $passwd;

	/** @var datetime Password */
	public $last_passwd_gen;

	/** @var boolean Status */
	public $active = true;

	/** @var boolean Status */
	public $is_guest = 0;

	/** @var boolean True if carrier has been deleted (staying in database as deleted) */
	public $deleted = 0;

	/** @var string Object creation date */
	public $date_add;

	/** @var string Object last modification date */
	public $date_upd;

	public $years;
	public $days;
	public $months;

	/** @var int customer id_country as determined by geolocation */
	public $geoloc_id_country;
	/** @var int customer id_state as determined by geolocation */
	public $geoloc_id_state;
	/** @var string customer postcode as determined by geolocation */
	public $geoloc_postcode;

	/** @var boolean is the customer logged in */
	public $logged = 0;

	/** @var int id_guest meaning the guest table, not the guest customer  */
	public $id_guest;

	public $groupBox;

	protected $webserviceParameters = array(
		'fields' => array(
			'id_default_group' => array('xlink_resource' => 'groups'),
			'newsletter_date_add' => array(),
			'ip_registration_newsletter' => array(),
			'last_passwd_gen' => array('setter' => null),
			'secure_key' => array('setter' => null),
			'deleted' => array(),
			'passwd' => array('setter' => 'setWsPasswd'),
		),
		'associations' => array(
			'groups' => array('resource' => 'group'),
		)
	);

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'customer',
		'primary' => 'id_customer',
		'fields' => array(
			'secure_key' => 				array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
			'lastname' => 					array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
			'firstname' => 					array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
			'email' => 						array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
			'passwd' => 					array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 32),
			'last_passwd_gen' =>			array('type' => self::TYPE_STRING, 'copy_post' => false),
			'id_gender' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'birthday' => 					array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),
			'newsletter' => 				array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'newsletter_date_add' =>		array('type' => self::TYPE_DATE,'copy_post' => false),
			'ip_registration_newsletter' =>	array('type' => self::TYPE_STRING, 'copy_post' => false),
			'optin' => 						array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'website' =>					array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
			'company' =>					array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'siret' =>						array('type' => self::TYPE_STRING, 'validate' => 'isSiret'),
			'ape' =>						array('type' => self::TYPE_STRING, 'validate' => 'isApe'),
			'outstanding_allow_amount' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
			'show_public_prices' =>			array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
			'id_risk' =>					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
			'max_payment_days' =>			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
			'active' => 					array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
			'deleted' => 					array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
			'note' => 						array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),
			'is_guest' =>					array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
			'id_shop' => 					array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
			'id_shop_group' => 				array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
			'id_default_group' => 			array('type' => self::TYPE_INT, 'copy_post' => false),
			'date_add' => 					array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
			'date_upd' => 					array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
		),
	);

	protected static $_defaultGroupId = array();
	protected static $_customerHasAddress = array();
	protected static $_customer_groups = array();

	public function add($autodate = true, $null_values = true)
	{
		$this->id_shop = ($this->id_shop) ? $this->id_shop : Context::getContext()->shop->id;
		$this->id_shop_group = ($this->id_shop_group) ? $this->id_shop_group : Context::getContext()->shop->id_shop_group;
		$this->birthday = (empty($this->years) ? $this->birthday : (int)$this->years.'-'.(int)$this->months.'-'.(int)$this->days);
		$this->secure_key = md5(uniqid(rand(), true));
		$this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_FRONT').'minutes'));
		
		if ($this->newsletter && !Validate::isDate($this->newsletter_date_add))
			$this->newsletter_date_add = date('Y-m-d H:i:s');
			
		if ($this->id_default_group == _PS_DEFAULT_CUSTOMER_GROUP_)
			if ($this->is_guest)
				$this->id_default_group = Configuration::get('PS_GUEST_GROUP');
			else
				$this->id_default_group = Configuration::get('PS_CUSTOMER_GROUP');

		/* Can't create a guest customer, if this feature is disabled */
		if ($this->is_guest && !Configuration::get('PS_GUEST_CHECKOUT_ENABLED'))
			return false;
	 	$success = parent::add($autodate, $null_values);
		$this->updateGroup($this->groupBox);
		return $success;
	}

	public function update($nullValues = false)
	{
		$this->birthday = (empty($this->years) ? $this->birthday : (int)$this->years.'-'.(int)$this->months.'-'.(int)$this->days);

		if ($this->newsletter && !Validate::isDate($this->newsletter_date_add))
			$this->newsletter_date_add = date('Y-m-d H:i:s');
		if (Context::getContext()->controller->controller_type == 'admin')
			$this->updateGroup($this->groupBox);
			
		if ($this->deleted)
		{
			$addresses = $this->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
			foreach ($addresses as $address)
			{
				$obj = new Address((int)$address['id_address']);
				$obj->delete();
			}
		}

	 	return parent::update(true);
	}

	public function delete()
	{
		if (!count(Order::getCustomerOrders((int)$this->id)))
		{
			$addresses = $this->getAddresses((int)Configuration::get('PS_LANG_DEFAULT'));
			foreach ($addresses as $address)
			{
				$obj = new Address((int)$address['id_address']);
				$obj->delete();
			}
		}
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_customer` = '.(int)$this->id);
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'message WHERE id_customer='.(int)$this->id);
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'specific_price WHERE id_customer='.(int)$this->id);		
		Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'compare WHERE id_customer='.(int)$this->id);
				
		$carts = Db::getInstance()->executes('SELECT id_cart 
															FROM '._DB_PREFIX_.'cart
															WHERE id_customer='.(int)$this->id);
		if ($carts)
			foreach ($carts as $cart)
			{
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart WHERE id_cart='.(int)$cart['id_cart']);
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'cart_product WHERE id_cart='.(int)$cart['id_cart']);
			}
		
		$cts = Db::getInstance()->executes('SELECT id_customer_thread 
															FROM '._DB_PREFIX_.'customer_thread
															WHERE id_customer='.(int)$this->id);
		if ($cts)
			foreach ($cts as $ct)
			{
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'customer_thread WHERE id_customer_thread='.(int)$ct['id_customer_thread']);
				Db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'customer_message WHERE id_customer_thread='.(int)$ct['id_customer_thread']);
			}

		CartRule::deleteByIdCustomer((int)$this->id);
		return parent::delete();
	}

	/**
	 * Return customers list
	 *
	 * @return array Customers
	 */
	public static function getCustomers()
	{
		$sql = 'SELECT `id_customer`, `email`, `firstname`, `lastname`
				FROM `'._DB_PREFIX_.'customer`
				WHERE 1 '.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
				ORDER BY `id_customer` ASC';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	/**
	 * Return customer instance from its e-mail (optionnaly check password)
	 *
	 * @param string $email e-mail
	 * @param string $passwd Password is also checked if specified
	 * @return Customer instance
	 */
	public function getByEmail($email, $passwd = null)
	{
		if (!Validate::isEmail($email) || ($passwd && !Validate::isPasswd($passwd)))
			die (Tools::displayError());

		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'customer`
				WHERE `email` = \''.pSQL($email).'\'
					'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).'
					'.(isset($passwd) ? 'AND `passwd` = \''.Tools::encrypt($passwd).'\'' : '').'
					AND `deleted` = 0
					AND `is_guest` = 0';
		$result = Db::getInstance()->getRow($sql);

		if (!$result)
			return false;
		$this->id = $result['id_customer'];
		foreach ($result as $key => $value)
			if (key_exists($key, $this))
				$this->{$key} = $value;

		return $this;
	}

	/**
	 * Retrieve customers by email address
	 *
	 * @static
	 * @param $email
	 * @return array
	 */
	public static function getCustomersByEmail($email)
	{
		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'customer`
				WHERE `email` = \''.pSQL($email).'\'
					'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);

		return Db::getInstance()->ExecuteS($sql);
	}


	/**
	 * Check id the customer is active or not
	 *
	 * @return boolean customer validity
	 */
	public static function isBanned($id_customer)
	{
		if (!Validate::isUnsignedId($id_customer))
			return true;
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_customer`
		FROM `'._DB_PREFIX_.'customer`
		WHERE `id_customer` = \''.(int)$id_customer.'\'
		AND active = 1
		AND `deleted` = 0');
		if (isset($result['id_customer']))
			return false;
		return true;
	}

	/**
	 * Check if e-mail is already registered in database
	 *
	 * @param string $email e-mail
	 * @param $return_id boolean
	 * @param $ignore_guest boolean, to exclude guest customer
	 * @return Customer ID if found, false otherwise
	 */
	public static function customerExists($email, $return_id = false, $ignore_guest = true)
	{
		if (!Validate::isEmail($email))
			die (Tools::displayError());

		$sql = 'SELECT `id_customer`
				FROM `'._DB_PREFIX_.'customer`
				WHERE `email` = \''.pSQL($email).'\'
					'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER).
					($ignore_guest ? ' AND `is_guest` = 0' : '');
		$result = Db::getInstance()->getRow($sql);

		if ($return_id)
			return $result['id_customer'];
		return isset($result['id_customer']);
	}

	/**
	 * Check if an address is owned by a customer
	 *
	 * @param integer $id_customer Customer ID
	 * @param integer $id_address Address ID
	 * @return boolean result
	 */
	public static function customerHasAddress($id_customer, $id_address)
	{
		if (!array_key_exists($id_customer, self::$_customerHasAddress))
		{
			self::$_customerHasAddress[$id_customer] = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_address`
			FROM `'._DB_PREFIX_.'address`
			WHERE `id_customer` = '.(int)$id_customer.'
			AND `id_address` = '.(int)$id_address.'
			AND `deleted` = 0');
		}
		return self::$_customerHasAddress[$id_customer];
	}

	public static function resetAddressCache($id_customer)
	{
		if (array_key_exists($id_customer, self::$_customerHasAddress))
			unset(self::$_customerHasAddress[$id_customer]);
	}

	/**
	 * Return customer addresses
	 *
	 * @param integer $id_lang Language ID
	 * @return array Addresses
	 */
	public function getAddresses($id_lang)
	{
		$sql = 'SELECT a.*, cl.`name` AS country, s.name AS state, s.iso_code AS state_iso
				FROM `'._DB_PREFIX_.'address` a
				LEFT JOIN `'._DB_PREFIX_.'country` c ON (a.`id_country` = c.`id_country`)
				LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
				LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)
				WHERE `id_lang` = '.(int)$id_lang.' AND `id_customer` = '.(int)$this->id.' AND a.`deleted` = 0';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	/**
	 * Count the number of addresses for a customer
	 *
	 * @param integer $id_customer Customer ID
	 * @return integer Number of addresses
	 */
	public static function getAddressesTotalById($id_customer)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(`id_address`)
			FROM `'._DB_PREFIX_.'address`
			WHERE `id_customer` = '.(int)$id_customer.'
			AND `deleted` = 0'
		);
	}

	/**
	 * Check if customer password is the right one
	 *
	 * @param string $passwd Password
	 * @return boolean result
	 */
	public static function checkPassword($id_customer, $passwd)
	{
		if (!Validate::isUnsignedId($id_customer) || !Validate::isMd5($passwd))
			die (Tools::displayError());

		$sql = 'SELECT `id_customer`
				FROM `'._DB_PREFIX_.'customer`
				WHERE `id_customer` = '.$id_customer.'
					AND `passwd` = \''.$passwd.'\'';
		return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
	}

	/**
	 * Light back office search for customers
	 *
	 * @param string $query Searched string
	 * @return array Corresponding customers
	 */
	public static function searchByName($query)
	{
		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'customer`
				WHERE (
						`email` LIKE \'%'.pSQL($query).'%\'
						OR `id_customer` LIKE \'%'.pSQL($query).'%\'
						OR `lastname` LIKE \'%'.pSQL($query).'%\'
						OR `firstname` LIKE \'%'.pSQL($query).'%\'
					)'.Shop::addSqlRestriction(Shop::SHARE_CUSTOMER);
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
	}

	/**
	 * Search for customers by ip address
	 *
	 * @param string $ip Searched string
	 */
	public static function searchByIp($ip)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT c.*
		FROM `'._DB_PREFIX_.'customer` c
		LEFT JOIN `'._DB_PREFIX_.'guest` g ON g.id_customer = c.id_customer
		LEFT JOIN `'._DB_PREFIX_.'connections` co ON g.id_guest = co.id_guest
		WHERE co.`ip_address` = \''.ip2long(trim($ip)).'\'');
	}

	/**
	 * Return several useful statistics about customer
	 *
	 * @return array Stats
	 */
	public function getStats()
	{
		$result = Db::getInstance()->getRow('
		SELECT COUNT(`id_order`) AS nb_orders, SUM(`total_paid` / o.`conversion_rate`) AS total_orders
		FROM `'._DB_PREFIX_.'orders` o
		WHERE o.`id_customer` = '.(int)$this->id.'
		AND o.valid = 1');

		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT MAX(c.`date_add`) AS last_visit
		FROM `'._DB_PREFIX_.'guest` g
		LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest
		WHERE g.`id_customer` = '.(int)$this->id);

		$result3 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT (YEAR(CURRENT_DATE)-YEAR(c.`birthday`)) - (RIGHT(CURRENT_DATE, 5)<RIGHT(c.`birthday`, 5)) AS age
		FROM `'._DB_PREFIX_.'customer` c
		WHERE c.`id_customer` = '.(int)$this->id);

		$result['last_visit'] = $result2['last_visit'];
		$result['age'] = ($result3['age'] != date('Y') ? $result3['age'] : '--');
		return $result;
	}

	public function getLastConnections()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.date_add, COUNT(cp.id_page) AS pages, TIMEDIFF(MAX(cp.time_end), c.date_add) as time, http_referer,INET_NTOA(ip_address) as ipaddress
		FROM `'._DB_PREFIX_.'guest` g
		LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest
		LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
		WHERE g.`id_customer` = '.(int)$this->id.'
		GROUP BY c.`id_connections`
		ORDER BY c.date_add DESC
		LIMIT 10');
	}

	/*
	* Specify if a customer already in base
	*
	* @param $id_customer Customer id
	* @return boolean
	*/
	// DEPRECATED
	public function customerIdExists($id_customer)
	{
		return Customer::customerIdExistsStatic((int)$id_customer);
	}

	public static function customerIdExistsStatic($id_customer)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_customer`
		FROM '._DB_PREFIX_.'customer c
		WHERE c.`id_customer` = '.(int)$id_customer);

		return isset($row['id_customer']);
	}

	/**
	 * Update customer groups associated to the object
	 *
	 * @param array $list groups
	 */
	public function updateGroup($list)
	{
		$this->cleanGroups();
		if ($list && !empty($list))
			$this->addGroups($list);
		else
			$this->addGroups(array($this->id_default_group));
	}

	public function cleanGroups()
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_customer` = '.(int)$this->id);
	}

	public function addGroups($groups)
	{
		foreach ($groups as $group)
		{
			$row = array('id_customer' => (int)$this->id, 'id_group' => (int)$group);
			Db::getInstance()->insert('customer_group', $row);
		}
	}

	public static function getGroupsStatic($id_customer)
	{
		if (!Group::isFeatureActive() || $id_customer == 0)
			return array(Configuration::get('PS_CUSTOMER_GROUP'));

		if (!isset(self::$_customer_groups[$id_customer]))
		{
			self::$_customer_groups[$id_customer] = array();
			$result = Db::getInstance()->executeS('
			SELECT cg.`id_group`
			FROM '._DB_PREFIX_.'customer_group cg
			WHERE cg.`id_customer` = '.(int)$id_customer);
			foreach ($result as $group)
				self::$_customer_groups[$id_customer][] = (int)$group['id_group'];
		}
		return self::$_customer_groups[$id_customer];
	}

	public function getGroups()
	{
		return Customer::getGroupsStatic((int)$this->id);
	}

	/**
	 * @deprecated since 1.5
	 */
	public function isUsed()
	{
		Tools::displayAsDeprecated();
		return false;
	}

	public function getBoughtProducts()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT * FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		WHERE o.valid = 1 AND o.`id_customer` = '.(int)$this->id);
	}

	public static function getDefaultGroupId($id_customer)
	{
		if (!Group::isFeatureActive())
			return Configuration::get('PS_CUSTOMER_GROUP');

		if (!isset(self::$_defaultGroupId[(int)$id_customer]))
			self::$_defaultGroupId[(int)$id_customer] = Db::getInstance()->getValue('
				SELECT `id_default_group`
				FROM `'._DB_PREFIX_.'customer`
				WHERE `id_customer` = '.(int)$id_customer
			);
		return self::$_defaultGroupId[(int)$id_customer];
	}

	public static function getCurrentCountry($id_customer, Cart $cart = null)
	{
		if (!$cart)
			$cart = Context::getContext()->cart;
		if (!$cart || !$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})
			$id_address = (int)Db::getInstance()->getValue('
				SELECT `id_address`
				FROM `'._DB_PREFIX_.'address`
				WHERE `id_customer` = '.(int)$id_customer.'
				AND `deleted` = 0 ORDER BY `id_address`'
			);
		else
			$id_address = $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
		$ids = Address::getCountryAndState($id_address);
		return (int)$ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT');
	}

	public function toggleStatus()
	{
		parent::toggleStatus();

		/* Change status to active/inactive */
		return Db::getInstance()->execute('
		UPDATE `'.pSQL(_DB_PREFIX_.$this->def['table']).'`
		SET `date_upd` = NOW()
		WHERE `'.$this->def['primary'].'` = '.(int)$this->id);
	}


	public function isGuest()
	{
		return (bool)$this->is_guest;
	}

	public function transformToCustomer($id_lang, $password = null)
	{
		if (!$this->isGuest())
			return false;
		if (empty($password))
			$password = Tools::passwdGen();
		if (!Validate::isPasswd($password))
			return false;

		$this->is_guest = 0;
		$this->passwd = Tools::encrypt($password);
		$this->cleanGroups();
		$this->addGroups(array(Configuration::get('PS_CUSTOMER_GROUP'))); // add default customer group
		if ($this->update())
		{
			$vars = array(
				'{firstname}' => $this->firstname,
				'{lastname}' => $this->lastname,
				'{email}' => $this->email,
				'{passwd}' => $password
			);

			Mail::Send(
				(int)$id_lang,
				'guest_to_customer',
				Mail::l('Your guest account has been transformed to customer account', (int)$id_lang),
				$vars,
				$this->email,
				$this->firstname.' '.$this->lastname,
				null,
				null,
				null,
				null,
				_PS_MAIL_DIR_,
				false,
				(int)$this->id_shop
			);
			return true;
		}
		return false;
	}

	public function setWsPasswd($passwd)
	{
		if ($this->id != 0)
		{
			if ($this->passwd != $passwd)
				$this->passwd = Tools::encrypt($passwd);
		}
		else
			$this->passwd = Tools::encrypt($passwd);
		return true;
	}

	/**
	 * Check customer informations and return customer validity
	 *
	 * @since 1.5.0
	 * @param boolean $with_guest
	 * @return boolean customer validity
	 */
	public function isLogged($with_guest = false)
	{
		if (!$with_guest && $this->is_guest == 1)
			return false;

		/* Customer is valid only if it can be load and if object password is the same as database one */
		if ($this->logged == 1 && $this->id && Validate::isUnsignedId($this->id) && Customer::checkPassword($this->id, $this->passwd))
			return true;
		return false;
	}

	/**
	 * Logout
	 *
	 * @since 1.5.0
	 */
	public function logout()
	{
		if (isset(Context::getContext()->cookie))
			Context::getContext()->cookie->logout();
		$this->logged = 0;
	}

	/**
	 * Soft logout, delete everything links to the customer
	 * but leave there affiliate's informations
	 *
	 * @since 1.5.0
	 */
	public function mylogout()
	{
		if (isset(Context::getContext()->cookie))
			Context::getContext()->cookie->mylogout();
		$this->logged = 0;
	}

	public function getLastCart($with_order = true)
	{
		$carts = Cart::getCustomerCarts((int)$this->id, $with_order);
		if (!count($carts))
			return false;
		$cart = array_shift($carts);
		$cart = new Cart((int)$cart['id_cart']);
		return ($cart->nbProducts() === 0 ? (int)$cart->id : false);
	}

	public function getOutstanding()
	{
		$query = new DbQuery();
		$query->select('SUM(oi.total_paid_tax_incl)');
		$query->from('order_invoice', 'oi');
		$query->leftJoin('orders', 'o', 'oi.id_order = o.id_order');
		$query->groupBy('o.id_customer');
		$query->where('o.id_customer = '.(int)$this->id);
		$total_paid = (float)Db::getInstance()->getValue($query->build());

		$query = new DbQuery();
		$query->select('SUM(op.amount)');
		$query->from('order_payment', 'op');
		$query->leftJoin('order_invoice_payment', 'oip', 'op.id_order_payment = oip.id_order_payment');
		$query->leftJoin('orders', 'o', 'oip.id_order = o.id_order');
		$query->groupBy('o.id_customer');
		$query->where('o.id_customer = '.(int)$this->id);
		$total_rest = (float)Db::getInstance()->getValue($query->build());

		return $total_paid - $total_rest;
	}

	public function getWsGroups()
	{
		return Db::getInstance()->executeS('
			SELECT cg.`id_group` as id
			FROM '._DB_PREFIX_.'customer_group cg
			'.Shop::addSqlAssociation('group', 'cg').'
			WHERE cg.`id_customer` = '.(int)$this->id
		);
	}

	public function setWsGroups($result)
	{
		$groups = array();
		foreach ($result as $row)
			$groups[] = $row['id'];
		$this->cleanGroups();
		$this->addGroups($groups);
		return true;
	}

	/**
	 * @see ObjectModel::getWebserviceObjectList()
	 */
	public function getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit)
	{
		$sql_filter .= Shop::addSqlRestriction(Shop::SHARE_CUSTOMER, 'main');
		return parent::getWebserviceObjectList($sql_join, $sql_filter, $sql_sort, $sql_limit);
	}
}
