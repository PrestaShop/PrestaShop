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
*  @version  Release: $Revision: 6844 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class EmployeeCore extends ObjectModel
{
	public $id;

	/** @var string Determine employee profile */
	public $id_profile;

	/** @var string employee language */
	public $id_lang;

	/** @var string Lastname */
	public $lastname;

	/** @var string Firstname */
	public $firstname;

	/** @var string e-mail */
	public $email;

	/** @var string Password */
	public $passwd;

	/** @var datetime Password */
	public $last_passwd_gen;

	public $stats_date_from;
	public $stats_date_to;

	/** @var string Display back office background in the specified color */
	public $bo_color;

	public $default_tab;

	/** @var string employee's chosen theme */
	public $bo_theme;

	/** @var integer employee desired screen width */
	public $bo_width;

	/** @var bool, true */
	public $bo_show_screencast;

	/** @var boolean Status */
	public $active = 1;

	public $remote_addr;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'employee',
		'primary' => 'id_employee',
		'fields' => array(
			'lastname' => 			array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
			'firstname' => 			array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
			'email' => 				array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
			'id_lang' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
			'passwd' => 			array('type' => self::TYPE_STRING, 'validate' => 'isPasswdAdmin', 'required' => true, 'size' => 32),
			'last_passwd_gen' => 	array('type' => self::TYPE_STRING),
			'active' => 			array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'id_profile' => 		array('type' => self::TYPE_INT, 'validate' => 'isInt', 'required' => true),
			'bo_color' => 			array('type' => self::TYPE_STRING, 'validate' => 'isColor', 'size' => 32),
			'default_tab' => 		array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'bo_theme' => 			array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 32),
			'bo_width' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'bo_show_screencast' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'stats_date_from' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'stats_date_to' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	protected $webserviceParameters = array(
		'fields' => array(
			'id_lang' => array('xlink_resource' => 'languages'),
			'last_passwd_gen' => array('setter' => null),
			'stats_date_from' => array('setter' => null),
			'stats_date_to' => array('setter' => null),
			'passwd' => array('setter' => 'setWsPasswd'),
		),
	);

	protected $associated_shops = array();

	public function __construct($id = null, $id_lang = null, $id_shop = null)
	{
		parent::__construct($id, $id_lang, $id_shop);

		if ($this->id)
			$this->associated_shops = $this->getAssociatedShops();
	}

	/**
	 * @see ObjectModel::getFields()
	 * @return array
	 */
	public function getFields()
	{
		if (empty($this->stats_date_from))
			$this->stats_date_from = date('Y-m-d 00:00:00');

		if (empty($this->stats_date_to))
			$this->stats_date_to = date('Y-m-d 23:59:59');

		return parent::getFields();
	}

	public function add($autodate = true, $null_values = true)
	{
		$this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_BACK').'minutes'));
	 	return parent::add($autodate, $null_values);
	}

	/**
	 * Return list of employees
	 */
	public static function getEmployees()
	{
		return Db::getInstance()->executeS('
			SELECT `id_employee`, `firstname`, `lastname`
			FROM `'._DB_PREFIX_.'employee`
			WHERE `active` = 1
			ORDER BY `lastname` ASC
		');
	}

	/**
	  * Return employee instance from its e-mail (optionnaly check password)
	  *
	  * @param string $email e-mail
	  * @param string $passwd Password is also checked if specified
	  * @return Employee instance
	  */
	public function getByEmail($email, $passwd = null)
	{
	 	if (!Validate::isEmail($email) || ($passwd != null && !Validate::isPasswd($passwd)))
	 		die(Tools::displayError());

		$result = Db::getInstance()->getRow('
		SELECT *
		FROM `'._DB_PREFIX_.'employee`
		WHERE `active` = 1
		AND `email` = \''.pSQL($email).'\'
		'.($passwd ? 'AND `passwd` = \''.Tools::encrypt($passwd).'\'' : ''));
		if (!$result)
			return false;
		$this->id = $result['id_employee'];
		$this->id_profile = $result['id_profile'];
		foreach ($result as $key => $value)
			if (property_exists($this, $key))
				$this->{$key} = $value;
		return $this;
	}

	public static function employeeExists($email)
	{
	 	if (!Validate::isEmail($email))
	 		die (Tools::displayError());

		return (bool)Db::getInstance()->getValue('
		SELECT `id_employee`
		FROM `'._DB_PREFIX_.'employee`
		WHERE `email` = \''.pSQL($email).'\'');
	}

	/**
	  * Check if employee password is the right one
	  *
	  * @param string $passwd Password
	  * @return boolean result
	  */
	public static function checkPassword($id_employee, $passwd)
	{
	 	if (!Validate::isUnsignedId($id_employee) || !Validate::isPasswd($passwd, 8))
	 		die (Tools::displayError());

		return Db::getInstance()->getValue('
		SELECT `id_employee`
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_employee` = '.(int)$id_employee.'
		AND `passwd` = \''.pSQL($passwd).'\'
		AND active = 1');
	}

	public static function countProfile($id_profile, $active_only = false)
	{
		return Db::getInstance()->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_profile` = '.(int)$id_profile.'
		'.($active_only ? ' AND `active` = 1' : ''));
	}

	public function isLastAdmin()
	{
		return ($this->isSuperAdmin()
			&& Employee::countProfile($this->id_profile, true) == 1
			&& $this->active
		);
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
	  * Check employee informations saved into cookie and return employee validity
	  *
	  * @return boolean employee validity
	  */
	public function isLoggedBack()
	{
		/* Employee is valid only if it can be load and if cookie password is the same as database one */
	 	return ($this->id
			&& Validate::isUnsignedId($this->id)
			&& Employee::checkPassword($this->id, $this->passwd)
			&& (!isset($this->remote_addr) || $this->remote_addr == ip2long(Tools::getRemoteAddr()) || !Configuration::get('PS_COOKIE_CHECKIP'))
		);
	}

	/**
	  * Logout
	  */
	public function logout()
	{
		if (isset(Context::getContext()->cookie))
			Context::getContext()->cookie->logout();
		$this->id = null;
	}

	/**
	 * Check if the employee is associated to a specific shop
	 *
	 * @since 1.5.0
	 * @param int $id_shop
	 * @return bool
	 */
	public function hasAuthOnShop($id_shop)
	{
		return $this->isSuperAdmin() || in_array($id_shop, $this->associated_shops);
	}

	/**
	 * Check if the employee is associated to a specific shop group
	 *
	 * @since 1.5.0
	 * @param int $id_shop_shop
	 * @return bool
	 */
	public function hasAuthOnShopGroup($id_shop_group)
	{
		if ($this->isSuperAdmin())
			return true;

		foreach ($this->associated_shops as $id_shop)
			if ($id_shop_group == Shop::getGroupFromShop($id_shop, true))
				return true;
		return false;
	}

	/**
	 * Get default id_shop with auth for current employee
	 *
	 * @since 1.5.0
	 * @return int
	 */
	public function getDefaultShopID()
	{
		if ($this->isSuperAdmin() || in_array(Configuration::get('PS_SHOP_DEFAULT'), $this->associated_shops))
			return Configuration::get('PS_SHOP_DEFAULT');
		return $this->associated_shops[0];
	}

	public static function getEmployeesByProfile($id_profile, $active_only = false)
	{
		return Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_profile` = '.(int)$id_profile.'
		'.($active_only ? ' AND `active` = 1' : ''));
	}

	/**
	 * Check if current employee is super administrator
	 *
	 * @return bool
	 */
	public function isSuperAdmin()
	{
		return $this->id_profile == _PS_ADMIN_PROFILE_;
	}
}
