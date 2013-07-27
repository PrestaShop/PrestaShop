<?php
/*
* 2007-2013 PrestaShop
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
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class ReferralProgramModule extends ObjectModel
{
	public $id_sponsor;
	public $email;
	public $lastname;
	public $firstname;
	public $id_customer;
	public $id_cart_rule;
	public $id_cart_rule_sponsor;
	public $date_add;
	public $date_upd;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
		'table' => 'referralprogram',
		'primary' => 'id_referralprogram',
		'fields' => array(
			'id_sponsor' =>			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'email' =>				array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 255),
			'lastname' =>			array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 128),
			'firstname' =>			array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 128),
			'id_customer' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_cart_rule' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_cart_rule_sponsor' =>array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'date_add' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		),
	);

	public static function getDiscountPrefix()
	{
		return 'SP';
	}

	public function registerDiscountForSponsor($id_currency)
	{
		if ((int)$this->id_cart_rule_sponsor > 0)
			return false;
		return $this->registerDiscount((int)$this->id_sponsor, 'sponsor', (int)$id_currency);
	}

	public function registerDiscountForSponsored($id_currency)
	{
		if (!(int)$this->id_customer OR (int)$this->id_cart_rule > 0)
			return false;
		return $this->registerDiscount((int)$this->id_customer, 'sponsored', (int)$id_currency);
	}

	public function registerDiscount($id_customer, $register = false, $id_currency = 0)
	{
		$configurations = Configuration::getMultiple(array('REFERRAL_DISCOUNT_TYPE', 'REFERRAL_PERCENTAGE', 'REFERRAL_DISCOUNT_VALUE_'.(int)$id_currency));

		$cartRule = new CartRule();		
		if ($configurations['REFERRAL_DISCOUNT_TYPE'] == Discount::PERCENT)
			$cartRule->reduction_percent = (float)$configurations['REFERRAL_PERCENTAGE'];
		elseif ($configurations['REFERRAL_DISCOUNT_TYPE'] == Discount::AMOUNT AND isset($configurations['REFERRAL_DISCOUNT_VALUE_'.(int)$id_currency]))
			$cartRule->reduction_amount = (float)$configurations['REFERRAL_DISCOUNT_VALUE_'.(int)$id_currency];
		
		$cartRule->quantity = 1;
		$cartRule->quantity_per_user = 1;
		$cartRule->date_from = date('Y-m-d H:i:s', time());
		$cartRule->date_to = date('Y-m-d H:i:s', time() + 31536000); // + 1 year
		$cartRule->code = $this->getDiscountPrefix().Tools::passwdGen(6);
		$cartRule->name = Configuration::getInt('REFERRAL_DISCOUNT_DESCRIPTION');
		$cartRule->id_customer = (int)$id_customer;
		$cartRule->reduction_currency = (int)$id_currency;

		if ($cartRule->add())
		{
			if ($register != false)
			{
				if ($register == 'sponsor')
					$this->id_cart_rule_sponsor = (int)$cartRule->id;
				elseif ($register == 'sponsored')
					$this->id_cart_rule = (int)$cartRule->id;
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
	public static function getSponsorFriend($id_customer, $restriction = false)
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

		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	}

	/**
	  * Return if a customer is sponsorised
	  *
	  * @return boolean
	  */
	public static function isSponsorised($id_customer, $getId=false)
	{
		$result = Db::getInstance()->getRow('
		SELECT s.`id_referralprogram`
		FROM `'._DB_PREFIX_.'referralprogram` s
		WHERE s.`id_customer` = '.(int)$id_customer);
		
		if (isset($result['id_referralprogram']) AND $getId === true)
			return (int)$result['id_referralprogram'];

		return isset($result['id_referralprogram']);
	}

	public static function isSponsorFriend($id_sponsor, $id_friend)
	{
		if (!(int)($id_sponsor) OR !(int)($id_friend))
			return false; 
	
		$result = Db::getInstance()->getRow('
		SELECT s.`id_referralprogram`
		FROM `'._DB_PREFIX_.'referralprogram` s
		WHERE s.`id_sponsor` = '.(int)($id_sponsor).' AND s.`id_referralprogram` = '.(int)($id_friend));

		return isset($result['id_referralprogram']);
	}
	
	/**
	  * Return if an email is already register
	  *
	  * @return boolean OR int idReferralProgram
	  */
	public static function isEmailExists($email, $getId = false, $checkCustomer = true)
	{
		if (empty($email) OR !Validate::isEmail($email))
			die (Tools::displayError('The email address is invalid.'));

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
