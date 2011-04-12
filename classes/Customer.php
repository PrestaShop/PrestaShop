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

class CustomerCore extends ObjectModel
{
	public 		$id;

	/** @var string Secure key */
	public		$secure_key;

	/** @var string protected note */
	public		$note;

	/** @var integer Gender ID */
	public		$id_gender = 9;

	/** @var integer Default group ID */
	public		$id_default_group;

	/** @var string Lastname */
	public 		$lastname;

	/** @var string Firstname */
	public 		$firstname;

	/** @var string Birthday (yyyy-mm-dd) */
	public 		$birthday = NULL;

	/** @var string e-mail */
	public 		$email;

	/** @var boolean Newsletter subscription */
	public 		$newsletter;

	/** @var string Newsletter ip registration */
	public		$ip_registration_newsletter;

	/** @var string Newsletter ip registration */
	public		$newsletter_date_add;

	/** @var boolean Opt-in subscription */
	public 		$optin;

	/** @var integer Password */
	public 		$passwd;

	/** @var datetime Password */
	public $last_passwd_gen;

	/** @var boolean Status */
	public 		$active = true;

	/** @var boolean Status */
	public 		$is_guest = 0;
	
	/** @var boolean True if carrier has been deleted (staying in database as deleted) */
	public 		$deleted = 0;

	/** @var string Object creation date */
	public 		$date_add;

	/** @var string Object last modification date */
	public 		$date_upd;

	public		$years;
	public		$days;
	public		$months;

	protected $tables = array ('customer');

 	protected 	$fieldsRequired = array('lastname', 'passwd', 'firstname', 'email');
 	protected 	$fieldsSize = array('lastname' => 32, 'passwd' => 32, 'firstname' => 32, 'email' => 128, 'note' => 65000);
 	protected 	$fieldsValidate = array('secure_key' => 'isMd5', 'lastname' => 'isName', 'firstname' => 'isName', 'email' => 'isEmail', 'passwd' => 'isPasswd',
		 'id_gender' => 'isUnsignedId', 'birthday' => 'isBirthDate', 'newsletter' => 'isBool', 'optin' => 'isBool', 'active' => 'isBool', 'note' => 'isCleanHtml', 'is_guest' => 'isBool');

	protected	$webserviceParameters = array(
		'objectMethods' => array('add' => 'addWs'),
		'fields' => array(
			'id_default_group' => array('xlink_resource' => 'groups'),
			'newsletter_date_add' => array(),
			'ip_registration_newsletter' => array(),
			'last_passwd_gen' => array('setter' => null),
			'secure_key' => array('setter' => null),
			'deleted' => array()
		),
	);

	protected 	$table = 'customer';
	protected 	$identifier = 'id_customer';

	protected static $_defaultGroupId = array();
	protected static $_customerHasAddress = array();

	public function getFields()
	{
		parent::validateFields();
		if (isset($this->id))
			$fields['id_customer'] = (int)($this->id);
		$fields['secure_key'] = pSQL($this->secure_key);
		$fields['note'] = pSQL($this->note, true);
		$fields['id_gender'] = (int)($this->id_gender);
		$fields['id_default_group'] = (int)($this->id_default_group);
		$fields['lastname'] = pSQL($this->lastname);
		$fields['firstname'] = pSQL($this->firstname);
		$fields['birthday'] = pSQL($this->birthday);
		$fields['email'] = pSQL($this->email);
		$fields['newsletter'] = (int)($this->newsletter);
		$fields['newsletter_date_add'] = pSQL($this->newsletter_date_add);
		$fields['ip_registration_newsletter'] = pSQL($this->ip_registration_newsletter);
		$fields['optin'] = (int)($this->optin);
		$fields['passwd'] = pSQL($this->passwd);
		$fields['last_passwd_gen'] = pSQL($this->last_passwd_gen);
		$fields['active'] = (int)($this->active);
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		$fields['is_guest'] = (int)($this->is_guest);
		$fields['deleted'] = (int)($this->deleted);
		return $fields;
	}

	public function add($autodate = true, $nullValues = true)
	{
		$this->birthday = (empty($this->years) ? $this->birthday : (int)($this->years).'-'.(int)($this->months).'-'.(int)($this->days));
		$this->secure_key = md5(uniqid(rand(), true));
		$this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_FRONT').'minutes'));
		if (empty($this->id_default_group))
			$this->id_default_group = 1;
		/* Can't create a guest customer, if this feature is disabled */
		if ($this->is_guest AND !Configuration::get('PS_GUEST_CHECKOUT_ENABLED'))
			return false;
	 	if (!parent::add($autodate, $nullValues))
			return false;

		$row = array('id_customer' => (int)($this->id), 'id_group' => (int)$this->id_default_group);
		return Db::getInstance()->AutoExecute(_DB_PREFIX_.'customer_group', $row, 'INSERT');
	}

	public function update($nullValues = false)
	{
		$this->birthday = (empty($this->years) ? $this->birthday : (int)$this->years.'-'.(int)$this->months.'-'.(int)$this->days);
		if ($this->newsletter AND !$this->newsletter_date_add)
			$this->newsletter_date_add = date('Y-m-d H:i:s');
	 	return parent::update(true);
	}

	public function delete()
	{
		$addresses = $this->getAddresses((int)(Configuration::get('PS_LANG_DEFAULT')));
		foreach ($addresses AS $address)
		{
			$obj = new Address((int)($address['id_address']));
			$obj->delete();
		}
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_customer` = '.(int)($this->id));
		Discount::deleteByIdCustomer((int)($this->id));
		return parent::delete();
	}

	/**
	  * Return customers list
	  *
	  * @return array Customers
	  */
	static public function getCustomers()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT `id_customer`, `email`, `firstname`, `lastname`
		FROM `'._DB_PREFIX_.'customer`
		ORDER BY `id_customer` ASC');
	}

	/**
	  * Return customer instance from its e-mail (optionnaly check password)
	  *
	  * @param string $email e-mail
	  * @param string $passwd Password is also checked if specified
	  * @return Customer instance
	  */
	public function getByEmail($email, $passwd = NULL)
	{
	 	if (!Validate::isEmail($email) OR ($passwd AND !Validate::isPasswd($passwd)))
	 		die (Tools::displayError());

		$result = Db::getInstance()->getRow('
		SELECT *
		FROM `'._DB_PREFIX_	.'customer`
		WHERE `active` = 1
		AND `email` = \''.pSQL($email).'\'
		'.(isset($passwd) ? 'AND `passwd` = \''.md5(pSQL(_COOKIE_KEY_.$passwd)).'\'' : '').'
		AND `deleted` = 0
		AND `is_guest` = 0');

		if (!$result)
			return false;
		$this->id = $result['id_customer'];
		foreach ($result AS $key => $value)
			if (key_exists($key, $this))
				$this->{$key} = $value;

		return $this;
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
		WHERE `id_customer` = \''.(int)($id_customer).'\'
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
	  * @param $ignoreGuest boolean, for exclure guest customer
	  * @return Customer ID if found, false otherwise
	  */
	static public function customerExists($email, $return_id = false, $ignoreGuest = true)
	{
	 	if (!Validate::isEmail($email))
	 		die (Tools::displayError());

		$result = Db::getInstance()->getRow('
		SELECT `id_customer`
		FROM `'._DB_PREFIX_.'customer`
		WHERE `email` = \''.pSQL($email).'\''
		.($ignoreGuest ? 'AND `is_guest` = 0' : ''));

		if ($return_id)
			return (int)($result['id_customer']);
		else
			return isset($result['id_customer']);
	}

	/**
	  * Check if, except current customer, someone else registered this e-email
	  *
	  * @return integer Number of customers who have also this e-mail
	  * @deprecated
	  */
	public function cantChangeemail()
	{
	 	Tools::displayAsDeprecated();
		if (!Validate::isEmail($this->email))
	 		die (Tools::displayError());
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(`id_customer`) AS total
		FROM `'._DB_PREFIX_.'customer`
		WHERE `email` = \''.pSQL($this->email).'\' AND `id_customer` != '.(int)($this->id));

		return $result['total'];
	}

	/**
	  * Check if an address is owned by a customer
	  *
	  * @param integer $id_customer Customer ID
	  * @param integer $id_address Address ID
	  * @return boolean result
	  */
	static public function customerHasAddress($id_customer, $id_address)
	{
		if (!array_key_exists($id_customer, self::$_customerHasAddress))
		{
			self::$_customerHasAddress[$id_customer] = (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `id_address`
			FROM `'._DB_PREFIX_.'address`
			WHERE `id_customer` = '.(int)($id_customer).'
			AND `id_address` = '.(int)($id_address).'
			AND `deleted` = 0');
		}
		return self::$_customerHasAddress[$id_customer];
	}
	
	static public function resetAddressCache($id_customer)
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
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT a.*, cl.`name` AS country, s.name AS state
		FROM `'._DB_PREFIX_.'address` a
		LEFT JOIN `'._DB_PREFIX_.'country` c ON (a.`id_country` = c.`id_country`)
		LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country`)
		LEFT JOIN `'._DB_PREFIX_.'state` s ON (s.`id_state` = a.`id_state`)
		WHERE `id_lang` = '.(int)($id_lang).' AND `id_customer` = '.(int)($this->id).' AND a.`deleted` = 0');
	}


	/**
	  * Returns customer last connections
	  *
	  * @param integer $nb Number of connections wanted
	  * @return array Connections
	  */
	public function getConnections($nb = 10)
	{
		Tools::displayAsDeprecated();

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT `ip_address`, `date_add`
		FROM `'._DB_PREFIX_.'connections`
		WHERE `id_guest` IN (SELECT `id_guest` FROM `'._DB_PREFIX_.'guest` WHERE `id_customer` = '.(int)($this->id).')
		ORDER BY `date_add` DESC
		LIMIT 0,'.(int)($nb));
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
		SELECT COUNT(a.`id_address`)
		FROM `'._DB_PREFIX_.'address` a
		WHERE a.`id_customer` = '.(int)($id_customer).'
		AND a.`deleted` = 0');
	}

	/**
	  * Check if customer password is the right one
	  *
	  * @param string $passwd Password
	  * @return boolean result
	  */
	static public function checkPassword($id_customer, $passwd)
	{
	 	if (!Validate::isUnsignedId($id_customer) OR !Validate::isMd5($passwd))
	 		die (Tools::displayError());

		return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
		SELECT `id_customer`
		FROM `'._DB_PREFIX_.'customer`
		WHERE `id_customer` = '.(int)($id_customer).'
		AND `passwd` = \''.pSQL($passwd).'\'');
	}

	/**
	  * Return customers who have subscribed to the newsletter
	  *
	  * @return array Customers
	  * @deprecated
	  */
	public static function getNewsletteremails()
	{
		Tools::displayAsDeprecated();
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT `email`, `firstname`, `lastname`, `newsletter`, `ip_registration_newsletter`, `newsletter_date_add`
		FROM `'._DB_PREFIX_.'customer`
		WHERE `newsletter` = 1
		AND `active` = 1');
	}

	/**
	  * Return the number of customers who registered today
	  *
	  * @return integer number of customers who registered today
	  * @deprecated
	  */
	public static function getTodaysRegistration()
	{
		Tools::displayAsDeprecated();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT COUNT(`id_customer`) as nb
		FROM `'._DB_PREFIX_.'customer`
		WHERE DAYOFYEAR(`date_add`) = DAYOFYEAR(NOW())
		AND YEAR(`date_add`) = YEAR(NOW())');
		if (!$result['nb'])
			return '0';
		return $result['nb'];
	}

	/**
	  * Light back office search for customers
	  *
	  * @param string $query Searched string
	  * @return array Corresponding customers
	  */
	public static function searchByName($query)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.*
		FROM `'._DB_PREFIX_.'customer` c
		WHERE c.`email` LIKE \'%'.pSQL($query).'%\'
		OR c.`id_customer` LIKE \'%'.pSQL($query).'%\'
		OR c.`lastname` LIKE \'%'.pSQL($query).'%\'
		OR c.`firstname` LIKE \'%'.pSQL($query).'%\'');
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
		WHERE o.`id_customer` = '.(int)($this->id).'
		AND o.valid = 1');

		$result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT MAX(c.`date_add`) AS last_visit
		FROM `'._DB_PREFIX_.'guest` g
		LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest
		WHERE g.`id_customer` = '.(int)($this->id));

		$result3 = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT (YEAR(CURRENT_DATE)-YEAR(c.`birthday`)) - (RIGHT(CURRENT_DATE, 5)<RIGHT(c.`birthday`, 5)) AS age
		FROM `'._DB_PREFIX_.'customer` c
		WHERE c.`id_customer` = '.(int)($this->id));

		$result['last_visit'] = $result2['last_visit'];
		$result['age'] = ($result3['age'] != date('Y') ? $result3['age'] : '--');
		return $result;
	}

	public function getLastConnections()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT c.date_add, COUNT(cp.id_page) AS pages, TIMEDIFF(MAX(cp.time_end), c.date_add) as time, http_referer,INET_NTOA(ip_address) as ipaddress
        FROM `'._DB_PREFIX_.'guest` g
        LEFT JOIN `'._DB_PREFIX_.'connections` c ON c.id_guest = g.id_guest
        LEFT JOIN `'._DB_PREFIX_.'connections_page` cp ON c.id_connections = cp.id_connections
        WHERE g.`id_customer` = '.(int)($this->id).'
        GROUP BY c.`id_connections`
        ORDER BY c.date_add DESC
        LIMIT 10');
    }

	/**
	  * Return last cart ID for this customer
	  *
	  * @return integer Cart ID
	  * @deprecated
	  */
	public function getLastCart()
	{
		Tools::displayAsDeprecated();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT MAX(c.`id_cart`) AS id_cart
		FROM `'._DB_PREFIX_.'cart` c
		WHERE c.`id_customer` = '.(int)($this->id));
		if (isset($result['id_cart']))
			return $result['id_cart'];
		return false;
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
		return self::customerIdExistsStatic((int)($id_customer));
	}

	static public function customerIdExistsStatic($id_customer)
	{
		$row = Db::getInstance()->getRow('
		SELECT `id_customer`
		FROM '._DB_PREFIX_.'customer c
		WHERE c.`id_customer` = '.(int)($id_customer));

		return isset($row['id_customer']);
	}

	public function cleanGroups()
	{
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'customer_group` WHERE `id_customer` = '.(int)($this->id));
	}

	public function addGroups($groups)
	{
		foreach ($groups as $group)
		{
			$row = array('id_customer' => (int)($this->id), 'id_group' => (int)($group));
			Db::getInstance()->AutoExecute(_DB_PREFIX_.'customer_group', $row, 'INSERT');
		}
	}

	public static function getGroupsStatic($id_customer)
	{
		$groups = array();
		$result = Db::getInstance()->ExecuteS('
		SELECT cg.`id_group`
		FROM '._DB_PREFIX_.'customer_group cg
		WHERE cg.`id_customer` = '.(int)($id_customer));
		foreach ($result AS $group)
			$groups[] = (int)($group['id_group']);
		return $groups;
	}

	public function getGroups()
	{
		return self::getGroupsStatic((int)($this->id));
	}

	public function isUsed()
	{
		return false;
	}

	/**
	 * @param int $id_group
	 * @return int
	 * @deprecated
	 */
	public function isMemberOfGroup($id_group)
	{
		Tools::displayAsDeprecated();
		$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT count(cg.`id_group`) as nb
		FROM '._DB_PREFIX_.'customer_group cg
		WHERE cg.`id_customer` = '.(int)($this->id).'
		AND cg.`id_group` = '.(int)($id_group));

		return $result['nb'];
	}

	public function getBoughtProducts()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `'._DB_PREFIX_.'orders` o
		LEFT JOIN `'._DB_PREFIX_.'order_detail` od ON o.id_order = od.id_order
		WHERE o.valid = 1 AND o.`id_customer` = '.(int)($this->id));
	}

	/**
	 * @deprecated
	 * @return bool
	 */
	public function getNeedDNI()
	{
		Tools::displayAsDeprecated();
		
		return false;
	}

	static public function getDefaultGroupId($id_customer)
	{
		if (!isset(self::$_defaultGroupId[(int)($id_customer)]))
			self::$_defaultGroupId[(int)($id_customer)] = Db::getInstance()->getValue('SELECT `id_default_group` FROM `'._DB_PREFIX_.'customer` WHERE `id_customer` = '.(int)($id_customer));
		return self::$_defaultGroupId[(int)($id_customer)];
	}

	static public function getCurrentCountry($id_customer)
	{
		global $cart;

		if (!$cart OR !$cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})
			$id_address = (int)(Db::getInstance()->getValue('SELECT `id_address` FROM `'._DB_PREFIX_.'address` WHERE `id_customer` = '.(int)($id_customer).' AND `deleted` = 0 ORDER BY `id`'));
		else
			$id_address = $cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
		$ids = Address::getCountryAndState($id_address);
		return (int)($ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT'));
	}
	
	public function toggleStatus()
	{
		parent::toggleStatus();

		/* Change status to active/inactive */
		return Db::getInstance()->Execute('
		UPDATE `'.pSQL(_DB_PREFIX_.$this->table).'`
		SET `date_upd` = NOW()
		WHERE `'.pSQL($this->identifier).'` = '.(int)($this->id));
	}


	public function isGuest()
	{
		return (bool)$this->is_guest;
	}
	
	public function transformToCustomer($id_lang, $password = NULL)
	{
		if (!$this->isGuest())
			return false;
		if (empty($password))
			$password = Tools::passwdGen();
		if (!Validate::isPasswd($password))
			return false;
		
		$this->is_guest = 0;
		$this->passwd = Tools::encrypt($password);
		if ($this->update())
		{
			$vars = array(
				'{firstname}' => $this->firstname,
				'{lastname}' => $this->lastname,
			    '{email}' => $this->email,
			    '{passwd}' => $password
			);
			
			Mail::Send((int)$id_lang, 'guest_to_customer', Mail::l('Your guest account has been transformed to customer account'), $vars, $this->email, $this->firstname.' '.$this->lastname);
			return true;
		}
		return false;
	}
	public function addWs($autodate = true, $nullValues = false)
	{
		$this->passwd = Tools::encrypt($this->passwd);
		return $this->add($autodate, $nullValues);
	}
}

