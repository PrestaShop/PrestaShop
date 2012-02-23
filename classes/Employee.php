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
			'bo_theme' => 			array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 32),
			'bo_width' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
			'bo_show_screencast' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'stats_date_from' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'stats_date_to' => 		array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	protected	$webserviceParameters = array(
		'fields' => array(
			'id_lang' => array('xlink_resource' => 'languages'),
			'last_passwd_gen' => array('setter' => null),
			'stats_date_from' => array('setter' => null),
			'stats_date_to' => array('setter' => null),
			'passwd' => array('setter' => 'setWsPasswd'),
		),
	);

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
			if (key_exists($key, $this))
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
		return ($this->id_profile == _PS_ADMIN_PROFILE_
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

	public static function getEmployeeShopAccess($id_employee)
	{
		$context = Context::getContext();

		switch ($type = $context->shop->getContextType())
		{
			case 1:
				if ($context->shop->checkIfShopExist($context->shop->id))
				{
					if (!in_array($context->shop->id, Employee::getEmployeeShopById($id_employee)))
						return false;
				}
				else
					return false;
			break;

			case 2:
				if ($context->shop->checkIfGroupShopExist($context->shop->getGroupID()))
				{
					$shops = $context->shop->getIdShopsByIdGroupShop($context->shop->getGroupID());
					foreach ($shops as $shop)
						if (!in_array($shop, Employee::getEmployeeShopById($id_employee)))
							return false;
				}
				else
					return false;
			break;

			case 3:
				if ($context->employee->id_profile == _PS_ADMIN_PROFILE_ ||
					$context->shop->getTotalShopsWhoExists() == Employee::getTotalEmployeeShopById($id_employee))
					return true;
				else
					return false;
			break;
		}
		return true;
	}

	public static function getTotalEmployeeShopById($id)
	{
		return (int)Db::getInstance()->getValue(sprintf('SELECT COUNT(*) FROM`'._DB_PREFIX_.'employee_shop` WHERE `id_employee` = %d', (int)$id));
	}

	public static function getEmployeeShopById($id)
	{
		$result = Db::getInstance()->executeS(sprintf('SELECT * FROM`'._DB_PREFIX_.'employee_shop` WHERE `id_employee` = %d', (int)$id));
		$data = array();
		foreach ($result as $group_data)
			$data[] = (int)$group_data['id_shop'];
		return $data;
	}

	public static function getEmployeesByProfile($id_profile, $active_only = false)
	{
		return Db::getInstance()->executeS('
		SELECT *
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_profile` = '.(int)$id_profile.'
		'.($active_only ? ' AND `active` = 1' : ''));
	}
}
