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
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
include(_PS_MODULE_DIR_.'/trustedshops/lib/TSBPException.php');

/**
 * @see the technical doc for entire description.
 * 		too long to set it here.
 * @author Prestashop - Nans Pellicari
 * @since prestashop 1.4
 * @version 0.1
 */
class TSBuyerProtection extends AbsTrustedShops
{
	const PREFIX_TABLE = 'TS_TAB1_';
	const ENV_TEST = 'test';
	const ENV_PROD = 'production';
	const DB_ITEMS = 'ts_buyerprotection_items';
	const DB_APPLI = 'ts_application_id';
	const WEBSERVICE_BO = 'administration';
	const WEBSERVICE_FO = 'front-end';

	/**
	 * List of registration link, need to add parameters
	 * @see TSBuyerProtection::_getRegistrationLink()
	 * @var array
	 */
	private $registration_link = array(
		'DE'	=> 'http://www.trustedshops.de/shopbetreiber/mitgliedschaft.html',
		'EN'	=> 'http://www.trustedshops.com/merchants/membership.html',
		'FR'	=> 'http://www.trustedshops.com/marchands/affiliation.html',
		'PL'	=> 'http://www.trustedshops.pl/handlowcy/cennik.html',
	);

	/**
	 * Link to obtain the certificate about the shop.
	 * Use by seal of approval.
	 * @see TSBuyerProtection::hookRightColumn()
	 * @var array
	 */
	private static $certificate_link = array(
		'DE'	=> 'http://www.trustedshops.de/profil/#shop_name#_#shop_id#.html',
		'EN'	=> 'http://www.trustedshops.com/profile/#shop_name#_#shop_id#.html',
		'FR'	=> 'http://www.trustedshops.fr',
		'PL'	=> 'http://www.trustedshops.de/profil/#shop_name#_#shop_id#.html',
	);

	/**
	 * Available language for used TrustedShops Buyer Protection
	 * @see TSBuyerProtection::__construct()
	 * @var array
	 */
	private $available_languages = array('EN'=>'', 'FR'=>'', 'DE'=>'', 'PL'=>'', );

	/**
	 * @todo : be sure : see TrustedShopsRating::__construct()
	 * @var array
	 */
	public $limited_countries = array('PL', 'GB', 'US', 'FR', 'DE');

	/**
	 * Differents urls to call for Trusted Shops API
	 * @var array
	 */
	private static $webservice_urls = array(
		'administration'	=> array(
			'test'				=> 'https://qa.trustedshops.de/ts/services/TsProtection?wsdl',
			'production'		=> 'https://www.trustedshops.de/ts/services/TsProtection?wsdl',
		),
		'front-end'			=> array(
			'test'				=> 'https://protection-qa.trustedshops.com/ts/protectionservices/ApplicationRequestService?wsdl',
			'production'		=> 'https://protection.trustedshops.com/ts/protectionservices/ApplicationRequestService?wsdl',
		),
	);

	// Configuration vars
	private static $SHOPSW;
	private static $ET_CID;
	private static $ET_LID;

	/**
	 * Its must look like :
	 * array(
	 * 		'lang_iso(ex: FR)' => array('stateEnum'=>'', 'typeEnum'=>'', 'url'=>'', 'tsID'=>'', 'user'=>'', 'password'=>''),
	 * 		...
	 * )
	 * @var array
	 */
	private static $CERTIFICATE;
	private static $DEFAULT_LANG;
	private static $CAT_ID;
	private static $ENV_API;

	/**
	 * save shop url
	 * @var string
	 */
	private $site_url;

	/**
	 * Payment type used by Trusted Shops.
	 * @var array
	 */
	private static $payments_type;

	public function __construct()
	{
		// need to set this in constructor to allow translation
		TSBuyerProtection::$payments_type = array(
			'DIRECT_DEBIT'		=> $this->l('Direct debit'),
			'CREDIT_CARD'		=> $this->l('Credit Card'),
			'INVOICE'			=> $this->l('Invoice'),
			'CASH_ON_DELIVERY'	=> $this->l('Cash on delivery'),
			'PREPAYMENT'		=> $this->l('Prepayment'),
			'CHEQUE'			=> $this->l('Cheque'),
			'PAYBOX'			=> $this->l('Paybox'),
			'PAYPAL'			=> $this->l('PayPal'),
			'CASH_ON_PICKUP'	=> $this->l('Cash on pickup'),
			'FINANCING'			=> $this->l('Financing'),
			'LEASING'			=> $this->l('Leasing'),
			'T_PAY'				=> $this->l('T-Pay'),
			'CLICKANDBUY'		=> $this->l('Click&Buy'),
			'GIROPAY'			=> $this->l('Giropay'),
			'GOOGLE_CHECKOUT'	=> $this->l('Google Checkout'),
			'SHOP_CARD'			=> $this->l('Online shop payment card'),
			'DIRECT_E_BANKING'	=> $this->l('DIRECTebanking.com'),
			'MONEYBOOKERS'		=> $this->l('moneybookers.com'),
			'OTHER'				=> $this->l('Other method of payment'),
		);
		$this->tab_name = $this->l('Seal of Approval and Buyer Protection');
		$this->site_url = Tools::htmlentitiesutf8('http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__);
		TSBPException::setTranslationObject($this);
		if (!method_exists('Tools', 'jsonDecode') || !method_exists('Tools', 'jsonEncode'))
		{
			$this->warnings[] = $this->l('Json functions must be implemented in your php version');
		}
		else
		{
			foreach ($this->available_languages as $iso => $lang)
			{
				if($lang === '')
					$this->available_languages[$iso] = Language::getLanguage(Language::getIdByIso($iso));
				TSBuyerProtection::$CERTIFICATE[strtoupper($iso)] = (array)Tools::jsonDecode(
					Tools::htmlentitiesDecodeUTF8(Configuration::get(TSBuyerProtection::PREFIX_TABLE.'CERTIFICATE_'.strtoupper($iso))));
			}
			if(TSBuyerProtection::$SHOPSW === NULL)
			{
				TSBuyerProtection::$SHOPSW = Configuration::get(TSBuyerProtection::PREFIX_TABLE.'SHOPSW');
				TSBuyerProtection::$ET_CID = Configuration::get(TSBuyerProtection::PREFIX_TABLE.'ET_CID');
				TSBuyerProtection::$ET_LID = Configuration::get(TSBuyerProtection::PREFIX_TABLE.'ET_LID');
				TSBuyerProtection::$DEFAULT_LANG = (int)Configuration::get('PS_LANG_DEFAULT');
				TSBuyerProtection::$CAT_ID = (int)Configuration::get(TSBuyerProtection::PREFIX_TABLE.'CAT_ID');
				TSBuyerProtection::$ENV_API = Configuration::get(TSBuyerProtection::PREFIX_TABLE.'ENV_API');
			}
		}
	}

	public function install()
	{
		if (!method_exists('Tools', 'jsonDecode') || !method_exists('Tools', 'jsonEncode'))
			return false;

		foreach ($this->available_languages as $iso=>$lang)
			Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'CERTIFICATE_'.strtoupper($iso),
			Tools::htmlentitiesUTF8(Tools::jsonEncode(array('stateEnum'=>'', 'typeEnum'=>'', 'url'=>'', 'tsID'=>'', 'user'=>'', 'password'=>''))));

		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'SHOPSW', '');
		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'ET_CID', '');
		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'ET_LID', '');
		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'ENV_API', TSBuyerProtection::ENV_PROD);
		$req = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.TSBuyerProtection::DB_ITEMS.'` (
			`id_item` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`id_product` INT NOT NULL,
			`ts_id` VARCHAR( 33 ) NOT NULL,
			`id` INT NOT NULL,
			`currency` VARCHAR( 3 ) NOT NULL ,
			`gross_fee` DECIMAL( 20, 6 ) NOT NULL ,
			`net_fee` DECIMAL( 20, 6 ) NOT NULL ,
			`protected_amount_decimal` INT NOT NULL ,
			`protection_duration_int` INT NOT NULL ,
			`ts_product_id` TEXT NOT NULL ,
			`creation_date` VARCHAR( 25 ) NOT NULL
			);
		';
		Db::getInstance()->Execute($req);

		$req = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.TSBuyerProtection::DB_APPLI.'` (
			`id_application` INT NOT NULL PRIMARY KEY,
			`ts_id` VARCHAR( 33 ) NOT NULL,
			`id_order` INT NOT NULL,
			`statut_number` INT NOT NULL DEFAULT \'0\',
			`creation_date` DATETIME NOT NULL,
			`last_update` DATETIME NOT NULL
			);
		';
		Db::getInstance()->Execute($req);

		//add hidden category
		$category = new Category();
		$languages = Language::getLanguages(true);
		foreach ($this->available_languages as $iso=>$lang)
		{
			$category->name[Language::getIdByIso(strtolower($iso))] = 'Trustedshops';
			$category->link_rewrite[Language::getIdByIso(strtolower($iso))] = 'trustedshops';
		}

		// If the default lang is different than available languages :
		// (Bug occurred otherwise)
		if (!array_key_exists(Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT')), $this->available_languages))
		{
			$category->name[(int)Configuration::get('PS_LANG_DEFAULT')] = 'Trustedshops';
			$category->link_rewrite[(int)Configuration::get('PS_LANG_DEFAULT')] = 'trustedshops';
		}

		$category->id_parent = 0;
		$category->level_depth = 0;
		$category->active = 0;
		$category->add();
		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'CAT_ID', intval($category->id));
		return true;
	}

	public function uninstall()
	{
		foreach ($this->available_languages as $iso=>$lang)
			Configuration::deleteByName(TSBuyerProtection::PREFIX_TABLE.'CERTIFICATE_'.strtoupper($iso));

		$category = new Category((int)TSBuyerProtection::$CAT_ID);
		$category->delete();
		Configuration::deleteByName(TSBuyerProtection::PREFIX_TABLE.'CAT_ID');
		Configuration::deleteByName(TSBuyerProtection::PREFIX_TABLE.'SHOPSW');
		Configuration::deleteByName(TSBuyerProtection::PREFIX_TABLE.'ET_CID');
		Configuration::deleteByName(TSBuyerProtection::PREFIX_TABLE.'ET_LID');
		Configuration::deleteByName(TSBuyerProtection::PREFIX_TABLE.'ENV_API');
		return true;
	}

	/**
	 * Just for return the file path
	 * @return string
	 */
	public function getCronFilePath()
	{
		return $this->site_url.'modules/'.self::$module_name.'/cron_garantee.php';
	}

	/**
	 * This method is used to access of TrustedShops API
	 * from a SoapClient object.
	 *
	 * @uses TSBuyerProtection::$webservice_urls with TSBuyerProtection::$ENV_API
	 * 		 To get the api url according to the environment (test or production)
	 * @param string $type
	 * @return SoapClient
	 */
	private function _getClient($type = TSBuyerProtection::WEBSERVICE_BO)
	{
		$url = TSBuyerProtection::$webservice_urls[$type][TSBuyerProtection::$ENV_API];
		$client = new SoapClient($url);
		return $client;
	}

	/**
	 * Checks the Trusted Shops IDs entered in the shop administration
	 * and returns the characteristics of the corresponding certificate.
	 *
	 * @uses TSBuyerProtection::_getClient()
	 * @param string $certificate certificate code already send by Trusted Shops
	 */
	private function _checkCertificate($certificate)
	{
		$array_state = array(
			'PRODUCTION'	=> $this->l('The certificate is valid'),
			'CANCELLED'		=> $this->l('The certificate has expired'),
			'DISABLED'		=> $this->l('The certificate has been disabled'),
			'INTEGRATION'	=> $this->l('The shop is currently being certified'),
			'INVALID_TS_ID'	=> $this->l('No certificate has been allocated to the Trusted Shops ID'),
			'TEST'			=> $this->l('Test certificate'),
		);
		$client = $this->_getClient();
		$validation = false;
		try {
			$validation = $client->checkCertificate($certificate);
		} catch (SoapFault $fault) {
			$this->errors[] = $this->l('Code #').$fault->faultcode.',<br />'.$this->l('message:').$fault->faultstring;
		}

		if(is_int($validation))
			throw new TSBPException($validation, TSBPException::ADMINISTRATION);

		if (!$validation OR array_key_exists($validation->stateEnum, $array_state))
		{
			if ($validation->stateEnum === 'TEST' || $validation->stateEnum === 'PRODUCTION' || $validation->stateEnum === 'INTEGRATION')
			{
				$this->confirmations[] = $array_state[$validation->stateEnum];
				return $validation;
			}
			else
			{
				$this->errors[] = $array_state[$validation->stateEnum];
				return false;
			}
		}
		else
		{
			$this->errors[] = $this->l('Unknown error.');
		}
	}

	/**
	 * Checks the shop's web service access credentials.
	 *
	 * @uses TSBuyerProtection::_getClient()
	 * @param string $ts_id
	 * @param string $user
	 * @param string $password
	 */
	private function _checkLogin($ts_id, $user, $password)
	{
		$client = $this->_getClient();
		$return = 0;
		try {
			$return = $client->checkLogin($ts_id, $user, $password);
		} catch (SoapClient $fault) {
			$this->errors[] = $this->l('Code #').$fault->faultcode.',<br />'.$this->l('message:').$fault->faultstring;
		}
		if ($return < 0)
			throw new TSBPException($return, TSBPException::ADMINISTRATION);

		return true;
	}

	/**
	 * Returns the characteristics of the buyer protection products
	 * that are allocated individually to each certificate by Trusted Shops.
	 *
	 * @uses TSBuyerProtection::_getClient()
	 * @param string $ts_id
	 */
	private function _getProtectionItems($ts_id)
	{
		$client = $this->_getClient();
		try {
			$items = $client->getProtectionItems($ts_id);
		} catch (SoapFault $fault) {
			$this->errors[] = $this->l('Code #').$fault->faultcode.',<br />'.$this->l('message:').$fault->faultstring;
		}
		if (isset($items->item))
			return $items->item;
		return false;
	}

	/**
	 * Check validity for params required for TSBuyerProtection::_requestForProtectionV2()
	 *
	 * @param array $params
	 */
	private function _requestForProtectionV2ParamsValidator(array $params)
	{
		$bool_flag = true;
		$mandatory_keys = array(
			array('name'=>'tsID', 'validator'=>array('isCleanHtml'),),
			array('name'=>'tsProductID', 'validator'=>array('isCleanHtml'),),
			array('name'=>'amount', 'validator'=>array('isFloat'),),
			array('name'=>'currency', 'length'=>3, 'validator'=>array('isString'),),
			array('name'=>'paymentType', 'validator'=>array('isString'),),
			array('name'=>'buyerEmail', 'validator'=>array('isEmail'),),
			array('name'=>'shopCustomerID', 'validator'=>array('isInt'),),
			array('name'=>'shopOrderID', 'validator'=>array('isInt'),),
			array('name'=>'orderDate', 'ereg'=>'#[0-9]{4}\-[0-9]{2}\-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2}#',),
			array('name'=>'shopSystemVersion','validator'=>array('isCleanHtml'),),
			array('name'=>'wsUser','validator'=>array('isCleanHtml'),),
			array('name'=>'wsPassword', 'validator'=>array('isCleanHtml'),),
		);
		foreach ($mandatory_keys as $key)
		{
			$bool_flag = (!array_key_exists($key['name'], $params)) ? false : $bool_flag;
			if ($bool_flag)
			{
				if (isset($key['length']))
					$bool_flag = strlen((string)$params[$key['name']]) === $key['length'];
				if (isset($key['length-min']))
					$bool_flag = strlen((string)$params[$key['name']]) > $key['length-min'];
				if (isset($key['length-max']))
					$bool_flag = strlen((string)$params[$key['name']]) < $key['length-max'];
				if (isset($key['validator']))
					foreach ($key['validator'] as $validator)
						if (method_exists('Validate', $validator))
							$bool_flag = !Validate::$validator((string)$params[$key['name']]) ? false : $bool_flag;
				if(isset($key['ereg']))
					$bool_flag = !preg_match($key['ereg'], $params[$key['name']]) ? false : $bool_flag ;
			}

			if (!$bool_flag)
			{
				$this->errors[] = sprintf($this->l('The field %s is wrong, please ensure it was correctly filled.'), $key['name']);
				break;
			}
		}
		return $bool_flag;

	}

	/**
	 * Create the Buyer Protection application by the web service.
	 * Applications are saved by Trusted Shops and are processed at regular intervals.
	 *
	 * @uses TSBuyerProtection::_getClient()
	 * @uses TSBuyerProtection::_requestForProtectionV2ParamsValidator()
	 * 		 to check required params
	 * @see TSBuyerProtection::cronTasks()
	 * @param array $params
	 */
	private function _requestForProtectionV2(array $params)
	{
		$client = $this->_getClient(TSBuyerProtection::WEBSERVICE_FO);
		$testing_params = $this->_requestForProtectionV2ParamsValidator($params);
		$code = 0;
		$sql = '
		SELECT *
		FROM `'._DB_PREFIX_.TSBuyerProtection::DB_APPLI.'`
		WHERE `id_order` = "'.$params['shopOrderID'].'"
		';
		$order = Db::getInstance()->ExecuteS($sql);

		// If an order was already added, no need to continue.
		// Otherwise a new application is created by TrustedShops.
		// this can occured when order confirmation page is reload.
		if(isset($order[0]))
			return false;

		if ($testing_params)
		{
			try {
				$code = $client->requestForProtectionV2($params['tsID'], $params['tsProductID'], $params['amount'], $params['currency'], $params['paymentType'], $params['buyerEmail'], $params['shopCustomerID'], $params['shopOrderID'], $params['orderDate'], $params['shopSystemVersion'], $params['wsUser'], $params['wsPassword']);
				if ($code < 0)
					throw new TSBPException($code, TSBPException::FRONT_END);
			} catch (SoapFault $fault) {
				$this->errors[] = $this->l('Code #').$fault->faultcode.',<br />'.$this->l('message:').$fault->faultstring;
			} catch (TSBPException $e) {
				$this->errors[] = $e->getMessage();
			}
			if ($code > 0)
			{
				$date = date('Y-m-d H:i:s');
				$sql = '
				INSERT INTO `'._DB_PREFIX_.TSBuyerProtection::DB_APPLI.'`
				(
				`id_application`,
				`ts_id`,
				`id_order`,
				`creation_date`,
				`last_update`
				)
				VALUES
				(
				"'.pSQL($code).'",
				"'.pSQL($params['tsID']).'",
				"'.pSQL($params['shopOrderID']).'",
				"'.$date.'",
				"'.$date.'"
				)
				';
				Db::getInstance()->Execute($sql);

				// To reset product quantity in database.
				$sql = '
				SELECT `id_product`
				FROM `'._DB_PREFIX_.TSBuyerProtection::DB_ITEMS.'`
				WHERE `ts_product_id` = "'.$params['tsProductID'].'"
				';
				$ts_product = Db::getInstance()->ExecuteS($sql);
				$product = new Product($ts_product[0]['id_product']);
				$product->quantity = 1000;
				$product->update();
			}
		}
		else
			$this->errors[] = $this->l('Some parameters sending to "requestForProtectionV2" method are wrong or missing.');
	}

	/**
	 * With the getRequestState() method,
	 * the status of a guarantee application is requested
	 * and in the event of a successful transaction,
	 * the guarantee number is returned.
	 *
	 * @uses TSBuyerProtection::_getClient()
	 * @param array $params
	 * @throws TSBPException
	 */
	private function _getRequestState(array $params)
	{
		$client = $this->_getClient(TSBuyerProtection::WEBSERVICE_FO);
		$code = 0;
		try {
			$code = $client->getRequestState($params['tsID'], $params['applicationID']);
			if ($code < 0)
				throw new TSBPException($code, TSBPException::FRONT_END);
		} catch (SoapFault $fault) {
			$this->errors[] = $this->l('Code #').$fault->faultcode.',<br />'.$this->l('message:').$fault->faultstring;
		} catch (TSBPException $e) {
			$this->errors[] = $e->getMessage();
		}
		return $code;
	}

	/**
	 * Check statut of last applications
	 * saved with TSBuyerProtection::_requestForProtectionV2()
	 *
	 * Negative value means an error occured.
	 * Error code are managed in TSBPException.
	 * @see (exception) TSBPException::_getFrontEndMessage() method
	 *
	 * Trusted Shops recommends that the request
	 * should be automated by a cronjob with an interval of 10 minutes.
	 * @see /../cron_garantee.php
	 *
	 * A message is added to the sheet order in Back-office,
	 * @see Message class
	 *
	 * @uses TSBuyerProtection::_getRequestState()
	 * @uses Message class
	 * @return void
	 */
	public function cronTask()
	{
		// get the last 20min to get the api number (to be sure)
		$mktime = mktime(date('H'), date('i')-20, date('s'), date('m'), date('d'), date('Y'));
		$date = date('Y-m-d H:i:s', $mktime);
		$db_name = _DB_PREFIX_.TSBuyerProtection::DB_APPLI;
		$sql = '
		SELECT *
		FROM `'.$db_name.'`
		WHERE `last_update` >= "'.$date.'" OR `statut_number` <= 0
		';
		$to_check = Db::getInstance()->ExecuteS($sql);
		foreach ($to_check as $application)
		{
			$code = $this->_getRequestState(array('tsID'=>$application['ts_id'], 'applicationID'=>$application['id_application']));
			if (!empty($this->errors))
			{
				$return_message = '<p style="color:red;">'.$this->l('Trusted Shops API returns an error concerning the application #').$application['id_application'].': <br />'.implode(', <br />', $this->errors).'</p>';
				$this->errors = array();
			}
			elseif ($code > 0)
			{
				$return_message = sprintf($this->l('Trusted Shops application number %1$d was successfully processed. The garantee number is: %2$d'), $application['id_application'], $code);
			}
			$sql = '
			UPDATE `'.$db_name.'`
			SET `statut_number` = "'.$code.'"
			WHERE `id_application` >= "'.$application['id_application'].'"
			';
			Db::getInstance()->Execute($sql);
			$msg = new Message();
			$msg->message = $return_message;
			$msg->id_order = (int)$application['id_order'];
			$msg->private = 1;
			$msg->add();
		}
	}

	/**
	 * Registration link to Trusted Shops
	 *
	 * @param string $shopsw
	 * @param string $et_cid
	 * @param string $et_lid
	 * @param string $lang
	 * @return boolean|string boolean in case of $lang is not supported by Trusted Shops
	 * 		   string return is the url to access of form subscription
	 */
	private function _makeRegistrationLink($shopsw, $et_cid, $et_lid, $lang)
	{
		if(array_key_exists($lang, $this->registration_link))
			return $this->registration_link[$lang].sprintf('?shopsw=%s&et_cid=%s&et_lid=%s', urlencode($shopsw), urlencode($et_cid), urlencode($et_lid));
		return false;
	}

	/**
	 * Method to display or redirect the subscription link.
	 *
	 * @param string $link
	 */
	private function _getRegistrationLink($link)
	{

		return '<script type="text/javascript" >$().ready(function(){window.open("'.$link.'");});</script>
		<noscript><p><a href="'.$link.'" target="_blank" title="'.$this->l('Registration Link').'" class="link">'.$this->l('Click to get the Registration Link').'</a><p></noscript>';
	}

	/**
	 * saved paramter to acces of particular subscribtion link.
	 *
	 * @return string the registration link.
	 */
	private function _submitRegistrationLink()
	{
		// @todo : ask for more infos about values types
		TSBuyerProtection::$SHOPSW = (Validate::isCleanHtml(Tools::getValue('shopsw'))) ? Tools::getValue('shopsw') : '';
		TSBuyerProtection::$ET_CID = (Validate::isCleanHtml(Tools::getValue('et_cid'))) ? Tools::getValue('et_cid') : '';
		TSBuyerProtection::$ET_LID = (Validate::isCleanHtml(Tools::getValue('et_lid'))) ? Tools::getValue('et_lid') : '';

		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'SHOPSW', TSBuyerProtection::$SHOPSW);
		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'ET_CID', TSBuyerProtection::$ET_CID);
		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'ET_LID', TSBuyerProtection::$ET_LID);
		$link_registration = $this->_makeRegistrationLink(TSBuyerProtection::$SHOPSW, TSBuyerProtection::$ET_CID, TSBuyerProtection::$ET_LID, Tools::getValue('lang'));
		$this->confirmations[] = $this->l('Registration link has been created. Follow this link if you was not redirected ealier:').'&nbsp;<a href="'.$link_registration.'" class="link">&gt;'.$this->l('Link').'&lt;</a>';
		return $link_registration;
	}

	/**
	 * Save in special database each buyer protection product for a certificate,
	 * Each Trusted Shops particular characteristics is saved.
	 * Create a product in Prestashop database to allow added each of them in cart.
	 *
	 * @param array|stdClass $protection_items
	 * @param string $ts_id
	 */
	private function _saveProtectionItems($protection_items, $ts_id)
	{
		$sql = '
		DELETE ts, p, pl
		FROM `'._DB_PREFIX_.TSBuyerProtection::DB_ITEMS.'` AS ts
		LEFT JOIN `ps_product` AS p ON ts.`id_product` = p.`id_product`
		LEFT JOIN `ps_product_lang` AS pl ON ts.`id_product` = pl.`id_product`
		WHERE ts.`ts_id`="'.$ts_id.'"';
		Db::getInstance()->Execute($sql);

		foreach ($protection_items as $key=>$item)
		{
			//add hidden product
			$product = new Product();
			foreach ($this->available_languages as $iso=>$lang)
			{
				$language = Language::getIdByIso(strtolower($iso));
				if ((int)$language !== 0)
				{
					$product->name[$language] = 'TrustedShops garantee';
					$product->link_rewrite[$language] = 'trustedshops_garantee';
				}
			}

			// If the default lang is different than available languages :
			// (Bug occurred otherwise)
			if (!array_key_exists(Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT')), $this->available_languages))
			{
				$product->name[(int)Configuration::get('PS_LANG_DEFAULT')] = 'Trustedshops';
				$product->link_rewrite[(int)Configuration::get('PS_LANG_DEFAULT')] = 'trustedshops';
			}
			$product->quantity = 1000;
			$product->price = ToolsCore::convertPrice($item->grossFee,Currency::getIdByIsoCode($item->currency));
			$product->id_category_default = TSBuyerProtection::$CAT_ID;
			$product->active = true;
			$product->id_tax = 0;
			$product->add();

			if ($product->id)
			{
				$sql = '
				INSERT INTO `'._DB_PREFIX_.TSBuyerProtection::DB_ITEMS.'` (
				`creation_date`,
				`id_product`,
				`ts_id`,
				`id`,
				`currency`,
				`gross_fee`,
				`net_fee`,
				`protected_amount_decimal`,
				`protection_duration_int`,
				`ts_product_id`
				) VALUES (
				"'.pSQL($item->creationDate).'",
				"'.pSQL($product->id).'",
				"'.pSQL($ts_id).'",
				"'.(int)$item->id.'",
				"'.pSQL($item->currency).'",
				"'.pSQL($item->grossFee).'",
				"'.pSQL($item->netFee).'",
				"'.pSQL($item->protectedAmountDecimal).'",
				"'.pSQL($item->protectionDurationInt).'",
				"'.pSQL($item->tsProductID).'"
				)';
				Db::getInstance()->Execute($sql);
			}
			else {
				$this->errors['products'] = $this->l('Product wasn\'t be saved.');
			}
		}
	}

	/**
	 * Check and add a Trusted Shops certificate in shop.
	 *
	 * @uses TSBuyerProtection::_getProtectionItems()
	 * 		 to get all buyer protection products from Trusted Shops
	 * @uses TSBuyerProtection::_saveProtectionItems()
	 * 		 to save buyer protection products in shop
	 * @return boolean true if certificate is well added, false otherwise
	 */
	private function _submitAddCertificate()
	{
		$checked_certificate = false;
		try {
			$checked_certificate = $this->_checkCertificate(ToolsCore::getValue('new_certificate'));
		} catch (TSBPException $e) {
			$this->errors[] = $e->getMessage();
		}
		if ($checked_certificate)
		{
			TSBuyerProtection::$CERTIFICATE[strtoupper($checked_certificate->certificationLanguage)] = array('stateEnum'=>$checked_certificate->stateEnum, 'typeEnum'=>$checked_certificate->typeEnum, 'url'=>$checked_certificate->url, 'tsID'=>$checked_certificate->tsID, 'user'=>'', 'password'=>'');

			// update the configuration var
			Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'CERTIFICATE_'.strtoupper($checked_certificate->certificationLanguage), Tools::htmlentitiesUTF8(Tools::jsonEncode(TSBuyerProtection::$CERTIFICATE[strtoupper($checked_certificate->certificationLanguage)])));
			$this->confirmations[] = $this->l('Certificate has been well added.');
			if ($checked_certificate->typeEnum === 'EXCELLENCE')
			{
				try {
					$protection_items = $this->_getProtectionItems($checked_certificate->tsID);
					if($protection_items)
						$this->_saveProtectionItems($protection_items, $checked_certificate->tsID);
				} catch (TSBPException $e) {
					$this->errors[] = $e->getMessage();
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Apply delete or edit action to a certificate
	 *
	 * @return boolean|array
	 * 		   - false if action concerned multiple certificate
	 * 		   (in normal way, this never occured )
	 * 		   - return required $certificate to edit.
	 * 		   - true in other case.
	 */
	private function _submitEditCertificate()
	{
		$edit = Tools::getValue('certificate_edit');
		$delete = Tools::getValue('certificate_delete');
		if ((is_array($edit) AND count($edit) > 1) OR (is_array($delete) AND count($delete) > 1))
		{
			$this->errors[] = $this->l('You must edit or delete a Certificate one per one');
			return false;
		}

		// delete action :
		if (is_array($delete) AND isset(TSBuyerProtection::$CERTIFICATE[$delete[0]]['tsID']))
		{
			$certificate_to_delete = TSBuyerProtection::$CERTIFICATE[$delete[0]]['tsID'];
			Configuration::deleteByName(TSBuyerProtection::PREFIX_TABLE.'CERTIFICATE_'.strtoupper($delete[0]));
			unset(TSBuyerProtection::$CERTIFICATE[$delete[0]]);
			$this->confirmations[] = $this->l('The certificate')
				.' "'.$certificate_to_delete.'" ('.$this->l('language').' : '.$delete[0].') '
				.$this->l('has been well deleted');
		}

		// edit action :
		if (is_array($edit))
		{
			$return = TSBuyerProtection::$CERTIFICATE[$edit[0]];
			$return['language'] = $edit[0];
			return $return;
		}
		return true;
	}

	/**
	 * Change the certificate values.
	 * concerns only excellence certificate
	 * for payment type, login and password values.
	 *
	 * @uses TSBuyerProtection::_checkLogin()
	 * @return true;
	 */
	private function _submitChangeCertificate()
	{
		$iso_lang = Tools::getValue('iso_lang');
		$user = Tools::getValue('user');
		$password = Tools::getValue('password');
		$all_payment_type = Tools::getValue('choosen_payment_type');
		if($user != '' AND $password != '')
		{
			TSBuyerProtection::$CERTIFICATE[$iso_lang]['payment_type'] = array();
			if ($all_payment_type)
			{
				if (is_array($all_payment_type))
					foreach ($all_payment_type as $key=>$module_id)
						TSBuyerProtection::$CERTIFICATE[$iso_lang]['payment_type'][(string)$key] = $module_id;
			}

			$check_login = false;
			try {
				$check_login = $this->_checkLogin(TSBuyerProtection::$CERTIFICATE[$iso_lang]['tsID'], $user, $password);
			} catch (TSBPException $e) {
				$this->errors[] = $e->getMessage();
			}
			if($check_login)
			{
				TSBuyerProtection::$CERTIFICATE[$iso_lang]['user'] = $user;
				TSBuyerProtection::$CERTIFICATE[$iso_lang]['password'] = $password;
				Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'CERTIFICATE_'.$iso_lang, Tools::htmlentitiesUTF8(Tools::jsonEncode(TSBuyerProtection::$CERTIFICATE[$iso_lang])));
				$this->confirmations[] = $this->l('Certificate login has been well added.');

			}
		}
		else
		{
			$this->errors[] = $this->l('You have to set a username and a password before any change.');
		}
		return true;
	}

	/**
	 * Change the environment for working.
	 * Not use anymore but keeped
	 * @return true
	 */
	private function _submitEnvironment()
	{
		TSBuyerProtection::$ENV_API = Tools::getValue('env_api');
		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'ENV_API', TSBuyerProtection::$ENV_API);
		return true;
	}

	/*
	 ** Update the env_api
	 */
	public function _setEnvApi($env_api)
	{
		TSBuyerProtection::$ENV_API = $env_api;
		Configuration::updateValue(TSBuyerProtection::PREFIX_TABLE.'ENV_API', TSBuyerProtection::$ENV_API);
	}

	/**
	 * Dispatch post process depends on each formular
	 *
	 * @return array depend on the needs about each formular.
	 */
	private function _preProcess()
	{
		$posts_return = array();
		/*if (Tools::isSubmit('submit_registration_link'))
			$posts_return['registration_link'] = $this->_submitRegistrationLink();*/
		if (Tools::isSubmit('submit_add_certificate'))
			$posts_return['add_certificate'] = $this->_submitAddCertificate();
		if (Tools::isSubmit('submit_edit_certificate'))
			$posts_return['edit_certificate'] = $this->_submitEditCertificate();
		if (Tools::isSubmit('submit_change_certificate'))
			$posts_return['change_certificate'] = $this->_submitChangeCertificate();
		return $posts_return;
	}

	/**
	 * Display each formaular in back-office
	 *
	 * @see Module::getContent()
	 * @return string for displaying form.
	 */
	public function getContent()
	{
		$out = '';
		$posts_return = $this->_preProcess();
		if(empty($this->errors))
		{
			$out .= '';
		}
		$out .= $this->_displayPresentation();
		$out .= '<br />';
		//$out .= $this->_displayFormRegistrationLink(( isset($posts_return['registration_link']) ? $posts_return['registration_link'] : false ));
		$out .= '<br />';
		$out .= $this->_displayFormAddCertificate();
		$out .= '<br />';

		$bool_display_certificats = false;
		if (is_array(self::$CERTIFICATE))
			foreach (self::$CERTIFICATE as $certif)
				$bool_display_certificats = (isset($certif['tsID']) && $certif['tsID'] != '')? true : $bool_display_certificats;

		if ($bool_display_certificats)
			$out .= $this->_displayFormCertificatesList();
			if (isset($posts_return['edit_certificate']) && $posts_return['edit_certificate'] &&
			is_array($posts_return['edit_certificate']))
		{
			$out .= '<br />';
			$out .= $this->_displayFormEditCertificate($posts_return['edit_certificate']);
		}
		$out .= '<br />';
		$out .= $this->_displayInfoCronTask();
		return $out;
	}
	private function _displayPresentation()
	{
		return '
			<div style="text-align:right; margin:10px 20px 10px 0">
				<img src="'.__PS_BASE_URI__.'modules/'.self::$module_name.'/img/siegel.gif" alt="logo"/>
			</diV>
		<h3>'.$this->l('Seal of Approval and Buyer Protection').'</h3>
		<p>'.$this->l('Trusted Shops is the well-known internet Seal of Approval for online shops which also offers customers a Buyer Protection. During the audit, your online shop is subjected to extensive and thorough tests. This audit, consisting of over 100 individual criteria, is based on the requirements of consumer protection, national and European laws.').'</p>
		<h3>'.$this->l('More trust leads to more sales!').'</h3>
		<p>'.$this->l('The Trusted Shops seal of approval is the optimal way to increase the trust of your online customers. Trust increases customers\' willingness to buy from you.').'</p>
		<h3>'.$this->l('Less abandoned purchases').'</h3>
		<p>'.$this->l('Give your online customers a strong reason to buy proposing the Trusted Shops Buyer Protection. This additional security leads to less shopping basket abandonment').'</p>
		<h3>'.$this->l('Profitable and long-term customer relationship').'</h3>
		<p>'.$this->l('For many online shoppers, the Trusted Shops Seal of Approval with Buyer Protection is an effective sign of quality for safe shopping on the internet. One-time buyers become regular customers.').'</p><br />
		<h3>'.$this->l('Environment type').'</h3>
		<p>'.$this->l('You are currently using the mode :').' <b>'.TSBuyerProtection::$ENV_API.'</b></p><br />';
	}

	private function _displayFormRegistrationLink($link = false)
	{
		$out = '
		<form action="'.$this->_makeFormAction($_SERVER['REQUEST_URI'], $this->id_tab).'" method="post" >
			<fieldset>
				<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Get the Registration Link').'</legend>
				<p>'.$this->l('This variable was sent you via email by TrustedShops').'</p>
				<label>'.$this->l('Internal identification of shop software at Trusted Shops').'</label>
				<div class="margin-form">
					<input type="text" name="shopsw" value="'.TSBuyerProtection::$SHOPSW.'"/>
				</div>
				<br />
				<br class="clear" />
				<label>'.$this->l('Etracker channel').'</label>
				<div class="margin-form">
					<input type="text" name="et_cid" value="'.TSBuyerProtection::$ET_CID.'"/>
				</div>
				<br class="clear" />
				<label>'.$this->l('Etracker campaign').'</label>
				<div class="margin-form">
					<input type="text" name="et_lid" value="'.TSBuyerProtection::$ET_LID.'"/>
				</div>
				<label>'.$this->l('Language').'</label>
				<div class="margin-form">
					<select name="lang" >';
		foreach ($this->available_languages as $iso=>$lang)
			if(is_array($lang))
						$out .= '<option value="'.$iso.'" '.((int)$lang['id_lang'] === TSBuyerProtection::$DEFAULT_LANG ? 'selected' : '' ).'>'.$lang['name'].'</option>';
					$out .= '</select>
				</div>
				<div style="text-align:center;">';
				// If Javascript is deactivated
				if ($link !== false)
					$out .= $this->_getRegistrationLink($link);
				$out .='<input type="submit" name="submit_registration_link" class="button" value="'.$this->l('send').'"/>
				</div>
			</fieldset>
		</form>';
		return $out;
	}
	private function _displayFormAddCertificate()
	{
		$out = '
		<form action="'.$this->_makeFormAction($_SERVER['REQUEST_URI'], $this->id_tab).'" method="post" >
			<fieldset>
				<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Add Trusted Shops certificate').'</legend>
				<label>'.$this->l('New certificate').'</label>
				<div class="margin-form">
					<input type="text" name="new_certificate" value="" style="width:300px;"/>&nbsp;
					<input type="submit" name="submit_add_certificate" class="button" value="'.$this->l('Add it').'"/>
				</div>
			</fieldset>
		</form>';
		return $out;
	}
	private function _displayFormCertificatesList()
	{
		$out = '';
		$out .= '
		<script type="text/javascript">
			$().ready(function()
			{
				$(\'#certificate_list\').find(\'input[type=checkbox]\').click(function()
				{
					$(\'#certificate_list\').find(\'input[type=checkbox]\').not($(this)).removeAttr(\'checked\');
				});
			});
		</script>
		<form action="'.$this->_makeFormAction($_SERVER['REQUEST_URI'], $this->id_tab).'" method="post" >
		<fieldset>
			<legend><img src="../img/admin/cog.gif" alt="" />'.$this->l('Manage Trusted Shops certificates').'</legend>
				<table width="100%">
					<thead>
						<tr style="text-align:center;">
							<th>'.$this->l('Certificate').'</th>
							<th>'.$this->l('Language').'</th>
							<th>'.$this->l('State').'</th>
							<th>'.$this->l('Type').'</th>
							<th>'.$this->l('Shop url').'</th>
							<th>'.$this->l('Edit').'</th>
							<th>'.$this->l('Delete').'</th>
						</tr>
					</thead>
					<tbody id="certificate_list">';
		foreach (TSBuyerProtection::$CERTIFICATE as $lang=>$certificate)
		{
			$certificate = (array)$certificate;
			if (isset($certificate['tsID']) AND $certificate['tsID'] !== '')
			{
				$out .= '
						<tr style="text-align:center;">
							<td>'.$certificate['tsID'].'</td>
							<td>'.$lang.'</td>
							<td>'.$certificate['stateEnum'].'</td>
							<td>'.$certificate['typeEnum'].'</td>
							<td>'.$certificate['url'].'</td>
							<td>';
				if ($certificate['typeEnum'] === 'EXCELLENCE') {
					$out .= '<input type="checkbox" name="certificate_edit[]" value="'.$lang.'" />';
					$out .= $certificate['user'] == '' ? '<br /><b style="color:red;font-size:0.7em;">'.$this->l('Login or password missing').'</b>' : '';
				} else {
					$out .= $this->l('No need');
				}
				$out .= '
							</td>
							<td>';
				if ($certificate['typeEnum'] === 'EXCELLENCE') {
					$out .= '<input type="checkbox" name="certificate_delete[]" value="'.$lang.'" />';
				} else {
					$out .= $this->l('No need');
				}
				$out .= '
							</td>
						</tr>';
			}
		}
		$out .='
					</tbody>
				</table>
				<div style="text-align:center;"><input type="submit" name="submit_edit_certificate" class="button" value="'.$this->l('Edit certificate').'"/></div>
			</fieldset>
		</form>
		';
		return $out;
	}

	/**
	 * Check if a module is payment module.
	 *
	 * Method instanciate a $module by its name,
	 * Module::getInstanceByName() rather than Module::getInstanceById()
	 * is used for cache improvement and avoid an sql request.
	 *
	 * Method test if PaymentMethod::getCurrency() is a method from the module.
	 *
	 * @see Module::getInstanceByName() in classes/Module.php
	 * @param string $module name of the module
	 */
	private static function _isPaymentModule($module)
	{
		$return = false;
		$module = Module::getInstanceByName($module);

		if (method_exists($module, 'getCurrency')){
			$return = clone $module;
		}
		unset($module);
		return $return;
	}
	private function _displayFormEditCertificate(array $certificate)
	{
		$installed_modules = Module::getModulesInstalled();
		$payment_module_collection = '';
		foreach ($installed_modules as $k=>$value)
			if($return = TSBuyerProtection::_isPaymentModule($value['name']))
				$payment_module_collection[$value['id_module']] = $value;
		$out = '
		<script type="text/javascript" src="'.$this->site_url.'modules/trustedshops/lib/js/payment.js" ></script>
		<script type="text/javascript">
			$().ready(function()
			{
				TSPayment.payment_type = $.parseJSON(\''.Tools::jsonEncode(TSBuyerProtection::$payments_type).'\');
				TSPayment.payment_module = $.parseJSON(\''.Tools::jsonEncode($payment_module_collection).'\');
				$(\'.payment-module-label\').css(TSPayment.module_box.css).fadeIn();
				$(\'.choosen_payment_type\').each(function()
				{
					TSPayment.deleteModuleFromList($(this).val());
					TSPayment.setLabelModuleName($(this).val());
				});
				TSPayment.init();
			});

		</script>
		<form action="'.$this->_makeFormAction($_SERVER['REQUEST_URI'], $this->id_tab).'" method="post" >
			<fieldset>
				<legend><img src="../img/admin/tab-tools.gif" alt="" />'.$this->l('Edit certificate').'</legend>
				<input type="hidden" name="iso_lang" value="'.$certificate['language'].'" />
				<label>'.$this->l('Language').'</label>
				<div class="margin-form">'.$certificate['language'].'</div>
				<label>'.$this->l('Shop url').'</label>
				<div class="margin-form">'.$certificate['url'].'</div>
				<label>'.$this->l('Certificate id').'</label>
				<div class="margin-form">'.$certificate['tsID'].'</div>
				<label>'.$this->l('User Name').' <sup>*</sup></label>
				<div class="margin-form"><input type="text" name="user" value="'.$certificate['user'].'" style="width:300px;"/></div>
				<label>'.$this->l('Password').' <sup>*</sup></label>
				<div class="margin-form"><input type="text" name="password" value="'.$certificate['password'].'" style="width:300px;"/></div>
				<div id="payment-type">
					<label>'.$this->l('Payment type to edit').' <sup>*</sup></label>
					<div class="margin-form">
						<select name="payment_type">';
		foreach (TSBuyerProtection::$payments_type as $type=>$translation)
			$out .= '	<option value="'.$type.'" >'.$translation.'</option>';
		$out .= '		</select>&nbsp;'
					.$this->l('with')
						.'&nbsp;
						<select name="payment_module">';
		foreach ($payment_module_collection as $module_info)
			$out .= '		<option value="'.$module_info['id_module'].'" >'.$module_info['name'].'</option>';
		$out .= '		</select>&nbsp;'
					.$this->l('payment module')
					.'&nbsp;<input type="button" value="'.$this->l('Add it').'" class="button" name="add_payment_module" />
					</div><!-- .margin-form -->
					<div id="payment_type_list">';
		$input_output = '';
		if (isset($certificate['payment_type']) AND !empty($certificate['payment_type']))
		{
			foreach ($certificate['payment_type'] as $payment_type=>$modules)
			{
			$out .= '	<label style="clear:both;" class="payment-type-label" >'.TSBuyerProtection::$payments_type[$payment_type].'</label>';
			$out .= '	<div class="margin-form" id="block-payment-'.$payment_type.'">';
				foreach ($modules as $module_id)
				{
					$out .= '<b class="payment-module-label" id="label-module-'.$module_id.'"></b>';
					$input_output .= '<input type="hidden" value="'.$module_id.'" class="choosen_payment_type" name="choosen_payment_type['.$payment_type.'][]">';
				}
			$out .= '	</div><!-- .margin-form -->';
			}
		}
		$out .= '</div><!-- #payment_type_list -->
			</div><!-- #payment-type -->
			<p id="input-hidden-val" style="display:none;">'.$input_output.'</p>
			<p style="text-align:center;">
				<input type="submit" name="submit_change_certificate" class="button" value="'.$this->l('Update it').'"/>
			</p>
			</fieldset>
		</form>';
		return $out;
	}
	private function _displayInfoCronTask()
	{
		$out = '<fieldset>
				<legend><img src="../img/admin/warning.gif" alt="" />'.$this->l('Cronjob configuration').'</legend>';
		$out .= '<p>'
					.$this->l('You need to set a cron Task on your server, working with your EXCELLENT certificate.').'<br />'
					.$this->l('The file you need to call:').' <b style="color:red;">'.$this->getCronFilePath().'</b><br />'
					.$this->l('Trusted Shops recommends that the request should be automated by a cronjob with an interval of 10 minutes.')
				.'</p>';
		$out .= '</fieldset>';
		return $out;
	}
	public function hookRightColumn($params)
	{
		$lang = Language::getIsoById($params['cookie']->id_lang);
		$lang = strtoupper($lang);
		if (array_key_exists($lang, $this->available_languages) AND isset(TSBuyerProtection::$CERTIFICATE[$lang]['tsID']))
		{
			TSBuyerProtection::$smarty->assign('trusted_shops_id', TSBuyerProtection::$CERTIFICATE[$lang]['tsID']);
			TSBuyerProtection::$smarty->assign('onlineshop_name', ConfigurationCore::get('PS_SHOP_NAME'));
			$url = str_replace(array('#shop_id#', '#shop_name#'), array(TSBuyerProtection::$CERTIFICATE[$lang]['tsID'], urlencode(str_replace('_', '-', ConfigurationCore::get('PS_SHOP_NAME')))), TSBuyerProtection::$certificate_link[$lang]);
			TSBuyerProtection::$smarty->assign('trusted_shops_url', $url);
			return $this->display(TSBuyerProtection::$module_name, 'seal_of_approval.tpl');
		}
	}
	/**
	 * For Excellence certificate display Buyer protection products.
	 * An error message if the certificate is not totally filled
	 *
	 * @param array $params
	 * @return string tpl content
	 */
	public function hookPaymentTop($params)
	{
		$lang = Language::getIsoById($params['cookie']->id_lang);
		$lang = strtoupper($lang);

		if (!isset(TSBuyerProtection::$CERTIFICATE[$lang]) ||
			!isset(TSBuyerProtection::$CERTIFICATE[$lang]['typeEnum']))
			return '';

		// This hook is available only with EXCELLENCE certificate.
		if(TSBuyerProtection::$CERTIFICATE[$lang]['typeEnum'] == 'CLASSIC' OR (TSBuyerProtection::$CERTIFICATE[$lang]['stateEnum'] !== 'INTEGRATION' AND TSBuyerProtection::$CERTIFICATE[$lang]['stateEnum'] !== 'PRODUCTION' AND TSBuyerProtection::$CERTIFICATE[$lang]['stateEnum'] !== 'TEST'))
			return '';

		// If login parameters missing for the certificate an error occured
		if ((TSBuyerProtection::$CERTIFICATE[$lang]['user'] == '' OR TSBuyerProtection::$CERTIFICATE[$lang]['password'] == '') AND TSBuyerProtection::$CERTIFICATE[$lang]['typeEnum'] == 'EXCELLENCE')
		{
			return '
			<p style="color:red;text-align:center;font-size:14px;font-weight:bold;">'
			.$this->l('The Trusted Shop Buyer Protection need a login to success. Please contact the shop administrator.')
			.'<br />'
			.$this->l('Problem occurred with your language:').' "'.$lang
			.'"</p>';
		}

		if (array_key_exists($lang, $this->available_languages))
		{
			$currency = new Currency((int)$params['cookie']->id_currency);
			$sql = '
			SELECT * FROM `'._DB_PREFIX_.TSBuyerProtection::DB_ITEMS.'`
			WHERE 1
			AND ts_id ="'.TSBuyerProtection::$CERTIFICATE[$lang]['tsID'].'"
			AND `protected_amount_decimal` >= "'.$params['cart']->getOrderTotal(true, Cart::BOTH).'"
			AND `currency` = "'.$currency->iso_code.'"
			ORDER BY `protected_amount_decimal`
			LIMIT 0,1';
			$items = Db::getInstance()->ExecuteS($sql);
			if (empty($items))
			{
				$sql = '
				SELECT * FROM `'._DB_PREFIX_.TSBuyerProtection::DB_ITEMS.'`
				WHERE 1
				AND ts_id ="'.TSBuyerProtection::$CERTIFICATE[$lang]['tsID'].'"
				AND `protected_amount_decimal` <= "'.$params['cart']->getOrderTotal(true, Cart::BOTH).'"
				AND `currency` = "'.$currency->iso_code.'"
				ORDER BY `protected_amount_decimal`
				LIMIT 0,1';
				$items = Db::getInstance()->ExecuteS($sql);
			}

			TSBuyerProtection::$smarty->assign(array(
				'tax_label' => 'TTC',
				'buyer_protection_items' => $items)
			);
		}
		return $this->display(TSBuyerProtection::$module_name, 'display_products.tpl');
	}

	/**
	 * This prepare values to create the Trusted Shops web service
	 * for Excellence certificate.
	 *
	 * @see TSBuyerProtection::_requestForProtectionV2() method
	 * @param array $params
	 * @param string $lang
	 * @return string empty if no error occured or no item was set.
	 */
	private function _orderConfirmationExcellence($params, $lang)
	{
		$currency = new Currency((int)$params['objOrder']->id_currency);
		$order_products = $params['objOrder']->getProducts();
		$order_item_ids = array();

		foreach ($order_products as $product)
			$order_item_ids[] = $product['product_id'];

		$sql = '
		SELECT * FROM `'._DB_PREFIX_.TSBuyerProtection::DB_ITEMS.'`
		WHERE 1
		AND `id_product` IN ('.implode(',', $order_item_ids).')
		AND `ts_id` ="'.TSBuyerProtection::$CERTIFICATE[$lang]['tsID'].'"
		AND `currency` = "'.$currency->iso_code.'"
		';
		$item = Db::getInstance()->ExecuteS($sql);

		// No items ? means no buyer protection products was bought.
		if(empty($item))
			return '';

		// In normal context this never occured,
		// because of a buyer could never add multiple Buyer protection products.
		if (count($item) > 1)
		{
			$this->errors[] = $this->l('A buyer can\'t buy multiple Buyer Protection Products.');
			die($this->errors);
		}
		$item = $item[0];

		$customer = new Customer($params['objOrder']->id_customer);
		$payment_module = Module::getInstanceByName($params['objOrder']->module);
		$arr_params = array();
		foreach (TSBuyerProtection::$CERTIFICATE[$lang]['payment_type'] as $payment_type => $id_modules)
		{
			if(in_array($payment_module->id, $id_modules))
			{
				$arr_params['paymentType'] = (string)$payment_type;
				break;
			}
		}
		if ($arr_params['paymentType'] == '')
			$arr_params['paymentType'] = 'OTHER';
		$arr_params['tsID'] = TSBuyerProtection::$CERTIFICATE[$lang]['tsID'];
		$arr_params['tsProductID'] = $item['ts_product_id'];
		$arr_params['amount'] = $params['total_to_pay'];
		$arr_params['currency'] = $currency->iso_code;
		$arr_params['buyerEmail'] = $customer->email;
		$arr_params['shopCustomerID'] = $customer->id;
		$arr_params['shopOrderID'] = $params['objOrder']->id;
		$arr_params['orderDate'] = date('Y-m-d\TH:i:s', strtotime($params['objOrder']->date_add));
		$arr_params['shopSystemVersion'] = 'Prestashop '._PS_VERSION_;
		$arr_params['wsUser'] = TSBuyerProtection::$CERTIFICATE[$lang]['user'];
		$arr_params['wsPassword'] = TSBuyerProtection::$CERTIFICATE[$lang]['password'];

		$this->_requestForProtectionV2($arr_params);

		$return = '';
		if (!empty($this->errors))
			$return = '<p style="color:red">'.implode('<br />', $this->errors).'</p>';
		else
			$return = '<p>'.$this->l('You will receive a mail by Trusted Shops about your garantie number.').'</p>';
		return $return;
	}

	/**
	 * Trusted Shops Buyer Protection is integrated at the end of the checkout
	 * as a form on the order confirmation page.
	 * At the moment the customer clicks the registration button,
	 * the order data is processed to Trusted Shops.
	 * The customer confirms the Buyer Protection on the Trusted Shops site.
	 * The guarantee is then booked and the customer receives an email by Trusted Shops.
	 *
	 * @param array $params
	 * @param string $lang
	 * @return string tpl content
	 */
	private function _orderConfirmationClassic($params, $lang)
	{
		$customer = new Customer($params['objOrder']->id_customer);
		$currency = new Currency((int)$params['objOrder']->id_currency);

		$arr_params = array();
		$arr_params['charset'] = 'UTF-8';
		$arr_params['shop_id'] = TSBuyerProtection::$CERTIFICATE[$lang]['tsID'];
		$arr_params['buyer_email'] = $customer->email;
		$arr_params['amount'] = $params['total_to_pay'];
		$arr_params['currency'] = $currency->iso_code;
		$arr_params['payment_type'] = 'CHEQUE';//$params['objOrder']->module;
		$arr_params['customer_id'] = $customer->id;
		$arr_params['order_id'] = $params['objOrder']->id;
		TSBuyerProtection::$smarty->assign(
			array(
				'tax_label' => 'TTC',
				'buyer_protection' => $arr_params
			)
		);
		return $this->display(TSBuyerProtection::$module_name, 'order-confirmation-tsbp-classic.tpl');
	}

	/**
	 * Order confirmation displaying and actions depend on the certificate type.
	 *
	 * @uses TSBuyerProtection::_orderConfirmationClassic() for Classic certificate
	 * @uses TSBuyerProtection::_orderConfirmationExcellence for Excellence certificate.
	 * @param array $params
	 * @return string depend on which certificate is used.
	 */
	public function hookOrderConfirmation($params)
	{
		$lang = Language::getIsoById($params['objOrder']->id_lang);
		$lang = strtoupper($lang);

		// If certificate is a classic type or certificate login parameters missing
		if (((TSBuyerProtection::$CERTIFICATE[$lang]['user'] == '' OR TSBuyerProtection::$CERTIFICATE[$lang]['password'] == '') AND TSBuyerProtection::$CERTIFICATE[$lang]['typeEnum'] == 'EXCELLENCE')
		OR (TSBuyerProtection::$CERTIFICATE[$lang]['stateEnum'] !== 'INTEGRATION' AND TSBuyerProtection::$CERTIFICATE[$lang]['stateEnum'] !== 'PRODUCTION' AND TSBuyerProtection::$CERTIFICATE[$lang]['stateEnum'] !== 'TEST'))
			return '';

		if(TSBuyerProtection::$CERTIFICATE[$lang]['typeEnum'] == 'CLASSIC')
			return $this->_orderConfirmationClassic($params, $lang);
		else
			return $this->_orderConfirmationExcellence($params, $lang);
	}
}

