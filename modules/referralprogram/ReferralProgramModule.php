<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_CAN_LOAD_FILES_'))
	exit;

class ReferralProgramModule extends ObjectModel
{
	public $id_sponsor;
	public $email;
	public $lastname;
	public $firstname;
	public $id_customer;
	public $id_discount;
	public $id_discount_sponsor;
	public $date_add;
	public $date_upd;

	protected $fieldsRequired = array('id_sponsor', 'email', 'lastname', 'firstname');
	protected $fieldsSize = array('id_sponsor' => 8, 'email' => 255, 'lastname' => 128, 'firstname' => 128, 'id_customer' => 8, 'id_discount' => 8, 'id_discount_sponsor' => 8);
	protected $fieldsValidate = array( 'id_sponsor' => 'isUnsignedId', 'email' => 'isEmail', 'lastname' => 'isName', 'firstname' => 'isName', 'id_customer' => 'isUnsignedId', 'id_discount' => 'isUnsignedId', 'id_discount_sponsor' => 'isUnsignedId');
	protected $table = 'referralprogram';
	protected $identifier = 'id_referralprogram';

	public function getFields()
	{
		parent::validateFields();
		$fields['id_sponsor'] = (int)$this->id_sponsor;
		$fields['email'] = pSQL($this->email);
		$fields['lastname'] = pSQL($this->lastname);
		$fields['firstname'] = pSQL($this->firstname);
		$fields['id_customer'] = (int)$this->id_customer;
		$fields['id_discount'] = (int)$this->id_discount;
		$fields['id_discount_sponsor'] = (int)$this->id_discount_sponsor;
		$fields['date_add'] = pSQL($this->date_add);
		$fields['date_upd'] = pSQL($this->date_upd);
		return $fields;
	}

	static public function getDiscountPrefix()
	{
		return 'SP';
	}

	public function registerDiscountForSponsor($id_currency)
	{
		if ((int)$this->id_discount_sponsor > 0)
			return false;
		return $this->registerDiscount((int)$this->id_sponsor, 'sponsor', (int)$id_currency);
	}

	public function registerDiscountForSponsored($id_currency)
	{
		if (!(int)$this->id_customer OR (int)$this->id_discount > 0)
			return false;
		return $this->registerDiscount((int)$this->id_customer, 'sponsored', (int)$id_currency);
	}

	public function registerDiscount($id_customer, $register = false, $id_currency = 0)
	{
		$configurations = Configuration::getMultiple(array('REFERRAL_DISCOUNT_TYPE', 'REFERRAL_PERCENTAGE', 'REFERRAL_DISCOUNT_VALUE_'.(int)$id_currency));

		$discount = new Discount();
		$discount->id_discount_type = (int)$configurations['REFERRAL_DISCOUNT_TYPE'];
		
		/* % */
		if ($configurations['REFERRAL_DISCOUNT_TYPE'] == 1)
			$discount->value = (float)$configurations['REFERRAL_PERCENTAGE'];
		/* Fixed amount */
		elseif ($configurations['REFERRAL_DISCOUNT_TYPE'] == 2 AND isset($configurations['REFERRAL_DISCOUNT_VALUE_'.(int)($id_currency)]))
			$discount->value = (float)$configurations['REFERRAL_DISCOUNT_VALUE_'.(int)($id_currency)];
		/* Unknown or value undefined for this currency (configure your module correctly) */
		else
			$discount->value = 0;
		
		$discount->quantity = 1;
		$discount->quantity_per_user = 1;
		$discount->date_from = date('Y-m-d H:i:s', time());
		$discount->date_to = date('Y-m-d H:i:s', time() + 31536000); // + 1 year
		$discount->name = $this->getDiscountPrefix().Tools::passwdGen(6);
		$discount->description = Configuration::getInt('REFERRAL_DISCOUNT_DESCRIPTION');
		$discount->id_customer = (int)$id_customer;
		$discount->id_currency = (int)$id_currency;

		if ($discount->add())
		{
			if ($register != false)
			{
				if ($register == 'sponsor')
					$this->id_discount_sponsor = (int)$discount->id;
				elseif ($register == 'sponsored')
					$this->id_discount = (int)$discount->id;
				return $this->save();
			}
			return true;
		}
		return false;
	}

	/**
	  * Return sponsored friends
	  *
	  * @return array Sponsor
	  */
	static public function getSponsorFriend($id_customer, $restriction = false)
	{
		if (!(int)($id_customer))
			return array();

		$query = '
		SELECT s.*
		FROM `'._DB_PREFIX_.'referralprogram` s
		WHERE s.`id_sponsor` = '.(int)$id_customer;
		if ($restriction)
		{
			if ($restriction == 'pending')
				$query.= ' AND s.`id_customer` = 0';
			elseif ($restriction == 'subscribed')
				$query.= ' AND s.`id_customer` != 0';
		}

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
	}

	/**
	  * Return if a customer is sponsorised
	  *
	  * @return boolean
	  */
	static public function isSponsorised($id_customer, $getId=false)
	{
		$result = Db::getInstance()->getRow('
		SELECT s.`id_referralprogram`
		FROM `'._DB_PREFIX_.'referralprogram` s
		WHERE s.`id_customer` = '.(int)$id_customer);
		
		if (isset($result['id_referralprogram']) AND $getId === true)
			return (int)$result['id_referralprogram'];

		return isset($result['id_referralprogram']);
	}

	/**
	  * Return if an email is already register
	  *
	  * @return boolean OR int idReferralProgram
	  */
	static public function isEmailExists($email, $getId = false, $checkCustomer = true)
	{
		if (empty($email) OR !Validate::isEmail($email))
			die (Tools::displayError('Email invalid.'));
		if ($checkCustomer === true AND Customer::customerExists($email))
			return false;
		$result = Db::getInstance()->getRow('
		SELECT s.`id_referralprogram`
		FROM `'._DB_PREFIX_.'referralprogram` s
		WHERE s.`email` = \''.pSQL($email).'\'');
		if ($getId)
			return (int)$result['id_referralprogram'];
		return isset($result['id_referralprogram']);
	}
}
