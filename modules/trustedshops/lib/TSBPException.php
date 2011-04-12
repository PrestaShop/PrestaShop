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

class TSBPException extends Exception
{
	const ADMINISTRATION = 1;
	const FRONT_END = 2;
	/**
	 * Set the object which use the translation method for the specific module.
	 * @var AbsTrustedShops
	 */
	private static $translation_object;
	private static $translate_key;
	
	public function __construct($code, $type = TSBPException::ADMINISTRATION)
	{
		if(!(self::$translation_object instanceof AbsTrustedShops) OR self::$translation_object === NULL)
		{
			die('In '.__METHOD__.', you must defined an object for get messages translations. An herited object from AbsTrustedShops.');
		}
		if (TSBPException::$translate_key === NULL)
			TSBPException::$translate_key = basename(__FILE__, '.php');
		
		if($type === TSBPException::ADMINISTRATION)
			$message = $this->_getAdministrationMessage((int)$code);
		else
			$message = $this->_getFrontEndMessage($code);
		parent::__construct($message, $code);
	}
	private function _getAdministrationMessage($code)
	{
		// @todo : don't forget to change email in translation
		$mail = self::$translation_object->l('Please contact Trusted Shops at service@trustedshops.co.uk.', TSBPException::$translate_key);
		$errors = array(
			-10001 => self::$translation_object->l('Username or password invalid.', TSBPException::$translate_key),
			-10002 => self::$translation_object->l('You have reached your credit limit.', TSBPException::$translate_key),
			-10011 => self::$translation_object->l('No credit available from Trusted Shops.', TSBPException::$translate_key),
			-11111 => self::$translation_object->l('The data could not be saved.', TSBPException::$translate_key),
		);
		$return_message = '';
		if(array_key_exists($code, $errors))
			$return_message = $errors[$code].$mail;
		else
			$return_message = self::$translation_object->l('An error occured.').$mail;
		
		return $return_message;
	}
	private function _getFrontEndMessage($code)
	{
		$return_message = '';
		switch ($code)
		{
			case -10001 : case 'NO_VALID_SHOP' : $return_message .= self::$translation_object->l('Web service login or TS-ID invalid', TSBPException::$translate_key);break;
			case -10002 : case 'LIMIT_CANCELLED' : $return_message .= self::$translation_object->l('The shop\'s credit limit has been suspended by Trusted Shops', TSBPException::$translate_key); break;
			case -10003 : case 'DUPLICATE_ORDER_NUMBER' : $return_message .= self::$translation_object->l('Order number already used', TSBPException::$translate_key); break;
			case -10004 : case 'UNSUPPORTED_TS_PRODUCT' : $return_message .= self::$translation_object->l('Unsupported Buyer Protection product', TSBPException::$translate_key); break;
			case -10005 : case 'INACTIVE_PAYMENT_TYPE' : $return_message .= self::$translation_object->l('Inactive payment method', TSBPException::$translate_key); break;
			case -10006 : case 'UNSUPPORTED_PAYMENT_TYPE' : $return_message .= self::$translation_object->l('Unsupported payment method', TSBPException::$translate_key); break;
			case -10007 : case 'CURRENCY_MISMATCH' : $return_message .= self::$translation_object->l('The currency of the Buyer Protection product does not match with the currency of the shopping basket', TSBPException::$translate_key); break;
			case -10008 : case 'UNSUPPORTED_CURRENCY' : $return_message .= self::$translation_object->l('This currency is not supported in this shop', TSBPException::$translate_key); break;
			case -10009 : case 'UNSUPPORTED_EXCHANGE_RATE' : $return_message .= self::$translation_object->l('This exchange rate is not supported', TSBPException::$translate_key); break;
			case -10010 : case 'NOT_PERSISTENT_PAYMENT_TYPE' : $return_message .= self::$translation_object->l('This payment method is not supported', TSBPException::$translate_key); break;
			case -10011 : case 'NO_LIMIT' : $return_message .= self::$translation_object->l('No credit limit available for this certificate', TSBPException::$translate_key); break;
			case -10012 : case 'PAST_DELIVERY_DATE' : $return_message .= self::$translation_object->l('The delivery date is in the past', TSBPException::$translate_key); break;
			case -10013 : case 'TOO_OLD_ORDER' : $return_message .= self::$translation_object->l('The guarantee is for a purchase that was made over 3 days ago', TSBPException::$translate_key); break;
			case -10014 : case 'EMAIL_MALFORMED' : $return_message .= self::$translation_object->l('The email address contains an error', TSBPException::$translate_key); break;
			case -10015 : case 'ORDER_ID_EMPTY' : $return_message .= self::$translation_object->l('No order number was assigned', TSBPException::$translate_key); break;
			case -10016 : case 'CUSTOMER_ID_EMPTY' : $return_message .= self::$translation_object->l('No customer number was assigned', TSBPException::$translate_key); break;
			case -10017 : case 'LIMIT_OVERFLOW' : $return_message .= self::$translation_object->l('The credit limit for this certificate has been exceeded', TSBPException::$translate_key); break;
			case -10018 : case 'EMAIL_EMPTY' : $return_message .= self::$translation_object->l('No email address was assigned', TSBPException::$translate_key); break;
			case -10019 : case 'WRONG_TS_PRODUCT' : $return_message .= self::$translation_object->l('Non-applicable Buyer Protection product', TSBPException::$translate_key); break;
			case -11001 : case 'INVALID_SECURITY_TOKEN' : $return_message .= self::$translation_object->l('Invalid security token', TSBPException::$translate_key); break;
			case -11111 : case 'SYSTEM_EXCEPTION' : $return_message .= self::$translation_object->l('General system error, please contact Trusted Shops', TSBPException::$translate_key); break;
			default : $return_message .= self::$translation_object->l('An error occured.', TSBPException::$translate_key); break;
		}
		return $return_message;
	}
	public static function setTranslationObject(AbsTrustedShops $object)
	{
		self::$translation_object = $object;
	}
}