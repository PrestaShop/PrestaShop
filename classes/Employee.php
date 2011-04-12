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

class EmployeeCore extends ObjectModel
{
	public 		$id;
	
	/** @var string Determine employee profile */
	public 		$id_profile;
	
	/** @var string employee language */
	public 		$id_lang;
	
	/** @var string Lastname */
	public 		$lastname;
	
	/** @var string Firstname */
	public 		$firstname;
	
	/** @var string e-mail */
	public 		$email;
	
	/** @var string Password */
	public 		$passwd;
	
	/** @var datetime Password */
	public 		$last_passwd_gen;
	
	public $stats_date_from;
	public $stats_date_to;
	
	/** @var string Display back office background in the specified color */
	public		$bo_color;
	
	/** @var string employee's chosen theme */
	public		$bo_theme;
	
	/** @var string / enum hover or click mode */
	public		$bo_uimode;
	
	/** @var boolean Status */
	public 		$active = 1;
	
 	protected 	$fieldsRequired = array('lastname', 'firstname', 'email', 'passwd', 'id_profile', 'id_lang');
 	protected 	$fieldsSize = array('lastname' => 32, 'firstname' => 32, 'email' => 128, 'passwd' => 32, 'bo_color' => 32, 'bo_theme' => 32);
 	protected 	$fieldsValidate = array('lastname' => 'isName', 'firstname' => 'isName', 'email' => 'isEmail', 'id_lang' => 'isUnsignedInt', 
		'passwd' => 'isPasswdAdmin', 'active' => 'isBool', 'id_profile' => 'isInt', 'bo_color' => 'isColor', 'bo_theme' => 'isGenericName', 'bo_uimode' => 'isGenericName');
	
	protected 	$table = 'employee';
	protected 	$identifier = 'id_employee';

	protected	$webserviceParameters = array(
		'objectMethods' => array('add' => 'addWs'),
		'fields' => array(
			'id_lang' => array('xlink_resource' => 'languages'),
			'last_passwd_gen' => array('setter' => null),
			'stats_date_from' => array('setter' => null),
			'stats_date_to' => array('setter' => null),
		),
	);
	
	
	public	function getFields()
	{
	 	parent::validateFields();
		
		$fields['id_profile'] = (int)$this->id_profile;
		$fields['id_lang'] = (int)$this->id_lang;
		$fields['lastname'] = pSQL($this->lastname);
		$fields['firstname'] = pSQL(Tools::ucfirst($this->firstname));
		$fields['email'] = pSQL($this->email);
		$fields['passwd'] = pSQL($this->passwd);
		$fields['last_passwd_gen'] = pSQL($this->last_passwd_gen);
		$fields['stats_date_from'] = pSQL($this->stats_date_from);
		$fields['stats_date_to'] = pSQL($this->stats_date_to);
		$fields['bo_color'] = pSQL($this->bo_color);
		$fields['bo_theme'] = pSQL($this->bo_theme);
		$fields['bo_uimode'] = pSQL($this->bo_uimode);
		$fields['active'] = (int)$this->active;
		
		return $fields;
	}
	
	/**
	 * Return all employee id and email
	 *
	 * @return array Employees
	 * @deprecated
	 */
	static public function getEmployees()
	{
		Tools::displayAsDeprecated();
		return Db::getInstance()->ExecuteS('
		SELECT `id_employee`, CONCAT(`firstname`, \' \', `lastname`) AS "name"
		FROM `'._DB_PREFIX_.'employee`
		WHERE `active` = 1
		ORDER BY `email`');
	}
	
	public function add($autodate = true, $nullValues = true)
	{
		$this->last_passwd_gen = date('Y-m-d H:i:s', strtotime('-'.Configuration::get('PS_PASSWD_TIME_BACK').'minutes'));
	 	return parent::add($autodate, $nullValues);
	}
		
	/**
	  * Return employee instance from its e-mail (optionnaly check password)
	  * 
	  * @param string $email e-mail
	  * @param string $passwd Password is also checked if specified
	  * @return Employee instance
	  */
	public function getByEmail($email, $passwd = NULL)
	{
	 	if (!Validate::isEmail($email) OR ($passwd != NULL AND !Validate::isPasswd($passwd)))
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
		foreach ($result AS $key => $value)
			if (key_exists($key, $this))
				$this->{$key} = $value;
		return $this;
	}
	
	static public function employeeExists($email)
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
	static public function checkPassword($id_employee, $passwd)
	{
	 	if (!Validate::isUnsignedId($id_employee) OR !Validate::isPasswd($passwd, 8))
	 		die (Tools::displayError());
			
		return Db::getInstance()->getValue('
		SELECT `id_employee`
		FROM `'._DB_PREFIX_.'employee`
		WHERE `id_employee` = '.(int)$id_employee.'
		AND `passwd` = \''.pSQL($passwd).'\'
		AND active = 1');
	}
	
	static public function countProfile($id_profile, $activeOnly = false)
	{
		return Db::getInstance()->getValue('
		SELECT COUNT(*)
		FROM `'._DB_PREFIX_.'employee`  				
		WHERE `id_profile` = '.(int)$id_profile.'
		'.($activeOnly ? ' AND `active` = 1' : ''));
	}
	
	public function isLastAdmin()
	{
		return ($this->id_profile == _PS_ADMIN_PROFILE_		
			AND Employee::countProfile($this->id_profile, true) == 1
			AND $this->active
		);
	}
	
	
	public function addWs($autodate = true, $nullValues = false)
	{
		$this->passwd = Tools::encrypt($this->passwd);
		return $this->add($autodate, $nullValues);
	}

}