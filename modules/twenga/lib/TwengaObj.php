<?php
/**
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
 *  @copyright 2007-2013 PrestaShop SA : 6 rue lacepede, 75005 PARIS
 *  @version  Release: $Revision: 16958 $
 *  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 **/

/**
 * This Class allow to use Twenga API.
 * See details for more infos.
 * 
 * @version 1.3
 */
class TwengaObj
{
	/**
	 * path to load each needed files
	 * @var string
	 */
	public static $base_dir;
	
	/**
	 * Set the object which use the translation method for the specific module.
	 * @var AbsTrustedShops
	 */
	private static $translation_object;
	
	/** 
	 * @var string for authentication on Twenga API
	 */
	private static $user_name;
	
	/**
	 * @var string for authentication on Twenga API
	 */
	private static $password;
	
	/**
	 * This partner_id is the same for each prestashop/twenga module.
	 * You don't have to change it.
	 */
	const PARTNER_AUTH_KEY = 'NTM3YTU1MTJjNTZhODg3OWI2Y2FhNDgyZjU4Njc0ZWU5NDMyNjgxNA==';
	
	/**
	 * @var array is build in constructor, save all url method of twenga API
	 */
	private static $arr_api_url;
	
	/**
	 * @var string is the twenga hashkey, use to identify the merchant.
	 * 		this value is saved in the configuration table in database.
	 * @see TwengaObj::saveMerchantLogin()
	 * @see TwengaObj::__constructor()
	 */
	public static $hashkey;
	
	public function getUserName()
	{
		return self::$user_name;
	}
	public function setUserName($user_name)
	{
		if ($user_name !== '' && is_string($user_name))
		{
			self::$user_name = $user_name;
			return true;
		}
		else 
			return false;
	}
	public function getPassword()
	{
		return self::$password;
	}
	public function setPassword($password)
	{
		if ($password !== '' && Validate::isCleanHtml($password) && is_string($password) && strlen($password) <= 32)
		{
			self::$password = $password;
			return true;
		}
		else 
			return false;
	}
	public function getHashkey()
	{
		return self::$hashkey;
	}
	/**
	 * Set the hashkey
	 * @param string $hashkey
	 * @return boolean true if it's a valid key false otherwise.
	 */
	public function setHashkey($hashkey)
	{
		if ($hashkey !== '' && Validate::isCleanHtml($hashkey) && is_string($hashkey) && strlen($hashkey) <= 32)
		{
		   self::$hashkey = $hashkey;
		   return true;
		}
		else 
			return false;
	}
	public static function deleteMerchantLogin()
	{
		if (Configuration::get('TWENGA_USER_NAME'))
			Configuration::deleteByName('TWENGA_USER_NAME');
		self::$user_name = null;
		if (Configuration::get('TWENGA_PASSWORD'))
			Configuration::deleteByName('TWENGA_PASSWORD');
		self::$password = null;
		if (Configuration::get('TWENGA_HASHKEY'))
		Configuration::deleteByName('TWENGA_HASHKEY');
		self::$hashkey = null;
		return true;
	}
	/**
	 * Save the Login access and hashkey in database
	 * Use TwengaObj::siteExist() method to check the hashkey validity and login
	 */
	public function saveMerchantLogin()
	{
		if (self::$user_name !== NULL
		&& self::$password !== NULL
		&& self::$hashkey !== NULL)
		{
			$site_exist = false;
			
			try {
			   $site_exist = $this->siteExist();
			} catch (Exception $e) {
				self::deleteMerchantLogin();
				throw $e;
			}
			if (!$site_exist)
			{
				self::deleteMerchantLogin();
				return false;
			}
			else
			{
				Configuration::updateValue('TWENGA_USER_NAME', self::$user_name);
				Configuration::updateValue('TWENGA_PASSWORD', self::$password);
				Configuration::updateValue('TWENGA_HASHKEY', self::$hashkey);
				return true;
			}
		} else {
			return false;
		}
	}
	public function getArrApiUrl ()
	{
		return self::$arr_api_url;
	}
	
	/**
	 * Constructor get all necessary infos for the API.
	 * - urls for API methods
	 * - the hashkey, login & password for authentication
	 * Else method check 
	 */
	public function __construct()
	{
		require_once realpath(dirname(__FILE__).'/TwengaFields.php');
		
		if (self::$arr_api_url === NULL)
		{
			self::$arr_api_url = array();
			self::$arr_api_url['getSubscriptionLink'] = 'http://rts.twenga.com/api/Site/GetSubscriptionLink';
			self::$arr_api_url['siteExist'] = 'http://rts.twenga.com/api/Site/Exist';
			self::$arr_api_url['siteActivate'] = 'http://rts.twenga.com/api/Site/Activate';
			self::$arr_api_url['getTrackingScript'] = 'http://rts.twenga.com/api/Site/GetTrackingScript';
			self::$arr_api_url['orderExist'] = 'http://rts.twenga.com/api/Order/Exist';
			self::$arr_api_url['orderValidate'] = 'http://rts.twenga.com/api/Order/Validate';
			self::$arr_api_url['orderCancel'] = 'http://rts.twenga.com/api/Order/Cancel';
			self::$arr_api_url['addFeed'] = 'https://rts.twenga.com/api/Site/AddFeed';
			
			if (self::PARTNER_AUTH_KEY === NULL)
				throw new TwengaException(self::$translation_object->l('To activate the Twenga plugin, "PARTNER_AUTH_KEY" contant must be set. Default installation of Prestashop contains this value.', basename(__FILE__, '.php')));
			
			if (Configuration::get('TWENGA_HASHKEY') !== false && self::$hashkey === NULL)
				self::$hashkey = Configuration::get('TWENGA_HASHKEY');
			if (Configuration::get('TWENGA_USER_NAME') !== false && self::$user_name === NULL)
				self::$user_name = Configuration::get('TWENGA_USER_NAME');
			if (Configuration::get('TWENGA_PASSWORD') !== false && self::$password === NULL)
				self::$password = Configuration::get('TWENGA_PASSWORD');
		}
	}
	
	/**
	 * Build correct URl for cURL, using array of params 
	 * @param string $url
	 * @param array $params
	 */
	private static function buildUrlToQuery($url, $params)
	{
		$str_params = http_build_query($params);
		$str_url = $url.(($str_params !== '') ? '?'.$str_params : ''); 
		return $str_url;
	}
	
	/**
	 * Execute cURL to access of the Twenga API.
	 * @param string $query
	 * @param array $params
	 * @param boolean $authentication
	 * @throws TwengaException in case of cURL error.
	 * @return array with status code and response of the cURL request.
	 */
	private static function executeQuery($query, $params = array(), $authentication = true)
	{
		$defaultParams = array(
			CURLOPT_HEADER => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLINFO_HEADER_OUT => TRUE,
			CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
		);
		if ($authentication)
			$defaultParams[CURLOPT_USERPWD] = md5(self::$user_name).':'.md5(self::$password);
		$session = curl_init($query);
		$arr_opt = $defaultParams + $params;
		curl_setopt_array($session, $arr_opt);
		$response = curl_exec($session);
		$response = explode("\r\n\r\n", $response);
		
		$header = $response[0];
		$response = $response[1];
		
		$status_code = (int)curl_getinfo($session, CURLINFO_HTTP_CODE);
		if ($status_code === 0)
			throw new TwengaException('CURL Error: '.curl_error($session));
		curl_close($session);
		
		return array('status_code' => $status_code, 'response' => $response);
	}
	
	/** 
	 * @param string $method_name use to instanciate the good TwengaFields subclass.
	 * @param array $params to check
	 * @throws TwengaException if the object instanciate by the method name to check $params is a TwengaFields subclass.
	 * @throws TwengaFieldsException thrown by the TwengaFields::checkParams() method.
	 */
	public static function checkParams($method_name, $params)
	{
		$classname = 'TwengaFields'.ucfirst($method_name);
		if (class_exists($classname))
		{
			$fields = new $classname();
			if (!$fields instanceof TwengaFields)
				throw new TwengaException(self::$translation_object->l('Object for validate fields must be an instance of TwengaFields class', basename(__FILE__, '.php')));
			try {
				$fields->setParams($params)->checkParams();
			} catch (TwengaFieldsException $e) {
				throw $e;
			}
		}
	}
	
	/**
	 * @param array $params
	 * @throws TwengaFieldsException
	 * @throws TwengaException
	 */
	public function getSubscriptionLink($params = array())
	{
		require_once realpath(dirname(__FILE__).'/TwengaFieldsGetSubscriptionLink.php');
//		$params['site_id'] = self::$site_id;
		$params['PARTNER_AUTH_KEY'] = self::PARTNER_AUTH_KEY;
		try {
			self::checkParams(__FUNCTION__, $params);
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_params = self::buildUrlToQuery(self::$arr_api_url[__FUNCTION__],$params);
		
		try {
			$response = self::executeQuery($str_params, array(), false);
			self::checkStatusCode($response['status_code']);
			$obj_xml = self::parseXml($response['response']);
		} catch (TwengaException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new TwengaException(self::$translation_object->l($e->getMessage(),basename(__FILE__, '.php')), $e->getCode());
		}
		return (array)$obj_xml;
	}

	public function addFeed($params = array())
	{
		require_once realpath(dirname(__FILE__).'/TwengaAddFeed.php');
		$params['key'] = self::$hashkey;
		$params['PARTNER_AUTH_KEY'] = self::PARTNER_AUTH_KEY;
		try {
			self::checkParams(__FUNCTION__, $params);
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_params = self::buildUrlToQuery(self::$arr_api_url[__FUNCTION__],$params);
		try {
			$response = self::executeQuery($str_params);
			self::checkStatusCode($response['status_code']);
			$obj_xml = self::parseXml($response['response']);
		} catch (TwengaException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new TwengaException(self::$translation_object->l($e->getMessage(),basename(__FILE__, '.php')), $e->getCode());
		}
		return ((string)$obj_xml->message === 'true') ? true : false;
	}

	/**
	 * @param array $params
	 * @throws TwengaFieldsException
	 * @throws TwengaException
	 */
	public function siteExist($params = array())
	{
		require_once realpath(dirname(__FILE__).'/TwengaFieldsSiteExist.php');
		$params['key'] = self::$hashkey;
		$params['PARTNER_AUTH_KEY'] = self::PARTNER_AUTH_KEY;
		
		try {
			self::checkParams(__FUNCTION__, $params);
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_params = self::buildUrlToQuery(self::$arr_api_url[__FUNCTION__],$params);
		try {
			$response = self::executeQuery($str_params);
			self::checkStatusCode($response['status_code']);
			$obj_xml = self::parseXml($response['response']);
		} catch (TwengaException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new TwengaException(self::$translation_object->l($e->getMessage(),basename(__FILE__, '.php')), $e->getCode());
		}
		return ((string)$obj_xml->message === 'true') ? true : false;
	}
	
	/**
	 * @param array $params
	 * @throws TwengaFieldsException
	 * @throws TwengaException
	 */
	public function siteActivate($params = array())
	{
		require_once realpath(dirname(__FILE__).'/TwengaFieldsSiteExist.php');
		require_once realpath(dirname(__FILE__).'/TwengaFieldsSiteActivate.php');
		$params['key'] = self::$hashkey;
		$params['PARTNER_AUTH_KEY'] = self::PARTNER_AUTH_KEY;
		try {
			self::checkParams(__FUNCTION__, $params);
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_params = self::buildUrlToQuery(self::$arr_api_url[__FUNCTION__],$params);
		 
		try {
			$response = self::executeQuery($str_params);
			self::checkStatusCode($response['status_code']);
			$obj_xml = self::parseXml($response['response']);
		} catch (TwengaException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new TwengaException(self::$translation_object->l($e->getMessage(),basename(__FILE__, '.php')), $e->getCode());
		}
		return ((string)$obj_xml->message === 'true') ? true : false;
	}
	
	/**
	 * @param array $params
	 * @throws TwengaFieldsException
	 * @throws TwengaException
	 */
	public function getTrackingScript($params = array())
	{
		require_once realpath(dirname(__FILE__).'/TwengaFieldsGetTrackingScript.php');
		$params['key'] = self::$hashkey;
		$params['PARTNER_AUTH_KEY'] = self::PARTNER_AUTH_KEY;
		try {
			self::checkParams(__FUNCTION__, $params);
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_params = self::buildUrlToQuery(self::$arr_api_url[__FUNCTION__],$params);
		try {
			$response = self::executeQuery($str_params);
			self::checkStatusCode($response['status_code']);
			$obj_xml = self::parseXml($response['response']);
		} catch (TwengaException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new TwengaException(self::$translation_object->l($e->getMessage(),basename(__FILE__, '.php')), $e->getCode());
		}
		return (string)$obj_xml->message;
	}
	
	/**
	 * @param array $params
	 * @throws TwengaFieldsException
	 * @throws TwengaException
	 */
	public function orderExist($params = array())
	{
		require_once realpath(dirname(__FILE__).'/TwengaFieldsOrderValidate.php');
		require_once realpath(dirname(__FILE__).'/TwengaFieldsOrderExist.php');
		$params['key'] = self::$hashkey;
		$params['PARTNER_AUTH_KEY'] = self::PARTNER_AUTH_KEY;
		try {
			self::checkParams(__FUNCTION__, $params);
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_params = self::buildUrlToQuery(self::$arr_api_url[__FUNCTION__],$params);
		try {
			$response = self::executeQuery($str_params);
			self::checkStatusCode($response['status_code']);
			$obj_xml = self::parseXml($response['response']);
		} catch (TwengaException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new TwengaException(self::$translation_object->l($e->getMessage(),basename(__FILE__, '.php')), $e->getCode());
		}
		return ((string)$obj_xml->message === 'true') ? true : false;
	}
	
	/**
	 * @param array $params
	 * @throws TwengaFieldsException
	 * @throws TwengaException
	 */
	public function orderValidate($params = array())
	{
		require_once realpath(dirname(__FILE__).'/TwengaFieldsOrderValidate.php');
		$params['key'] = self::$hashkey;
		$params['PARTNER_AUTH_KEY'] = self::PARTNER_AUTH_KEY;
		try {
			self::checkParams(__FUNCTION__, $params);
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_params = self::buildUrlToQuery(self::$arr_api_url[__FUNCTION__],$params);
		try {
			$response = self::executeQuery($str_params);
			self::checkStatusCode($response['status_code']);
			$obj_xml = self::parseXml($response['response']);
		} catch (TwengaException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new TwengaException(self::$translation_object->l($e->getMessage(),basename(__FILE__, '.php')), $e->getCode());
		}
		return ((string)$obj_xml->message === 'true') ? true : false;
	}
	
	/**
	 * @param array $params
	 * @throws TwengaFieldsException
	 * @throws TwengaException
	 */
	public function orderCancel($params = array())
	{
		require_once realpath(dirname(__FILE__).'/TwengaFieldsOrderValidate.php');
		require_once realpath(dirname(__FILE__).'/TwengaFieldsOrderCancel.php');
		$params['key'] = self::$hashkey;
		try {
			self::checkParams(__FUNCTION__, $params);
		} catch (TwengaFieldsException $e) {
			throw $e;
		}
		$str_params = self::buildUrlToQuery(self::$arr_api_url[__FUNCTION__],$params);
		try {
			$response = self::executeQuery($str_params);
			self::checkStatusCode($response['status_code']);
			$obj_xml = self::parseXml($response['response']);
		} catch (TwengaException $e) {
			throw $e;
		} catch (Exception $e) {
			throw new TwengaException(self::$translation_object->l($e->getMessage(),basename(__FILE__, '.php')), $e->getCode());
		}
		return ((string)$obj_xml->message === 'true') ? true : false;
	}
	
	/**
	 * @param string $resource to parse
	 * @throws TwengaException
	 */
	public static function parseXml($resource)
	{
		if ($resource != '')
		{
			libxml_use_internal_errors(true);
			$xml = simplexml_load_string($resource);
			if (libxml_get_errors())
				throw new TwengaException(self::$translation_object->l('HTTP XML response is not parsable : ', basename(__FILE__, '.php')).'<br />'.var_export(libxml_get_errors(), true));
			if ($xml->getName() === 'error')
				throw new TwengaException(self::$translation_object->l((string)$xml->message,basename(__FILE__, '.php')), (int)$xml->code);
			return $xml;
		}
		else
			throw new TwengaException(self::$translation_object->l('HTTP response is empty'));
	}
	/**
	 * @param int $status_code
	 * @throws TwengaException message depends on the error code
	 */
	protected static function checkStatusCode($status_code)
	{
		switch($status_code)
		{
			case 200:case 201:break;
			default: throw new TwengaException('', (int)$status_code);
		}
	}
	
	public static function setTranslationObject(Module $object)
	{
		self::$translation_object = $object;
	}
}
class TwengaException extends Exception
{
	/**
	 * Set the object which use the translation method for the specific module.
	 * @var AbsTrustedShops
	 */
	private static $translation_object; 
	public static function setTranslationObject(Module $object)
	{
		self::$translation_object = $object;
	}
	public function __construct($message, $code = 0)
	{
		if ($code !== 0)
		{
			$error_label = self::$translation_object->l('This call to Twenga Web Services failed and returned an HTTP status of %d. That means:', basename(__FILE__, '.php'))."\n";
			$error_label = sprintf($error_label, $code);
			if ($message === '' || empty($message) || $message === NULL)
			{
				switch ($code)
				{
					case 11: $message .= self::$translation_object->l('The function you tried to access does not exist', basename(__FILE__, '.php'));break;
					case 12: $message .= self::$translation_object->l('A required field is empty', basename(__FILE__, '.php'));break;
					case 13: $message .= self::$translation_object->l('The type of the parameter that has been sent is different from parameter expected type', basename(__FILE__, '.php'));break;
					case 21: $message .= self::$translation_object->l('Hash key is not valid', basename(__FILE__, '.php'));break;
					case 24: $message .= self::$translation_object->l('Hash key is required', basename(__FILE__, '.php'));break;
					case 31: $message .= self::$translation_object->l('No order found. Please check parameters you sent', basename(__FILE__, '.php'));break;
					case 401: $message .= self::$translation_object->l('Authentication is required.', basename(__FILE__, '.php'));break;
					case 403: $message .= self::$translation_object->l('Authentication failed.', basename(__FILE__, '.php'));break;
					case 404: $message .= self::$translation_object->l('Invalid url.', basename(__FILE__, '.php'));break;
					case 500: $message .= self::$translation_object->l('The server encountered an internal server error', basename(__FILE__, '.php'));break;
					case 503: $message .= self::$translation_object->l('The server is unavailable for the moment', basename(__FILE__, '.php'));break;
					default:  $message .= self::$translation_object->l('This call to PrestaShop Web Services returned an unexpected HTTP status of:', basename(__FILE__, '.php')).$code;
				}
			}
			$message = self::$translation_object->l('Error #', basename(__FILE__, '.php')).$code." : \n".$error_label.$message.'';
		}
		parent::__construct($message, $code);
	}
}
